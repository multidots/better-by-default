<?php
/**
 * The disable-block-editor-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Personalize;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Disable_Block_Editor class file.
 */
class Disable_Block_Editor {

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
	 * Options retrieved from settings.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->setup_disable_block_editor_hooks();
	}
	/**
	 * Function is used to define disable-block-editor hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_disable_block_editor_hooks() {
		$this->options = get_option( BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS, array() );

		add_action( 'admin_init', array( $this, 'disable_block_editor_admin_callback' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'disable_block_editor_frontend_callback' ), 100 );
	}

	/**
	 * Disable Gutenberg in wp-admin for some or all post types
	 *
	 * @since 1.0.0
	 */
	public function disable_block_editor_admin_callback() {
		// Get current page's post type from WP core globals and query parameters.
		global $pagenow, $typenow;
		$post_type = null;
		if ( 'edit.php' === $pagenow ) {
			// on the list table screen, $typenow returns correct post type.
			$post_type = $typenow;
		} elseif ( 'post.php' === $pagenow ) {
			// on the edit screen, $typenow is empty, so we detect it.
			$post_id   = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
			$post_type = ( isset( $post_id ) ? get_post_type( $post_id ) : 'post' );
		} elseif ( 'post-new.php' === $pagenow ) {
			$post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			// on the add new screen, best to get post type from GET parameter.
			$post_type = ( isset( $post_type ) ? $post_type : 'post' );
		}

		// Check if Gutenberg feature is enabled for the site.
		// Before/after WP v5.0.0 via feature plugin.
		$gutenberg = function_exists( 'gutenberg_register_scripts_and_styles' );
		// Since WP v5.0.0, gutenberg is in core.
		$block_editor = has_action( 'enqueue_block_assets' );
		// Gutenberg feature is not enabled for the site.
		if ( ! $gutenberg && false === $block_editor ) {
			return;
		}
		// Assemble single-dimensional array of post types for which Gutenberg should be disabled.
		$options                          = $this->options;
		$disable_gutenberg_for            = $options['disable_block_editor_for'];
		$post_types_for_disable_gutenberg = array();
		if ( ! empty( $disable_gutenberg_for ) ) {
			foreach ( $disable_gutenberg_for as $post_type_slug => $is_gutenberg_disabled ) {
				if ( $is_gutenberg_disabled ) {
					$post_types_for_disable_gutenberg[] = $post_type_slug;
				}
			}
		}

		// Selectively disable Gutenberg.
		if ( ( in_array( $post_type, $post_types_for_disable_gutenberg, true ) ) ) {
			// For WP v5.0.0 upwards.
			add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
			// If Gutenberg feature plugin is activated.
			if ( $gutenberg ) {
				add_filter( 'gutenberg_can_edit_post_type', '__return_false', 100 );
				$this->remove_all_gutenberg_hooks();
			}
		}
	}

	/**
	 * Remove Gutenberg hooks added via feature plugin.
	 *
	 * @link https://plugins.trac.wordpress.org/browser/classic-editor/tags/1.6.2/classic-editor.php#L138
	 * @since 1.0.0
	 */
	public function remove_all_gutenberg_hooks() {
		remove_action( 'admin_menu', 'gutenberg_menu' );
		remove_action( 'admin_init', 'gutenberg_redirect_demo' );
		// Gutenberg 5.3+.
		remove_action( 'wp_enqueue_scripts', 'gutenberg_register_scripts_and_styles' );
		remove_action( 'admin_enqueue_scripts', 'gutenberg_register_scripts_and_styles' );
		remove_action( 'admin_notices', 'gutenberg_wordpress_version_notice' );
		remove_action( 'rest_api_init', 'gutenberg_register_rest_widget_updater_routes' );
		remove_action( 'admin_print_styles', 'gutenberg_block_editor_admin_print_styles' );
		remove_action( 'admin_print_scripts', 'gutenberg_block_editor_admin_print_scripts' );
		remove_action( 'admin_print_footer_scripts', 'gutenberg_block_editor_admin_print_footer_scripts' );
		remove_action( 'admin_footer', 'gutenberg_block_editor_admin_footer' );
		remove_action( 'admin_enqueue_scripts', 'gutenberg_widgets_init' );
		remove_action( 'admin_notices', 'gutenberg_build_files_notice' );
		remove_filter( 'load_script_translation_file', 'gutenberg_override_translation_file' );
		remove_filter( 'block_editor_settings', 'gutenberg_extend_block_editor_styles' );
		remove_filter( 'default_content', 'gutenberg_default_demo_content' );
		remove_filter( 'default_title', 'gutenberg_default_demo_title' );
		remove_filter( 'block_editor_settings', 'gutenberg_legacy_widget_settings' );
		remove_filter( 'rest_request_after_callbacks', 'gutenberg_filter_oembed_result' );
		// Previously used, compat for older Gutenberg versions.
		remove_filter( 'wp_refresh_nonces', 'gutenberg_add_rest_nonce_to_heartbeat_response_headers' );
		remove_filter( 'get_edit_post_link', 'gutenberg_revisions_link_to_editor' );
		remove_filter( 'wp_prepare_revision_for_js', 'gutenberg_revisions_restore' );
		remove_action( 'rest_api_init', 'gutenberg_register_rest_routes' );
		remove_action( 'rest_api_init', 'gutenberg_add_taxonomy_visibility_field' );
		remove_filter( 'registered_post_type', 'gutenberg_register_post_prepare_functions' );
		remove_action( 'do_meta_boxes', 'gutenberg_meta_box_save' );
		remove_action( 'submitpost_box', 'gutenberg_intercept_meta_box_render' );
		remove_action( 'submitpage_box', 'gutenberg_intercept_meta_box_render' );
		remove_action( 'edit_page_form', 'gutenberg_intercept_meta_box_render' );
		remove_action( 'edit_form_advanced', 'gutenberg_intercept_meta_box_render' );
		remove_filter( 'redirect_post_location', 'gutenberg_meta_box_save_redirect' );
		remove_filter( 'filter_gutenberg_meta_boxes', 'gutenberg_filter_meta_boxes' );
		remove_filter( 'body_class', 'gutenberg_add_responsive_body_class' );
		remove_filter( 'admin_url', 'gutenberg_modify_add_new_button_url' );
		// old.
		remove_action( 'admin_enqueue_scripts', 'gutenberg_check_if_classic_needs_warning_about_blocks' );
		remove_filter( 'register_post_type_args', 'gutenberg_filter_post_type_labels' );
	}

	/**
	 * Disable Gutenberg styles and scripts on the front end for all or some post types
	 *
	 * @since 1.0.0
	 */
	public function disable_block_editor_frontend_callback() {
		$post = get_queried_object();
		if ( ! is_null( $post ) ) {
			if ( property_exists( $post, 'post_type' ) ) {
				$post_type = $post->post_type;
				// Assemble single-dimensional array of post types for which Gutenberg should be disabled.
				$options                          = $this->options;
				$disable_gutenberg_for            = isset( $options['disable_gutenberg_for'] ) ? $options['disable_gutenberg_for'] : array();
				$post_types_for_disable_gutenberg = array();
				if ( ! empty( $disable_gutenberg_for ) && is_array( $disable_gutenberg_for ) ) {
					foreach ( $disable_gutenberg_for as $post_type_slug => $is_gutenberg_disabled ) {
						if ( $is_gutenberg_disabled ) {
							$post_types_for_disable_gutenberg[] = $post_type_slug;
						}
					}
				}
				// Selectively disable for the selected post types.
				if ( ( in_array( $post_type, $post_types_for_disable_gutenberg, true ) ) ) {
					global $wp_styles;
					$keep_enqueued = array();
					foreach ( $wp_styles->queue as $handle ) {
						if ( false !== strpos( $handle, 'wp-block' ) ) {
							if ( ! in_array( $handle, $keep_enqueued, true ) ) {
								wp_dequeue_style( $handle );
							}
						}
					}
					// Additional dequeuing.
					wp_dequeue_style( 'core-block-supports' );
					wp_dequeue_style( 'global-styles' );
					// theme.json.
					wp_dequeue_style( 'classic-theme-styles' );
				}
			}
		}
	}
}
