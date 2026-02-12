<div class="grid grid-cols-1 divide-y divide-black bg-slate-50 pt-7 px-7 md:text-base text-sm">
    <div class="mb-2 md:mb-8">
        <h1 class="md:text-base text-sm font-normal leading-28px">Order #<?php echo $order->get_order_number(); ?></h1>
        <p>
            <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>">
                <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
            </time>
        </p>
    </div>

    <div class="py-2 md:py-8 flex justify-between items-center">
        <h2 class="font-normal md:text-base text-sm leading-28px">Order Status:</h2>
        <p><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></p>
    </div>

    <div class="py-2 md:py-8 flex justify-between items-center">
        <h2 class="font-normal md:text-base text-sm leading-28px">Items Ordered:</h2>
        <p><?php echo $order->get_item_count(); ?></p>
    </div>

    <div class="py-2 md:py-8 space-y-2">
        <?php
        // Loop for all totals except order_total
        foreach ( $order->get_order_item_totals() as $key => $total ) {
            if ($key !== 'order_total') {
                ?>
                <div class="flex justify-between items-center">
                    <div class="md:text-base text-sm"><?php echo esc_html( $total['label'] ); ?></div>
                    <div><?php echo wp_kses_post( $total['value'] ); ?></div>
                </div>
                <?php
            }
        }
        ?>
    </div>

    <div class="py-4 md:py-8">
        <?php
        // Separate div for order_total
        if (isset($order->get_order_item_totals()['order_total'])) {
            $total = $order->get_order_item_totals()['order_total'];
            ?>
            <div class="flex justify-between">
                <div><?php echo esc_html( $total['label'] ); ?></div>
                <div><?php echo wp_kses_post( $total['value'] ); ?></div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
