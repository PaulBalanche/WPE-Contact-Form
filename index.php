<?php
/**
* Plugin Name: WPE Contact form
* Description: Adds admin-post/admin-ajax support, saving form entries in custom post type, export in CSV feature (properly developed by WPExtend team)
* Text Domain: wpe-contact-form
* Version: 1.0.4
* Author: Paul Balanche
**/

/**
 * Define namespace
 * You need to update this value !!!
 */
namespace WpeContactForm;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

error_reporting(E_ALL | E_STRICT);

/**
 * Define constants
 *
 */
define( __NAMESPACE__ . '\PLUGIN_DIR_PATH',     plugin_dir_path( __FILE__ ) );
define( __NAMESPACE__ . '\PLUGIN_URL',          plugins_url('', __FILE__) . '/' );
define( __NAMESPACE__ . '\PLUGIN_ASSETS_URL' ,  PLUGIN_URL . 'assets' . '/' );
define( __NAMESPACE__ . '\PLUGIN_TEXTDOMAIN' ,  'wpe-contact-form' );

define( __NAMESPACE__ . '\METADATA_PREFIX' ,  'wpe_contact_form_' );

/**
 * Initialize plugin
 * 
 */
add_action( 'plugins_loaded', __NAMESPACE__ . '\_plugin_init' );
function _plugin_init() {

	// Load text domain
	load_plugin_textdomain( PLUGIN_TEXTDOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages' );

	// Plugin vendor autoloader
	require( PLUGIN_DIR_PATH . 'vendor/autoload.php' );

	// Load Main instance
	Main::getInstance();
}