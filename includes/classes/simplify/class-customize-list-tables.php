<?php
/**
 * The customize-list-tables-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Simplify;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Customize_List_Tables class file.
 */
class Customize_List_Tables {

	use Singleton;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The settings of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $options   Settings value related to the admin interface section.
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->options = get_option( BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS, array() );
		$this->setup_hooks();
	}

	/**
	 * Set up hooks based on options.
	 */
	private function setup_hooks() {
		$columns = $this->options['extra_list_table_columns'] ?? array();

		if ( $this->is_option_enabled( $columns, 'show_featured_image_column' ) ) {
			add_action( 'admin_init', array( $this, 'show_featured_image_column' ) );
		}

		if ( $this->is_option_enabled( $columns, 'show_excerpt_column' ) ) {
			add_action( 'admin_init', array( $this, 'show_excerpt_column' ) );
		}

		if ( $this->is_option_enabled( $columns, 'show_id_column' ) ) {
			add_action( 'admin_init', array( $this, 'show_id_column' ) );
		}

		if ( $this->is_option_enabled( $columns, 'show_file_size_column' ) ) {
			add_filter( 'manage_upload_columns', array( $this, 'add_column_file_size' ) );
			add_action( 'manage_media_custom_column', array( $this, 'display_file_size' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_media_enqueue_styles' ) );
		}
	}

	/**
	 * Register the stylesheets for the admin.
	 *
	 * @since    1.0.0
	 */
	public function add_media_enqueue_styles() {

		wp_enqueue_style( 'better-by-default-customize-list-tables-style', BETTER_BY_DEFAULT_URL . 'assets/build/css/admin/customize-list-tables.css', array(), $this->version, 'all' );
	}

	/**
	 * Check if an option is enabled.
	 *
	 * @param array  $columns Columns array.
	 * @param string $option  Option key.
	 * @return bool
	 */
	private function is_option_enabled( $columns, $option ) {
		return isset( $columns[ $option ] ) && ( $columns[ $option ] || 'true' === $columns[ $option ] );
	}

	/**
	 * Show featured image column for post types with featured image support.
	 */
	public function show_featured_image_column() {
		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'thumbnail' ) ) {
				add_filter( "manage_{$post_type}_posts_columns", array( $this, 'add_featured_image_column' ), 999 );
				add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'display_featured_image' ), 10, 2 );
			}
		}
	}

	/**
	 * Add a Featured Image column.
	 *
	 * @param array $columns Columns array.
	 * @return array Modified columns.
	 */
	public function add_featured_image_column( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $value ) {
			if ( 'title' === $key || 'thumb' === $key ) {
				$new_columns['better-by-default-featured-image'] = ( 'thumb' === $key ) ? __( 'Product Image', 'better-by-default' ) : __( 'Featured Image', 'better-by-default' );
			}
			$new_columns[ $key ] = $value;
		}

		if ( isset( $new_columns['thumb'] ) ) {
			unset( $new_columns['thumb'] );
		}

		return $new_columns;
	}

	/**
	 * Display featured image in the column.
	 *
	 * @param string $column_name Column name.
	 * @param int    $post_id     Post ID.
	 */
	public function display_featured_image( $column_name, $post_id ) {
		if ( 'better-by-default-featured-image' === $column_name ) {
			if ( has_post_thumbnail( $post_id ) ) {
				// Escaping the post thumbnail output.
				echo wp_kses_post( get_the_post_thumbnail( $post_id, 'thumbnail' ) );
			} else {
				// Escaping the URL and output for the default image.
				$default_image = esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/default_featured_image.png' );
				echo '<img src="' . esc_url( $default_image ) . '" width="75" height="75" alt="' . esc_attr__( 'Default Featured Image', 'better-by-default' ) . '" />'; //phpcs:ignore
			}
		}
	}

	/**
	 * Show excerpt column for post types with excerpt support.
	 */
	public function show_excerpt_column() {
		$post_types = get_post_types( array( 'public' => true ), 'names' );

		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				if ( post_type_supports( $post_type, 'excerpt' ) ) {
					add_filter( "manage_{$post_type}_posts_columns", array( $this, 'add_excerpt_column' ) );
					add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'display_excerpt' ), 10, 2 );
				}
			}
		}
	}

	/**
	 * Add an Excerpt column.
	 *
	 * @param array $columns Columns array.
	 * @return array Modified columns.
	 */
	public function add_excerpt_column( $columns ) {
		$columns['better-by-default-excerpt'] = __( 'Excerpt', 'better-by-default' );
		return $columns;
	}

	/**
	 * Display excerpt in the column.
	 *
	 * @param string $column_name Column name.
	 * @param int    $post_id     Post ID.
	 */
	public function display_excerpt( $column_name, $post_id ) {
		if ( 'better-by-default-excerpt' === $column_name ) {
			$excerpt = get_the_excerpt( $post_id );
			echo esc_html( wp_trim_words( $excerpt, 20, '...' ) );
		}
	}

	/**
	 * Show ID column for various post types and taxonomies.
	 */
	public function show_id_column() {
		$post_types = array_merge( array( 'pages', 'posts' ), get_post_types() );
		foreach ( $post_types as $post_type ) {
			add_filter( "manage_{$post_type}_columns", array( $this, 'add_id_column' ) );
			add_action( "manage_{$post_type}_custom_column", array( $this, 'display_id' ), 10, 2 );
		}
		add_filter( 'manage_media_columns', array( $this, 'add_id_column' ) );
		add_action( 'manage_media_custom_column', array( $this, 'display_id' ), 10, 2 );
	}

	/**
	 * Add ID column.
	 *
	 * @param array $columns Columns array.
	 * @return array Modified columns.
	 */
	public function add_id_column( $columns ) {
		$columns['better-by-default-id'] = __( 'ID', 'better-by-default' );
		return $columns;
	}

	/**
	 * Display ID in the column.
	 *
	 * @param string $column_name Column name.
	 * @param int    $id          Post ID or taxonomy ID.
	 */
	public function display_id( $column_name, $id ) {
		if ( 'better-by-default-id' === $column_name ) {
			echo esc_html( $id );
		}
	}

	/**
	 * Add file size column to the media library.
	 *
	 * @param array $columns Columns array.
	 * @return array Modified columns.
	 */
	public function add_column_file_size( $columns ) {
		$columns['better-by-default-file-size'] = __( 'File Size', 'better-by-default' );
		return $columns;
	}

	/**
	 * Display file size in the media library column.
	 *
	 * @param string $column_name Column name.
	 * @param int    $attachment_id Attachment ID.
	 */
	public function display_file_size( $column_name, $attachment_id ) {
		if ( 'better-by-default-file-size' === $column_name ) {
			$file_size = filesize( get_attached_file( $attachment_id ) );
			echo esc_html( size_format( $file_size ) );
		}
	}
}
