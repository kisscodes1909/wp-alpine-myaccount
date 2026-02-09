<div class="flex flex-col leading-base md:leading-28px text-sm md:text-base">
    <h3 class="text-sm md:text-base font-normal leading-base md:leading-28px">Order #<?php echo $order->get_id(); ?></h3>
    <div>Ordered on <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"> <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time></div>
    <div>Status: <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></div>
</div>