<?php

define('CHILD_DIR', get_stylesheet_directory());
define('CHILD_URL', get_stylesheet_directory_uri());
define('CHILD_ASSETS_URL', get_stylesheet_directory_uri() . '/assets');

require_once CHILD_DIR . '/includes/class-initializer.php';

/**
 * Register custom elements
 */
add_action( 'init', function() {
  $element_files = [
	  __DIR__ . '/elements/title.php',
	  __DIR__ . '/elements/woocommerce/woo-login-form.php',
	  __DIR__ . '/elements/woocommerce/Woo_Kisscodes_Popup_Authenication_Form.php',
	  __DIR__ . '/elements/woocommerce/Lost_Password_Form.php',
	  __DIR__ . '/elements/woocommerce/Reset_Password_Form.php',
	  __DIR__ . '/elements/woocommerce/Announcement_Bar.php',
  ];

  foreach ( $element_files as $file ) {
    \Bricks\Elements::register_element( $file );
  }
}, 11 );

/**
 * Add text strings to builder
 */
add_filter( 'bricks/builder/i18n', function( $i18n ) {
  // For element category 'custom'
  $i18n['custom'] = esc_html__( 'Custom', 'bricks' );

  return $i18n;
} );

// TODO: Module brick conditions
add_filter( 'bricks/conditions/options', 'simple_operating_system_condition' );
function simple_operating_system_condition( $options ) {
	// Ensure key is unique, and that group exists
	$options[] = [
		'key'   => 'wp_operating',
		'label' => esc_html__( 'Simple operating system', 'bricks' ),
		'group' => 'other',
		'compare' => [
			'type'        => 'select',
			'options'     =>  [
				'==' => esc_html__( 'is', 'bricks' ),
//				'!=' => esc_html__( 'is not', 'bricks' ),
			],
			'placeholder' => esc_html__( 'is', 'bricks' ),
		],
		'value'   => [
			'type'        => 'select',
			'options'     =>  [
				'desktop' => esc_html__( 'Desktop', 'bricks' ),
				'tablet_mobile' => esc_html__( 'Tablet & Mobile', 'bricks' ),
			],
			'placeholder' => esc_html__( 'Desktop', 'bricks' ),
		],
	];

	return $options;
}

add_filter( 'bricks/conditions/result', 'run_simple_operating_system_condition', 10, 3 );
function run_simple_operating_system_condition( $result, $condition_key, $condition ) {
	// If $condition_key is not 'my_post_type', we return the $render as it is
	if ( $condition_key !== 'wp_operating' ) {
		return $result;
	}

	$value      = $condition['value'] ?? 'desktop';

	if($value == 'desktop' && !wp_is_mobile()) {
		$condition_met = true;
	} else if($value == 'tablet_mobile' && wp_is_mobile()) {
		$condition_met = true;
	} else {
		$condition_met = false;
	}

	return $condition_met;
}

add_filter('woocommerce_cart_item_name', 'custom_woocommerce_cart_item_name', 10, 3);

function custom_woocommerce_cart_item_name($product_name, $cart_item, $cart_item_key) {
	$product = $cart_item['data'];

	if ($product->is_type('variation')) {
		$parent_product_name = get_parent_product_name($product);
		$attributes_html = get_variation_attributes_html($product);

		return $parent_product_name;
	}

	return $product->get_name();
}

function get_parent_product_name($product) {
	$parent_product = wc_get_product($product->get_parent_id());
	return $parent_product ? $parent_product->get_name() : '';
}

function get_variation_attributes_html($product) {
	$variation_attributes = $product->get_variation_attributes();
	$attributes_html = '<div class="variation-attributes capitalize m-0 p-0">';

	foreach ($variation_attributes as $attribute_name => $attribute_value) {
		$taxonomy = str_replace('attribute_', '', $attribute_name);
		$attribute_label = wc_attribute_label($taxonomy);
		$term = get_term_by('slug', $attribute_value, $taxonomy);

		if ($term && !is_wp_error($term)) {
			$attributes_html .= '<div class="m-0 p-0">' . esc_html($attribute_label) . ': ' . esc_html($term->name) . '</div>';
		} else {
			$attributes_html .= '<div class="m-0 p-0">' . esc_html($attribute_label) . ': ' . esc_html($attribute_value) . '</div>';
		}
	}

	$attributes_html .= '</div>';
	return $attributes_html;
}

/* Reformat Product Attribute to Array */

add_filter('cm_tsfwc_data_before_entry', function ($formatted_data, $raw_data, $object_id, $schema_name) {
	if($schema_name === 'product') {
		$formatted_data['pa_Sizes_attribute_filter'] = explode('|', preg_replace('/\s+/', '', $formatted_data['pa_Sizes_attribute_filter'][0]) );
		$formatted_data['pa_Colors_attribute_filter'] = explode('|', preg_replace('/\s+/', '', $formatted_data['pa_Colors_attribute_filter'][0]));
	}
	return $formatted_data;
}, 10, 4);






