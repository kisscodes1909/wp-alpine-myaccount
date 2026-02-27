<?php
/**
 * View Order
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();

$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();

wc_get_template(
	'myaccount/page-heading.php',
	array(
		'page_heading' => __( 'Order Details', 'woocommerce' ),
		'prev_page'    => array(
			'title' => __( 'Order History', 'woocommerce' ),
			'url'   => wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ),
		),
	)
);
?>

<div class="ma-view-order">
	<div class="ma-view-order__layout">
		<div class="ma-view-order__summary">
			<?php wc_get_template( 'order/order-total.php', array( 'order' => $order ) ); ?>
		</div>
		<div class="ma-view-order__details">
			<?php
			if ( $show_customer_details ) {
				wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
			}
			?>
		</div>
	</div>

	<?php if ( $notes ) : ?>
		<section class="ma-view-order__updates" aria-labelledby="order-updates-heading">
			<h2 id="order-updates-heading" class="ma-view-order__updates-title"><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></h2>
			<ol class="woocommerce-OrderUpdates commentlist notes ma-view-order__updates-list">
				<?php foreach ( $notes as $note ) : ?>
				<li class="woocommerce-OrderUpdate comment note">
					<div class="woocommerce-OrderUpdate-inner comment_container">
						<div class="woocommerce-OrderUpdate-text comment-text">
							<p class="woocommerce-OrderUpdate-meta meta ma-view-order__updates-meta"><?php echo date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
							<div class="woocommerce-OrderUpdate-description description">
								<?php echo wpautop( wptexturize( $note->comment_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							<div class="clear"></div>
						</div>
						<div class="clear"></div>
					</div>
				</li>
				<?php endforeach; ?>
			</ol>
		</section>
	<?php endif; ?>
</div>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>
