<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd
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

class JEventsMapView extends JEventsDefaultView 
{
	var $jevlayout = null;

	function __construct($config = null)
	{
		parent::__construct($config);

		$this->jevlayout="map";	

		$this->addHelperPath(dirname(__FILE__)."/../helpers/");
		$this->addHelperPath( JPATH_BASE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.JEV_COM_COMPONENT.'/'."helpers");

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		// do not load this - it should only contain css relevant to the map
		//JEVHelper::componentStylesheet($this);

		if ($params->get("darktemplate",0)) JEVHelper::componentStylesheet($this,"dark.css");
		 
		$document = JFactory::getDocument();
		$stylelink = '<!--[if lte IE 6]>' ."\n";
		$stylelink .= '<link rel="stylesheet" href="'.JURI::root().'components/com_jevents/views/iconic/assets/css/ie6.css" />' ."\n";
		$stylelink .= '<![endif]-->' ."\n"; 
		$document->addCustomTag($stylelink);

		// Fix to show 100 events if ONLY the map is displayed since there is no pagination
		$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($jevparams->get("mapplacement", 1) == 2 || (isset($this->limit) && $this->limit < 100))
		{
			if (JRequest::getInt('limit', 0) < 100)
			{
				$this->limit = 100;
				JRequest::setVar("limit", 100);
			}
		}

	}


}
