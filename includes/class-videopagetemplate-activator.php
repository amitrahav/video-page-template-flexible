<?php

/**
 * Fired during plugin activation
 *
 * @link       http://the-two.co/
 * @since      1.0.0
 *
 * @package    Videopagetemplate
 * @subpackage Videopagetemplate/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Videopagetemplate
 * @subpackage Videopagetemplate/includes
 * @author     Amit Rahav <amit.r.89@gmail.com>
 */
class Videopagetemplate_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wp_version;

		if ( $wp_version < 4.7 ) {
			error_log( 'WordPress need to be at least 4.7' );      
			$args = var_export( func_get_args(), true );
			error_log( $args );
			wp_die( 'WordPress need to be at least 4.7 to activate this plugin' );
		}

		if(!is_plugin_active('advanced-custom-fields-pro/acf.php')){
			error_log( 'ACF pro is not activated' );      
			$args = var_export( func_get_args(), true );
			error_log( $args );
			wp_die( 'This plugin must use ACF pro installation. Please install and activate ACF pro before activating this plugin.' );
		}

	}

}
