<?php
$passed_args = $args['passed_args'] ?? [];
?>
<div class="cmtsfwc-Filter-customAttributes">
	<?php
	//allows adding custom / additional filters
	//https://codemanas.github.io/cm-typesense-docs/tsfwc/#displaying-the-filter
	do_action( 'cm_tsfwc_custom_attributes', $passed_args );
	?>
</div>