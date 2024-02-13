<?php
if (!defined('WPINC')) {
    die;
}

/**
 * 
 */

require_once KPM_COUNTER_PLUGIN_PATH . 'includes/singletons/abstract-kpm-counter-singleton.php';
require_once KPM_COUNTER_PLUGIN_PATH . 'includes/singletons/class-kpm-counter-jwt.php';

class Kpm_Counter_Auth extends Kpm_Counter_Singleton
{
    /**
     * @var string singleton_class
     * 
     * diese Variable muss in der abgeleiteten Klasse mit dem Inhalte der Konstanten __CLASS__
     * belegt werden, damit die Instanzen richtig funktionieren
     */
    protected static $singleton_class = __CLASS__;


    /**
     * @var Kpm_Counter_JWT $jwt
     * 
     * diese Variable muss in der abgeleiteten Klasse mit dem Inhalte der Konstanten __CLASS__
     * belegt werden, damit die Instanzen richtig funktionieren
     */
    protected $jwt;

    /**
     * PRIVATE METHODS
     */




    /**
     * PUBLIC METHODS
     */

    /**
     * 
     * 
     * 
     * 
     * @param
     * @return array
     */
    public function authenticate($request)
    {
        // $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['AUTHORIZATION'];
        $authHeader = $request->get_header('authorization');

        $arr = explode(" ", $authHeader);
        $data    = null;

        if (1 < count($arr)) {
            $data = $this->jwt->decode_jwt($arr[1]);
            // error_log(__CLASS__ . '->' . __LINE__ . '->'. print_r($data, 1));
        }
        return $data;
    }

    /**
     * Konstruktor
     */
    protected function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
        $this->jwt          = Kpm_Counter_JWT::get_instance($plugin_name);
    }
}
