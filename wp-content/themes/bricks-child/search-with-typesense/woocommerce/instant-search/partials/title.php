<?php
$config = $args['config'] ?? [];
?>
<h2 class="cmtsfwc-ShopTitle">
	<?php
	$title = ( isset( $config['config']['post_type']['product']['label'] ) && $config['config']['post_type']['product']['label'] != '' ) ? $config['config']['post_type']['product']['label'] : $config['available_post_types']['product']['label'];

	echo esc_html( $title ); ?>
</h2>