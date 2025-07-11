<?php
/**
 * The last-login-column-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the last-login-column-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Simplify;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Last_Login_Column class file.
 */
class Last_Login_Column {

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
	 * The setting options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $options    The setting options of this plugin.
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
		$this->setup_last_login_column_hooks();
	}
	/**
	 * Function is used to define last-login-column hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_last_login_column_hooks() {
		$this->options = get_option( BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS, array() );
		add_action( 'wp_login', array( $this, 'log_login_datetime' ) );
		add_filter( 'manage_users_columns', array( $this, 'add_last_login_column' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'show_last_login_info' ), 10, 3 );
		add_action( 'admin_print_styles-users.php', array( $this, 'add_column_style' ) );
	}

	/**
	 * Log date time when a user last logged in successfully
	 *
	 * @param string $user_login The user login name.
	 *
	 * @since   1.0.0
	 */
	public function log_login_datetime( $user_login ) {
		$user = get_user_by( 'login', $user_login );
		// by username.
		if ( $user && isset( $user->ID ) ) {
			update_user_meta( $user->ID, 'better_by_default_last_login_on', time() );
		}
	}

	/**
	 * Add Last Login column to users list table
	 *
	 * @param array $columns The columns of the users list table.
	 *
	 * @since   1.0.0
	 */
	public function add_last_login_column( $columns ) {
		$columns['better_by_default_last_login'] = 'Last Login';
		return $columns;
	}

	/**
	 * Show Last Login info in Last Login column.
	 *
	 * @param string $output     The output to print.
	 * @param string $column_name The name of the column.
	 * @param int    $user_id     The ID of the user.
	 *
	 * @since   1.0.0
	 */
	public function show_last_login_info( $output, $column_name, $user_id ) {
		if ( 'better_by_default_last_login' === $column_name ) {
			if ( ! empty( get_user_meta( $user_id, 'better_by_default_last_login_on', true ) ) ) {
				$last_login_unixtime = (int) get_user_meta( $user_id, 'better_by_default_last_login_on', true );
				$date_format         = get_option( 'date_format' );
				$time_format         = get_option( 'time_format' );
				if ( function_exists( 'wp_date' ) ) {
					$output = wp_date( $date_format . ' - ' . $time_format, $last_login_unixtime );
				} else {
					$output = date_i18n( $date_format . ' - ' . $time_format, $last_login_unixtime );
				}
				$output = str_replace( ' - ', ' at ', $output );
			} else {
				$output = __( 'Never', 'better-by-default' );
			}
		}
		return $output;
	}

	/**
	 * Add custom CSS for the Last Login column
	 *
	 * @since   1.0.0
	 */
	public function add_column_style() {

		wp_enqueue_style( 'column_better_by_default_last_login', BETTER_BY_DEFAULT_URL . 'assets/build/admin.css', array(), $this->version, 'all' );

		$add_column__css = "
			.column-better_by_default_last_login {
				width: 200px;
			}";
		wp_add_inline_style( 'column_better_by_default_last_login', $add_column__css );			
	}
}
