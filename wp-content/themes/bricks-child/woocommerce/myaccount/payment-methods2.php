<?php
/**
 * Payment methods
 *
 * Shows customer payment methods on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/payment-methods.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$types         = wc_get_account_payment_methods_types();

do_action( 'woocommerce_before_account_payment_methods', $has_methods );
//
//echo "<pre>";
//    print_r($saved_methods);
//echo "</pre>";
?>

<div x-cloak x-data="userPayment()" class="md:container mx-auto px-8 space-y-10">
    <h1 class="text-2xl font-bold text-center">Payment Methods</h1>
    <div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <template x-for="paymentMethod in paymentMethods">
                <div class="bg-[#F6F8FC] rounded-lg p-8">
                    <div class="text-xl mb-5">Payment Method</div>
                    <div class="flex flex-row gap-3">
                        <img x-bind:src='"https://localhost:3000/wp-content/themes/bricks-child/assets/images/Mastercard.png"'>
                        <div class="flex flex-col">
                            <span class="font-semibold">8125</span>
                            <span class="text-gray-600">Exp: 06/24</span>
                        </div>
                    </div>
                    <div class="border-t my-8"></div>
                    <div class="flex flex-row gap-4">
                        <span x-text="paymentMethod.default ? 'Default' : 'Set as default'"
                              class="underline cursor-pointer"
                              @click="paymentMethod.default ? false : $store.userAddress.setDefault(paymentMethod.id, true)">
                        </span>
                        |
                        <span class="underline cursor-pointer" @click="
                            $store.popup.openPopup(document.getElementById('form-change-payment-method').innerHTML)
                            ">
                                Edit</span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- UI for Empty Addresses -->
    <div x-show="paymentMethods.length === 0" class="p-4 bg-white">
        <h3 class="text-lg text-center text-gray-700">No Addresses Found</h3>
        <p class="text-center text-gray-600">You have not added any addresses yet.</p>
        <div class="text-center mt-4">
            <button @click="startAdd()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Address
            </button>
        </div>
    </div>

    <div class="flex flex-col items-center justify-between" >
        <template>
            <div class="text-red-600 text-center mb-2">*You can only save up to 9 payment methods.</div>
        </template>
        <button
                @click="
                $store.popup.openPopup(document.getElementById('form-change-payment-method').innerHTML)"
                class="button slim max-w-[800px] w-full">Add Payment</button>
    </div>


</div>




<?php
wc_get_template('myaccount/apl-form-edit-payment-method.php');
wc_get_template('ui/apl-popup.php');
wc_get_template('ui/apl-toast.php');
wc_get_template('ui/apl-loader.php');
?>

<script>
    const saveAccountDetailsNonce = '<?php echo wp_create_nonce('save-account-details'); ?>'

    const paymentMethods = [
        {
            type : 'Visa',
            cardNumber : '123123123',
            expire: '14/02',
            default: true,
            cardImage: '<?php theme_assets('Visa') ?>'
        },
        {
            type : 'Visa',
            cardNumber : '123123123',
            expire: '14/02',
            default: true,
            cardImage: '<?php theme_assets('Visa') ?>'
        },
        {
            type : 'Visa',
            cardNumber : '123123123',
            expire: '14/02',
            default: true,
            cardImage: '<?php theme_assets('Visa') ?>'
        },
        {
            type : 'Visa',
            cardNumber : '123123123',
            expire: '14/02',
            default: true,
            cardImage: '<?php theme_assets('Visa') ?>'
        },
    ];


    document.addEventListener('alpine:init', () => {
        Alpine.data('userPayment', () => ({
            paymentMethods,
            // other properties and methods you need for this component
            init() {
                // Initialization code
                console.log('userPayment component initialized');
            },
            addPaymentMethod() {
                // Function to add a payment method
            },
            removePaymentMethod(index) {
                // Function to remove a payment method
            },
            // ... other methods and properties
        }));
    });

</script>

