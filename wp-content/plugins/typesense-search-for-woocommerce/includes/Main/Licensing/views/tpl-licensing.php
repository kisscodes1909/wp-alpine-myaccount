<?php
$settings = [ 'status' => '', 'license' => '' ];
$settings = ( isset( $args ) && is_array( $args ) ) ? array_merge( $settings, $args ) : $settings;
?>
<div class="cm-typesense-addon-wrapper">
    <form class="form" action="" method="POST">
		<?php wp_nonce_field( 'cm_typesense_wc_verify_licensing_nonce', 'cm_typesense_wc_licensing_nonce' ); ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
					<?php _e( 'WooCommerce License Key', 'typesense-search-for-woocommerce' ); ?>
                </th>
                <td>
                    <div class="cm-typesense-activator-inputs" style="display: inline-block;vertical-align: top;">
						<?php if ( $settings['status'] !== 'valid' ) { ?>
                            <input id="cm_tsfwc_recurring_license_ley"
                                   name="cm_tsfwc_recurring_license_ley"
                                   type="text" class="regular-text" 
                                   value="<?php esc_attr_e( $settings['license'] ); ?>"
                                   placeholder="<?php _e( 'Your license key here', 'typesense-search-for-woocommerce' ); ?>"/>
						<?php } ?>
                    </div>
                    <div class="cm-tsfwc-addon-activator-buttons cm-typesense-addon-activator-buttons" style="display: inline-block;vertical-align: top;">
						<?php if ( $settings['status'] == 'valid' ) { ?>
                            <input type="submit"
                                   class="button button-primary"
                                   name="cm_tsfwc_recurring_deactivate"
                                   value="Deactivate License"/>
						<?php } else { ?>
                            <input type="submit"
                                   class="button button-primary"
                                   name="cm_tsfwc_recurring_activate"
                                   value="Activate License"/>
						<?php } ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>