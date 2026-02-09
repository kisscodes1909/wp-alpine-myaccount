<?php
    $actions = wc_get_account_orders_actions( $order );

    $actions['view']['name'] = 'View Order';

    if ( ! empty( $actions ) ) {
        foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            echo '<a href="' . esc_url( $action['url'] ) . '" class="px-6 text-sm woocommerce-button slim md:w-[200px]' . esc_attr( $wp_button_class ) . ' button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
        }
    }
?>