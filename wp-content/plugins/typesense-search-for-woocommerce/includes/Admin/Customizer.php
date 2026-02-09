<?php

namespace Codemanas\Typesense\WooCommerce\Admin;

class Customizer {
	public static $instance = null;

	public static function getInstance() {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		add_action( 'customize_register', [ $this, 'register_settings' ] );
		add_action( 'customize_controls_print_scripts', [ $this, 'add_scripts' ], 70 );

	}

	/**
	 * @param \WP_Customize_Manager $wp_customize
	 */
	public function register_settings( \WP_Customize_Manager $wp_customize ) {

		$wp_customize->add_section( 'cm_tsfwc_global_settings_section', [
			'title'      => __( 'Typesense Settings', 'typesense-search-for-woocommerce' ),
			'panel'      => 'woocommerce', // Not typically needed.
			'priority'   => 160,
			'capability' => 'manage_woocommerce',
		] );

		$checkboxes = [
			'replace_shop'     => [
				'label'       => __( 'Replace Shop Pages', 'typesense-search-for-woocommerce' ),
				'description' => __( 'Replace shop page with Typesense instant search listing', 'typesense-search-for-woocommerce' )
			],
			'show_cat'        => [
				'label' => __( 'Show Category Filters', 'typesense-search-for-woocommerce' )
			],
			'show_price'      => [
				'label' => __( 'Show Price Filter', 'typesense-search-for-woocommerce' )
			],
			'show_rating'     =>
				[
					'label' => __( 'Show Rating Filter', 'typesense-search-for-woocommerce' )
				],
			'show_attr'       =>
				[
					'label' => __( 'Show Attribute Filter', 'typesense-search-for-woocommerce' )
				],
			'show_pagination' =>
				[
					'label' => __( 'Show Pagination', 'typesense-search-for-woocommerce' )
				],
			'show_sortby'     => [
				'label' => __( 'Show Sort By', 'typesense-search-for-woocommerce' )
			],
			'show_featured_first'     => [
				'label'       => __( 'Display featured products first', 'typesense-search-for-woocommerce' ),
				'description' => __( 'Display the feauted producst first while filtering', 'typesense-search-for-woocommerce' )
			],
		];
		foreach ( $checkboxes as $key => $checkbox ) {
			//always add settings first
			$wp_customize->add_setting( 'cm_tsfwc_global_setting[' . $key . ']', [
				'type'       => 'option',
				'capability' => 'manage_woocommerce',
				'transport'  => 'refresh',
				'default'    => true,
			] );

			$wp_customize->add_control( 'cm_tsfwc_global_setting[' . $key . ']', [
				'type'        => 'checkbox',
				'priority'    => 10, // Within the section.
				'section'     => 'cm_tsfwc_global_settings_section', // Required, core or custom.
				'label'       => $checkbox['label'] ?? '',
				'description' => $checkbox['description'] ?? '',
			] );

            if($key == 'show_sortby'){
                //show after default sort by
	            $wp_customize->add_setting( 'cm_tsfwc_global_setting[default_sort_by]', [
		            'type'       => 'option',
		            'capability' => 'manage_woocommerce',
		            'transport'  => 'refresh',
		            'default'    => 'recent',
	            ] );

	            $wp_customize->add_control( 'cm_tsfwc_global_setting[default_sort_by]', [
		            'type'        => 'select',
                    'choices' => [
	                    'recent'                     => __( 'Recent', 'typesense-search-for-woocommerce' ),
	                    'oldest'                     => __( 'Oldest', 'typesense-search-for-woocommerce' ),
	                    'sort_by_rating_low_to_high' => __( 'Sort by rating: low to high', 'typesense-search-for-woocommerce' ),
	                    'sort_by_rating_high_to_low' => __( 'Sort by rating: high to low', 'typesense-search-for-woocommerce' ),
	                    'sort_by_price_low_to_high'  => __( 'Sort by price: low to high', 'typesense-search-for-woocommerce' ),
	                    'sort_by_price_high_to_low'  => __( 'Sort by price: high to low', 'typesense-search-for-woocommerce' ),
	                    'sort_by_popularity'         => __( 'Sort by popularity', 'typesense-search-for-woocommerce' ),
                    ],
		            'priority'    => 10, // Within the section.
		            'section'     => 'cm_tsfwc_global_settings_section', // Required, core or custom.
		            'label' => __( 'Select Default Sort By', 'typesense-search-for-woocommerce' ),
		            'description' => __( 'Select the default Sort By option', 'typesense-search-for-woocommerce' ),
	            ] );
            }

            if($key == 'show_pagination'){
                //show after default sort by
                $wp_customize->add_setting( 'cm_tsfwc_global_setting[pagination_type]', [
                        'type'       => 'option',
                        'capability' => 'manage_woocommerce',
                        'transport'  => 'refresh',
                        'default'    => 'recent',
                ] );

                $wp_customize->add_control( 'cm_tsfwc_global_setting[pagination_type]', [
                        'type'        => 'select',
                        'choices' => [
                                'default'                     => __( 'Default', 'typesense-search-for-woocommerce' ),
                                'infinite'                     => __( 'Infinite ( with button )', 'typesense-search-for-woocommerce' ),
                        ],
                        'priority'    => 10, // Within the section.
                        'section'     => 'cm_tsfwc_global_settings_section', // Required, core or custom.
                        'label' => __( 'Select Pagination Type', 'typesense-search-for-woocommerce' ),
                ] );

                $wp_customize->add_setting( 'cm_tsfwc_global_setting[show_more_text]', [
                        'type'       => 'option',
                        'capability' => 'manage_woocommerce',
                        'transport'  => 'refresh',
                        'default'    => '',
                ] );

                $wp_customize->add_control( 'cm_tsfwc_global_setting[show_more_text]', [
                        'type'        => 'text',
                        'priority'    => 11,
                        'section'     => 'cm_tsfwc_global_settings_section',
                        'label'       => __( 'Show More Button Text', 'typesense-search-for-woocommerce' ),
                        'description'       => __( 'Show More Button Text when Infinite( with button ) pagination is enabled', 'typesense-search-for-woocommerce' ),
                        'santize_callback' => 'santize_text_field'
                ] );
            }
		}

		// Change checkbox to radio for adding popup as option
		$wp_customize->add_setting( 'cm_tsfwc_global_setting[replace_product_search]', [
			'type'       => 'option',
			'capability' => 'manage_woocommerce',
			'transport'  => 'refresh',
			'default'    => 'autocomplete',
		] );

		$wp_customize->add_control( 'cm_tsfwc_global_setting[replace_product_search]', [
			'type'        => 'radio',
			'priority'    => 20,
			'section'     => 'cm_tsfwc_global_settings_section',
			'label'       => __( 'Replace Product Search', 'typesense-search-for-woocommerce' ),
			'description' => __( 'Replaces WooCommerce search widget and block with autocomplete or popup.' ),
            'santize_callback' => 'santize_text_field',
			'choices' => [
				'autocomplete' => __( 'With autocomplete', 'typesense-search-for-woocommerce' ),
				'popup' => __( 'With popup', 'typesense-search-for-woocommerce' ),
			],
		] );

		$wp_customize->add_setting( 'cm_tsfwc_global_setting[search_placeholder]', [
			'type'       => 'option',
			'capability' => 'manage_woocommerce',
			'transport'  => 'refresh',
			'default'    => '',
		] );

		$wp_customize->add_control( 'cm_tsfwc_global_setting[search_placeholder]', [
			'type'        => 'text',
			'priority'    => 30,
			'section'     => 'cm_tsfwc_global_settings_section',
			'label'       => __( 'Search Placeholder', 'typesense-search-for-woocommerce' ),
			'description' => __( 'Change placeholder for Typesense Search Bar' ),
            'santize_callback' => 'santize_text_field'
		] );


	}

	public function add_scripts() {
		?>
        <script type="text/javascript">
            (function ($) {
                $(function () {
                    wp.customize.section('cm_tsfwc_global_settings_section', function (section) {
                        // console.log('digthis');
                        section.expanded.bind(function (isExpanded) {
                            if (isExpanded) {
                                wp.customize.previewer.previewUrl.set('<?php echo esc_js( wc_get_page_permalink( 'shop' ) ); ?>');
                            }
                        });
                    });
                })
            }(jQuery))

        </script>
		<?php
	}
}
