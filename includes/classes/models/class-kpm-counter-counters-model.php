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

 require_once KPM_COUNTER_PLUGIN_PATH . 'includes/abstracts/abstract-kpm-counter-model-ctagged.php';

 class Kpm_Counter_Counters_Model extends Kpm_Counter_Model_Ctagged
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
        'ctag'              => '%d',
        'id'                => '%d',
        'counter_owner'             => '%d',
        'counter_type'              => '%d',
        'measure'           => '%d',
        'counter_location'  => '%d',
        'parent'            => '%d',
        'identifier'        => '%s',
        'counter_name'              => '%s',
        // 'created_at'    => '%s',
        // 'updated_at'    => '%s',
    ];


    /**
     * $import_file
     * the name of the file to import
     * 
     * @var string
     */
    protected static $import_file = 'counter.csv';

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
    protected static $table_name = 'counter';


    /**
     * $user_column
     * 
     * die Spalte, die die User-Informationen beinhaltet
     * 
     * @var integer
     */
    protected static $user_column = 'counter_owner';


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
            'CREATE TABLE %1$s (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                counter_owner bigint(20) unsigned NOT NULL,
                counter_type bigint(20) unsigned NOT NULL,
                measure bigint(20) unsigned DEFAULT NULL,
                counter_location bigint(20) unsigned NOT NULL,
                parent bigint(20) unsigned DEFAULT NULL,
                identifier varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                counter_name varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                ctag bigint(20) unsigned DEFAULT 1,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY  (id)
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
            ADD PRIMARY KEY (`%2$s`)',
            static::get_tablename(),
            static::$primary
        );
        // $wpdb->query($sql);

        $sql = sprintf(
            'ALTER TABLE
                `%1$s`
            MODIFY `%2$s` bigint UNSIGNED NOT NULL AUTO_INCREMENT',
            static::get_tablename(),
            static::$primary
        );
        // $wpdb->query($sql);
    }



    /**
     * @function get_defaults
     * 
     * get default values to the table columns
     * 
     * @return array    default values
     */
    protected static function get_defaults()
    {
        if(isset(static::$user)){
            return array(static::$user_column => static::$user);
        }
        return array();
    }
}
