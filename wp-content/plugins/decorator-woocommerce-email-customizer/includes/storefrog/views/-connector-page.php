<?php
/**
 * StoreFrog connector page template.
 *
 * @package Storefrog_Connector
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include plugin_dir_path(__FILE__) . '--tab2.php';

?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php
// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- This font is only used in this page. ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<style>
.notice, div.error, p.error, div.updated, p.updated{ display:none; }
.wbte-sf-connector {
	width:100%; box-sizing: border-box; margin:0px; margin-left:-20px; padding:0px; font-family:"Poppins", serif;
}
.wbte-sf-top-box{ width:calc( 100% + 20px); padding:30px 20px 1px 20px; box-sizing:border-box; background:#F1F8FE; }
.wbte-sf-header {
	text-align: center;
	margin-bottom:2rem;
}
.wbte-sf-header h1{ font-size:28px; font-weight:700; color:#2d2d2d; }
</style>