<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://flopress.io
 * @since      1.1.2
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
class Flopress_Codex {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.1.2
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.1.2
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The codex definitions.
	 *
	 * @since    1.1.2
	 * @access   private
	 * @var      array    $version    The list of codexs definitions.
	 */
	private $codexs;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.2
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $loader, $codexs ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->codexs = $codexs;

		$loader->add_filter( 'flopress/add/codex', 		$this, 'flopress_codexs' );
		$loader->add_filter( 'flopress/add/functions', 	$this, 'flopress_functions_definitions' );
		$loader->add_filter( 'flopress/add/actions', 	$this, 'flopress_actions_definitions' );
		$loader->add_filter( 'flopress/add/filters', 	$this, 'flopress_filters_definitions' );
	}

	/**
	 * Register flopress codex.
	 *
	 * @since    1.1.2
	 */
	public function flopress_codexs($sources){
		foreach($this->codexs as $kc => $codex){
			unset($codex['functions'],$codex['actions'],$codexs['filters'],$codexs['templates']);
			$sources[$kc] = $codex;
		}
		return $sources;
	}

	/**
	 * Register enabled functions definitions.
	 *
	 * @since    1.1.2
	 */
	public function flopress_functions_definitions($functions){
		foreach($this->codexs as $kc => $codex){
			if(array_key_exists('functions', $codex)){
				if($codex['enabled'] || (in_array($kc, get_option('flopress_codex_sources')))){
					foreach($codex['functions'] as $kf => $functionList){
						$str_data = file_get_contents($functionList);
						$file_data = (array) json_decode(gzuncompress($str_data));
						$functions = array_merge($functions, $file_data);
					}
				}
			}
		}
		return $functions;
	}

	/**
	 * Register enabled actions.
	 *
	 * @since    1.1.2
	 */
	public function flopress_actions_definitions($actions){
		foreach($this->codexs as $kc => $codex){
			if(array_key_exists('actions', $codex)){
				if($codex['enabled'] || (in_array($kc, get_option('flopress_codex_sources')))){
					foreach($codex['actions'] as $kf => $actionList){
						$str_data = file_get_contents($actionList);
						$file_data = (array) json_decode(gzuncompress($str_data));
						$actions = array_merge($actions, $file_data);
					}
				}
			}
		}
		return $actions;
	}

	/**
	 * Register enabled filters.
	 *
	 * @since    1.1.2
	 */
	public function flopress_filters_definitions($filters){
		foreach($this->codexs as $kc => $codex){
			if(array_key_exists('filters', $codex)){
				if($codex['enabled'] || (in_array($kc, get_option('flopress_codex_sources')))){
					foreach($codex['filters'] as $kf => $filterList){
						$str_data = file_get_contents($filterList);
						$file_data = (array) json_decode(gzuncompress($str_data));
						$filters = array_merge($filters, $file_data);
					}
				}
			}
		}
		return $filters;
	}

}
