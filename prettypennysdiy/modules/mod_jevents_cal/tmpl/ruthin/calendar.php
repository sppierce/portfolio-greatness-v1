<?php
/**
 * copyright (C) 2008-2016 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component frontend
 *
 * @static
 */
include_once(JPATH_SITE."/modules/mod_jevents_cal/tmpl/default/calendar.php");

class RuthinModCalView extends DefaultModCalView 
{	
	function _displayCalendarMod($time, $startday, $linkString, &$day_name, $monthMustHaveEvent=false, $basedate=false){

		$db	= JFactory::getDBO();
		$cfg = JEVConfig::getInstance();
		$option = JEV_COM_COMPONENT;

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$colourscheme = $params->get("colourscheme","red");
		
		$cal_year=date("Y",$time);
		$cal_month=date("m",$time);
		// do not use $cal_day since it's not reliable due to month offset calculation
		//$cal_day=date("d",$time);

		if (!$basedate) $basedate=$time;
		$base_year = date("Y",$basedate);
		$base_month = date("m",$basedate);
		$basefirst_of_month   = JevDate::mktime(0,0,0,$base_month, 1, $base_year);

		$base_prev_month 	= $base_month - 1;
		$base_next_month 	= $base_month + 1;
		$base_next_month_year	= $base_year;
		$base_prev_month_year	= $base_year;
		if( $base_prev_month == 0 ) {
			$base_prev_month 	= 12;
			$base_prev_month_year 	-=1;
		}
		if( $base_next_month == 13 ) {
			$base_next_month 	= 1;
			$base_next_month_year 	+=1;
		}

		$requestYear = JRequest::getInt("year",0);
		$requestMonth = JRequest::getInt("month",0);
		// special case when site link set the dates for the mini-calendar in the URL but not in the ajax request
		if ($requestMonth && $requestYear && JRequest::getString("task","")!="modcal.ajax"  && $this->modparams->get("minical_usedate",0)){
			$requestDay = JRequest::getInt("day",1);

			$requestTime = JevDate::mktime(0,0,0,$requestMonth, $requestDay, $requestYear);
			if ($time-$basedate > 100000) $requestTime = JevDate::strtotime("+1 month",$requestTime);
			else if ($time-$basedate < -100000) $requestTime = JevDate::strtotime("-1 month",$requestTime);
	
			$cal_year = date("Y",$requestTime);
			$cal_month = date("m",$requestTime);

			$base_year = $requestYear;
			$base_month = $requestMonth;
			$basefirst_of_month   = JevDate::mktime(0,0,0,$requestMonth, $requestDay, $requestYear);
		}
		else {
			$cal_year=date("Y",$time);
			$cal_month=date("m",$time);
		}		

		$reg = JFactory::getConfig();
		$reg->set("jev.modparams",$this->modparams);
		if ($this->modparams->get("showtooltips",0)) {
			$data = $this->datamodel->getCalendarData($cal_year,$cal_month,1,false, false);
			$this->hasTooltips	 = true;
		}
		else {
			$data = $this->datamodel->getCalendarData($cal_year,$cal_month,1,true, $this->modparams->get("noeventcheck",0));
		}
		$reg->set("jev.modparams",false);
                $width = $this->modparams->get("mod_cal_width","135px");
                $height = $this->modparams->get("mod_cal_height","");
		
		$month_name = JEVHelper::getMonthName($cal_month);
		$first_of_month = JevDate::mktime(0,0,0,$cal_month, 1, $cal_year);
		//$today = JevDate::mktime(0,0,0,$cal_month, $cal_day, $cal_year);
		$today = JevDate::strtotime(date('Y-m-d', $this->timeWithOffset));

		$content    = '<div style="margin:0px 0px 4px 0px;padding:0px;border-width:0px;">';

		$mod ="";
		if (isset($this->_modid) && $this->_modid>0){
			$mod = 'id="modid_'.$this->_modid.'" ';
			// This duplicates the same code from default.php - so drop it!
			//$content  .= "<span id='testspan".$this->_modid."' style='display:none'></span>\n";
		}

		$scriptlinks = "";
		if( $this->minical_showlink ){

			$content .= "\n".'<table class="jevcalendar" style="width: '.$width.';">' . "\n"
			. '<tr class="jevtopnav jevnav jev_'.$colourscheme.'" style="background-size: 100% 100%;">' . "\n";

			if( $this->minical_showlink == 1 ){

				if( $this->minical_prevyear ){
					$content .= $this->monthYearNavigation($basefirst_of_month,"-1 year",'DoubleLeft.png',JText::_('JEV_CLICK_TOSWITCH_PY'),"month.calendar","jevdoubleleft");
				}

				if( $this->minical_prevmonth ){
					$linkprevious = htmlentities(JURI::base()  . "index.php?option=$option&task=modcal.ajax&day=1&month=$base_prev_month&year=$base_prev_month_year&modid=$this->_modid&tmpl=component".$this->cat);
					$scriptlinks .= "linkprevious = '".$linkprevious."';\n";
					$content .= $this->monthYearNavigation($basefirst_of_month,"-1 month",'Left.png',JText::_('JEV_CLICK_TOSWITCH_PM'),"month.calendar","jevleft");
				}

				if( $this->minical_actmonth == 1 ){
					// combination of actual month and year: view month
					$seflinkActMonth = JRoute::_( $this->linkpref.'month.calendar&month='.$cal_month.'&year='.$cal_year);

					$content .= '<td  class="jevmy">';
					$content .= $this->htmlLinkCloaking($seflinkActMonth, $month_name, array('class'=>"mod_events_link",'title'=> JText::_('JEV_CLICK_TOSWITCH_MON')))." ";
					
					if( $this->minical_actyear < 1 ) $content .= '</td>';
				}elseif( $this->minical_actmonth == 2 ){
					$content .= '<td class="jevmy">';
					$content .= $month_name . "\n";
					if( $this->minical_actyear < 1 ) $content .= '</td>';
				}

				if( $this->minical_actyear == 1 ){
					// combination of actual month and year: view year
					$seflinkActYear = JRoute::_( $this->linkpref . 'year.listevents' . '&month=' . $cal_month
					. '&year=' . $cal_year );

					if( $this->minical_actmonth < 1 )$content .= '<td  class="jevmy">';
					$content .= $this->htmlLinkCloaking($seflinkActYear, $cal_year, array('class'=>"mod_events_link",'title'=> JText::_('JEV_CLICK_TOSWITCH_YEAR')))." ";
					$content .= '</td>';
				}elseif( $this->minical_actyear == 2 ){
					if( $this->minical_actmonth < 1 ) $content .= '<td style="text-align:center">';
					$content .= $cal_year . "\n";
					$content .= '</td>';
				}

				if( $this->minical_nextmonth ){
					$linknext = htmlentities(JURI::base()  . "index.php?option=$$option&task=modcal.ajax&day=1&month=$base_next_month&year=$base_next_month_year&modid=$this->_modid&tmpl=component".$this->cat);
					$scriptlinks .= "linknext = '".$linknext."';\n";
					$content .= $this->monthYearNavigation($basefirst_of_month,"+1 month",'Right.png',JText::_('JEV_CLICK_TOSWITCH_NM'),"month.calendar","jevright");
				}

				if( $this->minical_nextyear ){
					$content .= $this->monthYearNavigation($basefirst_of_month,"+1 year",'DoubleRight.png',JText::_('JEV_CLICK_TOSWITCH_NY'),"month.calendar","jevdoubleright");
				}

				// combination of actual month and year: view year & month [ mic: not used here ]
				// $seflinkActYM   = JRoute::_( $link . 'month.calendar' . '&month=' . $cal_month
				// . '&year=' . $cal_year );
			}else{
				// show only text
				$content .= '<td>';
				$content .= $month_name . ' ' . $cal_year;
				$content .= '</td>';
			}

			$content .= "</tr>\n"
			. "</table>\n";
		}
		$lf = "\n";



		$content	.= '<table class="jevcalendar jevcalendar2" style="width: '.$width.'; height: '.$height.';">'.$lf
		. '<tr class="jevtopnav jev_'. $colourscheme.'" style="background-size: 100% 100%;">'.$lf;

		// Days name rows
		for ($i=0;$i<7;$i++) {
			$content.="<td class=\"jevdayname jev_".$colourscheme."\">".$day_name[($i+$startday)%7]."</td>".$lf	;
		}

		$content.='</tr>'.$lf;

		$datacount = count($data["dates"]);
		$dn=0;
		for ($w=0;$w<6 && $dn<$datacount;$w++){
			$content .="<tr>\n";
			/*
			echo "<td width='2%' class='cal_td_weeklink'>";
			list($week,$link) = each($data['weeks']);
			echo "<a href='".$link."'>$week</a></td>\n";
			*/
			for ($d=0;$d<7 && $dn<$datacount;$d++){
				$currentDay = $data["dates"][$dn];
				switch ($currentDay["monthType"]){
					case "prior":
					case "following":
						$content .= '<td class="jevoutofmonth" style="background-size: 100% 100%;">'.$currentDay["d"]."</td>\n";
						break;
					case "current":
						if ($currentDay["events"]){
							$class = ($currentDay["cellDate"] == $today) ? "jevtoday jev_".$colourscheme : "jevhasevents";
						}
						else {
							$class = ($currentDay["cellDate"] == $today) ? "jevtoday jev_".$colourscheme : "jevnoevents";
						}
						
						$content .= "<td class='".$class."' style='background-size: 100% 100%;'>\n";
						// To make sure there are no strict php error messages when currentDay is changed we add second method
						if (method_exists($this, "getTooltipReference")){
							$tooltip = $this->getTooltipReference($currentDay, array('class'=>""));
						}
						else {
							$tooltip = $this->getTooltip($currentDay, array('class'=>""));
						}
						if ($tooltip) {
							$content .= $tooltip;
						}
						else {
							if ($this->modparams->get("emptydaylinks", 1) || $currentDay["events"] || $this->modparams->get("noeventcheck",0)) {
								$content .= $this->htmlLinkCloaking($currentDay["link"], $currentDay['d'], array('class'=>"mod_events_daylink",'title'=> JText::_('JEV_CLICK_TOSWITCH_DAY')));
							}
							else {
								$content .= $currentDay['d'];
							}
						}
						$content .="</td>\n";

						break;
				}
				$dn++;
			}
			$content .= "</tr>\n";
		}

		$content .= '</table>'.$lf;
		$content .= '</div>'.$lf;

		if ($scriptlinks!=""){
			$content .= "<script style='text/javascript'>xyz=1;".$scriptlinks."zyx=1;</script>";
		}

		return $content;
	}

