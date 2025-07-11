<?php
/**
 * The Simplify plugin class.
 *
 * @since      1.0.0
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Simplify class File.
 */
class Simplify {


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

		$options = get_option( BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS, array() );
		if ( ! empty( $options ) && array_key_exists( 'bbd_disable_dashboard_widgets', $options ) && true === (bool) $options['bbd_disable_dashboard_widgets'] ) {
			Simplify\Disable_Dashboard_Widgets::get_instance();
		}

		// @codingStandardsIgnoreStart
		// Disable auto update.
		// if ( ! empty( $options ) && array_key_exists( 'disable_auto_update', $options ) && ( $options['disable_auto_update'] || 'true' === $options['disable_auto_update'] ) ) {
		// 	Simplify\Auto_Update::get_instance();
		// }
		// @codingStandardsIgnoreEnd
		// Disable Comments.
		if ( ! empty( $options ) && array_key_exists( 'disable_comments', $options ) && ( $options['disable_comments'] || 'true' === $options['disable_comments'] ) ) {
			Simplify\Comments::get_instance();
		}

		// Disable Post Tags.
		if ( ! empty( $options ) && array_key_exists( 'disable_post_tags', $options ) && ( $options['disable_post_tags'] || 'true' === $options['disable_post_tags'] ) ) {
			Simplify\Post_Tags::get_instance();
		}

		// Admin Footer Text.
		if ( ! empty( $options ) && array_key_exists( 'custom_admin_footer_text', $options ) && ( $options['custom_admin_footer_text'] || 'true' === $options['custom_admin_footer_text'] ) ) {
			Simplify\Admin_Footer_Text::get_instance();
		}

		// Hide Admin Bar.
		if ( ! empty( $options ) && array_key_exists( 'hide_admin_bar', $options ) && $options['hide_admin_bar'] && array_key_exists( 'hide_admin_bar_for', $options ) && isset( $options['hide_admin_bar_for'] ) ) {
			Simplify\Hide_Admin_Bar::get_instance();
		}

		// Enhance List Tables.
		if ( ! empty( $options ) && array_key_exists( 'customize_list_tables', $options ) && ( $options['customize_list_tables'] || 'true' === $options['customize_list_tables'] ) ) {
			Simplify\Customize_List_Tables::get_instance();
		}

		// Search By Title.
		if ( ! empty( $options ) && array_key_exists( 'enable_search_by_title', $options ) && ( $options['enable_search_by_title'] || 'true' === $options['enable_search_by_title'] ) ) {
			Simplify\Search_By_Title::get_instance();
		}

		// Enable Last Login Column.
		if ( ! empty( $options ) && array_key_exists( 'enable_last_login_column', $options ) && ( $options['enable_last_login_column'] || 'true' === $options['enable_last_login_column'] ) ) {
			Simplify\Last_Login_Column::get_instance();
		}
	}
}
