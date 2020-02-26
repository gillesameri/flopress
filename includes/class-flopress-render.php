<?php

/**
 * The render-specific functionality of the plugin.
 *
 * @link       https://flopress.io
 * @since      1.1.6
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 */

/**
 * The render-specific functionality of the plugin.
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 * @author     Flopress team <team@flopress.io>
 */


class Flopress_Render {

	/**
	 * The list of functions.
	 *
	 * @since    1.1.6
	 * @access   private
	 * @var      array    $files    The list of files.
	 */
    private static $fcts = array();

	/**
	 * The twig instance.
	 *
	 * @since    1.1.6
	 * @access   private
	 * @var      object    $twig    The twig instance.
	 */
    private static $twig = false;

	/**
	 * The loader.
	 *
	 * @since    1.1.6
	 * @access   private
	 * @var      string    $loader    The loader.
	 */
    private static $loader = false;

	/**
	 * The static class flag.
	 *
	 * @since    1.1.6
	 * @access   private
	 * @var      array    $components   The list of components.
	 */
	private static $initialized = false;
	
	private $codexs;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.4
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $loader, $codexs ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->codexs = $codexs;

		$loader->add_filter( 'flopress/template/functions', $this, 'add_templates_functions' );
	}

	/**
	 * Show contact notices content
	 * 
	 * @since    1.4
	 * @access   public
	 */
	public function add_templates_functions($functions) {
		foreach($this->codexs as $kc => $codex){
			if(array_key_exists('templates', $codex)){
				if($codex['enabled'] || (get_option('flopress_codex_sources') && in_array($kc, get_option('flopress_codex_sources')))){
					foreach($codex['templates'] as $kf => $functionList){
						if(file_exists($functionList)){
							$str_data = file_get_contents($functionList);
							$file_data = (array) json_decode(gzuncompress($str_data));
							$functions = array_merge($functions, $file_data);
						}
					}
				}
			}
		}
		return $functions;
	}

	/**
	 * Static initialization
	 *
	 * @since    1.1.6
	 */
    private static function initialize()
    {
        if (self::$initialized)
            return;

		$data = apply_filters('flopress/template/functions', array());
        self::$fcts = $data;

        self::$loader = new \Twig\Loader\ArrayLoader(array());
        self::$twig = new Twig_Environment(self::$loader, array(
            'debug' => true,
		));
		self::$twig->addExtension(new Twig_Extension_Debug());
        self::$twig->addFunction( new Twig_SimpleFunction('fp_function', function ($function, $args=array()) {
            if(in_array($function,self::$fcts)){
                return call_user_func_array($function, $args);
            }
            else{
                return false;
            }
		}));
		
		foreach(self::$fcts as $fct){
			if($fct != 'func_get_args'){
				self::$twig->addFunction( new Twig_SimpleFunction($fct, function ()  use ($fct) {
					return call_user_func_array($fct, func_get_args());
				}));
			}
		}

        self::$initialized = true;
    }

	/**
	 * Static method for Flopress dynamic class
	 *
	 * @since    1.1.6
	 */
    public static function template($text,$args=array())
    {
        self::initialize();
        $args = (!is_array($args)) ? array() : $args;
        $template = self::$twig->createTemplate($text);
        return $template->render($args);
    }


}
