<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://flopress.io
 * @since      1.0.0
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 */

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Flopress
 * @subpackage Flopress/includes
 * @author     Flopress team <team@flopress.io>
 */
class Flopress_FTP_Library {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Flopress_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * FTP settings host.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $host    	The ftp host name.
	 */
	protected $host;

	/**
	 * FTP settings username.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $user    	The ftp username.
	 */
	protected $user;

	/**
	 * FTP settings password (encrypted).
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $password    The ftp encrypted password.
	 */
	protected $password;

	/**
	 * FTP settings ssl option.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $ssl    		The SSL option.
	 */
	protected $ssl;

	/**
	 * FTP settings port.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $port    	The ftp connexion port.
	 */
	protected $port;

	/**
	 * FTP settings path.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $path    	The target folder.
	 */
	protected $path;

	/**
	 * FTP handle.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $ftp    		The handle to the connexion.
	 */
	protected $ftp;

	/**
	 * Core functionality of the plugin.
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct($plugin_name, $version, $loader) {
		
		/**
		 * Initiale Flopress Loader
		 */
		$this->loader = $loader;
		
		/**
		 * Get store informations
		 */
		$this->loader->add_filter( 'flopress/sources/add', $this, 'flopress_get_store' );
		
		/**
		 * Get settings
		 */
		$this->loader->add_filter( 'flopress/sources/ftp/settings/get', $this, 'flopress_get_settings', 0, 1 );
		/**
		 * Save settings
		 */
		$this->loader->add_filter( 'flopress/sources/ftp/settings/save', $this, 'flopress_save_settings', 0, 2 );

		/**
		 * List remote features
		 */
		$this->loader->add_filter( 'flopress/sources/ftp/features/list', $this, 'flopress_list_features' );

		/**
		 * Add remote features
		 */
		$this->loader->add_filter( 'flopress/sources/ftp/features/add', $this, 'flopress_add_feature', 0, 3 );

		/**
		 * Remove remote features
		 */
		$this->loader->add_filter( 'flopress/sources/ftp/features/remove', $this, 'flopress_remove_feature', 0, 3 );

