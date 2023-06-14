<?php

/**
 * Fired during plugin activation
 *
 * @link       https://idevo.de
 * @since      1.0.0
 *
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/includes
 * @author     Kai Pfeiffer <kp@idevo.de>
 */
class Kpm_Counter_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		$models = array(
			'Kpm_Counter_Adresses_Model',
			'Kpm_Counter_Customers_Model',
			'Kpm_Counter_Counters_Model',
			'Kpm_Counter_Readings_Model',
		);

		foreach ($models as $model) {
			$class =  KPM_COUNTER_PLUGIN_PATH . 'includes/classes/models/class-' . str_replace('_', '-', strtolower($model)) . '.php';

			require_once $class;
			$model::setup_table();
		}
	}

}
