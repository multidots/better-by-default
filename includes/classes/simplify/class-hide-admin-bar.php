<?php
/**
 * The hide-admin-bar-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Simplify;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Hide_Admin_Bar class file.
 */
class Hide_Admin_Bar {

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

		$this->setup_hide_admin_bar_hooks();
	}
	/**
	 * Function is used to define setup_hide_admin_bar hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_hide_admin_bar_hooks() {
		add_filter( 'show_admin_bar', [$this, 'hide_admin_bar_for_roles_on_frontend'] );	//phpcs:ignore
	}

	/**
	 * Hide admin bar on the frontend for the user roles selected
	 *
	 * @since 1.0.0
	 */
	public function hide_admin_bar_for_roles_on_frontend() {
		$options            = get_option( BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS );
		$for_roles_frontend = $options['hide_admin_bar_for'];
		$current_user       = wp_get_current_user();
		$current_user_roles = (array) $current_user->roles;
		// single dimensional array of role slugs.
		// User has no role, i.e. logged-out.
		if ( count( $current_user_roles ) === 0 ) {
			return false;
		}
		// User has role(s). Do further checks.
		if ( isset( $for_roles_frontend ) && count( $for_roles_frontend ) > 0 ) {
			// Assemble single-dimensional array of roles for which admin bar would be hidden.
			$roles_admin_bar_hidden_frontend = array();
			foreach ( $for_roles_frontend as $role_slug => $admin_bar_hidden ) {
				if ( $admin_bar_hidden ) {
					$roles_admin_bar_hidden_frontend[] = $role_slug;
				}
			}
			// Check if any of the current user roles is one for which admin bar should be hidden.
			foreach ( $current_user_roles as $role ) {
				if ( in_array( $role, $roles_admin_bar_hidden_frontend, true ) ) {
					return false;
				}
			}
		}
		return true;
	}
}
