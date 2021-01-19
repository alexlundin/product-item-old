<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://alexlundin.com
 * @since             1.0.0
 * @package           Product_Item
 *
 * @wordpress-plugin
 * Plugin Name:       Product Item
 * Plugin URI:        https://alexlundin.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Александр Лундин
 * Author URI:        https://alexlundin.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       product-item
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PRODUCT_ITEM_DIR_URL', plugin_dir_url(__FILE__) );
define( 'PRODUCT_ITEM_DIR_PATH', plugin_dir_path(__FILE__) );
define( 'PRODUCT_ITEM_PUBLIC_DIR_URL', PRODUCT_ITEM_DIR_URL . 'public/' );
define( 'PRODUCT_ITEM_VERSION', '1.0.0' );

$product_item_instances = [];
$product_item_current_rendering_item = [];

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-product-item-activator.php
 */
function activate_product_item() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-product-item-activator.php';
	Product_Item_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-product-item-deactivator.php
 */
function deactivate_product_item() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-product-item-deactivator.php';
	Product_Item_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_product_item' );
register_deactivation_hook( __FILE__, 'deactivate_product_item' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-product-item.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_product_item() {

	$plugin = new Product_Item();
	$plugin->run();

}
run_product_item();
