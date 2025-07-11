<?php
/**
 * The auto-update-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Simplify;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Auto_Update class file.
 */
class Auto_Update {

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

		$this->setup_auto_update_hooks();
	}
	/**
	 * Function is used to define auto-update hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_auto_update_hooks() {
		add_filter( 'allow_major_auto_core_updates', '__return_false' );
		add_filter( 'allow_minor_auto_core_updates', '__return_false' );
		add_filter( 'allow_dev_auto_core_updates', '__return_false' );

		//phpcs:ignore // removed for the autoupdate disable option // add_filter( 'aauto_uupdate_plugin__return_false' );
		// add_filter( 'auto_update_theme', '__return_false' );
		// add_filter( 'auto_update_translation', '__return_false' );
	}
}
