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
<?php wc_get_template('myaccount/page-heading.php', ['page_heading' => 'Order History', 'page_description' => 'View and track your past orders']); ?>


<?php if ( $has_orders ) : ?>

    <div class="ma-orders">

    <?php
        foreach ( $customer_orders->orders as $customer_order ) {
        $order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

        $items = array_values($order->get_items());

    ?>

            <div class="ma-orders__item">
                <?php wc_get_template('order/order-meta-data.php', ['order' => $order]); ?>
                <div class="ma-orders__summary">
                    <ul class="ma-orders__thumbnails">
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
                                <li class="ma-orders__thumbnail"><?php echo $image ?></li>
                            <?php else: ?>
                                <?php if($index < 3): ?>
                                    <li class="ma-orders__thumbnail"><?php echo $image; ?></li>
                                <?php else: ?>
                                    <li class="ma-orders__thumbnail">
                                        <span class="ma-orders__thumbnail-more">+<?php echo count($items) - 4; ?></span>
                                        <?php echo $image ?>
                                    </li>
                                    <?php break; ?>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </ul>
                    <div class="ma-orders__actions">
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
            <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination ma-orders__pagination">
                <?php if ( 1 !== $current_page ) : ?>
                    <a class="slim woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button<?php echo esc_attr( $wp_button_class ); ?> ma-orders__pagination-button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ma-orders__pagination-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                        <span><?php esc_html_e( 'Previous', 'woocommerce' ); ?></span>
                    </a>
                <?php endif; ?>

                <?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
                    <a class="slim woocommerce-button woocommerce-button--next woocommerce-Button button<?php echo esc_attr( $wp_button_class ); ?> ma-orders__pagination-button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>">
                        <span><?php esc_html_e( 'Next', 'woocommerce' ); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="ma-orders__pagination-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5L15.75 12l-7.5 7.5" />
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php else : ?>

            <div class="ma-orders__empty">
                <p class="ma-orders__empty-text">No orders were found</p>
                <a href="/shop" class="button slim ma-orders__empty-link">
                    <svg xmlns="http://www.w3.org/2000/svg" class="ma-orders__empty-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386a1.5 1.5 0 011.415 1.026L5.91 6.75m0 0h12.84m-12.84 0l1.531 8.677A1.5 1.5 0 008.917 16.5h8.666a1.5 1.5 0 001.476-1.073L20.25 9H6.75m2.25 10.5a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0zm9 0a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0z" />
                    </svg>
                    <span>Continue Shopping</span>
                </a>
            </div>

        <?php endif; ?>

    </div>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
