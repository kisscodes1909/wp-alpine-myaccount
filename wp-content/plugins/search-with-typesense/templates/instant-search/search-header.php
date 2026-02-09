<?php

$facets      = $args['facet'] ?? [];
$config      = $args['config'] ?? [];
$schema      = $args['schema'] ?? [];
$passed_args = $args['passed_args'] ?? [];
?>
<div class="cmswt-Header">
	<?php do_action( 'cm_typesense_instant_search_header', $passed_args, $config, $facets, $schema ); ?>
</div>