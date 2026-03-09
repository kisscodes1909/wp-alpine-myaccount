<?php
$order_status      = $order->get_status();
$order_status_name = wc_get_order_status_name( $order_status );
$status_styles     = array(
	'completed'  => 'ma-order-meta__status--completed',
	'processing' => 'ma-order-meta__status--processing',
	'pending'    => 'ma-order-meta__status--pending',
	'on-hold'    => 'ma-order-meta__status--pending',
	'cancelled'  => 'ma-order-meta__status--failed',
	'failed'     => 'ma-order-meta__status--failed',
	'refunded'   => 'ma-order-meta__status--pending',
);
$status_class = isset( $status_styles[ $order_status ] ) ? $status_styles[ $order_status ] : 'ma-order-meta__status--pending';
?>

<div class="ma-order-meta">
    <h3 class="ma-order-meta__title">Order #<?php echo esc_html( $order->get_id() ); ?></h3>
    <div>Ordered on <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"> <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time></div>
    <div class="ma-order-meta__status <?php echo esc_attr( $status_class ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="ma-order-meta__status-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75m6 2.25a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span><?php echo esc_html( $order_status_name ); ?></span>
    </div>
</div>
