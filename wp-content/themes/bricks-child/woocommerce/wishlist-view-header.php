<?php
/**
 * Wishlist header
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Templates\Wishlist\View
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist \YITH_WCWL_Wishlist Current wishlist
 * @var $is_custom_list bool Whether current wishlist is custom
 * @var $can_user_edit_title bool Whether current user can edit title
 * @var $form_action string Action for the wishlist form
 * @var $page_title string Page title
 * @var $fragment_options array Array of items to use for fragment generation
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>
<?php
/**
 * DO_ACTION: yith_wcwl_before_wishlist_form
 *
 * Allows to render some content or fire some action before the wishlist form.
 *
 * @param YITH_WCWL_Wishlist $wishlist Wishlist object
 */
do_action( 'yith_wcwl_before_wishlist_form', $wishlist );
?>

<form
	id="yith-wcwl-form"
	action="<?php echo esc_attr( $form_action ); ?>"
	method="post"
	class="yith-wcwl-form wishlist-fragment md:container mx-auto px-8"
	data-fragment-options="<?php echo wc_esc_json( wp_json_encode( $fragment_options ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
>

	<!-- TITLE -->
	<?php
	/**
	 * DO_ACTION: yith_wcwl_before_wishlist_title
	 *
	 * Allows to render some content or fire some action before the wishlist title.
	 *
	 * @param YITH_WCWL_Wishlist $wishlist Wishlist object
	 */
	do_action( 'yith_wcwl_before_wishlist_title', $wishlist );

	?>
    <?php wc_get_template('myaccount/page-heading.php',
        [
            'page_heading' => 'Wishlist',
            'page_description' => 'Items you\'ve saved for later',
        ]
    ); ?>
    <?php

	/**
	 * DO_ACTION: yith_wcwl_before_wishlist
	 *
	 * Allows to render some content or fire some action before the wishlist.
	 *
	 * @param YITH_WCWL_Wishlist $wishlist Wishlist object
	 */
	do_action( 'yith_wcwl_before_wishlist', $wishlist );
	?>
