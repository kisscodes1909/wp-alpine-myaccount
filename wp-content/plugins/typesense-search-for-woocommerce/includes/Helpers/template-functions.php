<?php
if ( ! function_exists( 'cm_tsfwc_get_template' ) ) {
	function cm_tsfwc_get_template( $file_name = '', $args = [] ) {
		\Codemanas\Typesense\WooCommerce\Helpers\Templates::getInstance()->include_file( $file_name, $args );
	}
}