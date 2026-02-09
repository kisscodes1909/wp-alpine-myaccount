<?php

use Bricks\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// TODO: Refactory, Extends Woocommerce_Account_Form_Reset_Password
class Reset_Password_Form extends \Bricks\Woo_Element
{
    public $name            = 'woocommerce-reset-password-form';
    public $icon            = 'fas fa-passport';
    public $category        = 'woocommerce';

    public function get_label() {
        return esc_html__( 'Alpine - Reset Password Form', 'bricks' );
    }

    public function render() {
        // Get woo template
        ob_start();

        wc_get_template( 'myaccount/form-reset-password.php', [ 'args' => Woocommerce::get_reset_password_args() ] );

        $woo_template = ob_get_clean();

        // Render Woo template
        echo "<div {$this->render_attributes( '_root' )}>{$woo_template}</div>";
    }

    public function enqueue_scripts(){
            wp_enqueue_script('alpine-bundle');
            wp_enqueue_script_module('reset-password-handler', CHILD_URL . '/assets/js/ResetPasswordHandler.js', ['alpine-bundle']);
    }
}