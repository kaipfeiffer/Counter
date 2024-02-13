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

class Kpm_Counter_Adresses_Model extends Kpm_Counter_Model
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
        'location'  => '%s',
        'company'   => '%s',
        'street'    => '%s',
        'misc'      => '%s',
        'zipcode'   => '%s',
        'city'      => '%s',
        'country'   => '%s',
        'phone_company' => '%s',
        'fax_company'   => '%s',
        // 'created'       => '%s',
    ];
    

    /**
     * $import_file
     * the name of the file to import
     * 
     * @var string
     */
    protected static $import_file = 'adresses.csv';

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
    protected static $table_name = 'adresses';


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
                `location` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
                `company` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
                `street` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
                `misc` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
                `zipcode` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
                `city` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
                `country` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
                `phone_company` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
                `fax_company` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
                `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
                ADD PRIMARY KEY (`%2$s`);',
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
