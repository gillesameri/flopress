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
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Flopress
 * @subpackage Flopress/includes
 * @author     Flopress team <team@flopress.io>
 */
class Flopress {

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
	 * The codex definitions.
	 *
	 * @since    1.1.2
	 * @access   private
	 * @var      array    $codex    The list of codexs definitions.
	 */
	private $codexs;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * - common. Flopress usefull methods.
	 * - Flopress_Loader. Orchestrates the hooks of the plugin.
	 * - Flopress_i18n. Defines internationalization functionality.
	 * - Flopress_Admin. Defines all hooks for the admin area.
	 * 
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'FLOPRESS_VERSION' ) ) {
			$this->version = FLOPRESS_VERSION;
		} else {
			$this->version = 'dev';
		}
		$this->plugin_name = 'flopress';
		
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-flopress-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-flopress-i18n.php';

		/**
		 * The composer autoload class
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/vendor/autoload.php';

		/**
		 * The class responsible for defining template rendering.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-flopress-render.php';

		/**
		 * Initiale Flopress Loader
		 */
		$this->loader = new Flopress_Loader();

		/**
		 * Init language
		 */
		$this->set_locale();
		
		/**
		 * Load codexs definitions.
		 */
		$this->codexs = require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codexs.php';
		
		/**
		 * Check if user is logged and root admin
		 */
		if(is_admin()){

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-flopress-admin.php';

			/**
			 * The class responsible for defining definitions.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-flopress-codex.php';

			/**
			 * The class responsible for defining the builder.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-flopress-builder.php';
			
			/**
			 * Define admin hooks for Flopress Editor.
			 */
			$this->define_admin_hooks();

			/**
			 * Define codex definitions.
			 */
			$this->define_definitions();

			/**
			 * The class responsible for defining time-saving features.
			 */
			$this->define_library();

			/**
			 * The class responsible for defining ftp time-saving features.
			 */
			$this->define_ftp_library();

		}

		/**
		 * Define templates only definitions.
		 */
		$this->define_templates_defintions();

		/**
		 * Load compiled Flopress Class with some exceptions for non-blocking use
		 */
		global $pagenow;
		$secure_ajax_requests = array(
			'flopress_load_installed_features',
			'flopress_activate_feature',
			'flopress_deactivate_feature',
			'flopress_load_feature_infos',
			'flopress_load_feature_infos',
			'flopress_source_types'
		);
		
		/**
		 * Disable features loading on important flopress page
		 */
		if(!($pagenow == 'admin.php' && $_GET['page'] == 'flopress') && 
			!($pagenow == 'admin-ajax.php' && array_key_exists('action', $_GET) && in_array($_GET['action'], $secure_ajax_requests)) &&
			!(defined('DISABLE_FLOPRESS') && (DISABLE_FLOPRESS == true))){
				$this->load_active_features();
		}
		
		/**
		 * Add action to remove WordPress notifications
		 */
		if(($pagenow == 'admin.php' && $_GET['page'] == 'flopress')){
			$this->loader->add_action( 'admin_head', $this, 'remove_notifications' );
		}
		
	}

	/**
	 * Remove WordPress notifications
	 *
	 * @since    1.2.4
	 * @access   public
	 */
	public function remove_notifications() {
		remove_all_actions( 'admin_notices' );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 * 
	 * NOT ready now - English only
	 *
	 * Uses the Flopress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Flopress_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Load and register Flopress feature class.
	 *
	 * @since    1.0.6
	 * @access   private
	 */
	private function load_active_features() {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "flopress_feature";
		
		$querystr = "SELECT $table_name.* FROM $table_name WHERE status = 'active'";
		$features = $wpdb->get_results($querystr, OBJECT);
		foreach($features as $feature){
			try {
				if(file_exists(wp_upload_dir()["basedir"].'/flopress/'.$feature->build.'.php')){
					if(include(wp_upload_dir()["basedir"].'/flopress/'.$feature->build.'.php')){
						$classname = $feature->build;
						$settings = unserialize($feature->settings);
						$settings = ($settings) ? $settings : array();
						if(class_exists($classname)){
							new $classname($settings);
						}
					}
				}
			} catch(Throwable $e) {
				if(is_admin()){
					add_action( 'admin_notices', function() use ($feature) {
						?>
						<div class="notice notice-error is-dismissible">
							<p><?php _e( 'Error in Flopress feature '.$feature->title ); ?></p>
						</div>
						<?php
					} );
				}
			} catch(\Exception $e) {
				if(is_admin()){
					add_action( 'admin_notices', function() {
						?>
						<div class="notice notice-error is-dismissible">
							<p><?php _e( 'Error in Flopress feature '.$feature->title ); ?></p>
						</div>
						<?php
					} );
				}
			}
		}
	}


	/**
	 * Check if ajax request, if is a flopress action and check nonce
	 * Inspired from https://developer.wordpress.org/reference/functions/is_protected_ajax_action/
	 *
	 * @since    1.4.2
	 * @access   private
	 */
	private function check_nonces($nonces){
		if ( wp_doing_ajax() && isset( $_REQUEST['action']) && in_array( $_REQUEST['action'], $nonces, true )) {
			if(isset($_REQUEST['_ajax_nonce'])){
				if ( !wp_verify_nonce( $_REQUEST['_ajax_nonce'], $_REQUEST['action']) ) {
					status_header(403);
					wp_send_json_error("Security error. Try reload the page.");
					exit();
				}
			}
			else{
				status_header(403);
				wp_send_json_error("Security error. Try reload the page.");
				exit();
			}
		}
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.9
	 * @access   private
	 */
	private function define_admin_hooks() {

		include_once(ABSPATH . 'wp-includes/pluggable.php');

		if(current_user_can( 'manage_options' )){

			$admin_hooks = array(
				'flopress_category_types',
				'flopress_order_installed_features',
				'flopress_load_installed_features',
				'flopress_activate_feature',
				'flopress_deactivate_feature',
				'flopress_import_local_feature',
				'flopress_export_feature',
				'flopress_delete_feature',
				'flopress_copy_feature',
				'flopress_create_feature',
				'flopress_edit_feature',
				'flopress_load_feature',
				'flopress_load_feature_infos',
				'flopress_build_feature',
				'flopress_save_feature',
				'flopress_load_component',
				'flopress_load_components',
				'flopress_get_settings_feature',
				'flopress_set_settings_feature',
				'flopress_post_picker',
				'flopress_post_by_id',

				'flopress_codex_sources',
				'flopress_source_types',
				'flopress_codex_sources_save',
				'flopress_add_to_source',
				'flopress_get_source_settings',
				'flopress_save_source_settings',
				'flopress_remove_to_source',
				'flopress_filter_remote_features',
				'flopress_import_remote_feature',
			);

			/* Check nonces before define ajax action. If nonce failed, ajax hooks are not registered */
			$this->check_nonces($admin_hooks);
			
			$plugin_admin = new Flopress_Admin( $this->get_plugin_name(), $this->get_version() );
			$plugin_admin->add_nonces($admin_hooks);

			foreach($admin_hooks as $hook){
				$this->loader->add_action( 'wp_ajax_'.$hook, $plugin_admin, $hook );
			}

			$this->loader->add_action( 'admin_menu', $plugin_admin, 'flopress_menu' );
		}
	}

	/**
	 * Register all functions & hooks definitions for core use
	 *
	 * @since    1.1.2
	 * @access   private
	 */
	private function define_definitions() {

		if(current_user_can( 'manage_options' )){
			$plugin_codex = new Flopress_Codex( $this->get_plugin_name(), $this->get_version(), $this->loader, $this->codexs);
		}
	}

	/**
	 * Register library features
	 *
	 * @since    1.4.0
	 * @access   private
	 */
	private function define_library() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-flopress-library.php';

		if(current_user_can( 'manage_options' )){
			$plugin_library = new Flopress_Library( $this->get_plugin_name(), $this->get_version(), $this->loader );
		}
	}

	/**
	 * Register library features
	 *
	 * @since    1.4.0
	 * @access   private
	 */
	private function define_ftp_library() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-flopress-ftp-library.php';
		
		if(current_user_can( 'manage_options' )){
			$plugin_ftp_library = new Flopress_FTP_Library( $this->get_plugin_name(), $this->get_version(), $this->loader );
		}
	}

	/**
	 * Register files for allowed templates functions
	 * 
	 * @since    1.3.1
	 * @access   public
	 */
	private function define_templates_defintions(){
		$plugin_render = new Flopress_Render($this->get_plugin_name(), $this->get_version(), $this->loader, $this->codexs);
	}
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @access   public
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @access   public
	 * @return    Flopress_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @access   public
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
