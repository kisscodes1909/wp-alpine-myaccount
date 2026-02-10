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
                                    echo '<a href="' . esc_url( $method['actions']['default']['url'] ) . '" class="text-sm underline font-bold">' . esc_html( $method['actions']['default']['name'] ) . '</a>';
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
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="34" height="33" viewBox="0 0 34 33" fill="none">
                                              <rect x="0.703125" y="0.417725" width="32.3176" height="32.3176" rx="5" fill="black" fill-opacity="0.05"/>
                                              <path fill-rule="evenodd" clip-rule="evenodd" d="M14.4614 9.52569H9.51586C9.12626 9.52569 8.75262 9.66498 8.47713 9.91292C8.20164 10.1609 8.04688 10.4971 8.04688 10.8478C8.04688 11.1984 8.20164 11.5347 8.47713 11.7826C8.75262 12.0306 9.12626 12.1699 9.51586 12.1699H10.2503V23.628C10.2505 24.0954 10.457 24.5437 10.8242 24.8742C11.1915 25.2048 11.6896 25.3906 12.209 25.3907H21.5126C22.032 25.3906 22.53 25.2048 22.8973 24.8742C23.2646 24.5437 23.471 24.0954 23.4712 23.628V12.1699H24.2057C24.5952 12.1697 24.9688 12.0304 25.2443 11.7825C25.5197 11.5346 25.6745 11.1984 25.6747 10.8478C25.6745 10.4972 25.5197 10.161 25.2443 9.91306C24.9688 9.66515 24.5952 9.52582 24.2057 9.52569H19.2601C19.1471 9.02804 18.8466 8.58078 18.4094 8.25958C17.9722 7.93838 17.4251 7.76294 16.8608 7.76294C16.2964 7.76294 15.7494 7.93838 15.3121 8.25958C14.8749 8.58078 14.5744 9.02804 14.4614 9.52569ZM22.4919 12.1699V23.628C22.4918 23.8617 22.3886 24.0858 22.2049 24.2511C22.0213 24.4164 21.7723 24.5093 21.5126 24.5093H12.209C11.9493 24.5093 11.7003 24.4164 11.5166 24.2511C11.333 24.0858 11.2298 23.8617 11.2297 23.628V12.1699H22.4919ZM16.3711 14.3733V22.3059C16.3711 22.4227 16.4227 22.5348 16.5145 22.6175C16.6064 22.7001 16.7309 22.7466 16.8608 22.7466C16.9906 22.7466 17.1152 22.7001 17.207 22.6175C17.2988 22.5348 17.3504 22.4227 17.3504 22.3059V14.3733C17.3504 14.2565 17.2988 14.1444 17.207 14.0617C17.1152 13.9791 16.9906 13.9326 16.8608 13.9326C16.7309 13.9326 16.6064 13.9791 16.5145 14.0617C16.4227 14.1444 16.3711 14.2565 16.3711 14.3733ZM12.9435 14.3733V22.3059C12.9435 22.4227 12.9951 22.5348 13.0869 22.6175C13.1787 22.7001 13.3033 22.7466 13.4331 22.7466C13.563 22.7466 13.6876 22.7001 13.7794 22.6175C13.8712 22.5348 13.9228 22.4227 13.9228 22.3059V14.3733C13.9228 14.2565 13.8712 14.1444 13.7794 14.0617C13.6876 13.9791 13.563 13.9326 13.4331 13.9326C13.3033 13.9326 13.1787 13.9791 13.0869 14.0617C12.9951 14.1444 12.9435 14.2565 12.9435 14.3733ZM19.7987 14.3733V22.3059C19.7987 22.4227 19.8503 22.5348 19.9422 22.6175C20.034 22.7001 20.1585 22.7466 20.2884 22.7466C20.4183 22.7466 20.5428 22.7001 20.6346 22.6175C20.7265 22.5348 20.7781 22.4227 20.7781 22.3059V14.3733C20.7781 14.2565 20.7265 14.1444 20.6346 14.0617C20.5428 13.9791 20.4183 13.9326 20.2884 13.9326C20.1585 13.9326 20.034 13.9791 19.9422 14.0617C19.8503 14.1444 19.7987 14.2565 19.7987 14.3733ZM24.2057 11.2885H9.51586C9.38605 11.2882 9.26162 11.2418 9.16967 11.1593C9.07911 11.076 9.02772 10.9644 9.0262 10.8478C9.02625 10.7309 9.07786 10.6188 9.16967 10.5362C9.26149 10.4536 9.38601 10.4071 9.51586 10.4071H24.2057C24.3356 10.4071 24.4601 10.4535 24.5519 10.5362C24.6438 10.6188 24.6954 10.7309 24.6954 10.8478C24.6954 10.9647 24.6438 11.0767 24.5519 11.1594C24.4601 11.242 24.3356 11.2885 24.2057 11.2885ZM18.246 9.52569C18.1447 9.26791 17.9571 9.04473 17.709 8.88688C17.4609 8.72903 17.1646 8.64427 16.8608 8.64427C16.557 8.64427 16.2606 8.72903 16.0125 8.88688C15.7645 9.04473 15.5768 9.26791 15.4755 9.52569H18.246Z" fill="black"/>
                                            </svg>'
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


