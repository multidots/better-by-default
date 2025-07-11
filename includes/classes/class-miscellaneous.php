<?php
/**
 * The Miscellaneous plugin class.
 *
 * @since      1.0.0
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Miscellaneous class File.
 */
class Miscellaneous {


	use Singleton;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      BetterByDefault_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// load class.
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function setup_hooks() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'better_by_default_activity_log';

		// Maybe create table if it does not exist yet, e.g. upgraded from previous version of plugin, so, no activation methods are fired.
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

		if ( $wpdb->get_var( $query ) === $table_name ) {	//phpcs:ignore
			// Table already exists, do nothing.
		} else {
			$activation = new \BetterByDefault\Inc\Activator();
			$activation->create_user_activity_log_table();
		}

		$options = get_option( BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS, array() );

		// Default template for network site.
		if ( ! empty( $options ) && array_key_exists( 'default_template_network_site', $options ) && ( $options['default_template_network_site'] || 'true' === $options['default_template_network_site'] ) ) {
			Miscellaneous\Default_Template_Network_Site::get_instance();
		}

		// Maintenance mode.
		if ( ! empty( $options ) && array_key_exists( 'maintenance_mode', $options ) && ( $options['maintenance_mode'] || 'true' === $options['maintenance_mode'] ) ) {
			Miscellaneous\Maintenance_Mode::get_instance();
		}

		// Activity log.
		if ( ! empty( $options ) && array_key_exists( 'activity_log', $options ) && ( $options['activity_log'] || 'true' === $options['activity_log'] ) ) {
			Miscellaneous\Activity_Log::get_instance();
		}

		// Public Page Preview.
		if ( ! empty( $options ) && array_key_exists( 'enable_public_page_preview', $options ) && ( true === $options['enable_public_page_preview'] || 'true' === $options['enable_public_page_preview'] ) ) {
			Miscellaneous\Public_Page_Preview::get_instance();
		}

		// Disable Crawling.
		if ( ! empty( $options ) && array_key_exists( 'disable_crawling', $options ) && (  true === $options['disable_crawling'] || 'true' === $options['disable_crawling'] ) ) {
			Miscellaneous\Disable_Crawling::get_instance();
		} else {
			Miscellaneous\Enable_Crawling::get_instance();
		}
	}
}
