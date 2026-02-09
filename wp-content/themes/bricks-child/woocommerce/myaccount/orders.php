<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
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

do_action( 'woocommerce_before_account_orders', $has_orders );

?>
<?php wc_get_template('myaccount/page-heading.php', ['page_heading' => 'Order History']); ?>


<?php if ( $has_orders ) : ?>

    <div class="space-y-10 md:container mx-auto px-8">

    <?php
        foreach ( $customer_orders->orders as $customer_order ) {
        $order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        $item_count = $order->get_item_count() - $order->get_item_count_refunded();

        $items = array_values($order->get_items());

    ?>

            <div class="flex flex-col md:flex-row bg-[#F6F8FC] p-8 md:p-14 rounded-xl justify-between md:gap-10 gap-3">
                <?php wc_get_template('order/order-meta-data.php', ['order' => $order]); ?>
                <div class="flex flex-row md:gap-16 gap-6 md:items-center">
                    <ul class="flex flex-row gap-2 list-none m-0 p-0">
                        <?php foreach($items as $index => $item): ?>
                            <?php
                                $product       = $item->get_product();

                                if($product) {
                                    $image         =  $product->get_image('thumbnail');
                                } else {
                                    $image = wp_sprintf('<img src="%s"/>', wc_placeholder_img_src('thumbnail'));
                                }

                                ?>

                            <?php if(count($items) < 5): ?>
                                <li class="rounded-lg overflow-hidden md:max-w-[70px] max-w-[35px]"><?php echo $image ?></li>
                            <?php else: ?>
                                <?php if($index < 3): ?>
                                    <li class="rounded-lg overflow-hidden md:max-w-[70px] max-w-[35px]"><?php echo $image; ?></li>
                                <?php else: ?>
                                    <li class="relative rounded-lg overflow-hidden md:max-w-[70px] max-w-[35px]">
                                        <span class="pointer-events-none absolute inset-0 bg-black bg-opacity-70 flex justify-center items-center opacity-100 text-white text-sm">+<?php echo count($items) - 4; ?></span>
                                        <?php echo $image ?>
                                    </li>
                                    <?php break; ?>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </ul>
                    <div class="flex flex-col gap-2">
                        <?php wc_get_template('order/order-actions.php', [
                                'order' => $order,
                                'wp_button_class' => $wp_button_class
                        ]); ?>
                    </div>
                </div>

            </div>
    <?php } ?>

        <?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

        <?php if ( 1 < $customer_orders->max_num_pages ) : ?>
            <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination mt-10 justify-center">
                <?php if ( 1 !== $current_page ) : ?>
                    <a class="slim woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
                <?php endif; ?>

                <?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
                    <a class="slim woocommerce-button woocommerce-button--next woocommerce-Button button<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php else : ?>

            <div class="flex flex-col items-center justify-center p-6">
                <p class="mb-4 text-lg font-semibold text-gray-800">No orders were found</p>
                <a href="/shop" class="button slim">Continue Shopping</a>
            </div>

        <?php endif; ?>

    </div>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
