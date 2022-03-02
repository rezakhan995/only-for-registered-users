<?php

/**
 * Plugin Name:     Only Registered Users
 * Plugin URI:      https://wordpress.org/plugins/only-registered-users/
 * Description:     Redirects all non-logged in users to your website's login form. 
 * Version:         1.0.0
 * Author:          Reza Khan
 * Author URI:      https://www.reza-khan.com/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     only-registered-users
 * Domain Path:     /languages
 */




defined('ABSPATH') || wp_die('No access directly.');

class Ofrusers {

    public static $instance = null;

    public static function init(){
        if( self::$instance === null){
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        add_action('init', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'initialize_modules']);
    }

    public function i18n(){
        load_plugin_textdomain('only-registered-users', false, self::plugin_dir() . 'languages/');
    }

    /**
     * Initialize Modules
     *
     * @since 1.0.0
     */
    public function initialize_modules(){

        do_action( 'ofrusers/before_load' );
        
        require_once self::core_dir() . 'bootstrap.php';

        do_action( 'ofrusers/after_load' );
    }  
    

    static function ofrusers_activate() {
        update_option( "ofrusers_activated", 'yes' );
    }
    
    static function ofrusers_deactivate() {
        delete_option( 'ofrusers_activated' );
    }

    /**
     * Plugin Version
     * 
     * @since 1.0.0
     *
     * @return string
     */
    public static function version(){
        return '1.0.0';
    }

    /**
     * Core Url
     * 
     * @since 1.0.0
     *
     * @return string
     */
    public static function assets_url(){
        return trailingslashit( self::plugin_url() . 'assets' );
    }

    /**
     * Core Directory Path
     * 
     * @since 1.0.0
     *
     * @return string
     */
    public static function assets_dir(){
        return trailingslashit( self::plugin_dir() . 'assets' );
    }

    /**
     * Core Url
     * 
     * @since 1.0.0
     *
     * @return string
     */
    public static function core_url(){
        return trailingslashit( self::plugin_url() . 'core' );
    }

    /**
     * Core Directory Path
     * 
     * @since 1.0.0
     *
     * @return string
     */
    public static function core_dir(){
        return trailingslashit( self::plugin_dir() . 'core' );
    }

    /**
     * Plugin Url
     * 
     * @since 1.0.0
     *
     * @return string
     */
    public static function plugin_url(){
        return trailingslashit( plugin_dir_url( self::plugin_file() ) );
    }

    /**
     * Plugin Directory Path
     * 
     * @since 1.0.0
     *
     * @return string
     */
    public static function plugin_dir(){
        return trailingslashit( plugin_dir_path( self::plugin_file() ) );
    }

    /**
     * Plugins Basename
     * 
     * @since 1.0.0
     *
     * @return string
     */
    public static function plugins_basename(){
        return plugin_basename( self::plugin_file() );
    }
    
    /**
     * Plugin File
     * 
     * @since 1.0.0
     *
     * @return string
     */
    public static function plugin_file(){
        return __FILE__;
    }

}


/**
 * Load Ofrusers plugin when all plugins are loaded
 *
 * @return Ofrusers
 */
function ofrusers(){
    return Ofrusers::init();
}

// Let's go...
ofrusers();

/* Do something when the plugin is activated? */
register_activation_hook( __FILE__, ['Ofrusers', 'ofrusers_activate'] );

/* Do something when the plugin is deactivated? */
register_deactivation_hook( __FILE__, ['Ofrusers', 'ofrusers_deactivate'] );

