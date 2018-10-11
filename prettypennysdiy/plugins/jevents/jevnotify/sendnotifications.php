<?php

if (isset($_SERVER["REQUEST_URI"])) {
	$x = str_replace("plugins/jevents/jevnotify/sendnotifications.php", "", $_SERVER["REQUEST_URI"]);
	echo header("Location:".$x."index.php?option=com_jevents&typeaheadtask=gwejson&file=sendnotifications&path=plugin&folder=jevents&plugin=jevnotify&json={}");
	//include ("../../../index.php");
}
exit();

/**
 * @copyright	Copyright (C) 2009-2015 GWE Systems Ltd. All rights reserved.
 */
//ini_set("display_errors",1);
// Set flag that this is a parent file
define('_JEXEC', 1);
$x = realpath(dirname(__FILE__) . "/../../");
if (!file_exists($x . "/plugins"))
{
	$x = realpath(dirname(__FILE__) . "/../../../");
}
if (!file_exists($x . '/' . "plugins") && isset($_SERVER["SCRIPT_FILENAME"]))
{
	$x = dirname(dirname(dirname(dirname($_SERVER["SCRIPT_FILENAME"]))));
}
define('JPATH_BASE', $x);

// create the mainframe object
$_REQUEST['tmpl'] = 'component';

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

