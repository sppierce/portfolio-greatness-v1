<?php
/**
 * copyright (C) 2008-2015 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the module  frontend
 *
 * @static
 */
include_once(JPATH_SITE."/modules/mod_jevents_latest/tmpl/default/latest.php");

class MapModLatestView extends DefaultModLatestView  
{
	function displayLatestEvents(){

		// this will get the viewname based on which classes have been implemented
		$viewname = $this->getTheme();

		$cfg = JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;
		
		$jevparams  = JComponentHelper::getParams("com_jevents");
		$h = $jevparams->get("modheight","250px");
		$w = $jevparams->get("modwidth","250px");

		$viewpath = "components/".JEV_COM_COMPONENT."/views/".$viewname."/assets/css/";
		
		$dispatcher	= JDispatcher::getInstance();
		$datenow	= JEVHelper::getNow();

		$this->getLatestEventsData();

		$content = "";
		if ($this->customFormatStr!="MAP"){
			// load global view version first
			$jevhelper = new modJeventsLatestHelper();
			$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$fallbackview = $jevparams->get("fallbackview", "geraint");
			$viewclass = $jevhelper->getViewClass($fallbackview, 'mod_jevents_latest',$fallbackview.'/'."latest", $this->modparams);
			
			$modview = new $viewclass($this->modparams, $this->_modid);
			$modview->jevlayout = $fallbackview;
			$content .= $modview->displayLatestEvents();			
		}
		
		$events = array();	
		if(isset($this->eventsByRelDay) && count($this->eventsByRelDay)){

			foreach($this->eventsByRelDay as $relDay => $dayEvents){

				foreach ($dayEvents as $dayEvent){
					if (!isset($dayEvent->_loc_id) || intval($dayEvent->_loc_id)==0) continue;
					$events[] = $dayEvent;
				}
			}
		}
		$modid = $this->_modid;
		if (count ($events)==0) return $content;
		
		$compparams = JComponentHelper::getParams("com_jevlocations");
		JLoader::register('JevLocationsHelper',JPATH_ADMINISTRATOR."/components/com_jevlocations/libraries/helper.php");
		JevLocationsHelper::loadApiScript();
		$disableautopan = $compparams->get("autopan", 1)  ? "false":"true";
				
		ob_start();
?>
<div class='modlocmap'>
	<?php
	$root = JURI::root();
	$Itemid = $this->myItemid;	
$script = "var mlmurlroot = '".JURI::root(true)."/media/com_jevlocations/images/';\n";
	$script .= <<<SCRIPT
var myMapMulti$modid = false;
function addPoint$modid(lat, lon, evid, locid, loctitle, evttitle, icon, url){
		// Create our "tiny" marker icon
		var blueIcon = new google.maps.MarkerImage(mlmurlroot + icon,
		// This marker is 32 pixels wide by 32 pixels tall.
		new google.maps.Size(32, 32),
		// The origin for this image is 0,0 within a sprite
		new google.maps.Point(0,0),
		// The anchor for this image is the base of the flagpole at 0,32.
		new google.maps.Point(16, 32));
		// Set up our GMarkerOptions object
		var point = new google.maps.LatLng(lat,lon);
		markerOptions = { icon:blueIcon, draggable:false , map:myMapMulti$modid, icon:blueIcon,  disableAutoPan:$disableautopan, position:point};
		
		var myMarkerMulti = new google.maps.Marker(markerOptions);
		
		var infowindow = new google.maps.InfoWindow({disableAutoPan:false, content: "<div style='color:rgb(134,152,150);font-weight: bold;max-width:300px!important;'>"+evttitle+"<br/><br/><span style='color:#454545;font-weight:normal'>"+loctitle+"<span></div>",  disableAutoPan:true});
		google.maps.event.addListener(myMarkerMulti, "mouseover", function(e) {
			 // infowindow.open(myMapMulti$modid,myMarkerMulti);
			 $('mapnodeinfo$modid').innerHTML = "<div style='color:rgb(134,152,150);font-weight: bold;border:solid 1px #222;padding:2px;'>"+evttitle+"<br/><br/><span style='color:#454545;font-weight:normal'>"+loctitle+"<span></div>";
		});
		google.maps.event.addListener(myMarkerMulti, "mouseout", function(e) {
			  //infowindow.close(myMapMulti$modid,myMarkerMulti);
			$('mapnodeinfo$modid').innerHTML = "";
		});
		google.maps.event.addListener(myMarkerMulti, "click", function(e) {
			
			// use for event detail page
			document.location = url;
			//document.location.replace("{$root}/index.php?option=com_jevents&task=icalrepeat.detail&Itemid=$Itemid&evid="+evid);
			//document.location.replace("{$root}index.php?option=com_jevlocations&task=locations.detail&se=1&Itemid=$Itemid&loc_id="+locid);
		});

}

function myMaploadMulti$modid(){
		
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
	
	$locparams = JComponentHelper::getParams("com_jevlocations");
	$maptype =  $locparams->get("maptype", "ROADMAP") ;

$script.=<<<SCRIPT

var myOptions$modid = {
	center: new google.maps.LatLng($midlat,$midlon),
	mapTypeId: google.maps.MapTypeId.$maptype
}

myMapMulti$modid = new google.maps.Map(document.getElementById("gmapMulti$modid"),myOptions$modid );

var bounds$modid = new google.maps.LatLngBounds(new google.maps.LatLng($minlat,$minlon), new google.maps.LatLng($maxlat,$maxlon));

SCRIPT;
foreach ($events as $event) {
	if ($event->_loc_id==0) continue;

	// On mouse over date formats
	$event->start_date	= JEventsHTML::getDateFormat( $event->yup(), $event->mup(), $event->dup(), 0 );
	$event->start_time = $event->startTime()	;

	$event->stop_date	= JEventsHTML::getDateFormat(  $event->ydn(), $event->mdn(), $event->ddn(), 0 );
	$event->stop_time = $event->endTime()	;

	$event->stop_time_midnightFix = $event->stop_time ;
	$event->stop_date_midnightFix = $event->stop_date ;
	if ($event->sdn() == 59 && $event->mindn()==59){
		$event->stop_time_midnightFix = JEVHelper::getTime($event->getUnixEndTime()+1,0,0);
		$event->stop_date_midnightFix = JEventsHTML::getDateFormat(  $event->ydn(), $event->mdn(), $event->ddn()+1, 0 );
	}
	
	if (isset($event->_loc_mapicon) && $event->_loc_mapicon!=""){
		$icon = $event->_loc_mapicon;
	}
	else {
		$icon = "blue-dot.png";
	}
	$url = $event->viewDetailLink($event->yup(), $event->mup(), $event->dup(), true, $Itemid);
	//$url = JRoute::_("index.php?option=com_jevents&task=icalrepeat.detail&Itemid=$Itemid&evid=".$event->rp_id());
	$script.="	addPoint$modid($event->_loc_lat,$event->_loc_lon,".$event->rp_id().",$event->_loc_id, '".addslashes($event->_location."<br/>".$event->repeatSummary())."', '".addslashes($event->title())."', '$icon', '$url');\n";	
}
$script.=<<<SCRIPT
   myMapMulti$modid.fitBounds(bounds$modid);
};
jQuery(document).ready(function (){window.setTimeout("myMaploadMulti$modid()",1000);});
SCRIPT;
	$document = JFactory::getDocument();
	$document->addScriptDeclaration($script);
	JHTML::_('behavior.modal');
	?>
	<div id="gmapMulti<?php echo $modid;?>" style="width:<?php echo $w;?>; height:<?php echo $h;?>;" class="gmap" ></div>
	<div id="mapnodeinfo<?php echo $modid;?>" style="width:<?php echo $w;?>;"  class="gmapnode" ></div>
</div>
<?php
		$mapcontent = ob_get_clean();

		if ($jevparams->get("mapplacement", 1) == 0)
		{
			// map before module
			return $mapcontent . $content;
		
		}
		else if ($jevparams->get("mapplacement", 1) == 1)
		{
			// module before map
			return $content . $mapcontent;
		}	
		else {
			// just map
			return $mapcontent;
		}
		
		return $content;
	} // end of function
} // end of class
