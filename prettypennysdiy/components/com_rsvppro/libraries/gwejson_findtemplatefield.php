<?php
/**
* @copyright	Copyright (C) 2008-2015 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/
defined('JPATH_PLATFORM') or die;

function ProcessJsonRequest(&$requestObject, $returnData){

	if (!isset($requestObject->typeahead)){
		return array();
	}

	$user = JFactory::getUser();
	if ($user->id==0){
		throwerror("There was an error");
	}
        
        $lang = JFactory::getLanguage();
	$lang->load("com_rsvppro", JPATH_ADMINISTRATOR);
        $lang->load("com_rsvppro", JPATH_SITE);
	$lang->load("com_jevents", JPATH_ADMINISTRATOR);
        $lang->load("com_jevents", JPATH_SITE);

	$returnData->titles	= array();
	$returnData->exactmatch=false;

	ini_set("display_errors",0);

        include_once(JPATH_SITE."/components/com_rsvppro/models/jevtemplates.php");
        $model = JModelLegacy::getInstance("JevtemplatesModelJevtemplates");
        $colonPos = strpos($requestObject->typeahead, "#");
        if ($colonPos>0 && strlen($requestObject->typeahead)>=$colonPos+1){
            list($template, $field) = explode("#", $requestObject->typeahead,2);
        }
        else {
            $template = $requestObject->typeahead;
            $field = false;
        }
       
        $model->typeahead = $template;
        $items =  $model->getItems();

        if ($colonPos>0 && isset($items[0])){
            $templateid = $items[0]->id;
            /*
            include_once(JPATH_SITE."/components/com_rsvppro/models/jevfields.php");
            $model = JModelLegacy::getInstance("JevfieldsModelJevfields");
            $model->setState("template", $templateid);
            $items =  $model->getItems();
             */

            $items = array();
            include_once(JPATH_ADMINISTRATOR."/components/com_rsvppro/models/template.php");
            include_once(JPATH_ADMINISTRATOR."/components/com_rsvppro/tables/template.php");
            $datamodel = JModelLegacy::getInstance("TemplatesModelTemplate");
            $datamodel->setId($templateid);
            
            // model function needs to think we are editing the template
            JRequest::setVar("option","com_rsvppro");
            JRequest::setVar('task', 'templates.edit');
            
            $templateItem = $datamodel->getData();
            // Get field data from the model                   
            $params	= $datamodel->getParams($templateItem);
            $templateparams = $templateItem->params;
            if (is_string($templateparams) && strlen(trim($templateparams))>2){
                    $templateparams = new JRegistry($templateparams);
            }
            else if (is_string($templateparams) && trim($templateparams)==""){
                    $templateparams = JComponentHelper::getParams(RSVP_COM_COMPONENT);
            }
                        
            foreach ($templateItem->fields as $field)
            {
                    $fieldhtml = $field->html;
                    ob_start();
                    ?>
                    <div class='rsvpfield' id='field<?php echo $field->field_id; ?>'>
                            <span class="sortable-handler" style="cursor: move;float:left;margin:10px 10px 0px 0px;">
                                    <i class="icon-menu"></i>
                            </span>
                            <input id="deleteFieldButtonfield<?php echo $field->field_id; ?>" type="button" value="<?php echo JText::_("RSVP_DELETE_FIELD"); ?>" class="deleteFieldButton"/>
                            <input id="closeFieldButtonfield<?php echo $field->field_id; ?>" type="button" value="<?php echo JText::_("RSVP_CLOSE_FIELD"); ?>" class="closeFieldButton"/>
                            <?php
                            echo $fieldhtml;
                            ?>
                    </div>
                    <?php
                    $field->html = ob_get_clean();
                    $field->title = $template. "#".$field->name;
                    $field->id = $field->field_id;
                    $items[] = $field;
            }            
        }

	return $items;

}

