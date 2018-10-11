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

class IconicModLatestView extends DefaultModLatestView
{
	public function __construct($params, $modid){	
		return parent::__construct($params, $modid);
	}
	
	function displayLatestEvents(){

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$this->colourscheme = $params->get("iccolourscheme","red");

	if ($this->colourscheme == "gradient")
	{
		static $loadedScalableCssGradient = false;
		if (!$loadedScalableCssGradient)
		{
			$document = JFactory::getDocument();
			$loadedScalableCssGradient = true;
			$icgradient1 = $params->get('icgradient1', '#FECCB1,#F17432 50%,#EA5507 61%,#FB955E');
			$icgradienttext1 = $params->get('icgradienttext1', '#F17432');
			$icgradienttext2 = $params->get('icgradienttext2', '#FFF');
			$icbackground1 = $params->get('icbackground1', '#F17432');
			$icbackground2 = $params->get('icbackground2', '#EEE');
			$oldmsie = explode(",", $icgradient1);
			if (count($oldmsie) > 1)
			{
				$oldmsie1 = $oldmsie[0];
				$oldmsie2 = $oldmsie[count($oldmsie) - 1];
			}
			else
			{
				$oldmsie1 = $oldmsie2 = $oldmsie;
			}

			$css = <<<CSS
/** Gradient **/

.jev_gradient .jevdateicon {
	color:$icgradienttext2!important;
	border:solid 1px $icgradienttext1!important;
}

.jev_gradient .jevdateicon1 {
	background-color: $icbackground2!important;
	color:$icgradienttext1!important;
}
.jev_gradient .jevdateicon2 {
	background-image: linear-gradient( $icgradient1);
}

CSS;
			$document->addStyleDeclaration($css);
		}
	}

		// this will get the viewname based on which classes have been implemented
		$viewname = $this->getTheme();

		$cfg = JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;

		$viewpath = "components/".JEV_COM_COMPONENT."/views/".$viewname."/assets/css/";

		$dispatcher	= JDispatcher::getInstance();
		$datenow	= JEVHelper::getNow();

		list($usec, $sec) = explode(" ", microtime());
		$starttime = (float) $usec + (float) $sec;
		
		$this->getLatestEventsData();

		list ($usec, $sec) = explode(" ", microtime());
		$time_end = (float) $usec + (float) $sec;
		//echo  "time to get data  = ".round($time_end - $starttime, 4)."<br/>";
		
		$content = "";

		if(isset($this->eventsByRelDay) && count($this->eventsByRelDay)){

			$content .= $this->modparams->get("modlatest_templatetop") ? $this->modparams->get("modlatest_templatetop") : "<div class='jeventslatest jev_".$this->colourscheme."'>";

			// Now to display these events, we just start at the smallest index of the $this->eventsByRelDay array
			// and work our way up.

			$firstTime=true;

			// initialize name of com_jevents module and task defined to view
			// event detail.  Note that these could change in future com_event
			// component revisions!!  Note that the '$this->itemId' can be left out in
			// the link parameters for event details below since the event.php
			// component handler will fetch its own id from the db menu table
			// anyways as far as I understand it.

			$this->processFormatString();

			foreach($this->eventsByRelDay as $relDay => $daysEvents){
				reset($daysEvents);

				// get all of the events for this day
				foreach($daysEvents as $dayEvent){
					$eventcontent = "";
					$content .= "<div class='iconicevent'>\n";
					// get the title and start time
					$startDate	= JevDate::strtotime($dayEvent->publish_up());
					if ($relDay>0){
						$eventDate	= JevDate::strtotime($datenow->toFormat('%Y-%m-%d ').JevDate::strftime('%H:%M', $startDate)." +$relDay days");
					}
					else {
						$eventDate	= JevDate::strtotime($datenow->toFormat('%Y-%m-%d ').JevDate::strftime('%H:%M', $startDate)." $relDay days");
					}
					$endDate	= JevDate::strtotime($dayEvent->publish_down());

					list($st_year, $st_month, $st_day) = explode('-', JevDate::strftime('%Y-%m-%d', $startDate));
					list($ev_year, $ev_month, $ev_day) = explode('-', JevDate::strftime('%Y-%m-%d', $startDate));

					if (!function_exists("Iconicdateicon")){
						include_once(JPATH_SITE."/components/".JEV_COM_COMPONENT."/views/iconic/helpers/iconicdateicon.php");
					}
					if ($this->displayLinks) {
						$link = $dayEvent->viewDetailLink($ev_year,$ev_month,$ev_day,false,$this->myItemid);
						$link = JRoute::_($link.$this->datamodel->getCatidsOutLink());
						$title = JEventsHTML::special($dayEvent->title());
						//$inner = JEV_CommonFunctions::jev_strftime("<div class='jevdateicon1'>%d</div><div class='jevdateicon2'>%b</div>",$startDate);
						if ($this->dispMode==6){
							$inner = Iconicdateicon($this, explode(":",JEV_CommonFunctions::jev_strftime("%d:%b",$startDate)),$title, $link,"jevdateiconmod");
							//$inner = Iconicdateicon($this, explode(":",JEV_CommonFunctions::jev_strftime("%d:%b",$startDate)),$title, $link,"jevdateiconmod", $dayEvent, $this->myItemid);
							//$inner = JEV_CommonFunctions::jev_strftime("<span class='jevdateicon1'>%d</span><span class='jevdateicon2'>%b</span>",$startDate);
						}
						else {
							$inner = Iconicdateicon($this, explode(":",JEV_CommonFunctions::jev_strftime("%d:%b",$eventDate)),$title, $link,"jevdateiconmod");
							//$inner = Iconicdateicon($this, explode(":",JEV_CommonFunctions::jev_strftime("%d:%b",$eventDate)),$title, $link,"jevdateiconmod", $dayEvent, $this->myItemid);
							//$inner = JEV_CommonFunctions::jev_strftime("<span class='jevdateicon1'>%d</span><span class='jevdateicon2'>%b</span>",$eventDate);
						}
						//$eventcontent .= '<a class="jevdateiconmod" href="' . $link . '" title="' . $title . '">' . $inner.'</a>'."\n";
						$eventcontent .= $inner;
					} 
					else {
						if ($this->dispMode==6 ){
							$inner = Iconicdateicon($this, explode(":",JEV_CommonFunctions::jev_strftime("%d:%b",$startDate)),"", "","jevdateiconmod");
							//$inner = JEV_CommonFunctions::jev_strftime("<span class='jevdateicon1'>%d</span><span class='jevdateicon2'>%b</span>",$startDate);
						}
						else {
							$inner = Iconicdateicon($this, explode(":",JEV_CommonFunctions::jev_strftime("%d:%b",$eventDate)),"", "","jevdateiconmod");
							//$inner = JEV_CommonFunctions::jev_strftime("<span class='jevdateicon1'>%d</span><span class='jevdateicon2'>%b</span>",$eventDate);
						}
						//$eventcontent .= '<div class="jevdateiconmod">'.$inner.'</div>';
						$eventcontent .= $inner;
					}

					if ($params->get("colourbar",0)) $listyle = 'style="border-color:'.$dayEvent->bgcolor().';"';
					else $listyle = 'style="border-color:transparent"';

					$eventcontent .= '<div class="jeviconnotdate" '.$listyle.'>';

					//href="#" onclick="window.location.href=\'' . $link . '\'; return false;"					

					// generate output according custom string
					foreach($this->splitCustomFormat as $condtoken) {

						if (isset($condtoken['cond'])) {
							if ( $condtoken['cond'] == 'a'  && !$dayEvent->alldayevent()) continue;
							else if ( $condtoken['cond'] == '!a' &&  $dayEvent->alldayevent()) continue;
							else if ( $condtoken['cond'] == 'e'  && !($dayEvent->noendtime() || $dayEvent->alldayevent())) continue;
							else if ( $condtoken['cond'] == '!e' &&  ($dayEvent->noendtime() || $dayEvent->alldayevent())) continue;							
							else if ( $condtoken['cond'] == '!m' &&  $dayEvent->getUnixStartDate()!=$dayEvent->getUnixEndDate() ) continue;
							else if ( $condtoken['cond'] == 'm' &&  $dayEvent->getUnixStartDate()==$dayEvent->getUnixEndDate() ) continue;
						}
						foreach($condtoken['data'] as $token) {
							unset($match);
							unset($dateParm);
							$dateParm="";
							$match='';
							if (is_array($token)) {
								$match = $token['keyword'];
								$dateParm = isset($token['dateParm']) ? trim($token['dateParm']) : "";
							}
							else if (strpos($token,'${')!==false){
								$match = $token;
							}
							else {
								$eventcontent .= $token;
								continue;
							}

							$this->processMatch($eventcontent, $match, $dayEvent, $dateParm,$relDay);
						} // end of foreach
					} // end of foreach
					$eventcontent .= "</div>";

					if ($params->get("colourbar",0)) $listyle = 'style="border-color:'.$dayEvent->bgcolor().';"';
					else $listyle = 'style="border-color:transparent"';

					$eventrow = "<div class='iconicdaterow'>%s</div><div class='jev_clear' ></div>\n";

					$templaterow = $this->modparams->get("modlatest_templaterow") ? $this->modparams->get("modlatest_templaterow")  : $eventrow;
					$content .= str_replace("%s", $eventcontent , $templaterow);

					$firstTime=false;

				} // end of foreach
			} // end of foreach
			$content .=$this->modparams->get("modlatest_templatebottom") ? $this->modparams->get("modlatest_templatebottom") : "</div>\n";
		}
		else if ($this->modparams->get("modlatest_NoEvents", 1)){
			$content .= $this->modparams->get("modlatest_templatetop") ? $this->modparams->get("modlatest_templatetop") : "<div class='jeventslatest jev_".$this->colourscheme."'>";
			$templaterow = $this->modparams->get("modlatest_templaterow") ? $this->modparams->get("modlatest_templaterow")  : '%s' . "\n";
			$content .= str_replace("%s", JText::_('JEV_NO_EVENTS') , $templaterow);
			$content .=$this->modparams->get("modlatest_templatebottom") ? $this->modparams->get("modlatest_templatebottom") : "</div>\n";

		}

		$callink_HTML = '<div class="mod_events_latest_callink">'
		.$this->getCalendarLink()
		. '</div>';

		if ($this->linkToCal == 1) $content = $callink_HTML . $content;
		if ($this->linkToCal == 2) $content .= $callink_HTML;

		if ($this->displayRSS){
			if (version_compare(JVERSION, "1.6.0", 'ge')) {
				$rssimg = JURI::root() . "media/system/images/livemarks.png";
			}
			else {
				$rssimg = JURI::root() . "images/M_images/livemarks.png";
			}

			$callink_HTML = '<div class="mod_events_latest_rsslink">'
			.'<a href="'.$this->rsslink.'" title="'.JText::_("RSS_FEED").'"  target="_blank">'
			.'<img src="'.$rssimg.'" alt="'.JText::_("RSS_FEED").'" />'
			.JText::_("SUBSCRIBE_TO_RSS_FEED")
			. '</a>'
			. '</div>';
			$content .= $callink_HTML;
		}
list ($usec, $sec) = explode(" ", microtime());
$time_end = (float) $usec + (float) $sec;
//echo  "time to do the lot = ".round($time_end - $starttime, 4)."<br/>";

		if ($this->modparams->get("contentplugins", 0)){
			$dispatcher = JDispatcher::getInstance();
			$eventdata = new stdClass();
			//$eventdata->text = str_replace("{/toggle","{/toggle}",$content);
			$eventdata->text = $content;
			$dispatcher->trigger('onContentPrepare', array('com_jevents', &$eventdata, &$this->modparams, 0));
			 $content = $eventdata->text;
		}

		return $content;
	} // end of function
} // end of class
