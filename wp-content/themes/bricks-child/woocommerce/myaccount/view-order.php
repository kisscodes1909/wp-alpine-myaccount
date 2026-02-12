<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
?>
<?php

$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();


?>

<?php wc_get_template('myaccount/page-heading.php',
    [
            'page_heading' => 'Order Details',
            'prev_page' => [
                    'title' => 'Order History',
                    'url'   => wc_get_endpoint_url('orders')
            ]
    ]
); ?>


<?php if ( $notes ) : ?>
	<h2 class="text-2xl"><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></h2>
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

<div class="md:container mx-auto px-8">
    <div class="grid grid-cols-1 lg:grid-cols-10 lg:gap-[90px]">
        <div class="col-span-3 mb-10 lg:mb-0">
            <?php wc_get_template('order/order-total.php', ['order' => $order]); ?>
        </div>
        <div class="col-span-7">
            <?php
                if ( $show_customer_details ) {
                    wc_get_template( 'order/order-details-customer.php', array('order' => $order ) );
                }
            ?>
        </div>
    </div>
</div>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>
