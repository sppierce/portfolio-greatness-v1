<?php

/**
 * @copyright	Copyright (C) 2015-2017 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
 */
function ProcessJsonRequest(&$requestObject, $returnData)
{

	$returnData = array();

	ini_set("display_errors", 0);

	$input = JFactory::getApplication()->input;

	include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");

	$token = JSession::getFormToken();
	;
	if ((isset($requestObject->token) && $requestObject->token != $token) || JFactory::getApplication()->input->get('token', '', 'string') != $token)
	{
		PlgSystemGwejson::throwerror("There was an error - bad token.  Please refresh the page and try again.");
	}

	$user = JFactory::getUser();
	if ($user->id == 0 && isset($requestObject->task) && $requestObject->task == "checkEmail")
	{
		$returnData["existingemail"] = 0;
		$returnData["existingattendee"] = 0;

		$db = JFactory::getDBO();
		$title = JFilterInput::getInstance()->clean($requestObject->address, "string");
		$text = $db->Quote($db->escape(strtolower($title), false), false);

		if (trim($title) == "" && trim($title) == "")
		{
			PlgSystemGwejson::throwerror("There was an error - no valid argument");
		}

		if (strlen($title) < 2 && $title != "*")
		{

			return $returnData;
		}

		$sql = "SELECT username, name, id  FROM #__users WHERE email = " . $text . " AND block=0 order by name asc";
	}
	else
	{
		if ($user->id == 0)
		{
			PlgSystemGwejson::throwerror("There was an error");
		}

		// If user is jevents can deleteall or has backend access then allow them to specify the creator
		$jevuser = JEVHelper::getAuthorisedUser();
		$access = false;
		if ($user->get('id') > 0)
		{
			$access = $user->authorise('core.deleteall', 'com_jevents');
		}

		$db = JFactory::getDBO();
		$json = $input->get('json', '', 'raw');
		if (!isset($json["rp_id"]))
		{
			PlgSystemGwejson::throwerror("There is no event");
		}
		$db->setQuery("SELECT * FROM #__jevents_repetition where rp_id=" . intval($json["rp_id"]));
		$event = $db->loadObject();
		require_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");
		if (!$event)
		{
			PlgSystemGwejson::throwerror("could not fetch event " . intval($json["rp_id"]));
		}

		if ($event)
		{
			$dataModel = new JEventsDataModel();
			$queryModel = new JEventsDBModel($dataModel);

			// Find the repeat
			$event = $dataModel->queryModel->listEventsById(intval($json["rp_id"]), false, "icaldb", false);
		}

		if ($event && ($event->created_by == $user->get('id') || JEVHelper::isAdminUser($user) || JEVHelper::canDeleteEvent($event, $user) || JEVHelper::canPublishEvent($event, $user)))
		{
			$access = true;
		}

		if (!$access)
		{
			PlgSystemGwejson::throwerror("There was an error - no access ");
		}

		$db = JFactory::getDBO();

		// Remove any dodgy characters from fields
		// Only allow a to z , 0 to 9, ', " space (\\040), hyphen (\\-), underscore (\\_)
		/*
		  $regex     = '/[^a-zA-Z0-9_\'\"\'\\40\\-\\_]/';
		  $title    = preg_replace($regex, "", $title);
		  $title = substr($title."    ",0,4);
		 */

		$title = JFilterInput::getInstance()->clean($requestObject->typeahead, "string");
		$text = $db->Quote('%' . $db->escape($title, true) . '%', false);

		if (trim($title) == "" && trim($title) == "")
		{
			PlgSystemGwejson::throwerror("There was an error - no valid argument");
		}

		if (strlen($title) < 2 && $title != "*")
		{

			return $returnData;
		}

		if ($title != "*")
		{
			$sql = "SELECT username, name, id  FROM #__users WHERE (name LIKE " . $text . " OR username LIKE " . $text . "  OR email LIKE " . $text . " ) AND block=0 order by name asc";
		}
		else
		{
			$sql = "SELECT username, name, id  FROM #__users WHERE block = 0";
		}
	}
	$db->setQuery($sql);
	$matches = $db->loadObjectList();

	if (count($matches) > 0)
	{
		foreach ($matches as $match)
		{
			if (isset($requestObject->task) && isset($requestObject->task) == "checkEmail")
			{
				if (!isset($returnData["matches"]))
				{
					$returnData["matches"] = array();
				}
			}
			$result = new stdClass();
			if (isset($requestObject->task) && isset($requestObject->task) == "checkEmail")
			{
				// Don't send real data for security reasons
				$result->title = "title";
				$result->user_id = 1;
			}
			else
			{
				$result = $match;
				$result->title = $match->name . " (" . $match->username . ")";
			}
			if (isset($requestObject->task) && isset($requestObject->task) == "checkEmail")
			{
				$returnData["matches"] = $result;
				$returnData["existingemail"] = 1;
			}
			else
			{
				$returnData[] = $result;
			}
		}
	}

	// do we need to check for existing attendees?
	if (isset($requestObject->task) && isset($requestObject->task) == "checkEmail" && $returnData["existingemail"] == 0)
	{

		$emailaddress = $requestObject->address;
		if ($emailaddress != "" && filter_var($emailaddress, FILTER_VALIDATE_EMAIL))
		{
			$emailaddress = trim(strtolower($emailaddress));
			$rsvpid = JFactory::getApplication()->input->getInt('rsvpid', 0);
			$rp_id = JFactory::getApplication()->input->getInt('rp_id', 0);

			if ($rsvpid > 0)
			{
				$sql = "SELECT * FROM #__jev_attendance WHERE id=" . $rsvpid;
				$db->setQuery($sql);
				$rsvpdata = $db->loadObject();

				// Check in case this email address is associated with a Joomla user account
				// check  if we already have this attendee
				$sql = "SELECT * FROM #__jev_attendees WHERE at_id=" . $rsvpid . " and LOWER(email_address)=" . $db->Quote($emailaddress);
				if (!$rsvpdata->allrepeats)
				{
					$sql .= " AND rp_id=" . $rp_id;
				}
				$db->setQuery($sql);
				$testattendee = $db->loadObject();
				if ($testattendee)
				{

					$params = JComponentHelper::getParams("com_rsvppro");

					include_once(JPATH_ADMINISTRATOR . "/components/com_rsvppro/libraries/attendeehelper.php");
					$attendeehelper = new RsvpAttendeeHelper($params);

					$user = JFactory::getUser();
					$datamodel = new JEventsDataModel();
					$row = $datamodel->queryModel->listEventsById($rp_id, 1, "icaldb");
					$attendeehelper->notifyUser($rsvpdata, $row, $user, $testattendee->email_address, $testattendee->email_address, $testattendee, 'ack', $testattendee->waiting);

					$returnData["existingattendee"] = 1;
				}
			}
		}
	}

	return $returnData;
}

/*
	if (!$access){
		PlgSystemGwejson::throwerror("There was an error - no access");
	}

	if (isset($requestObject->typeahead) && trim($requestObject->typeahead)!==""){
		$returnData->result = "title is ".$requestObject->typeahead;
	}
	else {
		PlgSystemGwejson::throwerror ( "There was an error - no valid argument");
	}

 */