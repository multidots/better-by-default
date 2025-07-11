<?php
/**
 * Abstract class for Block Registration
 *
 * @package better-by-default
 */

namespace BetterByDefault\Inc;

use BetterByDefault\Inc\Traits\Singleton;

/**
 * Base Block class for all of our blocks.
 */
abstract class Block_Base {
	use Singleton;

	/**
	 * Block arguments.
	 *
	 * @var array args passed to block.
	 */
	protected $block_args = array();

	/**
	 * Block name.
	 *
	 * @var name of the block.
	 */
	protected $block;

	/**
	 * Register Block. Called on `init`.
	 */
	final public function init(): void {
		$name            = "better-by-default/{$this->block}";
		$render_callback = 'render_callback';

		if ( method_exists( $this, $render_callback ) ) {
			$this->block_args['render_callback'] = array( $this, $render_callback );
		}

		// Set default, assume no block registration to this point.
		$has_block = false;

		// Check the register and determine if we have a block already by name.
		if ( class_exists( '\WP_Block_Type_Registry', false ) ) {
			$registry  = \WP_Block_Type_Registry::get_instance();
			$has_block = $registry->is_registered( $name );
		}

		$block_directory = BETTER_BY_DEFAULT_SRC_BLOCK_DIR_PATH . '/' . $this->block;

		// If this is a custom block, and it's not yet registered.
		// Register.
		if ( ! $has_block ) {
			register_block_type(
				$block_directory,
				$this->block_args
			);
		}
	}
}
