<?php
$passed_args = $args['passed_args'] ?? [ 'rating_filter' => 'show' ];
if ( $passed_args['rating_filter'] === 'show' ) { ?>
    <div
            class="cmtsfwc-Filter-rating"
            data-attr_facet_name="<?php esc_attr_e( 'pa_rating_attribute_filter' ); ?>"
            data-attr_label="<?php  esc_html_e( 'Rating', 'typesense-search-for-woocommerce' ) ?>"
            data-title="<?php esc_html_e( 'Filter by Rating', 'typesense-search-for-woocommerce' ); ?>"
            data-settings="<?php echo _wp_specialchars( json_encode( apply_filters( 'cm_tsfwc_rating_facet_settings', [ 'max' => 5 ] ) ), ENT_QUOTES, 'UTF-8', true ); ?>"
            data-filter_type="<?php echo apply_filters( 'cm_tsfwc_rating_filter_type', 'ratingMenu' ); ?>"
    ></div>
<?php } ?>