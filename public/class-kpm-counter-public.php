<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://idevo.de
 * @since      1.0.0
 *
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/public
 * @author     Kai Pfeiffer <kp@idevo.de>
 */
class Kpm_Counter_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	
	/**
	 * Enable the API-Endpoints of this Plugin
	 *
	 * @since    1.0.0
	 */
	public function disable_wp_rest_api_server_var_custom($var) { 
	
		// Example
		return array(
			'/wp-json/contact-form-7/v1/contact-forms/1757/refill',
			'/wp-json/contact-form-7/v1/contact-forms/1757/refill/',
			'/wp-json/contact-form-7/v1/contact-forms/1757/feedback', 
			'/wp-json/contact-form-7/v1/contact-forms/1757/feedback/', 
			'/wp-json/contact-form-7/v1/contact-forms/1757/feedback/schema', 
			'/wp-json/contact-form-7/v1/contact-forms/1757/feedback/schema/'
		); 
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_rest_routes()
	{

		$models = array(
			// 'Kpm_Counter_Adresses_Controller',
			'Kpm_Counter_Login_Controller',
			'Kpm_Counter_Counters_Controller',
			'Kpm_Counter_Readings_Controller',
		);

		foreach ($models as $model) {
			$class =  KPM_COUNTER_PLUGIN_PATH . 'includes/classes/controllers/class-' . str_replace('_', '-', strtolower($model)) . '.php';

			// error_log(__CLASS__.' => '.$class);
			require_once $class;
			$model::register_rest_route();
		}
	}

	/**
	 * init_cors
	 * 
	 * Alte Headers durch neue CORS-Headers ersetzen
	 *
	 * @since    1.0.0
	 */
	public function init_cors()
	{

		// Alte CORS-Headers entfernen
		remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');

		// Eigene CORS-Headers einfügen
		add_filter('rest_pre_serve_request', [__CLASS__, 'set_cors_headers']);
	}

	/**
	 * set the cors-headers
	 *
	 * @since    1.0.0
	 */
	public static function set_cors_headers($value)
	{

		$origin_url = '*';

		// Check if production environment or not
		if ('' === 'production') {
			$origin_url = 'https://linguinecode.com';
		}

		header('Access-Control-Allow-Origin: ' . $origin_url);
		header('Access-Control-Allow-Methods: POST, GET,DELETE,PUT');
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Allow-Headers: *');
		return $value;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Kpm_Counter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kpm_Counter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/kpm-counter-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Kpm_Counter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Kpm_Counter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/kpm-counter-public.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Template for the Single Page-App
	 *
	 * @since    1.0.0
	 */
	public function page_template($page_template)
	{
		if (is_page('kpm-counter')) {
			$page_template = KPM_COUNTER_PLUGIN_PATH . '/templates/page-kpm-counter-index.php';
		}
		return $page_template;
	}
}