		/**
		 * Import remote features
		 */
		$this->loader->add_filter( 'flopress/sources/ftp/features/import', $this, 'flopress_import_feature' );
		
	}
	
	/**
	 * Init FTP Raw
	 *
	 * @since    1.0.5
	 */
	public function init_ftp_raw($host,$user,$password,$ssl=false,$port=21,$path="/"){
		
		try {
			$this->ftp = new \FtpClient\FtpClient();
			$this->ftp->connect($this->host, $this->ssl, $this->port);
			if(@$this->ftp->login($this->user, $this->password)){
				return true;
			}
			else{
				return false;
			}
		} catch(FtpException $e) {
			return false;
		} catch(\Exception $e) {
			return false;
		}
	}
	
	/**
	 * Init FTP
	 *
	 * @since    1.0.5
	 */
	public function init_ftp(){
		$options = get_option( 'fp_ftp_library_settings' );

		$this->host= $options['host'];
		$this->user = $options['user'];
		$this->password = $this->dencrypt($options['password'],'d');
		$this->ssl = (is_array($options) && array_key_exists('ssl', $options)) ? $options['ssl'] : false;
		$this->port = (is_array($options) && array_key_exists('port', $options) && !empty($options['port'])) ? $options['port'] : 21;
		$this->path = (is_array($options) && array_key_exists('path', $options) && !empty($options['path'])) ? $options['path'] : '/';
		
		return $this->init_ftp_raw(
			$this->host,
			$this->user,
			$this->password,
			$this->ssl,
			$this->port,
			$this->path
		);
	}
	
	/**
	 * Render the settings page
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function is_writable(  ) { 

		$isWritable = false;
		$connect = $this->init_ftp();

		if($connect){
			try {
				$testFile = $this->path.uniqid().'.json';
				if(@$this->ftp->putFromString($testFile, json_encode(array()))){
					$this->ftp->delete($testFile);
					$isWritable = true;
				}
			} catch(FtpException $e) {
				$isWritable = false;
			} catch(\Exception $e) {
				$isWritable = false;
			}
		}
	}

	/**
	 * Encrypt Text
	 *
	 * @since    1.0.5
	 */
	public function dencrypt( $string, $action = 'e' ) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash( 'sha256', 'fpkftpc20cptfkpf' );
		$iv = substr( hash( 'sha256', 'fpkftpc20cptfkpf' ), 0, 16 );
		if( $action == 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		}
		else if( $action == 'd' ){
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}
		return $output;
	}

	/**
	 * Register source settings.
	 *
	 * @since    1.0.5
	 */
	public function flopress_get_store($sources){

        $sources[] = array(
			'slug' => 'ftp', 
			'title' => 'FTP',  
			'can_add' => true, 
			'can_delete' => true,
			'icon' => "fab fa-php",
			'has_settings' => 'ftp-settings'
		);
    	return $sources;
	}

	/**
	 * Get settings.
	 *
	 * @since    1.0.5
	 */
	public function flopress_get_settings($source){
		$options = get_option( 'fp_ftp_library_settings' );
		if($options){
			$options['password'] = '';
		}
		else{
			$options = array(
				'host' => '',
				'user' => '',
				'password' => '',
				'ssl' => false,
				'port' => '',
				'path' => '',
			);
		}
		$options['change_password'] = false;
		return (object) $options;
	}

	/**
	 * Save source settings.
	 *
	 * @since    1.0.5
	 */
	public function flopress_save_settings($source, $settings){
		$old_value = get_option( 'fp_ftp_library_settings' );
		$settings = (array) $settings;
		$settings['path'] = (substr($settings['path'], -1) == '/') ? $settings['path'] : $settings['path'].'/' ;

		if(is_array($old_value) && array_key_exists('password',$old_value) && !empty($old_value['password'])){
			if(array_key_exists('change_password',$settings) && ($settings['change_password'] == true)){
				$settings['password'] = $this->dencrypt($settings['password']);
			}
			else{
				$settings['password'] = $old_value['password'];
			}
		}
		else{
			$settings['password'] = $this->dencrypt($settings['password']);
		}
		unset($settings['change_password']);
		$result = update_option('fp_ftp_library_settings', $settings);
		return $result;
	}

	/**
	 * Get features definitions.
	 *
	 * @since    1.0.5
	 */
	private function flopress_get_features_index(){

        ob_start();
		$result = $this->ftp->get("php://output", $this->path.'features.json', FTP_BINARY);
		$contents = ob_get_contents();
		ob_end_clean();
	
		$features = (array) json_decode($contents);
		return $features;
	}

	/**
	 * List features from index files.
	 *
	 * @since    1.0.5
	 */
	public function flopress_list_features($data){
		$this->init_ftp();

		$data2 = $this->flopress_get_features_index();
		return array_merge((array) $data,(array) $data2);
	}

	/**
	 * Add a feature from source.
	 *
	 * @since    1.0.5
	 */
	public function flopress_add_feature($slug,$content,$infos){
		$this->init_ftp();
		
		$index = $this->flopress_get_features_index();
		$exist = false;
		foreach($index as $k => $def){
			if($def->slug == $slug){
				$exist = true;
			}
		}
		$response = false;
		if(!$exist){
			$index[] = $infos;
			try {
				// update features index
				if(@$this->ftp->putFromString($this->path.'features.json', json_encode($index))){
					$response = true;
				}
				// add gzcompressed feature
				if(@$this->ftp->putFromString($this->path."$slug.fpz", $content)){
					$response = true;
				}
			} catch(FtpException $e) {
				
			} catch(\Exception $e) {
				
			}
		}
		return $response;
	}
	
	/**
	 * Remove a feature from source.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function flopress_remove_feature($slug){
		$this->init_ftp();

		$index = $this->flopress_get_features_index();

		$exist = false;
		foreach($index as $k => $def){
			if($def->slug == $slug){
				$exist = $k;
			}
		}
	
		$response = false;
		if($exist !== false){
			unset($index[$exist]);
			try {
				if(@$this->ftp->putFromString($this->path.'features.json', json_encode($index))){
					$response = true;
				}
				if(@$this->ftp->delete($this->path."$slug.fpz")){
					//$response = true;
				}
			} catch(FtpException $e) {
				
			} catch(\Exception $e) {
				
			}
		}
		return $response;
	}
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function flopress_import_feature($slug){
		$this->init_ftp();

		ob_start();
		$result = $this->ftp->get("php://output", $this->path."$slug.fpz", FTP_BINARY);
		$contents = ob_get_contents();
		ob_end_clean();
		return json_decode(gzuncompress($contents));
	}

}
