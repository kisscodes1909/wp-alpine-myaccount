<div class="ma-order-total">
    <div class="ma-order-total__head">
        <h1 class="ma-order-total__title">Order #<?php echo esc_html( $order->get_order_number() ); ?></h1>
        <p>
            <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>">
                <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
            </time>
        </p>
    </div>

    <div class="ma-order-total__row">
        <h2 class="ma-order-total__label">Order Status:</h2>
        <p><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></p>
    </div>

    <div class="ma-order-total__row">
        <h2 class="ma-order-total__label">Items Ordered:</h2>
        <p><?php echo esc_html( $order->get_item_count() ); ?></p>
    </div>

    <div class="ma-order-total__totals">
        <?php
        foreach ( $order->get_order_item_totals() as $key => $total ) {
            if ( 'order_total' !== $key ) {
                ?>
                <div class="ma-order-total__row">
                    <div><?php echo esc_html( $total['label'] ); ?></div>
                    <div><?php echo wp_kses_post( $total['value'] ); ?></div>
                </div>
                <?php
            }
        }
        ?>
    </div>

    <div class="ma-order-total__grand">
        <?php
        if ( isset( $order->get_order_item_totals()['order_total'] ) ) {
            $total = $order->get_order_item_totals()['order_total'];
            ?>
            <div class="ma-order-total__row">
                <div><?php echo esc_html( $total['label'] ); ?></div>
                <div><?php echo wp_kses_post( $total['value'] ); ?></div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
