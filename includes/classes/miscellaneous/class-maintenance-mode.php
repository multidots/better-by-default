<?php
/**
 * The maintenance-mode-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the maintenance-mode-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Miscellaneous;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Maintenance_Mode class file.
 */
class Maintenance_Mode {

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

		$this->setup_maintenance_mode_hooks();
	}
	/**
	 * Function is used to define maintenance-mode hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_maintenance_mode_hooks() {
		$this->options = get_option( BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS, array() );
		add_action( 'send_headers', array( $this, 'bbd_send_header_callback' ) );
		add_action( 'plugins_loaded', array( $this, 'bbd_maintenance_mode_icon_on_admin_bar_callback' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_enqueue_styles' ) );
	}

	/**
	 * Redirect for when maintenance mode is enabled
	 *
	 * @since 1.0.0
	 */
	public function bbd_send_header_callback() {
		$options = $this->options;

		$allow_frontend_access = $this->allowed_frontend_access_callback();
		if ( ! is_admin() && ! is_login() && ! $allow_frontend_access ) {
			$maintenance_page_type = 'custom';
			// ======== Customizable maintenance page ========
			if ( 'custom' === $maintenance_page_type ) {
				header( 'HTTP/1.1 503 Service Unavailable', true, 503 );
				header( 'Status: 503 Service Unavailable' );
				header( 'Retry-After: 3600' );
				$heading     = $options['maintenance_page_heading'];
				$description = $options['maintenance_page_description'];

				$title = '';
				?>
				<html>
					<head>
						<title><?php echo esc_html( $title ); ?></title>
						<link rel="stylesheet" id="better-by-default-maintenance" href="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/build/css/frontend/maintenance.css' ); ?>" media="all">	<?php //phpcs:ignore ?>
						<meta name="viewport" content="width=device-width">
					</head>
					<body>
						<div class="page-wrapper">
							<div class="page-overlay">
							</div>
							<div class="message-box">
								<img src="<?php echo esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/maintenance.jpg' ); //phpcs:ignore ?>" width="500" height="300" class="bbd-maintenance-img" alt="maintenance img" />
								<h1><?php echo wp_kses_post( $heading ); ?></h1>
								<div class="description"><?php echo wp_kses_post( $description ); ?></div>
							</div>
						</div>
					</body>
				</html>
				<?php
				exit;
			}
		} else {
			return;
		}
	}

	/**
	 * Show Password Protection admin bar status icon
	 *
	 * @since 1.0.0
	 */
	public function bbd_maintenance_mode_icon_on_admin_bar_callback() {
		add_action( 'wp_before_admin_bar_render', array( $this, 'bbd_add_maintenance_mode_item_on_admin_bar_callback' ) );
	}

	/**
	 * Add WP Admin Bar item
	 *
	 * @since 1.0.0
	 */
	public function bbd_add_maintenance_mode_item_on_admin_bar_callback() {
		global $wp_admin_bar;
		$allow_frontend_access = $this->allowed_frontend_access_callback();
		if ( is_user_logged_in() ) {
			if ( $allow_frontend_access ) {
				$wp_admin_bar->add_menu(
					array(
						'id'    => 'maintenance_mode',
						'title' => '',
						'href'  => admin_url( 'tools.php?page=better-by-default-settings#miscellaneous' ),
						'meta'  => array(
							'title' => __( 'Maintenance mode is currently enabled for this site.', 'better-by-default' ),
						),
					)
				);
			}
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function admin_enqueue_styles() {

		wp_enqueue_style( 'maintainance-mode-admin-style', BETTER_BY_DEFAULT_URL . 'assets/build/css/admin/maintainance-mode.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function front_enqueue_styles() {
		$allow_frontend_access = $this->allowed_frontend_access_callback();
		if ( is_user_logged_in() ) {
			if ( $allow_frontend_access ) {
				wp_enqueue_style( 'maintainance-mode-front-style', BETTER_BY_DEFAULT_URL . 'assets/build/css/frontend/maintenance.css', array(), $this->version, 'all' );
			}
		}
	}

	/**
	 * Check if a user role is allowed to access the frontend
	 *
	 * @since 1.0.0
	 */
	public function allowed_frontend_access_callback() {
		$allow_frontend_access = false;
		if ( current_user_can( 'edit_posts' ) ) {
			$allow_frontend_access = true;
		}
		return $allow_frontend_access;
	}
}
