<?php
/**
 * The post-tags-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Simplify;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Post_Tags class file.
 */
class Post_Tags {

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

		$this->setup_post_tags_hooks();
	}
	/**
	 * Function is used to define post-tags hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_post_tags_hooks() {
		add_action( 'init', array( $this, 'remove_post_tags' ) );
	}

	/**
	 * Function is used to remove post tags.
	 *
	 * @since   1.0.0
	 */
	public function remove_post_tags() {
		// Remove tags taxonomy from posts.
		unregister_taxonomy_for_object_type( 'post_tag', 'post' );
	}
}
