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
<tr>
    <td class="py-6 whitespace-nowrap align-top">
        <div class="flex flex-col lg:flex-row justify-between gap-5">
            <div class="flex flex-row items-start lg:space-x-4 lg:gap-7 gap-4">
                <div class="w-[150px] overflow-hidden flex justify-center"><?php echo $image; ?></div>
                <div class="flex flex-col justify-between">
                <span class="text-sm sm:text-base">
                    <?php echo wp_kses_post( sprintf( '<a class="capitalize text-[#4d4d4d]" href="%s">%s</a>', $product_permalink, $item_name ) ); ?>
                </span>
                    <?php wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>

                    <?php
                        if(in_array($order->get_status(), ['completed', 'refunded'])) {
                            wc_get_template('order/order-item-rating.php');
                        }
                    ?>
                </div>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap align-top"><?php echo $qty_display; ?></td>
    <td class="px-6 py-4 whitespace-nowrap text-right align-top"><?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
</tr>

