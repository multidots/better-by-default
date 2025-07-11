<?php
/**
 * The disable-crawling-specific functionality of the plugin.
 *
 * @package    better-by-default
 */

namespace BetterByDefault\Inc\Miscellaneous;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Disable_Crawling class file.
 */
class Disable_Crawling {

	use Singleton;

	/**
	 * The setting options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $options    The setting options of this plugin.
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Load the plugin options.
		$this->options = get_option( BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS, array() );

		$this->setup_disable_crawling_hooks();
	}

	/**
	 * Define disable-crawling hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_disable_crawling_hooks() {
		// Force noindex, nofollow via the wp_robots hook.
		add_filter( 'wp_headers', array( $this, 'disable_seo_set_headers' ), 999 );

		add_action( 'init', array( $this, 'ensure_search_engine_indexing_is_disabled_when_crawling_disabled' ) );
	}

	/**
	 * Set headers to prevent crawling.
	 *
	 * @param array $headers The headers.
	 * @return array
	 */
	public function disable_seo_set_headers( $headers ) {
		if ( ! isset( $headers['X-Robots-Tag'] ) ) {
			$headers['X-Robots-Tag'] = 'noindex, nofollow';
		}
		return $headers;
	}

	/**
	 * Ensure the "Allow search engines to index this site" checkbox is unchecked when disable_crawling setting is true.
	 *
	 * @return void
	 */
	public function ensure_search_engine_indexing_is_disabled_when_crawling_disabled() {

		$options = $this->options;

		// If disable_crawling is enabled and blog_public is not already 0.
		if ( isset( $options['disable_crawling'] ) && true === (bool) $options['disable_crawling'] ) {
			$current_blog_public = get_option( 'blog_public' );

			// If blog_public is not already 0, update it.
			if ( '0' !== $current_blog_public ) {
				update_option( 'blog_public', '0' );
			}
		}
	}
}
