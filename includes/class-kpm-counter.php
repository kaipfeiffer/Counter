<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://idevo.de
 * @since      1.0.0
 *
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/includes
 * @author     Kai Pfeiffer <kp@idevo.de>
 */
class Kpm_Counter
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Kpm_Counter_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('KPM_COUNTER_VERSION')) {
			$this->version = KPM_COUNTER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'kpm-counter';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Kpm_Counter_Loader. Orchestrates the hooks of the plugin.
	 * - Kpm_Counter_i18n. Defines internationalization functionality.
	 * - Kpm_Counter_Admin. Defines all hooks for the admin area.
	 * - Kpm_Counter_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-kpm-counter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-kpm-counter-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-kpm-counter-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-kpm-counter-public.php';

		$this->loader = new Kpm_Counter_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Kpm_Counter_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Kpm_Counter_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		if (is_admin()) {
			// error_log(__CLASS__ . '->' . __FUNCTION__ . '-> IS ADMIN');
			$plugin_admin = new Kpm_Counter_Admin($this->get_plugin_name(), $this->get_version());

			// $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
			// $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
			
			$this->loader->add_action('personal_options', $plugin_admin, 'personal_options');
			$this->loader->add_action('personal_options_update', $plugin_admin, 'personal_options_update');
			$this->loader->add_action('edit_user_profile_update', $plugin_admin, 'personal_options_update');
		}
	}




	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		// error_log(__CLASS__.'->'.__LINE__.' => '.'PUBLIC');
		$plugin_public = new Kpm_Counter_Public($this->get_plugin_name(), $this->get_version());


		// error_log(__CLASS__.'->'.__LINE__.' => '.is_callable([$plugin_public, 'register_rest_routes']));
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('rest_api_init', $plugin_public, 'register_rest_routes');
		$this->loader->add_action('rest_api_init', $plugin_public, 'init_cors', 15);
		$this->loader->add_action('init', $plugin_public, 'init', 15);
		$this->loader->add_action('parse_request', $plugin_public, 'request', 15);
		$this->loader->add_filter('single_template', $plugin_public, 'single_template');

		// Enable API-Endpoints if Disable-Rest-Api is activated
		// $this->loader->add_filter('disable_wp_rest_api_server_var', $plugin_public, 'disable_wp_rest_api_server_var_custom');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Kpm_Counter_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
