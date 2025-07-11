<?php
/**
 * The Performance plugin class.
 *
 * @since      1.0.0
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Login_Logout class File.
 */
class Performance {


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

		$options = get_option( BETTER_BY_DEFAULT_PERFORMANCE_OPTIONS, array() );

		// Disable Wp Generator.
		if ( ! empty( $options ) && array_key_exists( 'disable_obscure_wp_head_items', $options ) && ( $options['disable_obscure_wp_head_items'] || 'true' === $options['disable_obscure_wp_head_items'] ) ) {
			Performance\Obscure_Wp_Head::get_instance();
		}

		// Enable Lazy Load for Embeds.
		if ( ! empty( $options ) && array_key_exists( 'enable_lazy_load_embeds', $options ) && ( $options['enable_lazy_load_embeds'] || 'true' === $options['enable_lazy_load_embeds'] ) ) {
			Performance\Lazy_Load_Embeds::get_instance();
		}

		// Enable Critical CSS Embeds.
		if ( ! empty( $options ) && array_key_exists( 'enable_critical_css', $options ) && ( $options['enable_critical_css'] || 'true' === $options['enable_critical_css'] ) ) {
			Performance\Critical_CSS::get_instance();
		}
	}
}
