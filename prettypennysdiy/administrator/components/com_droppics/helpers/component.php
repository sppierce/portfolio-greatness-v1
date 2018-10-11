<?php
/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');

class droppicsComponentHelper extends JComponentHelper {
    /**
     * Method to set config parameters
     * @param array $datas
     * @return boolean 
     */
    public static function setParams($datas){
            $component = self::getComponent('com_droppics');
            $table	= JTable::getInstance('extension');
            // Load the previous Data
            if (!$table->load($component->id,false)) {
                    return false;
            }
            $d = json_decode($table->params);
            foreach ($datas as $key => $data) {
                $d->$key = $data;
            }
            $table->params = json_encode($d);
            // Bind the data.
            if (!$table->bind($datas)) {       
                    return false;
            }
            // Check the data.
            if (!$table->check()) {       
                    return false;
            }
            // Store the data.
            if (!$table->store()) {
                    return false;
            }
            unset(self::$components['com_droppics']);
            return true;
    }
    
    /**
     * Method to get the version of a component
     * @param string $option
     * @return null
     */
    static public function getVersion(){
        $manifest = self::getManifest();
        if(property_exists($manifest, 'version')){
            return $manifest->version;
        }
        return null;
    }
    
    /**
     * Method to get an object containing the manifest values
     * @param string $option
     * @return object
     */
    static public function getManifest(){
        $component = self::getComponent('com_droppics');
        $table	= JTable::getInstance('extension');
        // Load the previous Data
        if (!$table->load($component->id,false)) {
                return false;
        }
        return json_decode($table->manifest_cache);
    }
    
    public static function bxSliderFilterText($text){
        $text = preg_replace('/<p[^>]*>[\s|&nbsp;|<br>]*<\/p>/', '', $text);
        if($text===JText::_('PLG_DROPPICS_BXSLIDER_IMAGE_ADD_CONTENT')){
            return '';
        }
        return JComponentHelper::filterText($text);
    }
}