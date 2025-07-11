<?php
/**
 * The security_headers-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the security_headers-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Protect;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Security_Headers class file.
 */
class Security_Headers {

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

		$this->setup_security_headers_hooks();
	}
	/**
	 * Function is used to define security_headers hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_security_headers_hooks() {
		add_action( 'wp_headers', array( $this, 'maybe_set_frame_option_header' ), 99, 1 );
	}

	/**
	 * Set the X-Frame-Options header to 'SAMEORIGIN' to prevent clickjacking attacks
	 *
	 * @param string $headers Headers.
	 */
	public function maybe_set_frame_option_header( $headers ) {
		$options = get_option( BETTER_BY_DEFAULT_PROTECT_OPTIONS, array() );

		if ( ! isset( $headers['X-Frame-Options'] ) && ! empty( $options ) && isset( $options['x_frame_options'] ) && ! empty( $options['x_frame_options'] ) ) {
			$headers['X-Frame-Options'] = $options['x_frame_options'];
		}

		if ( ! isset( $headers['X-Xss-Protection'] ) && ! empty( $options ) && isset( $options['x_xss_protection'] ) && ! empty( $options['x_xss_protection'] ) ) {
			$headers['X-Xss-Protection'] = $options['x_xss_protection'];
		}

		if ( ! isset( $headers['X-Content-Type-Options'] ) && ! empty( $options ) && isset( $options['x_content_type_options'] ) && ! empty( $options['x_content_type_options'] ) ) {
			$headers['X-Content-Type-Options'] = $options['x_content_type_options'];
		}

		if ( ! isset( $headers['Referrer-Policy'] ) && ! empty( $options ) && isset( $options['referrer_policy'] ) && ! empty( $options['referrer_policy'] ) ) {
			$headers['Referrer-Policy'] = $options['referrer_policy'];
		}

		if ( ! isset( $headers['permissions_policy'] ) && ! empty( $options ) && isset( $options['permissions_policy'] ) && ! empty( $options['permissions_policy'] ) ) {
			$headers['Permissions-Policy'] = $options['permissions_policy'];
		}

		if ( ! isset( $headers['Content-Security-Policy'] ) && ! empty( $options ) && isset( $options['content_security_policy'] ) && ! empty( $options['content_security_policy'] ) ) {
			$headers['Content-Security-Policy'] = $options['content_security_policy'];
		}

		if ( isset( $headers['X-hacker'] ) && ! empty( $options ) && isset( $options['x_hacker'] ) && 'true' === $options['x_hacker'] ) {
			unset( $headers['X-hacker'] );
		}

		if ( isset( $headers['X-Powered-By'] ) && isset( $options['x_powered_by'] ) && 'true' === $options['x_powered_by'] ) {
			unset( $headers['X-Powered-By'] );
		}

		if ( isset( $headers['X-Pingback'] ) && isset( $options['x_pingback'] ) && 'true' === $options['x_pingback'] ) {
			unset( $headers['X-Pingback'] );
		}

		return $headers;
	}
}
