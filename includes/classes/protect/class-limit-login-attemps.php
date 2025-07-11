<?php
/**
 * The limit-login-attemps-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the limit-login-attemps-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Protect;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Limit_Login_Attemps class file.
 */
class Limit_Login_Attemps {

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

		if ( defined( 'BETTER_BY_DEFAULT_VERSION' ) ) {
			$this->version = BETTER_BY_DEFAULT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->setup_limit_login_attemps_hooks();
	}
	/**
	 * Function is used to define limit-login-attemps hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_limit_login_attemps_hooks() {

		$this->options = get_option( BETTER_BY_DEFAULT_PROTECT_OPTIONS, array() );

		add_filter( 'authenticate', array( $this, 'maybe_allow_login' ), 999, 1 );

		add_action( 'login_enqueue_scripts', array( $this, 'maybe_hide_login_form' ) );
		add_filter( 'login_message', array( $this, 'add_failed_login_message' ) );
		/**
		 * Note: Anam
		 * Fires after a user login has failed.
		 * And record the failed login attempts in the database.
		 */
		add_action( 'wp_login_failed', array( $this, 'log_failed_login' ), 5 );

		// Higher priority than one in Change Login URL.
		add_action( 'wp_login', array( $this, 'clear_failed_login_log' ) );
	}

	/**
	 * Maybe allow login if not locked out. Should return WP_Error object if not allowed to login.
	 *
	 * @param mixed $user_or_error WP_User object if the user is authenticated. WP_Error or null otherwise.
	 * @since 1.0.0
	 */
	public function maybe_allow_login( $user_or_error ) {
		global $wpdb, $better_by_default_limit_login;

		$table_name = $wpdb->prefix . 'better_by_default_failed_logins';

		// Get values from options needed to do various checks.
		$options = $this->options;

		$failed_login_attempts  = $options['failed_login_attempts'];
		$failed_login_attempts  = max( 1, (int) $failed_login_attempts ); // prevent zero

		$login_lockout_maxcount = $options['login_lockout_maxcount'];

		$ip_address_whitelist_raw = ( isset( $options['limit_login_attempts_ip_whitelist'] ) ) ? explode( PHP_EOL, $options['limit_login_attempts_ip_whitelist'] ) : array();
		$ip_address_whitelist     = array();
		if ( ! empty( $ip_address_whitelist_raw ) ) {
			foreach ( $ip_address_whitelist_raw as $ip_address ) {
				$ip_address_whitelist[] = trim( $ip_address );
			}
		}
		/**
		 * Note: Anam
		 * Get Protect options are stored in the database.
		 */
		$login_logout_options = get_option( BETTER_BY_DEFAULT_PROTECT_OPTIONS, array() );

		$change_login_url  = isset( $login_logout_options['change_login_url'] ) ? $login_logout_options['change_login_url'] : false;
		$custom_login_slug = isset( $login_logout_options['custom_login_slug'] ) ? $login_logout_options['custom_login_slug'] : '';

		// Instantiate object to access common methods.
		$common_methods = new \BetterByDefault\Inc\Common_Methods();

		// Get user/visitor IP address.
		$ip_address   = $common_methods->get_user_ip_address( 'ip' );
		$result_count = 0;
		if ( ! in_array( $ip_address, $ip_address_whitelist, true ) ) { // IP is not whitelisted.
			// Check if IP address has failed login attempts recorded in the DB log.
			$sql = $wpdb->prepare("SELECT * FROM `" . $table_name . "` Where `ip_address` = %s", $ip_address);	//phpcs:ignore
			$result = $wpdb->get_results( $sql, ARRAY_A );	//phpcs:ignore
			$result_count = count( $result );

			if ( $result_count > 0 ) { // IP address has been recorded in the database.
				$fail_count    = $result[0]['fail_count'];
				$lockout_count = $result[0]['lockout_count'];
				$last_fail_on  = $result[0]['unixtime'];
			} else {
				$fail_count    = 0;
				$lockout_count = 0;
				$last_fail_on  = '';
			}
		} else { // IP is whitelisted.
			$result        = array();
			$result_count  = 0;
			$fail_count    = 0;
			$lockout_count = 0;
			$last_fail_on  = '';
		}

		/**
		 * Note: Anam
		 * Get the current URL of the page
		 */
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		// Initialize the global variable.
		$better_by_default_limit_login = array(
			'ip_address'               => $ip_address,
			'request_uri'              => $request_uri,
			'ip_address_log'           => $result,
			'fail_count'               => $fail_count,
			'lockout_count'            => $lockout_count,
			'maybe_lockout'            => false,
			'extended_lockout'         => false,
			'within_lockout_period'    => false,
			'lockout_period'           => 0,
			'lockout_period_remaining' => 0,
			'failed_login_attempts'    => $failed_login_attempts,
			'login_lockout_maxcount'   => $login_lockout_maxcount,
			'default_lockout_period'   => ( 60 * 15 - 1 ), // 15 minutes in seconds.
			'extended_lockout_period'  => ( 24 * 60 * 60 - 1 ), // 24 hours in seconds.
			'change_login_url'         => $change_login_url, // is custom login URL enabled?.
			'custom_login_slug'        => $custom_login_slug,
		);

		if ( ! in_array( $ip_address, $ip_address_whitelist, true ) ) { // IP is not whitelisted.

			if ( $result_count > 0 ) { // IP address has been recorded in the database.

				// Failed attempts have been recorded and fulfills lockout condition.
				/**
				 * Note: Anam
				 * fail_count = number of failed attempts by the user
				 * failed_login_attempts = number of limit setted for 15 min lockout in settings panel.
				 */
				if ( ! empty( $fail_count ) && ( 0 === ( $fail_count ) % $failed_login_attempts ) ) {
					/**
					 * Note: Anam
					 * if user already tried the number of times defined in settings
					 * Now below code will be executed
					 */
					$better_by_default_limit_login['maybe_lockout'] = true;

					// Has reached max / gone beyond number of lockouts allowed?.
					/**
					 * Note: Anam
					 * Set the lockout status for extended lockout
					 * Meaning: 24 hours lockout
					 * Set the lockout period remaining
					 */
					if ( $lockout_count >= $login_lockout_maxcount ) {
						$better_by_default_limit_login['extended_lockout'] = true;
						$lockout_period                                    = $better_by_default_limit_login['extended_lockout_period'];
					} else {
						$better_by_default_limit_login['extended_lockout'] = false;
						$lockout_period                                    = $better_by_default_limit_login['default_lockout_period'];
					}

					$better_by_default_limit_login['lockout_period'] = $lockout_period;

					// User/visitor is still within the lockout period.
					/**
					 * Note: Anam
					 * If user is still within the lockout period
					 * then show the message to the user that you are locked out
					 * and you can login again after the lockout period
					 * otherwise return the user
					 * and user can login and record the failed login attempts in the database
					 * and update the record in the database
					 */
					if ( ( time() - $last_fail_on ) <= $better_by_default_limit_login['lockout_period'] ) {
						/**
						 * Note: Anam
						 * User is still within the 24 hour lockout period.
						 */
						$better_by_default_limit_login['within_lockout_period']    = true;
						$better_by_default_limit_login['lockout_period_remaining'] = $better_by_default_limit_login['lockout_period'] - ( time() - $last_fail_on );
						/**
						 * Note: Anam
						 * Get the remaining lockout period in minutes and seconds
						 */
						if ( $better_by_default_limit_login['lockout_period_remaining'] <= 60 ) {
							// Get remaining lockout period in minutes and seconds.
							$lockout_period_remaining = $better_by_default_limit_login['lockout_period_remaining'] . ' seconds';

						} elseif ( $better_by_default_limit_login['lockout_period_remaining'] <= 60 * 60 ) {
							// Get remaining lockout period in minutes and seconds.
							$lockout_period_remaining = $common_methods->seconds_to_period( $better_by_default_limit_login['lockout_period_remaining'], 'to-minutes-seconds' );

						} elseif ( $better_by_default_limit_login['lockout_period_remaining'] > 60 * 60 && $better_by_default_limit_login['lockout_period_remaining'] <= 24 * 60 * 60 ) {
							// Get remaining lockout period in minutes and seconds.
							$lockout_period_remaining = $common_methods->seconds_to_period( $better_by_default_limit_login['lockout_period_remaining'], 'to-hours-minutes-seconds' );

						} elseif ( $better_by_default_limit_login['lockout_period_remaining'] > 24 * 60 * 60 ) {
							// Get remaining lockout period in minutes and seconds.
							$lockout_period_remaining = $common_methods->seconds_to_period( $better_by_default_limit_login['lockout_period_remaining'], 'to-days-hours-minutes-seconds' );

						}

						$error = new \WP_Error( 'ip_address_blocked', '<b>WARNING!</b> You\'ve been locked out. You can login again in ' . $lockout_period_remaining . '.' );

						return $error;

					} else { // User/visitor is no longer within the lockout period.
						$better_by_default_limit_login['within_lockout_period'] = false;
						/**
						 * Note: Anam
						 * number of 15 min lockouts === 24 hours lockout
						 */
						if ( $lockout_count === $login_lockout_maxcount ) {
							// Remove the DB log entry for the current IP address. i.e. release from extended lockout.
							$where        = array( 'ip_address' => $ip_address );
							$where_format = array( '%s' );

							// Delete existing data in the database.
							$wpdb->delete(		// phpcs:ignore
								$table_name,    // phpcs:ignore $wpdb->prefix . 'better_by_default_failed_logins';
								$where,
								$where_format
							);
						}

						return $user_or_error;

					}
				} else {
					/**
					 * Note: Anam
					 * User has not tried the number of times defined in settings
					 */
					$better_by_default_limit_login['maybe_lockout'] = false;

					return $user_or_error;

				}
			} else { // IP address has not been recorded in the database.

				return $user_or_error;

			}
		} else {  // IP is whitelisted.
			return $user_or_error;
		}
	}

	/**
	 * Disable login form inputs via CSS
	 *
	 * @since 1.0.0
	 */
	public function maybe_hide_login_form() {
		global $better_by_default_limit_login;
		/**
		 * Note: Anam
		 * value of $better_by_default_limit_login['within_lockout_period'] is boolean - true / false
		 */
		if ( isset( $better_by_default_limit_login['within_lockout_period'] ) && $better_by_default_limit_login['within_lockout_period'] ) {
			/**
			 * Note: Anam
			 * If user is within lockout period
			 * Hide login for [ 24 hours ]
			 */
			// Hide logo, login form and the links below it.
			?>
			<script>
				document.addEventListener("DOMContentLoaded", function(event) {
					var loginForm = document.getElementById("loginform");
					loginForm.remove();
				});
			</script>
			<?php
			wp_enqueue_style( 'better_by_default_limit_login_style', BETTER_BY_DEFAULT_URL . 'assets/build/admin.css', array(), $this->version, 'all' );

			$better_by_default_limit_login_css = "
			body.login {
				background: #fff;
				position: relative;
			}
			.login #login .notice-error {
				overflow: hidden;
				background: transparent;
				border: 0;
				margin: 0;
				padding: 0;
				width: 500px;
				box-shadow: none;
			}
			.login #login .notice-error >p {
				padding: 0 50px;
				font-size: 18px;
				line-height: 28px;
				text-align: center;
				color: #000;
			}
			.login #login {
				width: 500px;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				height: 100vh;
				padding: 0 !important;
			}
			.login #login .notice-error >p>b {
				display: block;
				font-size: 50px;
				margin-bottom: 25px;
				padding: 10px 0;
			}
			.login #login .notice-error >p>b:before {
				content: '⚠';
				font-size: 50px;
				color: #d63638;
				margin-right: 5px;
			}
			#login h1,
			#loginform,
			#login #nav,
			#backtoblog,
			.language-switcher { 
				display: none; 
			}

			@media screen and (max-height: 550px) {

				#login {
					padding: 80px 0 20px !important;
				}

			}
			";
			wp_add_inline_style( 'better_by_default_limit_login_style', $better_by_default_limit_login_css );		

		} else {
			$options               = $this->options;
			$failed_login_attempts = $options['failed_login_attempts'];
			$rl                    = filter_input( INPUT_GET, 'rl', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$page_was_reloaded     = isset( $rl ) && 1 === (int) $rl ? true : false;
			$failed_login_attempts = ! empty( $failed_login_attempts ) ? intval( $failed_login_attempts ) : 0;

			/**
			 * Note: Anam
			 * $failed_login_attempts = number of 15 min lockout setted in the settings panel.
			 * $better_by_default_limit_login['fail_count'] = number of failed attempts by the user
			 */
			if ( isset( $better_by_default_limit_login['fail_count'] )
				&& ( ( $failed_login_attempts - 1 ) === intval( $better_by_default_limit_login['fail_count'] )
					|| ( 2 * $failed_login_attempts - 1 ) === intval( $better_by_default_limit_login['fail_count'] )
					|| ( 3 * $failed_login_attempts - 1 ) === intval( $better_by_default_limit_login['fail_count'] )
					|| ( 4 * $failed_login_attempts - 1 ) === intval( $better_by_default_limit_login['fail_count'] )
					|| ( 5 * $failed_login_attempts - 1 ) === intval( $better_by_default_limit_login['fail_count'] )
					|| ( 6 * $failed_login_attempts - 1 ) === intval( $better_by_default_limit_login['fail_count'] )
				)
			) {
				// Custom Login URL is not enabled then, e.g. /manage.
				// Default login URL, i.e. /wp-login.php.
				// Reload the login page so we get the up-to-date data in $better_by_default_limit_login.
				// Only reload if page was not reloaded before. This prevents infinite reloads.
				if ( ( ! isset( $options['change_login_url'] ) || empty( $options['change_login_url'] ) ) && ! $page_was_reloaded ) {
					?>
					<script>
						/**
						 * Note: Anam
						 * Comment the below code by Anam.
						 */
						// let url = window.location.href;    
						// if (url.indexOf('?') > -1){
						// 	url += '&rl=1'
						// } else {
						// 	url += '?rl=1'
						// }
						// location.replace(url);
					</script>
					<?php
				}
			}
		}
	}

	/**
	 * Add login error message on top of the login form
	 *
	 * @param string $message The login error message.
	 * @since 1.0.0
	 */
	public function add_failed_login_message( $message ) {
		global $wpdb, $better_by_default_limit_login;
		/**
		 * Note: Anam
		 * Get the last failed login time from the database
		 */
		$table_name = $wpdb->prefix . 'better_by_default_failed_logins';
		$common_methods = new \BetterByDefault\Inc\Common_Methods();
		$ip_address   = $common_methods->get_user_ip_address( 'ip' );
		$sql = $wpdb->prepare("SELECT * FROM `" . $table_name . "` Where `ip_address` = %s", $ip_address);	//phpcs:ignore
		$result = $wpdb->get_results( $sql, ARRAY_A );	//phpcs:ignore
		$last_fail_on  = count($result) > 0 ? $result[0]['unixtime'] : 0;

		if ( isset( $_REQUEST['failed_login'] ) && 'true' === sanitize_text_field( $_REQUEST['failed_login'] ) ) {	// phpcs:ignore

			if ( ! is_null( $better_by_default_limit_login ) && isset( $better_by_default_limit_login['within_lockout_period'] ) && ! $better_by_default_limit_login['within_lockout_period'] ) {

				$message = '<div id="login_error" class="notice notice-error"><b>' . __( 'Error:', 'better-by-default' ) . '</b> ' . __( 'Invalid username/email or incorrect password.', 'better-by-default' ) . '</div>';

			}
		}else if( isset( $_REQUEST['reauth'] ) && $_REQUEST['reauth'] === '1' ){ // phpcs:ignore
			if ( ( time() - $last_fail_on ) <= $better_by_default_limit_login['lockout_period'] ) {
				/**
				 * Note: Anam
				 * User is still within the 24 hour lockout period.
				 */
				$better_by_default_limit_login['within_lockout_period']    = true;
				$better_by_default_limit_login['lockout_period_remaining'] = $better_by_default_limit_login['lockout_period'] - ( time() - $last_fail_on );
				/**
				 * Note: Anam
				 * Get the remaining lockout period in minutes and seconds
				 */
				if ( $better_by_default_limit_login['lockout_period_remaining'] <= 60 ) {
					// Get remaining lockout period in minutes and seconds.
					$lockout_period_remaining = $better_by_default_limit_login['lockout_period_remaining'] . ' seconds';

				} elseif ( $better_by_default_limit_login['lockout_period_remaining'] <= 60 * 60 ) {
					// Get remaining lockout period in minutes and seconds.
					$lockout_period_remaining = $common_methods->seconds_to_period( $better_by_default_limit_login['lockout_period_remaining'], 'to-minutes-seconds' );

				} elseif ( $better_by_default_limit_login['lockout_period_remaining'] > 60 * 60 && $better_by_default_limit_login['lockout_period_remaining'] <= 24 * 60 * 60 ) {
					// Get remaining lockout period in minutes and seconds.
					$lockout_period_remaining = $common_methods->seconds_to_period( $better_by_default_limit_login['lockout_period_remaining'], 'to-hours-minutes-seconds' );

				} elseif ( $better_by_default_limit_login['lockout_period_remaining'] > 24 * 60 * 60 ) {
					// Get remaining lockout period in minutes and seconds.
					$lockout_period_remaining = $common_methods->seconds_to_period( $better_by_default_limit_login['lockout_period_remaining'], 'to-days-hours-minutes-seconds' );

				}


				$message = '<div id="login_error" class="notice notice-error"><p><b>WARNING!</b> You\'ve been locked out. You can login again in ' . $lockout_period_remaining . '</p></div>';

			}
		}

		return $message;
	}

	/**
	 * Log failed login attempts
	 *
	 * @param string $username The username.
	 * @since 1.0.0
	 */
	// Note: Anam - Fires after a user login has failed.
	/**
	 * For failed login
	 *
	 * @param [string] $username user name of the user.
	 */
	public function log_failed_login( $username ) {

		global $wpdb, $better_by_default_limit_login;

		$table_name = $wpdb->prefix . 'better_by_default_failed_logins';

		// Check if the IP address has been used in a failed login attempt before, i.e. has it been recorded in the database?.
		$sql = $wpdb->prepare( "SELECT * FROM `" . $table_name . "` WHERE `ip_address` = %s", $better_by_default_limit_login['ip_address'] );	//phpcs:ignore
		/**
		 * Note: it is getting the previous record of the IP address from the database.
		 */
		$result = $wpdb->get_results( $sql, ARRAY_A );	//phpcs:ignore
		$result_count = count( $result );
		if( $result_count > 0 ){
			$last_fail_on  = $result[0]['unixtime'];
			if( ( time() - $last_fail_on ) <= $better_by_default_limit_login['lockout_period'] ){
				return;
			}
		}

		// Update logged info for the IP address in the global variable.
		$better_by_default_limit_login['ip_address_log'] = $result;

		/**
		 * Note: Anam
		 * update fail count and lockout count based on the request
		 */
		if ( 0 === $result_count ) { // IP address has not been recorded in the database.

			$new_fail_count    = 1;
			$new_lockout_count = 0;

		} else { // phpcs:ignore 
			// IP address has been recorded in the database.
			/**
			 * Note: Anam
			 * Update fail count and lockout count based on Request.
			 */
			if ( isset( $_REQUEST ) && count( $_REQUEST ) > 0 ) { // phpcs:ignore
				$new_fail_count    = $result[0]['fail_count'] + 1;
				$new_lockout_count = floor( ( $result[0]['fail_count'] + 1 ) / $better_by_default_limit_login['failed_login_attempts'] );
			} else { // phpcs:ignore
				$new_fail_count    = $result[0]['fail_count'];
				$new_lockout_count = floor( ( $result[0]['fail_count'] ) / $better_by_default_limit_login['failed_login_attempts'] );
			}
			// phpcs:ignore $new_fail_count    = $result[0]['fail_count'] + 1;
			// phpcs:ignore $new_lockout_count = floor( ( $result[0]['fail_count'] + 1 ) / $better_by_default_limit_login['failed_login_attempts'] );

		}

		// Time stamps.
		$unixtime = time();
		if ( function_exists( 'wp_date' ) ) {
			$datetime_wp = wp_date( 'Y-m-d H:i:s', $unixtime );
		} else {
			$datetime_wp = date_i18n( 'Y-m-d H:i:s', $unixtime );
		}

		$data = array(
			'ip_address'    => $better_by_default_limit_login['ip_address'],
			'username'      => $username,
			'fail_count'    => $new_fail_count,
			'lockout_count' => $new_lockout_count,
			'request_uri'   => $better_by_default_limit_login['request_uri'],
			'unixtime'      => $unixtime,
			'datetime_wp'   => $datetime_wp,
			'info'          => '',
		);

		$data_format = array(
			'%s',
			'%s',
			'%d',
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
		);
		/**
		 * Note: Anam
		 * If IP address has not been recorded in the database or respective IP address record is not found in the database
		 * then insert the record in the database
		 * otherwise update the record in the database
		 */
		if ( 0 === $result_count ) {
			// Insert into the database.
			$result = $wpdb->insert(	//phpcs:ignore
				$table_name,
				$data,
				$data_format
			);

		} else {
			// phpcs:ignore $fail_count    = $result[0]['fail_count'];
			// phpcs:ignore $lockout_count = $result[0]['lockout_count'];
			/**
			 * Note: Anam
			 * New code added by Anam
			 */
			$fail_count    = $new_fail_count;
			$lockout_count = $new_lockout_count;

			$where        = array( 'ip_address' => $better_by_default_limit_login['ip_address'] );
			$where_format = array( '%s' );

			/**
			 * Note: Anam
			 * if fail count is not empty and fail count is multiple of failed login attempts
			 * or if user already tried the number of times defined in settings
			 * Now below code will be executed
			 * $fail_count = number of failed attempts by the user
			 * $better_by_default_limit_login['failed_login_attempts'] = number of limit setted for 15 min lockout in settings panel.
			 */
			// Failed attempts have been recorded and fulfills lockout condition.
			if ( ! empty( $fail_count ) && ( intval( $fail_count ) % intval( $better_by_default_limit_login['failed_login_attempts'] ) === 0 ) ) {

				// Has reached max / gone beyond number of lockouts allowed?.
				/**
				 * Note: Anam
				 * $lockout_count = number of 15 min lockouts
				 * $better_by_default_limit_login['login_lockout_maxcount'] = 24 hour lockout variable
				 * if number of 15 min lockout is greater than or equal 24 hour lockout number setted in the settings panel.
				 * Update variables for the respective lockout.
				 */
				if ( $lockout_count >= $better_by_default_limit_login['login_lockout_maxcount'] ) {
					/**
					 * Note: Anam
					 * Set the lockout status for extended lockout
					 * Meaning: 24 hours lockout
					 */
					$better_by_default_limit_login['extended_lockout'] = true;
					$lockout_period                                    = $better_by_default_limit_login['extended_lockout_period'];
				} else {
					// Note: Anam | Set 15 min lockout.
					$better_by_default_limit_login['extended_lockout'] = false;
					$lockout_period                                    = $better_by_default_limit_login['default_lockout_period'];
				}

				$better_by_default_limit_login['lockout_period'] = $lockout_period;

				// User/visitor is still within the lockout period.
				/**
				 * Note: Anam
				 * $lockout_count = number of 15 min lockouts
				 * $better_by_default_limit_login['login_lockout_maxcount'] = 24 hour lockout variable
				 * If number of 15 min lockout is less than 24 hour lockout number setted in the settings panel.
				 */
				if ( $lockout_count <= $better_by_default_limit_login['login_lockout_maxcount'] ) {

					if ( isset( $_REQUEST ) && count( $_REQUEST ) > 0 ) { // phpcs:ignore
						// Update existing data in the database.
						$wpdb->update(	//phpcs:ignore
							$table_name,
							$data,
							$where,
							$data_format,
							$where_format
						);
					}
				}
				?>
				<?php
				/**
				 * Note: Anam
				 * Redirect to the same page
				 */
				if ( ! isset( $_GET['redirect_done'] ) ) { // phpcs:ignore
					// Add a query parameter to avoid repeated redirects.
					$request_uri = isset( $_SERVER['REQUEST_URI'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
					$new_url     = add_query_arg( 'redirect_done', '1', $request_uri );
					wp_safe_redirect( $new_url );
					exit; // Always use exit after wp_redirect.
				}
			} else { // phpcs:ignore
				/**
				 * Note: Anam
				 * User has not tried the number of times defined in settings
				 * now update the record in the database
				 */
				// Update existing data in the database.
				if ( isset( $_REQUEST ) && count( $_REQUEST ) > 0 ) { // phpcs:ignore
					// phpcs:ignore $name = isset($_GET['rl']) ? $_GET['rl'] : 0; // Default to 'Guest' if 'name' is not set
					$update_status = $wpdb->update(	//phpcs:ignore
						$table_name,
						$data,
						$where,
						$data_format,
						$where_format
					);
				}
			}
		}
	}

	/**
	 * Clear failed login attempts log after successful login
	 *
	 * @since 1.0.0
	 */
	public function clear_failed_login_log() {
		global $wpdb, $better_by_default_limit_login;

		$table_name = $wpdb->prefix . 'better_by_default_failed_logins';
		$ip_address = isset( $better_by_default_limit_login['ip_address'] ) ? $better_by_default_limit_login['ip_address'] : '';

		// Remove the DB log entry for the current IP address.

		$where        = array( 'ip_address' => $ip_address );
		$where_format = array( '%s' );

		$wpdb->delete(	//phpcs:ignore
			$table_name,
			$where,
			$where_format
		);
	}
}
