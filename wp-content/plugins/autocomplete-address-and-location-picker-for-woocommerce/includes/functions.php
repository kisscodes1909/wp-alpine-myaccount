<?php

/**
 * Register the JavaScript for the public-facing side of the site.
 *
 * @since    1.0.0
 */
function aafw_autocomplete() {
    $aafw_billing_autocomplete = get_option( 'aafw_billing_autocomplete', '' );
    $aafw_shipping_autocomplete = get_option( 'aafw_shipping_autocomplete', '' );
    $aafw_pickup_autocomplete = get_option( 'aafw_pickup_autocomplete', '' );
    $aafw_google_api_key = get_option( 'aafw_google_api_key', '' );
    $aafw_initial_map = get_option( 'aafw_initial_map', '' );
    return ( '' !== $aafw_google_api_key && ('1' === $aafw_initial_map || '1' === $aafw_billing_autocomplete || '1' === $aafw_shipping_autocomplete || '1' === $aafw_pickup_autocomplete) ? true : false );
}

/**
 * Premium feature.
 *
 * @since 1.0.0
 * @param string $value text.
 * @return html
 */
function aafw_premium_feature(  $value  ) {
    $result = $value;
    if ( aafw_is_free() ) {
        $result = ' <div class="aafw_premium_feature">
						<a class="aafw_locker_button" href="#"><svg style="color:#ffc106" width=20 aria-hidden="true" focusable="false" class="aafw_premium_icon" role="img" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 390 511.815"><title>' . esc_attr__( 'Premium Feature', 'aafw' ) . '</title><path fill="currentColor" d="M24.983 197.869h16.918v-39.203c0-43.387 17.107-82.959 44.667-111.698C114.365 18 152.726 0 194.998 0c42.259 0 80.652 17.981 108.41 46.968 27.58 28.739 44.692 68.292 44.692 111.698v39.203h16.917c13.738 0 24.983 11.245 24.983 24.984v263.978c0 13.739-11.245 24.984-24.983 24.984H24.983C11.226 511.815 0 500.57 0 486.831V222.853c-.013-13.739 11.226-24.984 24.983-24.984zm149.509 173.905l-26.968 70.594h94.923l-24.966-71.573c15.852-8.15 26.688-24.67 26.688-43.719 0-27.169-22.015-49.169-49.184-49.169-27.153 0-49.153 22-49.153 49.169-.016 19.826 11.737 36.905 28.66 44.698zM89.187 197.869h211.602v-39.203c0-30.858-12.024-58.823-31.376-79.005-19.147-19.964-45.49-32.368-74.428-32.368-28.925 0-55.288 12.404-74.422 32.368-19.37 20.182-31.376 48.147-31.376 79.005v39.203z"/></svg></a>
					  	<div class="aafw_premium_feature_note" style="display:none">
						  <a href="#" class="aafw_premium_close">
						  <svg aria-hidden="true"  width=10 focusable="false" data-prefix="fas" data-icon="times" class="svg-inline--fa fa-times fa-w-11" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg></a>
						  <h2>' . esc_html( __( 'Premium Feature', 'aafw' ) ) . '</h2>
						  <p>' . esc_html( __( 'You Discovered a Premium Feature!', 'aafw' ) ) . '</p>
						  <p>' . esc_html( __( 'Upgrading to Premium will unlock it.', 'aafw' ) ) . '</p>
						  <a target="_blank" href="https://checkout.freemius.com/plugin/8803/plan/14760/licenses/1/" class="aafw_premium_buynow">' . esc_html( __( 'UNLOCK PREMIUM', 'aafw' ) ) . '</a>
						  </div>
					  </div>';
    }
    return $result;
}

/**
 * Check for free version
 *
 * @since 1.1.2
 * @return boolean
 */
function aafw_is_free() {
    if ( aafw_fs()->is__premium_only() && aafw_fs()->can_use_premium_code() ) {
        return false;
    } else {
        return true;
    }
}

/**
 * Admin plugin bar.
 *
 * @since 1.1.0
 * @return html
 */
function aafw_admin_plugin_bar() {
    return '<div class="aafw_admin_bar">' . esc_html( __( 'Developed by', 'aafw' ) ) . ' <a href="https://powerfulwp.com/" target="_blank">PowerfulWP</a> | <a href="https://powerfulwp.com/autocomplete-address-and-location-picker-for-woocommerce-premium" target="_blank" >' . esc_html( __( 'Premium', 'aafw' ) ) . '</a> | <a href="https://powerfulwp.com/docs/autocomplete-address-and-location-picker-for-woocommerce-premium/" target="_blank" >' . esc_html( __( 'Documents', 'aafw' ) ) . '</a></div>';
}
