<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              orionorigin.com
 * @since             1.0.0
 * @package           Private_Demos_Generator
 *
 * @wordpress-plugin
 * Plugin Name:       Private Demos Generator
 * Plugin URI:        orionorigin.com
 * Description:       Allows to clone a wordpress website and send new credentials to the customer
 * Version:           1.0
 * Author:            ORION
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       odg
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PDG_VERSION', '1.0' );
define( 'PDG_URL', plugins_url('/', __FILE__) );
define( 'PDG_DIR', dirname(__FILE__) );
define( 'PDG_MAIN_FILE', 'private-demos-generator/pdg.php' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-odg-activator.php
 */
function activate_private_demos_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-odg-activator.php';
	Private_Demos_Generator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-odg-deactivator.php
 */
function deactivate_private_demos_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-odg-deactivator.php';
	Private_Demos_Generator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_private_demos_generator' );
register_deactivation_hook( __FILE__, 'deactivate_private_demos_generator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-odg.php';
if(!function_exists("o_admin_fields"))
	require plugin_dir_path(__FILE__) . 'includes/utils.php';
if(!function_exists("odg_mail"))
	require plugin_dir_path(__FILE__) . 'includes/functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_private_demos_generator() {

	$plugin = new Private_Demos_Generator();
	$plugin->run();

}
run_private_demos_generator();
