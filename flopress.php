<?php

/**
 * Flopress bootstrap file
 *
 * @link              https://flopress.io
 * @since             1.0.0
 * @package           Flopress
 *
 * @wordpress-plugin
 * Plugin Name:       FloPress
 * Plugin URI:        https://flopress.io
 * Description:       Build awesome features with visual scripting !
 * Version:           1.5.0
 * Author:            FloPress team
 * Text Domain:       flopress
 * Domain Path:       /languages
 * License: GPL3
 * 
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'FLOPRESS_VERSION', '1.5.0' );
define( 'FLOPRESS_DEV_MODE', false);

require_once plugin_dir_path( __FILE__ ) . 'includes/common.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-flopress.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-flopress-activator.php
 */
function activate_flopress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-flopress-activator.php';
	Flopress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-flopress-deactivator.php
 */
function deactivate_flopress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-flopress-deactivator.php';
	Flopress_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/class-flopress-uninstall.php
 */
function uninstall_flopress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-flopress-uninstall.php';
	Flopress_Uninstall::uninstall();
}

register_activation_hook( __FILE__, 'activate_flopress' );
register_deactivation_hook( __FILE__, 'deactivate_flopress' );
register_uninstall_hook( __FILE__, 'uninstall_flopress' );

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_flopress() {
	$plugin = new Flopress();
	$plugin->run();
}

run_flopress();



