<?php

/* 
 * A helpfer file for the JEvents notifications plugin.
 * Created by Tony Partridge - GWE Systems Ltd
 * 
 *  * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

class JEVNotifyHelper {

    public static function parseMessage($message, $row, $notification, $creator)
{

	$message = str_replace("{USERNAME}", $notification->username, $message);
	$message = str_replace("{NAME}", $notification->name, $message);
	$message = str_replace("{EVENT}", $row->title(), $message);
	$message = str_replace("{CREATOR}", $creator->name, $message);

	$event_up = new JEventDate($row->publish_up());
	$row->start_date = JEventsHTML::getDateFormat($event_up->year, $event_up->month, $event_up->day, 0);
	$row->start_time = JEVHelper::getTime($row->getUnixStartTime());

	$event_down = new JEventDate($row->publish_down());
	$row->stop_date = JEventsHTML::getDateFormat($event_down->year, $event_down->month, $event_down->day, 0);
	$row->stop_time = JEVHelper::getTime($row->getUnixEndTime());
	$row->stop_time_midnightFix = $row->stop_time;
	$row->stop_date_midnightFix = $row->stop_date;
	if ($event_down->second == 59)
	{
		$row->stop_time_midnightFix = JEVHelper::getTime($row->getUnixEndTime() + 1);
		$row->stop_date_midnightFix = JEventsHTML::getDateFormat($event_down->year, $event_down->month, $event_down->day + 1, 0);
	}

	$message = str_replace("{REPEATSUMMARY}", $row->repeatSummary(), $message);
	$message = str_replace("{DESCRIPTION}",$row->content(),$message);
	$message = str_replace("{EXTRA}",$row->extra_info(),$message);
	$message = str_replace("{LOCATION}",$row->location(),$message);
	$message = str_replace("{CONTACT}",$row->contact_info(),$message);
    $message = str_replace("{CALENDAR}", $row->getCalendarName(), $message);

	$regex = "#{DATE}(.*?){/DATE}#s";
	preg_match($regex, $message, $matches);
	if (class_exists("JevDate"))
	{
		$dateClass = "JevDate";
	}
	else
	{
		$dateClass = "JDate";
	}

	if (count($matches) == 2)
	{
		$date = new $dateClass($row->getUnixStartDate());
		$message = preg_replace($regex, $date->toFormat($matches[1]), $message);
	}

	$regex = "#{LINK}(.*?){/LINK}#s";
	preg_match($regex, $message, $matches);
	if (count($matches) == 2)
	{
		global $Itemid;
		list($year, $month, $day) = JEVHelper::getYMD();
		$link = $row->viewDetailLink($year, $month, $day, false, $Itemid);

		// Check language specificity
		$catids = $row->catids() ? $row->catids() : array($row->catid());
		static $catData = false;
		if (!$catData) {
			$db	= JFactory::getDBO();
			$arr_catids = array();
			$catsql = "SELECT cat.id, cat.title as name, cat.language, l.* FROM #__categories  as cat "
					. "left join #__languages as l on l.lang_code=cat.language "
					. "WHERE cat.extension='com_jevents' and cat.published=1 and cat.language <> '*'" ;
			$db->setQuery($catsql);
			$catData = $db->loadObjectList('id') ;
		}

		$lang = "";
		if ($catData){
			foreach ($catids as $catid) {
				if (isset($catData[$catid])) {
					$lang = "&lang=".$catData[$catid]->sef;
				}
				else {
					// one category supports all languages
					$lang = "";
					break;
				}
			}
		}
		$link .= $lang;
		
		// Should not be a popup URL 
		$link = str_replace(array('&tmpl=component','&amp;tmpl=component','?tmpl=component','&#63;tmpl=component'), '', $link);
		$link = JRoute::_($link);

		if (strpos($link, "/") !== 0)
		{
			$link = "/" . $link;
		}
		$link = str_replace("plugins/jevents/jevnotify/", "", $link);
		$link = str_replace("plugins/jevents/", "", $link);

		$uri =  JURI::getInstance(JURI::base());
		$root = $uri->toString(array('scheme', 'host', 'port'));

		$link = $root . $link;
		$link = str_replace("plugins/jevents/jevnotify/", "", $link);
		$link = str_replace("plugins/jevents/", "", $link);

		// access 1 for Joomla 1.6 onwards!
		if ($row->access() > 1)
		{
			if (strpos($link, "?") > 0)
			{
				$link .= "&login=1";
			}
			else
			{
				$link .= "?login=1";
			}
		}

		$message = preg_replace($regex, "<a href='$link'>" . $matches[1] . "</a>", $message);
	}

	// do we run through the JEvents plugins?
	$plugin = JPluginHelper::getPlugin('jevents', 'jevnotify');
	$params = new JRegistry($plugin->params);

	if ($params->get("runplugins", 0))
	{
		include_once(JEV_PATH . "/views/default/helpers/defaultloadedfromtemplate.php");
		ob_start();
		DefaultLoadedFromTemplate(false, false, $row, 0, $message);
		$message = ob_get_clean();
	}

	// convert relative to absolute URLs - this must be after the plugins have run for obvious reasons! If you really need an explanation - images plugin will give relative URLs etc,
	$message = preg_replace('#(href|src|action|background)[ ]*=[ ]*\"(?!(https?://|\#|mailto:|/))(?:\.\./|\./)?#', '$1="' . JURI::root(), $message);
	$message = preg_replace('#(href|src|action|background)[ ]*=[ ]*\"(?!(https?://|\#|mailto:))/#', '$1="' . JURI::root(), $message);

	$message = preg_replace("#(href|src|action|background)[ ]*=[ ]*\'(?!(https?://|\#|mailto:|/))(?:\.\./|\./)?#", "$1='" . JURI::root(), $message);
	$message = preg_replace("#(href|src|action|background)[ ]*=[ ]*\'(?!(https?://|\#|mailto:))/#", "$1='" . JURI::root(), $message);

	return $message;

}

function getEventData($rpid, $jevtype, $year, $month, $day, $datamodel)
{

	$data = array();


	$pop = intval(JRequest::getVar('pop', 0));
	$Itemid = JEVHelper::getItemid();
	$db = JFactory::getDBO();

	$cfg = JEVConfig::getInstance();
	$row = self::listEventsById($rpid);  // include unpublished events for publishers and above

	$num_row = count($row);

	// No matching rows 
	if ($num_row == 0 || !$row)
	{
		return null;
	}

	if ($num_row)
	{
		// process the new plugins
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onGetEventData', array(& $row));

		$params = new JRegistry(null);

		$event_up = new JEventDate($row->publish_up());
		$row->start_date = JEventsHTML::getDateFormat($event_up->year, $event_up->month, $event_up->day, 0);
		$row->start_time = JEVHelper::getTime($row->getUnixStartTime());

		$event_down = new JEventDate($row->publish_down());
		$row->stop_date = JEventsHTML::getDateFormat($event_down->year, $event_down->month, $event_down->day, 0);
		$row->stop_time = JEVHelper::getTime($row->getUnixEndTime());
		$row->stop_time_midnightFix = $row->stop_time;
		$row->stop_date_midnightFix = $row->stop_date;
		if ($event_down->second == 59)
		{
			$row->stop_time_midnightFix = JEVHelper::getTime($row->getUnixEndTime() + 1);
			$row->stop_date_midnightFix = JEventsHTML::getDateFormat($event_down->year, $event_down->month, $event_down->day + 1, 0);
		}

		// *******************
		// ** This cloaking should be done by mambot/Joomla function
		// *******************
		// Parse http and  wrap in <a> tag
		// trigger content plugin

		$pattern = '[a-zA-Z0-9&?_.,=%\-\/]';

		// Adresse
		// don't convert address that already has a link tag
		if (strpos($row->location(), '<a href=') === false)
		{
			$row->location(preg_replace('#(http://)(' . $pattern . '*)#i', '<a href="\\1\\2">\\1\\2</a>', $row->location()));
		}
		$tmprow = new stdClass();
		$tmprow->text = $row->location();

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('content');

		if (version_compare(JVERSION, "1.6.0", 'ge'))
		{
			$dispatcher->trigger('onContentPrepare', array('com_jevents', &$tmprow, &$params, 0));
		}
		else
		{
			$dispatcher->trigger('onPrepareContent', array(&$tmprow, &$params, 0));
		}
		$row->location($tmprow->text);

		//Contact
		if (strpos($row->contact_info(), '<a href=') === false)
		{
			$row->contact_info(preg_replace('#(http://)(' . $pattern . '*)#i', '<a href="\\1\\2">\\1\\2</a>', $row->contact_info()));
		}
		$tmprow = new stdClass();
		$tmprow->text = $row->contact_info();

		if (version_compare(JVERSION, "1.6.0", 'ge'))
		{
			$dispatcher->trigger('onContentPrepare', array('com_jevents', &$tmprow, &$params, 0));
		}
		else
		{
			$dispatcher->trigger('onPrepareContent', array(&$tmprow, &$params, 0));
		}
		$row->contact_info($tmprow->text);

		//Extra
		if (strpos($row->extra_info(), '<a href=') === false)
		{
			$row->extra_info(preg_replace('#(http://)(' . $pattern . '*)#i', '<a href="\\1\\2">\\1\\2</a>', $row->extra_info()));
		}
		//$row->extra_info(eregi_replace('[^(href=|href="|href=\')](((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)','\\1', $row->extra_info()));
		$tmprow = new stdClass();
		$tmprow->text = $row->extra_info();

		if (version_compare(JVERSION, "1.6.0", 'ge'))
		{
			$dispatcher->trigger('onContentPrepare', array('com_jevents', &$tmprow, &$params, 0));
		}
		else
		{
			$dispatcher->trigger('onPrepareContent', array(&$tmprow, &$params, 0));
		}
		$row->extra_info($tmprow->text);

		// Do main mambot processing here
		// process bots
		//$row->text      = $row->content;
		$params->set("image", 1);
		$row->text = $row->content();

		if (version_compare(JVERSION, "1.6.0", 'ge'))
		{
			$dispatcher->trigger('onContentPrepare', array('com_jevents', &$row, &$params, 0));
		}
		else
		{
			$dispatcher->trigger('onPrepareContent', array(&$row, &$params, 0));
		}
		$row->content($row->text);

		$data['row'] = $row;

		return $data;
	}

}
/**
 *
 * this version doesn't apply the where from the plugins so should always get the event
 * @param type $rpid
 * @return string 
 */
function listEventsById($rpid)
{
	$user =  JFactory::getUser();
	$db = JFactory::getDBO();
	$frontendPublish = JEVHelper::isEventPublisher();

	// process the new plugins
	// get extra data and conditionality from plugins
	$extrafields = "";  // must have comma prefix
	$extratables = "";  // must have comma prefix
	$extrawhere = array();
	$extrajoin = array();
	$dispatcher = JEventDispatcher::getInstance();
	$dispatcher->trigger('onListEventsById', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin));
	$extrajoin = ( count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '' );
	$extrawhere = ( count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '' );

	$query = "SELECT ev.*, ev.state as published, rpt.*, rr.*, det.* $extrafields, ev.created as created "
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
			. "\n FROM (#__jevents_vevent as ev $extratables)"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
			. $extrajoin
			. "\n WHERE rpt.rp_id = '$rpid'";
	$query .="\n GROUP BY rpt.rp_id";

	$db->setQuery($query);
	//echo $db->_sql;
	$rows = $db->loadObjectList();

	// iCal agid uses GUID or UUID as identifier
	if ($rows)
	{
		$row = new jIcalEventRepeat($rows[0]);
	}
	else
	{
		$row = null;
	}

	return $row;

}
    
}
