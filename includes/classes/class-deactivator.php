<?php
/**
 * The deactivation functionality of the plugin.
 *
 * @package    better-by-default
 * @subpackage better-by-default/admin
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Deactivator class file.
 */
class Deactivator {

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
	public static function deactivate() {
		self::delete_failed_logins_log_table();
	}
	/**
	 * Delete failed login log table for Limit Login Attempts feature
	 *
	 * @since 1.0.0
	 */
	public static function delete_failed_logins_log_table() {

		global $wpdb;

		// Limit Login Attempts Log Table.

		$table_name = $wpdb->prefix . 'better_by_default_failed_logins';

		// Drop table if already exists.
		$wpdb->query("DROP TABLE IF EXISTS `". $table_name ."`");	//phpcs:ignore
	}
}
