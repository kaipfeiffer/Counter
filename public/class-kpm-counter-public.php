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
	public function disable_wp_rest_api_server_var_custom($var)
	{

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

		// error_log(__CLASS__.'->'.__LINE__.' => '.__FUNCTION__);
		$models = array(
			// 'Kpm_Counter_Adresses_Controller',
			'Kpm_Counter_Login_Controller',
			'Kpm_Counter_Counters_Controller',
			'Kpm_Counter_Readings_Controller',
		);

		foreach ($models as $model) {
			$class =  KPM_COUNTER_PLUGIN_PATH . 'includes/classes/controllers/class-' . str_replace('_', '-', strtolower($model)) . '.php';

			// error_log(__CLASS__.'->'.__LINE__.' => '.$class);
			require_once $class;
			$model::register_rest_route();
		}
	}

	/**
	 * request
	 *  
	 * 
	 *
	 * @since    1.0.0
	 */
	public function request($request)
	{
		// if(is_int(strpos($request->request,'kpmcntr'))){
		// 	$request->request	= 'kpmcntr';
		// 	$request->query_vars['page']	= null;
		// 	$request->query_vars['pagename']	= 'kpmcntr';
		// }
		// echo $args .','.$url;
		// echo '<pre>' . print_r($request, 1) . '</pre>';
		return $request;
	}


	/**
	 * pre_get_posts
	 *  
	 * 
	 *
	 * @since    1.0.0
	 */
	public function pre_get_posts($query)
	{
		error_log(__CLASS__ . '->' . __LINE__ . '->' . print_r($query, 1));
		// echo '<pre>' . print_r($query, 1) . '</pre>';
	}

	/**
	 * init
	 * 
	 * 
	 *
	 * @since    1.0.0
	 */
	public function init()
	{
		$options  = get_option(KPM_COUNTER_PREFIX . '_slug_options');

		register_post_type(
			$options['post_type'],
			array(
				'public'          => true,
			)
		);


		if ($options) {
			// Base form, will probably not work because it does nothing with the `news` part
			add_rewrite_rule($options['post_type'] . '/' . $options['app_slug'] . '/.+$', 'index.php?p=' . $options['app_post_id'], 'top');

			// add_rewrite_rule('^/'.$options['app_slug'] . '/?$}', 'index.php?p=' . $options['app_post_id'], 'top');

			// Use this instead if `news` is a page

			if (get_option(KPM_COUNTER_PREFIX . '_flush_plugin_permalinks', false)) {
				delete_option(KPM_COUNTER_PREFIX . '_flush_plugin_permalinks');
				error_log(__CLASS__ . '->' . __LINE__ . '->Flush');
				flush_rewrite_rules();
			}
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
	public function single_template($page_template)
	{
		$options  = get_option(KPM_COUNTER_PREFIX . '_slug_options');
		// if ($options && is_page($options['app_slug'])) {
		global $post;
		error_log(__CLASS__.'->'.__LINE__.'->'.strtolower(KPM_COUNTER_PLUGIN_NAME).' === '.$post->post_type);
		if ($options['post_type'] === $post->post_type) {
			error_log(__CLASS__ . '->' . __LINE__ . '-> is page');
			$page_template = KPM_COUNTER_PLUGIN_PATH . '/templates/single-kpm-counter.php';
		}
		return $page_template;
	}
}
