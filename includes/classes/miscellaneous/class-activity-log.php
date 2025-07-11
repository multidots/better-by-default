<?php
/**
 * The activity-log-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the activity-log-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Miscellaneous;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Activity_Log class file.
 */
class Activity_Log {

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

		$this->setup_activity_log_hooks();
	}
	/**
	 * Function is used to define activity-log hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_activity_log_hooks() {

		$this->options = get_option( BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS, array() );

		add_action( 'profile_update', array( $this, 'bbd_profile_update_callback' ), 10, 3 );
		add_action( 'set_user_role', array( $this, 'bbd_set_user_role_callback' ), 10, 3 );
		add_action( 'updated_user_meta', array( $this, 'bbd_updated_user_meta_callback' ), 10, 3 );
		add_action( 'user_register', array( $this, 'bbd_user_register_callback' ), 10, 2 );
		add_action( 'deleted_user', array( $this, 'bbd_deleted_user_callback' ), 10 );
		add_action( 'wp_login', array( $this, 'bbd_wp_login_callback' ), 10, 2 );

		add_action( 'activated_plugin', array( $this, 'bbd_activated_plugin_callback' ), 10, 2 );
		add_action( 'deactivated_plugin', array( $this, 'bbd_deactivated_plugin_callback' ), 10, 2 );
		add_action( 'delete_plugin', array( $this, 'bbd_delete_plugin_callback' ) );

		add_action( 'switch_theme', array( $this, 'bbd_switch_theme_callback' ), 10, 3 );
		add_action( 'deleted_theme', array( $this, 'bbd_deleted_theme_callback' ), 10, 2 );
		add_action( 'updated_option', array( $this, 'bbd_updated_option_callback' ) );
		add_action( 'added_option', array( $this, 'bbd_added_option_callback' ) );

		add_action( 'admin_menu', array( $this, 'bbd_menu_page_activity_logs_callback' ) );
		add_action( 'wp_ajax_activity_logs_data', array( $this, 'activity_logs_data_callback' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_activity_logs_scripts_scripts' ) );
		add_action( 'wp_ajax_activity_logs_data_flush', array( $this, 'activity_logs_data_flush_callback' ) );
	}

	/**
	 * Setup styles and scripts for activity logs
	 *
	 * @since 1.0.0
	 */
	public function enqueue_activity_logs_scripts_scripts() {
		wp_enqueue_script( 'better-by-default-activity-logs-admin', BETTER_BY_DEFAULT_URL . '/assets/build/js/admin/activity-logs-admin.js', array(), 1.0, true );

		wp_localize_script(
			'better-by-default-activity-logs-admin',
			'betterByDefaultActivityLogs',
			array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'admin_option_nonce' ),
			)
		);

		wp_enqueue_style( 'better-by-default-activity-logs-admin', BETTER_BY_DEFAULT_URL . '/assets/build/css/admin/activity-logs-admin.css', array(), 1.0 );
	}


	/**
	 * Log profile update
	 *
	 * @param int     $user_id       User ID.
	 * @param WP_User $old_user_data Object containing user's data prior to update.
	 * @param array   $userdata      The raw array of data passed to wp_insert_user.
	 */
	public function bbd_profile_update_callback( $user_id, $old_user_data, $userdata ) {
		$changed_keys = array();

		foreach ( $userdata as $key => $value ) {
			if ( isset( $old_user_data->data->$key ) && (string) $old_user_data->data->$key !== (string) $value ) {
				$changed_keys[] = $key;
			}
		}

		$this->log(
			array(
				'action'  => 'profile_update',
				'summary' => 'User ' . $user_id . ' profile updated.' . ( ! empty( $changed_keys ) ? ' Changed: ' . implode( ', ', $changed_keys ) : '' ),
			),
			'users'
		);
	}

	/**
	 * Log user role change
	 *
	 * @param int    $user_id User ID.
	 * @param string $role    Role name.
	 * @param array  $old_roles Old roles.
	 */
	public function bbd_set_user_role_callback( $user_id, $role, $old_roles ) {
		if ( ! empty( $old_roles ) ) { // Don't log on user creation.
			$this->log(
				array(
					'action'  => 'set_user_role',
					'summary' => 'User ' . $user_id . ' role changed from ' . implode( ', ', $old_roles ) . ' to ' . $role,
				),
				'users'
			);
		}
	}

	/**
	 * Provides keys of user meta changes to log.
	 *
	 * @return array
	 */
	private function bbd_get_user_meta_keys_to_log_callback() {
		$user_meta_keys_to_log = array(
			'description',
			'first_name',
			'last_name',
			'nickname',
		);

		/**
		 * Filters the user meta keys to log.
		 *
		 * @param array $user_meta_keys_to_log
		 */
		return apply_filters( 'better_by_default_experience_logged_user_meta_changes', $user_meta_keys_to_log );
	}


	/**
	 * Log user meta update
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $user_id    User ID.
	 * @param string $meta_key   Metadata key.
	 */
	public function bbd_updated_user_meta_callback( $meta_id, $user_id, $meta_key ) {
		if ( in_array( $meta_key, $this->bbd_get_user_meta_keys_to_log_callback(), true ) ) {
			$this->log(
				array(
					'action'  => 'updated_user_meta',
					'summary' => 'User ' . $user_id . ' meta updated. Key: ' . $meta_key,
				),
				'users'
			);
		}
	}

	/**
	 * New user created
	 *
	 * @param int   $user_id  User ID.
	 * @param array $userdata The raw array of data passed to wp_insert_user().
	 */
	public function bbd_user_register_callback( $user_id, $userdata ) {
		$role = ( ! empty( $userdata['role'] ) ) ? $userdata['role'] : 'n/a';
		$this->log(
			array(
				'action'  => 'user_register',
				'summary' => 'User ' . $user_id . ' created with role ' . $role,
			),
			'users'
		);
	}

	/**
	 * User deleted
	 *
	 * @param int $user_id  User ID.
	 */
	public function bbd_deleted_user_callback( $user_id ) {
		$this->log(
			array(
				'action'  => 'deleted_user',
				'summary' => 'User ' . $user_id . ' deleted.',
			),
			'users'
		);
	}

	/**
	 * User logged in
	 *
	 * @param string $user_login Username.
	 * @param object $user       WP_User object of the logged-in user.
	 */
	public function bbd_wp_login_callback( $user_login, $user ) {
		$this->log(
			array(
				'action'  => 'wp_login',
				'summary' => 'User ' . $user->ID . ' logged in.',
			),
			'users'
		);
	}

	/**
	 * Plugin is activated
	 *
	 * @param string  $plugin Plugin path.
	 * @param boolean $network_wide Whether the plugin is activated network wide.
	 */
	public function bbd_activated_plugin_callback( $plugin, $network_wide ) {
		$msg = 'Plugin `' . $plugin . '` is activated';

		if ( $network_wide ) {
			$msg .= ' network-wide';
		}

		$this->log(
			array(
				'action'  => 'activated_plugin',
				'summary' => $msg,
			),
			'plugins'
		);
	}

	/**
	 * Plugin is deactivated
	 *
	 * @param string  $plugin Plugin path.
	 * @param boolean $network_wide Whether the plugin is deactivated network wide.
	 */
	public function bbd_deactivated_plugin_callback( $plugin, $network_wide ) {
		$msg = 'Plugin `' . $plugin . '` is deactivated';

		if ( $network_wide ) {
			$msg .= ' network-wide';
		}

		$this->log(
			array(
				'action'  => 'deactivated_plugin',
				'summary' => $msg,
			),
			'plugins'
		);
	}

	/**
	 * Plugin is deleted
	 *
	 * @param string $plugin Plugin path.
	 */
	public function bbd_delete_plugin_callback( $plugin ) {
		$msg = 'Plugin `' . $plugin . '` is deleted';

		$this->log(
			array(
				'action'  => 'delete_plugin',
				'summary' => $msg,
			),
			'plugins'
		);
	}

	/**
	 * Switch theme
	 *
	 * @param string   $new_name  Name of the new theme.
	 * @param WP_Theme $new_theme WP_Theme instance of the new theme.
	 * @param WP_Theme $old_theme WP_Theme instance of the old theme.
	 */
	public function bbd_switch_theme_callback( $new_name, $new_theme, $old_theme ) {
		$this->log(
			array(
				'action'  => 'switch_theme',
				'summary' => 'Theme switched to `' . $new_name . '` from `' . $old_theme->get( 'Name' ) . '`',
			),
			'themes'
		);
	}

	/**
	 * Theme is deleted
	 *
	 * @param string  $stylesheet Stylesheet name.
	 * @param boolean $deleted    Whether the theme is deleted.
	 */
	public function bbd_deleted_theme_callback( $stylesheet, $deleted ) {
		if ( $deleted ) {
			$this->log(
				array(
					'action'  => 'deleted_theme',
					'summary' => 'Theme `' . $stylesheet . '` is deleted',
				),
				'themes'
			);
		}
	}

	/**
	 * Provides options to log.
	 *
	 * @see https://codex.wordpress.org/Option_Reference
	 * @return array
	 */
	private function bbd_get_option_changes_to_log_callback() {
		$options_to_log = array(
			'admin_email',
			'adminhash',
			'blog_public',
			'blogname',
			'category_base',
			'default_comment_status',
			'default_role',
			'home',
			'page_for_posts',
			'page_on_front',
			'permalink_structure',
			'posts_per_page',
			'show_on_front',
			'siteurl',
			'tag_base',
			'upload_path',
			'upload_url_path',
			'users_can_register',
		);

		/**
		 * Filters the options to log.
		 *
		 * @param array $options_to_log Options to log.
		 */
		return apply_filters( 'better_by_default_support_monitor_logged_option_changes', $options_to_log );
	}

	/**
	 * Option is updated
	 *
	 * @param string $option Option name.
	 */
	public function bbd_updated_option_callback( $option ) {
		if ( in_array( $option, $this->bbd_get_option_changes_to_log_callback(), true ) ) {
			$this->log(
				array(
					'action'  => 'updated_option',
					'summary' => 'Option `' . $option . '` is updated',
				),
				'options'
			);
		}
	}

	/**
	 * Option is added
	 *
	 * @param string $option Option name.
	 */
	public function bbd_added_option_callback( $option ) {
		if ( in_array( $option, $this->bbd_get_option_changes_to_log_callback(), true ) ) {
			$this->log(
				array(
					'action'  => 'added_option',
					'summary' => 'Option `' . $option . '` is added',
				),
				'options'
			);
		}
	}

	/**
	 * Create a log entry
	 *
	 * @param array  $data   Data related to the action.
	 * @param string $subgroup Sub group.
	 */
	public function log( $data = array(), $subgroup = null ) {
		global $wpdb;
		$current_logs = array();

		$log_item = array(
			'date'     => time(),
			'summary'  => $data['summary'],
			'subgroup' => $subgroup,
			'user_id'  => get_current_user_id(),
		);

		$current_logs[] = $log_item;

		// Time stamps.
		$unixtime = time();
		if ( function_exists( 'wp_date' ) ) {
			$datetime_wp = wp_date( 'Y-m-d H:i:s', $unixtime );
		} else {
			$datetime_wp = date_i18n( 'Y-m-d H:i:s', $unixtime );
		}

		$table_name = $wpdb->prefix . 'better_by_default_activity_log';
		$data       = array(
			'user_action' => $data['action'],
			'summary'     => $data['summary'],
			'subgroup'    => $subgroup,
			'user_id'     => get_current_user_id(),
			'unixtime'    => $unixtime,
			'datetime_wp' => $datetime_wp,
		);

		$data_format = array(
			'%s', // string.
			'%s', // string.
			'%s', // string.
			'%d', // integer.
			'%d', // integer.
			'%s', // string.
		);

		// Insert into the database.
		$wpdb->insert(	//phpcs:ignore
			$table_name,
			$data,
			$data_format
		);
	}

	/**
	 * Actions - admin_menu.
	 */
	public function bbd_menu_page_activity_logs_callback() {
		add_submenu_page(
			null,
			'Activity Logs',
			'Activity Logs',
			'manage_options',
			'activity-logs',
			array( $this, 'activity_logs_page_callback' )
		);
	}

	/**
	 * Activity Logs - HTML
	 */
	public function activity_logs_page_callback() {
		?>	
		<div id="better-by-default-header" class="better-by-default-header better-by-default-log-header" style="position: sticky; width: auto;">
			<div class="better-by-default-header-left">
				<a href="#" target="_blank" class="main-logo"> <img src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/BBD.svg' ); //phpcs:ignore ?>" width="130" height="75" class="better-by-default-logo" alt="md logo" /> </a>
				<a href="#" target="_blank" class="main-logo mobile-logo"> <img src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/BBD-icon.svg' ); //phpcs:ignore ?>" width="130" height="75" class="better-by-default-logo mobile-logo-img" alt="md logo" /> </a>
			</div>
			<div class="better-by-default-header-right">
				<div class="better-by-default-saving-changes" style="display:none;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#2271b1" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"></path><path fill="#2271b1" d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z"><animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"></animateTransform></path></svg></div>
				<div class="better-by-default-changes-saved" style="display:none;"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><path fill="seagreen" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zM9.29 16.29L5.7 12.7a.996.996 0 1 1 1.41-1.41L10 14.17l6.88-6.88a.996.996 0 1 1 1.41 1.41l-7.59 7.59a.996.996 0 0 1-1.41 0z"></path></svg></div>
				<a href="https://www.multidots.com/" target="_blank" class="md-logo"> <img src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/MD-Logo.svg' ); //phpcs:ignore ?>" width="130" height="75" class="better-by-default-logo" alt="md logo" /> </a>
			</div>
		</div>	
		<div class="main-better-by-default-wrap activity-log-wrapper">
				<div class="main-bbd-desc">
					<h1 class="main-activitylog-title"><?php esc_html_e( 'Activity Logs', 'better-by-default' ); ?></h1>
					<p>This log is designed to monitor and record general user actions and activity on your site. It includes logs for plugin and theme activations, deactivations, and related user actions but does not track modifications to plugin or theme settings. Additionally, it does not log post or page updates, metadata changes, or other content modifications. The focus is primarily on user interactions, general site activity, and key events related to plugins and themes.</p>
				</div>
				<div class="activity-logs-container"></div>
				
				<div class="bbd-bottom">
					<p><?php echo wp_kses_post( __( 'Crafted by the experts at <a href="https://www.multidots.com/" target="_blank">Multidots</a>, designed for professionals who build with WordPress.', 'better-by-default' ) ); ?></p>
				</div>
			</div>
		<?php
	}

	/**
	 * Render the activity logs data table.
	 *
	 * This function queries the database for activity logs and displays them in a table format.
	 * If no logs are found, a message will be shown to the user.
	 *
	 * @since 2.3.0
	 */
	public function activity_logs_data_callback() {
		global $wpdb;

		// Initialize the result array with default values.
		$result = array(
			'success' => 0,
			'content' => __( 'Sorry, Something went wrong.', 'better-by-default' ),
		);

		// Start output buffering.
		ob_start();

		// Query the database for all activity logs ordered by the timestamp in descending order.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$entries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}better_by_default_activity_log ORDER BY id DESC", ARRAY_A );

		// Check if there are any activity logs.
		if ( ! empty( $entries ) && is_array( $entries ) ) {
			// Display logs in a table format.
			?>
			<table class="activity-logs-api-logs-table activity-logs-table" style="text-align: center;">
				<thead>
					<tr class="datatable-tr">
						<th><?php esc_html_e( 'User Action', 'better-by-default' ); ?></th>
						<th><?php esc_html_e( 'Summary', 'better-by-default' ); ?></th>
						<th><?php esc_html_e( 'Sub Group', 'better-by-default' ); ?></th>
						<th><?php esc_html_e( 'User ID', 'better-by-default' ); ?></th>
						<th><?php esc_html_e( 'Logged Time', 'better-by-default' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					// Loop through each activity log entry and display it.
					foreach ( $entries as $entry ) {
						$unixtime = intval( $entry['unixtime'] );
						$date     = function_exists( 'wp_date' ) ? wp_date( 'F j, Y', $unixtime ) : date_i18n( 'F j, Y', $unixtime );
						$time     = function_exists( 'wp_date' ) ? wp_date( 'H:i:s', $unixtime ) : date_i18n( 'H:i:s', $unixtime );
						?>
						<tr class="datatable-tr">
							<td><?php echo esc_html( $entry['user_action'] ); ?></td>
							<td><?php echo esc_html( $entry['summary'] ); ?></td>
							<td><?php echo esc_html( $entry['subgroup'] ); ?></td>
							<td><?php echo esc_html( $entry['user_id'] ); ?></td>
							<td><?php echo esc_html( $date ); ?><br><?php echo esc_html( $time ); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<!-- Modal for additional content -->
			<div class="activity-logs-model-main">
				<div class="activity-logs-model-inner">
					<div class="activity-logs-close-btn">×</div>
					<div class="activity-logs-model-wrap">
						<div class="activity-logs-pop-up-content-wrap"></div>
					</div>
				</div>
				<div class="activity-logs-bg-overlay"></div>
			</div>
			<?php
		} else {
			// Display message if no logs are found.
			?>
			<p class="no-activity-logs-found"><?php esc_html_e( 'No Activity Logs Found!', 'better-by-default' ); ?></p>
			<?php
		}

		// Get the buffered content and set it in the result array.
		$html              = ob_get_clean();
		$result['success'] = 1;
		$result['content'] = $html;

		// Return the result as a JSON response and end the AJAX request.
		wp_send_json( $result );
		wp_die();
	}

	/**
	 * AJAX handler to flush activity logs data.
	 *
	 * This function deletes activity log entries based on a selected time range (e.g., last 7 days, last 15 days).
	 * If 'all' is selected, all logs are removed.
	 *
	 * @since 2.3.0
	 */
	public function activity_logs_data_flush_callback() {
		global $wpdb;

		// Initialize the result array with default values.
		$result = array(
			'success' => 0,
			'content' => __( 'Sorry, Something went wrong.', 'better-by-default' ),
		);

		// Sanitize and verify the nonce for security.
		$activity_logs_nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_SPECIAL_CHARS );
		$range               = filter_input( INPUT_POST, 'range', FILTER_SANITIZE_SPECIAL_CHARS );

		if ( empty( $activity_logs_nonce ) || ! wp_verify_nonce( $activity_logs_nonce, 'admin_option_nonce' ) ) {
			// If the nonce fails, return an error.
			$result['success'] = 0;
			$result['content'] = __( 'Security check failed.', 'better-by-default' );
			wp_send_json( $result );
			wp_die();
		}

		// Determine the time range in seconds based on the selected range.
		$time_range = 30 * DAY_IN_SECONDS; // Default to last 30 days.
		switch ( $range ) {
			case '7':
				$time_range = 7 * DAY_IN_SECONDS; // Last 7 days.
				break;
			case '15':
				$time_range = 15 * DAY_IN_SECONDS; // Last 15 days.
				break;
			case 'all':
				$time_range = 0; // Flush all logs.
				break;
		}

		// Get the current timestamp.
		$current_time = time();
		$cutoff_time  = $current_time - $time_range;

		// If the selected range is 'all', delete all logs.
		if ( 'all' === $range ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$deleted_items = $wpdb->query( "DELETE FROM {$wpdb->prefix}better_by_default_activity_log" );
		} else {
			// Otherwise, delete logs older than the cutoff time.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$deleted_items = $wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}better_by_default_activity_log WHERE unixtime < %d",
					$cutoff_time
				)
			);
		}

		// Check if any logs were deleted.
		if ( 0 === $deleted_items ) {
			$result['success'] = 2; // No logs found for deletion.
		} else {
			$result['success'] = 1; // Logs successfully deleted.
		}

		// Return the result as a JSON response and end the AJAX request.
		wp_send_json( $result );
		wp_die();
	}
}
