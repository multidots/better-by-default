<?php
/**
 * Autoloader file for theme.
 *
 * @package better-by-default
 */

namespace BetterByDefault\Inc\Helpers;

/**
 * Auto loader function.
 *
 * @param string $resource_name Source namespace.
 *
 * @return void
 */
function autoloader( $resource_name = '' ) {
	$resource_path  = false;
	$namespace_root = 'BetterByDefault\\';
	$resource_name  = trim( $resource_name, '\\' );

	if ( empty( $resource_name ) || strpos( $resource_name, '\\' ) === false || strpos( $resource_name, $namespace_root ) !== 0 ) {
		// Not our namespace, bail out.
		return;
	}

	// Remove our root namespace.
	$resource_name = str_replace( $namespace_root, '', $resource_name );

	$path = explode(
		'\\',
		str_replace( '_', '-', strtolower( $resource_name ) )
	);

	/**
	 * Time to determine which type of resource path it is,
	 * so that we can deduce the correct file path for it.
	 */
	if ( empty( $path[0] ) || empty( $path[1] ) ) {
		return;
	}

	$directory = '';
	$file_name = '';

	if ( 'inc' === $path[0] ) {

		switch ( $path[1] ) {
			case 'traits':
				$directory = 'traits';
				$file_name = sprintf( 'trait-%s', trim( strtolower( $path[2] ) ) );
				break;

			case 'classes':
				$directory = 'classes';
				$file_name = sprintf( 'class-%s', trim( strtolower( $path[1] ) ) );
				break;

			default:
				if ( ! empty( $path[2] ) ) {
					$directory = sprintf( 'classes/%s', $path[1] );
					$file_name = sprintf( 'class-%s', trim( strtolower( $path[2] ) ) );
					break;
				} else {
					$directory = 'classes';
					$file_name = sprintf( 'class-%s', trim( strtolower( $path[1] ) ) );
					break;
				}
		}

		$resource_path = sprintf( '%s/includes/%s/%s.php', untrailingslashit( BETTER_BY_DEFAULT_DIR ), $directory, $file_name );

	}

	/**
	 * Load blocks class file from assets directory.
	 */
	if ( 'blocks' === $path[0] && ! empty( $path[1] ) ) {
		$block_dir_path = BETTER_BY_DEFAULT_DIR . 'assets/src/js/blocks/' . $path[1];
		$resource_path  = sprintf( '%s/block.php', untrailingslashit( $block_dir_path ) );
	}

	/**
	 * If $is_valid_file has 0 means valid path or 2 means the file path contains a Windows drive path.
	 */
	$is_valid_file = validate_file( $resource_path );

	if ( ! empty( $resource_path ) && file_exists( $resource_path ) && ( 0 === $is_valid_file || 2 === $is_valid_file ) ) {
		// We already making sure that file is exists and valid.
		require_once( $resource_path ); // phpcs:ignore
	}
}

spl_autoload_register( '\BetterByDefault\Inc\Helpers\autoloader' );
