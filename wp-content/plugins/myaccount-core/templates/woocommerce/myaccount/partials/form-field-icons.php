<?php
/**
 * Heroicons for My Account form fields (outline 24x24).
 * Used as left-side icons inside input wrappers.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'ma_form_icon_envelope' ) ) {
	/** Envelope icon – email fields. */
	function ma_form_icon_envelope() {
		?>
		<svg class="ma-form__input-icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
			<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
		</svg>
		<?php
	}
}

if ( ! function_exists( 'ma_form_icon_lock_closed' ) ) {
	/** Lock closed icon – password fields. */
	function ma_form_icon_lock_closed() {
		?>
		<svg class="ma-form__input-icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
			<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
		</svg>
		<?php
	}
}

if ( ! function_exists( 'ma_form_icon_user' ) ) {
	/** User icon – first/last name fields. */
	function ma_form_icon_user() {
		?>
		<svg class="ma-form__input-icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
			<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
		</svg>
		<?php
	}
}

if ( ! function_exists( 'ma_form_icon_phone' ) ) {
	/** Phone icon – phone number fields. */
	function ma_form_icon_phone() {
		?>
		<svg class="ma-form__input-icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
			<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372a2.25 2.25 0 00-.947-1.897l-4.083-2.04a2.25 2.25 0 00-2.65.372l-.97 1.293a16.962 16.962 0 006.062 6.062l1.292-.97a2.25 2.25 0 00.372-2.65l-2.04-4.083a2.25 2.25 0 00-1.897-.947H18.75A2.25 2.25 0 0116.5 6.75v2.25z" />
		</svg>
		<?php
	}
}

if ( ! function_exists( 'ma_form_icon_map_pin' ) ) {
	/** Map pin icon – address, city, region, postal fields. */
	function ma_form_icon_map_pin() {
		?>
		<svg class="ma-form__input-icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
			<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
			<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
		</svg>
		<?php
	}
}

if ( ! function_exists( 'ma_form_icon_globe_alt' ) ) {
	/** Globe alt icon – country (select) field. */
	function ma_form_icon_globe_alt() {
		?>
		<svg class="ma-form__input-icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
			<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
		</svg>
		<?php
	}
}
