<?php
/**
 * The default-template-network-site-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the default-template-network-site-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Miscellaneous;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Default_Template_Network_Site class file.
 */
class Default_Template_Network_Site {

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

		$this->setup_default_template_network_site_hooks();
	}
	/**
	 * Function is used to define default-template-network-site hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_default_template_network_site_hooks() {
		$this->options = get_option( BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS, array() );

		add_action( 'admin_init', array( $this, 'new_blog_setup' ) );
		add_filter( 'theme_page_templates', array( $this, 'register_custom_template' ) );
		add_filter( 'template_include', array( $this, 'load_custom_template' ) );
		add_filter( 'body_class', array( $this, 'add_custom_body_class_based_on_meta' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'bbd-network-default-template-style', BETTER_BY_DEFAULT_URL . 'assets/build/css/frontend/network-default-template.css', array(), $this->version, 'all' );
	}

	/**
	 * Set up the default template and front page for a new blog.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function new_blog_setup() {
		// Switch to the network main site.
		if ( is_multisite() ) {
			switch_to_blog( 1 ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.switch_to_blog_switch_to_blog
		}

		$network_site_image   = esc_url( BETTER_BY_DEFAULT_URL . 'assets/src/images/networkbg.png' );
		$default_page_content = $this->get_default_page_content( $network_site_image );

		// Prepare the default page array.
		$default_page = array(
			'post_title'   => 'Welcome',
			'post_name'    => 'welcome',
			'post_content' => wp_kses_post( $default_page_content ),
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'meta_input'   => array(
				'_wp_page_template' => 'templates/default-template.php',
			),
		);

		// Check if the page does not already exist and insert the page.
		if ( ! get_page_by_path( 'welcome' ) ) {
			$welcome_page = wp_insert_post( $default_page );
			if ( $welcome_page ) {
				update_post_meta( $welcome_page, '_wp_page_template', 'templates/default-template.php' );
				update_post_meta( $welcome_page, 'bbd_default_network_template', 'true' );
				update_option( 'show_on_front', 'page' ); // Set to show a static page as the front page.
				update_option( 'page_on_front', absint( $welcome_page ) ); // Set the welcome page as the front page.
			}
		}

		if ( is_multisite() ) {
			// Restore the current blog.
			restore_current_blog();
		}
	}

	/**
	 * Generate the default page content with the provided image.
	 *
	 * @since 1.0.0
	 * @param string $network_site_image URL of the network site image.
	 * @return string The default page content.
	 */
	private function get_default_page_content( $network_site_image ) {
		// Escape the image URL for security.
		$network_site_image = esc_url( $network_site_image );

		// Build the content with the dynamic image.
		$content = '<!-- wp:group {"className":"network-wrapper alignfull","style":{"background":{"backgroundImage":{"url":"' . $network_site_image . '","id":382,"source":"file","title":"network_bg"},"backgroundPosition":"50% 0"},"elements":{"link":{"color":{"text":"var:preset|color|base-2"}}},"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"right":"0","left":"0","top":"0","bottom":"0"}}},"textColor":"base-2","layout":{"type":"constrained"}} -->
	<div class="wp-block-group network-wrapper alignfull has-base-2-color has-text-color has-link-color" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"style":{"elements":{"link":{"color":{"text":"var:preset|color|base"}}},"spacing":{"margin":{"right":"0","left":"0","top":"0","bottom":"0"}}},"textColor":"base","fontSize":"x-large"} -->
	<h2 class="wp-block-heading has-base-color has-text-color has-link-color has-x-large-font-size" style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0">Welcome to <br>The <strong>Network site</strong></h2>
	<!-- /wp:heading -->
	
	<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|base"}}}},"textColor":"base","fontSize":"medium"} -->
	<p class="has-base-color has-text-color has-link-color has-medium-font-size">This is the default template for the&nbsp;<strong>Network Root Site</strong>, designed to provide seamless user experience.</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:group --></div>
	<!-- /wp:group -->';

		return $content;
	}

	/**
	 * Register the custom template for pages.
	 *
	 * @since 1.0.0
	 * @param array $templates The list of available templates.
	 * @return array
	 */
	public function register_custom_template( $templates ) {
		$templates['templates/default-template.php'] = 'Default Template';
		return $templates;
	}

	/**
	 * Load the custom page template if it's selected.
	 *
	 * @since 1.0.0
	 * @param string $template The path to the template.
	 * @return string The updated template path.
	 */
	public function load_custom_template( $template ) {
		if ( is_page_template( 'templates/default-template.php' ) ) {
			$plugin_template = plugin_dir_path( __FILE__ ) . 'templates/default-template.php';
			if ( file_exists( $plugin_template ) ) {
				return $plugin_template;
			}
		}
		return $template;
	}

	/**
	 * Add body class based on a post meta value.
	 *
	 * @param array $classes The body classes.
	 * @return array The updated body classes.
	 */
	public function add_custom_body_class_based_on_meta( $classes ) {
		// Get the current post ID.
		$post_id = get_the_ID();

		// Check if the meta value exists (replace 'your_meta_key' with your meta key).
		$meta_value = get_post_meta( $post_id, 'bbd_default_network_template', true );

		if ( ! empty( $meta_value ) ) {
			// Add a custom class if meta exists.
			$classes[] = 'page-netwrok-site';
		}

		return $classes;
	}
}
