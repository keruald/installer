<?php

/**
 * Settings
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * @package     Zed
 * @subpackage  Settings
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * The method to call in your objects, to save data.
 */
define("SETTINGS_SAVE_METHOD", "save_to_database");

require_once("page.php");

/**
 * Settings
 *
 * This class maps the Settings format (preferences.xml)
 *
 * It allows to generate settings web forms and handle replies, from a
 * XML document.
 */
class Settings {
    
    /**
     * The file path
     * 
     * @var string
     */
    public $file;
    
    /**
     * A collection of SettingsPage items
     * 
     * @var Array
     */
    public $pages;
    
    /**
     * Initializes a new instance of Settings class
     *
     * @param string $xmlFile The XML document which defines the settings.
     */
    function __construct ($xmlFile) {
        //Opens .xml
        if (!file_exists($xmlFile)) {
            message_die(GENERAL_ERROR, "$xmlFile not found.", "Settings load error");
        }
        $this->file = $xmlFile;
        
        //Parses it
        $this->parse();
    }

    /**
     * Parses XML file
     */
    function parse () {
        //Parses it
        $xml = simplexml_load_file($this->file);
        foreach ($xml->page as $page) {
            //Gets page
            $page = SettingsPage::from_xml($page);
                        
            //Adds to sections array
            $this->pages[$page->id] = $page;
        }
    }
}
?>