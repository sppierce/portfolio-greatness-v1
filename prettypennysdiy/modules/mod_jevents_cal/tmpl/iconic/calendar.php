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


class IconicModCalView extends DefaultModCalView
{

	function _displayCalendarMod($time, $startday, $linkString,	&$day_name, $monthMustHaveEvent=false, $basedate=false){
		$db	= JFactory::getDBO();
		$cfg = JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;

		$cal_day=date("d",$time);
		//$cal_year=date("Y",$time);
		//$cal_month=date("m",$time);
		//list($cal_year,$cal_month,$cal_day) = JEVHelper::getYMD();

		if (!$basedate) $basedate=$time;
		$base_year = date("Y",$basedate);
		$base_month = date("m",$basedate);
		$basefirst_of_month   = JevDate::mktime(0,0,0,$base_month, 1, $base_year);

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
                $height = $this->modparams->get("mod_cal_height","auto");
                $rowheight = $this->modparams->get("mod_cal_rowheight","auto");

		$month_name = JEVHelper::getMonthName($cal_month);
		$to_day     = date("Y-m-d", $this->timeWithOffset);
		$today = JevDate::mktime(0,0,0,$cal_month, $cal_day, $cal_year);

		$cal_prev_month 	= $cal_month - 1;
		$cal_next_month 	= $cal_month + 1;
		$cal_next_month_year	= $cal_year + 1;
		$cal_prev_month_year	= $cal_year - 1;

		// additional EBS
		if( $cal_prev_month == 0 ) {
			$cal_prev_month 	= 12;
			$cal_prev_month_year 	-=1;
		}
		if( $cal_next_month == 13 ) {
			$cal_next_month 	= 1;
			$cal_next_month_year 	+=1;
		}
               
                $viewname = $this->jevlayout;
		// use false to include port and scheme in URL to stop mixed http and https content
		$viewpath = JURI::root(false) . "/components/$compname/views/".$viewname."/assets";
		$viewimages = $viewpath . "/images";
		$linkpref = "index.php?option=$compname&Itemid=".$this->myItemid.$this->cat."&task=";

		/*
		$linkprevious = $linkpref."month.calendar&day=$cal_day&month=$cal_prev_month&year=$cal_prev_month_year";
		$linkprevious = JRoute::_($linkprevious);
		$linkprevious = $this->htmlLinkCloaking($linkprevious, '<img border="0" title="previous month" alt="previous month" src="'.$viewimages.'/mini_arrowleft.gif"/>' );
		*/
		$jev_component_name  = JEV_COM_COMPONENT;
		$this->_navigationJS($this->_modid);
		if( $this->minical_prevmonth ){
			$linkprevious = htmlentities("index.php?option=$jev_component_name&task=modcal.ajax&day=1&month=$base_prev_month&year=$base_prev_month_year&modid=$this->_modid&tmpl=component".$this->cat);
			$linkprevious = '<img border="0" title="previous month" alt="'.JText::_("JEV_LAST_MONTH").'" class="mod_events_link" src="'.$viewimages.'/mini_arrowleft.gif" onmousedown="callNavigation(\''.$linkprevious.'\');" />';
		}
		else {
			$linkprevious = "";
		}
		if( $this->minical_prevyear ){
			$linkpreviousyear = htmlentities("index.php?option=$jev_component_name&task=modcal.ajax&day=1&month=$cal_month&year=$cal_prev_month_year&modid=$this->_modid&tmpl=component".$this->cat);
			$linkpreviousyear = '<img border="0" title="previous month" alt="'.JText::_("JEV_LAST_YEAR").'" class="mod_events_link" src="'.$viewimages.'/mini_arrowleft.gif" onmousedown="callNavigation(\''.$linkpreviousyear.'\');" />';
		}
		else {
			$linkpreviousyear = "";
		}

		if( $this->minical_actmonth == 1 ){
			$linkcurrent = $linkpref."month.calendar&day=$cal_day&month=$cal_month&year=$cal_year";
			$linkcurrent = JRoute::_($linkcurrent);
			$linkcurrent = $this->htmlLinkCloaking($linkcurrent, $month_name." ".$cal_year, array("style"=>"text-decoration:none;color:inherit;"));
		}
		elseif( $this->minical_actmonth == 2 ){
			 $linkcurrent = $month_name." ".$cal_year;
		}
		else {
			$linkcurrent = "";
		}
		/*
		$linknext = $linkpref."month.calendar&day=$cal_day&month=$cal_next_month&year=$cal_next_month_year";
		$linknext = JRoute::_($linknext);
		$linknext = $this->htmlLinkCloaking($linknext, '<img border="0" title="next month" alt="next month" src="'.$viewimages.'/mini_arrowright.gif"/>' );
		*/
		$this->_navigationJS($this->_modid);
		if( $this->minical_nextmonth ){
			$linknext = htmlentities("index.php?option=$jev_component_name&task=modcal.ajax&day=1&month=$base_next_month&year=$base_next_month_year&modid=$this->_modid&tmpl=component".$this->cat);
			$linknext = '<img border="0" title="next month" alt="'.JText::_("JEV_NEXT_MONTH").'" class="mod_events_link" src="'.$viewimages.'/mini_arrowright.gif" onmousedown="callNavigation(\''.$linknext.'\');" />';
		}
		else {
			$linknext ="";
		}

		if( $this->minical_nextyear ){
			$linknextyear = htmlentities("index.php?option=$jev_component_name&task=modcal.ajax&day=1&month=$cal_month&year=$cal_next_month_year&modid=$this->_modid&tmpl=component".$this->cat);
			$linknextyear = '<img border="0" title="next month" alt="'.JText::_("JEV_NEXT_YEAR").'" class="mod_events_link" src="'.$viewimages.'/mini_arrowright.gif" onmousedown="callNavigation(\''.$linknextyear.'\');" />';
		}
		else {
			$linknextyear ="";
		}

		$content = <<<START
<div id="extcal_minical">
	<table cellspacing="1" cellpadding="0" border="0" align="center" style="width:$width; height:$height; border: 1px solid rgb(190, 194, 195); background-color: rgb(255, 255, 255);">
		<tr>
			<td style="vertical-align: top;">
START;
		if( $this->minical_showlink ){
		$content .= <<<START
			
				<table width="100%" cellspacing="0" cellpadding="2" border="0" class="extcal_navbar">
					<tr>
						<td valign="middle" height="18" align="center">
							$linkpreviousyear
						</td>
						<td valign="middle" height="18" align="center">
							$linkprevious
						</td>
						<td width="98%" valign="middle" nowrap="nowrap" height="18" align="center" class="extcal_month_label">
							$linkcurrent
						</td>
						<td valign="middle" height="18" align="center" style="margin: 0 auto; min-width: 4px;">
							$linknext
						</td>
						<td valign="middle" height="18" align="center" style="margin: 0 auto; min-width: 4px;">
							$linknextyear
						</td>
					</tr>
				</table>
START;
}
		$content .= <<<START
				<table class="extcal_weekdays" style="width:100%;">
START;
		$lf="\n";		

		// Days name rows - with blank week no.
		$content	.= "<tr>\n<td/>\n";
		for ($i=0;$i<7;$i++) {
			$content.="<td  class='extcal_weekdays'>".$day_name[($i+$startday)%7]."</td>".$lf	;
		}
		$content.="</tr>\n";

		$datacount = count($data["dates"]);
		$dn=0;
		for ($w=0;$w<6 && $dn<$datacount;$w++){
			$content .="<tr style='height:$rowheight;'>\n";
			// the week column
			list($week,$link) = each($data['weeks']);
			$content .= '<td class="extcal_weekcell">';
			$content .= $this->htmlLinkCloaking($link, "<img width='5' height='20' border='0' alt='week ".$week."' src='".$viewimages."/icon-mini-week.gif'/>" );
			$content .= "</td>\n";

			for ($d=0;$d<7 && $dn<$datacount;$d++){
				$currentDay = $data["dates"][$dn];
				switch ($currentDay["monthType"]){
					case "prior":
					case "following":
						$content .= "<td class='extcal_othermonth'/>\n";
						break;
					case "current":

						$dayOfWeek=JevDate::strftime("%w",$currentDay["cellDate"]);

						$class = ($currentDay["today"]) ? "extcal_todaycell" : "extcal_daycell";
						$linkclass = "extcal_daylink";
						if($dayOfWeek==0 && !$currentDay["today"]) {
							$class = "extcal_sundaycell";
							$linkclass = "extcal_sundaylink";
						}

						if ($currentDay["events"] || $this->modparams->get("noeventcheck",0)){
							$linkclass = "extcal_busylink";
						}
						$content .= "<td class='".$class."'>\n";
						$tooltip = $this->getTooltip($currentDay, array('class'=>$linkclass));
						if ($tooltip) {
							$content .= $tooltip;
						}
                                                else {
                                                    if ($this->modparams->get("emptydaylinks", 1) || $currentDay["events"] || $this->modparams->get("noeventcheck",0)) {
							$content .= $this->htmlLinkCloaking($currentDay["link"], $currentDay['d'], array('class'=>$linkclass,'title'=> JText::_('JEV_CLICK_TOSWITCH_DAY')));
                                                    } else {
                                                        $content .= $currentDay['d'];
                                                    }
						}

						$content .="</td>\n";
						break;

				}
				$dn++;
			}
			$content .="</tr>\n";
		}
		$content .= "</table>\n";
		$content .= "</td></tr></table></div>\n";

		// Now check to see if this month needs to have at least 1 event in order to display
		//			if (!$monthMustHaveEvent || $monthHasEvent) return $content;
		//			else return '';
		return $content;
	}

