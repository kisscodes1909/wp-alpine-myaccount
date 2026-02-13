<?php
$order_status      = $order->get_status();
$order_status_name = wc_get_order_status_name( $order_status );
$status_styles     = array(
    'completed'  => 'text-green-600 bg-gray-100 border-gray-200',
    'processing' => 'text-blue-700 bg-blue-50 border-gray-200',
    'pending'    => 'text-gray-700 bg-gray-100 border-gray-200',
    'on-hold'    => 'text-gray-700 bg-gray-100 border-gray-200',
    'cancelled'  => 'text-red-600 bg-gray-100 border-gray-200',
    'failed'     => 'text-red-600 bg-gray-100 border-gray-200',
    'refunded'   => 'text-gray-700 bg-gray-100 border-gray-200',
);
$status_class      = isset( $status_styles[ $order_status ] ) ? $status_styles[ $order_status ] : 'text-gray-700 bg-gray-100 border-gray-200';
?>

<div class="flex flex-col leading-base md:leading-28px text-sm md:text-base">
    <h3 class="text-sm md:text-base font-normal leading-base md:leading-28px">Order #<?php echo $order->get_id(); ?></h3>
    <div>Ordered on <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"> <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time></div>
    <div class="mt-1 inline-flex items-center gap-2 text-xs md:text-sm uppercase tracking-wide border px-3 py-1 w-fit <?php echo esc_attr( $status_class ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75m6 2.25a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span><?php echo esc_html( $order_status_name ); ?></span>
    </div>
</div>
