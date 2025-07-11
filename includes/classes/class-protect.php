<?php
/**
 * The Protect plugin class.
 *
 * @since      1.0.0
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Protect class File.
 */
class Protect {


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

		$table_name = $wpdb->prefix . 'better_by_default_failed_logins';

		// Maybe create table if it does not exist yet, e.g. upgraded from previous version of plugin, so, no activation methods are fired.
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );  //phpcs:ignore
		if ( $wpdb->get_var( $query ) === $table_name ) {	//phpcs:ignore
			// Table already exists, do nothing.
		} else {
			$activation = new \BetterByDefault\Inc\Activator();
			$activation->create_failed_logins_log_table();
		}

		$options = get_option( BETTER_BY_DEFAULT_PROTECT_OPTIONS, array() );

		// Limit login attempts.
		if ( ! empty( $options ) && array_key_exists( 'limit_login_attempts', $options ) && ( $options['limit_login_attempts'] || 'true' === $options['limit_login_attempts'] ) ) {
			Protect\Limit_Login_Attemps::get_instance();
		}
		// Disable xml rpc.
		if ( ! empty( $options ) && array_key_exists( 'disable_xml_rpc', $options ) && ( $options['disable_xml_rpc'] || 'true' === $options['disable_xml_rpc'] ) ) {
			Protect\Xmlrpc::get_instance();
		}

		// Security Headers.
		if ( ! empty( $options ) && array_key_exists( 'security_headers', $options ) && ( $options['security_headers'] || 'true' === $options['security_headers'] ) ) {
			Protect\Security_Headers::get_instance();
		}

		// Enable REST API Access Control.
		if ( ! empty( $options ) && array_key_exists( 'rest_api_access_control', $options ) && ( $options['rest_api_access_control'] || 'true' === $options['rest_api_access_control'] ) ) {
			Protect\Rest_API_Access_Control::get_instance();
		}

		// Change Login URL.
		if ( ! empty( $options ) && array_key_exists( 'change_login_url', $options ) && ( $options['change_login_url'] || 'true' === $options['change_login_url'] ) ) {
			if ( array_key_exists( 'custom_login_slug', $options ) && ! empty( $options['custom_login_slug'] ) ) {
				Protect\Change_Login_Url::get_instance();
			}
		}

		// Enable Strong Password.
		if ( ! empty( $options ) && array_key_exists( 'strong_password', $options ) && ( $options['strong_password'] || 'true' === $options['strong_password'] ) ) {
			Protect\Strong_Password::get_instance();
		}

		// Enable Reserved Username.
		if ( ! empty( $options ) && array_key_exists( 'reserved_usernames', $options ) && ( $options['reserved_usernames'] || 'true' === $options['reserved_usernames'] ) ) {
			Protect\Reserved_Username::get_instance();
		}
	}
}
