<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.6.0
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = true;

$wp_button_class = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';

$shipping_details = $order->get_address('shipping');
$billing_details = $order->get_address('billing');

?>
<section>

    <!-- Shipping Address -->
    <div class="grid sm:grid-cols-2 sm:gap-5 gap-14">
        <address class="leading-9 capitalize">
            <div class="md:mb-12 mb-6">
                <span class="text-base md:text-xl md:mb-12 mb-5 block">Shipping Info:</span>
                <div class="text-sm md:text-base">
                    <div class="md:mb-12">Contact Info:</div>
                    <p>
                        <?php echo esc_html( $shipping_details['first_name'] . ' ' . $shipping_details['last_name'] ); ?> <br />
                        <?php echo esc_html( $billing_details['email'] ); ?> <br />
                        <?php echo esc_html( $shipping_details['phone'] ); ?>
                    </p>
                </div>
            </div>

            <div class="md:mb-12 mb-6 text-sm md:text-base">
                <div class="md:mb-12 block">Address:</div>
                <p>
                    <?php echo esc_html( $shipping_details['address_1'] ); ?> <br>
                    <?php echo esc_html( $shipping_details['city'] . ', ' . $shipping_details['state'] . ' ' . $shipping_details['postcode'] ); ?> <br>
                    <?php echo esc_html( $shipping_details['country'] ); ?>
                </p>
            </div>

            <div class="text-sm md:text-base">
                <div class="md:mb-12 block">Shipping Method:</div>
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

        <!-- Shipping Address -->


        <address class="leading-9 capitalize">

            <div class="md:mb-12 mb-6 ">
                <span class="text-base md:text-xl md:mb-12 mb-5 block">Billing Info:</span>
                <div class="text-sm md:text-base">
                    <div class="md:mb-12">Contact Info:</div>
                    <p>
                        <?php echo esc_html( $billing_details['first_name'] . ' ' . $billing_details['last_name'] ); ?> <br />
                        <?php echo esc_html( $billing_details['email'] ); ?> <br />
                        <?php echo esc_html( $billing_details['phone'] ); ?>
                    </p>
                </div>
            </div>

            <div class="md:mb-12 mb-6 text-sm md:text-base">
                <div class="md:mb-12 block">Address:</div>
                <p>
                    <?php echo esc_html( $billing_details['address_1'] ); ?> <br>
                    <?php echo esc_html( $billing_details['city'] . ', ' . $billing_details['state'] . ' ' . $billing_details['postcode'] ); ?> <br>
                    <?php echo esc_html( $billing_details['country'] ); ?>
                </p>
            </div>

            <div class="text-sm md:text-base">
                <?php
                    $payment_method_details = $order->get_meta('_payment_method_details');
                ?>
                <div class="md:mb-12 block">Payment Method:</div>
                <span><?php echo $order->get_payment_method_title(); ?></span>
            </div>

        </address>
    </div>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>