	function monthYearNavigation($cal_today,$adj,$img, $label,$action="month.calendar", $class=''){
		$cfg = JEVConfig::getInstance();
		$jev_component_name  = JEV_COM_COMPONENT;
		$adjDate = JevDate::strtotime($adj,$cal_today);
		list($year,$month) = explode(":",JevDate::strftime("%Y:%m",$adjDate));
		$link = JRoute::_($this->linkpref.$action."&day=1&month=$month&year=$year".$this->cat);

		$content ="";
		if (isset($this->_modid) && $this->_modid>0){
			$this->_navigationJS($this->_modid);
			$link = htmlentities(JURI::base()  . "index.php?option=$jev_component_name&task=modcal.ajax&day=1&month=$month&year=$year&modid=$this->_modid&tmpl=component".$this->cat);
			$content = '<td class="'.$class.'">';
			$content .= '<div class="mod_jevents_link" onmousedown="callNavigation(\''.$link.'\');" ontouchstart="callNavigation(\''.$link.'\');"><img src="'.JURI::Root(true).'/components/com_jevents/views/ruthin/assets/images/'.$img.'" alt="'.$label.'"></div>';
			$content .= '</td>';
		}
		return $content;
	}

	 protected function getTooltip($currentDay, $linkattr) {
		$tooltip = "";
		if (!isset($currentDay["events"]) || !is_array($currentDay["events"]) ||  count($currentDay["events"])==0){
			return $tooltip;
		}
		// This loads jQuery too!
		JevHtmlBootstrap::framework();

		foreach ($currentDay["events"] as $event) {
			$link = $event->viewDetailLink($event->yup(), $event->mup(), $event->dup(), true, $this->myItemid);//JRoute::_('index.php?option='.$jev_component_name.'&Itemid='.$this->myItemid.$this->cat.'&task=icalrepeat.detail';
			$tooltip .= "<a href='".$link."'>". $event->title()."</a><br/>";
		}
		$tooltip .= "<hr class='jev-click-to-open'/><small class='jev-click-to-open'>".JText::_("JEV_EVENTS_CLICK_EVENT_FOR_MORE_DETAILS",true)."</small>";

		$tipTitle = '<div class="jevtt_title" >'.JText::_("JEV_EVENTS_THIS_DAY",true) .'</div>';
		$tipText = '<div class="jevtt_text">'.$tooltip.'</div>';
		$tooltip	= htmlspecialchars($tipTitle.$tipText,ENT_QUOTES);
		$link = $this->htmlLinkCloaking($currentDay["link"], $currentDay['d'], $linkattr);
		$tooltip = '<span class="editlinktip hasjevtip" title="'.$tooltip.'" >'.$link.'</span>';

		static $script;
		if (!isset($script	)){
			$script = true;
			JevHtmlBootstrap::popover('.hasjevtip' , array("trigger"=>"hover focus", "placement"=>"top", "container"=>"body", "delay"=> array( "show"=> 150, "hide"=> 150 )));
		}

		return $tooltip;
	 }
	 
