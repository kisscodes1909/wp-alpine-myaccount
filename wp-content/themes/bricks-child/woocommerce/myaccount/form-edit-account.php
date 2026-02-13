<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<?php
$phone_number = get_user_meta( $user->ID, 'billing_phone', true );
$phone_display = ! empty( $phone_number ) ? $phone_number : 'Not set';
$registered_timestamp = strtotime( $user->user_registered );
$active_since = $registered_timestamp ? date_i18n( 'F Y', $registered_timestamp ) : date_i18n( 'F Y' );
?>

<?php wc_get_template('myaccount/page-heading.php', ['page_heading' => 'My Info', 'page_description' => 'Update your personal details']); ?>

<form x-data="updateAccount" id="form-update-account" class="apl-form-refined woocommerce-EditAccountForm edit-account flex flex-col gap-[64px] md:container mx-auto px-8 max-w-[800px]"
      @submit.prevent="handleSubmit"
      @keyup.enter="handleSubmit"
      @keyup="setAllowSubmit(), validateForm()"
>
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-2">
            <h3 class="text-xl leading-28px uppercase tracking-wider text-gray-900">Personal Information</h3>
            <p class="text-sm text-gray-600">Update your personal details</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-2">
                <label for="firstName" class="block text-xs uppercase tracking-wide text-gray-500 mb-0">First Name</label>
                <input
                        type="text"
                        id="firstName"
                        x-model="firstName"
                        class="capitalize h-[50px] bg-gray-100 border border-gray-200 text-gray-900 px-4"
                        :class="{'field-invalid': errors.firstName}"
                />
                <span class="text-red-600 text-xs block" x-show="errors.firstName" x-text="errors.firstName"></span>
            </div>

            <div class="flex flex-col gap-2">
                <label for="lastName" class="block text-xs uppercase tracking-wide text-gray-500 mb-0">Last Name</label>
                <input
                        type="text"
                        id="lastName"
                        x-model="lastName"
                        class="capitalize h-[50px] bg-gray-100 border border-gray-200 text-gray-900 px-4"
                        :class="{'field-invalid': errors.lastName}"
                />
                <span class="text-red-600 text-xs block" x-show="errors.lastName" x-text="errors.lastName"></span>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-200 pt-[65px] flex flex-col gap-8">
        <div class="flex flex-col gap-2">
            <h3 class="text-xl leading-28px uppercase tracking-wider text-gray-900">Contact Information</h3>
            <p class="text-sm text-gray-600">Manage your contact details</p>
        </div>

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-2">
                <label for="email" class="block text-xs uppercase tracking-wide text-gray-500 mb-0">Email Address</label>
                <input
                        type="text"
                        id="email"
                        x-model="email"
                        autocomplete="email"
                        class="h-[50px] bg-gray-100 border border-gray-200 text-gray-900 px-4"
                        :class="{'field-invalid': errors.email}"
                />
                <p class="text-xs text-gray-600">This email is used for order confirmations and account notifications</p>
                <span class="text-red-600 text-xs block" x-show="errors.email" x-text="errors.email"></span>
            </div>

            <div class="flex flex-col gap-2">
                <label for="phone" class="block text-xs uppercase tracking-wide text-gray-500 mb-0">Phone Number</label>
                <input
                        type="text"
                        id="phone"
                        value="<?php echo esc_attr( $phone_display ); ?>"
                        readonly
                        class="h-[50px] bg-gray-100 border border-gray-200 text-gray-900 px-4"
                />
                <p class="text-xs text-gray-600">Used for delivery updates and customer support</p>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-200 pt-[65px] flex flex-col gap-8">
        <div class="flex flex-col gap-2">
            <h3 class="text-xl leading-28px uppercase tracking-wider text-gray-900">Account Settings</h3>
            <p class="text-sm text-gray-600">Manage your account security</p>
        </div>

        <div class="flex flex-col gap-6">
            <div class="border border-gray-200 min-h-[94px] px-[25px] py-4 flex items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <p class="text-sm uppercase tracking-wide text-gray-900 mb-0">Password</p>
                    <p class="text-sm text-gray-600 mb-0">••••••••</p>
                </div>
                <button
                        type="button"
                        @click="$store.popup.openPopup(document.getElementById('form-change-password').innerHTML)"
                        class="text-sm uppercase tracking-wide underline text-gray-900 whitespace-nowrap"
                >
                    Change Password
                </button>
            </div>

            <div class="border border-gray-200 min-h-[94px] px-[25px] py-4 flex items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <p class="text-sm uppercase tracking-wide text-gray-900 mb-0">Account Status</p>
                    <p class="text-sm text-gray-600 mb-0">Active since <?php echo esc_html( $active_since ); ?></p>
                </div>
                <span class="text-xs uppercase tracking-wide text-green-600 px-3 py-1" style="background-color:#ecfdf5;border-radius:9999px;">Active</span>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-200 pt-[33px] apl-form-actions">
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
<?php
    wc_get_template('myaccount/apl-form-edit-change-password.php');
?>

<script>
    // Localize data for Alpine component (updateAccount is registered in alpine/components/forms/updateAccount.js)
    window.saveAccountDetailsNonce = '<?php echo wp_create_nonce('save-account-details'); ?>';
    window.accountData = <?php echo wp_json_encode([
        'firstName' => $user->first_name,
        'lastName'  => $user->last_name,
        'email'     => $user->user_email
    ]); ?>;
    window.ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
