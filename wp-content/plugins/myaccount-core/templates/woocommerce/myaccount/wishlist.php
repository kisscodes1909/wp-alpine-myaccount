<?php
/**
 * My Account wishlist endpoint.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

wc_get_template(
	'myaccount/page-heading.php',
	array(
		'page_heading'     => 'Wishlist',
		'page_description' => 'Save favorites for later and move them to cart when you are ready.',
	)
);
?>

<div class="ma-wishlist">
	<div class="ma-wishlist__shortcode">
		<?php
		echo do_shortcode( '[yith_wcwl_wishlist pagination="yes" per_page="6" layout="images"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shortcode renders trusted plugin HTML.
		?>
	</div>
</div>
