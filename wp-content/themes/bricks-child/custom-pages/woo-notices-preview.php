<?php
/*
Template Name: WooCommerce Notices Preview
*/
get_header();
?>

<div class="woocommerce-notices-wrapper space-y-10 md:container mx-auto px-8" >
    <?php
        wc_print_notice('Đây là thông báo thành công.', 'success');
        wc_print_notice('Đây là thông báo thông tin.', 'notice');
        // Add multiple notices
        wc_add_notice( 'Your first notice text here.', 'error' );
        wc_add_notice( 'Your second notice text here.', 'success' );
        wc_add_notice( 'Your third notice text here.', 'notice' );

        // Print all notices
        wc_print_notices();
    ?>
</div>


<?php get_footer(); ?>