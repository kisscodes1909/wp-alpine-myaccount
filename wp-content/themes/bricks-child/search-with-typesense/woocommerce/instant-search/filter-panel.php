<?php
$passed_args = $args['passed_args'] ?? [];
?>
<div class="cmtsfwc-FilterPanel">
	<?php do_action( 'cm_tsfwc_before_filter_panel_start' ); ?>
    <div class="cmtsfwc-FilterToggle">
         <span class="cmtsfwc-FilterToggle-label">
                 <?php _e( 'Filter', 'typesense-search-for-woocommerce' ); ?>
            </span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 cmtsfwc-FilterToggle-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
        </svg>
    </div>
    <div class="cmtsfwc-Filter-items">
        <div class="cmtsfwc-Filter-itemsHeader">
            <h3><?php _e( 'Filter Search Results', 'typesense-search-for-woocommerce' ); ?></h3>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 cmtsfwc-Filter-itemsHeaderCloseIcon cmtsfwc-Filter-itemsClose" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div class="cmtsfwc-Filter-itemsContent">
			<?php
			/**
			 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks - category_filter 5
			 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks - price_filter 10
			 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks - rating_filter 15
			 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks - product_filter 20
			 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks - custom_filters 25
			 */
			do_action( 'cm_tsfwc_filter_panel_output', $passed_args );
			?>
        </div>
        <div class="cmtsfwc-Filter-itemsFooter">
            <a href="#" class="cmtsfwc-Filter-itemsFooterLink cmtsfwc-Filter-itemsClose" onclick="void(0)"><?php _e( 'Close', 'typesense-search-for-woocommerce' ); ?></a>
        </div>
    </div>
	<?php
	//allows other widgets / code etc. that are not dependent on instant search to be added.
	do_action( 'cm_tsfwc_before_filter_panel_end' );
	?>
</div>