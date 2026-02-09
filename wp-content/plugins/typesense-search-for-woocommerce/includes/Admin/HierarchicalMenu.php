<?php

namespace Codemanas\Typesense\WooCommerce\Admin;

use Codemanas\Typesense\WooCommerce\Main\Fields\Fields;

class HierarchicalMenu {
	public static ?HierarchicalMenu $instance = null;

	public static function get_instance(): ?HierarchicalMenu {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		//to do - do not forget to delete/unset term if term is deleted
		add_action( 'edited_terms', [ $this, 'update_reference_on_terms_edited' ], 10, 2 );
		add_action( 'created_term', [ $this, 'update_reference_on_terms_created' ], 10, 3 );
		add_action( 'pre_delete_term', [ $this, 'remove_on_term_deleted' ], 10, 2 );
	}

	public function update_reference_on_terms_created( $term_id, $tt_id, $taxonomy ) {
		$this->update_reference( $term_id, $taxonomy );
	}

	public function update_reference_on_terms_edited( $term_id, $taxonomy ) {
		$this->update_reference( $term_id, $taxonomy );
	}

	public function remove_on_term_deleted( $term_id, $taxonomy ) {
		if ( $taxonomy != 'product_cat' ) {
			return;
		}
		$hierarchical_settings  = Fields::get_option( 'hierarchical_settings' ) ?? [];
		if ( isset( $hierarchical_settings['hierarchical_cats_data']['hierarchical_cats'] ) && ! empty( $hierarchical_settings['hierarchical_cats_data']['hierarchical_cats'] ) ) {
			unset( $hierarchical_settings['hierarchical_cats_data']['hierarchical_cats'][ $term_id ] );
		}
		Fields::set_option( 'hierarchical_settings', $hierarchical_settings );

	}

	public function update_reference( $term_id, $taxonomy ) {
		if ( $taxonomy !== 'product_cat' ) {
			return;
		}

		$hierarchical_settings  = Fields::get_option( 'hierarchical_settings' ) ?? [];
		$hierarchical_cats_data = $hierarchical_settings['hierarchical_cats_data'] ?? [];
		$hierarchical_cats      = $hierarchical_cats_data['hierarchical_cats'] ?? [];
		$max_cat_level          = $hierarchical_cats_data['max_cat_level'] ?? 0;

		$updated_term = get_term( $term_id, $taxonomy );
		//do until parent is ! empty
		$hierarchical_cats[ $term_id ] = [ $updated_term->name ];
		$hierarchical_cats             = $this->get_formatted_ancestors( $updated_term, $term_id, $taxonomy, $hierarchical_cats );
		$term_depth_count              = count( $hierarchical_cats[ $term_id ] );

		$hierarchical_settings['hierarchical_cats_data']['hierarchical_cats'] = $hierarchical_cats;
		if ( $term_depth_count > $max_cat_level ) {
			$hierarchical_settings['hierarchical_cats_data']['max_cat_level'] = $term_depth_count;
		}
		Fields::set_option( 'hierarchical_settings', $hierarchical_settings );
	}

	private function get_formatted_ancestors( $term, $original_term_id, $taxonomy, $hierarchical_cats ) {
		if ( $term->parent == 0 ) {
			return $hierarchical_cats;
		}


		$parent_term = get_term( $term->parent, $taxonomy );

		if ( isset( $hierarchical_cats[ $original_term_id ] ) && is_array( $hierarchical_cats[ $original_term_id ] ) && ! empty( $hierarchical_cats[ $original_term_id ] ) ) {
			foreach ( $hierarchical_cats[ $original_term_id ] as $k => $saved_term_reference ) {
				$hierarchical_cats[ $original_term_id ][ $k ] = $parent_term->name . ' > ' . $saved_term_reference;
			}
		}

		array_unshift( $hierarchical_cats[ $original_term_id ], $parent_term->name );

		return $this->get_formatted_ancestors( $parent_term, $original_term_id, $taxonomy, $hierarchical_cats );
	}
}