<?php
/**
 * The obscure-wp-head-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the obscure-wp-head-specific stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Performance;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Obscure_Wp_Head class file.
 */
class Obscure_Wp_Head {

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
	 * Options retrieved from settings.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->options = get_option( BETTER_BY_DEFAULT_PERFORMANCE_OPTIONS, array() );
		$this->setup_obscure_wp_head_hooks();
	}

	/**
	 * Set up hooks for obscuring wp_head based on options.
	 *
	 * @return void
	 */
	public function setup_obscure_wp_head_hooks() {
		if ( ! empty( $this->options['disable_obscure_wp_head_items'] ) && true === (bool) $this->options['disable_obscure_wp_head_items'] ) {

			add_filter( 'the_generator', '__return_false' );
		}

		if ( ! empty( $this->options['remove_rsd_wlw_links'] ) && true === (bool) $this->options['remove_rsd_wlw_links'] ) {
			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}

		if ( ! empty( $this->options['remove_shortlinks'] ) && true === (bool) $this->options['remove_shortlinks'] ) {
			remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		}

		if ( ! empty( $this->options['remove_rss_links'] ) && true === (bool) $this->options['remove_rss_links'] ) {
			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'feed_links_extra', 3 );
			add_filter( 'feed_links_show_comments_feed', '__return_false' );
		}

		if ( ! empty( $this->options['remove_generator_tag'] ) && true === (bool) $this->options['remove_generator_tag'] ) {
			remove_action( 'wp_head', 'wp_generator' );
		}

		if ( ! empty( $this->options['remove_emoji_scripts'] ) && true === (bool) $this->options['remove_emoji_scripts'] ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			add_filter( 'emoji_svg_url', '__return_false' );
		}

		if ( ! empty( $this->options['remove_pingback'] ) && true === (bool) $this->options['remove_pingback'] ) {
			// Force ping status to closed.
			add_filter( 'pings_open', '__return_false' );
			add_filter( 'pre_option_default_ping_status', array( $this, 'ping_status_callback' ) );

			// Disable pingbacks in XML-RPC.
			add_filter( 'xmlrpc_methods', array( $this, 'xmlrpc_methods_callback' ) );
			add_action( 'xmlrpc_call', array( $this, 'xmlrpc_call_callback' ) );

			// Remove 'trackbacks' support from all post types.
			add_action( 'init', array( $this, 'remove_trackbacks_support_callback' ), PHP_INT_MAX );

			// Remove trackback rewrite rules.
			add_filter( 'rewrite_rules_array', array( $this, 'rewrite_rules_callback' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'pingbacks_enqueue_styles' ) );
		}

		add_action( 'wp_loaded', array( $this, 'remove_wp_head_actions' ) );
	}

	/**
	 * Remove additional wp_head actions based on options.
	 *
	 * @return void
	 */
	public function remove_wp_head_actions() {
		if ( ! empty( $this->options['remove_rest_api_links'] ) && true === (bool) $this->options['remove_rest_api_links'] ) {
			remove_action( 'wp_head', 'rest_output_link_wp_head' );
		}

		if ( ! empty( $this->options['remove_oembed_links'] ) && true === (bool) $this->options['remove_oembed_links'] ) {
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
			remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		}
	}

	/**
	 * Register the stylesheets for the admin.
	 *
	 * @since    1.0.0
	 */
	public function pingbacks_enqueue_styles() {

		// Only enqueue styles on the "Discussion Settings" page.
		global $pagenow;

		// Bail early if not on the options-discussion.php page.
		if ( 'options-discussion.php' !== $pagenow ) {
			return;
		}

		wp_enqueue_style( 'better-by-default-pingback-style', BETTER_BY_DEFAULT_URL . 'assets/build/css/admin/pingback-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Change Ping Status to Closed
	 *
	 * @return string
	 */
	public function ping_status_callback() {
		return 'closed';
	}

	/**
	 * Disable pingbacks in XML-RPC
	 *
	 * @param array $methods XML-RPC methods.
	 * @return array
	 */
	public function xmlrpc_methods_callback( $methods ) {
		unset( $methods['pingback.ping'] );
		return $methods;
	}

	/**
	 * Disable pingbacks in XML-RPC
	 *
	 * @param string $action XML-RPC action.
	 * @return void
	 */
	public function xmlrpc_call_callback( $action ) {
		if ( 'pingback.ping' === $action ) {
			wp_die( 'Pingbacks are not supported', 'Not Allowed!', array( 'response' => 403 ) );
		}
	}

	/**
	 * Remove 'trackbacks' support from all post types.
	 *
	 * @return void
	 */
	public function remove_trackbacks_support_callback() {
		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'trackbacks' ) ) {
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	}

	/**
	 * Remove trackback rewrite rules.
	 *
	 * @param array $rules Rewrite rules.
	 * @return array
	 */
	public function rewrite_rules_callback( $rules ) {
		foreach ( array_keys( $rules ) as $rule ) {
			if ( preg_match( '/trackback\/\?\$$/i', $rule ) ) {
				unset( $rules[ $rule ] );
			}
		}
		return $rules;
	}
}
