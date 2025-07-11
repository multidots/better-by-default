<?php
/**
 * The dashboard-widgets-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Simplify;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Class for Disable Dashboard Widgets module
 *
 * @since 1.0.0
 */
class Disable_Dashboard_Widgets {

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
		$this->setup_dashboard_widgets_hooks();
	}

	/**
	 * Function is used to define setup_dashboard_widgets hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_dashboard_widgets_hooks() {
		add_action( 'wp_dashboard_setup', array( $this, 'bbd_disable_dashboard_widgets' ), 99 );
		add_action( 'admin_init', array( $this, 'remove_welcome_panel' ) );
		//phpcs:ignore add_action( 'activated_plugin', array( $this, 'bbd_activated_new_plugin_callback' ), 10, 2 );
		//phpcs:ignore add_action( 'deactivated_plugin', array( $this, 'bbd_activated_new_plugin_callback' ), 10, 2 );
	}

	/**
	 * Callback function triggered when a plugin is activated or deactivated.
	 *
	 * This function updates the stored dashboard widgets option when a plugin
	 * is either activated or deactivated to ensure the widgets are refreshed.
	 *
	 * @param string $plugin      Path to the plugin file that was activated or deactivated.
	 * @param bool   $network_wide Whether the plugin was activated network-wide in a multisite.
	 */
	// @codingStandardsIgnoreStart ignored becuase it is not called directly.
	public function bbd_activated_new_plugin_callback( $plugin, $network_wide ) {
		// Instantiate the Disable_Dashboard_Widgets class to retrieve current dashboard widgets.
		$disable_dashboard_widgets = new \BetterByDefault\Inc\Simplify\Disable_Dashboard_Widgets();
		$dashboard_widgets         = $disable_dashboard_widgets->get_dashboard_widgets();

		// Prepare an array to store the retrieved dashboard widgets.
		$options_extra = get_option( BETTER_BY_DEFAULT_EXTRA_OPTIONS, array() );

		$options_extra['dashboard_widgets'] = $dashboard_widgets;
		update_option( BETTER_BY_DEFAULT_EXTRA_OPTIONS, $options_extra, true );
	}
	// @codingStandardsIgnoreEnd

	/**
	 * Disable dashboard widgets
	 *
	 * @since 1.0.0
	 */
	public function bbd_disable_dashboard_widgets() {

		global $wp_meta_boxes;

		// Get list of disabled widgets.
		$options                    = get_option( BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS, array() );
		$disabled_dashboard_widgets = isset( $options['bbd_disabled_dashboard_widgets'] ) ? $options['bbd_disabled_dashboard_widgets'] : array();
		// Store default widgets in extra options. This will be referenced from settings field.
		$dashboard_widgets                  = $this->get_dashboard_widgets();
		$options_extra                      = get_option( BETTER_BY_DEFAULT_EXTRA_OPTIONS, array() );
		$options_extra['dashboard_widgets'] = $dashboard_widgets;
		// Use 'administrator' capability check for single sites and 'super_admin' for multisite.
		if ( current_user_can( 'administrator' ) || ( is_multisite() && is_super_admin() ) ) { //phpcs:ignore
			update_option( BETTER_BY_DEFAULT_EXTRA_OPTIONS, $options_extra, true );
		}

		// Disable widgets.
		if ( is_array( $disabled_dashboard_widgets ) || is_object( $disabled_dashboard_widgets ) ) {
			foreach ( $disabled_dashboard_widgets as $disabled_widget_id_context_priority => $is_disabled ) {
				// e.g. dashboard_activity__normal__core => true/false.
				if ( $is_disabled ) {
					$disabled_widget = explode( '__', $disabled_widget_id_context_priority );
					$widget_id       = isset( $disabled_widget[0] ) ? $disabled_widget[0] : '';
					$widget_context  = isset( $disabled_widget[1] ) ? $disabled_widget[1] : '';
					$widget_priority = isset( $disabled_widget[2] ) ? $disabled_widget[2] : '';
					unset( $wp_meta_boxes['dashboard'][ $widget_context ][ $widget_priority ][ $widget_id ] );
				}
			}
		}
	}

	/**
	 * Get dashboard widgets
	 *
	 * @since 1.0.0
	 */
	public function get_dashboard_widgets() {

		global $wp_meta_boxes;

		$dashboard_widgets = array();
		// Hook into wp_dashboard_setup to ensure all widgets are loaded, including Yoast SEO ones.
		add_action(
			'wp_dashboard_setup',
			function () {
				// Set the dashboard screen to ensure all widgets are available.
				set_current_screen( 'dashboard' );
			},
			999
		); // Run this late to ensure all widgets are loaded.

		if ( ! isset( $wp_meta_boxes['dashboard'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/dashboard.php';
			set_current_screen( 'dashboard' );
			\wp_dashboard_setup();
		}

		if ( isset( $wp_meta_boxes['dashboard'] ) && ! empty( $wp_meta_boxes['dashboard'] ) && is_array( $wp_meta_boxes['dashboard'] ) ) {
			foreach ( $wp_meta_boxes['dashboard'] as $context => $priorities ) {
				foreach ( $priorities as $priority => $widgets ) {
					foreach ( $widgets as $widget_id => $data ) {
						$widget_title = ( isset( $data['title'] ) ) ? wp_strip_all_tags( preg_replace( '/ <span.*span>/im', '', $data['title'] ) ) : '';
						if ( ! empty( $widget_title ) ) {
							$dashboard_widgets[ $widget_id ] = array(
								'id'       => $widget_id,
								'title'    => $widget_title,
								'context'  => $context,
								'priority' => $priority,
							);
						}
					}
				}
			}
		}

		$dashboard_widgets = wp_list_sort( $dashboard_widgets, 'title', 'ASC', true );

		return $dashboard_widgets;
	}

	/**
	 * Maybe remove welcome panel from dashboard
	 *
	 * @since 1.0.0
	 */
	public function remove_welcome_panel() {

		$options                    = get_option( BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS, array() );
		$disabled_dashboard_widgets = isset( $options['bbd_disabled_dashboard_widgets'] ) ? $options['bbd_disabled_dashboard_widgets'] : array();
		$disable_welcome_panel      = isset( $disabled_dashboard_widgets['disable_welcome_panel_in_dashboard'] ) ? $disabled_dashboard_widgets['disable_welcome_panel_in_dashboard'] : false;
		if ( ! empty( $disable_welcome_panel ) ) {
			remove_action( 'welcome_panel', 'wp_welcome_panel' );
		}
	}
}
