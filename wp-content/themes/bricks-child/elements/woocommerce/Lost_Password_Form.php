<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Lost_Password_Form extends \Bricks\Woo_Element
{
    public $name            = 'woocommerce-lost-password-form';
    public $icon            = 'fas fa-passport';
    public $category        = 'woocommerce';

    public function get_label() {
        return esc_html__( 'Alpine - Lost Password Form', 'bricks' );
    }

    public function render() {
        ob_start();
        wc_get_template('myaccount/form-lost-password.php');
        $woo_template = ob_get_clean();
        echo "<div {$this->render_attributes( '_root' )}>{$woo_template}</div>";
    }

    public function enqueue_scripts(){
        wp_enqueue_script('alpine-bundle');
        wp_enqueue_script_module('lost-password-handler', CHILD_URL . '/assets/js/LostPasswordHandler.js', ['alpine-bundle'], filemtime(CHILD_DIR . '/assets/js/LostPasswordHandler.js'));
    }
}