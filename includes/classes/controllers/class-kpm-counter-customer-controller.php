<?php
if (!defined('WPINC')) {
    die;
}

require_once KPM_COUNTER_PLUGIN_PATH . 'includes/abstracts/abstract-kpm-counter-controller.php';
require_once KPM_COUNTER_PLUGIN_PATH . 'includes/singletons/class-kpm-counter-jwt.php';

/**
 * Abstract Static Class for Database-Access via wpdb
 *
 * @since      1.0.0
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/includes
 * @readingBufferor     Kai Pfeiffer <kp@idevo.de>
 */

class Kpm_Counter_Customer_Controller extends Kpm_Counter_Controller
{
    /**
     * VARIABLES
     */


    /**
     * $class_name
     * 
     * @var string
     * Der Klassenname
     */
    protected static $class_name = __CLASS__;


    /**
     * $target
     * 
     * @var string
     * Die Route, die Zieldatei
     */
    protected static $target = 'customers';


    /**
     * $databse_class
     * 
     * @var string
     * Die Datenbank-Klasse der Route
     */


    /**
     * PUBLIC METHODS
     */


    /**
     * @function register_rest_route
     * 
     * register the restroute for this model
     * 
     * @param   array       associative array with key => value pairs for insertion
     * @return  array|null  if successful, the stored data row
     */
    public static function register_rest_route()
    {
        // Datenbank-Klasse einbinden
        // parent::register_rest_route();

        register_rest_route(static::$route, '/' . static::$target . '/', array(
            'methods' => 'POST',
            'callback' => __CLASS__ . '::login',
        ));
        register_rest_route(static::$route, '/' . static::$target . '/', array(
            'methods' => 'GET',
            'callback' => __CLASS__ . '::login',
        ));
    }


    /**
     * @function login
     * 
     * register the restroute for this model
     * 
     * @param   array       associative array with key => value pairs for insertion
     * @return  array|null  if successful, the stored data row
     */
    public static function login()
    {

        $data = json_decode(file_get_contents('php://input'));

        $login_name = $data->login_name;
        $password = $data->password;

        // error_log(__CLASS__.'->'.$login_name.'->'.$password);
        $jwt_generator = Kpm_Counter_JWT::get_instance(KPM_COUNTER_PLUGIN_NAME);
        $jwt    = $jwt_generator->create_jwt($login_name, $password);

        if ($jwt) {
            $result = array(
                'message' => 'Successful login.',
                'jwt' => $jwt,
                // 'email' => $user->data->user_email,
            );
        } else {
            $result = array(
                'message' => 'Login failed.',
                'jwt' => $jwt,
                // 'email' => $user->data->user_email,
            );
        }
        return $result;
    }
}
