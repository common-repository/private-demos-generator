<?php

/**
 * Fired during plugin deactivation
 *
 * @link       orionorigin.com
 * @since      1.0.0
 *
 * @package    Private_Demos_Generator
 * @subpackage Private_Demos_Generator/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Private_Demos_Generator
 * @subpackage Private_Demos_Generator/includes
 * @author     ORION <support@orionorigin.com>
 */
class Private_Demos_Generator_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        wp_clear_scheduled_hook('odg_hourly_checks');
        wp_clear_scheduled_hook('odg_daily_checks');
        wp_clear_scheduled_hook('odg_twicedaily_checks');
	}

}
