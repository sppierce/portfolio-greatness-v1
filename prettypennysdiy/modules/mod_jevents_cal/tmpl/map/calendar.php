<?php
/**
 * copyright (C) 2008-2015 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component frontend
 *
 * @static
 */
include_once(JPATH_SITE."/modules/mod_jevents_cal/tmpl/default/calendar.php");


class MapModCalView extends DefaultModCalView
{

	function _displayCalendarMod($time, $startday, $linkString,	&$day_name, $monthMustHaveEvent=false, $basedate=false){
		
		// display the correct layout
		$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$fallbackview = $jevparams->get("fallbackview", "geraint");
		include_once(JPATH_SITE."/modules/mod_jevents_cal/tmpl/$fallbackview/calendar.php");
		
		$viewclass = ucfirst($fallbackview)."ModCalView";
		$mainlayout = new $viewclass($this->modparams, $this->_modid);
		
		return $mainlayout->_displayCalendarMod($time, $startday, $linkString,	$day_name, $monthMustHaveEvent, $basedate);
	}

}
