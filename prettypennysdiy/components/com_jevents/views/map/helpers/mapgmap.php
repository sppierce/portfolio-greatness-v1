<?php
defined('_JEXEC') or die('Restricted access');

function Mapgmap($view)
{
	$compparams = JComponentHelper::getParams("com_jevlocations");
	JLoader::register('JevLocationsHelper', JPATH_ADMINISTRATOR . "/components/com_jevlocations/libraries/helper.php");

	$jevparams = JComponentHelper::getParams("com_jevents");
	$h = $jevparams->get("compheight", "250px");
	$w = $jevparams->get("compwidth", "250px");

	$task = JRequest::getString("jevtask", JRequest::getString("task", ""));

	$events = array();

// range
	if (isset($view->data) && isset($view->data["rows"]))
		$events = $view->data["rows"];
	/* for sites that want to show all events regardless of pagination
	if ($task == "range.listevents")
	{
		$order = $jevparams->get("dataorder", "rpt.startrepeat asc, rpt.endrepeat ASC, det.summary ASC");
		$events = $this->datamodel->getRangeData($view->startdate,$view->enddate,999, 0, $order);
		$events = $events["rows"];
	}
	 */
// weekly
//	else
	if ($task == "week.listevents")
	{
		// backward compatability
		if (!isset($view->data))
			$view->data = $view->datamodel->getWeekData($view->year, $view->month, $view->day);
		if (isset($view->data) && isset($view->data["days"]))
		{
			for ($d = 0; $d < 7; $d++)
			{
				$events = array_merge($events, $view->data['days'][$d]['rows']);
			}
		}
	}
// month
	else if ($task == "month.calendar")
	{
		if (isset($view->data) && isset($view->data["dates"]))
		{
			$datacount = count($view->data["dates"]);
			for ($d = 0; $d < $datacount; $d++)
			{
				$events = array_merge($events, $view->data["dates"][$d]["events"]);
			}
		}
	}
// yearly
	else if ($task == "year.listevents")
	{
		// backward compatability
		if (!isset($view->data))
			$view->data = $view->datamodel->getYearData($view->year, $view->limit, $view->limitstart);

		if (isset($view->data) && isset($view->data["months"]))
		{
			for ($month = 1; $month <= 12; $month++)
			{
				$events = array_merge($events, $view->data["months"][$month]["rows"]);
			}
		}
	}
// day
	else if ($task == "day.listevents")
	{
		// backward compatability
		if (!isset($view->data))
			$view->data = $view->datamodel->getDayData($view->year, $view->month, $view->day);

		$view->Redirectdetail();
		
		if (isset($view->data) && isset($view->data["hours"]))
		{
			$events = array_merge($events, $view->data['hours']['timeless']['events']);
			for ($hours = 0; $hours < 24; $hours++)
			{
				$events = array_merge($events, $view->data['hours'][$hours]['events']);
			}
		}
	}

// category
	else if ($task == "cat.listevents")
	{
		$cfg = JEVConfig::getInstance();
		// backward compatability
		if (!isset($view->data))
			$view->data = $view->datamodel->getCatData($view->catids, $cfg->get('com_showrepeats', 0), $view->limit, $view->limitstart);

		if (isset($view->data) && isset($view->data["rows"]))
		{
			$events = array_merge($events, $view->data['rows']);
		}
	}
// range
	else if ($task == "range.listevents")
	{
		$cfg = JEVConfig::getInstance();
		// backward compatability
		if (!isset($view->data))
		{
			list($startdate, $enddate) = $view->getStartEndDates();
			$view->data = $view->datamodel->getRangeData($startdate, $enddate, $view->limit, $view->limitstart);
		}

		if (isset($view->data) && isset($view->data["rows"]))
		{
			$events = array_merge($events, $view->data['rows']);
		}
	}
// range
	else if ($task == "search.results")
	{
		$cfg = JEVConfig::getInstance();
		if (isset($view->data) && is_array($view->data))
		{
			$events = array_merge($events, $view->data);
		}
	}
	else
	{
		return;
	}

	if (!$events || count($events) == 0)
		return;

	$needsmap = false;
	foreach ($events as $event)
	{
		if (isset($event->_loc_id) && $event->_loc_id != 0)
		{
			$needsmap = true;
			break;
		}
	}
	if (!$needsmap)
		return;

	$locparams = JComponentHelper::getParams("com_jevlocations");
	$maptype =  $locparams->get("maptype", "ROADMAP") ;
	$disableautopan = $locparams->get("autopan", 1)  ? "false":"true";
	?>
	<div id='jev_maincal_map' >
	<?php
	
	usort($events, "sortformap");
	JLoader::register('JevLocationsHelper',JPATH_ADMINISTRATOR."/components/com_jevlocations/libraries/helper.php");
	JevLocationsHelper::loadApiScript();

	$zoom = 10;
	$document = JFactory::getDocument();
	$document->addStyleDeclaration("div.mainlocmap {clear:left;} div#gmapMulti{margin:5px auto} #gmapMulti img { max-width: inherit;}" );

	// Do I need to count how many events in a location
	if ($jevparams->get("comppresentation", 0)==1){
		$loccount = array();
		$eventcounted =  array();
		foreach ($events as $event)
		{
			if ($event->_loc_id == 0 || in_array($event->rp_id(), $eventcounted))
				continue;
			if (!isset($loccount[$event->_loc_id])){
				$loccount[$event->_loc_id] = 0;
			}
			$loccount[$event->_loc_id] ++;
			$eventcounted[]= $event->rp_id();
		}
	}

	$locationAdded=array();

	$clicktoview = JText::_("JEV_MATCHING_EVENTS_CLICK_TO_VIEW", true);
	?>
		 <div class='mainlocmap'>
				 <?php
				 $root = JURI::root();
				 if (strrpos($root, "/")==JString::strlen($root)){
					 $root = JString::substr($root,0,JString::strlen($root)-1);
				 }
				 $Itemid = JRequest::getInt("Itemid");
				 $script = "var urlroot = '" . JURI::root() . "media/com_jevlocations/images/';\n";
				 $script .= <<<SCRIPT
	var myMapMulti = false;
	function addPoint(lat, lon, evid, locid, loctitle, evttitle, icon, url, count){
			// Create our "tiny" marker icon
			var blueIcon = new google.maps.MarkerImage(urlroot + icon,
			// This marker is 32 pixels wide by 32 pixels tall.
			new google.maps.Size(32, 32),
			// The origin for this image is 0,0 within a sprite
			new google.maps.Point(0,0),
			// The anchor for this image is the base of the flagpole at 0,32.
			new google.maps.Point(16, 32));
			// Set up our GMarkerOptions object
			var point = new google.maps.LatLng(lat,lon);
			markerOptions = { icon:blueIcon, draggable:false , map:myMapMulti, disableAutoPan:$disableautopan, icon:blueIcon, position:point};

			var myMarkerMulti = new google.maps.Marker(markerOptions);

			if (count>0){
				//var infowindow = new google.maps.InfoWindow({disableAutoPan:$disableautopan, content: "<div style='color:rgb(134,152,150);font-weight: bold;max-width:250px!important;'><a href='"+url+"'>"+loctitle+"</a><br/><br/><span style='color:#454545;font-weight:normal'>"+count+" $clicktoview<span></div>"});
				var infowindow = new google.maps.InfoWindow({disableAutoPan:$disableautopan, content: "<div style='color:rgb(134,152,150);font-weight: bold;max-width:250px!important;'>"+loctitle+"<br/><br/><span style='color:#454545;font-weight:normal'>"+count+" $clicktoview<span></div>"});
			 }
			 else {
				//var infowindow = new google.maps.InfoWindow({disableAutoPan:$disableautopan, content: "<div style='color:rgb(134,152,150);font-weight: bold;max-width:250px!important;'><a href='"+url+"'>"+evttitle+"</a><br/><br/><span style='color:#454545;font-weight:normal'>"+loctitle+"<span></div>"});"
				var infowindow = new google.maps.InfoWindow({disableAutoPan:$disableautopan, content: "<div style='color:rgb(134,152,150);font-weight: bold;max-width:250px!important;'>"+evttitle+"<br/><br/><span style='color:#454545;font-weight:normal'>"+loctitle+"<span></div>"});
			 }
			google.maps.event.addListener(myMarkerMulti, "mouseover", function(e) {
				infowindow.open(myMapMulti,myMarkerMulti);
			});
			google.maps.event.addListener(myMarkerMulti, "mouseout", function(e) {
				infowindow.close(myMapMulti,myMarkerMulti);
			});
			google.maps.event.addListener(myMarkerMulti, "click", function(e) {
				// modal/squeezebox may cause problems with google since directions are not allowed in iframes

				// use for event detail page
				document.location = url;
				//document.location.replace("{$root}/index.php?option=com_jevents&task=icalrepeat.detail&Itemid=$Itemid&evid="+evid);
				//document.location.replace("{$root}index.php?option=com_jevlocations&task=locations.detail&se=1&Itemid=$Itemid&loc_id="+locid);
			});

	}

	function myMaploadMulti(){

SCRIPT;
				 $minlon = 0;
				 $minlat = 0;
				 $maxlon = 0;
				 $maxlat = 0;
				 $first = true;
				 foreach ($events as $event)
				 {
					 if ($event->_loc_zoom == 0)
						 continue;
					 if ($first)
					 {
						 $minlon = floatval($event->_loc_lon);
						 $minlat = floatval($event->_loc_lat);
						 $maxlon = floatval($event->_loc_lon);
						 $maxlat = floatval($event->_loc_lat);
						 $first = false;
					 }
					 $minlon = floatval($event->_loc_lon) > $minlon ? $minlon : floatval($event->_loc_lon);
					 $minlat = floatval($event->_loc_lat) > $minlat ? $minlat : floatval($event->_loc_lat);
					 $maxlon = floatval($event->_loc_lon) < $maxlon ? $maxlon : floatval($event->_loc_lon);
					 $maxlat = floatval($event->_loc_lat) < $maxlat ? $maxlat : floatval($event->_loc_lat);
				 }
				 if ($minlon == $maxlon)
				 {
					 $minlon-=0.002;
					 $maxlon+=0.002;
				 }
				 if ($minlat == $maxlat)
				 {
					 $minlat-=0.002;
					 $maxlat+=0.002;
				 }
				 $midlon = ($minlon + $maxlon) / 2.0;
				 $midlat = ($minlat + $maxlat) / 2.0;

				 $script.=<<<SCRIPT

	var myOptions = {
		center: new google.maps.LatLng($midlat,$midlon),
		mapTypeId: google.maps.MapTypeId.$maptype
	}

	myMapMulti = new google.maps.Map(document.getElementById("gmapMulti"),myOptions );

	var bounds = new google.maps.LatLngBounds(new google.maps.LatLng($minlat,$minlon), new google.maps.LatLng($maxlat,$maxlon));

SCRIPT;

				 foreach ($events as $event)
				 {
					 if ($event->_loc_id == 0 || in_array( $event->_loc_id, $locationAdded))
						 continue;

					 // On mouse over date formats
					 $event->start_date = JEventsHTML::getDateFormat($event->yup(), $event->mup(), $event->dup(), 0);
					 $event->start_time = $event->startTime();

					 $event->stop_date = JEventsHTML::getDateFormat($event->ydn(), $event->mdn(), $event->ddn(), 0);
					 $event->stop_time = $event->endTime();

					 $event->stop_time_midnightFix = $event->stop_time;
					 $event->stop_date_midnightFix = $event->stop_date;
					 if ($event->sdn() == 59 && $event->mindn() == 59)
					 {
						 $event->stop_time_midnightFix = JEVHelper::getTime($event->getUnixEndTime() + 1, 0, 0);
						 $event->stop_date_midnightFix = JEventsHTML::getDateFormat($event->ydn(), $event->mdn(), $event->ddn() + 1, 0);
					 }

					 if (isset($event->_loc_mapicon) && $event->_loc_mapicon != "")
					 {
						 $icon = $event->_loc_mapicon;
					 }
					 else
					 {
						 $icon = "blue-dot.png";
					 }
					if (isset($loccount[$event->_loc_id]) && $loccount[$event->_loc_id]>1){
						list($year, $month, $day) = JEVHelper::getYMD();
						$date = ($year ? '&year=' . $year : ''). ($month ? '&month=' . $month : ''). ($day ? '&day=' . $day : '');
						 $url = JRoute::_("index.php?option=com_jevents&task=$task&Itemid=$Itemid&loclkup_fv=".$event->_loc_id."$date");
						$script.="	addPoint($event->_loc_lat,$event->_loc_lon," . $event->rp_id() . ",$event->_loc_id, '" . addslashes($event->_loc_title ) . "', '" . addslashes($event->title()) . "', '$icon', '$url',  ".$loccount[$event->_loc_id].");\n";
					}
					else {
						 $url = JRoute::_("index.php?option=com_jevents&task=icalrepeat.detail&Itemid=$Itemid&evid=".$event->rp_id());
						$script.="	addPoint($event->_loc_lat,$event->_loc_lon," . $event->rp_id() . ",$event->_loc_id, '" . addslashes($event->_loc_title . "<br/>" . $event->repeatSummary()) . "', '" . addslashes($event->title()) . "', '$icon', '$url', 0);\n";
					}
					 $locationAdded[]=$event->_loc_id;
				 }
				 $script.=<<<SCRIPT
	myMapMulti.fitBounds(bounds);
	};
	jQuery(document).ready(function (){window.setTimeout("myMaploadMulti()",1000);});
SCRIPT;
				 $document = JFactory::getDocument();
				 $document->addScriptDeclaration($script);
				 ?>
			<div id="gmapMulti" style="width: <?php echo $w; ?>; height:<?php echo $h; ?>;overflow:hidden;"></div>

		</div>	 

	</div>
	<?php

}

function sortformap($a, $b)
{
	if ($a->_startrepeat == $b->_startrepeat)
	{
		return 0;
	}
	static $now;
	if (!isset($now)){
		$jnow = new JevDate("+0 seconds");
		$now = $jnow->toUnix();
	}

	// if after  today then reverse ascending order so that the ones nearest today appear on top
	if ($a->_unixstarttime >= $now && $b->_unixstarttime >= $now){
		return ($a->_unixstarttime < $b->_unixstarttime) ? 1 : -1;
	}
	else if ($a->_unixstarttime < $now && $b->_unixstarttime > $now){
		return ($a->_unixstarttime < $b->_unixstarttime) ? -1 : 1;
	}
	// otherwise they straddle so do reverse ascending order
	else {
		return ($a->_unixstarttime < $b->_unixstarttime) ? 1 : -1;
	}


}
