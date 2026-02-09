<?php
$is_visible        = $product && $product->is_visible();
$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
$qty          = $item->get_quantity();
$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

if ( $refunded_qty ) {
    $qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
} else {
    $qty_display = esc_html( $qty );
}

if($product) {
    $image         =  $product->get_image('order-product-item');
} else {
    $image = wp_sprintf('<img src="%s"/>', wc_placeholder_img_src('order-product-item'));
}

$isVariationProduct = is_callable( array( $product, 'get_variation_attributes' ) );

if($isVariationProduct) {
    $item_name = explode('-', $item->get_name())[0];
} else {
    $item_name = $item->get_name();
}

?>

<div class="py-6">
    <div class="flex flex-row items-stretch gap-4 justify-between overflow-hidden">
        <div class="w-[150px] overflow-hidden flex justify-center"><?php echo $image; ?></div>
        <div class="relative">
            <span class="text-sm leading-base">
                <?php echo wp_kses_post( sprintf( '<a class="capitalize text-[#4d4d4d]" href="%s">%s</a>', $product_permalink, $item_name ) ); ?>
            </span>

            <div class="text-sm leading-base">
                <?php wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>
                <div><span>Qty:</span> <?php echo $qty_display; ?></div>
            </div>

            <div class="2xs:absolute bottom-0 flex flex-row justify-between items-center w-full">
                <span class="text-sm leading-base"><?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>

                <div class="scale-50 relative left-[17px]">
                    <?php
                        if(in_array($order->get_status(), ['completed', 'refunded'])) {
                            wc_get_template('order/order-item-rating.php');
                        }
                        ?>
                </div>
            </div>

            <?php //wc_get_template('order/order-tracking-infor.php'); ?>

        </div>
    </div>
</div>