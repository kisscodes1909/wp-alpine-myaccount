<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
    return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
    wc_get_template(
        'order/order-downloads.php',
        array(
            'downloads'  => $downloads,
            'show_title' => true,
        )
    );
}

$shipments = aftership_get_shipment($order_id);

?>
        <!-- Item Summary - Shipment -->
        <?php wc_get_template('myaccount/page-heading.php',
            [
                'page_heading' => 'Item Summary',
            ]
        ); ?>

        <?php
            $shipment_index = 1;

            // Preprocess line item IDs for efficiency
            $shipmentLineItemIds = array_map(function ($shipment) {
                return array_column($shipment['line_items'], 'id');
            }, $shipments);

            foreach ($shipments as $index => $shipment) {
                // Use preprocessed line item IDs
                $line_item_ids = $shipmentLineItemIds[$index];

                $itemInShipment = array_filter($order_items, function ($item_id) use ($line_item_ids) {
                    return in_array($item_id, $line_item_ids, true); // Strict comparison
                }, ARRAY_FILTER_USE_KEY);

                foreach ($itemInShipment as $itemId => $data) {
                    unset($order_items[$itemId]);
                }

                wc_get_template('woocommerce/order/order-details-aftership-shipment-list-item.php', [
                    'itemInShipment' => $itemInShipment,
                    'order' => $order,
                    'shipment' => $shipment,
                    'shipment_index' => $shipment_index
                ]);

                $shipment_index++;
            }
        ?>

        <!-- Unship -->
        <?php if(count($order_items) > 0):  ?>
            <?php wc_get_template('myaccount/page-heading.php',
                [
                    'page_heading' => 'Not yet shipped',
                ]
            ); ?>

            <?php
                wc_get_template('woocommerce/order/order-details-list-item.php', [
                    'order_items' => $order_items,
                    'order' => $order,
                ]);
                ?>
        <?php endif; ?>

        <?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

        <div class="md:container mx-auto px-8">
            <?php if (  in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed', 'processing' ), $order ), true ) ) : ?>
                <div class="mt-14">

                        <?php
                            $request_order_cancellation = $order->get_meta('request_order_cancellation');
                        ?>

                        <form action="" method="post">
                            <?php wp_nonce_field('cancel_order_action_nonce', 'cancel_order_nonce'); ?>
                            <input type="hidden" value="<?php echo $order->get_id(); ?>" name="order_id"  />

                            <div class="flex flex-col sm:flex-row gap-5 items-center">
                                <button class="px-6 text-sm button slim w-[300px]" type="submit" <?php echo $request_order_cancellation ? 'disabled' : ''; ?>>Cancel Order</button>
                                <?php if($request_order_cancellation): ?>
                                    <span>Your cancelation request has been submitted.</span>
                                <?php endif; ?>
                            </div>
                        </form>

                </div>
            <?php endif; ?>

            <?php if($order->get_status() === 'shipped'): ?>
                <div class="mt-14 text-center md:text-left">
                    <a href="<?php echo wc_get_endpoint_url('return-order', $order->get_id()) ?>" class="px-6 text-sm button slim w-[200px] md:w-[300px]" type="submit">Return Order</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Return List Item -->
        <?php
            wc_get_template('woocommerce/order/order-details-return-list.php', [
                'order' => $order,
                'order_items' => $order->get_items(),
            ]);
        ?>



<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );


