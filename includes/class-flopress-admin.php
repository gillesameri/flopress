<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://flopress.io
 * @since      1.0.9
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 * @author     Flopress team <team@flopress.io>
 */
class Flopress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.9
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.9
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The nonces list.
	 *
	 * @since    1.4.3
	 * @access   private
	 * @var      array    $nonces    The nonces list.
	 */
	private $nonces;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.9
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		$this->nonces 		= array();
	}

	/**
	 * Register the menu.
	 *
	 * @since    1.0.9
	 */
	public function flopress_menu(){
		add_menu_page(
			'FloPress new', 
			'FloPress', 
			'manage_options', 
			'/flopress', 
			array($this, 'flopress_ui'), 
			'data:image/svg+xml;base64,' . base64_encode('<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" viewBox="0 0 64 64" width="64" height="64"><defs><path d="M43.02 14.36L44.29 14.54L45.53 14.8L46.75 15.12L47.95 15.51L49.11 15.96L50.24 16.48L51.33 17.06L52.39 17.69L53.41 18.38L54.39 19.12L55.33 19.91L56.22 20.76L57.06 21.65L57.85 22.58L58.6 23.56L59.28 24.58L59.92 25.64L60.49 26.73L61.01 27.86L61.46 29.03L61.85 30.22L62.18 31.44L62.43 32.69L62.62 33.96L62.73 35.25L62.77 36.56L62.73 37.88L62.62 39.17L62.43 40.44L62.18 41.69L61.85 42.91L61.46 44.1L61.01 45.26L60.49 46.39L59.92 47.49L59.28 48.54L58.6 49.56L57.85 50.54L57.06 51.48L56.22 52.37L55.33 53.21L54.39 54L53.41 54.75L52.39 55.44L51.33 56.07L50.24 56.65L49.11 57.16L47.95 57.61L46.75 58L45.53 58.33L44.29 58.58L43.02 58.77L41.72 58.88L40.41 58.92L39.1 58.88L37.8 58.77L36.53 58.58L35.29 58.33L34.07 58L32.87 57.61L31.71 57.16L30.58 56.65L29.49 56.07L28.43 55.44L27.41 54.75L26.43 54L25.5 53.21L24.6 52.37L23.76 51.48L22.97 50.54L22.23 49.56L21.54 48.54L20.9 47.49L20.33 46.39L19.81 45.26L19.36 44.1L18.97 42.91L18.64 41.69L18.48 40.86L1.23 40.86L1.23 32.53L18.42 32.53L18.64 31.44L18.97 30.22L19.36 29.03L19.81 27.86L20.33 26.73L20.9 25.64L21.54 24.58L22.23 23.56L22.97 22.58L23.76 21.65L24.6 20.76L25.5 19.91L26.43 19.12L27.41 18.38L28.43 17.69L29.49 17.06L30.58 16.48L31.71 15.96L32.87 15.51L34.07 15.12L35.29 14.8L36.53 14.54L37.8 14.36L39.1 14.24L40.41 14.21L41.72 14.24L43.02 14.36ZM37.79 23.84L36.55 24.16L35.36 24.6L34.22 25.14L33.15 25.79L32.15 26.54L31.23 27.38L30.39 28.3L29.64 29.3L28.99 30.37L28.44 31.51L28.01 32.7L27.69 33.95L27.49 35.24L27.42 36.56L27.49 37.89L27.69 39.18L28.01 40.42L28.44 41.62L28.99 42.75L29.64 43.82L30.39 44.82L31.23 45.74L32.15 46.58L33.15 47.33L34.22 47.98L35.36 48.53L36.55 48.97L37.79 49.29L39.08 49.48L40.41 49.55L41.74 49.48L43.03 49.29L44.27 48.97L45.46 48.53L46.6 47.98L47.67 47.33L48.67 46.58L49.59 45.74L50.43 44.82L51.18 43.82L51.83 42.75L52.38 41.62L52.81 40.42L53.13 39.18L53.33 37.89L53.4 36.56L53.33 35.24L53.13 33.95L52.81 32.7L52.38 31.51L51.83 30.37L51.18 29.3L50.43 28.3L49.59 27.38L48.67 26.54L47.67 25.79L46.6 25.14L45.46 24.6L44.27 24.16L43.03 23.84L41.74 23.64L40.41 23.57L39.08 23.64L37.79 23.84ZM43.09 30.89L44.26 31.64L45.24 32.62L45.98 33.79L46.46 35.12L46.63 36.56L46.46 38.01L45.98 39.34L45.24 40.51L44.26 41.49L43.09 42.23L41.76 42.71L40.31 42.87L38.87 42.71L37.54 42.23L36.37 41.49L35.39 40.51L34.64 39.34L34.17 38.01L34 36.56L34.17 35.12L34.64 33.79L35.39 32.62L36.37 31.64L37.54 30.89L38.87 30.42L40.31 30.25L41.76 30.42L43.09 30.89Z" id="d5tPVHwqF"></path></defs><g><g><g><use xlink:href="#d5tPVHwqF" opacity="1" fill="#ffffff" fill-opacity="1"></use><g><use xlink:href="#d5tPVHwqF" opacity="1" fill-opacity="0" stroke="#000000" stroke-width="0" stroke-opacity="1"></use></g></g></g></g></svg>'),
			65
		);
		add_submenu_page( '/flopress', 'Flopress', 'Features', 'manage_options', 'flopress', array($this, 'flopress_ui') );
	}

	/**
	 * Add different nonce for each ajax action.
	 *
	 * @since    1.0.9
	 */
	public function add_nonces($nonces){
		foreach($nonces as $nonce){
			$this->nonces[$nonce] = wp_create_nonce($nonce);
		}
	}

	/**
	 * Load Flopress script, styles and render the app
	 *
	 * @since    1.0.9
	 */
	public function flopress_ui(){
		global $post;
		wp_enqueue_script( $this->plugin_name.'-bluebird', plugin_dir_url(  dirname(__FILE__)  ) . 'js/bluebird.min.js', array( 'jquery'), '3.3.5', false );
		wp_enqueue_script( $this->plugin_name.'-admin-vue-manifest', plugin_dir_url(  dirname(__FILE__)  ) . 'js/manifest.js', array( 'jquery'), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-admin-vue-vendor', plugin_dir_url(  dirname(__FILE__)  ) . 'js/vendor.js', array( 'jquery'), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-admin-vue-index', plugin_dir_url(  dirname(__FILE__)  ) . 'js/index.js', array( 'jquery',$this->plugin_name.'-admin-vue-vendor'), $this->version, false );
		$params = array(
			'ajaxurl' 		=> esc_url(admin_url('admin-ajax.php', 'admin')),
			'ajax_nonce' 	=> $this->nonces,
			// remove these line
			'isP' 			=> esc_js(true),
			'gproi' 		=> esc_url('https://flopress.io'),
			'gproe' 		=> esc_url('https://flopress.io')
		);
		wp_localize_script( $this->plugin_name.'-admin-vue-index', 'flopQ', $params );
		wp_enqueue_media();
		?>
		<div id="app"></div>
		<?php  
	}

	/**
	 * Get codex sources
	 *
	 * @since    1.0.9
	 */
	public function flopress_codex_sources(){
		$codexs = apply_filters( 'flopress/add/codex', array());
		$options = get_option('flopress_codex_sources');
		foreach($codexs as $k => $codex){
			unset($codexs[$k]['actions']);
			unset($codexs[$k]['filters']);
			unset($codexs[$k]['templates']);
			if(in_array($k,$options)){
				$codexs[$k]['enabled'] = true;
			}
		}
		wp_send_json_success( $codexs );
		exit();
	}
	
	/**
	 * Save codex sources to options
	 *
	 * @since    1.0.9
	 */
	public function flopress_codex_sources_save(){
		$originalSources = apply_filters( 'flopress/add/codex', array());
		$toSave = array();
		if(array_key_exists('sources',$_POST)){
			$posts_sources = $_POST['sources'];
			$sources = json_decode(stripslashes($posts_sources));
			foreach($sources as $ks => $source){
				$kss = sanitize_text_field($ks);
				if(array_key_exists($kss,$originalSources)){
					$o = $originalSources[$kss];
					if(!$o['disabled']){
						if($source->enabled){
							$toSave[] = $kss;
						}
					}
				}
			}
		}
		if(count($toSave) > 0){
			$result = update_option('flopress_codex_sources',$toSave);
		}
		else{
			delete_option('flopress_codex_sources');
		}
		wp_send_json_success( true );
		exit();
	}

	/**
	 * Get sources
	 *
	 * @since    1.0.9
	 */
	public function flopress_source_types(){
		$data = apply_filters( 'flopress/sources/add', array());
		wp_send_json_success( $data );
		exit();
	}

	/**
	 * Get sources settings
	 *
	 * @since    1.4.0
	 */
	public function flopress_get_source_settings(){
		$source = (array_key_exists('source',$_GET)) ? strval($_GET['source']) : false;
		$data = false;
		if($source){
			$data = apply_filters( 'flopress/sources/'.$source.'/settings/get', $source);
		}
		wp_send_json_success( $data );
		exit();
	}

	/**
	 * Save sources settings
	 *
	 * @since    1.4.0
	 */
	public function flopress_save_source_settings(){
		$source = (array_key_exists('source',$_POST)) ? strval($_POST['source']) : false;
		$settings = (array_key_exists('settings',$_POST)) ? json_decode(stripslashes($_POST['settings'])) : false;
		$data = false;
		if($source && $source){
			$data = apply_filters( 'flopress/sources/'.$source.'/settings/save', $source, $settings);
		}
		wp_send_json_success( true );
		exit();
	}

	/**
	 * Add to source
	 *
	 * @since    1.0.9
	 */
	public function flopress_add_to_source(){
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		$feature	= (array_key_exists('feature',$_GET)) 	? intval($_GET['feature']) : false;
		$source		= (array_key_exists('source',$_GET)) 	? strval($_GET['source']) : false;
		if($feature && $source){
			$row = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = $feature", OBJECT);

			if($row){
				$content = unserialize($row->content);
				$data = (object) array();
	
				$data->title = $row->title;
				$data->category = $row->category;
				$data->description = $row->description;

				$infos = $data;

				$data->content = $content;
				$filecontent = gzcompress(json_encode($data));
				$checksum = crc32($filecontent);
				$infos->slug = $checksum;
	
				$response = apply_filters("flopress/sources/$source/features/add", $checksum, $filecontent, $infos);
				
				if($response){
					wp_send_json_success( true );
				}
				else{
					status_header(404);
					wp_send_json_error( "Can't add to source" );
				}
				exit();
			}
		}
		status_header(404);
		wp_send_json_error( "Can't add to source" );
		exit();
	}

	/**
	 * Remove to source
	 *
	 * @since    1.0.9
	 */
	public function flopress_remove_to_source(){
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		$feature = (array_key_exists('feature',$_GET)) ? strval($_GET['feature']) : false;
		$source = (array_key_exists('source',$_GET)) ? strval($_GET['source']) : false;
		if($feature && $source){
			$response = apply_filters("flopress/sources/$source/features/remove", $feature);
			wp_send_json_success( $response );
			if($response){
				wp_send_json_success( true );
			}
			else{
				status_header(404);
				wp_send_json_error( "Can't remove from source" );
			}
			exit();
		}
		status_header(404);
		wp_send_json_success( "Can't remove from source" );
		exit();
	}

	/**
	 * Get connectors types
	 *
	 * @since    1.0.9
	 */
	public function flopress_category_types(){
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		$results = array();
		$cats = $wpdb->get_results("SELECT category FROM ".$table_name." WHERE category IS NOT NULL and category != ''", ARRAY_N );
		if($cats){
			foreach($cats as $cat) {
				$cts = explode(',',$cat[0]);
				foreach($cts as $ct){
					if(!in_array($ct, $results)){
						$results[] = $ct;
					}
				}
			}
		}
		wp_send_json_success($results);
	}

	/**
	 * Sort features by array of ids
	 *
	 * @since    1.0.9
	 */
	public function flopress_order_installed_features() {
		if(array_key_exists('ids', $_GET) && is_array($_GET['ids'])){
			$orderArray = $_GET['ids'];
			$orderArray = array_reverse($orderArray);
			
			global $wpdb;
			$table_name = $wpdb->prefix . "flopress_feature";

			foreach ($orderArray as $k => $id) {
				$update = $wpdb->update( 
					$table_name, 
					array(
						'korder' => $k,
					), 
					array( 'ID' => intval($id) )
				);
			}
			wp_send_json_success(true);
		}
		else{
			wp_send_json_error( "Can't order features" );
		}
	}

	/**
	 * Load installed features.
	 *
	 * @since    1.0.9
	 */
	public function flopress_load_installed_features(){
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";

		$querystr = "SELECT $table_name.id, $table_name.title, $table_name.description, $table_name.category, $table_name.status FROM $table_name ORDER BY korder DESC";
		$features = $wpdb->get_results($querystr, OBJECT);

		if($features !== false){
			wp_send_json_success($features);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't load installed features.");
		}
		exit();
	}

	/**
	 * Activate a feature.
	 *
	 * @since    1.0.9
	 */
	public function flopress_activate_feature(){
		if(!array_key_exists('id',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		$id = intval($_GET['id']);
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";

		$feature = $wpdb->get_row("SELECT * FROM ".$table_name." WHERE id = ".$id);

		$buildname = uniqid('fpc_');

		$builder = new Flopress_Builder($buildname);
		$response = $builder->build(unserialize($feature->content));
		$write = false;

		if($response['status']){
			$path = wp_upload_dir()["basedir"].'/flopress/'.$buildname.'.php';
			$fp = fopen($path, 'w');
			$write = fwrite($fp, $response['message']);
			fclose($fp);
		}
		else{
			status_header(404);
			wp_send_json_error($response['message']);
			exit();
		}

		if(!$write){
			status_header(404);
			wp_send_json_error("An error occured. Can't save feature code.");
			exit();
		}

		$update = $wpdb->update( 
			$table_name, 
			array(
				'build' => $buildname, 
				'status' => 'active'
			), 
			array( 'ID' => $id )
		);
		
		if($update){
			wp_send_json_success(true);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't active feature.");
			exit();
		}
		exit();
	}

	/**
	 * Deactivate a feature.
	 *
	 * @since    1.0.9
	 */
	public function flopress_deactivate_feature(){
		if(!array_key_exists('id',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		$id = intval($_GET['id']);
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";

		$feature = $wpdb->get_row("SELECT * FROM ".$table_name." WHERE id = ".$id);

		if($feature->build != null){
			if(file_exists(wp_upload_dir()["basedir"].'/flopress/'.$feature->build.'.php'))
				unlink(wp_upload_dir()["basedir"].'/flopress/'.$feature->build.'.php');
		}

		$update = $wpdb->update( 
			$table_name, 
			array( 
				'status' => 'inactive',
				'build' => null
			), 
			array( 'ID' => $id )
		);

		if($update){
			wp_send_json_success(true);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't active feature.");
		}
		exit();
	}

	/**
	 * List remotes features.
	 *
	 * @since    1.0.9
	 */
	public function flopress_filter_remote_features(){
		$source 	= (array_key_exists('o',$_GET)) ? sanitize_text_field($_GET['o']) : 'local';
		$data 		= apply_filters( 'flopress/sources/'.$source.'/features/list', array());
		$search 	= sanitize_text_field($_GET['s']);
		$category 	= sanitize_text_field($_GET['c']);
		$items 		= array();
		$categories = array();

		foreach($data as $ki => $item){
			if(property_exists($item,'category') && !in_array($item->category,$categories))  {
				$cats = explode(',',$item->category);
				if(count($cats) > 0 && $cats[0] != '')
					$categories = array_merge($categories, $cats);
			}
		}

		$categories = array_unique($categories);
		
		foreach($data as $ki => $item){

			$toInsert = true;

			if(property_exists($item,'category') && $toInsert && $category && (!in_array($category,explode(',',$item->category)))){
				$toInsert = false;
			}

			if($toInsert && $search){
				$searchs = explode(' ',$search);
				foreach($searchs as $sea){
					if((stripos($item->title, $sea) === FALSE) && (stripos($item->description, $sea) === FALSE)){
						$toInsert = false;
					}
				}
			}

			if($toInsert){
				$items[] = $item;
			}
		}
		
		$response = array(
			'features' => $items,
			'categories' => $categories
		);
		wp_send_json_success($response);
	}

	/**
	 * Import a remote feature.
	 *
	 * @since    1.0.9
	 */
	public function flopress_import_remote_feature(){
		$source = (array_key_exists('source',$_GET)) ? sanitize_text_field($_GET['source']) : 'local';

		if(!array_key_exists('slug',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}
		global $wpdb;
		$slug = array_key_exists('slug', $_GET) ? sanitize_text_field($_GET['slug']) : '';
		$table_name = $wpdb->prefix . "flopress_feature";
		$content = apply_filters( "flopress/sources/$source/features/import",$slug);

		$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

		if($content){
			$tdata = $content;
			$data = array(
				"title" 		=> stripslashes(sanitize_text_field($tdata->title)),
				"description"	=> stripslashes(sanitize_textarea_field($tdata->description)),
				"category" 		=> stripslashes(sanitize_text_field($tdata->category)),
				"status" 		=> "inactive",
				"content" 		=> serialize($tdata->content),
				"korder" 		=> $rowcount
			);
			$insert = $wpdb->insert($table_name,$data);
			if($insert){
				wp_send_json_success($wpdb->insert_id);
			}
			else{
				status_header(404);
				wp_send_json_error("An error occured. Can't import feature.");
			}
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't import feature.");
		}
		exit();
	}

	/**
	 * Import file from an upload
	 *
	 * @since    1.0.9
	 */
	public function flopress_import_local_feature(){
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
		foreach($_FILES as $k => $file){
			$path_parts = pathinfo($file["name"]);
			if($path_parts['extension'] == 'fpz'){
				$content = file_get_contents($file['tmp_name']); 
				if($content){
					$tdata = json_decode(gzuncompress($content));
					$data = array(
						"title" 		=> sanitize_text_field($tdata->title),
						"description" 	=> sanitize_textarea_field($tdata->description),
						"category" 		=> sanitize_text_field($tdata->category),
						"status" 		=> "inactive",
						"content" 		=> serialize($tdata->content),
						"korder" 		=> $rowcount
					);
					$insert = $wpdb->insert($table_name,$data);
					if($insert){
						wp_send_json_success(true);
						exit();
					}
				}
			}
			else if($path_parts['extension'] == 'json'){
				$content = file_get_contents($file['tmp_name']); 
				if($content){
					$tdata = json_decode($content);
					$data = array(
						"title" 		=> sanitize_text_field($tdata->title),
						"description" 	=> sanitize_textarea_field($tdata->description),
						"category" 		=> sanitize_text_field($tdata->category),
						"status" 		=> "inactive",
						"content" 		=> serialize($tdata->content),
						"korder" 		=> $rowcount
					);
					$insert = $wpdb->insert($table_name,$data);
					if($insert){
						wp_send_json_success(true);
						exit();
					}
				}
			}
		}
		status_header(404);
		wp_send_json_error("An error occured. Can't import feature.");
		exit();
	}

	/**
	 * Export feature
	 *
	 * @since    1.0.9
	 */
	public function flopress_export_feature(){
		if(!array_key_exists('id',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		$id= intval($_GET['id']);
		$data = array(
			"ID" => $id
		);
		$row = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = $id", OBJECT);
		if($row){
			
			$content = unserialize($row->content);
			$data = (object) array();

			$data->title 		= $row->title;
			$data->category 	= $row->category;
			$data->description 	= $row->description;
			$data->content 		= $content;

			$ext = (defined('FLOPRESS_DEV_MODE') && FLOPRESS_DEV_MODE) ? 'json' : 'fpz';
			$filecontent = (defined('FLOPRESS_DEV_MODE') && FLOPRESS_DEV_MODE) ? json_encode($data) : gzcompress(json_encode($data));
			$filename = sanitize_title($row->title)  . ".".$ext;
			header("Content-type: application/".$ext);
			header('Content-Disposition: attachment; filename="'.$filename.'"'); 
			header("Pragma: no-cache"); 
			header("Expires: 0"); 
			echo $filecontent;
			exit();
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't found feature.");
			exit();
		}
	}

	/**
	 * Delete feature
	 *
	 * @since    1.0.9
	 */
	public function flopress_delete_feature(){
		if(!array_key_exists('id',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		$id= intval($_GET['id']);

		$feature = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = $id", OBJECT);
		if($feature && ($feature->build != null)){
			if(file_exists(wp_upload_dir()["basedir"].'/flopress/'.$feature->build.'.php'))
				unlink(wp_upload_dir()["basedir"].'/flopress/'.$feature->build.'.php');
		}
		
		$data = array(
			"ID" => $id
		);
		$wpdb->query("UPDATE $table_name SET korder=korder-1 WHERE korder >= $feature->korder");
		
		$delete = $wpdb->delete($table_name,$data);
		if($delete){
			wp_send_json_success(true);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't delete feature.");
		}
		exit();
	}


	/**
	 * Load feature infos and data.
	 *
	 * @since    1.0.9
	 */
	public function flopress_load_feature(){
		if(!array_key_exists('id',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		$id= intval($_GET['id']);
		
		$feature = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = $id", OBJECT);
		
		if($feature){
			$feature->content = unserialize($feature->content);
			wp_send_json_success($feature);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't load feature.");
		}
		exit();
	}


	/**
	 * Load feature infos only.
	 *
	 * @since    1.0.9
	 */
	public function flopress_load_feature_infos(){
		if(!array_key_exists('id',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		$id= intval($_GET['id']);
		
		$feature = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = $id", OBJECT);
		
		if($feature){
			unset($feature->content);
			wp_send_json_success($feature);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't load feature.");
		}
		exit();
	}

	/**
	 * Create feature.
	 *
	 * @since    1.0.9
	 */
	public function flopress_create_feature(){
		if(!array_key_exists('title',$_POST)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";

		$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

		$data = array(
			"title" 		=> stripslashes(sanitize_text_field($_POST['title'])),
			"description" 	=> stripslashes(sanitize_textarea_field($_POST['description'])),
			"category" 		=> stripslashes(sanitize_text_field($_POST['category'])),
			"status" 		=> "inactive",
			"korder" 		=> $rowcount
		);
		$insert = $wpdb->insert($table_name,$data);

		if($insert){
			wp_send_json_success($wpdb->insert_id);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't create feature.");
		}
		exit();
	}

	/**
	 * Copy feature.
	 *
	 * @since    1.0.9
	 */
	public function flopress_copy_feature(){
		if(!array_key_exists('id',$_POST) || !array_key_exists('title',$_POST)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		
		$id= intval($_POST['id']);

		$row = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = $id", OBJECT);
		$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

		$data = array(
			"title" 		=> stripslashes(sanitize_text_field($_POST['title'])),
			"description" 	=> stripslashes(sanitize_textarea_field($_POST['description'])),
			"category" 		=> stripslashes(sanitize_text_field($_POST['category'])),
			"status" 		=> "inactive",
			"content" 		=> $row->content,
			"korder" 		=> $rowcount
		);
		$insert = $wpdb->insert($table_name,$data);

		if($insert){
			wp_send_json_success(true);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't copy feature.");
		}
		exit();
	}
	
	/**
	 * Edit feature.
	 *
	 * @since    1.0.9
	 */
	public function flopress_edit_feature(){
		if(!array_key_exists('id',$_POST) || !array_key_exists('title',$_POST)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		
		$id= intval($_POST['id']);

		$row = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = $id", OBJECT);

		$data = array(
			"title" 		=> stripslashes(sanitize_text_field($_POST['title'])),
			"description" 	=> stripslashes(sanitize_textarea_field($_POST['description'])),
			"category"		=> stripslashes(sanitize_text_field($_POST['category']))
		);

		$update = $wpdb->update(  $table_name, $data, array( 'ID' => $id ) );

		if($update !== false){
			wp_send_json_success(true);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't edit feature.");
		}
		exit();
	}

	
	/**
	 * Save feature.
	 *
	 * @since    1.0.9
	 */
	public function flopress_save_feature(){
		if(!array_key_exists('id',$_POST) || !array_key_exists('content',$_POST)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		$id		 = intval($_POST['id']);
		$content = stripcslashes($_POST['content']);
		$content = json_decode($content);
		$contentToSave = serialize($content->content);

		$update = $wpdb->update( 
			$table_name, 
			array(
				'content' => $contentToSave,
			), 
			array( 'ID' => $id )
		);

		if($update){
			wp_send_json_success(true);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't save feature.");
		}
		exit();
	}

	
	/**
	 * Build feature.
	 *
	 * @since    1.0.9
	 */
	public function flopress_build_feature(){
		if(!array_key_exists('id',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		$id= intval($_GET['id']);

		$feature = $wpdb->get_row("SELECT * FROM ".$table_name." WHERE id = ".$id);

		$buildname = uniqid('fpc_');
		if($feature->build != null){
			if(file_exists(wp_upload_dir()["basedir"].'/flopress/'.$feature->build.'.php'))
				unlink(wp_upload_dir()["basedir"].'/flopress/'.$feature->build.'.php');
		}

		$update = $wpdb->update( 
			$table_name, 
			array('build' => $buildname), 
			array( 'ID' => $id )
		);
		
		$builder = new Flopress_Builder($buildname);
		$response = $builder->build(unserialize($feature->content));

		$write = false;

		if($response['status']){

			$path = wp_upload_dir()["basedir"].'/flopress/'.$buildname.'.php';
			$fp = fopen($path, 'w');
			$write = fwrite($fp, $response['message']);
			fclose($fp);
		}
		else{
			status_header(404);
			wp_send_json_error($response['message']);
		}

		if($write){
			wp_send_json_success(true);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't save feature code.");
		}
		exit();
	}

	/**
	 * Get feature settings.
	 *
	 * @since    1.0.9
	 */
	public function flopress_get_settings_feature(){
		if(!array_key_exists('id',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";

		$id= intval($_GET['id']);
		$feature = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = $id", OBJECT);
		
		// get settings value from data file and compute settings
		$settings = array();
		if($feature){
			$content = unserialize($feature->content);
			$savedSettings = unserialize($feature->settings);
			if($content){
				foreach($content as $key => $file){
					if(($key != '_meta') && ($file->type == 'data')){
						if(property_exists($file->content,'is_settings') && $file->content->is_settings){
							if($savedSettings && array_key_exists($key, $savedSettings))
								$file->override_value = $savedSettings[$key];
							$settings[$key] = $file;
						}
					}
				}
				uasort($settings, array($this, "sort_by_weight"));
			}
			wp_send_json_success($settings);
			
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't save feature settings.");
		}
		exit();
	}

	/**
	 * Set feature settings.
	 *
	 * @since    1.0.9
	 */
	public function flopress_set_settings_feature(){
		if(!array_key_exists('id',$_POST) || !array_key_exists('settings',$_POST)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";

		$id= intval($_POST['id']);
		$settings = $_POST['settings'];
		$feature = $wpdb->get_row("SELECT * FROM $table_name WHERE ID = $id", OBJECT);

		//save settings in option
		if($feature){
			$settingsToSave = array();
			$settings = stripcslashes($settings);
			$settings = json_decode($settings, true);
			if($settings){
				foreach($settings as $k => $setting){
					if($setting['override_value'] !== null){
						$settingsToSave[$k] = false;
						if(array_key_exists('override_value', $setting))	
							$settingsToSave[$k] = $setting['override_value'];
					}
				}
				$update = $wpdb->update( 
					$table_name, 
					array( 
						'settings' => serialize($settingsToSave),	// string
					), 
					array( 'ID' => $id )
				);
				if($update !== false){
					wp_send_json_success(true);
				}
				else{
					status_header(404);
					wp_send_json_error("An error occured. Can't save feature settings.");
				}
			}
			else{
				status_header(404);
				wp_send_json_error("An error occured. Settings bad formatted.");
			}
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't save feature settings.");
		}
		exit();
	}

	/**
	 * Return post for picker.
	 *
	 * @since    1.0.9
	 */
	public function flopress_post_picker(){
		if(!array_key_exists('search',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		$search = sanitize_text_field($_GET['search']);
		$args = array(
			's' => $search,
			'posts_per_page' => -1
		  );
		$posts = get_posts( $args );
	
		wp_send_json_success($posts);
		exit();
	}

	/**
	 * Return post by id for picker.
	 *
	 * @since    1.0.9
	 */
	public function flopress_post_by_id(){
		if(!array_key_exists('id',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		$id = intval($_GET['id']);
		$post = array(
			"ID" => $id,
			"post_title" => get_the_title($id),
			"post_status" => get_post_status ( $id )
		);
		wp_send_json_success($post);
		exit();
	}
	
	/**
	 * Sort by weight usort.
	 *
	 * @since    1.0.9
	 */
	public function sort_by_weight($a, $b){
		return $a->content->weight > $b->content->weight;
	}

	/**
	 * Load component infos by name.
	 *
	 * @since    1.0.9
	 */
	public function flopress_load_component(){
		if(!array_key_exists('component',$_GET)){
			status_header(400);
			wp_send_json_error("An error occured. Missing parameters");
			exit();
		}

		$name = sanitize_text_field($_GET['component']);
		$type = array_key_exists('type',$_GET) ? sanitize_text_field($_GET['type']) : false;

		if(!$type){
			status_header(404);
			wp_send_json_error("An error occured. Can't load component infos.");
			exit();
		}

		$types = array(
			"hook-action",
			"hook-filter",
			"hook-shortcode",
			"cast",
			"comparison",
			"arithmetic",
			"variable",
			"efct",
			"fct",
			"logical",
			"instruction",
			"global",
			"action",
			"filter",
			"shortcode",
			"template",
			"data"
		);
		if(!in_array($type,$types)){
			status_header(404);
			wp_send_json_error("Function definition type not found.");
		}

		$component = array();
		$data = array();
		switch($type){
			case 'hook-action':
				$data = apply_filters( 'flopress/add/actions', array());
			break;
			case 'hook-filter':
				$data = apply_filters( 'flopress/add/filters', array());
			break;
			default:
				$data = apply_filters( 'flopress/add/functions', array());
			break;
		}
		$k = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9\_]+/', '-', $name)));
		$component = (array_key_exists($k, $data)) ? $data[$k] : false;
		
		if($component){
			wp_send_json_success($component);
		}
		else{
			status_header(404);
			wp_send_json_error("An error occured. Can't load component infos.");
		}
		exit();
	}

	/**
	 * Sort by rank usort.
	 *
	 * @since    1.0.9
	 */
	public function sort_by_rank($a, $b){
		if(property_exists($a,'rank') && property_exists($b,'rank')){
			if ($a->rank == $b->rank) {
				return 0;
			}
			else if ($a->rank < $b->rank){
				return 1;
			}
			else{
				return -1;
			}
		}
		else{
			return -1;
		}
	}
	
	/**
	 * Load components list from codex
	 *
	 * @since    1.0.9
	 */
    public function flopress_load_components(){
		$search = (array_key_exists('search',$_GET)) 	? sanitize_text_field($_GET['search']) 	: false;
		$store 	= (array_key_exists('store',$_GET)) 	? sanitize_text_field($_GET['store']) 	: false;
		$group 	= (array_key_exists('group',$_GET)) 	? sanitize_text_field($_GET['group']) 	: false;
		$type 	= (array_key_exists('type',$_GET)) 		? sanitize_text_field($_GET['type']) 	: false;
		$page 	= (array_key_exists('page',$_GET)) 		? intval($_GET['page'])	: false;

		$data	= array();

		switch($store){
			case 'functions':
				$data = apply_filters( 'flopress/add/functions', array());
			break;
			case 'actions':
				$data = apply_filters( 'flopress/add/actions', array());
			break;
			case 'filters':
				$data = apply_filters( 'flopress/add/filters', array());
			break;
		}
		
		$items 		= array();
		$groups 	= array();
		$wp_version = get_bloginfo('version');

		foreach($data as $ki => $item){

			$toInsert = true;
			

			if(property_exists($item,'type') && $toInsert && $type && ($item->type != $type)){
				$toInsert = false;
			}

			if($toInsert && property_exists($item,'group') && !in_array($item->group,$groups))  {
				$groups[] = $item->group;
			}

			/*
			compare with plugin version
			if(property_exists($item,'since') && $item->since && version_compare($item->since, $wp_version, '>=')){
				$toInsert = false;
			}
			*/

			if(property_exists($item,'group') && $toInsert && $group && ($item->group != $group)){
				$toInsert = false;
			}

			if($toInsert && $search){
				$searchs = explode(' ',$search);
				foreach($searchs as $sea){
					if((stripos($item->name, $sea) === FALSE) && (stripos($item->summary, $sea) === FALSE) && (stripos($item->description, $sea) === FALSE)){
						$toInsert = false;
					}
				}
			}

			if($toInsert){
				$item->inputs = (property_exists($item,'inputs')) ? (object) $item->inputs : (object) array();
				$item->outputs = (property_exists($item,'outputs')) ? (object) $item->outputs : (object) array();
				if($store == 'functions') unset($item->description);
				$items[$ki] = $item;
			}
		}
		
		usort($items, array($this, "sort_by_rank"));

		$total = ceil(count($items)/10);
		if($page !== false){
			$paginate = ($page)*10;
			$items = array_slice($items, $paginate,10); 
		}
		else{
			$items = array_slice($items, 0,200); 
		}
		sort($groups);
		$response = array(
			'components' => $items,
			'searchPage' => $page+1,
			'total_pages' => $total,
			'_group' => $groups
		);

		wp_send_json_success($response);
		exit();

	}

}
