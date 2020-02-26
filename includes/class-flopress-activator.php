<?php

/**
 * Fired during plugin activation
 *
 * @link       https://flopress.io
 * @since      1.0.0
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Flopress
 * @subpackage Flopress/includes
 * @author     Flopress team <team@flopress.io>
 */
class Flopress_Activator {

	/**
	 * The plugin activation
	 *
	 * Nothing for the moment
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature"; 

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
			`id` int(10) NOT NULL auto_increment,
			`title` varchar(255),
			`description` text,
			`category` varchar(255),
			`status` varchar(255),
			`content` longblob,
			`build` varchar(255),
			`settings` text,
			`korder` int(10),
			PRIMARY KEY( `id` )
		)";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		$querystr = "SELECT $table_name.id, $table_name.title, $table_name.description, $table_name.category, $table_name.status FROM $table_name ORDER BY korder DESC";
		$features = $wpdb->get_results($querystr, OBJECT);
		if(count($features) == 0){
			$content = file_get_contents(dirname(__FILE__) . '/sample/private-content.json'); 
			if($content){
				$tdata = json_decode($content);
				$data = array(
					"title" => $tdata->title,
					"description" => $tdata->description,
					"category" => $tdata->category,
					"status" => "inactive",
					"content" => serialize($tdata->content),
					"korder" => 1
				);
				$wpdb->insert($table_name,$data);
			}
		}

		$upload = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = $upload_dir . '/flopress';
		if (! is_dir($upload_dir)) {
			mkdir( $upload_dir, 0700 );
		}


	}

}
