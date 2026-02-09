<div class="mb-16">
    <div class="md:container mx-auto px-8 text-xl mb-3">
        <span class='text-base md:text-md md:mb-10 mb-3 block'>Shipment #<?php echo $shipment_index; ?></span>
        <p class="mb-0 text-base">Tracking #: <a target="_blank" class="underline" href="<?php echo wp_sprintf('%s/%s', 'https://billingaftershipkg.aftership.com', $shipment['tracking_number']) ?>"><?php echo $shipment['tracking_number']; ?></a></p>
    </div>
    <?php


    wc_get_template('woocommerce/order/order-details-list-item.php', [
        'order_items' => $itemInShipment,
        'order' => $order,
    ]);
    ?>
</div>
