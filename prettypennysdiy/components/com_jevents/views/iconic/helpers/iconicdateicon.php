<?php 
defined('_JEXEC') or die('Restricted access');

function Iconicdateicon($view,$lines, $title, $href, $class="", $event=false, $Itemid=false){
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	if ($event && $href && $params->get('iconlinkstoevent',0)){
		if (!$Itemid){
			$Itemid = JRequest::getInt("Itemid");
		}
		$href = $event->viewDetailLink($event->yup(),$event->mup(),$event->dup(), true, $Itemid);
	}
	/*
	 * replace date icon for multi-day event with today's date
	 */
	/*
	list($year,$month,$day) = JEVHelper::getYMD();
	if ($event && $event->_multiday && !($event->yup()==$year && $event->mup()==$month && $event->dup()==$day) && count($lines)==2) {
		$datestp = JevDate::mktime(0, 0, 0, $month, $day, $year);
		$lines = explode(":", JEV_CommonFunctions::jev_strftime("%d:%b", $datestp));
	}
	 */
	$html = "";
	if (count($lines)==2) list($line1,$line2) = $lines;
	else {
		$line2 = $lines[0];
		$line1=false;
	}
	if ($href!=""){
		if ($line1){
			$html .= '<a class="jevdateicon '.$class.'" href="' . $href . '" title="' . $title . '"><span class="jevdateicon1">' . $line1.'</span><span class="jevdateicon2">'.$line2.'</span></a>'."\n";
		}
		else {
			$html .= '<a class="jevdateicon '.$class.'" href="' . $href . '" title="' . $title . '"><span class="jevdateicon2">' . $line2.'</span></a>'."\n";
		}
	}
	else {
		if ($line2){
			$html .= '<span class="jevdateicon '.$class.'" ><span class="jevdateicon1">' . $line1.'</span><span class="jevdateicon2">'.$line2.'</span></span>'."\n";
		}
		else {
			$html .= '<span class="jevdateicon '.$class.'" ><span class="jevdateicon2">' . $line2.'</span></span>'."\n";
		}
	}

	// Optional code to display end date too for multi-day events!
	/*
	if ($event && $event->_multiday && !($event->yup()==$event->ydn() && $event->mup()==$event->mdn() && $event->dup()==$event->ddn()) && count($lines)==2) {
		$datestp = JevDate::mktime(0, 0, 0, $event->mdn() , $event->ddn(), $event->ydn() );
		$lines = explode(":", JEV_CommonFunctions::jev_strftime("%d:%b", $datestp));
		if (count($lines)==2) list($line1,$line2) = $lines;
		else {
			$line2 = $lines[0];
			$line1=false;
		}
		if ($href!=""){
			if ($line1){
				$html .= '<a class="jevdateicon '.$class.'" href="' . $href . '" title="' . $title . '"><span class="jevdateicon1">' . $line1.'</span><span class="jevdateicon2">'.$line2.'</span></a>'."\n";
			}
			else {
				$html .= '<a class="jevdateicon '.$class.'" href="' . $href . '" title="' . $title . '"><span class="jevdateicon2">' . $line2.'</span></a>'."\n";
			}
		}
		else {
			if ($line2){
				$html .= '<span class="jevdateicon '.$class.'" ><span class="jevdateicon1">' . $line1.'</span><span class="jevdateicon2">'.$line2.'</span></span>'."\n";
			}
			else {
				$html .= '<span class="jevdateicon '.$class.'" ><span class="jevdateicon2">' . $line2.'</span></span>'."\n";
			}
		}
	}
	 */

	return $html;
}