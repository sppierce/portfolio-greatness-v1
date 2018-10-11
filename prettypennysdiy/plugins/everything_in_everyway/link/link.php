<?php

/**
 * @package     pwebbox
 * @version 	2.0.0
 *
 * @copyright   Copyright (C) 2015 Perfect Web. All rights reserved. http://www.perfect-web.co
 * @license     GNU General Public Licence http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die;

/**
 * Link Plugin.
 */
class PlgEverything_in_everywayLink extends JPlugin
{

    /**
     * Constructor
     *
     * @param   object  &$subject  The object to observe
     * @param   array   $config    An optional associative array of configuration settings.
     *                             Recognized key values include 'name', 'group', 'params', 'language'
     *                             (this list is not meant to be comprehensive).
     *
     * @since   1.5
     */
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);

        // Load the language file on instantiation.
        $this->loadLanguage('plg_' . $this->_type . '_' . $this->_name . '.site');
    }

    /**
     * Initialise plugin. Load all required JS, CSS and other dependences
     *
     * @param   integer $id  		The id of module instance.
     * @param	object	$params 	The JRegistry object with module instance options
     *
     * @return  boolean	True on success, false otherwise
     */
    public function onInit($context, $id, $params)
    {
        if ($context === $this->_type . '.' . $this->_name)
        {
            return true;
        }
        return null;
    }

    /**
     * Gets the output HTML
     *
     * @param   integer $id  		The id of module instance.
     * @param	object	$params 	The JRegistry object with module instance options
     *
     * @return  string  The HTML to be embedded in popup.
     */
    public function onDisplay($context, $id, $params) {}

    /**
     * Generate response for Joomla Ajax Interface.
     *
     * @return  string  The HTML representing form.
     */    
    public function onAjaxLink()
    {
        $jinput = JFactory::getApplication()->input;
        
        require_once JPATH_ROOT.'/modules/mod_pwebbox/pluginhelper.php';       
        
        // Check if method is called in context of Pweb server communication.
        if ($jinput->get('pwebServerCommunication', false))
        {
            return modPwebboxPluginHelper::setServerResponse($jinput->get('data', '', 'array')); 
        }
        
        return modPwebboxPluginHelper::getParams($this, $this->_type, $this->_name);
    }      
}
