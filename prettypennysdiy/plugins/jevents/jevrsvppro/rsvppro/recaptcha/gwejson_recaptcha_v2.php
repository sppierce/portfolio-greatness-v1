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

/*
 * function gwejson_skiptoken()

{
	// This file checks the token
	return false;

}
*/

function ProcessJsonRequest(&$requestObject, $returnData)
{
	$params = JComponentHelper::getParams("com_rsvppro");

	$lang 		= JFactory::getLanguage();
	$lang->load("plg_jevents_jevrsvppro", JPATH_ADMINISTRATOR);
	$lang->load("com_rsvppro", JPATH_ADMINISTRATOR);

	if ($params->get("recaptchaprivate",false)){
		if (isset($requestObject->gresponseField)  && trim($requestObject->gresponseField)!=="") {
			require_once('autoload.php');
			$recaptcha = new \ReCaptcha\ReCaptcha($params->get("recaptchaprivate",false));
			$resp = $recaptcha->verify( $requestObject->gresponseField,JRequest::getString("REMOTE_ADDR","","server"));
			//$resp = $recaptcha->verify($requestObject->gresponseField,$_SERVER['REMOTE_ADDR']);//"127.0.0.1");// "84.82.83.241");//$_SERVER['REMOTE_ADDR']);

			//$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);
			if ($resp->isSuccess()) {
				// verified!
			} else {
				$recaptcha = new \ReCaptcha\ReCaptcha($params->get("recaptchaprivate",false), new \ReCaptcha\RequestMethod\Curl());
				$resp = $recaptcha->verify( $requestObject->gresponseField,JRequest::getString("REMOTE_ADDR","","server"));
				if ($resp->isSuccess()) {
					// verified!
				} else {
					$errors = $resp->getErrorCodes();
					PlgSystemGwejson::throwerror ( JText::_("JEV_RECAPTCHA_ERROR",true));
				}
			}
		}
		else if (isset($requestObject->responseField) && trim($requestObject->responseField)!==""){
			require_once('recaptcha.php');
			$response = recaptcha_check_answer($params->get("recaptchaprivate",false),JRequest::getString("REMOTE_ADDR","","server"), $requestObject->challengeField,$requestObject->responseField);
			if (!$response->is_valid){
				PlgSystemGwejson::throwerror ( JText::_("JEV_RECAPTCHA_ERROR",true));
			}
		}
		else {
			 PlgSystemGwejson::throwerror ( JText::_("JEV_RECAPTCHA_ERROR",true));
			//PlgSystemGwejson::throwerror ( "There was an error - no valid argument");
		}

		$mainframe= JFactory::getApplication('site');
		$mainframe->setUserState("jevrecaptcha","ok");

		// we can't call the recaptcha twice so this is to pass on confirmation that the answer was correct
		if (isset($requestObject->gresponseField)  && trim($requestObject->gresponseField)!=="") {
			$returnData->secretcaptcha = md5($requestObject->gresponseField .  $params->get("recaptchaprivate",false) );
		}
		else {
			$returnData->secretcaptcha = md5($requestObject->responseField .  $params->get("recaptchaprivate",false) );
		}
		$returnData->success = 1;
	}

	return $returnData;
}
