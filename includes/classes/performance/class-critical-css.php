<?php
/**
 * The critical-css functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Performance;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Main class file.
 */
class Critical_CSS {

	use Singleton;

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
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->options = get_option( BETTER_BY_DEFAULT_PERFORMANCE_OPTIONS, array() );
		$this->setup_hooks();
	}

	/**
	 * Setup hooks for critical CSS functionality.
	 *
	 * @since 1.0.0
	 */
	private function setup_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_critical_css' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_critical_css_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_critical_css_meta' ) );
	}

	/**
	 * Enqueue critical CSS if available.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_critical_css() {
		global $post;

		if ( ! isset( $post->ID ) ) {
			return;
		}

		$this->enqueue_common_critical_css();
		$this->enqueue_page_specific_critical_css( $post->ID );
	}

	/**
	 * Enqueue common critical CSS.
	 *
	 * @since 1.0.0
	 */
	private function enqueue_common_critical_css() {
		$common_critical_css = isset( $this->options['common_critical_css'] ) && ! empty( $this->options['common_critical_css'] ) ? $this->options['common_critical_css'] : ''; //phpcs:ignore

		if ( ! empty( $common_critical_css ) ) {
			wp_register_style( 'better-by-default-common-inline-css', false, array(), $this->version, 'all' );
			wp_add_inline_style( 'better-by-default-common-inline-css', $common_critical_css );
			wp_enqueue_style( 'better-by-default-common-inline-css' );
		}
	}

	/**
	 * Enqueue page-specific critical CSS if applicable.
	 *
	 * @param int $post_id The ID of the current post.
	 * @since 1.0.0
	 */
	private function enqueue_page_specific_critical_css( $post_id ) {
		$current_post_type = get_post_type( $post_id );

		if ( isset( $this->options['critical_css_for'][ $current_post_type ] ) && 'true' === $this->options['critical_css_for'][ $current_post_type ] ) {

			$page_critical_css = get_post_meta( $post_id, 'better_by_default_critical_css', true );

			if ( ! empty( $page_critical_css ) ) {
				wp_register_style( 'better-by-default-inline-css', false, array(), $this->version, 'all' );
				wp_add_inline_style( 'better-by-default-inline-css', $page_critical_css );
				wp_enqueue_style( 'better-by-default-inline-css' );
			}
		}
	}

	/**
	 * Add meta box for critical CSS for selected post types.
	 *
	 * @since 1.0.0
	 */
	public function add_critical_css_meta_boxes() {
		if ( isset( $this->options['critical_css_for'] ) && is_array( $this->options['critical_css_for'] ) ) {
			foreach ( $this->options['critical_css_for'] as $post_type => $enabled ) {
				if ( 'true' === $enabled ) {

					add_meta_box(
						'critical_css_meta_box',
						__( 'Critical CSS', 'better-by-default' ),
						array( $this, 'render_critical_css_meta_box' ),
						$post_type,
						'normal',
						'default'
					);
				}
			}
		}
	}

	/**
	 * Render the meta box content.
	 *
	 * @param WP_Post $post The post object.
	 * @since 1.0.0
	 */
	public function render_critical_css_meta_box( $post ) {
		wp_nonce_field( 'critical_css_meta_box_nonce', 'critical_css_meta_box_nonce' );

		$critical_css = get_post_meta( $post->ID, 'better_by_default_critical_css', true );
		echo '<textarea id="critical_css" name="critical_css" rows="6" placeholder="Enter critical CSS." style="width:100%;">' . esc_textarea( $critical_css ) . '</textarea>';
	}

	/**
	 * Save the meta box data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 * @since 1.0.0
	 */
	public function save_critical_css_meta( $post_id ) {
		if ( ! isset( $_POST['critical_css_meta_box_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['critical_css_meta_box_nonce'] ) ), 'critical_css_meta_box_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$critical_css = isset( $_POST['critical_css'] ) ? $_POST['critical_css'] : ''; //phpcs:ignore
		update_post_meta( $post_id, 'better_by_default_critical_css', $critical_css );
	}
}
