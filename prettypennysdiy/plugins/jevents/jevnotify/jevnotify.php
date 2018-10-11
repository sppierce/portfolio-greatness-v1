<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
JLoader::register('JevJoomlaVersion', JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/version.php");
JLoader::register('JEVNotifyHelper', JPATH_SITE . "/plugins/jevents/jevnotify/helper.php");

class plgJEventsJevnotify extends JPlugin
{

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		JFactory::getLanguage()->load('plg_jevents_jevnotify', JPATH_ADMINISTRATOR);

	}

	function onAfterSaveEvent($event, $dryrun = false)
	{
		if ($dryrun)
			return;

		if ($event->ev_id > 0 && JRequest::getInt("evid", 0) == $event->ev_id)
		{
			$cmd = "modified";
			$ev_id = $event->ev_id;

			// get the data and query models
			$dataModel = new JEventsDataModel();
			$jevent = $dataModel->queryModel->getEventById(intval($ev_id), 1, "icaldb");

			if ($jevent)
			{
				// Unpublished events trigger same as deletion of event
				if (!$jevent->published())
				{
					$this->deleteEvent($jevent);
				}
				else
				{
					$this->changedEvent($jevent);
				}
			}
		}
		else
		{
			$cmd = "created";
			$ev_id = $event->ev_id;

			// get the data and query models
			$dataModel = new JEventsDataModel();
			$jevent = $this->loadEvent($ev_id);

			if ($jevent && ($jevent->published() || (isset($event->published) && $event->published)))
			{
				$this->newEvent($jevent);
			}
		}

		return true;

	}

	function onPublishEvent($cid, $newstate)
	{
		// We assume any published events are NEW - we have no way of knowing otherwise !
		if (is_array($cid))
		{
			foreach ($cid as $ev_id)
			{
				// get the data and query models
				$dataModel = new JEventsDataModel();
				$jevent = $dataModel->queryModel->getEventById(intval($ev_id), 1, "icaldb");
				if ($jevent)
				{
					// Unpublished events trigger same as deletion of event
					if ($newstate == 0 || $newstate == -1)
					{
						$this->deleteEvent($jevent);
					}
					else
					{
						$this->newEvent($jevent);
					}
				}
			}
		}

	}

	/*
	 * this will not work because the database entries are already gone
	  function onDeleteEventDetails($idlist) {
	  // delete the metatags too
	  $db = JFactory::getDBO();

	  $query = "SELECT DISTINCT (rp_id) FROM #__jevents_repetition WHERE eventdetail_id IN ($idlist)";
	  $db->setQuery( $query);
	  $repeatids = $db->loadColumn();

	  foreach ($repeatids as $repeatid){

	  }
	  return true;

	  }
	 */

	function onDeleteEventRepeat($id)
	{


		$sendAsICal = false;
		$hasRsvpPro = false;

		jimport('joomla.filesystem.folder');
		if (JFolder::exists(JPATH_SITE . "/administrator/components/com_rsvppro/"))
		{

			$params = JComponentHelper::getParams("com_rsvppro");

			$hasRsvpPro = true;
			if ($params->get("invite_icals", 0) && $this->params->get("send_as_ical", 0))
			{
				$sendAsICal = true;
			}
		}

		$task = JRequest::getString("task", "");
		//Set if we are using the main event or the repeat of it.
		$m_ev = 0;
		if ($task == "icalevent.save" || $task == "icalevent.apply")
		{
			$m_ev = 1;
		}


		if ($sendAsICal)
		{
			//Find the event details
			$dataModel = new JEventsDataModel();
			$jevent_rp = $dataModel->queryModel->listEventsById(intval($id), 1, "icaldb");

			$creator = JEVHelper::getUser($jevent_rp->created_by());

			if ($jevent_rp->created_by() == 0)
			{
				$dispatcher = JEventDispatcher::getInstance();
				$dispatcher->trigger('onDisplayCustomFields', array(&$jevent_rp));
				$creator = new stdClass();
				if (isset($jevent_rp->authorname) && isset($jevent_rp->authoremail))
				{
					$jevent_rp->email = $jevent_rp->authoremail;
					$jevent_rp->name = $jevent_rp->authorname;
				}
			}

			$sendname = $this->params->get("sendname", "");
			$sendemail = $this->params->get("sendaddress", "");

			if (isset($sendemail) && $sendemail != "")
			{
				$creator->email = $sendemail;
			}
			if (isset($sendname) && $sendname != "")
			{
				$creator->name = $sendname;
			}

			$db = JFactory::getDBO();
			jimport("joomla.utilities.date");
			if (class_exists("JevDate"))
			{
				$dateClass = "JevDate";
			}
			else
			{
				$dateClass = "JDate";
			}
			$now = new $dateClass("+0 seconds");

			$now = $now->toSql();
			$lag = intval($this->params->get("lag", 0));
			$lagtime = new $dateClass("-$lag seconds");
			$lagtime = $lagtime->toSql();


			if (class_exists("JevDate"))
			{
				$date = JevDate::getDate("+0 seconds");
			}
			else
			{
				$date = JFactory::getDate('+0 seconds');
			}
			$created = $date->toSql();

			//notifiy invitees ?
			$type = 2;
			$notifications = $this->params->get("deletenotifications");

			$db = JFactory::getDBO();

			$task = JRequest::getString("task", "");
			$n_extras['m_ev'] = $m_ev;
			$subject = JEVNotifyHelper::parseMessage($this->params->get("deletesubject"), $jevent_rp, $notifications, $creator);
			$message = JEVHelper::iCalMailGenerator($jevent_rp, $n_extras, $ics_method = "CANCEL");

			foreach ($notifications as $notification)
			{

				if ($notification < 0)
				{
					$db->setQuery("REPLACE INTO #__jev_notifications (rp_id,  messagetype, user_id, created, subject, message, creatoremail, creatorname, m_ev)
                                        SELECT  " . $db->Quote($id) . " as rpid,$type as type, user_id, " . $db->Quote($created) . " as created, " . $db->Quote($subject) . ", " . $db->Quote($message) . ", " . $db->Quote($sendemail) . ", " . $db->Quote($sendname) . ", " . $db->Quote($m_ev) . " as m_ev   FROM #__user_usergroup_map  where group_id=" . (-$notification) . " group by user_id");
					// Joomfish bug workaround
					$db->_skipSetRefTables = true;
					$db->query();
					$db->_skipSetRefTables = false;
				}


				// invitees
				$sql = "SELECT * FROM #__jev_attendance WHERE ev_id=" . $jevent_rp->ev_id();
				$db->setQuery($sql);
				$rsvpdata = $db->loadObject();

				if ($rsvpdata)
				{
					$sql = "SELECT a.id, a.user_id FROM #__jev_invitees as a WHERE a.at_id=" . $rsvpdata->id;

					if ($task == "icalrepeat.save" || $task == "icalrepeat.apply" || $task == "icals.reload" || ($task == "icalrepeat.delete" && $rsvpdata->allinvites == 0))
					{
						$sql .= " and a.rp_id=" . $id;
					}


					// only notify invitees who have viewed the event
					if ($this->params->get("whichinvitees") == 1)
					{
						$sql .= " and a.viewedevent=1";
					}

					$db->setQuery($sql);
					$invitees = $db->loadObjectList();

					if ($invitees && count($invitees) > 0)
					{
						$userids = array(-1);
						$atids = array(-1);
						foreach ($invitees as $invitee)
						{
							if (!in_array($invitee->user_id, $userids) && $invitee->user_id > 0)
							{
								$userids[] = $invitee->user_id;
								$atids[] = $invitee->id;
								//$message[] = $subject = JEVNotifyHelper::parseMessage($pparams->get("deletesubject"), $event, $notification, $creator);
								//$message = JEVNotifyHelper::parseMessage($pparams->get("deletemessage"), $event, $notification, $creator);
							}
						}
						// now remove any notifications for attendees who we are about to insert
						$db->setQuery("DELETE FROM #__jev_notifications WHERE messagetype=$type AND rp_id=$id AND sentmessage=0 AND user_id IN(" . implode(",", $userids) . ")");
						$db->query();

						// Now REPLACE the new entries
						$db->setQuery("REPLACE INTO #__jev_notifications (rp_id, messagetype, invitee_id, created, user_id, emailaddress, notificationtype, subject, message, creatoremail, creatorname, m_ev)
									SELECT  " . $db->Quote($id) . " as rpid,$type as type, id, " . $db->Quote($created) . " as created, user_id, email_address, '0', " . $db->Quote($subject) . ", " . $db->Quote($message) . ", " . $db->Quote($sendemail) . ", " . $db->Quote($sendname) . ", " . $db->Quote($m_ev) . " as m_ev FROM #__jev_invitees where id IN(" . implode(",", $atids) . ")");
						// Joomfish bug workaround
						$db->_skipSetRefTables = true;
						$db->query();
						$db->_skipSetRefTables = false;
					}
				}
			}
		}
		else
		{

			//Find the event details
			$dataModel = new JEventsDataModel();
			$jevent_rp = $dataModel->queryModel->listEventsById(intval($id), 1, "icaldb");

			$creator = JEVHelper::getUser($jevent_rp->created_by());

			if ($jevent_rp->created_by() == 0)
			{
				$dispatcher = JEventDispatcher::getInstance();
				$dispatcher->trigger('onDisplayCustomFields', array(&$jevent_rp));
				$creator = new stdClass();
				if (isset($jevent_rp->authorname) && isset($jevent_rp->authoremail))
				{
					$jevent_rp->email = $jevent_rp->authoremail;
					$jevent_rp->name = $jevent_rp->authorname;
				}
			}

			$sendname = $this->params->get("sendname", "");
			$sendemail = $this->params->get("sendaddress", "");

			if (isset($sendemail) && $sendemail != "")
			{
				$creator->email = $sendemail;
			}
			if (isset($sendname) && $sendname != "")
			{
				$creator->name = $sendname;
			}

			$db = JFactory::getDBO();
			jimport("joomla.utilities.date");
			if (class_exists("JevDate"))
			{
				$dateClass = "JevDate";
			}
			else
			{
				$dateClass = "JDate";
			}
			$now = new $dateClass("+0 seconds");

			$now = $now->toSql();
			$lag = intval($this->params->get("lag", 0));
			$lagtime = new $dateClass("-$lag seconds");
			$lagtime = $lagtime->toSql();

			if (class_exists("JevDate"))
			{
				$date = JevDate::getDate("+0 seconds");
			}
			else
			{
				$date = JFactory::getDate('+0 seconds');
			}
			$created = $date->toSql();

			//notifiy invitees ?
			$type = 2;
			$notifications = $this->params->get("deletenotifications");

			$db = JFactory::getDBO();

			$task = JRequest::getString("task", "");

			$subject = JEVNotifyHelper::parseMessage($this->params->get("deletesubject"), $jevent_rp, $notifications, $creator);
			$message = JEVNotifyHelper::parseMessage($this->params->get("deletemessage"), $jevent_rp, $notifications, $creator);

			foreach ($notifications as $notification)
			{

				if ($notification < 0)
				{
					$db->setQuery("REPLACE INTO #__jev_notifications (rp_id,  messagetype, user_id, created, subject, message, creatoremail, creatorname, m_ev)
                                        SELECT  " . $db->Quote($id) . " as rpid,$type as type, user_id, " . $db->Quote($created) . " as created, " . $db->Quote($subject) . ", " . $db->Quote($message) . ", " . $db->Quote($sendemail) . ", " . $db->Quote($sendname) . " , " . $db->Quote($m_ev) . " FROM #__user_usergroup_map  where group_id=" . (-$notification) . " group by user_id");
					// Joomfish bug workaround
					$db->_skipSetRefTables = true;
					$db->query();
					$db->_skipSetRefTables = false;
				}
				if ($hasRsvpPro)
				{
					// invitees
					$sql = "SELECT * FROM #__jev_attendance WHERE ev_id=" . $jevent_rp->ev_id();
					$db->setQuery($sql);
					$rsvpdata = $db->loadObject();
					if ($rsvpdata)
					{
						$sql = "SELECT a.id, a.user_id FROM #__jev_invitees as a WHERE a.at_id=" . $rsvpdata->id;

						if ($task == "icalrepeat.save" || $task == "icalrepeat.apply" || $task == "icals.reload" || $task == "icalrepeat.delete")
						{
							$sql .= " and a.rp_id=" . $id;
						}


						// only notify invitees who have viewed the event
						if ($this->params->get("whichinvitees") == 1)
						{
							$sql .= " and a.viewedevent=1";
						}

						$db->setQuery($sql);
						$invitees = $db->loadObjectList();
						// var_dump($invitees);
						// die();
						if ($invitees && count($invitees) > 0)
						{
							$userids = array(-1);
							$atids = array(-1);
							foreach ($invitees as $invitee)
							{
								if (!in_array($invitee->user_id, $userids) && $invitee->user_id > 0)
								{
									$userids[] = $invitee->user_id;
									$atids[] = $invitee->id;
									//$message[] = $subject = JEVNotifyHelper::parseMessage($pparams->get("deletesubject"), $event, $notification, $creator);
									//$message = JEVNotifyHelper::parseMessage($pparams->get("deletemessage"), $event, $notification, $creator);
								}
							}
							// now remove any notifications for attendees who we are about to insert
							$db->setQuery("DELETE FROM #__jev_notifications WHERE messagetype=$type AND rp_id=$id AND sentmessage=0 AND user_id IN(" . implode(",", $userids) . ")");
							$db->query();

							// Now REPLACE the new entries
							$db->setQuery("REPLACE INTO #__jev_notifications (rp_id, messagetype, invitee_id, created, user_id, emailaddress, notificationtype, subject, message, creatoremail, creatorname)
									SELECT  " . $db->Quote($id) . " as rpid,$type as type, id, " . $db->Quote($created) . " as created, user_id, email_address, '0', " . $db->Quote($subject) . ", " . $db->Quote($message) . ", " . $db->Quote($sendemail) . ", " . $db->Quote($sendname) . " FROM #__jev_invitees where id IN(" . implode(",", $atids) . ")");
							// Joomfish bug workaround
							$db->_skipSetRefTables = true;
							$db->query();
							$db->_skipSetRefTables = false;
						}
					}
				}
			}
		}

	}

	function onStoreCustomDetails(&$evdetail)
	{
		$detailid = $evdetail->evdet_id;

		// Are we saving a repeat - then notify of a change to this repeat
		$task = JRequest::getString("task", "");
		if ($task == "icalrepeat.save" || $task == "icalrepeat.apply" || $task == "icalevent.save" || $task == "icalevent.apply" || $task == "icals.reload")
		{
			$repeat_id = intval(JRequest::getVar("rp_id", "0"));
			// get the data and query models
			$dataModel = new JEventsDataModel();
			$jevent = $dataModel->queryModel->listEventsById(intval($repeat_id), 1, "icaldb");

			if (!$jevent || !$jevent->published())
				return;
			if ($jevent)
			{
				$this->changedEvent($jevent);
			}
		}

	}

	private
			function changedEvent($event)
	{
		$this->cleanUnsent(1, $event->rp_id());

		$this->newUnsent(1, $event);

	}

	private
			function newEvent($event)
	{
		$this->cleanUnsent(0, $event->rp_id());

		$this->newUnsent(0, $event);

	}

	private
			function deleteEvent($event)
	{
		$this->cleanUnsent(2, $event->rp_id());

		$this->newUnsent(2, $event);

	}

	private
			function newUnsent($type, $event, $manual = false)
	{
		$hasRsvpPro = false;

		jimport('joomla.filesystem.folder');
		if (JFolder::exists(JPATH_SITE . "/administrator/components/com_rsvppro/"))
		{
			$hasRsvpPro = true;
		}

		// imported events - we don't know if they have changed so we never set change notifications.
		$task = JRequest::getString("task", "");

		if ($task == "icals.reload")
		{
			$manual = true;
		}

		// if not manually triggered and it is a changed event requireing manual intervention then do nothing here
		if (!$manual && $type == 1 && $this->params->get("changenotificationtype") == 1)
		{
			return;
		}

		$rpid = $event->rp_id();
		if ($type == 0)
		{
			// new event
			$notifications = $this->params->get("neweventnotifications");
		}
		else if ($type == 2)
		{
			// delete event
			$notifications = $this->params->get("deletenotifications");
		}
		else
		{
			// TODO CHECK MANUAL/AUTO TYPE
			// changed event
			$notifications = $this->params->get("changenotifications");
		}

		if (is_string($notifications))
		{
			if ($notifications == 0)
				return;
			$notifications = array($notifications);
		}
		JArrayHelper::toInteger($notifications);
		if (class_exists("JevDate"))
		{
			$date = JevDate::getDate("+0 seconds");
		}
		else
		{
			$date = JFactory::getDate('+0 seconds');
		}
		$created = $date->toSql();
		$db = JFactory::getDBO();

		$task = JRequest::getString("task", "");
		//Set if we are using the main event or the repeat of it.
		$m_ev = 0;
		if ($task == "icalevent.save" || $task == "icalevent.apply" || $task == "icalevent.delete")
		{
			$m_ev = 1;
		}

		foreach ($notifications as $notification)
		{
			if ($notification < 0)
			{
				$db->setQuery("REPLACE INTO #__jev_notifications (rp_id,  messagetype, user_id, created, m_ev)
				SELECT  " . $db->Quote($rpid) . " as rpid,$type as type, user_id, " . $db->Quote($created) . " as created, " . $db->Quote($m_ev) . " as m_ev  FROM #__user_usergroup_map  where group_id=" . (-$notification) . " group by user_id");
				// Joomfish bug workaround
				$db->_skipSetRefTables = true;
				$db->query();
				$db->_skipSetRefTables = false;
			}
			else
			{
				switch ($notification) {
					case 0:
						// Bootstrap doesn't clear the zero select option !!! ARGH!!
						if (count($notifications) == 1)
						{
							return;
						}

						break;
					case 1:
						// registered users
						$db->setQuery("REPLACE INTO #__jev_notifications (rp_id,  messagetype, user_id, created, m_ev)
						SELECT  " . $db->Quote($rpid) . " as rpid,$type as type, id, " . $db->Quote($created) . " as created, " . $db->Quote($m_ev) . " as m_ev  FROM #__users where gid>0 AND gid<19");

						// Joomfish bug workaround
						$db->_skipSetRefTables = true;
						$db->query();
						$db->_skipSetRefTables = false;
						break;
					case 2:
						// special users
						$db->setQuery("REPLACE INTO #__jev_notifications (rp_id,  messagetype, user_id, created, m_ev)
						SELECT  " . $db->Quote($rpid) . " as rpid,$type as type, id, " . $db->Quote($created) . " as created, " . $db->Quote($m_ev) . " as m_ev FROM #__users where gid>=19");

						// Joomfish bug workaround
						$db->_skipSetRefTables = true;
						$db->query();
						$db->_skipSetRefTables = false;
						break;
					case 3:
						if ($hasRsvpPro)
						{
							// attendees
							// RSVP Pro first

							$sql = "SELECT * FROM #__jev_attendance WHERE ev_id=" . $event->ev_id();
							$db->setQuery($sql);
							$rsvpdata = $db->loadObject();
							if ($rsvpdata)
							{
								$sql = "SELECT a.id, a.user_id FROM #__jev_attendees as a WHERE a.at_id=" . $rsvpdata->id;
								if ($task == "icalrepeat.save" || $task == "icalrepeat.apply" || $task == "icals.reload")
								{
									// if attending specific repeat only notify when this particular repeat is changed
									if (!$rsvpdata->allrepeats)
									{
										$sql .= " and a.rp_id=" . $event->rp_id();
									} // attending all repeats to notift if any repeat is changed
									else
									{
										$sql .= " and a.rp_id= 0";
									}
								}
								else
								{
									// if changing the event then always notify
								}
								$sql .= " and a.attendstate!=0";
								$db->setQuery($sql);
								$attendees = $db->loadObjectList();
								if ($attendees && count($attendees) > 0)
								{
									$userids = array(-1);
									$atids = array();
									foreach ($attendees as $attendee)
									{
										if (!in_array($attendee->user_id, $userids) && $attendee->user_id > 0)
										{
											$userids[] = $attendee->user_id;
											$atids[] = $attendee->id;
										}
									}
									// now remove any notifications for attendees who we are about to insert
									$db->setQuery("DELETE FROM #__jev_notifications WHERE messagetype=$type AND rp_id=$rpid AND sentmessage=0 AND user_id IN(" . implode(",", $userids) . ")");
									$db->query();

									// Now REPLACE the new entries
									$db->setQuery("REPLACE INTO #__jev_notifications (rp_id,  messagetype, attendee_id, created, user_id, emailaddress, m_ev)
								SELECT " . $db->Quote($rpid) . " as rpid,$type as type, id, " . $db->Quote($created) . " as created, user_id, email_address, " . $db->Quote($m_ev) . " as m_ev FROM #__jev_attendees  where id IN(" . implode(",", $atids) . ")");
									// Joomfish bug workaround
									$db->_skipSetRefTables = true;
									$db->query();
									$db->_skipSetRefTables = false;
								}
							}
						}
						break;
					case 4:
						if ($hasRsvpPro)
						{

							// invitees
							$sql = "SELECT * FROM #__jev_attendance WHERE ev_id=" . $event->ev_id();
							$db->setQuery($sql);
							$rsvpdata = $db->loadObject();
							if ($rsvpdata)
							{
								$sql = "SELECT a.id, a.user_id FROM #__jev_invitees as a WHERE a.at_id=" . $rsvpdata->id;

								if ($task == "icalrepeat.save" || $task == "icalrepeat.apply" || $task == "icals.reload")
								{
									// if invited specific repeat only notify when this particular repeat is changed
									if (!$rsvpdata->allrepeats)
									{
										$sql .= " and a.rp_id=" . $event->rp_id();
									} // invited all repeats to notift if any repeat is changed
									else
									{
										$sql .= " and a.rp_id= 0";
									}
								}
								else
								{
									// if changing the event then always notify
								}

								// only notify invitees who have viewed the event
								if ($this->params->get("whichinvitees") == 1)
								{
									$sql .= " and a.viewedevent=1";
								}

								$db->setQuery($sql);
								$invitees = $db->loadObjectList();
								if ($invitees && count($invitees) > 0)
								{
									$userids = array(-1);
									$atids = array(-1);
									foreach ($invitees as $invitee)
									{
										if (!in_array($invitee->user_id, $userids) && $invitee->user_id > 0)
										{
											$userids[] = $invitee->user_id;
											$atids[] = $invitee->id;
										}
									}
									// now remove any notifications for attendees who we are about to insert
									$db->setQuery("DELETE FROM #__jev_notifications WHERE messagetype=$type AND rp_id=$rpid AND sentmessage=0 AND user_id IN(" . implode(",", $userids) . ")");
									$db->query();

									// Now REPLACE the new entries
									$db->setQuery("REPLACE INTO #__jev_notifications (rp_id, messagetype, invitee_id, created, user_id, emailaddress, m_ev)
									SELECT  " . $db->Quote($rpid) . " as rpid,$type as type, id, " . $db->Quote($created) . " as created, user_id, email_address, " . $db->Quote($m_ev) . " as m_ev  FROM #__jev_invitees where id IN(" . implode(",", $atids) . ")");
									// Joomfish bug workaround
									$db->_skipSetRefTables = true;
									$db->query();
									$db->_skipSetRefTables = false;
								}
							}
						}
						break;
					case 5:
						if ($hasRsvpPro)
						{

							// remindees
							$sql = "SELECT * FROM #__jev_attendance WHERE ev_id=" . $event->ev_id();
							$db->setQuery($sql);
							$rsvpdata = $db->loadObject();
							if ($rsvpdata)
							{
								$sql = "SELECT a.id, a.user_id FROM #__jev_reminders as a WHERE a.at_id=" . $rsvpdata->id;
								// if reminded for  specific repeat only notify when this particular repeat is changed
								if ($task == "icalrepeat.save" || $task == "icalrepeat.apply" || $task == "icals.reload")
								{
									$sql .= " and ( a.rp_id=" . $event->rp_id() . " OR a.rp_id=0) ";
								}
								else
								{
									// All repeats have changes to notify them all
								}

								$db->setQuery($sql);
								$invitees = $db->loadObjectList();
								if ($invitees && count($invitees) > 0)
								{
									$userids = array(-1);
									$atids = array(-1);
									foreach ($invitees as $invitee)
									{
										if (!in_array($invitee->user_id, $userids) && $invitee->user_id > 0)
										{
											$userids[] = $invitee->user_id;
											$atids[] = $invitee->id;
										}
									}
									// now remove any notifications for attendees who we are about to insert
									$db->setQuery("DELETE FROM #__jev_notifications WHERE messagetype=$type AND rp_id=$rpid AND sentmessage=0 AND user_id IN(" . implode(",", $userids) . ")");
									$db->query();

									// Now REPLACE the new entries
									$db->setQuery("REPLACE INTO #__jev_notifications (rp_id,  messagetype, remindee_id, created, user_id, emailaddress, m_ev)
									SELECT  " . $db->Quote($rpid) . " as rpid,$type as type, id, " . $db->Quote($created) . " as created,  user_id, email_address, " . $db->Quote($m_ev) . " as m_ev FROM #__jev_reminders where id IN(" . implode(",", $atids) . ")");

									// Joomfish bug workaround
									$db->_skipSetRefTables = true;
									$db->query();
									$db->_skipSetRefTables = false;
								}
							}
						}
						break;
					case 6:
						// Event creators - when the event is edited by someone else
						$user = JFactory::getUser();
						if ($user->id != $event->created_by())
						{
							if ($event->created_by() > 0)
							{
								// now remove any notifications for event editors we are about to insert
								$db->setQuery("DELETE FROM #__jev_notifications WHERE messagetype=$type AND rp_id=$rpid AND sentmessage=0 AND user_id=" . $event->created_by());
								$db->query();

								// Now REPLACE the new entries
								$db->setQuery("REPLACE INTO #__jev_notifications (rp_id,  messagetype, user_id, created, m_ev)
								SELECT  " . $db->Quote($rpid) . " as rpid,$type as type, id, " . $db->Quote($created) . " as created, " . $db->Quote($m_ev) . " as m_ev FROM #__users where id=" . $event->created_by());
								// Joomfish bug workaround
								$db->_skipSetRefTables = true;
								$db->query();
								$db->_skipSetRefTables = false;
							}
							else
							{
								$db->setQuery("SELECT * FROM #__jev_anoncreator WHERE ev_id=" . $event->ev_id());
								$creator = @$db->loadObject();

								if ($creator)
								{
									// now remove any notifications for attendees who we are about to insert
									$db->setQuery("DELETE FROM #__jev_notifications WHERE messagetype=$type AND rp_id=$rpid AND sentmessage=0 AND emailaddress = ", $db->Quote($creator->email));
									$db->query();

									// Now REPLACE the new entries
									$db->setQuery("REPLACE INTO #__jev_notifications (rp_id, messagetype, created, emailaddress, m_ev)
																									 VALUES (" . $db->Quote($rpid) . ",$type ," . $db->Quote($created) . " ," . $db->Quote($creator->email) . " , " . $m_ev . ")");
									// Joomfish bug workaround
									$db->_skipSetRefTables = true;
									$db->query();
									$db->_skipSetRefTables = false;
								}
							}
						}
						break;
					case 7:
						// Event notifications requested by users
						$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
						if ($jevparams->get("multicategory", 0))
						{
							$cats = $event->catids();
						}
						else
						{
							$cats = array($event->catid());
						}

						// now remove any notifications for USER we are about to insert
						$db->setQuery("DELETE FROM #__jev_notifications WHERE messagetype=$type AND rp_id=$rpid AND sentmessage=0 AND user_id in(SELECT user_id FROM #__jev_notification_map WHERE cat_id IN (" . implode(",", $cats) . "))");
						$db->query();

						// Now REPLACE the new entries
						// Joomfish bug workaround
						$db->_skipSetRefTables = true;
						foreach ($cats as $catid)
						{
							$db->setQuery("REPLACE INTO #__jev_notifications (rp_id,  messagetype, user_id, created, m_ev)
									SELECT  " . $db->Quote($rpid) . " as rpid,$type as type, user_id, " . $db->Quote($created) . " as created, " . $db->Quote($m_ev) . " as m_ev FROM #__jev_notification_map where cat_id=" . $catid);
							$db->query();
						}
						$db->_skipSetRefTables = false;

						break;
					case 8:
						// Event notifications sent to associated Managed People
						$db->setQuery("DELETE FROM #__jev_notifications WHERE messagetype=$type AND rp_id=$rpid AND sentmessage=0 AND notificationtype=" . $notification);
						$db->query();

						// Find the associated managed people
						/*
						  $db->setQuery("SELECT p.linktouser FROM #__jev_peopleeventsmap as pm
						  LEFT JOIN #__jev_people as p ON p.pers_id=pm.pers_id
						  WHERE p.linktouser<> 0 AND pm.evdet_id=".$event->evdet_id()
						  );
						 */
						// Now REPLACE the new entries
						$db->setQuery("REPLACE INTO #__jev_notifications (rp_id,  messagetype, user_id, created, notificationtype, m_ev)
								SELECT  " . $db->Quote($rpid) . " as rpid,$type as type, p.linktouser, " . $db->Quote($created) . " as created, $notification, " . $db->Quote($m_ev) . " as m_ev  FROM #__jev_peopleeventsmap as pm
							LEFT JOIN #__jev_people as p ON p.pers_id=pm.pers_id
							WHERE p.linktouser<>0 AND pm.evdet_id=" . $event->evdet_id());
						// Joomfish bug workaround
						$db->_skipSetRefTables = true;
						$db->query();
						$db->_skipSetRefTables = false;
						break;
					default:
						break;
				}
			}
		}

	}

	private
			function cleanUnsent($type, $rpid, $manual = false)
	{
		if (!$manual && $this->params->get("changenotificationtype") == 1)
		{
			return;
		}
		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM #__jev_notifications WHERE messagetype=$type AND rp_id=$rpid AND sentmessage=0");
		$db->query();

	}

	function onDisplayCustomFields(&$row)
	{
		// only valid if manual notifications
		if ($this->params->get("changenotificationtype") == 0)
		{
			return;
		}
		if (JEVHelper::canPublishEvent($row))
		{

			// Only do this if there are no queued notifications
			$db = JFactory::getDBO();
			$rpid = $row->rp_id();
			$db->setQuery("SELECT max(created) FROM #__jev_notifications WHERE rp_id=$rpid");
			$created = $db->loadResult();


			if (!isset($row->_modified) || is_null($row->_modified) || $row->_modified == "" || $row->_modified == "0000-00-00 00:00:00")
			{
				return;
			}
			else if (isset($row->_modified))
			{
				// Note that created is a GMT time but $row->_modified is native/jevents config time
				$jevparams = JComponentHelper::getParams("com_jevents");
				$tz = $jevparams->get("icaltimezonelive", "UTC");
				include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");
				$gmtModified = new JevDate($row->_modified, $tz);
				$gmtModified = $gmtModified->toSql(false);

				if ($gmtModified <= $created)
				{
					return;
				}
			}

			if (JRequest::getInt("jevnotify", 0))
			{
				// do it manutally
				if ($row->published())
				{
					$this->cleanUnsent(1, $row->rp_id(), 1);
					$this->newUnsent(1, $row, 1);
				}
				else
				{
					$this->cleanUnsent(2, $row->rp_id(), 1);
					$this->newUnsent(2, $row, 1);
				}
				JFactory::getApplication()->enqueueMessage(JText::_('JEV_NOTIFICATIONS_QUEUED'));
			}
			else
			{
				global $Itemid;
				list($year, $month, $day) = JEVHelper::getYMD();
				$link = $row->viewDetailLink($year, $month, $day, true, $Itemid);
				$html = '
<form action="' . $link . '"  method="post" >
	<input type="hidden" name="jevnotify" value="1"/>
	<input type="submit" name="submit" value="' . JText::_('JEV_NOTIFY_ANY_CHANGES') . '" />
</form>';

				// Add reference in the event
				$row->_jevnotify = $html;
				return $html;
			}
		}

	}

	static
			function fieldNameArray($layout = 'detail')
	{
		// only offer in detail view
		if ($layout != "detail")
			return array();

		$lang = JFactory::getLanguage();
		$lang->load("plg_jevents_jevnotify", JPATH_ADMINISTRATOR);

		$labels = array();
		$labels[] = JText::_("JEV_MANUAL_NOTIFICATION", true);
		$values = array();
		$values[] = "JEVNOTIFY";

		$return = array();
		$return['group'] = JText::_("JEV_CHANGE_NOTIFICATION", true);
		$return['values'] = $values;
		$return['labels'] = $labels;

		return $return;

	}

	static
			function substitutefield($row, $code)
	{
		if ($code == "JEVNOTIFY")
		{
			if (isset($row->_jevnotify))
				return $row->_jevnotify;
		}
		return "";

	}

	private
			function loadEvent($ev_id)
	{
		// can't use DB model since plugins may mess up the search!
		$db = JFactory::getDBO();

		$params = JComponentHelper::getParams("com_jevents");
		$extrajoin = "";
		$extrafields = "";
		if ($params->get("multicategory", 0))
		{
			$extrajoin .= "\n LEFT JOIN  #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			//$extrajoin .= "\n LEFT JOIN   #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmap.catid SEPARATOR ',') as catids";
		}

		// make sure we pick up the event state
		$query = "SELECT ev.*, rpt.*, rr.*, det.* , ev.state as state $extrafields"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. "\n FROM #__jevents_vevent as ev "
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. $extrajoin
				. "\n WHERE ev.ev_id = '$ev_id'"
				. "\n GROUP BY rpt.rp_id"
				. "\n LIMIT 1";

		$db->setQuery($query);
		$row = $db->loadObject();
		if (!$row)
			return false;
		$row = new jIcalEventRepeat($row);
		return $row;

	}

}
