<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @subpackage better-by-default/admin
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Main class file.
 */
class Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BETTER_BY_DEFAULT_VERSION' ) ) {
			$this->version = BETTER_BY_DEFAULT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->setup_admin_hooks();
	}

	/**
	 * Function is used to define admin hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_admin_hooks() {

		add_action( 'admin_enqueue_scripts', array( $this, 'better_by_default_enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'better_by_default_enqueue_scripts' ) );
		add_action( 'admin_notices', array( $this, 'better_by_default_add_notices' ), 5 );
		add_action( 'all_admin_notices', array( $this, 'better_by_default_suppress_generic_notices' ), 5 );

		// Sync the "Discourage search engines" setting with the plugin's disable_crawling option.
		add_action( 'update_option_blog_public', array( $this, 'better_by_default_sync_disable_crawling_with_blog_public' ), 10, 2 );

		// Flush cache object cache.
		add_action( 'wp_ajax_flush_cache_object_cache', array( $this, 'better_by_default_flush_cache_object_cache_callback' ) );

		// Flush cache page cache.
		add_action( 'wp_ajax_flush_cache_page_cache', array( $this, 'better_by_default_flush_cache_page_cache_callback' ) );

		// Callback function to reset custom options for the Better By Default Plugin.
		add_action( 'wp_ajax_better_by_default_options', array( $this, 'better_by_default_options_callback' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function better_by_default_enqueue_styles() {

		wp_enqueue_style( 'better-by-default', BETTER_BY_DEFAULT_URL . 'assets/build/admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'datatable-style', BETTER_BY_DEFAULT_URL . 'assets/library/datatables/datatables.min.css', array(), $this->version );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function better_by_default_enqueue_scripts() {

		wp_enqueue_media();
		wp_enqueue_script( 'datatable-script', BETTER_BY_DEFAULT_URL . 'assets/library/datatables/datatables.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'better-by-default', BETTER_BY_DEFAULT_URL . 'assets/build/admin.js', array( 'jquery', 'wp-color-picker' ), 1.1, true );

		wp_localize_script(
			'better-by-default',
			'betterByDefaultConfig',
			array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ),
				'dataTable'  => array(
					'emptyTable'   => __( 'No data available in table', 'better-by-default' ),
					'info'         => __( 'Showing _START_ to _END_ of _TOTAL_ entries', 'better-by-default' ),
					'infoEmpty'    => __( 'Showing 0 to 0 of 0 entries', 'better-by-default' ),
					'infoFiltered' => __( '(filtered from _MAX_ total entries)', 'better-by-default' ),
					'lengthMenu'   => __( 'Show _MENU_ entries', 'better-by-default' ),
					'search'       => __( 'Search:', 'better-by-default' ),
					'zeroRecords'  => __( 'No matching records found', 'better-by-default' ),
					'paginate'     => array(
						'first'    => __( 'First', 'better-by-default' ),
						'last'     => __( 'Last', 'better-by-default' ),
						'next'     => __( 'Next', 'better-by-default' ),
						'previous' => __( 'Previous', 'better-by-default' ),
					),
				),
			)
		);
	}

	/**
	 * Suppress all notices, then add notice for successful settings update
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_add_notices() {
		global $plugin_page;
		// Suppress all notices.
		if ( 'better-by-default-settings' === $plugin_page ) {
			remove_all_actions( 'admin_notices' );
		}
		// Add notice for successful settings update.
		$page_slug        = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$settings_updated = filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN );
		if ( isset( $page_slug ) && 'better-by-default-settings' === $page_slug && isset( $settings_updated ) && true === $settings_updated ) {
			?>
				<script>
					jQuery(document).ready( function() {
						jQuery('.better-by-default-changes-saved').fadeIn(400).delay(2500).fadeOut(400);
					});
				</script>

			<?php
		}
	}

	/**
	 * Suppress all generic notices on the plugin settings page
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_suppress_generic_notices() {
		global $plugin_page;
		// Suppress all notices.
		if ( 'better-by-default-settings' === $plugin_page ) {
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Synchronize the plugin's disable_crawling option when "Discourage search engines" is toggled.
	 *
	 * @param mixed $old_value The old value.
	 * @param mixed $new_value The new value.
	 *
	 *  @since 1.0.0
	 */
	public function better_by_default_sync_disable_crawling_with_blog_public( $old_value, $new_value ) {

		$options = get_option( BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS, array() );

		// If blog_public is 0 (Discourage search engines checked), enable disable_crawling.
		if ( 0 === intval( $new_value ) ) {
			$options['disable_crawling'] = 'true';
		} else {

			$options['disable_crawling'] = 'false';
		}

		// Update the plugin options with the new value.
		update_option( BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS, $options );
	}

	/**
	 * Flush cache object cache.
	 *
	 * @return void
	 */
	public function better_by_default_flush_cache_object_cache_callback() {
		check_ajax_referer( 'ajax_nonce', 'nonce' );
		wp_cache_flush();
		wp_send_json_success();
	}

	/**
	 * Flush cache page cache.
	 *
	 * @return void
	 */
	public function better_by_default_flush_cache_page_cache_callback() {
		check_ajax_referer( 'ajax_nonce', 'nonce' );

		$page_url = filter_input( INPUT_POST, 'urls', FILTER_SANITIZE_SPECIAL_CHARS );
		$page_url = isset( $page_url ) ? $page_url : '';

		if ( empty( trim( $page_url ) ) ) {
			$is_cleared = 'no';
		} else {
			$urls = preg_split( '/\r\n|\r|\n/', $page_url );

			if ( ! empty( $urls ) ) {
				$filtered_urls = array_filter(
					$urls,
					function ( $u ) {
						return filter_var( trim( $u ), FILTER_VALIDATE_URL );
					}
				);

				if ( ! empty( $filtered_urls ) && function_exists( 'wpcom_vip_purge_edge_cache_for_url' ) ) {
					foreach ( $filtered_urls as $url ) {
						wpcom_vip_purge_edge_cache_for_url( $url );
					}

					$is_cleared = 'yes';
				} else {
					$is_cleared = 'no';
				}
			} else {
				$is_cleared = 'no';
			}
		}
		if ( 'yes' === $is_cleared ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Callback function to reset custom options for the Better By Default Plugin.
	 *
	 * This function handles an AJAX request to delete specified options and reset them
	 * to default values if they do not already exist. It ensures secure processing
	 * through nonce verification and returns a success response upon completion.
	 */
	public function better_by_default_options_callback() {
		// Verify the AJAX request with nonce security check.
		check_ajax_referer( 'ajax_nonce', 'nonce' );

		// List of options to delete and reset.
		$options = array(
			'better_by_default_simplify_option',
			'better_by_default_performance_option',
			'better_by_default_extra_option',
			'better_by_default_miscellaneous_option',
			'better_by_default_personalize_option',
			'better_by_default_protect_option',
		);

		// Loop through each option and delete it.
		foreach ( $options as $option ) {
			delete_option( $option );
		}

		// Define default settings for the 'simplify' option.
		$dashboard_widgets_array   = array(
			'disable_welcome_panel_in_dashboard' => true,
		);
		$simplify_default_settings = array(
			'bbd_disable_dashboard_widgets'  => true,
			'bbd_disabled_dashboard_widgets' => $dashboard_widgets_array,
			//phpcs:ignore // removed for the autoupdate disable option // 'disable_auto_update'            => true,
			'disable_comments'               => true,
			'disable_post_tags'              => true,
			'enable_search_by_title'         => true,
			'enable_last_login_column'       => true,
		);

		// Define default settings for the 'performance' option.
		$performance_default_settings = array(
			'disable_emoji'                 => true,
			'disable_rss_links'             => true,
			'disable_obscure_wp_head_items' => true,
			'remove_shortlinks'             => true,
			'remove_rss_links'              => true,
			'remove_rest_api_links'         => true,
			'remove_rsd_wlw_links'          => true,
			'remove_oembed_links'           => true,
			'remove_generator_tag'          => true,
			'remove_emoji_scripts'          => true,
			'remove_pingback'               => true,
		);

		// Reset the 'simplify' option to default if it doesn't already exist.
		if ( false === get_option( 'better_by_default_simplify_option' ) ) {
			update_option( 'better_by_default_simplify_option', $simplify_default_settings );
		}

		// Reset the 'performance' option to default if it doesn't already exist.
		if ( false === get_option( 'better_by_default_performance_option' ) ) {
			update_option( 'better_by_default_performance_option', $performance_default_settings );
		}

		// Send a success response back to the AJAX request.
		wp_send_json_success();
	}
}
