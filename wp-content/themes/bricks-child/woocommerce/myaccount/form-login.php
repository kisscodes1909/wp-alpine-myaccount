<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>
<?php //TODO: Clean This Component ?>
<div x-data="{ openTab: 'login' }">
    <ul class="flex list-none p-0 justify-center m-0 md:text-2xl mb-8 gap-5 md:gap-0">
        <li>
            <a :class="{'border-b font-semibold': openTab === 'login'}" @click="openTab = 'login'" class="bg-white inline-block py-2 px-4 cursor-pointer">Log In</a>
        </li>
        <li>
            <a :class="{'border-b font-semibold': openTab === 'signUp'}" @click="openTab = 'signUp'" class="bg-white inline-block py-2 px-4 cursor-pointer">Sign Up</a>
        </li>
    </ul>
    <div class="tab-content">
        <div x-show="openTab === 'login'">
            <form

                    x-data="login"
                    class="woocommerce-form login ma-form"
                    method="post"
                    @submit.prevent="handleSubmit"
                    :class="isFormSubmitting ? 'loading' : ''"
            >

                <?php do_action( 'woocommerce_login_form_start' ); ?>

                <div
                        class="mb-16"
                        x-html="notice"
                ></div>

                <div class="mb-8" x-validate-field="{message: errors.email, touched:touched.email}">
                    <label for="reg_email"><?php esc_html_e('Email address', 'woocommerce'); ?></label>
                    <div class="relative">
                        <input id="reg_email" x-model="formData.email"
                               type="text" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input"
                               autocomplete="email"
                               @blur="handler.validateField('email')"
                        />
                        <div x-validate-icon="{message: errors.email, touched:touched.email}" class="absolute right-0 top-0"></div>
                        <span
                                x-transition:enter.duration.500ms x-validate-message="{message: errors.email, touched:touched.email}" class="text-red-600 mt-1 block"></span>
                    </div>
                </div>

                <div class="mb-8" :class="{'error': (touched.password && errors.password)}">
                    <label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label>
                    <div class="relative" x-data="{showPassword:false}">
                        <input
                                x-model="formData.password"
                                type="password" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input pr-5"
                                autocomplete="username"
                                @keyup="handler.validateField('password')"
                                :type="showPassword === true ? 'text' : 'password'"

                        />
                        <div class="password-toggle">
                            <!-- Validate Field -->
                            <div x-validate-icon="{message: errors.password, touched:touched.password}"></div>
                            <!-- Password Eye -->
                            <div class="block w-10 h-10 flex items-center justify-center" x-password-eye="showPassword" @click="showPassword=!showPassword"></div>
                        </div>
                    </div>
                    <span class="text-red-600 mt-1 block" x-show="touched.password && errors.password" x-text="errors.password"></span>
                </div>

                <p class="woocommerce-LostPassword lost_password text-right mb-8">
                    <a class="underline" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot password?', 'woocommerce' ); ?></a>
                </p>

                <?php do_action( 'woocommerce_login_form' ); ?>

                <div class="form-row">
                    <label class="jk-checkbox-wrapper">
                        <input x-model="formData.rememberme" type="checkbox" id="keep-signed-in">
                        <span class="jk-checkbox"></span>
                        <span class="jk-checkbox-label flex flex-col">
                                <span>Keep me signed in.</span>
                                <span class="text-sm"> if you are using a public device. </span>
                            </span>
                    </label>
                    <?php
                        // TODO: Find a way for reusable this button with alpine state.
                    ?>
                    <div class="ma-form-actions mt-12">
                        <button type="submit" class="woocommerce-button button inline-flex items-center justify-center gap-2" :disabled="isFormSubmitting" :aria-busy="isFormSubmitting" x-loading="isFormSubmitting" data-loading-label="<?php esc_attr_e( 'Signing in...', 'woocommerce' ); ?>">
                            <?php esc_html_e( 'Log in', 'woocommerce' ); ?>
                        </button>
                    </div>
                </div>


                <?php do_action( 'woocommerce_login_form_end' ); ?>

            </form>
        </div>
        <div x-show="openTab === 'signUp'">
            <form
                    @submit.prevent="handleSubmit"
                    x-data="signup"
                    class="register ma-form"
                    :class="isFormSubmitting ? 'loading' : ''"
            >

                <?php do_action( 'woocommerce_register_form_start' ); ?>

                <div
                        class="mb-16"
                        x-html="notice"
                ></div>

                <!-- First Name Input -->
                <div class="mb-8" x-validate-field="{message: errors.firstName, touched:touched.firstName}">
                    <label for="reg_firstName"><?php esc_html_e('First name', 'woocommerce'); ?></label>
                    <div class="relative">
                        <input id="reg_firstName" x-model="formData.firstName"
                               type="text" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input"
                               autocomplete="firstName"
                               @blur="validateField('firstName')"
                        />
                        <div x-validate-icon="{message: errors.firstName, touched:touched.firstName}" class="absolute right-0 top-0"></div>
                        <span x-validate-message="{message: errors.firstName, touched:touched.firstName}" class="text-red-600 mt-1 block"></span>
                    </div>
                </div>

                <!-- Last Name Input -->
                <div class="mb-8" x-validate-field="{message: errors.lastName, touched:touched.lastName}">
                    <label for="reg_lastName"><?php esc_html_e('Last name', 'woocommerce'); ?></label>
                    <div class="relative">
                        <input id="reg_lastName" x-model="formData.lastName"
                               type="text" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input"
                               autocomplete="lastName"
                               @blur="validateField('lastName')"
                        />
                        <div x-validate-icon="{message: errors.lastName, touched:touched.lastName}" class="absolute right-0 top-0"></div>
                        <span x-validate-message="{message: errors.lastName, touched:touched.lastName}" class="text-red-600 mt-1 block"></span>
                    </div>
                </div>

                <!-- Email Input -->
                <div class="mb-8" x-validate-field="{message: errors.email, touched:touched.email}">
                    <label for="reg_email"><?php esc_html_e('Email address', 'woocommerce'); ?></label>
                    <div class="relative">
                        <input id="reg_email" x-model="formData.email"
                               type="text" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input"
                               autocomplete="email"
                               @blur="validateField('email')"
                        />
                        <div x-validate-icon="{message: errors.email, touched:touched.email}" class="absolute right-0 top-0"></div>
                        <span x-validate-message="{message: errors.email, touched:touched.email}" class="text-red-600 mt-1 block"></span>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-10" :class="{'error': (touched.password && errors.password)}">
                    <label for="reg_password"><?php esc_html_e('Password', 'woocommerce'); ?></label>
                    <div class="relative" x-data="{showPassword:false}">
                        <input x-model="formData.password" minlength="8" type="password"
                               class="woocommerce-Input woocommerce-Input--text input-text ma-form__input" name="password"
                               id="reg_password" autocomplete="new-password"
                               @keyup="validateField('password')"
                               :type="showPassword === true ? 'text' : 'password'"
                        />
                        <div class="password-toggle">
                            <!-- Password Eye -->
                            <span class="block w-10 h-10 flex items-center justify-center">
                    <svg x-show="!showPassword" @click="showPassword=!showPassword" class="cursor-pointer size-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>

                     <svg x-show="showPassword" @click="showPassword=!showPassword" class="cursor-pointer size-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </span>
                            <svg x-show="errors.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-red-600 bg-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                            <svg x-show="touched.password && !errors.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <span class="text-red-600 mt-1 block" x-show="touched.password && errors.password" x-text="errors.password"></span>
                        <p class="mt-4">Password must contain:</p>
                        <template x-if="openTab === 'signUp'">
                            <ul class="grid grid-cols-1 md:grid-cols-2 list-none gap-4 md:gap-2">
                                <template x-for="(requirement, index) in Object.values(passwordRequirements)" :key="index" >
                                    <li class="flex flex-row items-center gap-3">
                                        <svg x-show="passedRequirements.includes(requirement.code) && touched.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-green-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <svg x-show="!passedRequirements.includes(requirement.code) && touched.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-red-600 bg-white">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                        <span :class="{
                                'text-green-600' : passedRequirements.includes(requirement.code) && touched.password,
                                'text-red-600' : !passedRequirements.includes(requirement.code) && touched.password,
                            }" x-text="requirement.message"></span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>
                </div>

                <!-- Receive Offers Checkbox -->
                <div class="mb-10">
                    <label class="jk-checkbox-wrapper">
                        <input x-model="formData.receiveOfferNews" type="checkbox" id="keep-signed-in">
                        <span class="jk-checkbox"></span>
                        <span class="jk-checkbox-label">Receive emails with specialized offers and news.</span>
                    </label>
                </div>

                <!-- Agree TOS Checkbox -->
                <div class="mb-10">
                    <label class="jk-checkbox-wrapper">
                        <input @change="validateField('agreeTOS')" x-model="formData.agreeTOS" type="checkbox" id="keep-signed-in">
                        <span class="jk-checkbox"></span>
                        <?php
                        $privacy_page_id = wc_privacy_policy_page_id();
                        $terms_page_id   = wc_terms_and_conditions_page_id();
                        ?>
                        <span class="jk-checkbox-label">I agree to the <a class="underline" target="_blank" href="<?php echo esc_html(get_permalink($terms_page_id)) ?>">Terms of Service</a> and <a class="underline" target="_blank" href="<?php echo esc_html(get_permalink($privacy_page_id)) ?>">Privacy Policy.</a></span>
                    </label>
                    <span class="text-red-600 mt-1 block ml-12" x-show="errors.agreeTOS" x-text="errors.agreeTOS"></span>
                </div>

                <?php do_action( 'woocommerce_register_form' ); ?>

                <div class="ma-form-actions mt-12">
                    <button type="submit" class="woocommerce-button button inline-flex items-center justify-center gap-2" :disabled="isFormSubmitting" :aria-busy="isFormSubmitting" x-loading="isFormSubmitting" data-loading-label="<?php esc_attr_e( 'Creating account...', 'woocommerce' ); ?>">
                        <?php esc_html_e( 'Create Account', 'woocommerce' ); ?>
                    </button>
                </div>

                <?php do_action( 'woocommerce_register_form_end' ); ?>

            </form>

        </div>
    </div>
</div>


<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
