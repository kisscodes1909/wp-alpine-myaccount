<?php

namespace Codemanas\Typesense\WooCommerce\Main;

use Codemanas\Typesense\Backend\Admin;
use Codemanas\Typesense\Main\TypesenseAPI;
use Codemanas\Typesense\WooCommerce\Main\Fields\Fields;
use WP_Query;

class Main {
	public static ?Main $_instance = null;

	/**
	 * @return Main|null
	 */
	public static function getInstance(): ?Main {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	/**
	 * pluginName constructor.
	 */
	public function __construct() {
		if ( ! defined( 'CODEMANAS_TYPESENSE_VERSION' ) ) {
			return;
		}
		if ( version_compare( CODEMANAS_TYPESENSE_VERSION, '1.2.6', '>' ) ) {
			add_filter( 'cm_typesense_available_index_types', [ $this, 'add_woo_post_type' ] );
		} else {
			add_filter( 'cm_typesense_available_post_types', [ $this, 'add_woo_post_type' ] );
		}
		add_filter( 'cm_typesense_schema', [ $this, 'add_schema' ], 10, 2 );
		add_filter( 'cm_typesense_data_before_entry', [ $this, 'document_format' ], 10, 4 );

		//run after comment updated / comment count is changed
		add_action( 'wp_update_comment_count', [ $this, 'on_comment_update_deleted' ], 50 );
		add_action( 'comment_post', [ $this, 'on_new_rating_added' ], 50 );

		//run on stock status changed
		add_action( 'woocommerce_reduce_order_stock', [ $this, 'index_product_after_order_stock_reduced' ], 100 );

		// Replace product search
		add_filter( 'get_product_search_form', [ $this, 'replace_search' ] );
		add_filter( 'render_block', [ $this, 'replace_search_block' ], 10, 2 );

		add_action( 'template_redirect', [ $this, 'replace_shop' ], 9999 );

		add_filter( 'cm_typesense_force_remove_post_on_update', [ $this, 'skip_or_remove_doc' ], 10, 2 );
		add_filter( 'cm_typesense_bulk_import_skip_post', [ $this, 'skip_or_remove_doc' ], 10, 2 );

		//Product Categories
		add_filter( 'cm_typesense_enabled_taxonomy_for_post_type', [ $this, 'add_product_taxonomies' ] );
		//workaround since there isn't really a method to call after attributes taxonomy is updated
		add_action( 'woocommerce_after_edit_attribute_fields', [ $this, 'on_attribute_updated' ], 10, 4 );


		//modify posts search when you're using default search
		add_filter( 'posts_search', [ $this, 'mod_search_query' ], 10, 2 );
		add_filter( 'posts_search_orderby', [ $this, 'mod_search_order_by' ], 10, 2 );

	}

	public function skip_or_remove_doc( $delete, $post ) {
		if ( $post->post_type == 'product' ) {
			$product = wc_get_product( $post->ID );

			if ( 'hidden' == $product->get_catalog_visibility() ) {
				$delete = true;
			}
		}

		return $delete;
	}

	/**
	 * @param           $search
	 * @param WP_Query  $query
	 *
	 * @return mixed|string
	 */
	public function mod_search_query( $search, WP_Query $query ) {
		$admin_settings = Fields::get_option( 'global_setting' );

		if ( isset( $admin_settings['autocomplete_submit_action'] ) && $admin_settings['autocomplete_submit_action'] != 'default_product_search' ) {
			return $search;
		}

		$post_type = $query->get( 'post_type' );
		if ( $query->is_main_query() && isset( $post_type ) && $post_type == 'product' ) {
			$search = '';
		}

		return $search;
	}

	public function mod_search_order_by( $search_orderby, WP_Query $query ) {
		$admin_settings = Fields::get_option( 'global_setting' );

		if ( isset( $admin_settings['autocomplete_submit_action'] ) && $admin_settings['autocomplete_submit_action'] != 'default_product_search' ) {
			return $search_orderby;
		}

		$post_type = $query->get( 'post_type' );
		if ( $query->is_main_query() && isset( $post_type ) && $post_type == 'product' ) {
			$search_orderby = '';
		}

		return $search_orderby;
	}

	public function replace_shop() {
		if ( ! is_shop() && ! is_product_category() ) {
			return;
		}
		$global_settings = get_option( 'cm_tsfwc_global_setting' );

		$replace_shop = $global_settings['replace_shop'] ?? true;


		if ( ! $replace_shop ) {
			return;
		}

		add_action( 'woocommerce_before_shop_loop', [ $this, 'remove_default_wc_loop' ] );

		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		add_action( 'woocommerce_before_shop_loop', [ $this, 'call_shortcode' ], 9999 );
	}

	public function add_product_taxonomies( $enabled_taxonomies ) {
		$enabled_taxonomies[] = 'product_cat';
		$attributes           = wc_get_attribute_taxonomies();
		foreach ( $attributes as $attribute ) {
			$enabled_taxonomies[] = wc_attribute_taxonomy_name( $attribute->attribute_name );
		}

		return $enabled_taxonomies;
	}

	public function on_attribute_updated() {
		global $wpdb;
		if ( ! empty( $_POST['save_attribute'] ) && ! empty( $_GET['edit'] ) ) {
			$attribute_id = absint( $_GET['edit'] );
			check_admin_referer( 'woocommerce-save-attribute_' . $attribute_id );

			$searchConfigSettings = Admin::get_search_config_settings();
			//abort if product is not selected
			$enabled_post_types = $searchConfigSettings['enabled_post_types'] ?? null;

			if ( ! in_array( 'product', $enabled_post_types ) ) {
				return;
			}

			$old_slug = filter_input( INPUT_POST, 'cm_old_slug' );

			//the code written by WooCommerce is confugins the attribute_name becomes the slug if it's empty the attribute label is used
			//see process_edit_attribute - class-wc-admin-attributes.php
			$attribute_label = isset( $_POST['attribute_label'] ) ? wc_clean( wp_unslash( $_POST['attribute_label'] ) ) : '';
			$attribute_name  = isset( $_POST['attribute_name'] ) ? wc_sanitize_taxonomy_name( wp_unslash( $_POST['attribute_name'] ) ) : '';
			$slug            = ! empty( $attribute_name ) ? $attribute_name : wc_sanitize_taxonomy_name( $attribute_label );

			if ( $slug != $old_slug ) {
				$taxonomy   = wc_attribute_taxonomy_name( $slug );
				$ajax_nonce = wp_create_nonce( "tsfwc-attributes-nonce" );
				//requried as taxonomy is updated but not registered so ajax request needs to be updated
				?>
                <script>
                  (function ($) {
                    $(function () {
                      $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                          action: 'tsfwc_update_attributes',
                          taxonomy:<?php echo '"' . esc_js( $taxonomy ) . '"' ?>,
                          security:<?php echo '"' . esc_js( $ajax_nonce ) . '"' ?>
                        },
                        success: function (response) {
                          console.log(response)
                        },
                        error: function (MLHttpRequest, textStatus, errorThrown) {
                          console.log(errorThrown)
                        }
                      })
                    })
                  })(jQuery)
                </script>
				<?php
			}

		}

		$edit = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;

		$attribute_to_edit = $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT attribute_type, attribute_label, attribute_name, attribute_orderby, attribute_public
				FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d
				",
				$edit
			)
		);

		echo '<input type="hidden" name="cm_old_slug" value="' . esc_attr( $attribute_to_edit->attribute_name ) . '">';


	}

	public function remove_default_wc_loop() {
		\wc_set_loop_prop( 'total', 0 ); // set loop total to 0 such that the archive loop doesn't run
	}

	public function call_shortcode() {
		$global_settings = get_option( 'cm_tsfwc_global_setting' );

		$show_cat     = $global_settings['show_cat'] ?? true;
		$show_cat_val = $show_cat ? 'show' : 'hide';

        $enable_routing     = $global_settings['routing'] ?? false;
        $enable_routing_val = $enable_routing ? 'enable' : 'disable';

		$show_price     = $global_settings['show_price'] ?? true;
		$show_price_val = $show_price ? 'show' : 'hide';

		$show_rating     = $global_settings['show_rating'] ?? true;
		$show_rating_val = $show_rating ? 'show' : 'hide';

		$show_attr     = $global_settings['show_attr'] ?? true;
		$show_attr_val = $show_attr ? 'show' : 'hide';

		$show_pagination     = $global_settings['show_pagination'] ?? true;
		$show_pagination_val = $show_pagination ? 'show' : 'hide';
        $pagination_type     = $global_settings['pagination_type'];
        $show_more_text     = $global_settings['show_more_text'];

        if( $show_pagination_val == 'show' && $pagination_type == 'infinite') {
            $show_pagination_val = 'infinite';
        }

		$show_sortby     = $global_settings['show_sortby'] ?? true;
		$show_sortby_val = $show_sortby ? 'show' : 'hide';

		$show_featured_first     = $global_settings['show_featured_first'] ?? false;
		$show_featured_first_val = $show_featured_first ? 'yes' : 'no';

		$search_placeholder = $global_settings['search_placeholder'] ?? __( 'Search products', 'typesense-search-for-woocommerce' );

		echo do_shortcode( '[cm_tsfwc_search
			cat_filter="' . $show_cat_val . '"
			routing="' . $enable_routing_val . '"
			price_filter="' . $show_price_val . '"
			rating_filter="' . $show_rating_val . '"
			attribute_filter="' . $show_attr_val . '"
			pagination="' . $show_pagination_val . '"
			show_more_text="' . $show_more_text . '"
			sortby="' . $show_sortby_val . '"
			placeholder="' . $search_placeholder . '"
			show_featured_first="' . $show_featured_first_val . '"
			unique_id="ts_woo_main_search"
		]' );
	}

	public function add_woo_post_type( $available_post_types ) {
		$available_post_types['product'] = [ 'label' => 'Products', 'value' => 'product', 'type' => 'post_type' ];

		return $available_post_types;
	}

	public function add_schema( $schema, $name ) {
		if ( $name === 'product' ) {
			$product_fields = apply_filters( 'cm_tsfwc_product_fields', [
				[ 'name' => 'category', 'type' => 'string[]', 'facet' => true ],
				[ 'name' => 'category_.*', 'type' => 'string[]', 'facet' => true ],
				[ 'name' => 'cat_links_html', 'type' => 'string', 'optional' => true, 'index' => false ],
				[ 'name' => 'product_thumbnail_html', 'type' => 'string', 'optional' => true, 'index' => false ],
				[ 'name' => 'prices', 'type' => 'int64[]', 'facet' => true ],
				[ 'name' => 'price', 'type' => 'float', 'facet' => true ],
				[ 'name' => 'rating', 'type' => 'int64', 'facet' => true ],
				[ 'name' => 'price_html', 'type' => 'string', 'optional' => true, 'index' => false ],
				[ 'name' => 'add_to_cart_btn', 'type' => 'string', 'optional' => true, 'index' => false ],
				[ 'name' => 'sale_flash', 'type' => 'string', 'optional' => true, 'index' => false ],
				[ 'name' => 'rating_html', 'type' => 'string', 'optional' => true, 'index' => false ],
				[ 'name' => '.*_attribute_filter', 'type' => 'string[]', 'facet' => true ],
				[ 'name' => 'total_sales', 'type' => 'int64', 'facet' => true ],
				[ 'name' => 'is_featured', 'type' => 'int32' ],

			] );

			$unset_fields_arr = [ 'comment_count', 'is_sticky', 'category', 'cat_link' ];
			foreach ( $unset_fields_arr as $unset_field ) {
				foreach ( $schema['fields'] as $index => $field ) {
					if ( $field['name'] === $unset_field ) {
						unset( $schema['fields'][ $index ] );
					}
				}
			}

			$schema['fields'] = array_merge( $schema['fields'], $product_fields );
		}

		return $schema;
	}

	/**
	 * Recursively sort an array of taxonomy terms hierarchically. Child categories will be
	 * placed under a 'children' member of their parent term.
	 *
	 * @param array   $cats     taxonomy term objects to sort
	 * @param integer $parentId the current parent ID to put them in
	 *
	 * @return array
	 */
	public function sort_terms_hierarchicaly( array $cats, $parentId = 0 ): array {

		$into = [];
		foreach ( $cats as $i => $cat ) {
			if ( $cat->parent == $parentId ) {
				$cat->children         = $this->sort_terms_hierarchicaly( $cats, $cat->term_id );
				$into[ $cat->term_id ] = $cat;
			}
		}

		// $into[0]['child_count'] = $i;
		return $into;
	}

	public function document_format( $formatted_data, $raw_data, $object_id, $schema_name ) {

		if ( $schema_name == 'product' ) {
			$schema = TypesenseAPI::getInstance()->getSchema( 'product' );

			$raw_data = wc_get_product( $object_id );
			$post     = get_post( $object_id );

			$fields = $schema['fields'];
			foreach ( $fields as $field ) {
				switch ( $field['name'] ) {
					case '.*_attribute_filter':
						$attribute_terms_name = [];
						foreach ( $raw_data->get_attributes() as $attribute ) {
							$attribute_name = $attribute->get_name();

							$attributes_terms     = $raw_data->get_attribute( $attribute_name );
							$attributes_terms_arr = explode( ', ', $attributes_terms );

							if ( $attribute->is_taxonomy() ) {
								$formatted_key = $attribute_name . '_attribute_filter';
							} else {
								$formatted_key = 'pa_' . $attribute_name . '_attribute_filter';
							}

							foreach ( $attributes_terms_arr as $attributes_term ) {
								$attribute_terms_name[ $formatted_key ][] = html_entity_decode( $attributes_term );
							}


							$formatted_data[ $formatted_key ] = is_array( $attribute_terms_name[ $formatted_key ] ) && ! empty( $attribute_terms_name[ $formatted_key ] ) ? $attribute_terms_name[ $formatted_key ] : [];

						}
						break;
					case 'category':
						$categories     = get_the_terms( $raw_data->get_id(), 'product_cat' );
						$category_names = [];
						if ( ! empty( $categories ) ) {
							foreach ( $categories as $category ) {
								$parents_ids = get_ancestors( $category->term_id, 'product_cat' );
								array_unshift( $parents_ids, $category->term_id );

								foreach ( $parents_ids as $parent_id ) {
									$term             = get_term_by( 'id', $parent_id, 'product_cat', 'taxonomy' );
									$category_names[] = html_entity_decode( $term->name );
								}
							}

						}
						$formatted_data['category'] = is_array( $category_names ) && ! empty( $category_names ) ? array_values( array_unique( $category_names ) ) : [];

						break;
					case 'cat_links_html':
						$formatted_data['cat_links_html'] = wc_get_product_category_list( $raw_data->get_id(), ', ', '<span class="posted_in">', '</span>' );
						break;
					case 'prices':

						//The product type is being checked because different types like variation products have different prices
						if ( $raw_data->is_type( 'variable' ) ) {
							if ( $raw_data instanceof \WC_Product_Variable ) {
								$regular_price = $raw_data->get_variation_regular_price( 'max', true );
								$sale_price    = $raw_data->get_variation_sale_price( 'min', true );
							}
						} elseif ( $raw_data->is_type( 'simple' ) ) {
							$regular_price = $raw_data->get_regular_price();
							$sale_price    = $raw_data->get_sale_price();
						} else {
							$regular_price = $raw_data->get_price();
						}

						$price_arr = [ (float) $regular_price ];
						// This condition is applied so that the least value on the rangeSlider does not always be 0 as the many products may not have sale price set.
						if ( isset( $sale_price ) && $sale_price !== '' ) {
							$price_arr[] = (float) $sale_price;
						}

						$formatted_data['prices'] = $price_arr;
						break;
					case 'price':
						$regular_price           = $raw_data->get_price();
						$formatted_data['price'] = (float) $regular_price;
						break;
					case 'rating':
						$formatted_data['rating'] = ceil( $raw_data->get_average_rating() );
						break;
					case 'price_html':
						$formatted_data['price_html'] = $raw_data->get_price_html();
						break;
					case 'add_to_cart_btn':
						global $product;
						$product          = $raw_data;
						$add_to_cart_html = '';
						ob_start();
						woocommerce_template_loop_add_to_cart();
						$add_to_cart_html                  .= ob_get_clean();
						$formatted_data['add_to_cart_btn'] = $add_to_cart_html;
						break;
					case 'sale_flash':
						if ( $raw_data->is_on_sale() ) {
							$formatted_data['sale_flash'] = apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</span>', $post, $raw_data );
						}
						break;
					case 'rating_html':
						if ( wc_review_ratings_enabled() ) {
							$formatted_data['rating_html'] = wc_get_rating_html( $raw_data->get_average_rating() );
						}
						break;
					case 'total_sales':
						$formatted_data['total_sales'] = absint( get_post_meta( $raw_data->get_id(), 'total_sales', true ) );
						break;
					case 'is_featured':
						$formatted_data['is_featured'] = ( int ) $raw_data->is_featured();
						break;
					case 'category_.*':
						$hierarchical_settings = Fields::get_option( 'hierarchical_settings' );
						if ( $hierarchical_settings['make_category_hierarchical_menu'] ) {

							$ancestors_arr       = $hierarchical_settings['hierarchical_cats_data']['hierarchical_cats'];
							$assigned_categories = get_the_terms( $raw_data->get_id(), 'product_cat' );
							if ( ! empty( $assigned_categories ) ) {
								foreach ( $assigned_categories as $category ) {
									$ancestors_cats = $ancestors_arr[ $category->term_id ];
									//don't understand this
									for ( $i = 0; $i < count( $ancestors_cats ); $i ++ ) {
										$formatted_data[ 'category_lvl' . $i ][] = html_entity_decode( $ancestors_cats[ $i ] );
										$formatted_data[ 'category_lvl' . $i ]   = array_unique( $formatted_data[ 'category_lvl' . $i ] );
									}
								}
							}
						}
						break;
					case 'product_thumbnail_html':
						$formatted_data['product_thumbnail_html'] = $raw_data->get_image( 'woocommerce_thumbnail', [ 'tsfwc-thumbnail_image' ] );
						break;
					default:
						break;
				}
			}

			$formatted_data['id'] = (string) $object_id;
			$formatted_data = apply_filters( 'cm_tsfwc_data_before_entry', $formatted_data, $raw_data, $object_id, $schema_name );
        }

		return $formatted_data;
	}

	/**
	 * @param             $comment_id
	 */
	public function on_new_rating_added( $comment_id ) {
		$comment = get_comment( $comment_id );
		$post_id = $comment->comment_post_ID;
		$product = wc_get_product( $post_id );
		$post    = get_post( $post_id );
		if ( ! $product ) {
			return;
		}

		$document = TypesenseAPI::getInstance()->formatDocumentForEntry( $post, $post_id, 'product' );

		TypesenseAPI::getInstance()->upsertDocument( 'product', $document );
	}


	/**
	 * @param             $post_id
	 */
	public function on_comment_update_deleted( $post_id ) {
		$product = wc_get_product( $post_id );
		$post    = get_post( $post_id );
		if ( ! $product ) {
			return;
		}

		$document = TypesenseAPI::getInstance()->formatDocumentForEntry( $post, $post_id, 'product' );

		TypesenseAPI::getInstance()->upsertDocument( 'product', $document );
	}

	public function replace_search( $form ) {
		$this->load_autocomplete_script();
		$global_settings        = get_option( 'cm_tsfwc_global_setting' );
		$replace_product_search = $global_settings['replace_product_search'] ?? false;
		if ( $replace_product_search === true || 'autocomplete' == $replace_product_search ) {
			$form = $this->autocomplete_html();
		}

		return $form;
	}

	/**
	 * @param $block_content
	 * @param $block
	 *
	 * @return mixed|string
	 */
	public function replace_search_block( $block_content, $block ) {
		$global_settings        = get_option( 'cm_tsfwc_global_setting' );
		// Updated WC search block passes post_type = product to wp:core/search block rather than using its custom block
		$wc_updated_search_block_check = ( isset( $block['attrs']['query']['post_type'] )  && 'product' == $block['attrs']['query']['post_type'] );
		$replace_product_search = $global_settings['replace_product_search'] ?? false;
		if ( ( $replace_product_search === true || 'autocomplete' == $replace_product_search ) &&
		     ( $block['blockName'] == 'woocommerce/product-search' || $wc_updated_search_block_check )
		) {
			$this->load_autocomplete_script();

			$placeholder   = $block['attrs']['placeholder'] ?? __( 'Search products…', 'ts' );
			$block_content = $this->autocomplete_html( $placeholder );
		}

		return $block_content;
	}

	public function autocomplete_html( $placeholder = '' ) {
		ob_start();
		?>
        <div class="cm-tsfwc-autocomplete"
             data-search_url="<?php echo esc_html( apply_filters( 'cm_tsfwc_autocomplete_search_url', home_url() . '?post_type=product&s=' ) ); ?>"
             data-placeholder="<?php echo esc_attr( $placeholder ); ?>"
             data-additional_autocomplete_params="<?php echo _wp_specialchars( json_encode( apply_filters( 'cm_tsfwc_additional_autocomplete_params', [] ) ), ENT_QUOTES, 'UTF-8', true ); ?>"
        ></div>
		<?php
		return ob_get_clean();
	}

	public function load_autocomplete_script() {
		wp_enqueue_script( 'cm-tsfwc-frontend-autocomplete' );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return void
	 */
	public function index_product_after_order_stock_reduced( \WC_Order $order ) {
		$items = $order->get_items();
		if ( count( $items ) > 0 ) {
			foreach ( $items as $item ) {
				$item_data = $item->get_data();
				if ( isset( $item_data['product_id'] ) ) {
					$product_id     = $item_data['product_id'];
					$product        = wc_get_product( $product_id );
					$formatted_data = TypesenseAPI::getInstance()->formatDocumentForEntry( $product, $product_id, 'product' );
					TypesenseAPI::getInstance()->upsertDocument( 'product', $formatted_data );
				}
			}
		}
	}

}
