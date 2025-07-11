<?php
/**
 * The admin-footer-text-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Simplify;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Admin_Footer_Text class file.
 */
class Admin_Footer_Text {

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
		$this->setup_admin_footer_text_hooks();
	}
	/**
	 * Function is used to define setup_admin_footer_text hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_admin_footer_text_hooks() {
		add_filter( 'admin_footer_text', array( $this, 'custom_admin_footer_text_left' ), 20 );
		add_filter( 'update_footer', array( $this, 'custom_admin_footer_text_right' ), 20 );
	}

	/**
	 * Modify footer text
	 *
	 * @since 1.0.0
	 */
	public function custom_admin_footer_text_left() {
		$options                  = get_option( BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS, array() );
		$custom_admin_footer_left = isset( $options['custom_admin_footer_left'] ) && ! empty( $options['custom_admin_footer_left'] ) ? html_entity_decode( $options['custom_admin_footer_left'], ENT_QUOTES, 'UTF-8' ) : '';
		return wp_kses_post( $custom_admin_footer_left );
	}

	/**
	 * Change WP version number text in footer
	 *
	 * @since 1.0.0
	 */
	public function custom_admin_footer_text_right() {
		$options                   = get_option( BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS, array() );
		$custom_admin_footer_right = isset( $options['custom_admin_footer_right'] ) && ! empty( $options['custom_admin_footer_right'] ) ? html_entity_decode( $options['custom_admin_footer_right'], ENT_QUOTES, 'UTF-8' ) : '';
		return wp_kses_post( $custom_admin_footer_right );
	}
}
