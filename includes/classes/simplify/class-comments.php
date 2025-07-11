<?php
/**
 * The comments-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Simplify;

use BetterByDefault\Inc\Traits\Singleton;
use BetterByDefault\Inc\Utils\Admin_Menu_Utils;

/**
 * Comments class file.
 */
class Comments {

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
		$this->setup_comments_hooks();
	}
	/**
	 * Function is used to define comments hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_comments_hooks() {

		// Disable support for comments and trackbacks in post types.
		add_action( 'admin_init', array( $this, 'disable_comments_post_types_support' ) );

		// Hide existing comments.
		add_filter( 'comments_array', array( $this, 'disable_comments_hide_existing_comments' ), PHP_INT_MAX );

		// Remove comments page from admin menu.
		add_action( 'admin_menu', array( $this, 'disable_comments_admin_menu' ) );

		// Redirect any user trying to access comments page.
		add_action( 'admin_init', array( $this, 'disable_comments_admin_menu_redirect' ) );

		// Remove comments metabox from dashboard.
		add_action( 'wp_dashboard_setup', array( $this, 'disable_comments_dashboard' ), 999 );

		// Remove comments links from admin bar.
		add_action( 'init', array( $this, 'disable_comments_admin_bar' ) );

		// Disable comments related blocks.
		add_action( 'enqueue_block_editor_assets', array( $this, 'disable_gutenberg_comment_blocks' ) );
		
		add_action( 'render_block', array( $this, 'disable_gutenberg_comment_blocks_render' ), 10, 2 );

		// Disable comments on the front-end.
		add_action( 'template_redirect', array( $this, 'disable_comments_on_frontend' ) );

		// Force comment status to closed.
		add_filter( 'pre_option_default_comment_status', array( $this, 'comment_status_callback' ) );

		add_action( 'admin_head', array( $this, 'hide_recent_comments_from_activity_widget' ), PHP_INT_MAX );

		// Remove comments menu items and redirect direct access.
		add_action( 'admin_menu', array( $this, 'remove_comment_menu_callback' ), PHP_INT_MAX );

		add_action( 'admin_enqueue_scripts', array( $this, 'hide_comments_enqueue_styles' ) );
	}

	/**
	 * Register the stylesheets for the admin.
	 *
	 * @since    1.0.0
	 */
	public function hide_comments_enqueue_styles() {

		// Only enqueue styles on the "Discussion Settings" page.
		global $pagenow;

		// Bail early if not on the options-discussion.php page.
		if ( 'options-discussion.php' !== $pagenow ) {
			return;
		}

		wp_enqueue_style( 'better-by-default-hide-comments-style', BETTER_BY_DEFAULT_URL . 'assets/build/css/admin/hide-comments-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Disable support for comments and trackbacks in post types.
	 *
	 * @return void
	 */
	public function disable_comments_post_types_support() {
		$post_types = get_post_types();
		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				if ( post_type_supports( $post_type, 'comments' ) ) {
					remove_post_type_support( $post_type, 'comments' );
					remove_post_type_support( $post_type, 'trackbacks' );
				}
			}
		}
	}

	/**
	 * Hide existing comments.
	 *
	 * @return array
	 */
	public function disable_comments_hide_existing_comments() {
		return array();
	}

	/**
	 * Remove comments page from admin menu.
	 *
	 * @return void
	 */
	public function disable_comments_admin_menu() {
		remove_menu_page( 'edit-comments.php' );
	}

	/**
	 * Redirect any user trying to access comments page.
	 *
	 * @return void
	 */
	public function disable_comments_admin_menu_redirect() {
		global $pagenow;
		if ( 'edit-comments.php' === $pagenow ) {
			wp_safe_redirect( admin_url() );
			exit;
		}
	}

	/**
	 * Remove comments metabox from dashboard.
	 *
	 * @return void
	 */
	public function disable_comments_dashboard() {
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	}

	/**
	 * Remove comments links from admin bar.
	 *
	 * @return void
	 */
	public function disable_comments_admin_bar() {
		if ( is_admin_bar_showing() ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
		}
	}

	/**
	 * Disable comments on the front-end.
	 *
	 * @return void
	 */
	public function disable_gutenberg_comment_blocks() {
		// Remove the comment-related blocks.
		wp_register_script( 'disable-comment-blocks', '', array(), '1.0.0', true );
		wp_add_inline_script(
			'disable-comment-blocks',
			'
			wp.domReady(function() {
				wp.blocks.unregisterBlockType("core/post-comments");
				wp.blocks.unregisterBlockType("core/post-comments-form");
				wp.blocks.unregisterBlockType("core/comments-title");
				wp.blocks.unregisterBlockType("core/comment-content");
				wp.blocks.unregisterBlockType("core/comment-date");
				wp.blocks.unregisterBlockType("core/comment-author-name");
				wp.blocks.unregisterBlockType("core/comments");
				wp.blocks.unregisterBlockType("core/latest-comments");
			});
		'
		);
		wp_enqueue_script( 'disable-comment-blocks' );
	}

	/**
	 * Disable comment blocks render.
	 *
	 * @param [type] $block_content
	 * @param [type] $block
	 * @return void
	 */
	public function disable_gutenberg_comment_blocks_render( $block_content, $block ) {

		$disabled_blocks = array(
			'core/post-comments',
			'core/post-comments-form',
			'core/comments-title',
			'core/comment-content',
			'core/comment-date',
			'core/comment-author-name',
			'core/comments',
			'core/latest-comments',
		);
		if ( in_array( $block['blockName'], $disabled_blocks, true ) ) {
			return ''; // Render nothing
		}
		return $block_content;	

	}

	/**
	 * Comment Status to Closed
	 *
	 * @param [type] $default_comment_status default comment status.
	 * @return string
	 */
	public function comment_status_callback( $default_comment_status ) {
			// Check if we are in the admin area and the user is on the Discussion settings page.
			$server_uri = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL );
		if ( is_admin() && isset( $server_uri ) && strpos( $server_uri, 'options-discussion.php' ) !== false ) {
			// Return the default comment status, don't force closed on the admin discussion settings page.
			return $default_comment_status;
		}
			return 'closed';
	}


	/**
	 * Disable comments on the front-end.
	 *
	 * @return void
	 */
	public function disable_comments_on_frontend() {

		// Disable the comments template.
		add_filter(
			'comments_template',
			function () {
				return false;
			},
			20
		);

		// Disable comments on posts.
		add_filter( 'comments_open', '__return_false', 20, 2 );
		add_filter( 'pings_open', '__return_false', 20, 2 );

		// Remove comments from the front-end.
		add_filter( 'get_comments_number', '__return_zero', 20, 2 );

		// Remove the recent comments widget.
		unregister_widget( 'WP_Widget_Recent_Comments' );
	}

	/**
	 * Hide recent comments from activity widget.
	 *
	 * @return void
	 */
	public function hide_recent_comments_from_activity_widget() {
		wp_enqueue_style( 'hide_recent_comments_from_activity_widget_style', BETTER_BY_DEFAULT_URL . 'assets/build/admin.css', array(), $this->version, 'all' );

		$hide_recent_comments_css = "
		#dashboard_activity #latest-comments { display: none !important; }";
		wp_add_inline_style( 'hide_recent_comments_from_activity_widget_style', $hide_recent_comments_css );	
	}
	/**
	 * Remove Comment menu.
	 *
	 * @return void
	 */
	public function remove_comment_menu_callback() {

		$admin_menu = Admin_Menu_Utils::instance();
		$admin_menu->remove_menu_page( 'edit-comments.php' );

		// If pingbacks are also disabled, remove the entire Discussion settings page.
		if ( has_filter( 'pings_open', '__return_false' ) ) {
			$admin_menu->remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		}
	}
}
