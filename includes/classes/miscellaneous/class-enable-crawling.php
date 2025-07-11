<?php
/**
 * The enable-crawling-specific functionality of the plugin.
 *
 * @package    better-by-default
 */

namespace BetterByDefault\Inc\Miscellaneous;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Enable_Crawling class file.
 */
class Enable_Crawling {

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
		// Load the plugin options.
		$this->options = get_option( BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS, array() );

		$this->setup_enable_crawling_hooks();
	}

	/**
	 * Define enable-crawling hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_enable_crawling_hooks() {

		add_action( 'init', array( $this, 'enable_search_engine_indexing_when_crawling_enabled' ) );
	}

	/**
	 * Ensure the "Allow search engines to index this site" checkbox is checked when disable_crawling setting is false.
	 *
	 * @return void
	 */
	public function enable_search_engine_indexing_when_crawling_enabled() {
		$options          = $this->options;
		$disable_crawling = isset( $options['disable_crawling'] ) && 'true' === $options['disable_crawling'] ? false : true;
		// If disable_crawling is not enabled.
		if ( $disable_crawling ) {
			$current_blog_public = get_option( 'blog_public' );

			// If blog_public is already 0 (unchecked), update it to 1 (checked).
			if ( '1' !== $current_blog_public ) {
				update_option( 'blog_public', '1' );
			}
		}
	}
}
