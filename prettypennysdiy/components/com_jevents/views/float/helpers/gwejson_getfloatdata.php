<?php

/**
 * @copyright	Copyright (C) 2015-2016 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
 */
function ProcessJsonRequest(&$requestObject, $returnData)
{
	JLoader::register('JevIsotope', JPATH_LIBRARIES . "/jevents/jevisotope/jevisotope.php");

	ini_set("display_errors", 0);
	$returnData->eventdata = false;

	$lang = JFactory::getLanguage();
	$lang->load("com_jevents", JPATH_SITE);
	$lang->load("com_jevents", JPATH_ADMINISTRATOR);

	include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");
	
	// TODO need to take account of Menu item constraints, logged in status etc. and filters !!!

	$user = JFactory::getUser();
	if (!JEVHelper::isEventCreator())
	{
		//	PlgSystemGwejson::throwerror(JText::_sprintf('JEW_GWE_JSON_NOT_CREATOR'));
	}

	if (!isset($requestObject->task) || !isset($requestObject->params)  || !isset($requestObject->Itemid))
	{
		PlgSystemGwejson::throwerror(JText::_sprintf('JEV_GWE_JSON_INVALID_TASK'));
	}

	// Set the Itemid for the request
	JFactory::getApplication()->input->set("Itemid", intval( $requestObject->Itemid));

	$menuparams = JFactory::getApplication()->getMenu()->getParams(intval($requestObject->Itemid));

	if ($requestObject->filters){
		foreach ($requestObject->filters as $key => $val){
			JFactory::getApplication()->input->set($key, $val);
		}
	}
	// force menu parameters to be active
	$component =  JComponentHelper::getComponent("com_jevents");
	// Override the global params if menu params have these values
	foreach($menuparams->getIterator() as $key => $val) {
		$component->params->set($key,$val);
	}
	$params = JComponentHelper::getParams("com_jevents");

	$float_columns = $params->get('float_columns', 4);

	$iso_params = array('microdata' => 1);

	$db = JFactory::getDBO();
	$dataModel = new JEventsDataModel();
	$dataModel->setupComponentCatids();

	// Ok lets go get this data!
	if (is_callable(array($dataModel, "get" . $requestObject->task)))
	{
		$method = "get" . $requestObject->task;
		$returnData->ev_pdata = "";

		// TODO should really have try/catch around this!
		$returnData->ev_data = call_user_func_array(array($dataModel, $method), $requestObject->params);

                // hard coded params so not ideal
                $limitstart = $requestObject->params[3];
                if ($returnData->ev_data["total"] <= $limitstart){
                    $returnData->ev_data['rows'] = array();
                }
		$num_events = count($returnData->ev_data['rows']);
		if ($num_events >= 1)
		{
			// Load JEvents Config Items
			$cfg = JComponentHelper::getParams('com_jevents');

			// Create a new query object.
			$query = $db->getQuery(true);

			//Block to load:
			$blocktl = "icalevent.list_block" . $cfg->get('float_style', 1);
			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName(array('title', 'name', 'value', 'state')));
			$query->from($db->quoteName('#__jev_defaults'));
			$query->where($db->quoteName('name') . ' = '. $db->quote($blocktl) . ' AND ' . $db->quoteName('state') . ' = 1');

			$db->setQuery($query);

			$results = $db->loadObject();

			if($results->value > '') {
				$item_bodycore = $results->value;
			} else {
				$item_bodycore = JFile::read(JEV_PATH . "/views/float/defaults/icalevent.list_block" . $cfg->get('float_style', 1) . ".html");
			}

			include_once(JEV_VIEWS."/float/abstract/abstract.php");
			include_once(JEV_VIEWS."/float/range/view.html.php");

			$view = new FloatViewRange();
                        if (JFactory::getApplication()->input->get("option")!=="com_jevents"){
                            JFactory::getApplication()->input->set("option","com_jevents");
                        }

			for ($r = 0; $r < $num_events; $r++)
			{
				$row = $returnData->ev_data['rows'][$r];
				$item_body = '<div class="jeviso_item w' . $cfg->get('float_columns', 4) . ' ' . $row->catname() . ' style' . $cfg->get('float_style', 1) . '">';
				$item_body .= $item_bodycore;
				$item_body .= '</div>';
				$returnData->ev_pdata .= JevIsotope::itembody($row, $iso_params, "", $item_body, $view);
			}
		}
		else
		{
			$returnData->no_more = 1;
		}
		$returnData->returned_count = $r;
		// Day Data fetching request
		if (isset($requestObject->task) == "DayData")
		{
			$returnData->test = 'DayData';
		}

	}

//	JRequest::setVar("tmpl","component");
	
	return $returnData;

}
