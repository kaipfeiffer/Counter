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
		if (isset($_POST['kpm_counter_user_id'])) {
			$counter_user_id	= intval($_POST['kpm_counter_user_id']);
			update_user_meta($userid, 'kpm_counter_user_id', $counter_user_id);
		}
	}


	/**
	 * Add a field to the User-Settings.
	 *
	 * @since    1.0.0
	 */
	public function personal_options($user)
	{
		$user_id			= $user->ID;
		$counter_user_id	= get_user_meta($user_id, 'kpm_counter_user_id', true);

		// Wenn der Customer-Controller existier
		if (class_exists('Kpm_Counter_Customers_Model')) {
			$counter_user_accounts	= Kpm_Counter_Customers_Model::get();
			error_log(__CLASS__ . '->' . __FUNCTION__ . '->' . print_r($counter_user_accounts, 1));
?>
			<tr>
				<th scope="row">
					<?php echo __('Zähler-Nutzer-ID', 'kpm-counter'); ?>
				</th>
				<td>
					<select name="kpm_counter_user_id">
						<?php foreach ($counter_user_accounts as $row) { ?>
							<option value="<?php echo $row->id; ?>" <?php echo ($row->id === $counter_user_id ? 'selected="selected"' : ''); ?>>
								<?php echo $row->loginname; ?>
							</option>
						<?php } ?>
					</select>
				</td>
			</tr>
<?php

		}
	}
}
