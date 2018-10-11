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
include_once(JPATH_SITE."/modules/mod_jevents_legend/tmpl/default/legend.php");


class MapModLegendView extends DefaultModLegendView
{

	function _displayCalendarMod($time, $startday, $linkString,	&$day_name, $monthMustHaveEvent=false, $basedate=false){
		
		// do not display normal legend if dynamic legend is visible on this page
		$registry	= JRegistry::getInstance("jevents");
		if ($registry->get("jevents.dynamiclegend",0)) {
			return;
		}
		
		// display the correct layout
		$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$fallbackview = $jevparams->get("fallbackview", "geraint");
		include_once(JPATH_SITE."/modules/mod_jevents_legend/tmpl/$fallbackview/legend.php");
		
		$viewclass = ucfirst($fallbackview)."ModLegendView";
		$mainlayout = new $viewclass($this->modparams, $this->_modid);
		
		return $mainlayout->displayCalendarLegend($time, $startday, $linkString,$day_name, $monthMustHaveEvent, $basedate);
	}

}
