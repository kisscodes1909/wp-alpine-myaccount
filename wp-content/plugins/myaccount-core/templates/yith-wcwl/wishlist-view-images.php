<?php
/**
 * Plugin-owned wishlist card grid for My Account.
 *
 * @package MyAccount_Core
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
}

apply_filters( 'yith_wcwl_wishlist_view_images_columns', 3 );

$ma_wishlist_trash_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3h6m-9 4h12m-1 0-1 11a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2L7 7m3 4v5m4-5v5" /></svg>';
?>

<ul
	class="wishlist_table wishlist_view images_grid responsive ma-wishlist-grid <?php echo $no_interactions ? 'no-interactions' : ''; ?> <?php echo $enable_drag_n_drop ? 'sortable' : ''; ?>"
	data-pagination="<?php echo esc_attr( $pagination ); ?>"
	data-per-page="<?php echo esc_attr( $per_page ); ?>"
	data-page="<?php echo esc_attr( $current_page ); ?>"
	data-id="<?php echo esc_attr( $wishlist_id ); ?>"
	data-token="<?php echo esc_attr( $wishlist_token ); ?>">
	<?php if ( $wishlist && $wishlist->has_items() ) : ?>
		<?php foreach ( $wishlist_items as $item ) : ?>
			<?php
			global $product;

			$product = $item->get_product();

			if ( ! $product || ! $product->exists() ) {
				continue;
			}

			$stock_status = $item->get_stock_status();
			$is_in_stock  = 'out-of-stock' !== $stock_status;
			$stock_label  = $is_in_stock ? __( 'In stock', 'yith-woocommerce-wishlist' ) : __( 'Out of stock', 'yith-woocommerce-wishlist' );
			$date_added = $show_dateadded && $item->get_date_added() ? $item->get_date_added_formatted() : '';
			$remove_after_add = 'yes' === get_option( 'yith_wcwl_remove_after_add_to_cart' );
			$attribute_summary = array();

			if ( $show_variation && $product->is_type( 'variation' ) ) {
				foreach ( $product->get_attributes() as $name => $value ) {
					$label = wc_attribute_label( $name );

					if ( taxonomy_exists( $name ) ) {
						$attribute = get_term_by( 'slug', $value, $name );
						if ( ! is_wp_error( $attribute ) && ! empty( $attribute->name ) ) {
							$attribute_summary[] = sprintf( '%1$s: %2$s', $label, $attribute->name );
						}
					} elseif ( '' !== $value ) {
						$attribute_summary[] = sprintf( '%1$s: %2$s', $label, rawurldecode( $value ) );
					}
				}
			}

			$add_to_cart_class = array(
				'ma-btn',
				'ma-btn--primary',
				'ma-btn--block',
				'ma-wishlist-card__cta',
			);

			$is_simple_product    = $product->is_type( 'simple' );
			$is_variation_product = $product->is_type( 'variation' );
			$variation_parent_id = $is_variation_product ? (int) $product->get_parent_id() : 0;
			$variation_attributes = $is_variation_product ? array_filter( (array) $product->get_variation_attributes() ) : array();
			$has_real_variation_data = $is_variation_product && $variation_parent_id > 0 && ! empty( $variation_attributes );
			$is_direct_add_to_cart = $is_simple_product || $has_real_variation_data;
			$add_to_cart_product_id = $is_variation_product ? $variation_parent_id : $product->get_id();
			$selected_variation_id = $is_variation_product ? (int) $product->get_id() : 0;
			$can_render_purchase_footer = $show_add_to_cart && $item->is_purchasable() && $is_in_stock;
			$wishlist_action_url = '';

			if ( $is_direct_add_to_cart && $product->is_purchasable() && $product->is_in_stock() ) {
				$add_to_cart_class[] = 'product_type_' . $product->get_type();
				$add_to_cart_class[] = 'add_to_cart_button';
			}

			if ( $can_render_purchase_footer ) {
				$wishlist_action_url = wc_get_endpoint_url( 'wishlist', '', wc_get_page_permalink( 'myaccount' ) );
				if ( ! empty( $wishlist_id ) ) {
					$wishlist_action_url = add_query_arg( 'wishlist_id', rawurlencode( (string) $wishlist_id ), $wishlist_action_url );
				}

				if ( isset( $current_page ) && (int) $current_page > 1 ) {
					$wishlist_action_url = add_query_arg( 'paged', (int) $current_page, $wishlist_action_url );
				}
			}

			$render_purchase_markup = static function( string $context ) use ( $is_direct_add_to_cart, $show_quantity, $item, $product, $wishlist_action_url, $has_real_variation_data, $selected_variation_id, $variation_attributes, $remove_after_add, $wishlist_id, $wishlist_token, $add_to_cart_product_id, $add_to_cart_class ) : void {
				if ( $is_direct_add_to_cart ) {
					$default_add_quantity = max( 1, (int) ( $show_quantity ? $item->get_quantity() : 1 ) );
					$max_add_quantity     = (int) $product->get_max_purchase_quantity();
					$qty_input_id         = sprintf( 'ma-wishlist-qty-%1$s-%2$s', $item->get_product_id(), $context );
					?>
					<form class="ma-wishlist-card__purchase-form" action="<?php echo esc_url( $wishlist_action_url ); ?>" method="get">
						<?php if ( $has_real_variation_data ) : ?>
							<input type="hidden" name="variation_id" value="<?php echo esc_attr( $selected_variation_id ); ?>" />
							<?php foreach ( $variation_attributes as $attribute_name => $attribute_value ) : ?>
								<input type="hidden" name="<?php echo esc_attr( $attribute_name ); ?>" value="<?php echo esc_attr( $attribute_value ); ?>" />
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if ( $remove_after_add ) : ?>
							<input type="hidden" name="remove_from_wishlist_after_add_to_cart" value="<?php echo esc_attr( $item->get_product_id() ); ?>" />
							<input type="hidden" name="wishlist_id" value="<?php echo esc_attr( (string) $wishlist_id ); ?>" />
							<input type="hidden" name="wishlist_token" value="<?php echo esc_attr( (string) $wishlist_token ); ?>" />
						<?php endif; ?>
						<label class="screen-reader-text" for="<?php echo esc_attr( $qty_input_id ); ?>">
							<?php esc_html_e( 'Quantity', 'woocommerce' ); ?>
						</label>
						<div class="ma-wishlist-card__purchase-quantity">
							<input
								id="<?php echo esc_attr( $qty_input_id ); ?>"
								class="input-text qty text"
								type="number"
								name="quantity"
								value="<?php echo esc_attr( $default_add_quantity ); ?>"
								min="1"
								<?php if ( $max_add_quantity > 0 ) : ?>
									max="<?php echo esc_attr( $max_add_quantity ); ?>"
								<?php endif; ?>
								step="1"
								inputmode="numeric"
								autocomplete="off" />
						</div>
						<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $add_to_cart_product_id ); ?>" class="<?php echo esc_attr( implode( ' ', array_filter( $add_to_cart_class ) ) ); ?>">
							<?php echo esc_html( $product->add_to_cart_text() ); ?>
						</button>
					</form>
					<?php
					return;
				}
				?>
				<a class="ma-btn ma-btn--primary ma-btn--block ma-wishlist-card__cta" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>">
					<?php echo esc_html( $product->add_to_cart_text() ); ?>
				</a>
				<?php
			};
			?>
			<li
				id="yith-wcwl-row-<?php echo esc_attr( $item->get_product_id() ); ?>"
				class="ma-wishlist-grid__item ma-wishlist-card ma-wishlist-grid__item--type-<?php echo esc_attr( sanitize_html_class( $product->get_type() ) ); ?>"
				data-row-id="<?php echo esc_attr( $item->get_product_id() ); ?>">
				<div class="ma-wishlist-card__media">
					<a class="ma-wishlist-card__image-link" href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>">
						<?php echo $product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce product image HTML. ?>
					</a>
					<?php if ( $show_stock_status ) : ?>
						<span class="ma-wishlist-card__stock-badge ma-u-badge <?php echo $is_in_stock ? 'ma-u-badge--success' : 'ma-u-badge--danger'; ?>">
							<?php echo esc_html( strtoupper( $stock_label ) ); ?>
						</span>
					<?php endif; ?>
					<?php if ( $can_render_purchase_footer ) : ?>
						<div class="ma-wishlist-card__media-actions ma-wishlist-card__media-actions--purchase-desktop" data-ma-wishlist-media-actions="purchase-desktop">
							<?php $render_purchase_markup( 'desktop' ); ?>
							<?php if ( $show_remove_product ) : ?>
								<a href="<?php echo esc_url( $item->get_remove_url() ); ?>" class="ma-btn ma-btn--secondary-light ma-wishlist-card__remove" aria-label="<?php esc_attr_e( 'Remove this product', 'yith-woocommerce-wishlist' ); ?>"><?php echo $ma_wishlist_trash_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup. ?></a>
							<?php endif; ?>
						</div>
					<?php elseif ( $show_remove_product ) : ?>
						<div class="ma-wishlist-card__media-actions ma-wishlist-card__media-actions--remove-only" data-ma-wishlist-media-actions="remove-only">
							<a href="<?php echo esc_url( $item->get_remove_url() ); ?>" class="ma-btn ma-btn--secondary-light ma-wishlist-card__remove" aria-label="<?php esc_attr_e( 'Remove this product', 'yith-woocommerce-wishlist' ); ?>"><?php echo $ma_wishlist_trash_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup. ?></a>
						</div>
					<?php endif; ?>
				</div>

				<div class="ma-wishlist-card__body">
					<h3 class="ma-wishlist-card__title">
						<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>">
							<?php echo wp_kses_post( apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ); ?>
						</a>
					</h3>
					<?php do_action( 'yith_wcwl_table_after_product_name', $item ); ?>

					<?php if ( ! empty( $attribute_summary ) ) : ?>
						<p class="ma-wishlist-card__variation" aria-label="<?php esc_attr_e( 'Selected options', 'myaccount-core' ); ?>">
							<?php echo esc_html( implode( ' · ', $attribute_summary ) ); ?>
						</p>
					<?php endif; ?>

					<?php if ( $show_price || $show_price_variations ) : ?>
						<p class="ma-wishlist-card__price"><?php echo wp_kses_post( ( $show_price ? $item->get_formatted_product_price() : '' ) . ( $show_price_variations ? $item->get_price_variation() : '' ) ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( $can_render_purchase_footer ) : ?>
					<div class="ma-wishlist-card__footer">
						<?php $render_purchase_markup( 'mobile' ); ?>
					</div>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	<?php else : ?>
		<li class="ma-wishlist-grid__empty">
			<?php
			wc_get_template(
				'myaccount/partials/ma-empty-state.php',
				array(
					'title'          => esc_html__( 'Your wishlist is empty.', 'myaccount-core' ),
					'description'    => esc_html__( 'Save the pieces you love and they will stay here for quick access later.', 'myaccount-core' ),
					'primary_url'    => esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ),
					'primary_label'  => esc_html__( 'Browse products', 'woocommerce' ),
					'primary_icon'   => true,
					'modifier_class' => 'ma-u-surface-panel ma-u-surface-panel--full',
					'heading_level'  => 'h3',
				)
			);
			?>
		</li>
	<?php endif; ?>
</ul>

<?php if ( ! empty( $page_links ) ) : ?>
	<?php
	$wl_links = preg_replace(
		array(
			'/class="(prev|next)\s+(page-numbers)(\b[^"]*)"/',
			'/class="(page-numbers)(\b[^"]*)"/',
		),
		array(
			'class="$1 $2 ma-btn ma-btn--secondary-light$3"',
			'class="$1 ma-btn ma-btn--secondary-light$2"',
		),
		$page_links
	);
	$pp  = max( 0, (int) ( $per_page ?? 0 ) );
	$cur = max( 1, (int) ( $current_page ?? 1 ) );
	$cnt = isset( $wishlist ) && method_exists( $wishlist, 'count_items' ) ? (int) $wishlist->count_items() : (int) ( $count ?? 0 );
	$tot = ( $pp > 0 && $cnt > 0 ) ? (int) ceil( $cnt / $pp ) : 1;
	?>
	<nav class="woocommerce-pagination woocommerce-Pagination ma-wishlist__pagination" aria-label="<?php esc_attr_e( 'Wishlist pagination', 'myaccount-core' ); ?>">
		<?php if ( $pp > 0 && $tot > 1 ) : ?>
			<p class="ma-wishlist__pagination-caption">
				<?php
				printf(
					/* translators: 1: current page, 2: total pages */
					esc_html__( 'Page %1$d of %2$d', 'myaccount-core' ),
					$cur,
					$tot
				);
				?>
			</p>
		<?php endif; ?>
		<div class="ma-wishlist__pagination-actions"><?php echo wp_kses_post( $wl_links ); ?></div>
	</nav>
<?php endif; ?>
