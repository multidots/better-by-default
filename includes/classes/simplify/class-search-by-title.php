<?php
/**
 * The search-by-title-specific functionality of the plugin.
 *
 * @package    better-by-default
 * @author     Multidots <info@multidots.com>
 */

namespace BetterByDefault\Inc\Simplify;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Search_By_Title class file.
 */
class Search_By_Title {

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

		$this->setup_search_by_title_hooks();
	}

	/**
	 * Function is used to define setup_search_by_title hooks.
	 *
	 * @since   1.0.0
	 */
	public function setup_search_by_title_hooks() {

		add_action( 'restrict_manage_posts', array( $this, 'better_by_default_add_custom_sites_filter' ) );
		add_filter( 'parse_query', array( $this, 'better_by_default_add_custom_filter_query' ) );
		add_filter( 'posts_where', array( $this, 'better_by_default_extend_wp_query_where' ), 10, 2 );
	}

	/**
	 * Custom filter option for site and author.
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_add_custom_sites_filter() {
		global $pagenow;

		if ( is_admin() && 'edit.php' === $pagenow ) {
			$title_search = filter_input( INPUT_GET, 'title_search', FILTER_SANITIZE_SPECIAL_CHARS );
			$title_search = isset( $title_search ) ? $title_search : '';
			?>	
			<input type="search" placeholder="Search Title" value="<?php echo esc_attr( $title_search ); ?>" name="title_search">
			<?php
		}   }

	/**
	 * Modify query for additional filters site and author.
	 *
	 * @param object $query WP_Query object.
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_add_custom_filter_query( $query ) {
		global $pagenow;

		$title_search = filter_input( INPUT_GET, 'title_search', FILTER_SANITIZE_SPECIAL_CHARS );
		$title_search = isset( $title_search ) ? $title_search : '';

		if ( is_admin() && 'edit.php' === $pagenow && isset( $title_search ) && '' !== $title_search ) {

			$query->query['extend_where'] = "(post_title like '%" . $title_search . "%')";

		}
		return $query;
	}

	/**
	 * Add search only by title query.
	 *
	 * @param string $where Query where clause.
	 * @param object $wp_query WP_Query object.
	 *
	 * @since 1.0.0
	 */
	public function better_by_default_extend_wp_query_where( $where, $wp_query ) {

		if ( isset( $wp_query->query['extend_where'] ) && ! empty( $wp_query->query['extend_where'] ) ) {
			if ( $extend_where = $wp_query->query['extend_where'] ) {	//phpcs:ignore
				$where .= ' AND ' . $extend_where;
			}
		}

		return $where;
	}
}
