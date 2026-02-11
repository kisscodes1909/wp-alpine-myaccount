<?php
/**
 * Payment methods
 *
 * Shows customer payment methods on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/payment-methods.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$types         = wc_get_account_payment_methods_types();

do_action( 'woocommerce_before_account_payment_methods', $has_methods );

?>
<?php wc_get_template('myaccount/page-heading.php',
    [
        'page_heading' => 'Payment methods',
        'page_description' => 'Manage your saved payment methods',
    ]
); ?>

<?php if ( $has_methods ) : ?>

    <div class="flex flex-col space-y-16 md:container mx-auto px-8">
        <?php foreach ( $saved_methods as $type => $methods ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
            <?php foreach ( $methods as $method ) : ?>
                <div class="flex flex-row items-center justify-between">
                    <div class="flex items-center space-x-3 gap-5">
                        <?php
//                            var_dump($method['method']['brand']);
                            $brandImage = '';
                            if($method['method']['brand'] === 'Visa') {
                                $brandImage = 'images/Visa.png';
                            } elseif($method['method']['brand'] === 'Mastercard') {
                                $brandImage = 'images/Mastercard.png';
                            } elseif($method['method']['brand'] === 'Amex') {
                                $brandImage = 'images/american-express.svg';
                            } elseif($method['method']['brand'] === 'Discover') {
                                $brandImage = 'images/discover.jpg';
                            }
                        ?>
                        <img class="w-[70px]" src="<?php theme_assets($brandImage) ?>" alt="<?php echo $method['method']['brand']; ?>>">
                        <!-- Card Info -->
                        <div class="flex flex-col">
                            <span class="font-semibold">
                                <?php
                                    if ( ! empty( $method['method']['last4'] ) ) {
                                        /* translators: 1: credit card type 2: last 4 digits */
                                        echo sprintf( esc_html__( '%1$s ****%2$s', 'woocommerce' ), esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) ), esc_html( $method['method']['last4'] ) );
                                    } else {
                                        echo esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) );
                                    }
                                ?>
                            </span>
                            <span class="text-gray-600">
                                <?php echo wp_sprintf("Expire %s", esc_html( $method['expires'] )); ?>
                            </span>
                            <?php
                                if(isset($method['actions']['default'])) {
                                    echo '<a href="' . esc_url( $method['actions']['default']['url'] ) . '" class="text-sm underline font-bold inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg><span>' . esc_html( $method['actions']['default']['name'] ) . '</span></a>';
                                }
                                ?>

                        </div>
                    </div>
                    <!-- Remove Link -->
                    <div class="flex flex-row-reverse gap-5 items-center">
                        <?php
                            if(isset($method['actions']['delete'])) {
                                echo wp_sprintf(
                                    "<a href='%s'>%s</a>",
                                    esc_url( $method['actions']['delete']['url']),
                                    '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0115.916 21H8.084a2.25 2.25 0 01-2.244-1.327L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0V4.5A2.25 2.25 0 0013.5 2.25h-3A2.25 2.25 0 008.25 4.5v.893m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>'
                                );
                            }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

<?php else : ?>

	<?php wc_print_notice( esc_html__( 'No saved methods found.', 'woocommerce' ), 'notice' ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>
