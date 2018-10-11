<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

include_once(JEV_VIEWS."/default/month/tmpl/calendar_cell.php");

class EventCalendarCell_extplus extends EventCalendarCell_default{
	function calendarCell(&$currentDay,$year,$month,$i, $slot=""){

		$cfg = JEVConfig::getInstance();

		$event = $currentDay["rows"][$i];

		// define start and end
		$blocks = 1;
		if (array_key_exists($slot,$currentDay["slots"])){
			$blocks =$currentDay["slots"][$slot][1];
		}

		$cellStart	= '<div class="eventfull"><div ' ;
		$cellClass	= 'eventstyle p0 ';
		$cellStyle	= '';
		$cellEnd		= '</div></div>' . "\n";

		// add the event color as the column background color
		include_once(JPATH_ADMINISTRATOR."/components/".JEV_COM_COMPONENT."/libraries/colorMap.php");

		// MSIE ignores "inherit" color for links - stupid Microsoft!!!
		//$linkStyle = $bgeventcolor ? 'style="color:'.JevMapColor($bgeventcolor) . ';"' : '';
		$linkStyle = "";

		// The title is printed as a link to the event's detail page
		$link = $this->event->viewDetailLink($year,$month,$currentDay['week_day'],false);
		$link = JRoute::_($link.$this->_datamodel->getCatidsOutLink());

		$title          = $event->title();
		
		// if title is too long, cut 'em for display
		$tmpTitle = $title;
		if (!$cfg->get('icignoretrunc',1)){
			if (!isset($this->event->truncatedtitle)) {
				$longestword = $cfg->get('wordlength',10)*$blocks;
				$words = explode(" ",$title);
				foreach ($words as &$word) {
					if (JString::strlen($word)>$longestword){
						$word = JString::substr($word,0,$longestword-2)."..";
					}
				}
				unset($word);
				$tmpTitle = implode(" ",$words);
				if( JString::strlen( $tmpTitle ) >= $cfg->get('com_calCutTitle',50)*$blocks){
					$tmpTitle = JString::substr( $title, 0, $cfg->get('com_calCutTitle',50)*$blocks ) . ' ...';
				}
				$tmpTitle = JEventsHTML::special($tmpTitle);
				$this->event->truncatedtitle = $tmpTitle;
			}
			else {
				$tmpTitle = $this->event->truncatedtitle ;
			}
		}

		// [new mic] if amount of displaing events greater than defined, show only a scmall coloured icon
		// instead of full text - the image could also be "recurring dependig", which means
		// for each kind of event (one day, multi day, last day) another icon
		// in this case the dfinition must moved down to be more flexible!

		// [tstahl] add a graphic symbol for all day events?
		$tmp_start_time = (($this->start_time == $this->stop_time && !$this->event->noendtime()) || $this->event->alldayevent()) ? '' : $this->start_time;

		$templatedcell = false;
		
		// BAR COLOR GENERATION
		$bgeventcolor = JEV_CommonFunctions::setColor($event);

		if( $currentDay['countDisplay'] < $cfg->get('com_calMaxDisplay',5)){
			ob_start();
			$templatedcell = $this->loadedFromTemplate('month.calendar_cell', $this->event, 0);
			$res = ob_get_clean();
			if ($templatedcell){
				$templatedcell = $res;
			}			
			else {
				if ($this->_view){
					$this->_view->assignRef("link",$link);
					$this->_view->assignRef("linkStyle",$linkStyle);
					$this->_view->assignRef("tmp_start_time",$tmp_start_time);
					$this->_view->assignRef("tmpTitle",$tmpTitle);
				}
				$title_event_link = $this->loadOverride("cellcontent");
				// allow fallback to old method
				if ($title_event_link==""){
					$title_event_link = "\n".'<a class="cal_titlelink" href="' . $link . '" '.$linkStyle.'>'
					. ( $cfg->get('com_calDisplayStarttime') ? $tmp_start_time : '' ) . ' ' . $tmpTitle . '</a>' . "\n";
				}
				$cellStyle .= "border-left-color:$bgeventcolor;border-bottom-color:$bgeventcolor;";
				$cellClass .= 'w100';
			}
		}else{
			$eventIMG	= '<img align="left" src="' . JURI::root()
			. 'components/'.JEV_COM_COMPONENT.'/images/event.png" alt="" style="height:12px;width:8px;border:1px solid white;background-color:'.$bgeventcolor.'" />';

			$title_event_link = "\n".'<a class="cal_titlelink" href="' . $link . '">' . $eventIMG . '</a>' . "\n";
			$cellClass .= ' fleft w10px';
		}

		$cellString	= '';
		// allow template overrides for cell popups
		// only try override if we have a view reference
		if ($this->_view){
			$this->_view->assignRef("ecc",$this);
			$this->_view->assignRef("cellDate",$currentDay["cellDate"]);
		}

		if( $cfg->get("com_enableToolTip",1)) {
			if ($cfg->get("tooltiptype",'overlib')=='overlib'){
				$tooltip = $this->loadOverride("overlib");
				// allow fallback to old method
				if ($tooltip==""){
					$tooltip=$this->calendarCell_popup($currentDay["cellDate"]);
				}
				$tooltip = $this->correctTooltipLanguage($tooltip);
				$cellString .= $tooltip;
			}
			else {
				// TT background
				if( $cfg->get('com_calTTBackground',1) == '1' ){
					$bground =  $this->event->bgcolor();
					$fground =  $this->event->fgcolor();
				}
				else {
					$bground =  "#000000";
					$fground =   "#ffffff";

				}

				JevHtmlBootstrap::popover('.hasjevtip' , array("trigger"=>"hover focus", "placement"=>"top", "container"=>"#jevents_body", "delay"=> array( "show"=> 150, "hide"=> 150 )));

				$tooltip = $this->loadOverride("tooltip");
				// allow fallback to old method
				if ($tooltip==""){
					$tooltip = $this->calendarCell_tooltip($currentDay["cellDate"]);
				}
				$tooltip = $this->correctTooltipLanguage($tooltip);

				if (strpos($tooltip,"templated")===0 ) {
					$title = JString::substr($tooltip,9);
					$cellString = "";
				}
				else {
					$cellString .= '<div class="jevtt_text" >'.$tooltip.'</div>';
					$title = '<div class="jevtt_title" style = "color:'.$fground.';background-color:'.$bground.'">'.$this->title.'</div>';
				}
				
				if ($templatedcell){
					$templatedcell = str_replace("[[TOOLTIP]]", htmlspecialchars($title.$cellString,ENT_QUOTES), $templatedcell);
					$templatedcell = str_replace("[[TOOLTIPTITLE]]", htmlspecialchars($title,ENT_QUOTES), $templatedcell);
					$templatedcell = str_replace("[[TOOLTIPCONTENT]]", htmlspecialchars($cellString,ENT_QUOTES), $templatedcell);
					$time = $cfg->get('com_calDisplayStarttime')?$tmp_start_time:"";
					$templatedcell = str_replace("[[EVTTIME]]", $time, $templatedcell);
					return  $templatedcell;
				}
				
				$html =  $cellStart . ' class="' . $cellClass . '" style="'.$cellStyle.'">' . $this->tooltip( $title , $cellString, $title_event_link) . $cellEnd;

				return $html;
			}

		}
		if ($templatedcell)
		{
			$templatedcell = str_replace("[[TOOLTIP]]", htmlspecialchars($title . $cellString, ENT_QUOTES), $templatedcell);
			$templatedcell = str_replace("[[TOOLTIPTITLE]]", htmlspecialchars($title,ENT_QUOTES), $templatedcell);
			$templatedcell = str_replace("[[TOOLTIPCONTENT]]", htmlspecialchars($cellString,ENT_QUOTES), $templatedcell);
			$time = $cfg->get('com_calDisplayStarttime') ? $tmp_start_time : "";
			$templatedcell = str_replace("[[EVTTIME]]", $time, $templatedcell);
			return $templatedcell;
		}

		// return the whole thing
		return $cellStart . ' style="' . $cellStyle . '" ' . $cellString . ">\n" . $title_event_link . $cellEnd;
	}

}?>
