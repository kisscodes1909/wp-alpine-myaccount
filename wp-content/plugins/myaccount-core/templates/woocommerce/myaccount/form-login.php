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

do_action( 'woocommerce_before_customer_login_form' );
require_once __DIR__ . '/partials/form-field-icons.php';
?>

<div class="ma-auth-container">
	<div x-data="{ openTab: 'login' }" class="ma-auth">
	<div class="ma-auth__content">
		<div x-show="openTab === 'login'">
			<div class="ma-auth__login-head">
				<p class="ma-auth__login-welcome"><?php esc_html_e( 'Welcome back', 'woocommerce' ); ?></p>
				<h2 class="ma-auth__login-title"><?php esc_html_e( 'Sign In', 'woocommerce' ); ?></h2>
				<p class="ma-auth__login-signup-line">
					<?php esc_html_e( "Don't have an account?", 'woocommerce' ); ?>
					<a href="#" class="ma-auth__login-signup-link" @click.prevent="openTab = 'signUp'"><?php esc_html_e( 'Create one', 'woocommerce' ); ?></a>
				</p>
			</div>
			<?php require __DIR__ . '/partials/auth-social-divider.php'; ?>
			<form
				x-data="login"
				class="woocommerce-form login ma-form"
				method="post"
				@submit.prevent="handleSubmit"
				:class="isFormSubmitting ? 'loading' : ''"
			>
				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<div class="ma-form__notice" x-show="notice">
					<div :class="noticeType === 'success' ? 'ma-notice ma-notice--success' : (noticeType === 'error' ? 'ma-notice ma-notice--error' : '')" x-html="notice"></div>
				</div>

				<div class="ma-form__section">
					<div class="ma-form__fields">
						<div class="ma-form__field" x-validate-field="{message: errors.email, touched:touched.email}" :class="{ 'error': (touched.email && errors.email) }">
							<label for="login_email" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Email address', 'woocommerce' ); ?></label>
							<div class="ma-form__input-wrap">
								<span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_envelope(); ?></span>
								<input id="login_email" x-model="formData.email" type="text" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input" autocomplete="email" @blur="handler.validateField('email')" />
							</div>
							<span x-validate-error="{message: errors.email, touched: touched.email}"></span>
						</div>

						<div class="ma-form__field" :class="{ 'error': (touched.password && errors.password) }">
							<div class="ma-form__field-header">
								<label for="login_password" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label>
								<a class="ma-form__lost-password ma-link-underline" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot password?', 'woocommerce' ); ?></a>
							</div>
							<div class="ma-form__input-wrap">
								<span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_lock_closed(); ?></span>
								<input id="login_password" x-model="formData.password" type="password" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input" autocomplete="username" @keyup="handler.validateField('password')" />
							</div>
							<span x-validate-error="{message: errors.password, touched: touched.password}"></span>
						</div>
					</div>
				</div>

				<div class="ma-form__section ma-form__options">
					<label class="ma-form__checkbox">
						<input x-model="formData.rememberme" type="checkbox" id="keep-signed-in-login">
						<span class="ma-form__checkbox-box"></span>
						<span class="ma-form__checkbox-label ma-form__checkbox-stack">
							<span><?php esc_html_e( 'Keep me signed in.', 'woocommerce' ); ?></span>
							<span class="ma-form__hint"><?php esc_html_e( 'If you are using a public device.', 'woocommerce' ); ?></span>
						</span>
					</label>
				</div>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<div class="ma-form__section">
					<div class="ma-form-actions">
						<button type="submit" class="ma-btn ma-btn--primary" :disabled="isFormSubmitting" :aria-busy="isFormSubmitting" x-loading="isFormSubmitting" data-loading-label="<?php esc_attr_e( 'Signing in...', 'woocommerce' ); ?>">
							<span class="ma-btn-content"><?php esc_html_e( 'Sign In', 'woocommerce' ); ?></span>
							<?php ma_form_icon_arrow_right(); ?>
						</button>
					</div>
				</div>

				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</div>

		<div x-show="openTab === 'signUp'">
			<div class="ma-auth__signup-head">
				<p class="ma-auth__signup-welcome"><?php esc_html_e( 'Join Maison', 'woocommerce' ); ?></p>
				<h2 class="ma-auth__signup-title"><?php esc_html_e( 'Create Account', 'woocommerce' ); ?></h2>
				<p class="ma-auth__signup-login-line">
					<?php esc_html_e( 'Already a member?', 'woocommerce' ); ?>
					<a href="#" class="ma-auth__signup-login-link" @click.prevent="openTab = 'login'"><?php esc_html_e( 'Sign in', 'woocommerce' ); ?></a>
				</p>
			</div>
			<?php
			$auth_social_context = 'signup';
			require __DIR__ . '/partials/auth-social-divider.php';
			?>
			<form
				@submit.prevent="handleSubmit"
				x-data="signup"
				class="register ma-form"
				:class="isFormSubmitting ? 'loading' : ''"
			>
				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<div class="ma-form__notice" x-show="notice">
					<div :class="noticeType === 'success' ? 'ma-notice ma-notice--success' : (noticeType === 'error' ? 'ma-notice ma-notice--error' : '')" x-html="notice"></div>
				</div>

				<div class="ma-form__section">
					<div class="ma-form__grid">
						<div class="ma-form__field" x-validate-field="{message: errors.firstName, touched:touched.firstName}" :class="{ 'error': (touched.firstName && errors.firstName) }">
							<label for="reg_firstName" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'First name', 'woocommerce' ); ?></label>
							<div class="ma-form__input-wrap">
								<span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_user(); ?></span>
								<input id="reg_firstName" x-model="formData.firstName" type="text" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input" autocomplete="firstName" @blur="validateField('firstName')" />
							</div>
							<span x-validate-error="{message: errors.firstName, touched: touched.firstName}"></span>
						</div>
						<div class="ma-form__field" x-validate-field="{message: errors.lastName, touched:touched.lastName}" :class="{ 'error': (touched.lastName && errors.lastName) }">
							<label for="reg_lastName" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Last name', 'woocommerce' ); ?></label>
							<div class="ma-form__input-wrap">
								<span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_user(); ?></span>
								<input id="reg_lastName" x-model="formData.lastName" type="text" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input" autocomplete="lastName" @blur="validateField('lastName')" />
							</div>
							<span x-validate-error="{message: errors.lastName, touched: touched.lastName}"></span>
						</div>
					</div>
					<div class="ma-form__fields">
						<div class="ma-form__field" x-validate-field="{message: errors.email, touched:touched.email}" :class="{ 'error': (touched.email && errors.email) }">
							<label for="reg_email" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Email address', 'woocommerce' ); ?></label>
							<div class="ma-form__input-wrap">
								<span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_envelope(); ?></span>
								<input id="reg_email" x-model="formData.email" type="text" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input" autocomplete="email" @blur="validateField('email')" />
							</div>
							<span x-validate-error="{message: errors.email, touched: touched.email}"></span>
						</div>

						<div class="ma-form__field" :class="{ 'error': (touched.password && errors.password) }">
							<label for="reg_password" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label>
							<div class="ma-form__input-wrap">
								<span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_lock_closed(); ?></span>
								<input id="reg_password" x-model="formData.password" minlength="8" type="password" name="password" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input" autocomplete="new-password" @keyup="validateField('password')" />
							</div>
							<span x-validate-error="{message: errors.password, touched: touched.password}"></span>
							<p class="ma-form__hint"><?php esc_html_e( 'Password must contain:', 'woocommerce' ); ?></p>
							<ul class="ma-form__password-requirements">
								<template x-for="(requirement, index) in Object.values(passwordRequirements)" :key="index">
									<li class="ma-form__password-requirement">
										<svg x-show="passedRequirements.includes(requirement.code) && touched.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ma-form__password-requirement-icon ma-form__password-requirement-icon--pass" aria-hidden="true">
											<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
										</svg>
										<svg x-show="!passedRequirements.includes(requirement.code) && touched.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ma-form__password-requirement-icon ma-form__password-requirement-icon--fail" aria-hidden="true">
											<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
										</svg>
										<span class="ma-form__password-requirement-text" :class="{ 'ma-form__password-requirement-text--pass': passedRequirements.includes(requirement.code) && touched.password, 'ma-form__password-requirement-text--fail': !passedRequirements.includes(requirement.code) && touched.password }" x-text="requirement.message"></span>
									</li>
								</template>
							</ul>
						</div>
					</div>
				</div>

				<div class="ma-form__section">
					<label class="ma-form__checkbox">
						<input x-model="formData.receiveOfferNews" type="checkbox" id="receive-offers">
						<span class="ma-form__checkbox-box"></span>
						<span class="ma-form__checkbox-label"><?php esc_html_e( 'Receive emails with specialized offers and news.', 'woocommerce' ); ?></span>
					</label>

					<div class="ma-form__field" :class="{ 'error': errors.agreeTOS }">
						<label class="ma-form__checkbox ma-form__label--required">
							<input @change="validateField('agreeTOS')" x-model="formData.agreeTOS" type="checkbox" id="agree-tos">
							<span class="ma-form__checkbox-box"></span>
							<?php
							$privacy_page_id = wc_privacy_policy_page_id();
							$terms_page_id   = wc_terms_and_conditions_page_id();
							?>
							<span class="ma-form__checkbox-label"><?php esc_html_e( 'I agree to the', 'woocommerce' ); ?> <a class="ma-link-underline" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( get_permalink( $terms_page_id ) ); ?>"><?php esc_html_e( 'Terms of Service', 'woocommerce' ); ?></a> <?php esc_html_e( 'and', 'woocommerce' ); ?> <a class="ma-link-underline" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( get_permalink( $privacy_page_id ) ); ?>"><?php esc_html_e( 'Privacy Policy.', 'woocommerce' ); ?></a></span>
						</label>
						<span x-validate-error="{message: errors.agreeTOS, touched: touched.agreeTOS}"></span>
					</div>
				</div>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<div class="ma-form__section">
					<div class="ma-form-actions">
						<button type="submit" class="ma-btn ma-btn--primary" :disabled="isFormSubmitting" :aria-busy="isFormSubmitting" x-loading="isFormSubmitting" data-loading-label="<?php esc_attr_e( 'Creating account...', 'woocommerce' ); ?>">
							<span class="ma-btn-content"><?php esc_html_e( 'Create Account', 'woocommerce' ); ?></span>
							<?php ma_form_icon_arrow_right(); ?>
						</button>
					</div>
				</div>

				<?php do_action( 'woocommerce_register_form_end' ); ?>
			</form>
		</div>
	</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
