<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Woo_Kisscodes_Popup_Authenication_Form extends \Bricks\Woo_Element {
    public $name            = 'woocommerce-popup-authenication-form';
    public $icon            = 'fa fa-address-card';
    public $category        = 'header';

    public function get_label() {
        return esc_html__( 'Authenication form', 'bricks' );
    }

    public function render() {
        ob_start();
        ?>
            <div x-data>
                <?php if(!is_user_logged_in()): ?>
                    <button @click="$store.popup.openPopup(document.getElementById('login-form').innerHTML)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <path d="M24 27V24.3333C24 22.9188 23.5224 21.5623 22.6722 20.5621C21.8221 19.5619 20.669 19 19.4667 19H11.5333C10.331 19 9.17795 19.5619 8.32778 20.5621C7.47762 21.5623 7 22.9188 7 24.3333V27" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.5 14C18.9853 14 21 11.9853 21 9.5C21 7.01472 18.9853 5 16.5 5C14.0147 5 12 7.01472 12 9.5C12 11.9853 14.0147 14 16.5 14Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div id="login-form" x-cloak x-show="false">
                        <div class="flex justify-end items-center mb-4">
                            <button @click="$store.popup.closePopup()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                    <path d="M9.46584 8.12341L15.6959 1.89301C16.1014 1.48775 16.1014 0.832499 15.6959 0.427237C15.2907 0.0219756 14.6354 0.0219756 14.2302 0.427237L7.99991 6.65763L1.76983 0.427237C1.36438 0.0219756 0.709335 0.0219756 0.304082 0.427237C-0.101361 0.832499 -0.101361 1.48775 0.304082 1.89301L6.53416 8.12341L0.304082 14.3538C-0.101361 14.7591 -0.101361 15.4143 0.304082 15.8196C0.506044 16.0217 0.771594 16.1233 1.03695 16.1233C1.30231 16.1233 1.56767 16.0217 1.76983 15.8196L7.99991 9.58918L14.2302 15.8196C14.4323 16.0217 14.6977 16.1233 14.963 16.1233C15.2284 16.1233 15.4938 16.0217 15.6959 15.8196C16.1014 15.4143 16.1014 14.7591 15.6959 14.3538L9.46584 8.12341Z" fill="#4D4D4D"/>
                                </svg>
                            </button>
                        </div>
                        <?php wc_get_template('myaccount/form-login.php'); ?>
                    </div>
                    <?php
                        apl_get_template('ui/apl-popup.php', 'APL_POPUP_INCLUDED');
                        apl_get_template('ui/apl-loader.php', 'APL_LOADER_INCLUDED');
                    ?>
                <?php endif; ?>

                <?php if(is_user_logged_in()): ?>
                    <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <path d="M24 27V24.3333C24 22.9188 23.5224 21.5623 22.6722 20.5621C21.8221 19.5619 20.669 19 19.4667 19H11.5333C10.331 19 9.17795 19.5619 8.32778 20.5621C7.47762 21.5623 7 22.9188 7 24.3333V27" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.5 14C18.9853 14 21 11.9853 21 9.5C21 7.01472 18.9853 5 16.5 5C14.0147 5 12 7.01472 12 9.5C12 11.9853 14.0147 14 16.5 14Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        <?php


        $woo_template = ob_get_clean();

        echo "<div {$this->render_attributes( '_root' )}>{$woo_template}</div>";
    }

    public function enqueue_scripts(){
        if( !is_user_logged_in() ) {
            wp_enqueue_script('alpine-bundle');
            wp_enqueue_script_module('authenication', CHILD_URL . '/assets/js/authenication.js', array('alpine-bundle'), filemtime(CHILD_DIR . '/assets/js/authenication.js'));
            add_action('wp_footer',  [$this, 'addDataToScript'],9);
            wp_enqueue_script('gg-recaptcha');
        }
    }


    // TODO: Apply localize data for script module in next core wordpress upgrade. Waiting to the next upgrade.
    function addDataToScript() {
        if( !is_user_logged_in() )

        ?>
            <script>
                window.authenicationData = {
                    wooLoginNonce: '<?php echo wp_create_nonce('woocommerce-login'); ?>',
                    signupNonce: '<?php echo wp_create_nonce('woocommerce-register'); ?>',
                    captchaSiteKey: '<?php echo CAPTCHA_SITE_KEY; ?>'
                }
            </script>
        <?php
    }
}
