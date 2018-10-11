<?php

/**
 * @copyright	Copyright (C) 2012 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
 */
ini_set("display_errors", 0);
define("DEBUG", 0);

list($usec, $sec) = explode(" ", microtime());
define('_SC_START', ((float) $usec + (float) $sec));

// Set flag that this is a parent file
define('_JEXEC', 1);
$x = realpath(dirname(__FILE__) . "/../../");
if (!file_exists($x .  "/plugins") && isset($_SERVER["SCRIPT_FILENAME"]))
{
	$x = dirname(dirname(dirname($_SERVER["SCRIPT_FILENAME"])));
}

define('JPATH_BASE', $x);

// create the mainframe object
$_REQUEST['tmpl'] = 'component';

// Create JSON data structure
$data = new stdClass();
$data->error = 0;
$data->result = "ERROR";

// Enforce referrer
if (!DEBUG && !array_key_exists("HTTP_REFERER", $_SERVER))
{
	throwerror("There was an error");
}

$live_site = $_SERVER['HTTP_HOST'];
$ref_parts = parse_url($_SERVER["HTTP_REFERER"]);

if (!DEBUG && (!isset($ref_parts["host"]) || $ref_parts["host"] != $live_site ))
{
	throwerror("There was an error - missing host in referrer");
}

// Get JSON data
if (!DEBUG && !array_key_exists("json", $_REQUEST))
{
	throwerror("There was an error - no request data");
}
else
{
	$requestData = $_REQUEST["json"];

	if (DEBUG && !isset($requestData))
	{
		$requestData = new stdClass();
		$requestData->modid = 77;
		$requestData = json_encode($requestData);
	}
	if (isset($requestData))
	{
		try {
			if (ini_get("magic_quotes_gpc"))
			{
				$requestData = stripslashes($requestData);
			}

			$requestObject = json_decode($requestData, 0);
			if (!$requestObject)
			{
				$requestObject = json_decode(utf8_encode($requestData), 0);
			}
			// From jQuery no need to decode
			if (!$requestObject && is_array($requestData))
			{
				$requestObject = (object)$requestData;
				$requestObject->error = false;
			}
		}
		catch (Exception $e) {
			throwerror("There was an exception");
		}

		if (!$requestObject)
		{
			file_put_contents(dirname(__FILE__) . "/cache/error.txt", var_export($requestData, true));
			throwerror("There was an error - no request object ");
		}
		else if ($requestObject->error)
		{
			throwerror("There was an error - Request object error " . $requestObject->error);
		}
		else
		{

			try {
				$data = ProcessRequest($requestObject, $data);
			}
			catch (Exception $e) {
				throwerror("There was an exception " . $e->getMessage());
			}
		}
	}
	else
	{
		throwerror("Invalid Input");
	}
}

header("Content-Type: application/x-javascript; charset=utf-8");

list ($usec, $sec) = explode(" ", microtime());
$time_end = (float) $usec + (float) $sec;
$data->timing = round($time_end - _SC_START, 4);

// Must suppress any error messages
@ob_end_clean();
echo json_encode($data);

function ProcessRequest(&$requestObject, $returnData)
{

	define("REQUESTOBJECT", serialize($requestObject));
	define("RETURNDATA", serialize($returnData));

	require_once JPATH_BASE . '/includes/defines.php';
	require_once JPATH_BASE .'/includes/framework.php';

	$requestObject = unserialize(REQUESTOBJECT);
	$returnData = unserialize(RETURNDATA);

	ini_set("display_errors", 0);

	global $option, $mainframe;
	$option = "com_jevents";
	$client = "site";
	if (isset($requestObject->client) && in_array($requestObject->client, array("site", "administrator")))
	{
		$client = $requestObject->client;
	}
	$mainframe = JFactory::getApplication($client);
	$mainframe->initialise();
	
	if (!isset($requestObject->modid) || $requestObject->modid == 0)
	{
		throwerror("There was an error");
	}

	$user =  JFactory::getUser();
	$db = JFactory::getDBO();
	JLoader::register('JevJoomlaVersion',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/version.php");
	
	if (version_compare(JVERSION, "1.6.0", 'ge')){
		$access = ' AND m.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')';
	}
	else {
		$aid = $user->get('aid', 0);
		$access=  ' AND m.access <= ' . (int) $aid;
	}

	$query = 'SELECT id, title, module, position, content, showtitle,  params'
			. ' FROM #__modules AS m'
			//. ' LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id'
			. ' WHERE m.published = 1'
			.$access
			. ' AND m.client_id = ' . (int) $mainframe->getClientId()
			. ' AND m.id = ' . (int) $requestObject->modid
			. ' ORDER BY position, ordering';

	$db->setQuery($query);
	$module = $db->loadObject();
	if (!$module)
	{
		throwerror("There was an error - no module ".$db->getQuery()."\n".addslashes($db->getErrorMsg()));
	}

	JFactory::getLanguage()->load('mod_tabbedmodules'); 

	include_once (JPATH_SITE."/modules/mod_tabbedmodules/helper.php");
	// Latest Events Title fix and pick up relevant parameters tweaks
	if ($module->module == "mod_jevents_latest")
	{
		modTabbedmodulesHelper::fixLatestEventsModule($module);
	}
	
	// commonly undeclared paths!
	jimport("joomla.filesystem.file");
	jimport("joomla.filesystem.folder");

	$document = JFactory::getDocument();
	$renderer = $document->loadRenderer('module');
	$tempparams = array('style' => -2);
	$module->tabcontent = $renderer->render($module, $tempparams);
	$module->tabcontent = str_replace("/modules/mod_tabbedmodules","", $module->tabcontent );

	// wrap latest events module
	modTabbedmodulesHelper::wrapLatestEventsModule($module);
	
	$returnData->result = 1;
	$returnData->modvalue = $module->tabcontent ;

	$returnData->modvalue = str_replace(array("?tmpl=component", "&tmpl=component"), "", $returnData->modvalue);
	
	return $returnData;

}

function throwerror($msg)
{
	$data = new stdClass();
	//"document.getElementById('products').innerHTML='There was an error - no valid argument'");
	$data->error = "alert('" . $msg . "')";
	$data->result = "ERROR";
	$data->user = "";

	header("Content-Type: application/x-javascript");
	// Must suppress any error messages
	@ob_end_clean();
	echo json_encode($data);
	exit();

}


