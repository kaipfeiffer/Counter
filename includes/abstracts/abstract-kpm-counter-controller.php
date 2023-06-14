<?php
if (!defined('WPINC')) {
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

abstract class Kpm_Counter_Controller
{
    /**
     * VARIABLES
     */


    /**
     * $auth
     * 
     * @var Kpm_Counter_Auth
     * Die Auth-Klasse der Route
     */
    protected static $auth;


    /**
     * $auth_class
     * 
     * @var string
     * Die Datenbank-Klasse der Route
     */
    protected static $auth_class = 'Kpm_Counter_Auth';


    /**
     * $class_name
     * 
     * @var string
     * Der Klassenname
     */
    protected static $class_name = __CLASS__;


    /**
     * $data
     * 
     * @var array|object
     * Die Daten für die Route
     */
    protected static $data;


    /**
     * $databse_class
     * 
     * @var string
     * Die Route, die Zieldatei
     */
    protected static $database_class;


    /**
     * $error404
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $error404 = '"%d" ist eine ungültige ID.';


    /**
     * $error404
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $errorMissingData = 'Es wurden keine gültigen Daten übermittelt';


    /**
     * $error404
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $errorOnSave = 'Die Daten konnten nicht gespeichert werden';


    /**
     * $error404
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $errorMulti404 = 'Es konnten keine Einträge gefunden werden';


    /**
     * $route
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $route = 'kpm-counter/v1';


    /**
     * $target
     * 
     * @var string
     * Die Route, die Zieldatei
     */
    protected static $target;


    /**
     * PRIVATE METHODS
     */


    protected static function include_database_class()
    {
        $class =  KPM_COUNTER_PLUGIN_PATH . 'includes/classes/models/class-' . str_replace('_', '-', strtolower(static::$database_class)) . '.php';

        require_once $class;
    }


    /**
     * PUBLIC METHODS
     */


    /**
     * @function authenticate
     * 
     * delete a row of the table
     * 
     * @param   array       associative array with key => value pairs for insertion
     * @return  array|null  if successful, the stored data row
     */
    public static function authenticate()
    {
        $class =  KPM_COUNTER_PLUGIN_PATH . 'includes/singletons/class-' . str_replace('_', '-', strtolower(static::$auth_class)) . '.php';

        require_once $class;

        static::$auth   = static::$auth_class::get_instance('');

        $result   = static::$auth->authenticate();

        // error_log(__CLASS__.'->'.__FUNCTION__.'-> RESULT: '.print_r($result,1));
        // error_log(__CLASS__.'->'.__FUNCTION__.'-> Class_name: '.print_r(static::$class_name,1));
        if ('Access granted:' === $result['message']) {
            static::$data = $result['data'];
            // error_log(__CLASS__.'->'.__FUNCTION__.'-> DATA: '.print_r(static::$data,1));
            return true;
        }
        else{
            // error_log(__CLASS__.'->'.__FUNCTION__.'-> ERROR: '.print_r($result,1));
            return false;
        }

    }


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
        static::include_database_class();
    }


    /**
     * @function delete
     * 
     * delete a row of the table
     * 
     * @param   array       associative array with key => value pairs for insertion
     * @return  array|null  if successful, the stored data row
     */
    public static function delete(WP_REST_Request $request)
    {
    }


    /**
     * @function edit
     * 
     * delete a row of the table
     * 
     * @param   array       associative array with key => value pairs for insertion
     * @return  array|null  if successful, the stored data row
     */
    public static function edit(WP_REST_Request $request)
    {
        $method = $request->get_method();
        switch ($method) {
                // Neuen Eintrag speichern
            case 'POST': {
                    $params = $request->get_params();
                    if ($params) {
                        $result = static::$database_class::create($params);
                        if ($result) {
                            return $result;
                        } else {
                            $error = new \WP_Error(
                                'rest_post_invalid_id',
                                __(static::$errorOnSave, ''),
                                array('status' => 404)
                            );
                            return $error;
                        }
                    } else {
                        $error = new \WP_Error(
                            'rest_post_invalid_id',
                            __(static::$errorMissingData, ''),
                            array('status' => 404)
                        );
                        return $error;
                    }
                    break;
                }
            case 'PUT': {
                    return ['message' => 'PUT Method'];
                    break;
                }
            case 'PATCH': {
                    return ['message' => 'PATCH Method'];
                    break;
                }
        }
    }


    /**
     * @function get
     * 
     * get a row from the table
     * 
     * @param   array       associative array with key => value pairs for insertion
     * @return  array|null  if successful, the stored data row
     */
    public static function get(WP_REST_Request $request)
    {
        // error_log(__CLASS__.'->'.__FUNCTION__.'-> Class_name: '.print_r(static::$class_name,1));
        // error_log(__CLASS__.'->'.__FUNCTION__.'-> STAT-DATA: '.print_r(static::$data,1));
        // error_log(__CLASS__.'->'.__FUNCTION__.'-> SELF-DATA: '.print_r($request['page'],1));
        // error_log(__CLASS__.'->'.__FUNCTION__.'-> REQUEST: '.print_r($request,1));

        $page = $request['page'] ? $request['page'] : 0;
        $page_size = $request['page_size'] ? $request['page_size'] : null;


        if ($request['id']) {
            $result = static::$database_class::read($request['id']);

            if (!$result) {
                $error = new \WP_Error(
                    'rest_post_invalid_id',
                    sprintf(__(static::$error404, ''), $request['id']),
                    array('status' => 404)
                );
                return $error;
            }
        } elseif ($request['ctag']) {
            $result = static::$database_class::user(static::$data->counter_user)::read(['ctag' => ['value' => $request['ctag'],'operator' => '>']],false,$page, $page_size);
        } elseif ($request['filters']) {
            $result = static::$database_class::read($request['filters'], null, $page, $page_size);

            if (!$result) {
                $error = new \WP_Error(
                    'rest_post_invalid_id',
                    __(static::$errorMulti404, ''),
                    array('status' => 404)
                );
                return $error;
            }
        } else {
            $result = static::$database_class::user(static::$data->counter_user)::get($page, $page_size);
        }

        return $result;
    }


    /**
     * @function patch
     * 
     * modify a row of the table
     * 
     * @param   array       associative array with key => value pairs for insertion
     * @return  array|null  if successful, the stored data row
     */
    public static function patch(WP_REST_Request $request)
    {
    }


    /**
     * @function put
     * 
     * save a row to the table
     * 
     * @param   array       associative array with key => value pairs for insertion
     * @return  array|null  if successful, the stored data row
     */
    public static function put(WP_REST_Request $request)
    {
    }


    /**
     * @function create
     * 
     * add a new row to the table
     * 
     * @param   array       associative array with key => value pairs for insertion
     * @return  array|null  if successful, the stored data row
     */
    public static function post(WP_REST_Request $request)
    {
    }
}
