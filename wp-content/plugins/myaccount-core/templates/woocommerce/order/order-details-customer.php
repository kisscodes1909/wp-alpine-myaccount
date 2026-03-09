<?php
/**
 * Order Customer Details
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

$shipping_details = $order->get_address( 'shipping' );
$billing_details  = $order->get_address( 'billing' );
?>
<section>
    <div class="ma-order-customer">
        <address class="ma-order-customer__col">
            <div class="ma-order-customer__section">
                <span class="ma-order-customer__title">Shipping Info:</span>
                <div class="ma-order-customer__text">
                    <div class="ma-order-customer__subtitle">Contact Info:</div>
                    <p>
                        <?php echo esc_html( $shipping_details['first_name'] . ' ' . $shipping_details['last_name'] ); ?> <br />
                        <?php echo esc_html( $billing_details['email'] ); ?> <br />
                        <?php echo esc_html( $shipping_details['phone'] ); ?>
                    </p>
                </div>
            </div>

            <div class="ma-order-customer__section ma-order-customer__text">
                <div class="ma-order-customer__subtitle">Address:</div>
                <p>
                    <?php echo esc_html( $shipping_details['address_1'] ); ?> <br>
                    <?php echo esc_html( $shipping_details['city'] . ', ' . $shipping_details['state'] . ' ' . $shipping_details['postcode'] ); ?> <br>
                    <?php echo esc_html( $shipping_details['country'] ); ?>
                </p>
            </div>

            <div class="ma-order-customer__text">
                <div class="ma-order-customer__subtitle">Shipping Method:</div>
                <span>
                    <?php
                    $shipping_methods = $order->get_shipping_methods();
                    foreach ( $shipping_methods as $shipping ) {
                        echo esc_html( $shipping->get_method_title() );
                    }
                    ?>
                </span>
            </div>
        </address>

        <address class="ma-order-customer__col">
            <div class="ma-order-customer__section">
                <span class="ma-order-customer__title">Billing Info:</span>
                <div class="ma-order-customer__text">
                    <div class="ma-order-customer__subtitle">Contact Info:</div>
                    <p>
                        <?php echo esc_html( $billing_details['first_name'] . ' ' . $billing_details['last_name'] ); ?> <br />
                        <?php echo esc_html( $billing_details['email'] ); ?> <br />
                        <?php echo esc_html( $billing_details['phone'] ); ?>
                    </p>
                </div>
            </div>

            <div class="ma-order-customer__section ma-order-customer__text">
                <div class="ma-order-customer__subtitle">Address:</div>
                <p>
                    <?php echo esc_html( $billing_details['address_1'] ); ?> <br>
                    <?php echo esc_html( $billing_details['city'] . ', ' . $billing_details['state'] . ' ' . $billing_details['postcode'] ); ?> <br>
                    <?php echo esc_html( $billing_details['country'] ); ?>
                </p>
            </div>

            <div class="ma-order-customer__text">
                <div class="ma-order-customer__subtitle">Payment Method:</div>
                <span><?php echo esc_html( $order->get_payment_method_title() ); ?></span>
            </div>
        </address>
    </div>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
</section>
