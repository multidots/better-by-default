<?php
/**
 * The account-menu-style-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Personalize;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Account_Menu_Style class file.
 */
class Account_Menu_Style {

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

		$this->setup_account_menu_style_hooks();
	}
	/**
	 * Function is used to define setup_account_menu_style hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_account_menu_style_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'account_menu_style_enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'account_menu_style_enqueue_styles' ) );
	}

	/**
	 * Register the stylesheets for the admin and frontend.
	 *
	 * @since    1.0.0
	 */
	public function account_menu_style_enqueue_styles() {

		// Only enqueue styles if the admin bar is visible.
		if ( ! is_admin_bar_showing() ) {
			return;
		}
		wp_enqueue_style( 'better-by-default-account-menu-style', BETTER_BY_DEFAULT_URL . 'assets/build/css/frontend/account-menu.css', array(), $this->version, 'all' );
	}
}
