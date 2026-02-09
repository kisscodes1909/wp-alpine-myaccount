<?php

use Codemanas\Typesense\WooCommerce\Main\Fields\Fields;

$cat_path_arr = [];
// if( is_product_category() ) {
// 	$breadcrumbs = new WC_Breadcrumb();
// 	foreach( $breadcrumbs->generate() as $crumb ) {
// 		$cat_path_arr[] = $crumb[0];
// 	}
// }

// pre_dump( $cat_path_arr );
$passed_args = $args['passed_args'] ?? [ 'cat_filter' => 'show' ];
if ( $passed_args['cat_filter'] == 'show' ) {
	$hierarchical_settings           = Fields::get_option( 'hierarchical_settings' );
	$make_category_hierarchical_menu = isset( $hierarchical_settings['make_category_hierarchical_menu'] ) ? $hierarchical_settings['make_category_hierarchical_menu'] : false;
	$max_cat_level                   = 0;
	$cat_path = false;

	if ( $make_category_hierarchical_menu ) {
		if( is_product_category() ) {
			$current_cat_obj = get_queried_object();
			$cat_id = $current_cat_obj->term_id;
			$cat_path = end( $hierarchical_settings['hierarchical_cats_data']['hierarchical_cats'][ $cat_id ] );
		}

		$max_cat_level = $hierarchical_settings['hierarchical_cats_data']['max_cat_level'];
	}

?>
    <div
            class="cmtsfwc-Filter-category"
            data-attr_facet_name="<?php $make_category_hierarchical_menu ? esc_attr_e( 'category_lvl0' ) : esc_attr_e( 'category' ); ?>"
            data-attr_label="<?php esc_html_e( 'Categories', 'typesense-search-for-woocommerce' ) ?>"
            data-title="<?php esc_html_e( 'Filter by Category', 'typesense-search-for-woocommerce' ); ?>"
            data-settings="<?php echo _wp_specialchars( json_encode( apply_filters( 'cm_tsfwc_category_facet_settings', [ 'searchable' => false ] ) ), ENT_QUOTES, 'UTF-8', true ); ?>"
            data-filter_type="<?php echo apply_filters( 'cm_tsfwc_category_filter_type', 'refinementList' ); ?>"
            data-hierarchical="<?php echo _wp_specialchars( json_encode( [
				'make_category_hierarchical_menu' => $make_category_hierarchical_menu,
				'max_cat_level'                   => $max_cat_level,
				'hierarchical_cat_path' => $cat_path
			] ),
				ENT_QUOTES,
				'UTF-8',
				true ); ?>"
    ></div>
<?php } ?>