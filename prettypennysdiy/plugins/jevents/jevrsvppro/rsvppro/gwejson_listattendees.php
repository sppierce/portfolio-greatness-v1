f<?php

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

function gwejson_skiptoken() {
    return true;
}

function ProcessJsonRequest(&$requestObject, $returnData) {
    
    $valid_IP_Addresses = array("127.0.0.1", "192.168.0.1", "192.168.1.51");
    // When in test mode you can supply an email address in the URL e.g. 
    // index.php?option=com_jevents&typeaheadtask=gwejson&file=listattendees&path=plugin&folder=jevents&plugin=jevrsvppro/rsvppro&json={"days":400, "email":"test@copyn.plus.com"}
    $testmode = true;
    // debug mode enables more diagnostic information
    $debug = true;

    // you can change the subject of the message and its content using the codes {TITLE} and {STARTDATE}
    $subject = "Export of attendees of {TITLE} at {STARTDATE}";
    $message = "This is and export of the current attendees for the event : {TITLE} occuring at {STARTDATE}";
    
    if (!in_array($_SERVER["REMOTE_ADDR"], $valid_IP_Addresses)){
        PlgSystemGwejson::throwerror("Invalid IP Address");
    }
    
    if (!isset($requestObject->days)) {
        $requestObject->days = 100;
    }
    if (!$testmode){
        $requestObject->email = "";
    }
    if (!isset($requestObject->email)) {
        $requestObject->email = "";
    }
    
    $lparams = JComponentHelper::getParams('com_languages');
    $sitelang = $lparams->get('site', 'en-GB');
    $sitelang = JLanguage::getInstance($sitelang, 0);
    JFactory::$language = $sitelang;

    $lang = JFactory::getLanguage();
    $lang->load("com_rsvppro", JPATH_SITE);
    $lang->load("com_rsvppro", JPATH_ADMINISTRATOR);

    // also load the plugin language file!
    $lang->load( 'plg_jevents_jevrsvppro',JPATH_ADMINISTRATOR );
    
    // User must be able to access all the events we need to
    $params = JComponentHelper::getParams("com_jevents");
    $adminuser = $params->get("jevadmin");

    include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");
    include_once(JPATH_ADMINISTRATOR . "/components/com_rsvppro/rsvppro.defines.php");

    $currentUser = JFactory::getUser();

    // Sets the current user to be the site admin user - but only for this request
    $registry = JRegistry::getInstance("jevents");
    $registry->set("jevents.icaluser", JFactory::getUser($adminuser));

    // ensure "user" can access non-public categories etc.
    $dataModel = new JEventsDataModel();
    $dataModel->accessuser = $adminuser;
    $dataModel->setupComponentCatids();

    if ($params->get("icaltimezonelive", "") != "" && is_callable("date_default_timezone_set")) {
        $timezone = date_default_timezone_get();
        $tz = $params->get("icaltimezonelive", "");
        date_default_timezone_set($tz);
        $registry = JRegistry::getInstance("jevents");
        $registry->set("jevents.timezone", $timezone);
    }

    $rsvpparams = JComponentHelper::getParams("com_rsvppro");

    include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");

    $dataModel = new JEventsDataModel();
    $queryModel = new JEventsDBModel($dataModel);

    $db = JFactory::getDBO();
    jimport("joomla.utilities.date");
    JLoader::register('JEventsVersion', JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/version.php");
    $jevversion = JEventsVersion::getInstance();
    JLoader::register('JevDate', JPATH_SITE . "/components/com_jevents/libraries/jevdate.php");

    $cutoff = new JevDate("+".(86400*intval($requestObject->days))." seconds");
    // use toMySQL to pick up timezone effects
    $cutofftime = $cutoff->toMySQL();

    $now = new JevDate("+0 seconds");
// use toMySQL to pick up timezone effects
    $now = $now->toMySQL();
    $sql = <<<QUERY
SELECT atc.*,atc.id as at_id, rpt.rp_id as repeatid  , rpt.startrepeat, det.summary
FROM #__jevents_vevent as ev
LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id
LEFT JOIN #__jevents_vevdetail as det ON rpt.eventdetail_id = det.evdet_id
LEFT JOIN  #__jev_attendance as atc ON atc.ev_id = ev.ev_id
LEFT JOIN #__jev_attendeecount AS atdc ON atc.id = atdc.at_id
WHERE rpt.startrepeat < '$cutofftime' AND rpt.startrepeat> '$now'
AND atc.allowregistration>0 AND atdc.atdcount > 0 
GROUP BY ev.ev_id
ORDER BY rpt.startrepeat ASC
QUERY;
    $db->setQuery($sql);
    $events = $db->loadObjectList();
    //echo $db->getQuery()."<br/>";
    //echo "found " . count($events) . " rows<br/>";

    $count = 0;
    foreach ($events as $event) {
        if ($debug)
            echo "Processing Event <strong>" . $event->summary . "</strong><br/>";

        $where = array();
        $join = array();

        $where[] = "ev.ev_id IS NOT NULL";
        $where[] = "atdees.rp_id IS NOT NULL";
        $where[] = "atd.id = $event->at_id";

        $sql = "SELECT * FROM #__jev_attendance WHERE id=" . $event->at_id;
        $db->setQuery($sql);
        $rsvpdata = $db->loadObject();
        
        $template = $rsvpdata->template;
        if ($template != "") {
            $xmlfile = JevTemplateHelper::getTemplate($rsvpdata);
        }
        
        //$where[] = "atdees.waiting =" . 0;

        //$where[] = "atdees.confirmed =" . 1;

        if (!$event->allrepeats){
            $where[] = "atdees.rp_id = $event->repeatid";
        }
        $where[] = "atd.allrepeats=".$event->allrepeats."  AND atd.allowregistration>0 ";

        $filter_order = 'atd.id';
        $filter_order_Dir = ' ASC';
        $orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;

        // Get the attendees
        $query = "SELECT det.*, atd.* , atd.id as atd_id, atdc.atdcount, atdees.*,atdees.id as atdee_id,  "
                . " CASE WHEN atdees.user_id=0 THEN atdees.email_address ELSE CONCAT_WS(' - ',ju.username,ju.email) END as attendee, "
                . " CASE WHEN atdees.user_id=0 THEN '' ELSE ju.name END as attendeename, "
                . " CASE WHEN atdees.user_id=0 THEN '' ELSE ju.username END as attendeeusername, "
                . " CASE WHEN atdees.user_id=0 THEN atdees.email_address ELSE ju.email END as attendeemail "
                . "\n FROM #__jevents_vevent as ev "
                . "\n LEFT JOIN #__jevents_vevdetail as det ON ev.detail_id=det.evdet_id"
                . "\n LEFT JOIN #__jev_attendance AS atd ON atd.ev_id = ev.ev_id"
                . "\n LEFT JOIN #__jev_attendeecount AS atdc ON atd.id = atdc.at_id"
                . "\n LEFT JOIN #__jev_attendees AS atdees ON atdees.at_id = atd.id"
                . "\n LEFT JOIN #__users AS ju ON ju.id = atdees.user_id"
                . ( count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '' )
                . ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' )
                . "\n GROUP BY atdees.id $orderby";
        $db->setQuery($query);

        
        //echo $db->getQuery()."<Br/>";
        $rows = $db->loadObjectList();
        echo $db->getErrorMsg();
        //echo "<Br/>".count($rows)."<br/>";
        
        if (!$rows  || count($rows)==0){
            continue;
        }
        
        if (!$event->allrepeats){
            // include unpublished events
            $vevent = $dataModel->queryModel->getEventById($rsvpdata->ev_id, true, "icaldb");
            $repeat = $vevent->getFirstRepeat();
        } else {
            list($year, $month, $day) = JEVHelper::getYMD();
            // include unpublished events
            $repeat = $dataModel->queryModel->listEventsById ($event->repeatid, 1, "icaldb");  
            //$repeatdata = $dataModel->getEventData($event->repeatid, "icaldb", $year, $month, $day);
            //if ($repeatdata && isset($repeatdata["row"]))
             //   $repeat = $repeatdata["row"];
        }
        
        $success = exportAttendees($repeat, $rsvpdata, $rows, $xmlfile, $subject, $message, $debug);
        if ($success) $count ++;
    }

    echo "Sent $count emails ";
    exit();
}

function exportAttendees($repeat, $rsvpdata, $rows , $xmlfile, $subject, $message, $debug = false) {

    static $xmlparams = array();
    ini_set("display_errors", 0);
    set_time_limit(180);
    $filename = "rsvp_export" . JApplicationHelper::stringURLSafe($repeat->title() . " " . $repeat->startDate()) . "_" . $repeat->rp_id() . ".csv";

    $data = array();

    $headerrow = array(JText::_('RSVP_ATTENDEE_NUMBER'), JText::_('RSVP_ATTENDEE'), JText::_('RSVP_ATTENDEE_NAME'), JText::_('RSVP_ATTENDEE_EMAIL'), JText::_('RSVP_ATTENDEE_USERNAME'), JText::_('RSVP_CONFIRMED'), JText::_('JEV_WAITING'), JText::_('RSVP_ATTEND_STATUS'), JText::_('RSVP_ATTENDED'), JText::_('RSVP_ATTENDANCENOTES'));

    if (count($rows) > 0) {

        $attendee = $rows[0];

        // New parameterised fields
        if ($rsvpdata->template != "") {
            if (is_int($xmlfile) || file_exists($xmlfile)) {
                if (isset($xmlparams[$xmlfile])) {
                    $params = clone ($xmlparams[$xmlfile]);
                } else {
                    $params = new JevRsvpParameter("", $xmlfile, $rsvpdata, $attendee);
                    $xmlparams[$xmlfile] = $params;
                }

                //$params = new JevRsvpParameter("",$xmlfile,$rsvpdata, $attendee);
                $params = $params->renderToBasicArray();
                foreach ($params as $param) {
                    if ($param['label'] != "") {
                        $headerrow [] = JText::_($param['label']);
                    }
                }
            }
        }
        $headerrow [] = JText::_('JEV_REGISTRATION_TIME');
        $headerrow [] = JText::_('JEV_MODIFICATION_TIME');

        // Including Transactions
        /*
          $headerrow [] =  JText::_('RSVP_TRANSACTION_NUMBER');
          $headerrow [] =  JText::_('RSVP_TRANSACTION_GATEWAY');
          $headerrow [] =  JText::_('RSVP_TRANSACTION_CURRENCY');
          $headerrow [] =  JText::_('RSVP_TRANSACTION_AMOUNT');
          $headerrow [] =  JText::_('RSVP_TRANSACTION_DATE');
          $headerrow [] =  JText::_('RSVP_TRANSACTION_PAYMENTSTATE');
         */
        $data[] = $headerrow;

        $attendstate = array(JText::_('RSVP_NOT_ATTENDING'), JText::_('RSVP_ATTENDING'), JText::_('RSVP_MAYBE_ATTENDING'), JText::_('RSVP_PENDING_APPROVAL'), JText::_('RSVP_OUTSTANDING_BALANCE'));

        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            $attendee = $rows[$i];

            $guestcount = (isset($attendee->guestcount) && $attendee->guestcount > 1) ? $attendee->guestcount : 1;
            for ($guest = 0; $guest < $guestcount; $guest ++) {
                $datarow = array();
                $datarow[] = $attendee->atdee_id;
                $datarow[] = $attendee->attendee;
                $datarow[] = $attendee->attendeename;
                $datarow[] = $attendee->attendeemail;
                $datarow[] = $attendee->attendeeusername;
                $datarow[] = $attendee->confirmed;
                $datarow[] = $attendee->waiting;
                $datarow[] = $attendstate[$attendee->attendstate];
                $datarow[] = $attendee->didattend;
                $datarow[] = $attendee->notes;

                // New parameterised fields
                if ($rsvpdata->template != "") {

                    if ((is_int($xmlfile) || file_exists($xmlfile)) && ($attendee->lockedtemplate == 0 || $attendee->lockedtemplate == $xmlfile)) {
                        // transfer attendee specific information into the event row
                        $eventrow = clone $repeat;
                        if (isset($xmlparams[$xmlfile])) {
                            $params = clone ($xmlparams[$xmlfile]);
                        } else {
                            $params = new JevRsvpParameter("", $xmlfile, $rsvpdata, $eventrow);
                            $xmlparams[$xmlfile] = $params;
                        }

                        foreach (get_object_vars($attendee) as $key => $val) {
                            $eventrow->$key = $val;
                        }

                        if (isset($attendee->params)) {
                            // building from scratch each time is slow! so use a cloned object!
                            //$params = new JevRsvpParameter($attendee->params, $xmlfile, $rsvpdata, $eventrow);
                            $params->loadData($attendee->params, $rsvpdata, $eventrow);
                            $feesAndBalances = $params->outstandingBalance($attendee);
                        } else {
                            //$params = new JevRsvpParameter("",$xmlfile,$rsvpdata, $eventrow);
                            $params = clone ($xmlparams[$xmlfile]);
                            $params->load($rsvpdata, $eventrow);
                            $feesAndBalances = false;
                        }

                        $params = $params->renderToBasicArray('xmlfile', $attendee);
                        foreach ($params as $param) {
                            if ($param['label'] != "") {
                                if ($param["peruser"] <= 0) {
                                    $datarow[] = $param['value'];
                                } else if ($param["peruser"] == 1) {
                                    if (!is_array($param['value']) && $guest == 0) {
                                        $datarow[] = $param['value'];
                                    } else if (array_key_exists($guest, $param['value'])) {
                                        $datarow[] = $param['value'][$guest];
                                    } else {
                                        $datarow[] = "";
                                    }
                                } else if ($param["peruser"] == 2) {
                                    if (array_key_exists($guest, $param['value'])) {
                                        $datarow[] = $param['value'][$guest];
                                    } else {
                                        $datarow[] = "";
                                    }
                                }
                            }
                        }
                        unset($params);
                        unset($eventrow);
                    } else if ($attendee->lockedtemplate > 0) {
                        $xmlfile = $attendee->lockedtemplate;

                        // transfer attendee specific information into the event row
                        $eventrow = clone $repeat;
                        if (isset($xmlparams[$xmlfile])) {
                            $params = clone ($xmlparams[$xmlfile]);
                        } else {
                            $params = new JevRsvpParameter("", $xmlfile, $rsvpdata, $eventrow);
                            $xmlparams[$xmlfile] = $params;
                        }
                        foreach (get_object_vars($attendee) as $key => $val) {
                            $eventrow->$key = $val;
                        }

                        if (isset($attendee->params)) {
                            // building from scratch each time is slow! so use a cloned object!
                            //$params = new JevRsvpParameter($attendee->params, $xmlfile, $rsvpdata, $eventrow);
                            $params->loadData($attendee->params, $rsvpdata, $eventrow);
                            $feesAndBalances = $params->outstandingBalance($attendee);
                        } else {
                            //$params = new JevRsvpParameter("",$xmlfile,$rsvpdata, $eventrow);
                            $params = clone ($xmlparams[$xmlfile]);
                            $params->load($rsvpdata, $eventrow);
                            $feesAndBalances = false;
                        }

                        $params = $params->renderToBasicArray('xmlfile', $attendee);
                        foreach ($params as $param) {
                            if ($param['label'] != "") {
                                if ($param["peruser"] <= 0) {
                                    $datarow[] = $param['value'];
                                } else if ($param["peruser"] == 1) {
                                    if (!is_array($param['value']) && $guest == 0) {
                                        $datarow[] = $param['value'];
                                    } else if (array_key_exists($guest, $param['value'])) {
                                        $datarow[] = $param['value'][$guest];
                                    } else {
                                        $datarow[] = "";
                                    }
                                } else if ($param["peruser"] == 2) {
                                    if (array_key_exists($guest, $param['value'])) {
                                        $datarow[] = $param['value'][$guest];
                                    } else {
                                        $datarow[] = "";
                                    }
                                }
                            }
                        }
                        unset($params);
                        unset($eventrow);
                    }
                }
                $datarow[] = $attendee->created;
                $datarow[] = $attendee->modified;

                // Transaction Data
                /*
                  if ( $feesAndBalances && isset($feesAndBalances["transactions"]) && count($feesAndBalances["transactions"])>0){
                  $keepdata = $datarow;
                  for ($t=0; $t<count($feesAndBalances["transactions"]); $t++){
                  $datarow = $keepdata;
                  $transaction = $feesAndBalances["transactions"][$t];
                  $datarow[] = $transaction->transaction_id;
                  $datarow[] = $transaction->gateway;
                  $datarow[] = $transaction->currency;
                  $datarow[] = $transaction->amount;
                  $datarow[] = $transaction->transaction_date;
                  $datarow[] = $transaction->paymentstate;
                  // Add the interim values here before going to the last one in the outer look
                  if ($t<count($feesAndBalances["transactions"])-1){
                  $data[] = $datarow;
                  }
                  }
                  }
                  else {
                  for ($ti=0;$ti<6;$ti++) {$datarow[] ="";}
                  }
                 */
                $data[] = $datarow;
            }
            unset($attendee);
            unset($rows[$i]);
//echo memory_get_peak_usage(true);
        }

        $data = exportAsCSV($data);
        
        // force UTF-8 BOM headers in file - see http://stackoverflow.com/questions/5368150/php-header-excel-and-utf-8
        $data = pack('CCC', 0xef, 0xbb, 0xbf) . $data;

        $config =  JFactory::getConfig();
        $path = $config->get('config.tmp_path') ? $config->get('config.tmp_path') : $config->get('tmp_path');
        $tmpFileName = $path. "/attendeedata_".$rsvpdata->ev_id."_".($rsvpdata->allrepeats?0:$repeat->rp_id()).".csv";
        file_put_contents($tmpFileName, $data);
        echo "created temp CSV conversion file in ".$tmpFileName."<br/>";
        
        // Who to send it to
        $templateParams  = RsvpHelper::getTemplateParams($rsvpdata);
        $comparams = JComponentHelper::getParams("com_rsvppro");
        $bcccreator = $comparams->get("bcccreator", "");
        $bcccreator = str_replace(" ", "", $bcccreator);
        if (strpos($bcccreator, ",")>0){
                $bcccreator = explode(",", $bcccreator);
        }
        $creator = JFactory::getUser($repeat->created_by());
        
        // Finally, generate a file
        $size = strlen($data);
                
        $subject = str_replace("{TITLE}", $repeat->title(), $subject );
        $subject = str_replace("{STARTDATE}", $repeat->startDate(), $subject );
        $message = str_replace("{TITLE}", $repeat->title(), $message );
        $message = str_replace("{STARTDATE}", $repeat->startDate(), $message );
        
        if ($debug)
            echo "Sending export file to<strong>" . $creator->email  . "</strong> with subject : $subject<br/>";

        //          sendAttendeesByMail($from,                      $fromname,       $recipient,      $subject, $body,    $mode = 0, $cc = null, $bcc = null, $attachment = null, $replyto = null, $replytoname = null, $debug = false)
        $success  = sendAttendeesByMail($creator->email, $creator->name, $creator->email, $subject, $message, 1,         null,       $bcccreator, $tmpFileName,       null,            null,                $debug);
        unlink($tmpFileName);
        return $success;
        /*
        @ob_end_clean();
        @ini_set("zlib.output_compression", "Off");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: private");
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filename");
        header("Accept-Ranges: bytes");
        header("Content-Length: $size");
        echo $data;
        exit();
         */
    }
}

function sendAttendeesByMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = null, $bcc = null, $attachment = null, $replyto = null, $replytoname = null, $debug = false) {
    $rsvpparams = JComponentHelper::getParams("com_rsvppro");
    $from = $rsvpparams->get("overridesenderemail", $from);
    $fromname = $rsvpparams->get("overridesendername", $fromname);
    $mail = JFactory::getMailer();

    // Set AltBody so we get plain text version in the message too!
    $mail->AltBody = $mail->normalizeBreaks($mail->html2text($body, true));

    if (!$bcc){
        $bcc = null;
    }
    return $mail->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
}

function exportAsCSV($data) {
    ob_start();
    $outstream = fopen("php://output", 'w');

    array_walk($data, '__outputCSV', $outstream);

    fclose($outstream);
    return ob_get_clean();
}

function __outputCSV(&$vals, $key, $filehandler) {
    // TODO THIS IS NOT CORRECT!!
    $temp = array();
    foreach ($vals as $val) {
        if (is_array($val)) {
            $val = implode(", ", $val);
        }
        $temp[] = $val;
    }
    fputcsv($filehandler, $temp, ',', '"');
}

