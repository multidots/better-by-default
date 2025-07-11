<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    better-by-default
 * @subpackage better-by-default/includes
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Blocks;

use BetterByDefault\Inc\Traits\Singleton;


/**
 * Main class File.
 */
class Better_By_Default {


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
		if ( defined( 'BETTER_BY_DEFAULT_VERSION' ) ) {
			$this->version = BETTER_BY_DEFAULT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'better-by-default';

		Front::get_instance();
		Admin::get_instance();
		Activator::get_instance();
		Deactivator::get_instance();
		I18::get_instance();
		Blocks::get_instance();

		// Setting Fields.
		Settings_Sections_Fields::get_instance();
		Setting_Fields::get_instance();
		Simplify::get_instance();
		Personalize::get_instance();
		Performance::get_instance();
		Protect::get_instance();
		Miscellaneous::get_instance();

		// Custom dashboard widgets.
		Custom_Dashboard_Widgets::get_instance();
	}
}
