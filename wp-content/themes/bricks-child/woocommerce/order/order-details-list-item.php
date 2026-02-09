<div class="md:container mx-auto px-8">
    <?php if(!wp_is_mobile()):  ?>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 border-b">
                <thead>
                <tr>
                    <th class="w-1/2 text-left font-medium tracking-wider">Item Description</th>
                    <th class="px-6 text-left font-medium tracking-wider">Quantity</th>
                    <th class="px-6 text-right font-medium tracking-wider" style="text-align: right">Price</th>
                    <!-- Add more columns if needed -->
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
    <?php
                    foreach ( $order_items as $item_id => $item ) {

                        $product = $item->get_product();

                        wc_get_template(
                            'order/order-details-item-table-row.php',
                            array(
                                'order'              => $order,
                                'item_id'            => $item_id,
                                'item'               => $item,
                                'purchase_note'      => $product ? $product->get_purchase_note() : '',
                                'product'            => $product,
                            )
                        );
                    }
    ?>
                <!-- Add more data rows if needed -->
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="flex flex-col divide-y border-t border-b">
            <?php
            foreach ( $order_items as $item_id => $item ) {
                $product = $item->get_product();

                wc_get_template(
                    'order/order-details-item.php',
                    array(
                        'order'              => $order,
                        'item_id'            => $item_id,
                        'item'               => $item,
                        'purchase_note'      => $product ? $product->get_purchase_note() : '',
                        'product'            => $product,
                    )
                );
            }
            ?>
        </div>
    <?php endif; ?>
</div>
