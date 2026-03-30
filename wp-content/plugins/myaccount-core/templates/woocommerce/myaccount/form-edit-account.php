<?php
/**
 * Edit account form
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );

$registered_timestamp = strtotime( $user->user_registered );
$active_since         = $registered_timestamp ? date_i18n( 'F Y', $registered_timestamp ) : date_i18n( 'F Y' );

$customer        = new WC_Customer( $user->ID );
$base_country    = WC()->countries->get_base_country();
$billing_country = $customer->get_billing_country() ? $customer->get_billing_country() : $base_country;
$billing_countries = WC()->countries->get_allowed_countries();
?>

<?php
require_once __DIR__ . '/partials/form-field-icons.php';
wc_get_template( 'myaccount/page-heading.php', array( 'page_heading' => 'My Info', 'page_description' => 'Update your personal details' ) );
?>

<form x-data="updateAccount" id="form-update-account" class="ma-form woocommerce-EditAccountForm edit-account"
      @submit.prevent="handleSubmit"
      @keyup.enter="handleSubmit"
      @input="setAllowSubmit()"
      @change="setAllowSubmit()"
>
    <div class="ma-form__section">
        <div class="ma-form__section-head">
            <h3 class="ma-form__section-title ma-u-section-title">Personal Information</h3>
            <p class="ma-form__section-description ma-u-section-description">Update your personal details</p>
        </div>

        <div class="ma-form__grid">
            <div class="ma-form__field">
                <label for="firstName" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'First name', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_user(); ?></span>
                    <input
                            type="text"
                            id="firstName"
                            x-model="formData.firstName"
                            @blur="validateField('firstName')"
                            class="ma-form__input ma-form__input--capitalize"
                            autocomplete="given-name"
                            :class="{'field-invalid': errors.firstName}"
                    />
                </div>
                <span x-validate-error="{message: errors.firstName, touched: touched.firstName}"></span>
            </div>

            <div class="ma-form__field">
                <label for="lastName" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Last name', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_user(); ?></span>
                    <input
                            type="text"
                            id="lastName"
                            x-model="formData.lastName"
                            @blur="validateField('lastName')"
                            class="ma-form__input ma-form__input--capitalize"
                            autocomplete="family-name"
                            :class="{'field-invalid': errors.lastName}"
                    />
                </div>
                <span x-validate-error="{message: errors.lastName, touched: touched.lastName}"></span>
            </div>
        </div>

        <div class="ma-form__field ma-form__field--wide">
            <label for="account-email-readonly" class="ma-form__label"><?php esc_html_e( 'Account email', 'myaccount-core' ); ?></label>
            <div class="ma-form__input-wrap">
                <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_envelope(); ?></span>
                <input
                        type="email"
                        id="account-email-readonly"
                        value="<?php echo esc_attr( $user->user_email ); ?>"
                        readonly
                        tabindex="-1"
                        autocomplete="email"
                        class="ma-form__input"
                        aria-readonly="true"
                />
            </div>
            <p class="ma-form__hint"><?php esc_html_e( 'Used for login and account notifications. Contact support to change it.', 'myaccount-core' ); ?></p>
        </div>
    </div>

    <div class="ma-form__section ma-form__section--divided">
        <div class="ma-form__section-head">
            <h3 class="ma-form__section-title ma-u-section-title"><?php esc_html_e( 'Contact', 'myaccount-core' ); ?></h3>
            <p class="ma-form__section-description ma-u-section-description"><?php esc_html_e( 'Address and contact details used at checkout and on invoices.', 'myaccount-core' ); ?></p>
        </div>

        <div class="ma-form__contact-fields">
        <div class="ma-form__grid ma-form__grid--equal-2 ma-form__contact-row">
            <div class="ma-form__field">
                <label for="billing_country" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Country / Region', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_globe_alt(); ?></span>
                    <select id="billing_country" name="billing_country" autocomplete="billing country"
                            x-model="formData.billing_country" @change="validateField('billing_country')"
                            class="ma-form__input" :class="{'field-invalid': errors.billing_country}">
                        <?php foreach ( $billing_countries as $code => $name ) : ?>
                            <option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <span x-validate-error="{message: errors.billing_country, touched: touched.billing_country}"></span>
            </div>
            <div class="ma-form__field">
                <label for="billing_company" class="ma-form__label"><?php esc_html_e( 'Company name', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_user(); ?></span>
                    <input type="text" id="billing_company" name="billing_company" autocomplete="organization"
                           x-model="formData.billing_company" class="ma-form__input" />
                </div>
            </div>
        </div>

        <div class="ma-form__grid ma-form__grid--equal-2 ma-form__contact-row">
            <div class="ma-form__field">
                <label for="billing_address_1" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Street address', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_map_pin(); ?></span>
                    <input type="text" id="billing_address_1" name="billing_address_1" autocomplete="billing street-address"
                           x-model="formData.billing_address_1" @blur="validateField('billing_address_1')"
                           class="ma-form__input" :class="{'field-invalid': errors.billing_address_1}" />
                </div>
                <span x-validate-error="{message: errors.billing_address_1, touched: touched.billing_address_1}"></span>
            </div>
            <div class="ma-form__field">
                <label for="billing_address_2" class="ma-form__label"><?php esc_html_e( 'Apartment, suite, unit, etc.', 'woocommerce' ); ?> <span class="ma-u-muted">(<?php esc_html_e( 'optional', 'woocommerce' ); ?>)</span></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_map_pin(); ?></span>
                    <input type="text" id="billing_address_2" name="billing_address_2" autocomplete="billing address-line2"
                           x-model="formData.billing_address_2" class="ma-form__input" />
                </div>
            </div>
        </div>

        <div class="ma-form__grid ma-form__grid--equal-2 ma-form__contact-row">
            <div class="ma-form__field">
                <label for="billing_phone" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Phone', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_phone(); ?></span>
                    <input type="tel" id="billing_phone" name="billing_phone" autocomplete="billing tel"
                           x-model="formData.billing_phone" @blur="validateField('billing_phone')"
                           class="ma-form__input" :class="{'field-invalid': errors.billing_phone}" />
                </div>
                <span x-validate-error="{message: errors.billing_phone, touched: touched.billing_phone}"></span>
            </div>
            <div class="ma-form__field">
                <label for="billing_email" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Email address', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_envelope(); ?></span>
                    <input type="email" id="billing_email" name="billing_email" autocomplete="billing email"
                           x-model="formData.billing_email" @blur="validateField('billing_email')"
                           class="ma-form__input" :class="{'field-invalid': errors.billing_email}" />
                </div>
                <span x-validate-error="{message: errors.billing_email, touched: touched.billing_email}"></span>
            </div>
        </div>

        <div class="ma-form__grid ma-form__grid--three ma-form__grid--equal-3 ma-form__contact-row">
            <div class="ma-form__field">
                <label for="billing_city" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Town / City', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_map_pin(); ?></span>
                    <input type="text" id="billing_city" name="billing_city" autocomplete="billing address-level2"
                           x-model="formData.billing_city" @blur="validateField('billing_city')"
                           class="ma-form__input" :class="{'field-invalid': errors.billing_city}" />
                </div>
                <span x-validate-error="{message: errors.billing_city, touched: touched.billing_city}"></span>
            </div>
            <div class="ma-form__field">
                <label for="billing_state" class="ma-form__label"><?php esc_html_e( 'State / County', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_map_pin(); ?></span>
                    <input type="text" id="billing_state" name="billing_state" autocomplete="billing address-level1"
                           x-model="formData.billing_state" @blur="validateField('billing_state')"
                           class="ma-form__input" :class="{'field-invalid': errors.billing_state}" />
                </div>
                <span x-validate-error="{message: errors.billing_state, touched: touched.billing_state}"></span>
            </div>
            <div class="ma-form__field">
                <label for="billing_postcode" class="ma-form__label"><?php esc_html_e( 'Postcode / ZIP', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_map_pin(); ?></span>
                    <input type="text" id="billing_postcode" name="billing_postcode" autocomplete="billing postal-code"
                           x-model="formData.billing_postcode" @blur="validateField('billing_postcode')"
                           class="ma-form__input" :class="{'field-invalid': errors.billing_postcode}" />
                </div>
                <span x-validate-error="{message: errors.billing_postcode, touched: touched.billing_postcode}"></span>
            </div>
        </div>
        </div>
    </div>

    <div class="ma-form__section ma-form__section--divided">
        <div class="ma-form__section-head">
            <h3 class="ma-form__section-title ma-u-section-title">Account Settings</h3>
            <p class="ma-form__section-description ma-u-section-description">Manage your account security</p>
        </div>

        <div class="ma-form__settings">
            <div class="ma-form__setting-card ma-u-panel-sm">
                <div class="ma-form__setting-meta">
                    <p class="ma-form__setting-label">Password</p>
                    <p class="ma-form__setting-value">••••••••</p>
                </div>
                <button
                        type="button"
                        @click="$store.popup.openPopup(document.getElementById('form-change-password').innerHTML)"
                        class="ma-form__setting-action"
                >
                    Change Password
                </button>
            </div>

            <div class="ma-form__setting-card ma-u-panel-sm">
                <div class="ma-form__setting-meta">
                    <p class="ma-form__setting-label">Account Status</p>
                    <p class="ma-form__setting-value">Active since <?php echo esc_html( $active_since ); ?></p>
                </div>
                <span class="ma-form__status-pill ma-u-badge ma-u-badge--success"><?php esc_html_e( 'Active', 'myaccount-core' ); ?></span>
            </div>
        </div>
    </div>

    <div class="ma-form-actions ma-form__actions">
        <button
                type="submit"
                class="ma-btn ma-btn--primary"
                :disabled="!allowSubmit || isFormSubmitting"
                :aria-busy="isFormSubmitting"
                x-loading="isFormSubmitting"
                data-loading-label="Saving..."
        >
            <span><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></span>
        </button>
    </div>

</form>
<?php wc_get_template( 'myaccount/ma-form-edit-change-password.php' ); ?>

<script>
    window.saveAccountDetailsNonce = '<?php echo wp_create_nonce( 'save-account-details' ); ?>';
    window.accountData = <?php echo wp_json_encode(
        array(
            'firstName'            => $user->first_name,
            'lastName'             => $user->last_name,
            'billing_company'      => $customer->get_billing_company(),
            'billing_address_1'    => $customer->get_billing_address_1(),
            'billing_address_2'    => $customer->get_billing_address_2(),
            'billing_city'         => $customer->get_billing_city(),
            'billing_state'        => $customer->get_billing_state(),
            'billing_postcode'     => $customer->get_billing_postcode(),
            'billing_country'      => $billing_country,
            'billing_phone'        => $customer->get_billing_phone(),
            'billing_email'        => $customer->get_billing_email() ? $customer->get_billing_email() : $user->user_email,
        )
    ); ?>;
    window.ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
</script>
