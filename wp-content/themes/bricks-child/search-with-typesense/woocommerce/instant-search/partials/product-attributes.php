<?php
$passed_args = $args['passed_args'] ?? [ 'attribute_filter' => 'show' ];
if ( $passed_args['attribute_filter'] === 'show' ) {
	$attributes_taxes = wc_get_attribute_taxonomies();
	foreach ( $attributes_taxes as $attribute ) {
		$skip_attribute_names = apply_filters( 'cm_tsfwc_attribute_facet_skip', [] );
		if ( in_array( $attribute->attribute_name, $skip_attribute_names ) ) {
			continue;
		}
		$terms = get_terms( [ 'taxonomy' => wc_attribute_taxonomy_name( $attribute->attribute_name ), 'hide_empty' => true ] );
		//don't add to show if pa_taxonomy does have any terms
		if ( empty( $terms ) ) {
			continue;
		}
		$attr_label = wc_attribute_label( 'pa_' . $attribute->attribute_name );
		?>
        <div
                class="cmtsfwc-Filter-productAttribute cmtsfwc-Filter-productAttribute--<?php echo esc_attr( $attribute->attribute_name ); ?>"
                data-attr_facet_name="<?php esc_attr_e( 'pa_' . $attribute->attribute_name . '_attribute_filter' ); ?>"
                data-attr_label="<?php echo $attr_label; ?>"
                data-attr_name="<?php echo esc_attr( $attribute->attribute_name ); ?>"
                data-title="<?php printf( esc_html__( 'Filter by %s', 'typesense-search-for-woocommerce' ), $attr_label ) ?>"
                data-settings="<?php echo _wp_specialchars( json_encode( apply_filters( 'cm_tsfwc_attribute_facet_settings', [ 'searchable' => false ], $attribute->attribute_name ) ), ENT_QUOTES, 'UTF-8', true ); ?>"
                data-filter_type="<?php echo apply_filters( 'cm_tsfwc_attribute_filter_type', 'refinementList', $attribute->attribute_name ); ?>"
        ></div>
		<?php
	}
}