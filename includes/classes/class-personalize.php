<?php
/**
 * The Personalize plugin class.
 *
 * @since      1.0.0
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Personalize class File.
 */
class Personalize {


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

		/**
		 * Load header classes.
		 */

		$options = get_option( BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS, array() );

		// Admin color branding.
		if ( ! empty( $options ) && array_key_exists( 'admin_color_branding', $options ) && ( $options['admin_color_branding'] || 'true' === $options['admin_color_branding'] ) ) {
			Personalize\Admin_Color_Branding::get_instance();
		}

		// Enable Site Identity.
		if ( ! empty( $options ) && array_key_exists( 'site_identity_on_login_page', $options ) && ( $options['site_identity_on_login_page'] || 'true' === $options['site_identity_on_login_page'] ) ) {
			Personalize\Admin_Login_Branding::get_instance();
		}

		// User Account Style.
		if ( ! empty( $options ) && array_key_exists( 'user_account_style', $options ) && ( $options['user_account_style'] || 'true' === $options['user_account_style']  ) ) {
			Personalize\Account_Menu_Style::get_instance();
		}

		// Enable customization of admin menu.
		if ( ! empty( $options ) && array_key_exists( 'customize_admin_menu', $options ) && ( $options['customize_admin_menu'] || 'true' === $options['customize_admin_menu'] ) ) {
			Personalize\Admin_Menu_Organization::get_instance();
		}

		// Enable duplication of pages, posts and custom posts.
		if ( ! empty( $options ) && array_key_exists( 'enable_duplication', $options ) && ( $options['enable_duplication'] || 'true' === $options['enable_duplication'] ) ) {
			Personalize\Content_Duplication::get_instance();
		}

		// Disable Block Editor.
		if ( ! empty( $options ) && array_key_exists( 'disable_block_editor', $options ) && ( $options['disable_block_editor'] || 'true' === $options['disable_block_editor'] ) ) {
			if ( array_key_exists( 'disable_block_editor_for', $options ) && ! empty( $options['disable_block_editor_for'] ) ) {
				Personalize\Disable_Block_Editor::get_instance();
			}
		}
	}
}
