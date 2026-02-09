<?php



class AfterShipCompability {
    public function __construct()
    {
        // Remove Aftership tracking template from plugin template, The view-order.php template conflicted with theme view-order.php

        $afterShip = aftership();
        remove_action( 'woocommerce_view_order', array( $afterShip->actions, 'display_tracking_info' ) );
    }
}

new AfterShipCompability();