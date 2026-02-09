<?php
$config      = $args['config'] ?? [];
$passed_args = $args['passed_args'] ?? [];
?>
<div class="cmtsfwc-Header">
	<?php
	/***
	 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks - results_title - 5
	 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks - sort_by - 10
	 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks - hits_per_page - 15
	 */
	do_action( 'cm_tsfwc_instant_search_results_header_output', [
		'passed_args' => $passed_args,
		'config'      => $config,
	] );
	?>
</div>