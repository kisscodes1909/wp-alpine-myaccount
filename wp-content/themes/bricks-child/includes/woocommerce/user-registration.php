<?php
class User_Registration {

    public function __construct() {
        //add_action('woocommerce_register_form_start', array($this, 'add_custom_registration_fields_1'));
        //add_action('woocommerce_register_form', array($this, 'add_custom_registration_fields_2'));

        add_action('woocommerce_created_customer', array($this, 'save_custom_fields'));
        add_action( 'wp_print_scripts', [$this, 'remove_password_strength'], 10 );

        add_filter('', [$this, 'remove_downloads_tab'], 100);
        //add_filter('woocommerce_process_registration_errors', [$this, 'validate_tos'], 100, 3);
    }

    function remove_downloads_tab($items) {
        unset($items['downloads']);
        return $items;
    }
    function remove_password_strength(): void
    {
        if( is_checkout() || is_account_page() ) {
            wp_dequeue_script( 'wc-password-strength-meter' );
        }
    }

    function validate_tos(WP_Error $errors, $username, $email) {
        if(!isset($_POST['agreeTOS']) || !$_POST['agreeTOS']) {
            $errors->add( 'registration-error-not-check-tos', __( 'Please agree with our term and services.', 'woocommerce' ) );
        }
        return $errors;
    }

    // Add first name, last name, password, and phone fields to the registration form
    public function add_custom_registration_fields_1(): void
    {
        ?>
            <p class="first_name woocommerce-form-row--wide form-row form-row-wide">
                <?php
                echo '<label for="reg_first_name">' . esc_html__( 'First name', 'woocommerce' ) . ' <span class="required">*</span></label>';

                $first_name = ! empty( $_POST['first_name'] ) ? esc_attr( wp_unslash( $_POST['first_name'] ) ) : '';
                echo '<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="first_name" id="reg_first_name" autocomplete="first_name" value="' . $first_name . '" />';
                ?>
            </p>
            <p class="last_name fwoocommerce-form-row--wide form-row form-row-wide">
                <?php
                echo '<label for="reg_last_name">' . esc_html__( 'Last name', 'woocommerce' ) . ' <span class="required">*</span></label>';

                $last_name = ! empty( $_POST['last_name'] ) ? esc_attr( wp_unslash( $_POST['last_name'] ) ) : '';
                echo '<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="last_name" id="reg_last_name" autocomplete="last_name" value="' . $last_name . '" />';
                ?>
            </p>
        <?php

    }

    public function add_custom_registration_fields_2(): void
    {

        woocommerce_form_field(
            'receive_newsletters',
            array(
                'type' => 'checkbox',
                'class' => array('woocommerce-form-row form-group'),
                'label' => __('Opt in marketing', 'woocommerce'),
                'default' => 1
            )
        );

        ?>
        <div class="form-group validate-required" id="agree_tos_field" data-priority="">
                <span class="woocommerce-input-wrapper">
                    <label class="checkbox">
                        <input type="checkbox" class="input-checkbox"  name="agree_tos" id="agree_tos">
                        <span>Agree with out terms of services and privacy policy</span>
                    </label>
                </span>
        </div>
        <?php

    }

    // Validate first name, last name, password, and phone fields
    public function validate_custom_fields($username, $email, $errors) {
        if (empty($_POST['first_name'])) {
            $errors->add('first_name_error', __('Please enter your first name.'));
        }

        if (empty($_POST['last_name'])) {
            $errors->add('last_name_error', __('Please enter your last name.'));
        }
    }

    // Save first name, last name, and phone fields
    public function save_custom_fields($customer_id) {
        if (isset($_POST['first_name'])) {
            update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['first_name']));
        }

        if (isset($_POST['last_name'])) {
            update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['last_name']));
        }
        
    }
}

return new User_Registration();