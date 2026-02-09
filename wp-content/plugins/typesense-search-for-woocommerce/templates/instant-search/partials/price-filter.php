<?php
$passed_args = $args['passed_args'] ?? [ 'price_filter' => 'show' ];
if ( $passed_args['price_filter'] === 'show' ) { ?>
    <div
            class="cmtsfwc-Filter-price"
            data-attr_facet_name="<?php esc_attr_e( 'prices' ); ?>"
            data-attr_label="<?php esc_html_e( 'Price', 'typesense-search-for-woocommerce' ) ?>"
            data-title="<?php esc_html_e( 'Filter by Price', 'typesense-search-for-woocommerce' ); ?>"
            data-settings="<?php echo _wp_specialchars( json_encode( apply_filters( 'cm_tsfwc_price_facet_settings', [ 'pips' => false ] ) ), ENT_QUOTES, 'UTF-8', true ); ?>"
            data-filter_type="<?php echo apply_filters( 'cm_tsfwc_price_filter_type', 'rangeSlider' ); ?>"
    ></div>
<?php }