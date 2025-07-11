<?php
/**
 * The activation functionality of the plugin.
 *
 * @package better-by-default
 * @subpackage better-by-default/admin
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Activator class file.
 */
class Activator {

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
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		self::set_option_default_values();

		$version = 7;
		$theme   = wp_get_theme();
		if ( version_compare( $version, get_bloginfo( 'version' ), '>=' ) ) {
			return true;
		} else {
			wp_die( esc_html_e( 'Please activate Twenty two theme', 'better-by-default' ), 'Theme dependency check', array( 'back_link' => true ) );
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins( BETTER_BY_DEFAULT_BASEPATH );
			wp_die( esc_html_e( 'Please install and Activate WooCommerce.', 'better-by-default' ), 'Plugin dependency check', array( 'back_link' => true ) );
		}
		if ( 'Twenty Twenty-Two' === ! $theme->name || 'Twenty Twenty-Two' === ! $theme->parent_theme ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins( BETTER_BY_DEFAULT_BASEPATH );
			wp_die( esc_html_e( 'Please activate Twenty two theme', 'better-by-default' ), 'Theme dependency check', array( 'back_link' => true ) );

		}
	}

	/**
	 * Set default values for the plugin options.
	 *
	 * @return void
	 */
	public static function set_option_default_values() {

		// Option names.
		$simplify_option_name    = 'better_by_default_simplify_option';
		$performance_option_name = 'better_by_default_performance_option';

		$dashboard_widgets_array = array(
			'disable_welcome_panel_in_dashboard' => true,
		);

		// Simplify default settings.
		$simplify_default_settings = array(
			'bbd_disable_dashboard_widgets'  => true,
			'bbd_disabled_dashboard_widgets' => $dashboard_widgets_array,
			//phpcs:ignore // removed for the autoupdate disable option // 'disable_auto_update'            => true,
			'disable_comments'               => true,
			'disable_post_tags'              => true,
			'enable_search_by_title'         => true,
			'enable_last_login_column'       => true,
		);

		// Performance default settings.
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

		// Check if this is a multisite setup and network admin.
		if ( is_multisite() && is_network_admin() ) {
			self::set_network_default_options( $simplify_option_name, $performance_option_name, $simplify_default_settings, $performance_default_settings );
		} else {
			// Single site setup.
			self::set_single_site_default_options( $simplify_option_name, $performance_option_name, $simplify_default_settings, $performance_default_settings );
		}
	}

	/**
	 * Set default options for a single site.
	 *
	 * @param string $simplify_option_name    The simplify option name.
	 * @param string $performance_option_name The performance option name.
	 * @param array  $simplify_default_settings The simplify default settings.
	 * @param array  $performance_default_settings The performance default settings.
	 *
	 * @return void
	 */
	private static function set_single_site_default_options( $simplify_option_name, $performance_option_name, $simplify_default_settings, $performance_default_settings ) {

		// Only set defaults if options don't already exist.
		if ( false === get_option( $simplify_option_name ) ) {
			update_option( $simplify_option_name, $simplify_default_settings );
		}

		if ( false === get_option( $performance_option_name ) ) {
			update_option( $performance_option_name, $performance_default_settings );
		}
	}

	/**
	 * Set default options for all subsites in a multisite network.
	 *
	 * @param string $simplify_option_name    The simplify option name.
	 * @param string $performance_option_name The performance option name.
	 * @param array  $simplify_default_settings The simplify default settings.
	 * @param array  $performance_default_settings The performance default settings.
	 *
	 * @return void
	 */
	private static function set_network_default_options( $simplify_option_name, $performance_option_name, $simplify_default_settings, $performance_default_settings ) {

		// Get all site IDs in the network.
		$sites = get_sites( array( 'fields' => 'ids' ) );

		foreach ( $sites as $site_id ) {
			switch_to_blog( $site_id ); // Switch to each site in the multisite network.

			// Only set defaults if options don't already exist for the current site.
			if ( false === get_option( $simplify_option_name ) ) {
				update_option( $simplify_option_name, $simplify_default_settings );
			}

			if ( false === get_option( $performance_option_name ) ) {
				update_option( $performance_option_name, $performance_default_settings );
			}

			restore_current_blog(); // Restore the original site.
		}
	}


	/**
	 * Create failed login log table for Limit Login Attempts feature.
	 *
	 * @since 1.0.0
	 */
	public function create_failed_logins_log_table() {

		global $wpdb;

		// Limit Login Attempts Log Table.

		$table_name = $wpdb->prefix . 'better_by_default_failed_logins';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collation_sql = "DEFAULT CHARACTER SET $wpdb->charset";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collation_sql .= " COLLATE $wpdb->collate";
		}

		// Drop table if already exists.
		$wpdb->query("DROP TABLE IF EXISTS `". $table_name ."`");	//phpcs:ignore

		// Create database table. This procedure may also be called.
		$sql = "CREATE TABLE {$table_name} (
			id int(6) unsigned NOT NULL auto_increment,
			ip_address varchar(40) NOT NULL DEFAULT '',
			username varchar(60) NOT NULL DEFAULT '',
			fail_count int(10) NOT NULL DEFAULT '0',
			lockout_count int(10) NOT NULL DEFAULT '0',
			request_uri varchar(24) NOT NULL DEFAULT '',
			unixtime int(10) NOT NULL DEFAULT '0',
			datetime_wp varchar(36) NOT NULL DEFAULT '',
			-- datetime_utc datetime NULL DEFAULT CURRENT_TIMESTAMP,
			info varchar(64) NOT NULL DEFAULT '',
			UNIQUE (ip_address),
			PRIMARY KEY (id)
		) {$charset_collation_sql}";

		require_once ABSPATH . '/wp-admin/includes/upgrade.php';

		dbDelta( $sql );

		return true;
	}

	/**
	 * Create user activity log table.
	 *
	 * @since 1.0.0
	 */
	public static function create_user_activity_log_table() {

		global $wpdb, $blog_id;

		// Limit Login Attempts Log Table.

		// Prefix for the specific site.
		$prefix = $wpdb->get_blog_prefix( $blog_id );

		$table_name = $prefix . 'better_by_default_activity_log';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collation_sql = "DEFAULT CHARACTER SET $wpdb->charset";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collation_sql .= " COLLATE $wpdb->collate";
		}

		// Drop table if already exists.
		$wpdb->query("DROP TABLE IF EXISTS `". $table_name ."`");	//phpcs:ignore

		// Create database table. This procedure may also be called.
		$sql = "CREATE TABLE {$table_name} (
			id int(6) unsigned NOT NULL auto_increment,
			user_action varchar(36) NOT NULL DEFAULT '0',
			summary longtext NOT NULL,
			subgroup varchar(36) NOT NULL DEFAULT '0',
			user_id int(10) NOT NULL DEFAULT '0',
			unixtime int(10) NOT NULL DEFAULT '0',
			datetime_wp varchar(36) NOT NULL DEFAULT '',
			-- datetime_utc datetime NULL DEFAULT CURRENT_TIMESTAMP,
			UNIQUE (id),
			PRIMARY KEY (id)
		) {$charset_collation_sql}";

		require_once ABSPATH . '/wp-admin/includes/upgrade.php';

		dbDelta( $sql );

		return true;
	}
}
