<?php

class Form_Handler
{
    public function __construct()
    {
        add_action('init', [$this, 'cancel_order']);
    }

    function cancel_order() {
        // Check if our nonce is set and verify it.
        if (isset($_POST['cancel_order_nonce']) && wp_verify_nonce($_POST['cancel_order_nonce'], 'cancel_order_action_nonce')) {

            include_once plugin_dir_path( WC_PLUGIN_FILE ) . 'includes/emails/class-wc-email.php';

            $order_id = $_POST['order_id'];

            $order = wc_get_order($order_id);

            $cancelled_email_obj = include plugin_dir_path( WC_PLUGIN_FILE ) . 'includes/emails/class-wc-email-cancelled-order.php';

            // 1. Trigger end cancelled email
            $cancelled_email_obj->trigger( $order_id, $order );

            // 2. Add request order cancellation comment
            $order->add_order_note('The customer requested the order cancellation', 0, true);

            // 3. Mark as request cancel, save value to meta
            $order->update_meta_data('request_order_cancellation', true);
            $order->save();
        }
    }
}

new Form_Handler();