<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://the-two.co/
 * @since             1.0.0
 * @package           Videopagetemplate
 *
 * @wordpress-plugin
 * Plugin Name:       VideoPageTemplate
 * Plugin URI:        http://the-two.co/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Amit Rahav
 * Author URI:        http://the-two.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       videopagetemplate
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
define( 'VIDEOPAGETEMPLATE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-videopagetemplate-activator.php
 */
function activate_videopagetemplate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-videopagetemplate-activator.php';
	Videopagetemplate_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-videopagetemplate-deactivator.php
 */
function deactivate_videopagetemplate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-videopagetemplate-deactivator.php';
	Videopagetemplate_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_videopagetemplate' );
register_deactivation_hook( __FILE__, 'deactivate_videopagetemplate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-videopagetemplate.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_videopagetemplate() {

	$plugin = new Videopagetemplate();
	$plugin->run();

}
run_videopagetemplate();
