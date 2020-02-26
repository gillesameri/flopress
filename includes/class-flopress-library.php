<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://flopress.io
 * @since      1.4.0
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 * @author     Flopress team <team@flopress.io>
 */
class Flopress_Library {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.4.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.4.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.4.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $loader ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$loader->add_filter( 'flopress/sources/add', $this,  'flopress_get_store', 0 );
		$loader->add_filter( 'flopress/sources/local/features/list', $this, 'flopress_features_definitions' );
		$loader->add_filter( 'flopress/sources/local/features/import', $this, 'flopress_features_import' );
	}

	/**
	 * Register flopress core time saving features.
	 *
	 * @since    1.4.0
	 */
	public function flopress_get_store($sources){
		$sources[] = array(
			'slug' => 'local', 
			'title' => 'FloPress', 
			'can_add' => false, 
			'can_delete' => false,
			'icon' => "fas fa-database",
			'has_settings' => false
		);
		return $sources;
	}

	/**
	 * Register flopress core time saving features.
	 *
	 * @since    1.4.0
	 */
	public function flopress_features_definitions($data){
        $str_data 	= file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/features/index.json');
		$data2 		= json_decode($str_data);
        return array_merge((array) $data,(array) $data2);
	}

	/**
	 * Register flopress base time saving features.
	 *
	 * @since    1.4.0
	 */
	public function flopress_features_import($slug){
		$str_data 	= file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/features/'.$slug.'.fpz');
		$data = json_decode(gzuncompress($str_data));
        return $data;
	}

}
