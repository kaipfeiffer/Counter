<?php
if (!defined('WPINC')) {
    die;
}

require_once KPM_COUNTER_PLUGIN_PATH . 'includes/abstracts/abstract-kpm-counter-model.php';
/**
 * Abstract Static Class for Database-Access via wpdb
 *
 * @since      1.0.0
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/includes
 * @author     Kai Pfeiffer <kp@idevo.de>
 */

abstract class Kpm_Counter_Model_Filtered extends Kpm_Counter_Model
{
    /**
     * VARIABLES
     */

    /**
     * @var string class
     * 
     * diese Variable muss in der abgeleiteten Klasse mit dem Inhalte der Konstanten __CLASS__
     * belegt werden, damit die Instanzen richtig funktionieren
     */
    protected static $class_name = __CLASS__;

    /**
     * $operators
     * 
     * die Operatoren für die Vergleiche in Where Statements
     * 
     * @var integer
     */
    protected static $operators;

    /**
     * $user
     * 
     * die User-ID
     * 
     * @var integer
     */
    protected static $user;

    /**
     * $user_column
     * 
     * die Spalte, die die User-Informationen beinhaltet
     * 
     * @var integer
     */
    protected static $user_column;


    /**
     * PRIVATE METHODS
     */




    /**
     * PUBLIC METHODS
     */


    /**
     * @function get
     * 
     * gets rows
     * 
     * @param integer                   ID of the required row
     * @return array|object|null|void   the fetched data row
     */
    public static function get($page = 0, $page_size = null)
    {
        global $wpdb;

        $page_size = $page_size ? $page_size : static::$page_size;

        error_log(__CLASS__ . '->' . __FUNCTION__ . '-> Class_name: ' . print_r(static::$class_name, 1));

        $sql = sprintf(
            'SELECT
                    `%1$s`
                FROM
                    `%2$s`
                WHERE
                    `%3$s` = %4$d
                LIMIT
                    %5$d,%6$d;',
            implode('`,`', array_keys(static::$columns)),
            static::get_tablename(),
            static::$user_column,
            static::$user,
            $page * $page_size,
            $page_size
        );

        error_log(__CLASS__ . '->' . __FUNCTION__ . '-> SQL:' . $sql);
        $result = $wpdb->get_results($sql);

        return $result;
    }


    /**
     * @function user
     * 
     */
    public static function user($user)
    {
        static::$user = $user;
        return static::$class_name;
    }


    /**
     * @function read
     * 
     * get the row to committed ID
     * 
     * @param integer                   ID of the required row
     * @return array|object|null|void   the fetched data row
     */
    public static function read($where, $or = false, $page = 0, $page_size = null)
    {
        global $wpdb;

        $operator       = $or ? ' OR ' : ' AND ';
        $pagination     = null;
        $id             = null;

        if ($page_size) {
            $pagination     = sprintf(
                'LIMIT 
                    %1$d,%2$d',
                $page * $page_size,
                $page_size
            );
        }

        $sql = sprintf(
            'SELECT
                    `%1$s`
                FROM
                    `%2$s`
                WHERE
                    `%3$s` = %4$d
                AND
                   ( %5$s )
                    %6$s;',
            implode('`,`', array_keys(static::$columns)),
            static::get_tablename(),
            static::$user_column,
            static::$user,
            implode($operator, static::get_where($where)),
            $pagination
        );

        error_log(__CLASS__ . $sql);
        error_log(__CLASS__ . $wpdb->prepare($sql, array_values($where)));
        // if a single row ist queried
        if ($id) {
            $result = $wpdb->get_row($wpdb->prepare($sql, array_values($where)));
        } else {
            $result = $wpdb->get_results($wpdb->prepare($sql, array_values($where)));
        }
        return $result;
    }


    /**
     * @function set_where
     * 
     * set the values of the where statements
     * 
     * @param   array   columns with the values to set
     * @return  array   list with statements
     */
    protected static function get_where($where,)
    {
        global $wpdb;

        $where_stmts    = array();

        // if an integer is submitted
        if (!is_array($where) && intval($where)) {
            $id     = $where;
            $sql    = sprintf(
                '`%1$s` = %2$s',
                static::$primary,
                static::$columns[static::$primary]
            );
            // Statement mit wpdb->prepare escapen
            $where_stmts[]  = $wpdb->prepare($sql, $id);
        }
        // an array was submitted
        else {
            foreach ($where as $key => $value) {
                $operator    = '=';
                // Falls $value ein Array ist,
                // Operator und Wert auslesen

                error_log(__CLASS__ . '->' . __FUNCTION__ . '-> REQUEST: ' . print_r($value, 1));
                if (is_array($value) && isset($value['operator'])) {
                    $operator   = $value['operator'];
                    $value      = $value['value'];
                }
                $sql    = sprintf(
                    '`%1$s` %2$s %3$s',
                    $key,
                    $operator,
                    static::$columns[$key]
                );
                // Statement mit wpdb->prepare escapen
                $where_stmts[]  = $wpdb->prepare($sql, $value);
            }
        }
        return $where_stmts;
    }
}
