<?php
/**
 * The rest-api-access-control-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the rest-api-access-control-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Protect;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Rest_API_Access_Control class file.
 */
class Rest_API_Access_Control {

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
	 * Options retrieved from settings.
	 *
	 * @var array
	 */
	private $options;

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

		$this->setup_rest_api_access_control_hooks();
	}
	/**
	 * Function is used to define rest-api-access-control hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_rest_api_access_control_hooks() {
		$this->options = get_option( BETTER_BY_DEFAULT_PROTECT_OPTIONS, array() );

		add_filter( 'rest_authentication_errors', array( $this, 'restrict_rest_api' ), 99 );
	}

	/**
	 * Return a 403 status and corresponding error for unauthed REST API access.
	 *
	 * @param  WP_Error|null|bool $result Error from another authentication handler,
	 *                                    null if we should handle it, or another value
	 *                                    if not.
	 * @return WP_Error|null|bool
	 */
	public function restrict_rest_api( $result ) {

		// Respect other handlers.
		if ( null !== $result ) {
			return $result;
		}
		$options         = $this->options;
		$rest_api_access = isset( $options['rest_api_access_control_options'] ) ? $options['rest_api_access_control_options'] : 'rest_api_except_users_endpoint';

		if ( 'rest_api_everyone' === $rest_api_access ) {
			return $result;
		} elseif ( 'rest_api_logged_in_user' === $rest_api_access && ! $this->user_can_access_rest_api() ) {
			return new \WP_Error( 'rest_api_restricted', esc_html__( 'Authentication Required', 'better-by-default' ), array( 'status' => rest_authorization_required_code() ) );
		} elseif ( 'rest_api_except_users_endpoint' === $rest_api_access && ! $this->user_can_access_rest_api() ) {
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			if ( strpos( $request_uri, '/wp-json/wp/v2/users' ) !== false ) {
				return new \WP_Error( 'rest_api_restricted', esc_html__( 'Authentication Required', 'better-by-default' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		return $result;
	}

	/**
	 * Remove user endpoints for unauthed users.
	 *
	 * @param  array $endpoints Array of endpoints.
	 * @return array
	 */
	public function restrict_user_endpoints( $endpoints ) {
		$options         = $this->options;
		$rest_api_access = isset( $options['rest_api_access_control_options'] ) ? $options['rest_api_access_control_options'] : 'rest_api_except_users_endpoint';

		if ( 'rest_api_everyone' === $rest_api_access ) {
			return $endpoints;
		}

		// Check if the current request is for a specific endpoint you want to protect.

		if ( isset( $endpoints['/wp/v2/users'] ) ) {
			// Iterate through the endpoints and check user authentication.
			foreach ( $endpoints['/wp/v2/users'] as $endpoint ) { //phpcs:ignore
				// Check if the user is logged in.
				if ( ! is_user_logged_in() ) {
					// Remove the endpoint or return an error.
					unset( $endpoints['/wp/v2/users'] );
					return $endpoints;
				}
			}
		}

		return $endpoints;
	}

	/**
	 * Check if user can access REST API based on our criteria
	 *
	 * @return bool         Whether the given user can access the REST API
	 */
	public function user_can_access_rest_api() {
		return is_user_logged_in();
	}
}
