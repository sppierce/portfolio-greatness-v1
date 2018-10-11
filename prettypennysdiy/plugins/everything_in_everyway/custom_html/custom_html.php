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
 * Custom HTML Plugin.
 */
class PlgEverything_in_everywayCustom_html extends JPlugin
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
    public function onDisplay($context, $id, $params)
    {
        $html = '';
        
        if ($context === $this->_type . '.' . $this->_name)
        {
            // Collect plugin configuration values from module params.
            $plugin_params = new JRegistry($params->get('plugin_config')->params);   
            
            // Get the path for the layout file
            if (version_compare(JVERSION, '3.0.0') == -1)
            {
                // J!2.5
                $path = dirname(__FILE__) . '/tmpl/default.php';
            }
            else
            {
                // J!3.0
                $path = JPluginHelper::getLayoutPath($this->_type, $this->_name, 'default');
            }           
            
            // Render the layout
            ob_start();
            include $path;
            $html .= ob_get_clean();
        }

        return $html;
    }

    /**
     * Generate response for Joomla Ajax Interface.
     *
     * @return  string  The HTML representing form.
     */    
    public function onAjaxCustom_html()
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
