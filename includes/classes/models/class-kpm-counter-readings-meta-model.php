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

// require_once KPM_COUNTER_PLUGIN_PATH . 'includes/abstracts/abstract-kpm-counter-model-filtered.php';

require_once KPM_COUNTER_PLUGIN_PATH . 'includes/abstracts/abstract-kpm-counter-model.php';

// class Kpm_Counter_Readings_Model extends Kpm_Counter_Model_Filtered
class Kpm_Counter_Readings_Meta_Model extends Kpm_Counter_Model
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
        'id'            => '%d',
        'reading_id'    => '%d',
        'meta_key'      => '%s',
        'meta_value'    => '%s',
    ];


    /**
     * $import_file
     * the name of the file to import
     * 
     * @var string
     */
    protected static $import_file = 'readings-meta.csv';


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
    protected static $table_name = 'readings_meta';


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
                reading_id bigint(20) NOT NULL,
                meta_key varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                meta_value varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                PRIMARY KEY  (`id`),
                KEY meta_key (meta_key),
                KEY reading_id (reading_id,meta_key)
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
            ADD KEY `meta_key` (`meta_key`),
            ADD KEY `reading_id` (`reading_id`,`meta_key`);',
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
}
