<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Abstract Static Class for Database-Access via wpdb
 *
 * @since      1.0.0
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/includes
 * @author     Kai Pfeiffer <kp@idevo.de>
 */

require_once KPM_COUNTER_PLUGIN_PATH . 'includes/abstracts/abstract-kpm-counter-controller.php';

class Kpm_Counter_Readings_Controller extends Kpm_Counter_Controller
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
     * $databse_class
     * 
     * @var string
     * Die Datenbank-Klasse der Route
     */
    protected static $database_class = 'Kpm_Counter_Readings_Model';

     
    /**
     * $error404
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $error404 = 'Es gibt keinen Zählerstand mit der ID `%d`.';

    
    /**
     * $target
     * 
     * @var string
     * Die Route, die Zieldatei
     */
    protected static $target = 'readings';


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
        parent::register_rest_route();

        // error_log(__CLASS__.'->'.__FUNCTION__.'-> CALLABLE: '.is_callable(__CLASS__ . '::get'));
        register_rest_route( static::$route, '/'.static::$target.'/', array(
            'methods' => 'GET',
            'callback' => __CLASS__ . '::get',
            'permission_callback' =>  __CLASS__ . '::authenticate',
          ) );
        register_rest_route(static::$route, '/' . static::$target . '/(?P<id>\d+)', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => __CLASS__ . '::get',
            'permission_callback' =>  __CLASS__ . '::authenticate',
        ));
        register_rest_route(static::$route, '/' . static::$target . '/ctag/(?P<ctag>\d+)', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => __CLASS__ . '::get',
            'permission_callback' =>  __CLASS__ . '::authenticate',
        ));
        register_rest_route(static::$route, '/' . static::$target . '/', array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => __CLASS__ . '::edit',
            'permission_callback' =>  __CLASS__ . '::authenticate',
        ));
    }
}