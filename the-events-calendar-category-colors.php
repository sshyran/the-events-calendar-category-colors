<?php
/**
 * Plugin Name:       The Events Calendar Category Colors
 * Plugin URI:        https://github.com/afragen/the-events-calendar-category-colors
 * Description:       This plugin adds event category background coloring to <a href="http://wordpress.org/plugins/the-events-calendar/">The Events Calendar</a> plugin.
 * Version:           5.0.1.2
 * Text Domain:       the-events-calendar-category-colors
 * Author:            Andy Fragen, Barry Hughes
 * Author URI:        http://thefragens.com
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/afragen/the-events-calendar-category-colors
 * Requires PHP:      5.3
 * Requires WP:       3.8
 */

/*
 * Exit if called directly.
 * PHP version check and exit.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( version_compare( '5.3.0', PHP_VERSION, '>=' ) ) {
	echo '<div class="error notice is-dismissible"><p>';
	printf(
		/* translators: 1: minimum PHP version required, 2: Upgrade PHP URL */
		wp_kses_post( __( 'The Events Calendar Category Colors cannot run on PHP versions older than %1$s. <a href="%2$s">Learn about upgrading your PHP.</a>', 'the-events-calendar-category-colors' ) ),
		'5.3.0',
		esc_url( __( 'https://wordpress.org/support/upgrade-php/' ) )
	);
	echo '</p></div>';

	return false;
}

// We'll use PHP 5.3 syntax to get the plugin directory
define( 'TECCC_DIR', __DIR__ );
define( 'TECCC_FILE', __FILE__ );
define( 'TECCC_CLASSES', TECCC_DIR . '/src' );
define( 'TECCC_INCLUDES', TECCC_DIR . '/includes' );
define( 'TECCC_VIEWS', TECCC_DIR . '/views' );
define( 'TECCC_RESOURCES', plugin_dir_url( __FILE__ ) . 'resources' );
define( 'TECCC_LANG', basename( __DIR__ ) . '/languages' );

/**
 * Initialization of plugin.
 */
function teccc_init() {
	global $teccc;

	// Back compat classes
	$compatibility = array(
		'Tribe__Events__Main'      => TECCC_CLASSES . '/Back_Compat/Events.php',
		'Tribe__Settings_Tab'      => TECCC_CLASSES . '/Back_Compat/Settings_Tab.php',
		'Tribe__Events__Pro__Main' => TECCC_CLASSES . '/Back_Compat/Events_Pro.php',
	);

	// Plugin namespace root
	$root = array(
		'Fragen\Category_Colors' => TECCC_CLASSES . '/Category_Colors',
	);

	// Autoloading
	require_once TECCC_CLASSES . '/Autoloader.php';
	$loader = 'Fragen\\Autoloader';
	new $loader( $root, $compatibility );

	/*
	 * The Events Calendar must be loaded.
	 */
	if ( ! class_exists( 'Tribe__Events__Main' ) ) {
		return;
	}

	// Set-up Action and Filter Hooks
	register_activation_hook( __FILE__, array( 'Fragen\\Category_Colors\\Main', 'add_defaults' ) );

	// Launch
	$launch_method = array( 'Fragen\\Category_Colors\\Main', 'instance' );
	$teccc         = call_user_func( $launch_method );
}

add_action( 'plugins_loaded', 'teccc_init', 15 );
