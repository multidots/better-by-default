<?php
/**
 * Dynamic Blocks.
 *
 * @package better-by-default
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Class Blocks
 */
class Blocks {
	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		// load class.
		$this->setup_hooks();
	}

	/**
	 * To register action/filter.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function setup_hooks() {

		/**
		 * Load blocks classes.
		 */
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_filter( 'block_categories_all', array( $this, 'better_by_default_custom_block_category' ) );
	}

	/**
	 * Automatically registers all blocks that are located within the includes/blocks directory
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register_blocks() {
		// Register all the blocks in the theme.
		if ( file_exists( BETTER_BY_DEFAULT_SRC_BLOCK_DIR_PATH ) ) {
			$block_json_files = glob( BETTER_BY_DEFAULT_SRC_BLOCK_DIR_PATH . '/*/block.json' );

			// auto register all blocks that were found.
			foreach ( $block_json_files as $filename ) {
				// Retrieve block meta data.
				$metadata = wp_json_file_decode( $filename, array( 'associative' => true ) );
				if ( empty( $metadata ) || empty( $metadata['name'] ) ) {
					continue;
				}

				$block_name = $metadata['name'];
				$class_name = $this->block_class_from_string( $block_name );

				if ( $class_name && class_exists( $class_name, true ) ) {
					$block = $class_name::get_instance();
					$block->init();
				}
			}
		}
	}

	/**
	 * Take a string with a block name, return the class name.
	 *
	 * @param string $str string to generate class name from.
	 *
	 * @return string|null class name with namespace
	 */
	public static function block_class_from_string( string $str ): ?string {
		// Force lowercase. Normalize.
		$string = strtolower( $str );

		// Default namespace for blocks.
		$namespace = 'BetterByDefault\Blocks\\';

		// Remove namespace from block name.
		if ( false !== strpos( $string, 'better-by-default/' ) ) {
			$string = str_replace( 'better-by-default/', '', $string );
		}

		// Blow up names on the hyphens.
		$split = explode( '-', $string );

		// Upper Case Words when we join things back together.
		// implode is used on the variable that is exploded above.
		return $namespace . implode( '_', array_map( 'ucfirst', (array) $split ) );
	}

	/**
	 * Register Custom Block Category
	 *
	 * @param array $categories return categories array.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function better_by_default_custom_block_category( array $categories ): array {
		return array_merge(
			array(
				array(
					'slug'  => 'better-by-default',
					'title' => __( 'Better By Default Block', 'better-by-default' ),
					'icon'  => 'welcome-add-page',
				),
			),
			$categories
		);
	}
}
