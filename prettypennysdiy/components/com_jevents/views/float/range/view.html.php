<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 1400 2009-03-30 08:45:17Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component frontend
 *
 * @static
 */
class FloatViewRange extends JEventsFloatView
{
	
	function listevents($tpl = null)
	{
		JEVHelper::componentStylesheet($this);

		$params = JComponentHelper::getParams( JEV_COM_COMPONENT );

		list($startdate, $enddate) = $this->getStartEndDates();

		$jevStartDate = new JevDate($startdate);
		$jevEndDate = new JevDate($enddate);
		$interval = date_diff($jevStartDate, $jevEndDate);
		$this->assign("current_date", array(
			"sdate" => $jevStartDate->format('Y-m-d'),
			"syear"=>$jevStartDate->format('Y'),
			"smonth" => $jevStartDate->format('m'),
			"sday" => $jevStartDate->format('d'),
			"edate" => $jevEndDate->format('Y-m-d'),
			"eyear" => $jevEndDate->format('Y'),
			"emonth" => $jevEndDate->format('m'),
			"eday" => $jevEndDate->format('d'),

		));
		//echo "current dates ".$jevStartDate->format('Y-m-d H:i:s'). " - ".$jevEndDate->format('Y-m-d H:i:s')."<br/>";

		$jevNextStartDate = date_add($jevStartDate, $interval);
		$jevNextEndDate = date_add($jevEndDate, $interval);
		//echo "next dates ".$jevNextStartDate->format('Y-m-d H:i:s'). " - ".$jevNextEndDate->format('Y-m-d H:i:s')."<br/>";
		$this->assign("next_date", array(
			"sdate" => $jevNextStartDate->format('Y-m-d'),
			"syear"=>$jevNextStartDate->format('Y'),
			"smonth" => $jevNextStartDate->format('m'),
			"sday" => $jevNextStartDate->format('d'),
			"edate" => $jevNextEndDate->format('Y-m-d'),
			"eyear" => $jevNextEndDate->format('Y'),
			"emonth" => $jevNextEndDate->format('m'),
			"eday" => $jevNextEndDate->format('d'),
		));

		// NB date_add and date_sub changes the value of the first parameter so take care
		$jevStartDate = new JevDate($startdate);
		$jevEndDate = new JevDate($enddate);
		$jevPrevStartDate = date_sub($jevStartDate, $interval);
		$jevPrevEndDate = date_sub($jevEndDate, $interval);
		//echo "previous dates ".$jevPrevStartDate->format('Y-m-d H:i:s'). " - ".$jevPrevEndDate->format('Y-m-d H:i:s')."<br/>";
		$this->assign("prev_date", array(
			"sdate" => $jevPrevStartDate->format('Y-m-d'),
			"syear"=>$jevPrevStartDate->format('Y'),
			"smonth" => $jevPrevStartDate->format('m'),
			"sday" => $jevPrevStartDate->format('d'),
			"edate" => $jevPrevEndDate->format('Y-m-d'),
			"eyear" => $jevPrevEndDate->format('Y'),
			"emonth" => $jevPrevEndDate->format('m'),
			"eday" => $jevPrevEndDate->format('d'),
		));

		list($startyear,$startmonth,$startday)=explode("-",$startdate);
		list($startyear,$startmonth,$startday)=explode("-",$startdate);
		list($endyear,$endmonth,$endday)=explode("-",$enddate);
		
		$this->assign("startdate",$startdate);
		$this->assign("startyear",$startyear);
		$this->assign("startmonth",$startmonth);
		$this->assign("startday",$startday);
		$this->assign("enddate",$enddate);
		$this->assign("endyear",$endyear);
		$this->assign("endmonth",$endmonth);
		$this->assign("endday",$endday);

		$order = $params->get("dataorder", "rpt.startrepeat asc, rpt.endrepeat ASC, det.summary ASC");
		
		// Note that using a $limit value of -1 the limit is ignored in the query
		$cfg = JEVConfig::getInstance();
		$total = $this->datamodel->queryModel->countIcalEventsByRange( $startdate,$enddate,  $cfg->get('com_showrepeats'));
		$this->assign("total", $total);

		//$this->assign("eventdata",$this->datamodel->getRangeData($startdate,$enddate,$this->limit, $this->limitstart, $order));
                
		$this->assign("data",array("total"=>$total,  "limit"=>$this->limit,  "limitstart"=>$this->limitstart));

	}
}
