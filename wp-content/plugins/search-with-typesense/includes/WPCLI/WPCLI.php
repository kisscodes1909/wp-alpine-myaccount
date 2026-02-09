<?php

namespace Codemanas\Typesense\WPCLI;

use Codemanas\Typesense\Main\TypesenseAPI;
use Codemanas\Typesense\Backend\Admin;
use WP_CLI;

class WPCLI {
	public static ?WPCLI $instance = null;
	private ?TypesenseAPI $typeSenseAPIInterface;

	public static $posts_per_page = 40;

	public static function getInstance(): ?WPCLI {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		add_action( 'cli_init', [ $this, 'register_commands' ] );
		$this->typeSenseAPIInterface = TypesenseAPI::getInstance();
	}

	public function index( $args, $assoc_args ) {
		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( __( 'Please specify the post type which you want to index.', 'search-with-typesense' ) );
		}

		$post_type = $args[0];

		if( isset( $assoc_args['ids'] ) ) {
			$post_ids_arr = explode( ',', $assoc_args['ids'] );

			foreach( $post_ids_arr as $post_id ) {
				$post = get_post( $post_id );

				if( ! is_null( $post) ) {
					$document = $this->typeSenseAPIInterface->formatDocumentForEntry( $post, $post_id, $post_type );
					$response = $this->typeSenseAPIInterface->upsertDocument( $post_type, $document );

					if( is_wp_error( $response ) ) {
						\WP_CLI::error( $response );
					} else {
						\WP_CLI::success( $post_type . ' ID: ' . $post_id . ' indexed successfully!!!' );
					}
				} else {
					\WP_CLI::error( $post_type . ' ID: ' . $post_id . ' does not exist.' );
				}
			}
		} else {
			// Check if the collection exists, if not create the collection
			$schemaDetails = $this->typeSenseAPIInterface->getCollectionInfo( $post_type );
			if( is_wp_error( $schemaDetails ) ) {
				$schema = $this->typeSenseAPIInterface->getSchema( $post_type );
				$schemaMaybeCreated = $this->typeSenseAPIInterface->createCollection( $schema );
			}

			// Get total post count.
			$total_posts = wp_count_posts( $post_type )->publish;
			// Use the smaller of self::$posts_per_page and $total_posts
			$posts_per_chunk = ( self::$posts_per_page <= $total_posts ) ? self::$posts_per_page : $total_posts;
			$chunks          = range( 0, $total_posts, $posts_per_chunk );

			foreach ( $chunks as $index => $chunk ) {
				WP_CLI::runcommand( "typesense bulkimport {$post_type} --offset={$chunk}", [ 'launch' => true ] );
			}

			WP_CLI::success( sprintf( 'Finished processing all %d posts.', $total_posts ) );
		}
	}


	public function bulkImport( $args, $assoc_args ) {
		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( __( 'Please specify the post type which you want to index.', 'search-with-typesense' ) );
		}

		$post_type = $args[0];
		$offset    = isset( $assoc_args['offset'] ) ? $assoc_args['offset'] : 0;

		$posts = get_posts( [
			'post_type'      => $post_type,
			'posts_per_page' => self::$posts_per_page,
			'offset'         => $offset,
		] );

		foreach ( $posts as $post ) {
			$document = $this->typeSenseAPIInterface->formatDocumentForEntry( $post, $post->ID, $post_type );
			$response = $this->typeSenseAPIInterface->upsertDocument( $post_type, $document );

			if ( is_wp_error( $response ) ) {
				WP_CLI::warning( $response->get_error_message() );
			} else {
				WP_CLI::success( $post_type . ' ID: ' . $post->ID . ' indexed successfully!!!' );
			}
		}

	}

	public static function modify_posts_per_page() {
		return self::$posts_per_page;
	}

	public function delete( $args, $assoc_args ) {
		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( __( 'Please specify the post type which you want to delete.', 'search-with-typesense' ) );
		}

		$post_type = $args[0];

		if( isset( $assoc_args['ids'] ) ) {
			$post_ids_arr = explode( ',', $assoc_args['ids'] );
			foreach ( $post_ids_arr as $post_id ) {
				$post = get_post( $post_id );

				$response = $this->typeSenseAPIInterface->deleteDocumentByID( $post_type, $post_id );

				if ( is_wp_error( $response ) ) {
					WP_CLI::warning( $response->get_error_message() );
				} else {
					WP_CLI::success( $post_type . ' ID: ' . $post_id . ' deleted!!!' );
				}
			}
		} else {
			$response = $this->typeSenseAPIInterface->dropCollection( $post_type );
			if ( is_wp_error( $response ) ) {
				WP_CLI::warning( $response->get_error_message() );
			} else {
				WP_CLI::success( $post_type . ' deleted!!!' );
			}
		}
	}

	public function health() {
		$health = $this->typeSenseAPIInterface->getServerHealth();
		if ( is_wp_error( $health ) ) {
			WP_CLI::error( $health );
		} else {
			WP_CLI::success( 'Health: okay!!!' );
		}
	}

	public function deleteAndReIndex( $args, $assoc_args ) {
		$this->delete( $args, $assoc_args );
		$this->index( $args, $assoc_args );
	}

	/**
	 * Registers our command when cli get's initialized.
	 *
	 * @since  1.0.0
	 * @author Scott Anderson
	 */
	function register_commands() {
		WP_CLI::add_command( 'typesense index', [ $this, 'index' ] );
		WP_CLI::add_command( 'typesense bulkimport', [ $this, 'bulkImport' ] ); // Register command.
		WP_CLI::add_command( 'typesense health', [ $this, 'health' ] );
		WP_CLI::add_command( 'typesense delete', [ $this, 'delete' ] );
		WP_CLI::add_command( 'typesense delete-reindex', [ $this, 'deleteAndReIndex' ] );
	}
}
