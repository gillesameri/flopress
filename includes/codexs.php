<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

$codexs = array(
    'flopress' => array(
        'title' 		=> 'FloPress',
        'description' 	=> 'FloPress Core : instructions, operators, variables, arithmetic, ...',
        'enabled' 		=> true,
        'disabled' 		=> true,
        'functions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/flopress-function.fpz' ),
        'actions'		=> array( ),
        'filters'		=> array( ),
        'templates'     => array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/flopress-function-template.fpz' ),
        'website'       => 'https://flopress.io/',
        'documentation' => 'https://flopress.io/documentation'
    ),
    'wordpress' => array(
        'title' 		=> 'WordPress',
        'description' 	=> 'Access to most WordPress functions, actions and filters.',
        'enabled' 		=> true,
        'disabled' 		=> true,
        'functions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wordpress-function.fpz' ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wordpress-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wordpress-filter.fpz' ),
        'templates'     => array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wordpress-function-template.fpz'),
        'documentation' => 'https://developer.wordpress.org'
    ),
    'php' => array(
        'title' 		=> 'PHP',
        'description' 	=> 'PHP functions, mainly for manipulating arrays and strings.',
        'enabled' 		=> true,
        'disabled' 		=> true,
        'functions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/php-function.fpz' ),
        'actions'		=> array( ),
        'filters'		=> array( ),
        'templates'     => array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/php-function-template.fpz' ),
        'documentation' => 'https://www.php.net/manual/en/'
	),
    'jetpack' => array(
        'title' 		=> 'Jetpack',
        'description' 	=> 'Jetpack is packed with powerful tools to help you reach your goals',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/jetpack-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/jetpack-filter.fpz' ),
        'templates'     => array( ),
        'website'       => 'https://jetpack.com/',
        'documentation' => 'https://developer.jetpack.com/'
    ),
    'acf' => array(
        'title' 		=> 'Advanced Custom Fields',
        'description' 	=> 'Take full control of your WordPress edit screens & custom field data.',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/acf-function.fpz' ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/acf-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/acf-filter.fpz' ),
        'templates'     => array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/acf-function-template.fpz'),
        'website'       => 'https://www.advancedcustomfields.com/',
        'documentation' => 'https://www.advancedcustomfields.com/resources/'
    ),
    'acf-pro' => array(
        'title' 		=> 'Advanced Custom Fields Pro',
        'description' 	=> 'ACF Pro definitions (flexible, repeater, gallery, options...)',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/acf-pro-function.fpz' ),
        'actions'		=> array( ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/acf-pro-filter.fpz' ),
        'templates'     => array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/acf-pro-function-template.fpz'),
        'website'       => 'https://www.advancedcustomfields.com/',
        'documentation' => 'https://www.advancedcustomfields.com/resources/'
    ),
    'elementor' => array(
        'title' 		=> 'Elementor',
        'description' 	=> 'The World\'s Leading WordPress Page Builder.',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/elementor-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/elementor-filter.fpz' ),
        'templates'     => array( ),
        'website'       => 'https://developers.elementor.com/',
        'documentation' => 'https://developers.elementor.com/'
    ),
    'elementor_pro' => array(
        'title' 		=> 'Elementor Pro',
        'description' 	=> 'Elementor Pro definitions (form api, ...).',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/elementor-pro-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/elementor-pro-filter.fpz' ),
        'templates'     => array( ),
        'website'       => 'https://elementor.com',
        'documentation' => 'https://developers.elementor.com/elementor-pro-apis/'
    ),
    'woocommerce' => array(
        'title' 		=> 'WooCommerce',
        'description' 	=> 'The most customizable eCommerce platform.',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/woocommerce-function.fpz' ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/woocommerce-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/woocommerce-filter.fpz' ),
        'templates'     => array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/woocommerce-function-template.fpz' ),
        'website'       => 'https://woocommerce.com/',
        'documentation' => 'https://docs.woocommerce.com/'
    ),
    'yoast' => array(
        'title' 		=> 'Yoast SEO',
        'description' 	=> 'Optimize your WordPress site with Yoast.',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/yoast-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/yoast-filter.fpz' ),
        'templates'     => array( ),
        'website'       => 'https://yoast.com/',
        'documentation' => 'https://developer.yoast.com/'
    ),
    'wpml' => array(
        'title' 		=> 'WPML',
        'description' 	=> 'WPML makes it easy to build multilingual sites.',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wpml-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wpml-filter.fpz' ),
        'templates'     => array( ),
        'website'       => 'https://wpml.org/',
        'documentation' => 'https://wpml.org/documentation/'
    ),
    'wpsecurityauditlog' => array(
        'title' 		=> 'WP Security Audit Log',
        'description' 	=> 'Real-time Audit Log & Tracking for WordPress.',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wpsecurityauditlog-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wpsecurityauditlog-filter.fpz' ),
        'templates'     => array( ),
        'website'       => 'https://www.wpsecurityauditlog.com/',
        'documentation' => 'https://www.wpsecurityauditlog.com/support-documentation/list-hooks/'
    ),
    'wprocket' => array(
        'title' 		=> 'WP Rocket',
        'description' 	=> 'Make WordPress Load Fast in a Few Clicks.',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wprocket-function.fpz' ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wprocket-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wprocket-filter.fpz' ),
        'templates'     => array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wprocket-function-template.fpz' ),
        'website'       => 'https://wp-rocket.me/',
        'documentation' => 'hhttps://docs.wp-rocket.me/'
    ),
    'metaslider' => array(
        'title' 		=> 'MetaSlider',
        'description' 	=> 'Powerful, customizable and SEO-optimized slideshows.',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( ),
        'actions'		=> array( ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/metaslider-filter.fpz' ),
        'templates'     => array( ),
        'website'       => 'https://www.metaslider.com/',
        'documentation' => 'https://www.metaslider.com/developer-api/'
    ),
    'wpforms' => array(
        'title' 		=> 'WPForms',
        'description' 	=> 'Friendly drag & drop WordPress form builder',
        'enabled' 		=> false,
        'disabled' 		=> false,
        'functions'		=> array( ),
        'actions'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wpforms-action.fpz' ),
        'filters'		=> array( plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codex/wpforms-filter.fpz' ),
        'templates'     => array( ),
        'website'       => 'https://wpforms.com/',
        'documentation' => 'https://wpforms.com/docs/'
    ),
);

return $codexs;
