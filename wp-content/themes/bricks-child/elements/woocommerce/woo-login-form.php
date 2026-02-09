<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Woo_Kisscode_Login_Form extends \Bricks\Woocommerce_Account_Form_Login {
    public $name            = 'kisscode-woocommerce-account-form-login';
    public $icon            = 'fa fa-address-card';
    public $panel_condition = [ 'templateType', '=', 'wc_account_form_login' ];

    public function render(): void
    {
        $this->set_attribute( '_root', 'class', 'woocommerce-form woocommerce-form-login login' );
        $this->set_attribute( '_root', 'method', 'post' );

        echo "<form {$this->render_attributes( '_root' )}>" . $this->get_login_form_content() . '</form>';
    }

    public function get_label() {
        return esc_html__( 'Account', 'bricks' ) . ' - ' . esc_html__( 'Login form from kisscode', 'bricks' );
    }

    private function get_login_form_content() {
        $settings = $this->settings;

        ob_start();
        do_action( 'woocommerce_login_form_start' );
        ?>

        <div class="form-group username">
            <?php
            $username       = ! empty( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : '';
            $username_label = esc_html__( 'Email address', 'woocommerce' );

            echo '<label for="username">' . $username_label . '</label>';
            echo '<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" spellcheck="false" value="' . $username . '" />';
            ?>
        </div>

        <div class="form-group password">
            <?php
            echo '<label for="password">' . esc_html__( 'Password', 'woocommerce' ) . '</label>';

            // Builder: Add span to wrap password input manually (no JS enqueued) to show password toggle icon
            if ( bricks_is_builder() || bricks_is_builder_call() ) {
                echo '<span class="password-input">';
            }

            echo '<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password"" />';

            if ( bricks_is_builder() || bricks_is_builder_call() ) {
                echo '<span class="show-password-input"></span>';
                echo '</span>';
            }
            ?>
        </div>

        <?php do_action( 'woocommerce_login_form' ); ?>

        <div class="form-group remember-lostpassword">
            <?php if ( ! isset( $settings['hideRememberMe'] ) ) { ?>
                <div class="form-group remember">
                    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                        <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
                    </label>
                </div>
            <?php } ?>

            <?php if ( ! isset( $settings['hideLostPassword'] ) ) { ?>
                <div class="woocommerce-LostPassword lost_password">
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
                </div>
            <?php } ?>
        </div>

        <div class="form-group submit">
            <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

            <button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>">
                <?php esc_html_e( 'Log in', 'woocommerce' ); ?>
            </button>
        </div>



        <?php
        do_action( 'woocommerce_login_form_end' );

        return ob_get_clean();
    }
}
