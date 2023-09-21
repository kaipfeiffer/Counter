<?php
if (!defined('WPINC')) {
    die;
}

/**
 * 
 */

require_once KPM_COUNTER_PLUGIN_PATH . 'includes/singletons/abstract-kpm-counter-singleton.php';
require_once KPM_COUNTER_PLUGIN_PATH . 'vendor/php-jwt/src/JWT.php';
require_once KPM_COUNTER_PLUGIN_PATH . 'vendor/php-jwt/src/Key.php';
require_once KPM_COUNTER_PLUGIN_PATH . 'vendor/php-jwt/src/SignatureInvalidException.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Kpm_Counter_JWT extends Kpm_Counter_Singleton
{

    /**
     * @var string $algorithm
     * 
     * der Algorithmus zum (De-)Kodieren
     */
    protected $algorithm = 'HS512';


    /**
     * @var string singleton_class
     * 
     * diese Variable muss in der abgeleiteten Klasse mit dem Inhalte der Konstanten __CLASS__
     * belegt werden, damit die Instanzen richtig funktionieren
     */
    protected static $singleton_class = __CLASS__;


    /**
     * $secret
     * 
     * @var string
     * Die Route, die Zieldatei
     */
    protected $secret = 'gn2gUVQzoya4qws5tVum1vfudp7otbc';



    /**
     * PRIVATE METHODS
     */



    /**
     * @function get_secret
     * 
     * register the restroute for this model
     * 
     * @param   array       associative array with key => value pairs for insertion
     * @return  array|null  if successful, the stored data row
     */
    protected function get_secret()
    {
        // Falls vorhanden $secret mit individuellen Schlüsseln aus der Wordpress-Installation belegen
        if (defined('AUTH_KEY') && defined('SECURE_AUTH_KEY')) {
            $this->secret = AUTH_KEY . SECURE_AUTH_KEY;
        }
        return $this->secret;
    }


    /**
     * PUBLIC METHODS
     */


    /**
     * @function create_jwt
     * 
     * register the restroute for this model
     * 
     * @param   array       associative array with key => value pairs the payload
     * @param   integer     Gültigkeit in Sekunden
     * @param   string      Algorithmus für den Hash 
     * @return  string      the token
     */
    public function create_jwt($data, $duration = 60 * 60 * 24 * 365)
    {
        $algorithm = $this->algorithm;

        if ($data) {
            $secret_key = $this->get_secret();
            $iat        = time();           // issued at
            $nbf        = $iat; //not before in seconds
            $exp        = $iat + $duration; // expire time in seconds
            $token = array(
                'iss' => get_bloginfo('url'),
                'iat' => $iat,
                'nbf' => $nbf,
                'exp' => $exp,
                'data' => $data
            );

            $jwt = JWT::encode($token, $secret_key, $algorithm);

            return $jwt;
        } else {
            return null;
        }
    }


    /**
     * @function decode_jwt
     * 
     * register the restroute for this model
     * 
     * @param   string      $jwt
     * @return  string      the token
     */
    public function decode_jwt($jwt)
    {
        $result = array();
        try {
            $algorithm  = $this->algorithm;
            $secret_key = $this->get_secret();

            // error_log(__CLASS__ . '->' . print_r($jwt,1));
            $decoded = JWT::decode($jwt, new Key($secret_key, $algorithm));

            // Access is granted. Add code of the operation here 

            $result = array(
                "message" => "Access granted:",
                "data" => $decoded->data
            );
        } catch (Exception $e) {

            $result = array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            );
        }

        return $result;
    }
}
