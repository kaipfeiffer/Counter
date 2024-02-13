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
class Kpm_Counter_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$models = array(
			'Kpm_Counter_Adresses_Model',
			'Kpm_Counter_Customers_Model',
			'Kpm_Counter_Counters_Model',
			'Kpm_Counter_Readings_Model',
			'Kpm_Counter_Readings_Meta_Model',
		);

		foreach ($models as $model) {
			$class =  KPM_COUNTER_PLUGIN_PATH . 'includes/classes/models/class-' . str_replace('_', '-', strtolower($model)) . '.php';

			require_once $class;
			$model::setup_table();
		}


		$options  = get_option(
			KPM_COUNTER_PREFIX . '_slug_options',
			array(
				'app_slug' => 'app',
				'post_type' => 'zhlr',
				'app_post_id'	=> null
			)
		);

		if (!$options['app_post_id']) {
			$options['post_type']	= post_type_exists($options['post_type']) ? strtolower(KPM_COUNTER_PLUGIN_NAME) : $options['post_type'];

			$pageByPostNameQuery = new WP_Query(array(
				'post_type' =>  $options['post_type'],
				'name'      => $options['post_name'],
			));

			// error_log(__CLASS__ . '->' . __LINE__ . '-> query:' . print_r($pageByPostNameQuery->request, 1));
			if (!$pageByPostNameQuery->have_posts()) {
				$page_id = wp_insert_post(
					array(
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_author'    => 1,
						'post_title'     => 'Zähler-App',
						'post_name'      => $options['post_name'],
						'post_status'    => 'publish',
						'post_content'   => '',
						'post_type'      => $options['post_type'],
					)
				);
				$options['app_post_id']	=  $page_id;
			} else {
				while ($pageByPostNameQuery->have_posts()) {
					$pageByPostNameQuery->the_post();
					$options['app_post_id']	= get_the_ID();
				}
			}

			// these options get called by init-hooks, so they have to be autoloaded
			update_option(
				KPM_COUNTER_PREFIX . '_slug_options',
				$options,
				true
			);
			update_option(KPM_COUNTER_PREFIX . '_flush_plugin_permalinks', true);
		}
	}
}
