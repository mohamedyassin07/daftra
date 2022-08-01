<?php
/**
 * Daftra
 *
 * @package       DAFTRA
 * @author        Mohamed Yassin
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Daftra
 * Plugin URI:    https://www.daftra.com
 * Description:   unofficial
 * Version:       1.0.0
 * Author:        Mohamed Yassin
 * Author URI:    https://github.com/mohamedyassin07/
 * Text Domain:   daftra
 * Domain Path:   /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Exit if WooCommerce is not installed or being update.
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

// Plugin name
define( 'DAFTRA_NAME',			'Daftra' );

// Plugin slug
define( 'DAFTRA_SLUG',			'daftra' );

// Plugin version
define( 'DAFTRA_VERSION',		'1.0.0' );

// Plugin Root File
define( 'DAFTRA_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'DAFTRA_PLUGIN_BASE',	plugin_basename( DAFTRA_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'DAFTRA_PLUGIN_DIR',	plugin_dir_path( DAFTRA_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'DAFTRA_PLUGIN_URL',	plugin_dir_url( DAFTRA_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once DAFTRA_PLUGIN_DIR . 'includes/class-daftra.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Mohamed Yassin
 * @since   1.0.0
 * @return  object|Daftra
 */
function DAFTRA() {
	return Daftra::instance();
}

DAFTRA();
