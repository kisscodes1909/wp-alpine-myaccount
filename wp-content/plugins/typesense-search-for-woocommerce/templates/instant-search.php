<?php

use Codemanas\Typesense\Backend\Admin;
use Codemanas\Typesense\Main\TypesenseAPI;
use Codemanas\Typesense\WooCommerce\Frontend\Frontend;
use Codemanas\Typesense\WooCommerce\Main\Fields\Fields;

$args = $args ?? [];

$config            = Admin::get_search_config_settings();
$tsfwc_wc_settings = Fields::get_option( 'global_setting' );

//slug will always for product
$args['collection'] = TypesenseAPI::getInstance()->getCollectionNameFromSchema( 'product' );

//Sort by options
$default_sorting_options = [
	'recent'                     => '_text_match:desc,sort_by_date:desc',
	'oldest'                     => '_text_match:desc,sort_by_date:asc',
	'sort_by_rating_low_to_high' => '_text_match:desc,rating:asc',
	'sort_by_rating_high_to_low' => '_text_match:desc,rating:desc',
	'sort_by_price_low_to_high'  => '_text_match:desc,price:desc',
	'sort_by_price_high_to_low'  => '_text_match:desc,price:desc',
	'sort_by_popularity'         => '_text_match:desc,total_sales:desc',
];
$maybe_sort_by_featured  = $maybe_sort_by_featured = ( $args['show_featured_first'] == 'yes' ) ? 'is_featured:desc,' : '';
$sort_by_key             = $tsfwc_wc_settings['default_sort_by'];
$sorting_initial = !empty($default_sorting_options[ $sort_by_key ])? $default_sorting_options[ $sort_by_key ]: '_text_match:desc,sort_by_date:desc';
$defaultSortBy           = $maybe_sort_by_featured . $sorting_initial;
$args['default_sort_by'] = apply_filters( 'cmtsfwc_sortby_default', $defaultSortBy );

//css class
$product_open_css_class = Frontend::getInstance()->get_woocommerce_product_loop_start_class();

//language hook for plugins like WPML
$current_lang = apply_filters( 'tsfwc_current_lang', null );

$additional_classes = [];
if ( ! empty( $args['custom_class'] ) ) {
	$custom_classes = explode( ',', $args['custom_class'] );
	foreach ( $custom_classes as $custom_class ) {
		$additional_classes[] = $custom_class;
	}
}

//current page
$current_page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
?>
<!-- Additonal config  -->
<div class="cmtsfwc-InstantSearch ais-InstantSearch <?php echo esc_html( implode( ' ', $additional_classes ) ); ?>"
     data-id="<?php echo esc_html( $args['unique_id'] ); ?>"
     data-config="<?php echo _wp_specialchars( json_encode( $args ), ENT_QUOTES, 'UTF-8', true ); ?>"
     data-placeholder="<?php echo esc_html( $args['placeholder'] ?? "Search for..." ); ?>"
     data-product_css_class="<?php echo esc_html( $product_open_css_class ); ?>"
     data-query_by="<?php echo esc_html( $args['query_by'] ); ?>"
     data-query_length="<?php echo apply_filters( 'cm_tsfwc_search_query_length', 0 ); ?>"
     data-lang="<?php echo $current_lang ?>"
     data-additional_search_params="<?php echo _wp_specialchars( json_encode( apply_filters( 'cm_tsfwc_additional_search_params', [] ) ), ENT_QUOTES, 'UTF-8', true ); ?>"
     data-additional_config="<?php echo _wp_specialchars( json_encode( apply_filters( 'cm_tsfwc_additional_config', [] ) ), ENT_QUOTES, 'UTF-8', true ); ?>"
     data-current_page="<?php echo esc_attr( $current_page ); ?>"
>
	<?php do_action( 'cm_tsfwc_instant_search_before_output', $args, $config ); ?>
    <div class="cmtsfwc-InstantSearch-overlay cmtsfwc-FilterPanel-itemsClose"></div>
    <div class="cmtsfwc-SearchHeader">
		<?php
		/**
		 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks search_box - 5
		 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks search_stats - 10
		 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks search_refinements - 15
		 */
		do_action( 'cm_tsfwc_instant_search_results_header', $args, $config );
		?>
    </div>
	<?php
	/**
	 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks - filter_panel - 5
	 * Codemanas\Typesense\WooCommerce\Main\TemplateHooks - main_panel - 10
	 */
	do_action( 'cm_tsfwc_instant_search_results_output', $args, $config );
	do_action( 'cm_tsfwc_instant_search_after_output', $args, $config ); ?>
</div>
