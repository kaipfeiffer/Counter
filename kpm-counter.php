<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://idevo.de
 * @since             1.0.0
 * @package           Kpm_Counter
 *
 * @wordpress-plugin
 * Plugin Name:       Zaehler-Verwaltung
 * Plugin URI:        https://idevo.de
 * Description:       Verwaltet Zählerstände und wertet diese aus
 * Version:           1.0.3
 * Author:            Kai Pfeiffer
 * Author URI:        https://idevo.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kpm-counter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin Name
 */
define( 'KPM_COUNTER_PLUGIN_NAME', 'KPM_COUNTER' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'KPM_COUNTER_VERSION', '1.0.3' );

/**
 * Currently plugin path.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'KPM_COUNTER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );


/**
 * Prefix for options.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'KPM_COUNTER_PREFIX', 'kpm_counter');


/**
 * Currently plugin path.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'KPM_COUNTER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kpm-counter-activator.php
 */
function activate_kpm_counter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kpm-counter-activator.php';
	Kpm_Counter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kpm-counter-deactivator.php
 */
function deactivate_kpm_counter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kpm-counter-deactivator.php';
	Kpm_Counter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_kpm_counter' );
register_deactivation_hook( __FILE__, 'deactivate_kpm_counter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kpm-counter.php';

/**
 * create function array_is_list
 * 
 * überprüft, ob ein angegebenes Array einen numerischen Index hat
 */
if (!function_exists('array_is_list')) {
    function array_is_list(array $arr)
    {
        if ($arr === []) {
            return true;
        }
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kpm_counter() {

	$plugin = new Kpm_Counter();
	$plugin->run();

}
run_kpm_counter();
