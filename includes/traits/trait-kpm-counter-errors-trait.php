<?php
if (!defined('WPINC')) {
    die;
}

trait Kpm_Counter_Errors
{


    /**
     * $error404
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $error404 = '"%d" ist eine ungültige ID.';


    /**
     * $error503
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $error503 = 'Sie sind nicht berechtigt, diesen Datensatz zu bearbeiten';


    /**
     * $error404
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $errorMissingData = 'Es wurden keine gültigen Daten übermittelt';


    /**
     * $error404
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $errorOnSave = 'Die Daten konnten nicht gespeichert werden';


    /**
     * $error404
     * 
     * @var string
     * Die Route, an die Rest-Requests entgegengenommen werden
     */
    protected static $errorMulti404 = 'Es konnten keine Einträge gefunden werden';

}
