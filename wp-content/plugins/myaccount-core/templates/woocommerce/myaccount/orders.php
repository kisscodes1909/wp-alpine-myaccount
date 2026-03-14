<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );
?>
<?php wc_get_template( 'myaccount/page-heading.php', array( 'page_heading' => 'Order History', 'page_description' => 'View and track your past orders' ) ); ?>

<div class="ma-orders">
<?php if ( $has_orders ) : ?>
		<?php
		foreach ( $customer_orders->orders as $customer_order ) {
			$order  = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$items  = array_values( $order->get_items() );
			$first_item = ! empty( $items ) ? $items[0] : null;
			if ( $first_item && is_object( $first_item ) ) {
				$product = $first_item->get_product();
				$image   = $product ? $product->get_image( 'thumbnail' ) : wp_sprintf( '<img src="%s" alt="" />', esc_url( wc_placeholder_img_src( 'thumbnail' ) ) );
			} else {
				$image = wp_sprintf( '<img src="%s" alt="" />', esc_url( wc_placeholder_img_src( 'thumbnail' ) ) );
			}
			?>
			<div class="ma-orders__item ma-line-card">
				<div class="ma-orders__item-image ma-line-card__media">
					<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- product image or placeholder HTML ?>
					<?php if ( count( $items ) > 1 ) : ?>
						<div class="ma-orders__item-image-overlay" aria-hidden="true">
							<span class="ma-orders__item-image-overlay-count">+<?php echo esc_html( (string) ( count( $items ) - 1 ) ); ?></span>
							<span class="ma-orders__item-image-overlay-label"><?php esc_html_e( 'more', 'woocommerce' ); ?></span>
						</div>
					<?php endif; ?>
				</div>
				<div class="ma-orders__item-body ma-line-card__body ma-u-panel-pad">
					<?php wc_get_template( 'order/order-list-item-content.php', array( 'order' => $order ) ); ?>
				</div>
			</div>
		<?php } ?>

		<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

		<?php
		$ma_orders_total     = (int) $customer_orders->total;
		$ma_orders_max_pages = (int) $customer_orders->max_num_pages;
		$ma_orders_paginated = $ma_orders_max_pages > 1;
		if ( $ma_orders_paginated ) {
			$ma_orders_caption = sprintf(
				/* translators: 1: current page, 2: total pages, 3: total order count */
				esc_html__( 'Page %1$d of %2$d · %3$d orders', 'woocommerce' ),
				$current_page,
				$ma_orders_max_pages,
				$ma_orders_total
			);
		} else {
			$ma_orders_caption = sprintf(
				/* translators: %d: total order count */
				esc_html__( '%d orders', 'woocommerce' ),
				$ma_orders_total
			);
		}
		?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination ma-orders__pagination">
			<p class="ma-orders__pagination-caption"><?php echo $ma_orders_caption; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via sprintf/esc_html__ ?></p>
			<?php if ( $ma_orders_paginated ) : ?>
				<div class="ma-orders__pagination-actions">
					<?php if ( 1 !== $current_page ) : ?>
						<a class="ma-btn ma-btn--secondary-light ma-orders__pagination-button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
					<?php endif; ?>
					<?php if ( $ma_orders_max_pages !== $current_page ) : ?>
						<a class="ma-btn ma-btn--secondary-light ma-orders__pagination-button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

<?php else : ?>

	<?php
	wc_get_template(
		'myaccount/partials/ma-empty-state.php',
		array(
			'title'          => esc_html__( 'No order has been made yet.', 'woocommerce' ),
			'description'    => esc_html__( 'When you place an order, it will appear here so you can track delivery and view details.', 'woocommerce' ),
			'primary_url'    => esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ),
			'primary_label'  => esc_html__( 'Browse products', 'woocommerce' ),
			'primary_icon'   => true,
			'modifier_class' => 'ma-u-surface-panel ma-u-surface-panel--full',
		)
	);
	?>

<?php endif; ?>
</div>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
