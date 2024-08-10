<?php
$class_path =  KPM_COUNTER_PLUGIN_PATH . 'includes/classes/models/class-kpm-counter-customers-model.php';
if (file_exists($class_path)) {
	require_once $class_path;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://idevo.de
 * @since      1.0.0
 *
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/admin
 * @author     Kai Pfeiffer <kp@idevo.de>
 */
class Kpm_Counter_Admin
{
	private static $admin_menu_slug	= 'kpm_counter_admin_menu';

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/kpm-counter-admin.css', array(), $this->version, 'all');
	}


	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/kpm-counter-admin.js', array('jquery'), $this->version, false);
	}


	/**
	 * Update th field-value to the User-Settings.
	 *
	 * @since    1.0.0
	 */
	public function personal_options_update($userid)
	{
		error_log(__CLASS__ . '->' . __FUNCTION__ . '->' . __LINE__ . '-> ID:' . print_r($_POST, 1));
		if (current_user_can('manage_options')) {
			if (isset($_POST['kpm_counter_user_id'])) {
				$counter_user_id	= intval($_POST['kpm_counter_user_id']);
				update_user_meta($userid, 'kpm_counter_user_id', $counter_user_id);
			}
			if (isset($_POST['kpm_counter_is_user'])) {
				$is_user	= $_POST['kpm_counter_is_user'];

				include_once KPM_COUNTER_PLUGIN_PATH . 'includes/classes/models/class-kpm-counter-customers-model.php';
				include_once KPM_COUNTER_PLUGIN_PATH . 'includes/classes/models/class-kpm-counter-adresses-model.php';
				$user	= Kpm_Counter_Customers_Model::read(array('wp_id' => $userid));
				if (!$user) {
					$wp_user	= get_user_by('id', $userid);
					$user_data	= array(
						'lastname'	=> sanitize_text_field($_POST['last_name']),
						'firstname'	=> sanitize_text_field($_POST['first_name']),
						'email'		=> sanitize_email($_POST['email']),
						'loginname'	=> $wp_user->user_login,
						'wp_id'		=> intval($userid),
					);
					$user_data	= Kpm_Counter_Customers_Model::create($user_data);

					$adress_data	= Kpm_Counter_Adresses_Model::create(['location' => '']);
					$user_data['adress_id']	= $adress_data['id'];
					$user_data	= Kpm_Counter_Customers_Model::update($user_data);
					// $user_data	= array_merge($user_data,$wp_user);
				}
				error_log(__CLASS__ . '->' . __LINE__ . '|' . $userid . '->' . print_r($user, 1) . '->' . print_r($user_data, 1) . '->' . print_r($wp_user, 1));
				update_user_meta($userid, 'kpm_counter_is_user', $is_user);
			} else {
				delete_user_meta($userid, 'kpm_counter_is_user');
			}
		}
	}


	/**
	 * Add a field to the User-Settings.
	 *
	 * @since    1.0.0
	 */
	public function personal_options($user)
	{
		$user_id	= $user->ID;
		$is_user	= get_user_meta($user_id, 'kpm_counter_is_user', true);
		// Wenn der Customer-Controller existier
		if (class_exists('Kpm_Counter_Customers_Model')) {
?>
			<tr>
				<th scope="row">
					<label for="kpm_counter_is_user">
						<?php echo __('Zähler-Nutzer', 'kpm-counter'); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" id="kpm_counter_is_user" name="kpm_counter_is_user" <?php echo ($is_user ? ' checked="checked" ' : '') ?> <?php echo (current_user_can('manage_options') ?  '' : ' disabled="disabled" ') ?>>
					<?php
					if ($is_user) {
						$options	= get_option(KPM_COUNTER_PREFIX . '_slug_options', array('app_slug' => '','post_type' => strtolower(KPM_COUNTER_PLUGIN_NAME)));
						$app_post	= get_post($options['app_post_id']);
					?>
						<a href="<?php echo $app_post->guid; ?>"><?php echo __('Hier Clicken um die Zaehler-App anzuzeigen', 'kpm-counter') ?></a>
					<?php
					} else {
						echo __('Wenn diese Schaltfläche aktiviert ist, darf der Nutzer die App zur Verwaltung von Zählerständen nutzen', 'kpm-counter');
					}
					?>
				</td>
			</tr>
		<?php

		}
	}
}
