<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php do_action( 'bricks_meta_tags' ); ?>
<?php wp_head(); ?>
</head>

<?php
do_action( 'bricks_body' );

do_action( 'bricks_before_site_wrapper' );

do_action( 'bricks_before_header' );

/**
 * @author Huu Nguyen
 * @override Parent Header
 * We do not show on my account page with non logged id state
 */

//if( !(!is_user_logged_in() && is_account_page()) ) {
    do_action( 'render_header' );
//}

do_action( 'bricks_after_header' );
