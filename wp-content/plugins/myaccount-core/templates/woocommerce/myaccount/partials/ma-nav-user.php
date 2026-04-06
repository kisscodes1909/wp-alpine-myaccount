<?php
/**
 * Account navigation user block (avatar, name, tier, status).
 *
 * Template vars:
 * - $ma_nav_user (WP_User) Required.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $ma_nav_user ) || ! ( $ma_nav_user instanceof WP_User ) || ! $ma_nav_user->exists() ) {
	return;
}

$ma_nav_avatar_url = get_avatar_url( $ma_nav_user->ID, array( 'size' => 96 ) );
$ma_nav_avatar_url = is_string( $ma_nav_avatar_url ) ? $ma_nav_avatar_url : '';

$ma_nav_first = trim( (string) $ma_nav_user->first_name );
$ma_nav_name  = '' !== $ma_nav_first ? $ma_nav_first : trim( (string) $ma_nav_user->display_name );
if ( '' === $ma_nav_name ) {
	$ma_nav_name = trim( (string) $ma_nav_user->user_login );
}
$ma_nav_display = '' !== $ma_nav_name ? sprintf( __( 'Hello %s', 'myaccount-core' ), $ma_nav_name ) : '';

$ma_nav_initials = mb_strtoupper( mb_substr( $ma_nav_name, 0, 1 ), 'UTF-8' );
if ( '' !== $ma_nav_first && '' !== trim( (string) $ma_nav_user->last_name ) ) {
	$ma_nav_initials = mb_strtoupper( mb_substr( $ma_nav_first, 0, 1 ) . mb_substr( trim( (string) $ma_nav_user->last_name ), 0, 1 ), 'UTF-8' );
}

$ma_nav_tier          = MyAccount_Core_Hooks::get_navigation_membership_label( $ma_nav_user );
$ma_nav_show_status   = (bool) apply_filters( 'myaccount_core_navigation_show_status', true, $ma_nav_user );
$ma_nav_status_label  = apply_filters( 'myaccount_core_navigation_status_label', __( 'Active', 'myaccount-core' ), $ma_nav_user );
?>
<div class="ma-nav__user">
	<div class="ma-nav__user-avatar" aria-hidden="true">
		<?php if ( '' !== $ma_nav_avatar_url ) : ?>
			<img
				class="ma-nav__user-avatar-img"
				src="<?php echo esc_url( $ma_nav_avatar_url ); ?>"
				alt=""
				width="96"
				height="96"
				loading="lazy"
				decoding="async"
				onerror="this.hidden=true;this.nextElementSibling.hidden=false;"
			/>
		<?php endif; ?>
		<span class="ma-nav__user-avatar-fallback"<?php echo '' !== $ma_nav_avatar_url ? ' hidden' : ''; ?>><?php echo esc_html( $ma_nav_initials ); ?></span>
	</div>
	<div class="ma-nav__user-body">
		<p class="ma-nav__user-name"><?php echo esc_html( $ma_nav_display ); ?></p>
		<?php if ( '' !== $ma_nav_tier ) : ?>
			<p class="ma-nav__user-tier"><?php echo esc_html( $ma_nav_tier ); ?></p>
		<?php endif; ?>
		<?php if ( $ma_nav_show_status ) : ?>
			<p class="ma-nav__user-status">
				<span class="ma-nav__user-status-dot" aria-hidden="true"></span>
				<span class="ma-nav__user-status-label"><?php echo esc_html( $ma_nav_status_label ); ?></span>
			</p>
		<?php endif; ?>
	</div>
</div>
