<?php

/**
 * Fired during plugin uninstall
 *
 * @link       https://flopress.io
 * @since      1.0.0
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 */

/**
 * Fired during plugin uninstall.
 *
 * This class defines all code necessary to run during the plugin's uninstall.
 *
 * @since      1.0.0
 * @package    Flopress
 * @subpackage Flopress/includes
 * @author     Flopress team <team@flopress.io>
 */
class Flopress_Uninstall {

	/**
	 * The plugin activation
	 *
	 * Nothing for the moment
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature"; 
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir']. '/flopress';
        self::rrmdir($upload_dir);
	}

	private static function rrmdir($dir) { 
		if (is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                if (is_dir($dir."/".$object))
                    rrmdir($dir."/".$object);
                else
                    unlink($dir."/".$object); 
                } 
            }
            rmdir($dir); 
		} 
    }
}
