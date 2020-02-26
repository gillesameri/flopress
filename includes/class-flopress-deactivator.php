<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://flopress.io
 * @since      1.0.0
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Flopress
 * @subpackage Flopress/includes
 * @author     Flopress team <team@flopress.io>
 */
class Flopress_Deactivator {

	/**
	 * The plugin deactivation
	 *
	 * Nothing for the moment
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature"; 

		// deactivate features when disable FloPress
		$update = $wpdb->update( 
			$table_name, 
			array( 'status' => 'inactive'), 
			array( 'status' => 'active' )
		);
	}
}
