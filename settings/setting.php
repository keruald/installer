<?php

/**
 * Settings: an individual setting class
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
 * Setting class
 *
 * This class map the <setting> XML block, from our settings XML format
 */
class Setting {
    
    public $key;
    
    //Rendering variables
    public $field;
    public $regExp;
    public $choices;
    
    //get/set variables
    public $object;
    private $property;
    private $method;
    private $handler;
    
    //error variable
    public $lastError;
    
    /**
     * Gets the current setting value
     * 
     * @return string the setting value
     */
    function get () {
        //1 - Evaluates custom handler
        if (array_key_exists('get', $this->handler)) {
            return eval($this->handler['get']);
        }
        
        //2 - Gets object property
        if ($this->object && $property = $this->property) {
            return $GLOBALS[$this->object]->$property;
        }
        
        if ($this->field == "password") {
            //Okay not to have a value for password fields
            return;
        }
        
        message_die(GENERAL_ERROR, "Setting $this->key haven't any get indication. Please set <object> and <property> / or a custom <handler><get></get></handler> block.", "Settings error");
    }
    
    /**
     * Sets a new value
     * 
     * @param $value the setting new value
     * @return boolean true if the setting have been successfully set ; otherwise, false.
     */
    function set ($value) {
        //Validates data
        if ($this->regExp) {
            if (!preg_match('/^' . $this->regExp . '$/', $value)) {
                $this->lastError = "Invalid format for $this->key setting";
                return false;
            }
        }
        
        //Tries to set value
        
        //1 - Evaluates custom handler
        if (array_key_exists('set', $this->handler)) {
            return eval($this->handler['set']);
        }
        
        //2 - Calls object method
        //3 - Sets object property
        if ($this->object) {
            $object = $GLOBALS[$this->object];
            if ($this->method) {
                return call_user_func(array($object, $this->method), $value);
            } elseif ($property = $this->property) {
                $object->$property = $value;
                return true;
            }
        }
        
        message_die(GENERAL_ERROR, "Setting $this->key haven't any set indication. Please set <object> (and wheter <method>, whether <property>) or a custom <handler><set></set></handler> block.", "Settings error");
    }
    
    /**
     * Saves setting
     * 
     * @return mixed the SETTINGS_SAVE_METHOD method value, or false if there's no method call;
     */
    function save () {
        if ($this->object) {
            $object = $GLOBALS[$this->object];
            if (method_exists($object, SETTINGS_SAVE_METHOD)) {
                return call_user_func(array($object, SETTINGS_SAVE_METHOD));
            }
        }
        
        return false;
    }

    /**
     * Initializes a new instance of Setting class from a XML element
     * 
     * @param SimpleXMLElement the xml element to parse
     * @return Setting the setting class
     */
    static function from_xml ($xml) {
        //Reads attributes
        $id = '';
        foreach ($xml->attributes() as $key => $value) {
            switch ($key) {
                case 'id':
                    $id = (string)$value;
                    break;
                
                default:
                    message_die(GENERAL_ERROR, "Unknown attribute: $key = \"$value\"", "Settings error");
            }
        }
        
        //id attribute is mandatory
        if (!$id) {
            message_die(GENERAL_ERROR, "Setting without id. Please add id='' in <setting> tag", "Settings error");
        }
        
        //Initializes new Setting instance
        $setting = new Setting($id);
        
        //Parses simple <tag>value</tag>
        $properties = array('key', 'field', 'object', 'property', 'method', 'regExp');
        foreach ($properties as $property) {
            if ($xml->$property)
                $setting->$property = (string)$xml->$property;
        }
        
        //Parses <handler>
        $setting->handler = array();
        if ($xml->handler) {
            if ($xml->handler->get) $setting->handler['get'] = (string)$xml->handler->get;
            if ($xml->handler->set) $setting->handler['set'] = (string)$xml->handler->set;
        }
        
        //Parses <choices>
        if ($xml->choices) {
            foreach ($xml->choices->choice as $choiceXml) {
                $setting->choices[(string)$choiceXml->key] = (string)$choiceXml->value;
                
            }
        }

        return $setting;
    }
}
?>
