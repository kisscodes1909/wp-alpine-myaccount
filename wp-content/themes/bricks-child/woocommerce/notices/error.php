<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notices ) {
	return;
}

?>


<div class="error bg-white shadow-xl border border-neutral-600 border-opacity-50 flex" role="alert">
    <ul class="list-none px-8">
        <?php foreach ( $notices as $notice ) : ?>
            <li class="flex items-center gap-5" <?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                <div class="py-5"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M0.364819 0.364819C0.851244 -0.121606 1.64014 -0.121606 2.12692 0.364819L8 6.2379L13.8731 0.364819C14.3595 -0.121606 15.1484 -0.121606 15.6352 0.364819C16.1216 0.851244 16.1216 1.64014 15.6352 2.12692L9.76211 8L15.6352 13.8731C16.1216 14.3595 16.1216 15.1484 15.6352 15.6352C15.1488 16.1216 14.3599 16.1216 13.8731 15.6352L8 9.76176L2.12692 15.6352C1.6405 16.1216 0.851604 16.1216 0.364819 15.6352C-0.121606 15.1488 -0.121606 14.3599 0.364819 13.8731L6.2379 8L0.364819 2.12692C-0.121606 1.6405 -0.121606 0.851604 0.364819 0.364819Z" fill="#E48181"/>
                </svg></div>
                <div class="text-base">
                    <?php echo wc_kses_notice( $notice['notice'] ); ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

