<?php

/**
 * 
 * 
 * 
 * 
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Kpm_Counter_Singleton
{

    /** 
     * @var Kpm_Counter_Singleton $instance => Instanz des Singletons
     * 
     * @since 1.0.0
     */
    private static $instance;

    /**
     * @var string plugin_name
     */
    protected $plugin_name;

    /**
     * @var string singleton_class
     * 
     * diese Variable muss in der abgeleiteten Klasse mit dem Inhalte der Konstanten __CLASS__
     * belegt werden, damit die Instanzen richtig funktionieren
     */
    protected static $singleton_class = __CLASS__;

    /**
     * Konstruktor
     */
    protected function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
    }

    /**
     * function get_instance
     * 
     * if no instance is present the instance is created
     */
    public static function get_instance($plugin_name)
    {
        if (!static::$instance) {
            static::$instance = new static::$singleton_class($plugin_name);
        }
        return static::$instance;
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone()
    {
    }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }
}
