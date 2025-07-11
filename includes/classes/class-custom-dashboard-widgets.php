<?php
/**
 * The dashboard_widgets-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Custom_Dashboard_Widgets class file.
 */
class Custom_Dashboard_Widgets {

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
		if ( defined( 'BETTER_BY_DEFAULT_VERSION' ) ) {
			$this->version = BETTER_BY_DEFAULT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->setup_dashboard_widgets_hooks();
	}
	/**
	 * Function is used to define dashboard_widgets hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_dashboard_widgets_hooks() {

		add_action( 'wp_dashboard_setup', array( $this, 'better_by_default_add_custom_dashboard_widget' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'better_by_default_enqueue_admin_assets' ) );
	}

	/**
	 * Admin assets enqueue.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function better_by_default_enqueue_admin_assets() {
		wp_enqueue_style( 'better-by-default-dashboard-widget-style', BETTER_BY_DEFAULT_URL . 'assets/build/css/admin/dashboard-widget.css', array(), $this->version, 'all' );
	}
	/**
	 * Function to register the custom dashboard widget.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function better_by_default_add_custom_dashboard_widget() {
		wp_add_dashboard_widget(
			'better_by_default_custom_dashboard_widget',          // Widget ID.
			'Better By Default Overview',            // Widget Title.
			array( $this, 'better_by_default_custom_dashboard_widget_content' )   // Callback Function.
		);
	}

	/**
	 * Function to define the content of the widget.
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_custom_dashboard_widget_content() {
		?>
		<div class="better-by-default-widget-content">
			<h3>WordPress Dashboard with Better By Default</h3>
			<div class="quick-links">
				<h4>Quick Links</h4>
				<ul>
					<li><a href="<?php echo esc_url( admin_url( 'options-general.php' ) ); ?>">Site Settings</a></li>
					<li><a href="<?php echo esc_url( admin_url( 'tools.php' ) ); ?>">Site Tools</a></li>
					<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=better-by-default-settings' ) ); ?>">Better By Default</a></li>
				</ul>
			</div>
			<div class="optimization-tips">
				<h4>Optimization Tips</h4>
				<ul>
					<li>Optimize images before uploading them to your site.</li>
					<li>Regularly clean up your database.</li>
					<li>Use caching to improve site performance.</li>
				</ul>
			</div>
		</div>
		<?php
	}
}
