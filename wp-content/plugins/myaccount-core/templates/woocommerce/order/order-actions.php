<?php
$actions = wc_get_account_orders_actions( $order );

$actions['view']['name'] = 'View Order';

$action_icons = array(
	'pay'     => '<svg xmlns="http://www.w3.org/2000/svg" class="ma-order-action__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M3.75 6h16.5A1.5 1.5 0 0121.75 7.5v9A1.5 1.5 0 0120.25 18H3.75a1.5 1.5 0 01-1.5-1.5v-9A1.5 1.5 0 013.75 6z" /></svg>',
	'view'    => '<svg xmlns="http://www.w3.org/2000/svg" class="ma-order-action__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12S5.25 5.25 12 5.25 21.75 12 21.75 12 18.75 18.75 12 18.75 2.25 12 2.25 12z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 100-6 3 3 0 000 6z" /></svg>',
	'cancel'  => '<svg xmlns="http://www.w3.org/2000/svg" class="ma-order-action__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>',
	'reorder' => '<svg xmlns="http://www.w3.org/2000/svg" class="ma-order-action__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>',
);

if ( ! empty( $actions ) ) {
	foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$icon = isset( $action_icons[ $key ] ) ? $action_icons[ $key ] : '<svg xmlns="http://www.w3.org/2000/svg" class="ma-order-action__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>';
		echo '<a href="' . esc_url( $action['url'] ) . '" class="ma-order-action__button woocommerce-button slim ' . esc_attr( trim( $wp_button_class ) ) . ' button ' . sanitize_html_class( $key ) . '">' . $icon . '<span>' . esc_html( $action['name'] ) . '</span></a>';
	}
}
?>
