<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: abstract.php 1400 2009-03-30 08:45:17Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML Abstract view class for the component frontend
 *
 * @static
 */
JLoader::register('JEventsDefaultView',JEV_VIEWS."/default/abstract/abstract.php");

class JEventsFloatView extends JEventsDefaultView
{
    var $jevlayout = null;

    function __construct($config = null)
    {
        parent::__construct($config);

        $this->jevlayout="float";

        $this->addHelperPath(dirname(__FILE__)."/../helpers/");
        $this->addHelperPath( JPATH_BASE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.JEV_COM_COMPONENT.'/'."helpers");
        $document = JFactory::getDocument();
        $params = JComponentHelper::getParams(JEV_COM_COMPONENT);
        $browser = JBrowser::getInstance();
        $browserType = $browser->getBrowser();
        $browserVersion = $browser->getMajor();

        // Library will load what we need for now.
        //JEVHelper::componentStylesheet($this);

        JLoader::register('JevIsotope', JPATH_LIBRARIES . "/jevents/jevisotope/jevisotope.php");

        //TODO - Sort Dark out later
        // if ($params->get("darktemplate",0)) JEVHelper::componentStylesheet($this,"dark.css");


        $document = JFactory::getDocument();

    }
}
