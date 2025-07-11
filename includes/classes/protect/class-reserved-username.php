<?php
/**
 * The reserved-username-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Protect;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Reserved_Username class file.
 */
class Reserved_Username {

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

		$this->setup_reserved_username_hooks();
	}
	/**
	 * Function is used to define strong-password hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_reserved_username_hooks() {
		add_filter( 'authenticate', array( $this, 'prevent_common_username' ), 30, 3 );
		add_action( 'user_register', array( $this, 'restrict_admin_username_on_registration' ) );
	}

	/**
	 * Prevent users from authenticating if they are using a generic username
	 *
	 * @param WP_User $user User object.
	 * @param string  $username Username.
	 *
	 * @since   1.0.0
	 *
	 * @return \WP_User|\WP_Error
	 */
	public function prevent_common_username( $user, $username ) {
		$test_tlds = array( 'test', '' );
		$tld       = preg_replace( '#^.*\.(.*)$#', '$1', wp_parse_url( site_url(), PHP_URL_HOST ) );

		if ( ! in_array( $tld, $test_tlds, true ) && in_array( strtolower( trim( $username ) ), $this->reserved_usernames(), true ) ) {
			return new \WP_Error(
				'Auth Error',
				__( 'Please have an administrator change your username in order to meet current security measures.', 'better-by-default' )
			);
		}

		return $user;
	}

	/**
	 * Prevent users from registering with a generic username
	 *
	 * @param int $user_id User ID.
	 *
	 * @since   1.0.0
	 */
	public function restrict_admin_username_on_registration( $user_id ) {
		// Get the user data.
		$user = get_userdata( $user_id );

		// Define restricted usernames.
		$restricted_usernames = $this->reserved_usernames();

		// Check if the created username is restricted.
		if ( in_array( strtolower( trim( $user->user_login ) ), $restricted_usernames, true ) ) {
			// Delete the user if username is restricted.
			if ( is_multisite() ) {
				wpmu_delete_user( $user_id ); // For multisite.
			} else {
				wp_delete_user( $user_id ); // For single site.
			}

			// Add a notice for the admin.
			wp_die( esc_html__( 'The username you selected is not allowed. Please choose a different username.', 'better-by-default' ) );
		}
	}

	/**
	 * List of reserved usernames
	 *
	 * @return array
	 */
	public function reserved_usernames() {
		$common_usernames = array(
			'admin',
			'dev',
			'dns',
			'ftp',
			'null',
			'privacy',
			'root',
			'spam',
			'support',
			'tech',
			'unsubscribe',
			'www',
			'administrator',
			'user',
			'username',
			'demo',
			'sql',
			'guest',
			'test',
			'mysql',
			'client',
			'backup',
			'blog',
			'login',
			'pass',
			'password',
			'tester',
			'user2',
		);

		return apply_filters( 'better_by_default_experience_reserved_usernames', $common_usernames );
	}
}