	 protected function getTooltipReference(&$currentDay, $linkattr) {
		 $params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$tooltip = "";
		if (!isset($currentDay["events"]) || !is_array($currentDay["events"]) ||  count($currentDay["events"])==0){
			return $tooltip;
		}

		// This loads jQuery too!
		JevHtmlBootstrap::framework();
		// This needs core bootstrap css not our namespaced version!
		if ($params->get("rucolourscheme", 1)) {
                    JHtmlBootstrap::loadCss();
                }

		foreach ($currentDay["events"] as $event) {
			$link = $event->viewDetailLink($event->yup(), $event->mup(), $event->dup(), true, $this->myItemid);//JRoute::_('index.php?option='.$jev_component_name.'&Itemid='.$this->myItemid.$this->cat.'&task=icalrepeat.detail';
			if (count ($currentDay["events"])==1 && JComponentHelper::getParams("com_jevents")->get("redirect_detail",1)){
				$currentDay["link"] = $link;
			}
			$tooltip .= "<a href='".$link."'>". $event->title()."</a><br/>";
		}
		$tooltip .= "<hr class='jev-click-to-open'/><small class='jev-click-to-open'>".JText::_("JEV_EVENTS_CLICK_EVENT_FOR_MORE_DETAILS",true)."</small>";

		$tipTitle = '<div class="jevtt_title" >'.JText::_("JEV_EVENTS_THIS_DAY",true) .'</div>';
		$tipText = '<div class="jevtt_text">'.$tooltip.'</div>';
		$tooltip	= htmlspecialchars($tipTitle.$tipText,ENT_QUOTES);
		$link = $this->htmlLinkCloaking($currentDay["link"], $currentDay['d'], $linkattr);
		$tooltip = '<span class="editlinktip hasjevtip" title="'.$tooltip.'" >'.$link.'</span>';

		static $script;
		if (!isset($script	)){
			$script = true;
			JevHtmlBootstrap::popover('.hasjevtip' , array("trigger"=>"hover focus", "placement"=>"top", "container"=>"body", "delay"=> array( "show"=> 150, "hide"=> 150 )));
		}

		return $tooltip;
	 }
}

