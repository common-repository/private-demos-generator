<?php

/**
 * Fired during plugin activation
 *
 * @link       orionorigin.com
 * @since      1.0.0
 *
 * @package    Private_Demos_Generator
 * @subpackage Private_Demos_Generator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Private_Demos_Generator
 * @subpackage Private_Demos_Generator/includes
 * @author     ORION <support@orionorigin.com>
 */
class Private_Demos_Generator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        if (!wp_next_scheduled('odg_hourly_checks')) {
            wp_schedule_event(time(), 'hourly', 'odg_hourly_checks');
        }

        if (!wp_next_scheduled('odg_daily_checks')) {
            wp_schedule_event(time(), 'daily', 'odg_daily_checks');
	}

        if (!wp_next_scheduled('odg_twicedaily_checks')) {
            wp_schedule_event(time(), 'twicedaily', 'odg_twicedaily_checks');
}
    }

}
