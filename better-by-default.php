<?php
/**
 * The plugin bootstrap file 
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.multidots.com/
 * @since             1.0
 * @package           better-by-default
 *
 * @wordpress-plugin
 * Plugin Name:       Better By Default
 * Description:       Enhance your WordPress site with the Better By Default Plugin, designed for simplicity, security and performance. Ideal for users seeking a clean and efficient experience, Better By Default focuses on delivering core functionalities while maintaining a minimalist approach.
 * Version:           1.3
 * Requires PHP:      7.2.5
 * Author:            Multidots
 * Author URI:        https://www.multidots.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       better-by-default
 * Domain Path:       /languages
 */

namespace BetterByDefault;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BETTER_BY_DEFAULT_VERSION', '1.3' );
define( 'BETTER_BY_DEFAULT_URL', plugin_dir_url( __FILE__ ) );
define( 'BETTER_BY_DEFAULT_DIR', plugin_dir_path( __FILE__ ) );
define( 'BETTER_BY_DEFAULT_BASEPATH', plugin_basename( __FILE__ ) );
define( 'BETTER_BY_DEFAULT_SRC_BLOCK_DIR_PATH', untrailingslashit( BETTER_BY_DEFAULT_DIR . 'assets/build/js/blocks' ) );

if ( ! defined( 'BETTER_BY_DEFAULT_PATH' ) ) {
	define( 'BETTER_BY_DEFAULT_PATH', __DIR__ );
}

if ( ! defined( 'BETTER_BY_DEFAULT_EXTRA_OPTIONS' ) ) {
	define( 'BETTER_BY_DEFAULT_EXTRA_OPTIONS', 'better_by_default_extra_option' );
}

if ( ! defined( 'BETTER_BY_DEFAULT_PROTECT_OPTIONS' ) ) {
	define( 'BETTER_BY_DEFAULT_PROTECT_OPTIONS', 'better_by_default_protect_option' );
}

if ( ! defined( 'BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS' ) ) {
	define( 'BETTER_BY_DEFAULT_MISCELLANEOUS_OPTIONS', 'better_by_default_miscellaneous_option' );
}

if ( ! defined( 'BETTER_BY_DEFAULT_PERFORMANCE_OPTIONS' ) ) {
	define( 'BETTER_BY_DEFAULT_PERFORMANCE_OPTIONS', 'better_by_default_performance_option' );
}

if ( ! defined( 'BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS' ) ) {
	define( 'BETTER_BY_DEFAULT_SIMPLIFY_OPTIONS', 'better_by_default_simplify_option' );
}

if ( ! defined( 'BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS' ) ) {
	define( 'BETTER_BY_DEFAULT_PERSONALIZE_OPTIONS', 'better_by_default_personalize_option' );
}


// Load the autoloader.
require_once plugin_dir_path( __FILE__ ) . '/includes/helpers/autoloader.php';

register_activation_hook( __FILE__, array( \BetterByDefault\Inc\Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( \BetterByDefault\Inc\Deactivator::class, 'deactivate' ) );

/**
 * Begins execution of the plugin.
 *
 * @since    1.0
 */
function run_md_scaffold() {
	new \BetterByDefault\Inc\Better_By_Default();
}
run_md_scaffold();
