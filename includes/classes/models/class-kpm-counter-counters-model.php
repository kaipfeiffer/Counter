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

 require_once KPM_COUNTER_PLUGIN_PATH . 'includes/abstracts/abstract-kpm-counter-model-filtered.php';

 class Kpm_Counter_Counters_Model extends Kpm_Counter_Model_Filtered
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
        'ctag'          => '%d',
        'id'            => '%d',
        'owner'         => '%d',
        'location'      => '%d',
        'parent'        => '%d',
        'identifier'    => '%s',
        'name'          => '%s',
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
    protected static $user_column = 'owner';


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
            `id` bigint UNSIGNED NOT NULL,
            `owner` bigint UNSIGNED NOT NULL,
            `location` bigint UNSIGNED NOT NULL,
            `parent` bigint UNSIGNED DEFAULT NULL,
            `identifier` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `name` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
            `ctag` bigint UNSIGNED DEFAULT 1,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL
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
