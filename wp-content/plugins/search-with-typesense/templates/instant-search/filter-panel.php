<?php
$facets = $args['facet'] ?? [];
$config = $args['config'] ?? [];
$schema = $args['schema'] ?? [];

$passed_args = $args['passed_args'] ?? [];
// Removed a logic to check multiple posttypes here so multi faceting can work
if ( $passed_args['filter'] === 'show' && ! empty( $facets ) ) { ?>
    <div class="cmswt-FilterPanel">
        <div class="cmswt-FilterPanel-items">
            <div class="cmswt-FilterPanel-itemsPopupHeader">
                <div class="cmswt-FilterPanel-itemsPopupLabel">
                    <h5 class="cmswt-FilterPanel-itemsPopupLabelHeader"><?php _e( 'Apply Filters', 'search-with-typesense' ); ?></h5>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6 cmswt-FilterPanel-itemsPopupHeaderCloseLogo cmswt-FilterPanel-itemsClose"
                         width="16" height="17" viewBox="0 0 16 17" fill="none">
                        <path d="M11.3334 5.16666L4.66675 11.8333M4.66675 5.16666L11.3334 11.8333" stroke="#2E2E2E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div class="cmswt-FilterPanel-itemsContent">
				<?php
				do_action( 'cm_typesense_instant_search_results_before_filter_panel', $facets );
				foreach ( $facets as $post_type => $filters ) {
					foreach ( $filters as $filter ) {
						$filter_label = $filter;
						if ( $filter_label == 'post_author' ) {
							$filter_label = __( 'Author', 'search-with-typesense' );
						}
						?>
                        <div class="cmswt-Filter cmswt-Filter-<?php echo esc_html( $filter ); ?> cmswt-Filter-collection_<?php echo $post_type; ?>"
                             data-facet_id="<?php echo esc_attr( $post_type . "_" . $filter ); ?>"
                             data-label="<?php echo esc_attr( apply_filters( 'cm_typesense_search_facet_label', $filter_label, $filter, $post_type ) ); ?>"
                             data-title="<?php esc_html_e(
							     apply_filters(
								     'cm_typesense_search_facet_title',
								     sprintf( '%s', esc_html( ucwords( $filter_label ) ) )
								     ,
								     $filter, $post_type ),
							     'search-with-typesense' ); ?>"
                             data-settings="<?php echo _wp_specialchars( json_encode( apply_filters( 'cm_typesense_search_facet_settings', [
							     'searchable' => false
						     ], $filter, $post_type ) ), ENT_QUOTES, 'UTF-8', true ); ?>"
                             data-filter_type="<?php echo apply_filters( 'cm_typesense_filter_type', 'refinement', $filter, $post_type ) ?>"
                        ></div>
					<?php }
				}
				do_action( 'cm_typesense_instant_search_results_after_filter_panel', $facets );
				?>
            </div>
            <div class="cmswt-FilterPanel-itemsFooter">
                <a class="cmswt-FilterPanel-itemsFooterCloseLink cmswt-FilterPanel-itemsClose">Close</a>
            </div>
        </div>
    </div>
<?php }