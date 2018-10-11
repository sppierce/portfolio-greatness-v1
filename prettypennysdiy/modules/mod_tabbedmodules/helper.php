<?php

/**
 * @version		$Id: helper.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.html.parameter' );
JLoader::register('JevJoomlaVersion',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/version.php");

class modTabbedmodulesHelper
{

	static public function script($filename)
	{
		if (file_exists(JPATH_BASE . '/templates/' . JFactory::getApplication()->getTemplate() . '/html/mod_tabbedmodules/assets/js/' . $filename))
		{
			JEVHelper::script($filename, 'templates/' . JFactory::getApplication()->getTemplate() . '/html/mod_tabbedmodules/assets/js/');
		}
		else
		{
			JEVHelper::script($filename, 'modules/mod_tabbedmodules/assets/js/');
		}

	}

	static public function stylesheet($filename)
	{
		if (file_exists(JPATH_BASE . '/templates/' . JFactory::getApplication()->getTemplate() . '/html/mod_tabbedmodules/assets/css/' . $filename))
		{
			JEVHelper::stylesheet($filename, 'templates/' . JFactory::getApplication()->getTemplate() . '/html/mod_tabbedmodules/assets/css/');
		}
		else
		{
			JEVHelper::stylesheet($filename, 'modules/mod_tabbedmodules/assets/css/');
		}

	}

	/**
	 *
	 * @return number with ordinal suffix
	 *
	 * @param int $number
	 *
	 * @param int $ss Turn super script on/off
	 *
	 * @return string
	 *
	 */
	static public function ordinalSuffix($number, $ss=0)
	{

		/*		 * * check for 11, 12, 13 ** */
		if ($number % 100 > 10 && $number % 100 < 14)
		{
			$os = 'th';
		}
		/*		 * * check if number is zero ** */
		elseif ($number == 0)
		{
			$os = '';
		}
		else
		{
			/*			 * * get the last digit ** */
			$last = substr($number, -1, 1);

			switch ($last) {
				case "1":
					$os = 'st';
					break;

				case "2":
					$os = 'nd';
					break;

				case "3":
					$os = 'rd';
					break;

				default:
					$os = 'th';
			}
		}

		/*		 * * add super script ** */
		$os = $ss == 0 ? $os : '<sup>' . $os . '</sup>';

		/*		 * * return ** */
		return $number . $os;

	}

	static public function fixLatestEventsModule(&$module)
	{
		$jevcfg = JEVConfig::getInstance();
		$dateformat = $jevcfg->get('dateformat', "");

		include_once (JPATH_SITE . '/components/com_jevents/jevents.defines.php');
		$registry = JRegistry::getInstance("jevents");
		switch ($module->title) {
			case "Today":
				$datenow = JevDate::getDate("+0 seconds");
				$module->tabtitle = $datenow->toFormat("%e");
				modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::_("MOD_TABBEDMODULES_TODAYS_EVENTS"),
					"listlink" =>JText::_("MOD_TABBEDMODULES_VIEW_TODAYS_EVENTS")));
				break;
			case "Tomorrow":
				$datenow = JevDate::getDate("+ 1 day");
				$module->tabtitle = $datenow->toFormat("%e");
				modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" =>  JText::_("MOD_TABBEDMODULES_TOMORROWS_EVENTS"),
					"listlink" => JText::_("MOD_TABBEDMODULES_VIEW_TOMORROWS_EVENTS")));
				break;
			case "Today + 2":
				$datenow = JevDate::getDate("+ 2 day");
				$module->tabtitle = $datenow->toFormat("%e");
				if ($dateformat!=""){
					modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::sprintf("MOD_TABBEDMODULES_EVENTS_ON", $datenow->toFormat($dateformat)),
						"listlink" => JText::sprintf("MOD_TABBEDMODULES_VIEW_EVENTS_ON", $datenow->toFormat($dateformat))));
				}
				else {
					modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::sprintf("MOD_TABBEDMODULES_EVENTS_ON", $datenow->toFormat("%A") . " " . modTabbedmodulesHelper::ordinalSuffix($datenow->toFormat("%e"), 1)),
						"listlink" => JText::sprintf("MOD_TABBEDMODULES_VIEW_EVENTS_ON", $datenow->toFormat("%A") . " " . modTabbedmodulesHelper::ordinalSuffix($datenow->toFormat("%e"), 1))));
				}
				break;
			case "Today + 3":
				$datenow = JevDate::getDate("+ 3 day");
				$module->tabtitle = $datenow->toFormat("%e");
				if ($dateformat!=""){
					modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::sprintf("MOD_TABBEDMODULES_EVENTS_ON", $datenow->toFormat($dateformat)),
						"listlink" => JText::sprintf("MOD_TABBEDMODULES_VIEW_EVENTS_ON", $datenow->toFormat($dateformat))));
				}
				else {
					modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::sprintf("MOD_TABBEDMODULES_EVENTS_ON", $datenow->toFormat("%A") . " " . modTabbedmodulesHelper::ordinalSuffix($datenow->toFormat("%e"), 1)),
						"listlink" => JText::sprintf("MOD_TABBEDMODULES_VIEW_EVENTS_ON", $datenow->toFormat("%A") . " " . modTabbedmodulesHelper::ordinalSuffix($datenow->toFormat("%e"), 1))));
				}
				break;
			case "Today + 4":
				$datenow = JevDate::getDate("+ 4 day");
				$module->tabtitle = $datenow->toFormat("%e");
				if ($dateformat!=""){
					modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::sprintf("MOD_TABBEDMODULES_EVENTS_ON", $datenow->toFormat($dateformat)),
						"listlink" => JText::sprintf("MOD_TABBEDMODULES_VIEW_EVENTS_ON", $datenow->toFormat($dateformat))));
				}
				else {
					modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::sprintf("MOD_TABBEDMODULES_EVENTS_ON", $datenow->toFormat("%A") . " " . modTabbedmodulesHelper::ordinalSuffix($datenow->toFormat("%e"), 1)),
						"listlink" => JText::sprintf("MOD_TABBEDMODULES_VIEW_EVENTS_ON", $datenow->toFormat("%A") . " " . modTabbedmodulesHelper::ordinalSuffix($datenow->toFormat("%e"), 1))));
				}
				break;
			case "Today + 5":
				$datenow = JevDate::getDate("+ 5 day");
				$module->tabtitle = $datenow->toFormat("%e");
				if ($dateformat!=""){
					modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::sprintf("MOD_TABBEDMODULES_EVENTS_ON", $datenow->toFormat($dateformat)),
						"listlink" => JText::sprintf("MOD_TABBEDMODULES_VIEW_EVENTS_ON", $datenow->toFormat($dateformat))));
				}
				else {
					modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::sprintf("MOD_TABBEDMODULES_EVENTS_ON", $datenow->toFormat("%A") . " " . modTabbedmodulesHelper::ordinalSuffix($datenow->toFormat("%e"), 1)),
						"listlink" => JText::sprintf("MOD_TABBEDMODULES_VIEW_EVENTS_ON", $datenow->toFormat("%A") . " " . modTabbedmodulesHelper::ordinalSuffix($datenow->toFormat("%e"), 1))));
				}
				break;
			case "Today + 6":
				$datenow = JevDate::getDate("+ 6 day");
				$module->tabtitle = $datenow->toFormat("%e");
				if ($dateformat!=""){
					modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::sprintf("MOD_TABBEDMODULES_EVENTS_ON", $datenow->toFormat($dateformat)),
						"listlink" => JText::sprintf("MOD_TABBEDMODULES_VIEW_EVENTS_ON", $datenow->toFormat($dateformat))));
				}
				else {
					modTabbedmodulesHelper::adjustParams($module->params, array("listtitle" => JText::sprintf("MOD_TABBEDMODULES_EVENTS_ON", $datenow->toFormat("%A") . " " . modTabbedmodulesHelper::ordinalSuffix($datenow->toFormat("%e"), 1)),
						"listlink" => JText::sprintf("MOD_TABBEDMODULES_VIEW_EVENTS_ON", $datenow->toFormat("%A") . " " . modTabbedmodulesHelper::ordinalSuffix($datenow->toFormat("%e"), 1))));
				}
				break;
			default:
				$datenow = JevDate::getDate("+0 seconds");
				break;
		}
		list($year, $month, $day) = modTabbedmodulesHelper::getYMD($datenow);
		modTabbedmodulesHelper::adjustParams($module->params, array("linkdmy" . "&year=$year&month=$month&day=$day"));

	}

	static public function wrapLatestEventsModule(&$module)
	{
		$modparams = new JRegistry($module->params);
		if ($modparams->get("listtitle") &&  $module->tabcontent != ""){
			$module->tabcontent = "<h4 class='latesteventstitle'>".$modparams->get("listtitle")."</h4>". $module->tabcontent;
			$module->tabcontent = str_replace(JText::_("JEV_CLICK_TOCOMPONENT"), $modparams->get("listlink"), $module->tabcontent);
		}		
	}

	static public function adjustParams(&$params, $addition)
	{
		if (version_compare(JVERSION, "1.6.0", 'ge'))
		{
			$params = json_decode($params);
			foreach ($addition as $key => $val)
			{
				$params->$key = $val;
			}
			$params = json_encode($params);
		}
		else
		{
			foreach ($addition as $key => $val)
			{
				$params.="$key=$val\n";
			}
		}

	}

	static public function getYMD($datenow)
	{
		list($year, $month, $day) = explode('-', $datenow->toFormat('%Y-%m-%d'));
		$year = min(2100, abs(intval(JRequest::getVar('year', $year))));
		$month = min(99, abs(intval(JRequest::getVar('month', $month))));
		$day = min(3650, abs(intval(JRequest::getVar('day', $day))));

		if ($day <= '9')
		{
			$day = '0' . $day;
		}
		if ($month <= '9')
		{
			$month = '0' . $month;
		}
		return array($year, $month, $day);

	}

}
