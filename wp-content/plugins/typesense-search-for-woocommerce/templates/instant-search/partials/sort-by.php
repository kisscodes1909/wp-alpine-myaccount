<?php

use Codemanas\Typesense\Main\TypesenseAPI;
use Codemanas\Typesense\WooCommerce\Main\Fields\Fields;

$args = $args ?? [];

$show_featured_first    = $args['passed_args']['show_featured_first'] ?? false;
$maybe_sort_by_featured = ( $show_featured_first == 'yes' ) ? 'is_featured:desc,' : '';
$collection_product_name = TypesenseAPI::getInstance()->getCollectionNameFromSchema( 'product' );
$tsfwc_wc_settings       = Fields::get_option( 'global_setting' );
$sort_by_key             = $tsfwc_wc_settings['default_sort_by'];
$items                   = [
	[
		'label' => __( 'Default', 'typesense-search-for-woocommerce' ),
		'value' => $collection_product_name,
	],
	[
		'label' => __( 'Recent', 'typesense-search-for-woocommerce' ),
		'value' => $collection_product_name . '/sort/' . $maybe_sort_by_featured . 'sort_by_date:desc',
	],
	[
		'label' => __( 'Oldest', 'typesense-search-for-woocommerce' ),
		'value' => $collection_product_name . '/sort/' . $maybe_sort_by_featured . 'sort_by_date:asc',
	],
	[
		'label' => __( 'Sort by rating: low to high', 'typesense-search-for-woocommerce' ),
		'value' => $collection_product_name . '/sort/' . $maybe_sort_by_featured . 'rating:asc',
	],
	[
		'label' => __( 'Sort by rating: high to low', 'typesense-search-for-woocommerce' ),
		'value' => $collection_product_name . '/sort/' . $maybe_sort_by_featured . 'rating:desc',
	],
	[
		'label' => __( 'Sort by price: low to high', 'typesense-search-for-woocommerce' ),
		'value' => $collection_product_name . '/sort/' . $maybe_sort_by_featured . 'price:asc',
	],
	[
		'label' => __( 'Sort by price: high to low', 'typesense-search-for-woocommerce' ),
		'value' => $collection_product_name . '/sort/' . $maybe_sort_by_featured . 'price:desc',
	],
	[
		'label' => __( 'Sort by popularity', 'typesense-search-for-woocommerce' ),
		'value' => $collection_product_name . '/sort/' . $maybe_sort_by_featured . 'total_sales:desc',
	],
];
?>
<div class="cmtsfwc-SortBy"
     data-settings="<?php
     echo _wp_specialchars(
	     json_encode( apply_filters( 'cmtsfwc_sortby_settings', [ 'items' => $items ] ) ),
	     ENT_QUOTES,
	     'UTF-8',
	     true ); ?>"></div>