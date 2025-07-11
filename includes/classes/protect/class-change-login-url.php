<?php
/**
 * The change-login-url-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Protect;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Change_Login_Url class file.
 */
class Change_Login_Url {

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

		$this->setup_change_login_url_hooks();
	}
	/**
	 * Function is used to define change-login-url hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_change_login_url_hooks() {
		$this->options = get_option( BETTER_BY_DEFAULT_PROTECT_OPTIONS, array() );
		add_action( 'init', array( $this, 'better_by_default_redirect_on_custom_login_url' ) );
		add_filter( 'login_url', array( $this, 'better_by_default_customize_login_url' ), 10, 3 );
		add_filter( 'lostpassword_url', array( $this, 'better_by_default_customize_lost_password_url' ) );
		add_filter( 'register_url', array( $this, 'better_by_default_customize_register_url' ) );
		add_action( 'wp_loaded', array( $this, 'better_by_default_redirect_on_default_login_urls' ) );
		add_action( 'wp_login_failed', array( $this, 'better_by_default_redirect_to_custom_login_url_on_login_fail' ) );
		add_filter( 'login_message', array( $this, 'better_by_default_add_failed_login_message' ) );
	}
	/**
	 * Redirect to valid login URL when custom login slug is part of the request URL
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_redirect_on_custom_login_url() {
		$options           = $this->options;
		$custom_login_slug = $options['custom_login_slug'];
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$url_input = isset( $_SERVER['REQUEST_URI'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		// Make sure $url_input ends with /.
		if ( false !== strpos( $url_input, $custom_login_slug ) ) {
			if ( substr( $url_input, -1 ) !== '/' ) {
				$url_input = $url_input . '/';
			}
		}
		// If URL contains the custom login slug, redirect to the dashboard.
		if ( false !== strpos( $url_input, '/' . $custom_login_slug . '/' ) ) {
			if ( is_user_logged_in() ) {
				if ( ! empty( $options ) && array_key_exists( 'redirect_after_login', $options ) && $options['redirect_after_login'] ) {
					if ( ! empty( $options ) && array_key_exists( 'redirect_after_login_for', $options ) && ! empty( $options['redirect_after_login_for'] ) ) {
						// An almost exact replica of redirect_after_login() in class-redirect-after-login.php.
						$redirect_after_login_to_slug_raw = ( isset( $options['redirect_after_login_to_slug'] ) ? $options['redirect_after_login_to_slug'] : '' );
						if ( ! empty( $redirect_after_login_to_slug_raw ) ) {
							$redirect_after_login_to_slug = trim( trim( $redirect_after_login_to_slug_raw ), '/' );
							if ( false !== strpos( $redirect_after_login_to_slug, '.php' ) ) {
								$slug_suffix = '';
							} else {
								$slug_suffix = '/';
							}
							$relative_path = $redirect_after_login_to_slug . $slug_suffix;
						} else {
							$relative_path = '';
						}
						$redirect_after_login_for = $options['redirect_after_login_for'];
						if ( isset( $redirect_after_login_for ) && count( $redirect_after_login_for ) > 0 ) {
							// Assemble single-dimensional array of roles for which custom URL redirection should happen.
							$roles_for_custom_redirect = array();
							foreach ( $redirect_after_login_for as $role_slug => $custom_redirect ) {
								if ( $custom_redirect ) {
									$roles_for_custom_redirect[] = $role_slug;
								}
							}
							// Does the user have roles data in array form?.
							$user = wp_get_current_user();
							if ( isset( $user->roles ) && is_array( $user->roles ) ) {
								$current_user_roles = $user->roles;
							}
							// Set custom redirect URL for roles set in the settings. Otherwise, leave redirect URL to the default, i.e. admin dashboard.
							foreach ( $current_user_roles as $role ) {
								if ( in_array( $role, $roles_for_custom_redirect, true ) ) {
									wp_safe_redirect( home_url( $relative_path ) );
									exit;
								} else {
									wp_safe_redirect( get_admin_url() );
									exit;
								}
							}
						}
					} else {
						wp_safe_redirect( get_admin_url() );
						exit;
					}
				} else {
					wp_safe_redirect( get_admin_url() );
					exit;
				}
			} else {
				// Redirect to the login URL with custom login slug in the query parameters.
				wp_safe_redirect( site_url( '/wp-login.php?' . $custom_login_slug . '&redirect=false' ) );
				exit;
			}
		}
	}

	/**
	 * Customize login URL returned when calling wp_login_url(). Add the custom login slug.
	 *
	 * @param string $login_url The login URL.
	 * @param string $redirect The path to redirect to on login.
	 * @param bool   $force_reauth Whether to force reauthorization.
	 * @since 1.0.0
	 */
	public function better_by_default_customize_login_url( $login_url, $redirect, $force_reauth ) {
		$options           = $this->options;
		$custom_login_slug = $options['custom_login_slug'];
		$login_url         = home_url( '/' . $custom_login_slug . '/' );
		if ( ! empty( $redirect ) ) {
			$login_url = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $login_url );
		}
		if ( $force_reauth ) {
			$login_url = add_query_arg( 'reauth', '1', $login_url );
		}
		return $login_url;
	}

	/**
	 * Customize lost password URL. Add the custom login slug.
	 *
	 * @param string $lostpassword_url The lost password URL.
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_customize_lost_password_url( $lostpassword_url ) {
		$options           = $this->options;
		$custom_login_slug = $options['custom_login_slug'];
		return $lostpassword_url . '&' . $custom_login_slug;
	}

	/**
	 * Customize registration URL. Add the custom login slug.
	 *
	 * @param string $registration_url The registration URL.
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_customize_register_url( $registration_url ) {
		$options           = $this->options;
		$custom_login_slug = $options['custom_login_slug'];
		return $registration_url . '&' . $custom_login_slug;
	}

	/**
	 * Redirect to /not_found when login URL does not contain the custom login slug
	 * This will redirect /wp-login.php and /wp-admin/ to /not_found/
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_redirect_on_default_login_urls() {
		global $pagenow;

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}
		$options           = $this->options;
		$custom_login_slug = $options['custom_login_slug'];
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$url_input       = isset( $_SERVER['REQUEST_URI'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$url_input_parts = explode( '/', $url_input );
		$redirect_slug   = 'not_found';
		$log             = filter_input( INPUT_POST, 'log', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$pwd             = filter_input( INPUT_POST, 'pwd', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$post_password   = filter_input( INPUT_POST, 'post_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$request_url     = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$request         = wp_parse_url( rawurldecode( $request_url ) );
		$action     = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$checkemail = filter_input( INPUT_GET, 'checkemail', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ( isset( $log ) && ( isset( $pwd ) || isset( $post_password ) ) ) || ( 'validate_2fa' === $action ) || ( 'lostpassword' === $action ) || ( 'confirm' === $checkemail ) || ( 'rp' === $action ) || ( 'resetpass' === $action ) ) {
			if ( $pagenow === 'wp-login.php' && ( ( isset( $request['path'] ) && $request['path'] !== $this->user_trailingslashit( $request['path'] ) && get_option( 'permalink_structure' ) )  ) ) {	// phpcs:ignore
				return;
				// Do nothing. Do not redirect. Allow login.
			} else {
				// Redirect to /not_found/.
				wp_safe_redirect( home_url( $redirect_slug . '/' ), 302 );
				exit;
			}
		} elseif ( is_user_logged_in() ) {
			if ( isset( $url_input_parts[1] ) && 'wp-login.php' === $url_input_parts[1] && empty( $_POST ) ) {	//phpcs:ignore	
				wp_safe_redirect( admin_url(), 302 );
				exit;
			}
		} elseif ( ! is_user_logged_in() ) {
			// Check if request URL ends in /admin/, /wp-admin/, /login/, /wp-login/ or /wp-login.php.
			if ( false !== strpos( $url_input, 'wp-admin' ) ) {
				wp_safe_redirect( home_url( $redirect_slug . '/' ), 302 );
				exit;
			} elseif ( false !== strpos( $url_input, 'wp-login' ) ) {
				$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

				if ( $action && ( 'lostpassword' === $action || 'register' === $action ) ) {
					// When resetting password or registering an account.
					if ( false === strpos( $url_input, $custom_login_slug ) ) {
						// Redirect to /not_found/.
						wp_safe_redirect( home_url( $redirect_slug . '/' ), 302 );
						exit;
					}
				} elseif ( false === strpos( $url_input, $custom_login_slug ) ) {
					wp_safe_redirect( home_url( $redirect_slug . '/' ), 302 );
					exit;
				}
			}
		}
	}

	/**
	 * Redirect to custom login URL on failed login
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_redirect_to_custom_login_url_on_login_fail() {
		global $better_by_default_limit_login;
		$options           = $this->options;
		$custom_login_slug = $options['custom_login_slug'];
		if ( ! isset( $better_by_default_limit_login ) && ! is_array( $better_by_default_limit_login ) && empty( $better_by_default_limit_login['within_lockout_period'] ) ) {
			$should_redirect = true;
			if ( $should_redirect ) {
				// Append 'failed_login=true' so we can output custom error message above the login form.
				wp_safe_redirect( home_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false&failed_login=true' ) );
				exit;
			}
		}
	}

	/**
	 * Add login error message on top of the login form.
	 * Only shown if there's a failed_login URL parameter, and Limit Login Attempts module is not enabled.
	 * If LLA module is enabled, the same custom login error message is handled there.
	 *
	 * @param string $message The login error message.
	 * @since 1.0.0
	 */
	public function better_by_default_add_failed_login_message( $message ) {
		global $better_by_default_limit_login;
		$failed_login = filter_input( INPUT_GET, 'failed_login', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( isset( $failed_login ) && 'true' === $failed_login ) {
			if ( is_null( $better_by_default_limit_login ) ) {
				$message = '<div id="login_error" class="notice notice-error"><b>' . __( 'Error:', 'better-by-default' ) . '</b> ' . __( 'Invalid username/email or incorrect password.', 'better-by-default' ) . '</div>';
			}
		}
		return $message;
	}

	/**
	 * Redirect to custom login URL on successful logout
	 *
	 * @since 1.0.0
	 */
	public function redirect_to_custom_login_url_on_logout_success() {
		$options           = $this->options;
		$custom_login_slug = $options['custom_login_slug'];
		// Redirect to the login URL with custom login slug in it.
		wp_safe_redirect( home_url( 'wp-login.php?' . $custom_login_slug . '&redirect=false' ) );
		exit;
	}

	/**
	 * Customize logout URL by adding the custom login slug to it
	 *
	 * @param string $logout_url The logout URL.
	 * @param string $redirect The path to redirect to on logout.
	 * @since 1.0.0
	 */
	public function customize_logout_url( $logout_url, $redirect ) {
		$options           = $this->options;
		$custom_login_slug = $options['custom_login_slug'];
		if ( ! empty( $redirect ) ) {
			$logout_url = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $logout_url );
		}
		$logout_url .= '&' . $custom_login_slug;
		return $logout_url;
	}

	/**
	 * Check if the permalink structure uses trailing slashes.
	 *
	 * @return bool True if the permalink structure uses trailing slashes, false otherwise.
	 */
	private function use_trailing_slashes() {

		return ( '/' === substr( get_option( 'permalink_structure' ), - 1, 1 ) );
	}

	/**
	 * Add or remove trailing slashes from a string based on permalink structure.
	 *
	 * @param string $str The string to add or remove trailing slashes from.
	 *
	 * @return string The string with or without trailing slashes.
	 */
	private function user_trailingslashit( $str ) {

		return $this->use_trailing_slashes() ? trailingslashit( $str ) : untrailingslashit( $str );
	}
}
