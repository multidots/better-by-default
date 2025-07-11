<?php
/**
 * The lazy-load-embeds functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the lazy-load-embeds stylesheet and JavaScript.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Performance;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Main class file.
 */
class Lazy_Load_Embeds {

	use Singleton;

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
		$this->setup_lazy_load_hooks();
	}

	/**
	 * Setup lazy load hooks for enqueueing scripts and filtering content.
	 *
	 * @since   1.0.0
	 */
	public function setup_lazy_load_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_lazy_load_scripts' ) );
		add_filter( 'the_content', array( $this, 'conditionally_add_lazy_load' ) );
	}

	/**
	 * Enqueue the JavaScript for lazy loading embeds on the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_lazy_load_scripts() {
		wp_enqueue_script(
			'better-by-default-lazy-load-js',
			BETTER_BY_DEFAULT_URL . 'assets/build/js/frontend/lazy-load-embeds.js',
			array( 'jquery' ),
			$this->version,
			true
		);
	}

	/**
	 * Conditionally add lazy loading to YouTube and/or standard iframes based on options.
	 *
	 * @param string $content The post content.
	 * @return string Modified post content with lazy-loaded iframes.
	 *
	 * @since   1.0.0
	 */
	public function conditionally_add_lazy_load( $content ) {
		$lazy_load_youtube = isset( $this->options['lazy_load_youtube'] ) && 'true' === $this->options['lazy_load_youtube'] ? true : false;
		$lazy_load_iframe  = isset( $this->options['lazy_load_iframe'] ) && 'true' === $this->options['lazy_load_iframe'] ? true : false;

		// Check if lazy loading should be applied to either or both.
		if ( $lazy_load_youtube ) {
			$content = $this->add_lazy_load_to_youtube_iframes( $content );
		}

		if ( $lazy_load_iframe ) {
			$content = $this->add_lazy_load_to_iframes( $content );
		}

		return $content;
	}

	/**
	 * Modify post content to add lazy loading to YouTube iframes.
	 *
	 * @param string $content The post content.
	 * @return string Modified post content with lazy-loaded YouTube iframes.
	 *
	 * @since   1.0.0
	 */
	public function add_lazy_load_to_youtube_iframes( $content ) {
		// Regex to match YouTube iframes.
		$youtube_pattern     = '/<iframe(.*?)src=["\'](https?:\/\/(www\.)?youtube\.com\/embed\/[^\s"\'<>]*)["\'](.*?)><\/iframe>/i';
		$youtube_replacement = '<iframe$1 data-src="$2"$4 class="lazy" loading="lazy" allowfullscreen></iframe>';

		// Replace YouTube iframes.
		return preg_replace( $youtube_pattern, $youtube_replacement, $content );
	}

	/**
	 * Modify post content to add lazy loading to standard iframes.
	 *
	 * @param string $content The post content.
	 * @return string Modified post content with lazy-loaded standard iframes.
	 *
	 * @since   1.0.0
	 */
	public function add_lazy_load_to_iframes( $content ) {
		// Regex to match standard iframes excluding YouTube iframes.
		$iframe_pattern     = '/<iframe([^>]*?)src=["\'](https?:\/\/(?!www\.youtube\.com\/embed\/)[^\s"\'<>]*)["\']([^>]*)><\/iframe>/i';
		$iframe_replacement = '<iframe$1data-src="$2"$3 class="lazy" loading="lazy"></iframe>';

		// Replace non-YouTube iframes.
		return preg_replace( $iframe_pattern, $iframe_replacement, $content );
	}
}
