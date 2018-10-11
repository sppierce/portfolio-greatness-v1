<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2015-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

function gwejson_skiptoken()
{
	// This file checks the token
	return true;

}

function ProcessJsonRequest(&$requestObject, $returnData)
{
	$returnData->discount = 0;
	$returnData->surcharge = 0;

	$lparams = JComponentHelper::getParams('com_languages');
	$sitelang = $lparams->get('site', 'en-GB');
	$sitelang = JLanguage::getInstance($sitelang, 0);
	JFactory::$language = $sitelang;

	$params = JComponentHelper::getParams("com_rsvppro");

	// Enforce referrer
	if ($params->get("testreferrer", 0))
	{
		if (!array_key_exists("HTTP_REFERER", $_SERVER))
		{
			throwerror("There was an error");
		}

		$live_site = $_SERVER['HTTP_HOST'];
		$ref_parts = parse_url($_SERVER["HTTP_REFERER"]);

		if (!isset($ref_parts["host"]) || $ref_parts["host"] != $live_site)
		{
			throwerror("There was an error - missing host in referrer");
		}
	}


	if (!isset($requestObject->rp_id) || $requestObject->rp_id == 0)
	{
		throwerror("There was an error - repeat id");
	}

	if (!isset($requestObject->fieldid) || intval($requestObject->fieldid) == 0)
	{
		throwerror("There was an error - no such field");
	}

	$token = JSession::getFormToken();;
	if (!isset($requestObject->token) || $requestObject->token != $token)
	{
		throwerror("There was an error - bad token.  Please refresh the page and try again.");
	}

	$db = JFactory::getDBO();
	$db->setQuery("SELECT * FROM #__jevents_repetition as rpt LEFT JOIN #__jevents_vevent as evt on evt.ev_id=rpt.eventid where rp_id=" . intval($requestObject->rp_id));
	$event = $db->loadObject();
	if (!$event)
	{
		throwerror("There was an error - no such repeat");
	}

	if ($requestObject->error)
	{
		return "Error";
	}
	// title is actually the coupon code!
	if (isset($requestObject->title) && trim($requestObject->title) !== "")
	{
		$returnData->result = "title is " . $requestObject->title;
	}
	else
	{
		throwerror("There was an error - no valid argument");
	}

	$db->setQuery("SELECT * FROM #__jev_rsvp_fields  where field_id=" . intval($requestObject->fieldid));
	$field = $db->loadObject();
	if (!$field || $field->type != "jevrcoupon")
	{
		throwerror("There was an error - not a coupon");
	}

	$db->setQuery("SELECT * FROM #__jev_attendance where id=" . intval($requestObject->atd_id));
	$rsvpdata = $db->loadObject();

	if (strlen($requestObject->title) < 1)
	{
		$returnData->discount = 0;
		$returnData->surcharge = 0;
		return $returnData;
	}

	$db = JFactory::getDBO();

	$fieldoptions = json_decode($field->options);
	$i = 0;

	if (isset($requestObject->atdee_id) && $requestObject->atdee_id>0){
		$sql = "SELECT * FROM #__jev_attendees where id=" . intval($requestObject->atdee_id);
		if (!$rsvpdata->allrepeats){
			$sql .= " AND rp_id=".intval($requestObject->rp_id);
		}
		$db->setQuery($sql);
		$attendee = $db->loadObject();
		$created = strtotime($attendee->created);
	}
	else {
		$created = mktime();
	}

	foreach ($fieldoptions->label as $code)
	{
		if (trim($code) == trim($requestObject->title))
		{
			if (isset($fieldoptions->validfrom)){
				$validfrom = $fieldoptions->validfrom[$i];
				$validto	= $fieldoptions->validto[$i];
				$validFrom = strtotime(($validfrom ?$validfrom : "1970-01-01"). " 00:00:00");
				$validTo = strtotime(($validto?$validto:"2199-12-31"). " 23:59:59");

				if ($created>=$validFrom && $created<=$validTo){
					// coupontype=0 for fixed and 1 for percentage
					if (isset($fieldoptions->type[$i]) && $fieldoptions->type[$i]==1){
						$discount = 0;
						$surcharge = -$fieldoptions->price[$i];
					}
					else {
						$discount = -$fieldoptions->price[$i];
						$surcharge = 0;
					}
				}
				else 	if ($created<$validFrom && trim($code) != ""){
					throwerror(JText::_("RSVP_COUPON_NOT_YET_ACTIVE", true));
				}
				else if ($created>$validTo && trim($code) != ""){
					throwerror(JText::_("RSVP_COUPON_HAS_EXPIRED", true));
				}
			}
			else {
				// coupontype=0 for fixed and 1 for percentage
				if (isset($fieldoptions->type[$i]) && $fieldoptions->type[$i]==1){
					$discount = 0;
					$surcharge = -$fieldoptions->price[$i];
				}
				else {
					$discount = -$fieldoptions->price[$i];
					$surcharge = 0;
				}

			}
		}
		$i++;
	}

	if (!isset($discount)){
		$returnData->discount = 0;
		$returnData->surcharge = 0;
		return $returnData;
	}
	$fieldparams = json_decode($field->params);
	if (isset($fieldparams->maxuses) && $fieldparams->maxuses > 0)
	{
		$sql = "SELECT * FROM #__jev_rsvp_couponusage  where atd_id=" . intval($requestObject->atd_id);
		if (!$rsvpdata->allrepeats){
			$sql .= " AND rp_id=".intval($requestObject->rp_id);
		}
		$db->setQuery($sql);
		$couponusage = $db->loadObject();
		if ($couponusage){
			$couponparams = json_decode($couponusage->params);
			$fieldname = "field".$field->field_id;
			$couponcode = trim($requestObject->title);
			// is this a valid coupon code and could we be at the max use limit!
			if (isset($couponparams->$fieldname) && isset($couponparams->$fieldname->$couponcode) && intval($couponparams->$fieldname->$couponcode)+1 > $fieldparams->maxuses ){
				$canusecoupon = false;
				// make sure we are not already using this same coupon
				if ($requestObject->atdee_id >0){
					$sql = "SELECT * FROM #__jev_attendees where id=" . intval($requestObject->atdee_id);
					if (!$rsvpdata->allrepeats){
						$sql .= " AND rp_id=".intval($requestObject->rp_id);
					}
					$db->setQuery($sql);
					$attendeedata = $db->loadObject();
					if ($attendeedata){
						$attendeeparams = json_decode($attendeedata->params);
						if (isset($attendeeparams->$fieldname) && trim($attendeeparams->$fieldname)==$couponcode){
							$canusecoupon	= true;
							//throwerror("Already using coupon");
						}
					}
				}
				if (!$canusecoupon) {
					// Load language
					$lang = JFactory::getLanguage();
					$lang->load("plg_jevents_jevrsvppro", JPATH_ADMINISTRATOR);

					throwerror(JText::_("RSVP_COUPON_ALREADY_USED", true));
				}
			}
			//JText::

		}
	}

	$returnData->discount = $discount;
	$returnData->surcharge = $surcharge;

	return $returnData;
}
