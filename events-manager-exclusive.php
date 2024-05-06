<?php
/*
Plugin Name: Events Manager Exclusive
Plugin URI: #
Description: Event Manager Exclusive plugin for managing exclusive events.
Version: 1.0.0
Author: Md. Abdul Hannan
Author URI: #
Text Domain: events-exclusive
Domain Path: /languages
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define required constants
 */
define( 'EVME_VER', '1.0.0' );
define( 'EVME_URL', plugins_url('', __FILE__) );
define( 'EVME_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EVME_URL_ASSETS', EVME_URL . '/assets' );

/**
 * Autoload require
 */
require_once __DIR__ . "/vendor/autoload.php";


class Evme_Events_Manager_Exclusive {
    /**
     * Properties
     */
    private static $instance = null;

    function __construct() {
        // admin enqueue 
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_frontend_assets'));
        // wp enqueue 
        add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_frontend_assets'));

        // load features 
        add_action('init', array($this, 'initialize_features'));

        // funnctions 
        new EvmeManager\Events\Functions();
    }

    /**
     * Instance
     */
    public static function get_instance() {
        if ( self::$instance == null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize features
     */
    public function initialize_features() {
        load_plugin_textdomain( 'events-exclusive', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Enqueue frontend assets
     */
    public function admin_enqueue_frontend_assets( ) {
        wp_enqueue_style( 'evme-style', EVME_URL_ASSETS . '/css/admin.css', array(), EVME_VER, 'all' );
        wp_enqueue_script( 'evme-script', EVME_URL_ASSETS . '/js/admin.js', array( 'jquery' ), EVME_VER, true );
    }

    /**
     * Frontend assets
     */
    public function wp_enqueue_frontend_assets( ) {
        wp_enqueue_style( 'evme-style-frontend', EVME_URL_ASSETS . '/css/style.css', array(), EVME_VER, 'all' );
        wp_enqueue_script( 'evme-script-frontend', EVME_URL_ASSETS . '/js/script.js', array( 'jquery' ), EVME_VER, true );
    }

}

/**
 * Instantiate
 */
Evme_Events_Manager_Exclusive::get_instance();