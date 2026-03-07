<?php
/**
 * Edit account form
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );

$phone_number          = get_user_meta( $user->ID, 'billing_phone', true );
$phone_display         = ! empty( $phone_number ) ? $phone_number : 'Not set';
$registered_timestamp  = strtotime( $user->user_registered );
$active_since          = $registered_timestamp ? date_i18n( 'F Y', $registered_timestamp ) : date_i18n( 'F Y' );
?>

<?php
require_once __DIR__ . '/partials/form-field-icons.php';
wc_get_template( 'myaccount/page-heading.php', array( 'page_heading' => 'My Info', 'page_description' => 'Update your personal details' ) );
?>

<form x-data="updateAccount" id="form-update-account" class="ma-form woocommerce-EditAccountForm edit-account"
      @submit.prevent="handleSubmit"
      @keyup.enter="handleSubmit"
      @keyup="setAllowSubmit(), validateForm()"
>
    <div class="ma-form__section">
        <div class="ma-form__section-head">
            <h3 class="ma-form__section-title">Personal Information</h3>
            <p class="ma-form__section-description">Update your personal details</p>
        </div>

        <div class="ma-form__grid">
            <div class="ma-form__field">
                <label for="firstName" class="ma-form__label">First Name</label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_user(); ?></span>
                    <input
                            type="text"
                            id="firstName"
                            x-model="firstName"
                            class="ma-form__input ma-form__input--capitalize"
                            :class="{'field-invalid': errors.firstName}"
                    />
                </div>
                <span x-validate-error="{message: errors.firstName}"></span>
            </div>

            <div class="ma-form__field">
                <label for="lastName" class="ma-form__label">Last Name</label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_user(); ?></span>
                    <input
                            type="text"
                            id="lastName"
                            x-model="lastName"
                            class="ma-form__input ma-form__input--capitalize"
                            :class="{'field-invalid': errors.lastName}"
                    />
                </div>
                <span x-validate-error="{message: errors.lastName}"></span>
            </div>
        </div>
    </div>

    <div class="ma-form__section ma-form__section--bordered">
        <div class="ma-form__section-head">
            <h3 class="ma-form__section-title">Contact Information</h3>
            <p class="ma-form__section-description">Manage your contact details</p>
        </div>

        <div class="ma-form__fields">
            <div class="ma-form__field">
                <label for="email" class="ma-form__label">Email Address</label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_envelope(); ?></span>
                    <input
                            type="text"
                            id="email"
                            x-model="email"
                            autocomplete="email"
                            class="ma-form__input"
                            :class="{'field-invalid': errors.email}"
                    />
                </div>
                <p class="ma-form__hint">This email is used for order confirmations and account notifications</p>
                <span x-validate-error="{message: errors.email}"></span>
            </div>

            <div class="ma-form__field">
                <label for="phone" class="ma-form__label">Phone Number</label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_phone(); ?></span>
                    <input
                            type="text"
                            id="phone"
                            value="<?php echo esc_attr( $phone_display ); ?>"
                            readonly
                            class="ma-form__input"
                    />
                </div>
                <p class="ma-form__hint">Used for delivery updates and customer support</p>
            </div>
        </div>
    </div>

    <div class="ma-form__section ma-form__section--bordered">
        <div class="ma-form__section-head">
            <h3 class="ma-form__section-title">Account Settings</h3>
            <p class="ma-form__section-description">Manage your account security</p>
        </div>

        <div class="ma-form__settings">
            <div class="ma-form__setting-card">
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

            <div class="ma-form__setting-card">
                <div class="ma-form__setting-meta">
                    <p class="ma-form__setting-label">Account Status</p>
                    <p class="ma-form__setting-value">Active since <?php echo esc_html( $active_since ); ?></p>
                </div>
                <span class="ma-form__status-pill">Active</span>
            </div>
        </div>
    </div>

    <div class="ma-form-actions ma-form__actions">
        <button
                type="submit"
                class="button"
                :disabled="!allowSubmit || isLoading"
                :aria-busy="isLoading"
                x-loading="isLoading"
                data-loading-label="Saving..."
        >
            <span>Save Changes</span>
        </button>
    </div>

</form>
<?php wc_get_template( 'myaccount/ma-form-edit-change-password.php' ); ?>

<script>
    // Localize data for Alpine component (updateAccount is registered in alpine/components/forms/updateAccount.js)
    window.saveAccountDetailsNonce = '<?php echo wp_create_nonce( 'save-account-details' ); ?>';
    window.accountData = <?php echo wp_json_encode(
    	array(
    		'firstName' => $user->first_name,
    		'lastName'  => $user->last_name,
    		'email'     => $user->user_email,
    	)
    ); ?>;
    window.ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
</script>
