<?php
/**
 * The xmlrpc-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the xmlrpc-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Protect;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Xmlrpc class file.
 */
class Xmlrpc {

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

		$this->setup_xmlrpc_hooks();
	}
	/**
	 * Function is used to define xmlrpc hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_xmlrpc_hooks() {
		add_filter( 'xmlrpc_enabled', '__return_false' );
	}
}
