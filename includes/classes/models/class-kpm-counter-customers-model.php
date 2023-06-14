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

 require_once KPM_COUNTER_PLUGIN_PATH . 'includes/abstracts/abstract-kpm-counter-model.php';

 class Kpm_Counter_Customers_Model extends Kpm_Counter_Model
{
    /**
     * VARIABLES 
     */

    /**
     * @var string class_name
     * 
     * diese Variable muss in der abgeleiteten Klasse mit dem Inhalte der Konstanten __CLASS__
     * belegt werden, damit die Instanzen richtig funktionieren
     */
    protected static $class_name = __CLASS__;
    

    /**
     * $columns
     * 
     * @var array
     * Assoziatives Array mit den Spaltennamen und den zugehörigen printf-Platzhaltern
     */
    protected static $columns = [
        'id'        => '%d',
        'ctag'      => '%d',
        'wp_id'     => '%d',
        'adress_id' => '%d',
        'firstname' => '%s',
        'lastname'  => '%s',
        'title'     => '%s',
        'phone'     => '%s',
        'fax'       => '%s',
        'cell'      => '%s',
        'email'     => '%s',
        'loginname' => '%s',
        'login_cnt' => '%s',
        'login_ip'  => '%s',
        'login_date'=> '%s',
        'password'  => '%s',
        'status'    => '%d',
    ];
    

    /**
     * $import_file
     * the name of the file to import
     * 
     * @var string
     */
    protected static $import_file = 'customers.csv';

    /**
     * $primary
     * the name of the primary index
     * 
     * @var string
     */
    protected static $primary = 'id';

    /**
     * $table_name
     * the name of the table without wp-prefix
     * 
     * @var string
     */
    protected static $table_name = 'customers';


    /**
     * PRIVATE METHODS
     */

    /**
     * @function  create_table
     * 
     * creates the table
     */
    protected static function create_table()
    {
        global $wpdb;

        $sql = sprintf(
            'CREATE TABLE `%1$s` (
                `id` int(11) NOT NULL,
                `ctag` bigint(20) NOT NULL DEFAULT "0",
                `wp_id` bigint(20) NOT NULL,
                `adress_id` int(11) NOT NULL DEFAULT "0",
                `firstname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
                `lastname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
                `title` enum("Herr","Frau") COLLATE utf8_unicode_ci DEFAULT NULL,
                `phone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
                `fax` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
                `cell` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
                `email` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
                `loginname` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
                `login_cnt` tinyint(4) NOT NULL DEFAULT "0",
                `login_ip` varchar(24) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
                `login_date` bigint(20) NOT NULL DEFAULT "0",
                `password` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
                `status` int(11) DEFAULT NULL
          ) ENGINE=InnoDB %2$s;',
            static::get_tablename(),
            $wpdb->get_charset_collate()
        );

        dbDelta($sql);
    }

    /**
     * @function  create_indices
     * 
     * creates the indices for the table
     */
    protected static function create_indices()
    {
        global $wpdb;

        $sql = sprintf(
            'ALTER TABLE 
                `%1$s`
                ADD PRIMARY KEY (`%2$s`),
                ADD UNIQUE KEY `loginname` (`loginname`),
                ADD UNIQUE KEY `email` (`email`),
                ADD KEY `nachname` (`nachname`(8)),
                ADD KEY `adress_id` (`adress_id`);',
            static::get_tablename(),
            static::$primary
        );
        $wpdb->query($sql);

        $sql = sprintf(
            'ALTER TABLE
                `%1$s`
            MODIFY `%2$s` bigint UNSIGNED NOT NULL AUTO_INCREMENT',
            static::get_tablename(),
            static::$primary
        );
        $wpdb->query($sql);
    }
}
