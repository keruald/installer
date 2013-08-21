<?php

/**
 * Settings: a settings page class
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

require_once("setting.php");

/**
 * This class maps the page XML element, from our Settings XML schema
 *
 * <page id="account" title="Account">
 *     <setting ...>
 *          ...
 *     </setting>
 *     <setting ...>
 *          ...
 *     </setting>
 * <page>
 *
 * It provides method to print a form built from this page and to handle form.
 */
class SettingsPage {
    /**
     * The page ID
     *
     * This property maps the id attribute from the page XML tag
     *
     * @var string the page ID
     */
    public $id;

    /**
     * The page's title
     *
     * This property maps the title attribute from the page XML tag
     * 
     * @var string the page title
     */
    public $title;
    
    /**
     * The settings
     *
     * This property is an array of Setting items and maps the <setting> tags
     * @var Array 
     */
    public $settings = array();
    
    /**
     * Initializes a new instance of SettingsPage class
     *
     * @param string $id the page ID
     */
    function __construct ($id) {
        $this->id = $id;
    }
       
    /**
     * Initializes a settings page from an SimpleXMLElement XML fragment
     * 
     * @param SimpleXMLElement $xml the XML fragment
     * @return SettingsPage the section instance
     */
    static function from_xml ($xml) {
        //Reads attributes
        $id = ''; $title = '';
        foreach ($xml->attributes() as $key => $value) {
            switch ($key) {
                case 'title':
                case 'id':
                    $$key = (string)$value;
                    break;
                
                default:
                    message_die(GENERAL_ERROR, "Unknown attribute: $key = \"$value\"", "Settings error");
            }
        }
        
        //id attribute is mandatory
        if (!$id) {
            message_die(GENERAL_ERROR, "Section without id. Please add id='' in <section> tag", "Story error");
        }
        
        //Initializes new SettingsPage instance
        $page = new SettingsPage($id);
        $page->title = $title;
        
        //Gets settings
        if ($xml->setting) {
            foreach ($xml->setting as $settingXml) {
                $setting = Setting::from_xml($settingXml);
                $page->settings[$setting->key] = $setting;
            }
        }
        
        return $page;
    }
    
    /**
     * Handles form reading $_POST array, set new settings values and saves.
     * 
     * @param Array $errors an array where the errors will be filled
     * @return boolean true if there isn't error ; otherwise, false.
     */
    function handle_form (&$errors = array()) {
        $objects = array();
        
        //Sets new settings values
        foreach ($this->settings as $setting) {
            $value = $_POST[$setting->key];
            
            if ($setting->field == "password" && !$value) {
                //We don't erase passwords if not set
                continue;
            }
            
            //If the setting value is different of current one, we update it
            $currentValue = $setting->get();
            if ($setting->field == "checkbox" || $currentValue != $value) {
                if (!$setting->set($value)) {
                    $errors[] = $setting->lastError ? $setting->lastError : "An error have occured in $setting->key field.";
                }
                if ($setting->object) $objects[] = $setting->object;
            }
        }
        
        //Saves object (when the SETTINGS_SAVE_METHOD save method exists)
        if (count($objects)) {
            $objects = array_unique($objects);
            foreach ($objects as $object) {
                $object = $GLOBALS[$object];
                if (method_exists($object, SETTINGS_SAVE_METHOD)) {
                    call_user_func(array($object, SETTINGS_SAVE_METHOD));
                }
            }
        }
        
    }

}