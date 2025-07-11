<?php
/**
 * The strong-password-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Protect;

require_once BETTER_BY_DEFAULT_PATH . '/vendor/autoload.php';

use BetterByDefault\Inc\Traits\Singleton;
use ZxcvbnPhp\Zxcvbn;

/**
 * Strong_Password class file.
 */
class Strong_Password {

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

		$this->setup_strong_password_hooks();
	}
	/**
	 * Function is used to define strong-password hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_strong_password_hooks() {
		$this->options = get_option( BETTER_BY_DEFAULT_PROTECT_OPTIONS, array() );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'user_profile_update_errors', array( $this, 'validate_profile_update' ), 0, 3 );
		add_action( 'validate_password_reset', array( $this, 'validate_strong_password' ), 10, 2 );
		add_action( 'resetpass_form', array( $this, 'validate_resetpass_form' ), 10 );
		add_filter( 'authenticate', array( $this, 'prevent_weak_password_auth' ), 30, 3 );
	}

	/**
	 * Prevent users from authenticating if they are using a weak password
	 *
	 * @param WP_User $user User object.
	 * @param string  $username Username.
	 * @param string  $password Password.
	 * @since 1.0.0
	 * @return \WP_User|\WP_Error
	 */
	public function prevent_weak_password_auth( $user, $username, $password ) {
		$test_tlds = array( 'test', '', '' );
		$tld       = preg_replace( '#^.*\.(.*)$#', '$1', wp_parse_url( site_url(), PHP_URL_HOST ) );

		if ( ! in_array( $tld, $test_tlds, true ) && in_array( strtolower( trim( $password ) ), $this->weak_passwords(), true ) ) {
			return new \WP_Error(
				'Auth Error',
				sprintf(
					'%s <a href="%s">%s</a> %s',
					esc_html__( 'Please', 'better-by-default' ),
					esc_url( wp_lostpassword_url() ),
					esc_html__( 'reset your password', 'better-by-default' ),
					esc_html__( 'in order to meet current security measures.', 'better-by-default' )
				)
			);
		}

		return $user;
	}

	/**
	 * List of popular weak passwords
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function weak_passwords() {
		return array(
			'123456',
			'Password',
			'password',
			'12345678',
			'12345',
			'123456789',
			'letmein',
			'1234567',
			'admin',
			'welcome',
			'monkey',
			'login',
			'abc123',
			'123123',
			'dragon',
			'passw0rd',
			'master',
			'hello',
			'freedom',
			'whatever',
			'654321',
			'password1',
			'1234',

		);
	}

	/**
	 * Setup styles and scripts for passwords
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts_styles() {
		wp_enqueue_script( 'better-by-default-passwords', BETTER_BY_DEFAULT_URL . '/assets/build/js/admin/password.js', array(), 1.0, true );

		wp_localize_script(
			'better-by-default-passwords',
			'betterByDefaultPasswords',
			array(
				'message' => esc_html__( 'Passwords must be medium strength or greater.', 'better-by-default' ),
			)
		);

		wp_enqueue_style( 'better-by-default-passwords', BETTER_BY_DEFAULT_URL . '/assets/build/css/admin/password.css', array(), 1.0 );
	}


	/**
	 * Check user profile update and throw an error if the password isn't strong.
	 *
	 * @param WP_Error $errors Current potential password errors.
	 * @param boolean  $update Whether PW update or not.
	 * @param WP_User  $user_data User being handled.
	 * @since 1.0.0
	 * @return WP_Error
	 */
	public function validate_profile_update( $errors, $update, $user_data ) {
		return $this->validate_strong_password( $errors, $user_data );
	}

	/**
	 * Check password reset form and throw an error if the password isn't strong.
	 *
	 * @param WP_User $user_data User being handled.
	 * @since 1.0.0
	 * @return WP_Error
	 */
	public function validate_resetpass_form( $user_data ) {
		return $this->validate_strong_password( false, $user_data );
	}


	/**
	 * Functionality used by both user profile and reset password validation.
	 *
	 * @param WP_Error $errors Current potential password errors.
	 * @param WP_User  $user_data User being handled.
	 * @since 1.0.0
	 * @return WP_Error
	 */
	public function validate_strong_password( $errors, $user_data ) {
		$password_ok = true;
		$enforce     = true;

		$pass1 = filter_input( INPUT_POST, 'pass1', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$role  = filter_input( INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$password = ( isset( $pass1 ) && trim( $pass1 ) ) ? $pass1 : false;
		$role     = isset( $role ) ? $role : false;
		$user_id  = isset( $user_data->ID ) ? sanitize_text_field( $user_data->ID ) : false;

		// Already got a password error?.
		if ( ( false === $password ) || ( is_wp_error( $errors ) && $errors->get_error_data( 'pass' ) ) ) {
			return $errors;
		}

		// Should a strong password be enforced for this user?.
		if ( $user_id ) {

			// User ID specified.
			$enforce = $this->enforce_for_user( $user_id );

		} elseif ( $role && in_array( $role, apply_filters( 'better_by_default_experience_weak_roles', array( 'subscriber' ) ), true ) ) {
			$enforce = false;
		}

		// Enforce?.
		if ( $enforce ) {
			// Zxcbn requires the mbstring PHP extension and min PHP 7.2, so we'll need to check for it before using.
			if ( function_exists( 'mb_ord' ) && version_compare( PHP_VERSION, '7.2.0' ) >= 0 ) {
				$zxcvbn = new Zxcvbn();

				$pw = $zxcvbn->passwordStrength( $password );

				if ( 3 > (int) $pw['score'] ) {
					$password_ok = false;
				}
			}
		}

		if ( ! $password_ok && is_wp_error( $errors ) ) {
			$errors->add( 'pass', apply_filters( 'better_by_default_experience_password_error_message', __( '<strong>ERROR</strong>: Password must be medium strength or greater.', 'better-by-default' ) ) );
		}

		return $errors;
	}


	/**
	 * Check whether the given WP user should be forced to have a strong password
	 *
	 * @since   1.0.0
	 * @param   int $user_id A user ID.
	 * @return  boolean
	 */
	public function enforce_for_user( $user_id ) {
		$enforce = true;

		// Force strong passwords from network admin screens.
		if ( is_network_admin() ) {
			return $enforce;
		}

		$check_caps = apply_filters(
			'better_by_default_experience_strong_password_caps',
			array(
				'edit_posts',
			)
		);

		if ( ! empty( $check_caps ) ) {
			$enforce = false; // Now we won't enforce unless the user has one of the caps specified.

			foreach ( $check_caps as $cap ) {
				if ( user_can( $user_id, $cap ) ) {
					$enforce = true;
					break;
				}
			}
		}

		return $enforce;
	}
}
