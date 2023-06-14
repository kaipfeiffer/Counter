<?php
if (!defined('WPINC')) {
    die;
}

/**
 * Class for sanitation
 *
 * @since      1.0.0
 * @package    Kpm_Counter
 * @subpackage Kpm_Counter/includes/classes
 * @author     Kai Pfeiffer <kp@idevo.de>
 */

class Kpm_Counter_Sanitize{

    /**
     * PUBLIC METHODS
     */


    /**
     * @function sanitize_int
     * 
     * sanitizes an integer
     * 
     * @param   int|string $value
     * @return  int 
     */
    static function sanitize_int($value){
        return intval($value);
    }
} 