JLoader::register('JevJoomlaVersion', JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/version.php");
JLoader::register('JEVNotifyHelper', JPATH_SITE . "/plugins/jevents/jevnotify/helper.php");

// User must be able to access all the events we need to
jimport('joomla.log.log');
$db = JFactory::getDbo();
$query = $db->getQuery(true);

$query->select('folder AS type, element AS name, params')
		->from('#__extensions')
		->where('enabled >= 1')
		->where('type =' . $db->Quote('plugin'))
		->where("folder ='jevents'")
		->where("element ='jevnotify'")
		->where('state >= 0')
		->order('ordering');

$plugin = $db->setQuery($query)->loadObject();
if (!$plugin)
{
	die("no plugin");
}

jimport("joomla.html.parameter");
$pparams = new JRegistry($plugin->params);

// are there ip restrictions
$iplist = $pparams->get("iplist", "");
if ($iplist != "")
{
	$iplist = explode(',', $iplist);
	if (!in_array($_SERVER['REMOTE_ADDR'], $iplist))
	{
		die("Invalid IP address");
	}
}

$shortlifetime = false;

// User must be able to access all the events we need to
// force lifetime for session to be very short as a precaution - this means that this session CANNOT continue
///shortlifetime validation
if ($pparams->get("shortlifetime", 1))
{
	$shortlifetime = JFactory::getConfig()->get("lifetime");
	JFactory::getConfig()->set("lifetime", 0.0001);
}
jimport('joomla.event.dispatcher');
jimport('joomla.environment.response');
jimport('joomla.log.log');
$session = JFactory::getSession();

// remove the cookie
if (isset($_COOKIE[session_name()]))
{
	$config = JFactory::getConfig();
	$cookie_domain = $config->get('cookie_domain', '');
	$cookie_path = $config->get('cookie_path', '/');
	// already expired
	setcookie(session_name(), '', time() - 42000, $cookie_path, $cookie_domain);
}
if ($shortlifetime)
{
	JFactory::getConfig()->set("lifetime", $shortlifetime);
}

global $mainframe;
$mainframe = JFactory::getApplication('site');
$mainframe->initialise();

// Detect and set default language - needed in Joomla 3.4+
try {
	$lparams = JComponentHelper::getParams('com_languages');
	$sitelang = $lparams->get('site', 'en-GB');
	$sitelang = JLanguage::getInstance($sitelang, 0);
	JFactory::$language =  $sitelang;
}
catch (Exception $e){
}

// User must be able to access all the events we need to
$jparams = JComponentHelper::getParams("com_jevents");
$adminuser = $jparams->get("jevadmin");

$resetsession = false;
$user = JFactory::getUser();
if ($user->id == 0)
{
	// delete the session from the database straight away so that it can't be resused at all!
	// BELTS AND BRACES
	$db = JFactory::getDbo();
	$sessid = $session->getId();
	$db->setQuery(
			'DELETE  FROM `#__session`' .
			' WHERE `session_id` = ' . $db->quote($sessid));
	$exists = $db->query();

	// DANGEROUS - it logs in this user but we set the session time to be short in the up top!
	$adminuser = JUser::getInstance($adminuser);
	// put spoof data into the user profile so that its meaningless
	//$adminuser->name = "silly";
	$adminuser->username = "silly";
	//$adminuser->email = "silly@silly.com";
	//$adminuser->id  = 1;
	$adminuser->password = 'something secret';
	$adminuser->password_clear = '';
	$session->set('user', $adminuser);
	$user = JFactory::getUser();
	$resetsession = true;
}


if ($jparams->get("icaltimezonelive", "") != "" && is_callable("date_default_timezone_set"))
{
	$timezone = date_default_timezone_get();
	$tz = $jparams->get("icaltimezonelive", "");
	date_default_timezone_set($tz);
	$registry = JRegistry::getInstance("jevents");
	$registry->set("jevents.timezone", $timezone);
}

$plugin = JPluginHelper::getPlugin("jevents", "jevnotify");
if (!$plugin)
{
	echo "No Plugin";
	return;
}

jimport("joomla.html.parameter");

include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");

// Select the next 20 emails to send
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
$lag = intval($pparams->get("lag", 0));
$lagtime = new $dateClass("-$lag seconds");
$lagtime = $lagtime->toSql();
$sendname = $pparams->get("sendname", "");
$sendemail = $pparams->get("sendaddress", "");

$limit = intval($pparams->get("batchsize", 10));
$db->setQuery("Select n.*, u.name, u.email , u.username from #__jev_notifications as n LEFT JOIN #__users as u on u.id = n.user_id where n.sentmessage=0 and created<" . $db->Quote($lagtime) . " order by n.created limit $limit");

$notifications = $db->loadObjectList();

//Do a total count so we know how many are left.
$db->setQuery("Select n.*, u.name, u.email , u.username from #__jev_notifications as n LEFT JOIN #__users as u on u.id = n.user_id where n.sentmessage=0 and created<" . $db->Quote($lagtime) . " order by n.created");
$total = count($db->loadObjectList());

if (count($notifications) == 0)
	die("No matching notifcations to process");

$datamodel = new JEventsDataModel();

// user fetching the data must be able to see the event
//$dataModel->aid = JEVHelper::getAid($adminuser);
//$dataModel->accessuser = $adminuser->get('id');

// prepare the messages
$sent = 0;
foreach ($notifications as $notification)
{

	if ($notification->message == '')
	{
		ob_start();
		$a_gen = 0;

		list ($y, $m, $d) = JEVHelper::getYMD();

		// just incase we don't have jevents plugins registered yet
		JPluginHelper::importPlugin("jevents");
		$event = $datamodel->getEventData($notification->rp_id, 'icaldb', $y, $m, $d);

		if (is_null($event))
		{
			// use the local version
			$event = JEVNotifyHelper::getEventData($notification->rp_id, 'icaldb', $y, $m, $d, $datamodel);
			if (is_null($event))
			{
				// this event no longer appears to exist to remove the notifications for it
				echo "Deleted notifications for missing event" . $notification->rp_id . "<br/>";
				$db->setQuery("DELETE FROM  #__jev_notifications WHERE rp_id=" . $notification->rp_id);
				$db->query();
				continue;
			}
		}

		$event = $event['row'];

		ob_clean();

		// private events and anon creators need this plugin to be called
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onDisplayCustomFields', array(&$event));

		if (isset($event->_privateevent) && intval($event->_privateevent)>0){
			// this event is not public so remove the notifications for it
			echo "Deleted notifications for private event" . $notification->rp_id . "<br/>";
			$db->setQuery("DELETE FROM  #__jev_notifications WHERE rp_id=" . $notification->rp_id);
			$db->query();
			continue;
		}

		$creator = JEVHelper::getUser($event->created_by());
		if ($event->created_by() == 0)
		{
			$creator = new stdClass();
			if (isset($event->authorname) && isset($event->authoremail))
			{
				$creator->email = $event->authoremail;
				$creator->name = $event->authorname;
			}
		}
		if (isset($sendemail) && $sendemail != "")
		{
			$creator->email = $sendemail;
			if (isset($sendname) && $sendname != "")
			{
				$creator->name = $sendname;
			}
		}
		if ($notification->messagetype == 0)
		{
			$subject = JEVNotifyHelper::parseMessage($pparams->get("newsubject"), $event, $notification, $creator);
			$message = JEVNotifyHelper::parseMessage($pparams->get("newmessage"), $event, $notification, $creator);
		}
		else if ($notification->messagetype == 2)
		{
			$subject = JEVNotifyHelper::parseMessage($pparams->get("deletesubject"), $event, $notification, $creator);
			$message = JEVNotifyHelper::parseMessage($pparams->get("deletemessage"), $event, $notification, $creator);
		}
		else
		{
			$subject = JEVNotifyHelper::parseMessage($pparams->get("changesubject"), $event, $notification, $creator);
			$message = JEVNotifyHelper::parseMessage($pparams->get("changemessage"), $event, $notification, $creator);
		}
	}
	else
	{

		$subject = $notification->subject;
		$message = $notification->message;
		$creator = new stdClass();
		$creator->email = $notification->creatoremail;
		$creator->name = $notification->creatorname;
		$a_gen = 1;
	}

	$email = $notification->email ? $notification->email : $notification->email_address;
	$name = $notification->name ? $notification->name : $notification->email_address;

    // Ok we need to check if the user is enabled/active
    $query = $db->getQuery(true);

    $query
        ->select($db->quoteName(array('block', 'activation')))
        ->from($db->quoteName('#__users'))
        ->where($db->quoteName('id'). ' = ' . $db->quote($notification->user_id));
    $db->setQuery($query);
    $results = $db->loadAssoc();

    //Set it up this way so block trumps activation.
    if ($results['block'] == 1 || $results['activation'] != "") {
        $user_status = 0;
    } else {
        $user_status = 1;
    }

	if ($name == "" || $user_status == 0)
	{
		echo "Could not send message to user ", $notification->user_id . " this user no longer exists<br/>";
		if ($notification->user_id > 0)
		{
			// this user no longer appears to exist to remove the notifications for it
			echo "Deleted notifications for missing user " . $notification->user_id . "<br/>";
			//var_dump($notification);
			$db->setQuery("DELETE FROM  #__jev_notifications WHERE user_id=" . $notification->user_id);
			$db->query();
		}


        if ($user_status = 0) {
            // this user no longer appears to be enabled. Remove notifications
            echo "Deleted notifications for user " . $notification->user_id . " as they have been blocked or not activated <br/>";
            //var_dump($notification);
            $db->setQuery("DELETE FROM  #__jev_notifications WHERE user_id=" . $notification->user_id);
            $db->query();
        }

        // We get a headers warning, so lets just run the session set and destroy.
        if ($resetsession && version_compare(JVERSION, "1.6.0", 'ge'))
        {
            // reset the session user
            $session->set('user', 0);
            // delete the session  BELTS AND BRACES
            $session->destroy();
        }

		continue;
	}

	if (isset($creator->name) && isset($creator->email))
	{
		$rparams = JComponentHelper::getParams("com_rsvppro");
                
		if ($rparams->get("invite_icals", 0) == 1 && $pparams->get("send_as_ical", 0) == 1)
		{
            //ICS Method Handle it here rather than in JEVHelper
			if ($notification->messagetype == 2)
			{
				$ics_method = "CANCEL";
			}
			elseif ($notification->messagetype != 0)
			{
				$ics_method = "REQUEST";
			}

			if ($a_gen == 1)
			{
				
			}
			else
			{
                $n_extras['m_ev'] = $notification->m_ev;
				$message = JEVHelper::iCalMailGenerator($event, $n_extras, $ics_method);
			}

			$mail = JFactory::getMailer();
			$mail->isHTML(false);
			$mail->Encoding = "7bit";
			$mail->ContentType = "text/calendar;method=REQUEST";
			$mode = 0;
			$cc = null;
			$bcc = null;
			$attachment = null;
			$replyto = null;
			$replytoname = null;
			$message_cleaned = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $message);
			//$from, $fromname, $recipient, $subject, $body, 0, $cc, $bcc, $attachment, $replyto, $replytoname
			$success = $mail->sendMail($creator->email, $creator->name, $email, $subject, $message_cleaned, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
		}
		else
		{

			$success = JFactory::getMailer()->sendMail($creator->email, $creator->name, $email, $subject, $message, 1);
		}
	}
	else
	{
		// simulate sending
		echo "Do not have creator name and email address for event id " . $notification->id . " with rp_id = " . $notification->rp_id . " - message NOT sent to  " . $notification->name . " - " . $notification->email . "<br/>";
		$success = true;
	}

	global $mainframe;
	if ($success === true)
	{
		echo JText::_('JEV_NOTIFICATIONS') . $name . "<br/>";

		$sql = "UPDATE #__jev_notifications set sentmessage=1 , whensent='" . $now . "' WHERE id=" . $notification->id;
		$db->setQuery($sql);
		$db->query();

		$sent++;
	}
	else
	{
		echo  JText::_('JEV_NOTIFICATIONS_FAILED_TO_SEND') . $name . "<br/>";
		//echo "creator email = ".$creator->email."<br/>";
		//echo "creator name = ".$creator->name."<br/>";
		//echo "recipient email = ".$email."<br/>";
		//echo "subject = ".$subject."<br/>";
		//echo "message = ".$message."<br/>";
	}
}
echo JText::_('JEV_NOTIFICATIONS_SENT_NOTIFICATIONS') . $sent . JText::_('JEV_NOTIFICATIONS');
echo JText::_('JEV_NOTICIATIONS_TOTAL_1') . $total . JText::_('JEV_NOTICIATIONS_TOTAL_2');

if ($resetsession )
{
	// reset the session user
	$session->set('user', 0);
	// delete the session  BELTS AND BRACES
	$session->destroy();
}