	 protected function getTooltip($currentDay, $linkattr) {
		$tooltip = "";
		if (!isset($currentDay["events"]) || !is_array($currentDay["events"]) ||  count($currentDay["events"])==0){
			return $tooltip;
		}
		// load core and extra mootools
		JHTML::_('behavior.framework');
		JHtmlBehavior::framework();
		JHtmlBehavior::framework(true);

		foreach ($currentDay["events"] as $event) {
			$tooltip .= $event->title()."<br/>";
		}
		$tooltip .= "<hr/><small>".JText::_("JEV_EVENTS_CLICK_FOR_MORE_DETAILS",true)."</small>";

		$tipTitle = '<div class="jevtt_title" >'.JText::_("JEV_EVENTS_THIS_DAY",true) .'</div>';
		$tipText = '<div class="jevtt_text">'.$tooltip.'</div>';
		$tooltip	= htmlspecialchars($tipTitle.$tipText,ENT_QUOTES);
		$link = $this->htmlLinkCloaking($currentDay["link"], $currentDay['d'], $linkattr);
		$tooltip = '<span class="editlinktip hasjevtip" title="'.$tooltip.'" >'.$link.'</span>';

		static $script;
		if (!isset($script	)){
			$script = "
			<script language='javascript'>
			function setTooltips(){
				if (window.parent){
					var tipelems = parent.$$('.hasjevtip');
					var JTooltips = new parent.Tips(parent.$$('.hasjevtip'), { maxTitleChars: 50, fixed: false});
				}
			}
			//window.onload=setTooltips;
			setTimeout(setTooltips,200);
			</script>
			";
			$tooltip .= $script;
		}

		return $tooltip;
	 }


}
