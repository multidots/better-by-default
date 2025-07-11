<?php
/**
 * The public-page-preview-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Miscellaneous;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Public_Page_Preview class file.
 */
class Public_Page_Preview {

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
		$this->setup_public_page_preview_hooks();
	}
	/**
	 * Function is used to define public-page-preview hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_public_page_preview_hooks() {

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'ppp_enqueue_admin_script' ) );
			add_action( 'wp_ajax_public-page-preview', array( $this, 'ppp_update_public_preview_status' ) );
		} else {
			add_action( 'pre_get_posts', array( $this, 'ppp_show_public_preview' ) );
		}
	}

	/**
	 * Register public preview hook to display revision.
	 *
	 * @param object $query The WP_Query object.
	 */
	public function ppp_show_public_preview( $query ) {
		$ppp = filter_input( INPUT_GET, 'ppp', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( $query->is_main_query() && $query->is_preview() && $query->is_singular() && ( isset( $ppp ) && ! empty( $ppp ) ) ) {
			if ( ! headers_sent() ) {
				nocache_headers();
				header( 'X-Robots-Tag: noindex' );
			}
			add_filter( 'wp_robots', 'wp_robots_no_robots' );
			add_filter( 'posts_results', array( $this, 'ppp_display_post_revision' ), 10, 2 );
		}
	}

	/**
	 * Display post revision.
	 *
	 * @param object $posts the post object.
	 *
	 * @return object
	 */
	public function ppp_display_post_revision( $posts ) {

		remove_filter( 'posts_results', array( $this, 'ppp_display_post_revision' ), 10 );

		if ( empty( $posts ) ) {
			return $posts;
		}

		$preview_enabled = $this->ppp_preview_enabled( $posts[0] );

		if ( $preview_enabled ) {

			if ( 'publish' !== $posts[0]->post_status ) {
				$query_result = $posts;
			} else {

				$args         = array(
					'post_parent'            => $posts[0]->ID,
					'post_status'            => 'inherit',
					'post_type'              => 'revision',
					'posts_per_page'         => 1,
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
				);
				$query_result = new \WP_Query( $args );
			}

			if ( isset( $query_result->posts[0] ) ) {
				$posts[0]->post_content = $query_result->posts[0]->post_content;
			}

			if ( 'publish' !== $posts[0]->post_status ) {
				$posts[0]->post_status = 'publish';
			}
		}
		return $posts;
	}

	/**
	 * Enqueue javascript for post editor screen.
	 *
	 * @param string $hook_suffix current page.
	 */
	public function ppp_enqueue_admin_script( $hook_suffix ) {
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		if ( get_current_screen()->is_block_editor() ) {
			$script_assets_path = BETTER_BY_DEFAULT_DIR . 'assets/build/js/public-page-preview/index.asset.php';
			$script_assets      = file_exists( $script_assets_path ) ?
				require $script_assets_path : //phpcs:ignore
				array(
					'dependencies' => array(),
					'version'      => '',
				);

			wp_enqueue_script(
				'public-page-preview-js',
				BETTER_BY_DEFAULT_URL . 'assets/build/js/public-page-preview/index.js',
				$script_assets['dependencies'],
				$script_assets['version'],
				true
			);

			$current_post    = get_post();
			$preview_enabled = $this->ppp_preview_enabled( $current_post );
			$preview_link    = $this->ppp_get_preview_url( $current_post );
			wp_localize_script(
				'public-page-preview-js',
				'publicPagePreviewData',
				array(
					'previewEnabled' => $preview_enabled,
					'previewUrl'     => $preview_link,
					'nonce'          => wp_create_nonce( 'public-page-preview_' . $current_post->ID ),
				)
			);
		}
	}

	/**
	 * Check preview enabled in the post.
	 *
	 * @param object $post the post object.
	 *
	 * @return boolean
	 */
	private function ppp_preview_enabled( $post ) {

		$preview_enabled = get_post_meta( $post->ID, 'ppp_enabled_preview', true );

		return $preview_enabled ? true : false;
	}

	/**
	 * Update the public page view status of the post.
	 */
	public function ppp_update_public_preview_status() {

		$preview_post_id = filter_input( INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT );
		$checked         = filter_input( INPUT_POST, 'checked', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! isset( $preview_post_id ) || ! isset( $checked ) ) {
			wp_send_json_error( 'incomplete_data' );
		}

		check_ajax_referer( 'public-page-preview_' . $preview_post_id );

		if ( ! current_user_can( 'edit_post', $preview_post_id ) ) {
			wp_send_json_error( 'cannot_edit' );
		}

		$allowed_post_status = $this->ppp_allowed_post_status();

		$post = get_post( $preview_post_id );

		if ( ! in_array( $post->post_status, $allowed_post_status, true ) ) {
			wp_send_json_error( 'invalid_post_status' );
		}

		if ( 'true' === $checked ) {
			update_post_meta( $post->ID, 'ppp_enabled_preview', true );
		} elseif ( 'false' === $checked ) {
			delete_post_meta( $post->ID, 'ppp_enabled_preview' );
		} else {
			wp_send_json_error( 'unknown_status' );
		}

		$data = null;
		if ( 'true' === $checked ) {
			$preview_url = $this->ppp_get_preview_url( $post );
			$data        = array( 'preview_url' => $preview_url );
		}

		wp_send_json_success( $data );
	}

	/**
	 * Generate the preview link.
	 *
	 * @param object $post the post object.
	 *
	 * @return string
	 */
	private function ppp_get_preview_url( $post ) {

		$post_url = 'publish' === $post->post_status ? get_the_permalink( $post ) : home_url( '/' );

		if ( 'page' === $post->post_type ) {
			$key  = 'publish' === $post->post_status ? 'preview_id' : 'page_id';
			$args = array(
				$key => $post->ID,
			);
		} elseif ( 'post' === $post->post_type ) {
			$key  = 'publish' === $post->post_status ? 'preview_id' : 'p';
			$args = array(
				$key => $post->ID,
			);
		} elseif ( 'publish' === $post->post_status ) {
				$args = array(
					'preview_id' => $post->ID,
				);
		} else {
			$args = array(
				'p'         => $post->ID,
				'post_type' => $post->post_type,
			);
		}
		$args['preview'] = true;
		$args['ppp']     = uniqid();
		$link            = add_query_arg( $args, $post_url );

		return $link;
	}

	/**
	 * Allowed post status for public view.
	 */
	private function ppp_allowed_post_status() {

		return array(
			'publish',
			'draft',
			'future',
		);
	}
}
