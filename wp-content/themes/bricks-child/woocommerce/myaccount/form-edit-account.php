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

<?php wc_get_template('myaccount/page-heading.php', ['page_heading' => 'My Info', 'page_description' => 'Update your personal details']); ?>

<form x-data="updateAccount" id="form-update-account" class="apl-form-refined flex flex-col gap-8 md:container mx-auto px-8"
      @submit.prevent="handleSubmit"
      @keyup.enter="handleSubmit"
      @keyup="setAllowSubmit(), validateForm()"
>
    <div>
        <label for="firstName" class="block">First Name</label>
        <input
                type="text"
                id="firstName"
                x-model="firstName"
                class="capitalize"
        />
    </div>

    <div>
        <label for="lastName" class="block">Last Name</label>
        <input type="text"
               id="lastName"
               x-model="lastName"
               class="capitalize"
        />
    </div>

    <div>
        <label for="email" class="block">Email Address</label>
        <input
                type="text"
                id="email"
                x-model="email"
                autocomplete="email"
                class=""
        />
    </div>

    <div>
        <label for="password" class="block">Password</label>
        <div class="relative">
            <input
                    disabled
                    type="password"
                    id="password"
                    x-model="password"
                    class="w-full pr-28" />
            <span
                    @click="$store.popup.openPopup(document.getElementById('form-change-password').innerHTML)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 underline cursor-pointer inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                </svg>
                <span>Change</span>
            </span>
        </div>
    </div>

    <ul class="space-y-2 p-0 m-0 list-none">
        <template x-for="(error, index) in errors">
            <li class="text-red-500 text-xs italic text-center" x-text="error"></li>
        </template>
    </ul>

    <div class="flex flex-col items-center justify-between">
        <button
                :disabled="!allowSubmit"
                class="button slim max-w-[800px] w-full inline-flex items-center justify-center gap-2"
                type="submit"
                >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            <span>Update</span>
        </button>
    </div>

</form>
<?php
    wc_get_template('myaccount/apl-form-edit-change-password.php');
?>

<script>
    // Localize data for Alpine component (updateAccount is registered in alpine/components/forms/updateAccount.js)
    window.saveAccountDetailsNonce = '<?php echo wp_create_nonce('save-account-details'); ?>';
    window.accountData = <?php echo json_encode([
        'firstName' => $user->first_name,
        'lastName'  => $user->last_name,
        'email'     => $user->user_email
    ]); ?>;
    window.ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
