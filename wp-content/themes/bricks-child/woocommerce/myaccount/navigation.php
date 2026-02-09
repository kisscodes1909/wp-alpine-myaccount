<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$current_user = wp_get_current_user();
do_action( 'woocommerce_before_account_navigation' );
$current_endpoint = WC()->query->get_current_endpoint();

$account_menu_items = wc_get_account_menu_items();

?>
<div class="relative flex flex-col-reverse md:flex-none items-center md:container md:px-8 md:mx-auto md:my-20 my-10">
    <h2 class="text-center text-base font-semibold md:text-xl md:font-normal">My Account</h2>
    <span class="md:absolute md:right-8 capitalize text-sm md:text-base mb-3 md:mb-0">
        <?php echo wp_sprintf("Not %s %s?", $current_user->first_name, $current_user->last_name) ?>
        <a class="underline" href="<?php echo wp_logout_url() ?>"> Sign out </a>
    </span>
</div>
<div class="border-t border-b border-[#CDCDCD] bg-[#F9F9F9] md:bg-transparent">
    <div class="relative"
         x-data="{
            showDropdown: false,
            activeEndpoint: '<?php echo wc_get_account_active_menu_item($current_endpoint, $account_menu_items); ?>',
            screenSize: '',
            updateSize: function() {
                this.screenSize = window.innerWidth > 992 ? 'large' : 'small';
                this.showDropdown = this.screenSize === 'large';
               }
         }"
         x-init="function updateSize() { screenSize = window.innerWidth > 992 ? 'large' : 'small'; }
                 updateSize();
                 window.addEventListener('resize', updateSize)
                 showDropdown = screenSize === 'large'
                "
         x-on:resize.window="updateSize()">
        <div @click="showDropdown=!showDropdown" class="relative items-center flex px-5 <?php echo wc_get_account_menu_item_classes( $current_endpoint ); ?> md:hidden">
            <a class="justify-center after:content-[none] lg:after:content-['']" x-text="activeEndpoint">Order History</a>
            <span class="absolute right-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="8" viewBox="0 0 14 8" fill="none">
                    <path d="M13.5234 1.47492C13.5234 1.58013 13.4813 1.67219 13.3969 1.75109L7.34502 7.41265C7.27473 7.49155 7.17633 7.53101 7.0498 7.53101C6.92328 7.53101 6.83191 7.49155 6.77568 7.41265L0.723779 1.75109C0.625374 1.67219 0.576172 1.58013 0.576172 1.47492C0.576172 1.36971 0.625374 1.27765 0.723779 1.19875L1.37747 0.587221C1.44776 0.508314 1.53913 0.468861 1.6516 0.468861C1.76406 0.468861 1.86246 0.508314 1.94681 0.587221L7.0498 5.34135L12.1528 0.587221C12.2371 0.508314 12.3426 0.468861 12.4691 0.468861C12.5956 0.468861 12.687 0.508314 12.7432 0.587221L13.3969 1.19875C13.4813 1.27765 13.5234 1.36971 13.5234 1.47492Z" fill="#4D4D4D" fill-opacity="0.75"/>
                </svg>
            </span>
        </div>
        <nav x-show="showDropdown" class="md:container md:px-8 mx-auto woocommerce-MyAccount-navigation absolute md:relative left-0 bg-[#F9F9F9] w-full z-10 shadow md:shadow-none">
            <ul>
                <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
                    <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                        <a class="leading-[44px]" href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
