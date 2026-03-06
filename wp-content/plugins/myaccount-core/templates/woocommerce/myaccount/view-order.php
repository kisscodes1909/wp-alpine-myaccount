<?php
/**
 * View Order
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();

// Option A: no big title here; "Order Details" is the label inside order-details-header (Figma 36:1502).
wc_get_template(
	'myaccount/page-heading.php',
	array(
		'page_heading' => '',
		'prev_page'    => array(
			'title' => __( 'Order History', 'woocommerce' ),
			'url'   => wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ),
		),
	)
);

?>

<div class="ma-view-order">
	<?php wc_get_template( 'order/order-details-header.php', array( 'order' => $order ) ); ?>
	<?php wc_get_template( 'order/order-status-card.php', array( 'order' => $order ) ); ?>
	<?php wc_get_template( 'order/order-details-items-summary.php', array( 'order' => $order ) ); ?>
</div>


<?php if ( $notes ) : ?>
	<h2><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></h2>
	<ol class="woocommerce-OrderUpdates commentlist notes">
		<?php foreach ( $notes as $note ) : ?>
		<li class="woocommerce-OrderUpdate comment note">
			<div class="woocommerce-OrderUpdate-inner comment_container">
				<div class="woocommerce-OrderUpdate-text comment-text">
					<p class="woocommerce-OrderUpdate-meta meta"><?php echo date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
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
<?php endif; ?>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>

