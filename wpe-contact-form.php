<?php
/**
 * Plugin Name: WPE Contact form
 * Description: Adds admin-post/admin-ajax support, saving form entries in custom post type, export in CSV feature
 * Version: 2.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Paul Balanche - BuzzBrothers
 * Author URI: https://buzzbrothers.ch
 * Text Domain: wpe-contact-form
 * Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

error_reporting(E_ALL | E_STRICT);

/**
 * Define variables
 *
 */
define( 'WPE_CF_PLUGIN_DIR_PATH',     plugin_dir_path( __FILE__ ) );
define( 'WPE_CF_PLUGIN_URL',          plugins_url('', __FILE__) . '/' );
define( 'WPE_CF_PLUGIN_ASSETS_URL' ,  WPE_CF_PLUGIN_URL . 'assets' . '/' );

define( 'WPE_CF_METADATA_PREFIX' ,  'wpe_contact_form_' );

/**
 * Dependencies
 *
 */
require WPE_CF_PLUGIN_DIR_PATH . "vendor/autoload.php";

/**
 * Initialize plugin
 * 
 */
add_action( 'plugins_loaded', '_wpe_cf_plugin_init' );
function _wpe_cf_plugin_init() {

	// Load Main instance
    WpeContactForm\Main::getInstance();
}

/**
 *  Loads a plugin’s translated strings.
 *
*/
add_action("init", "\_wpe_cf_plugin_load_textdomain");
function _wpe_cf_plugin_load_textdomain() {
    load_plugin_textdomain( 'wpe-contact-form', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}