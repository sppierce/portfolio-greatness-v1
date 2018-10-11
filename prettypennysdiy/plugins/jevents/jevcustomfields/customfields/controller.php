<?php

/**
 * Custom Fields for JEvents
 *
 * @package     JEvents
 * @copyright   Copyright (C) 2015-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.controller');

class JevCustomFieldsController extends JControllerLegacy {

    function __construct($config = array()) {
        JLoader::register('CustomFieldsHelper', JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/customfieldshelper.php");

        $config["base_path"] = JPATH_SITE . "/plugins/jevents/jevcustomfields";
        $config["view_path"] = JPATH_SITE . "/plugins/jevents/jevcustomfields/views";

        parent::__construct($config);
        // TODO get this from config
        $this->registerDefaultTask('overview');
        $this->registerTask('apply', 'save');

        $lang = JFactory::getLanguage();
        $lang->load("plg_jevents_jevcustomfields", JPATH_ADMINISTRATOR);

        $this->view = $this->getView("jevcustomfields", "", "", array("template_path" => JPATH_SITE . "/plugins/jevents/jevcustomfields/views/jevcustomfields/tmpl"));

        $this->input = JFactory::getApplication()->input;
    }

    function overview() {

        // Set the layout
        $this->view->setLayout("overview");
        $this->view->assign('title', JText::_('JEV_CUSTOM_FIELD_OVERVIEW'));

        $this->view->overview();

        $this->view->display();
    }

    function delete() {
        $cfname = $this->input->get("cfname", array(), "array");
        if (count($cfname) == 0) {
            return "";
        }
        jimport("joomla.filesystem.file");
        $filter = JFilterInput::getInstance();
        for ($i = 0; $i < count($cfname); $i++) {
            $filter->clean($cfname[$i], "path");
            if (strpos($cfname[$i], "..")) {
                continue;
            }
            if (strpos($cfname[$i], "/")) {
                continue;
            }
            $file = JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/templates/" . $cfname[$i];
            if (JFile::exists($file)) {
                JFile::delete($file);
            }
        }
        JFactory::getApplication()->redirect("index.php?option=com_jevents&task=plugin.jev_customfields.overview", JText::_("JEV_CUSTOM_FIELD_FILES_DELETED"));
    }

    function editcopy() {
        $cfname = $this->input->get("cfname", array(), "array");
        if (count($cfname) > 1) {
            JFactory::getApplication()->redirect("index.php?option=com_jevents&task=plugin.jev_customfields.overview", JText::_("JEV_CUSTOM_FIELD_ONLY_EDIT_ONE"));
        }
        jimport("joomla.filesystem.file");
        $filter = JFilterInput::getInstance();
        for ($i = 0; $i < count($cfname); $i++) {
            $filter->clean($cfname[$i], "path");
            if (strpos($cfname[$i], "..")) {
                continue;
            }
            if (strpos($cfname[$i], "/")) {
                continue;
            }
            $file = JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/templates/" . $cfname[$i];
            if (!JFile::exists($file)) {
                JFactory::getApplication()->redirect("index.php?option=com_jevents&task=plugin.jev_customfields.overview", JText::_("JEV_CUSTOM_FIELD_FILE_DOES_NOT_EXIST"));
            }
            JFile::copy($file, str_replace(".xml", "_copy.xml", $file));
            $file = str_replace(".xml", "_copy.xml", $cfname[$i]);
        }

        $this->input->set("cfname", $file);
        return $this->edit();
    }

    function edit() {
        $cfname = $this->input->get("cfname", array(), "array");
        if (count($cfname) > 1) {
            JFactory::getApplication()->redirect("index.php?option=com_jevents&task=plugin.jev_customfields.overview", JText::_("JEV_CUSTOM_FIELD_ONLY_EDIT_ONE"));
        }
        jimport("joomla.filesystem.file");
        $filter = JFilterInput::getInstance();

        $filename = "";
        $file = "";
        if (count($cfname)) {
            $filter->clean($cfname[0], "path");
            if (strpos($cfname[0], "..")) {
                return "";
            }
            if (strpos($cfname[0], "/")) {
                return "";
            }
            $file = JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/templates/" . $cfname[0];
            if (!JFile::exists($file)) {
                JFactory::getApplication()->redirect("index.php?option=com_jevents&task=plugin.jev_customfields.overview", JText::_("JEV_CUSTOM_FIELD_FILE_DOES_NOT_EXIST"));
            }
            $filename = $cfname[0];
        }

        // Set the layout
        $this->view->setLayout("edit");
        $this->view->assign('title', JText::sprintf('JEV_EDIT_CUSTOM_FIELDS', ucfirst(str_replace(".xml", "", $filename))));

        $this->view->assign("fullfile", $file);
        $this->view->assign("file", $filename);
        $this->view->assign("filename", ucfirst(str_replace(".xml", "", $filename)));

        //$file = JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/templates/".$file;
        if ($file) {
            $xml = simplexml_load_file($file);
        } else {
            $desc = JText::_("JEV_CUSTOM_FIELD_DEFAULT_DESCRIPTION");
            $emptyFile = <<< EMPTYFILE
<?xml version="1.0" encoding="utf-8"?>
<form>
    <![CDATA[
    $desc
    ]]>
    <fields>
        <fieldset addfieldpath="/plugins/jevents/jevcustomfields/customfields/" />
        <fieldset addfieldpath="/plugins/jevents/jevcustomfields/clientspecific/" />
        <fieldset name="default"  >
            <description><![CDATA[]]></description>
        </fieldset>
    </fields>
</form>
EMPTYFILE;
            $xml = simplexml_load_string($emptyFile);
        }
        $this->view->assign("xml", $xml);

        $this->view->edit();

        $this->view->display();
    }

    function save() {
        $cfname = $this->input->getString("cfname", "", "path");
        if (strpos($cfname, "..")) {
            return "";
        }
        if (strpos($cfname, "/")) {
            return "";
        }

        // empty filename!
        if ($cfname==""){
            JFactory::getApplication()->enqueueMessage(JText::_("JEVCF_CUSTOM_FIELDS_MISSING_FILENAME"),"warning");
            $cfname = uniqid("jevcf_").".xml";
        }
        
        if ($cfname && strpos($cfname, ".xml") === false) {
            $cfname .= ".xml";
        }

        $myXmlData = new stdClass();
        $myXmlData->description = $this->input->getString("description");
        $myXmlData->fieldsets = array();
        $myXmlData->fieldsetnames = $this->input->get("jevcffieldset", array(), 'array');
        $myXmlData->fieldsetdescriptions = $this->input->get("jevcffieldsetdescription", array(), 'array');
        $myXmlData->fields = array();

        //$array = JRequest::get("post",JREQUEST_ALLOWHTML);
        $types = $this->input->get("type", array(), 'array');
        foreach ($types as $fieldid => $fieldtype) {
            $filter = JFilterInput::getInstance();
            $filter->clean($fieldtype, "path");
            if (strpos($fieldtype, "..")) {
                return "";
            }
            if (strpos($fieldtype, "/")) {
                return "";
            }

            include_once( JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/" . $fieldtype . ".php");
            $typeClass = "JFormField" . ucfirst($fieldtype);
            if (!class_exists($typeClass)){
                include_once( JPATH_SITE . "/plugins/jevents/jevcustomfields/clientspecific/" . $fieldtype . ".php");
            }
            $field = new $typeClass();                

            if (($fs = $this->input->get("fieldsetmap", false, 'array')) && isset($fs[$fieldid]) && trim($fs[$fieldid]) != "") {
                if (!isset($myXmlData->fieldsets[$fs[$fieldid]])) {
                    $myXmlData->fieldsets[$fs[$fieldid]] = new stdClass();
                    $myXmlData->fieldsets[$fs[$fieldid]]->fields = array();
                }
                $myXmlData->fieldsets[$fs[$fieldid]]->fields[$fieldid] = $myXmlData->fields[$fieldid] = $field->bindField($fieldid);
            } else {
                $myXmlData->fields[$fieldid] = $this->bindField(JRequest::get("post", JREQUEST_ALLOWHTML), $fieldid);
            }
        }

        // Fix the conditional field references
        foreach ($myXmlData->fieldsets as $fieldset){
            foreach ($fieldset->fields as $fieldid => $field){
                    if (strpos($field->params, '"cf":"field')!==false) {
                        foreach ($myXmlData->fields as $cfieldid => $cfield){
                            if (strpos($field->params, '"cf":"'.$cfieldid.'"')!==false) {
                                $myXmlData->fieldsets[$fieldset][$fieldid]->params = str_replace('"cf":"field'.$cfieldid.'"', '"cf":"field'.$cfield->field_id.'"', $field->params);
                                $myXmlData->fields[$fieldid]->params = str_replace('"cf":"'.$cfieldid.'"', '"cf":"field'.$cfield->field_id.'"', $field->params);
                            }
                        }
                    }
            }
        }
        //var_dump($myXmlData);

        $myForm = new JevCfForm("com_jevent.customfields", array('control' => 'jform', 'load_data' => true));
        // make sure jevents custom fields path is included!
        $path = JPATH_ROOT . '/plugins/jevents/jevcustomfields/customfields/';
        $myForm->addFieldPath($path);
        $cspath = JPATH_ROOT . '/plugins/jevents/jevcustomfields/clientspecific/';
        $myForm->addFieldPath($cspath);

        //echo "<pre>".htmlentities($myForm->convertTemplateToXML($myXmlData))."<pre>";
        //echo "<h1>I MUST CHECK THERE IS NO DUPLICATE FILE NAME!</h1>";
        //echo "filename = $cfname<br/>";

        $task = JFactory::getApplication()->input->getCmd("task");
        $return = strpos($task, "save")>0 ? JRoute::_("index.php?option=com_jevents&task=plugin.jev_customfields.overview", false) :
            JRoute::_("index.php?option=com_jevents&task=plugin.jev_customfields.edit&cfname[]=".$cfname, false);
        
        $xml = $myForm->convertTemplateToXML($myXmlData);
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        if (!simplexml_load_string($xml)){
            $errors = libxml_get_errors();
            $xmlerrors = "";
            foreach ($errors as $error) {
                $warnlevel = "";
                switch ($error->level) {
                    case LIBXML_ERR_WARNING:
                        $warnlevel = "Warning $error->code: ";
                        break;
                    case LIBXML_ERR_ERROR:
                        $warnlevel = "Error $error->code: ";
                        break;
                    case LIBXML_ERR_FATAL:
                        $warnlevel = "Fatal Error $error->code: ";
                        break;
                }
                $xmlerrors .= $warnlevel." : ".trim($error->message)." : Line ".$error->line . " column ".$error->column."\n";
            }
            libxml_clear_errors();

            $xmlerrors = $xml . "\n". $xmlerrors;
            
            JFile::write($path . "templates/archive/invalid.xml" , $xmlerrors );
            
            JFactory::getApplication()->enqueueMessage(JText::_("JEVCF_CUSTOM_FIELDS_NOT_VALID"),"error");
            JFactory::getApplication()->redirect($return);
        }
        
        $now = new JDate("+0 seconds");
        // Backup the old version 
        jimport("joomla.filesystem.file");
        // New or copied file
        if (!JFile::exists($path . "templates/" . $cfname)) {
            JFile::write($path . "templates/" . $cfname, $xml);
            JFactory::getApplication()->enqueueMessage(JText::_("JEVCF_CUSTOM_FIELDS_UPDATED"),"message");
            JFactory::getApplication()->redirect($return);            
        }
        else if (JFile::copy($path . "templates/" . $cfname, $path . "templates/archive/" . JFile::makeSafe($now->format('d-m-y_H-i-'). $cfname)) &&
            JFile::copy($path . "templates/" . $cfname, $path . "templates/archive/" . $cfname))  
        {
        
            //$old = simplexml_load_file($path . "templates/" . $cfname);
            //$new = simplexml_load_string($xml);
            JFile::write($path . "templates/" . $cfname, $xml);
            JFactory::getApplication()->enqueueMessage(JText::_("JEVCF_CUSTOM_FIELDS_UPDATED"),"message");
            JFactory::getApplication()->redirect($return);
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_("JEVCF_UNABLE_TO_CREATE_CUSTOM_FIELDS_BACKUP"),"error");
            JFactory::getApplication()->redirect($return); 
        }
    }

    
    function setusage() {
        $cfname = $this->input->getString("cfname", "", "path");
        $usage = $this->input->getCmd("usage", "", "path");
        if (is_array($cfname)){
            if (count($cfname)>1){
                JFactory::getApplication()->enqueueMessage(JText::_("JEVCF_CUSTOM_FIELDS_TOO_MANY_FILENAMES"),"warning");
            }
            $cfname = current($cfname);
        }
        if (strpos($cfname, "..")) {
            JFactory::getApplication()->enqueueMessage(JText::_("JEVCF_CUSTOM_FIELDS_MISSING_FILENAME"),"warning");
            return "";
        }
        if (strpos($cfname, "/")) {
            JFactory::getApplication()->enqueueMessage(JText::_("JEVCF_CUSTOM_FIELDS_MISSING_FILENAME"),"warning");
            return "";
        }
        
        if ($cfname && strpos($cfname, ".xml") === false) {
            $cfname .= ".xml";
        }
        $db = JFactory::getDbo();
        if ($usage == "com_jevents") {
            $plugin = JPluginHelper::getPlugin('jevents', 'jevcustomfields' );
            $pluginparams = new JRegistry($plugin->params);
            $pluginparams->set("template",$cfname);
            $db->setQuery("UPDATE #__extensions set params = ".$db->quote(json_encode($pluginparams)). " WHERE  type='plugin' and folder='jevents' and element='jevcustomfields'");
            $db->execute();
        }

        else if ($usage == "com_jevlocations" || $usage == "com_jevpeople") {
            $compparams = JComponentHelper::getParams($usage);
            if($usage == "com_jevlocations"){                
                $compparams->set("fieldtemplate",$cfname);
            }
            else {
                $compparams->set("template",$cfname);
            }
            $db->setQuery("UPDATE #__extensions set params = ".$db->quote(json_encode($compparams)). " WHERE  type='component' and element=".$db->quote($usage));
            $db->execute();
        }
        else if (strpos ($usage, "com_jevpeople_type.")===0)
        { 
            $typeid = intval(str_replace("com_jevpeople_type.", "", $usage));
            if ($typeid>0){
                $db->setQuery("UPDATE #__jev_peopletypes set typetemplate = ".$db->quote($cfname). " WHERE type_id=$typeid");
                $db->execute();                
            }
            
        }
        
        // Clear cache of com_config component.
        $this->cleanCache('_system', 1); // admin
        $this->cleanCache('_system', 0); // site
        
        $return = JRoute::_("index.php?option=com_jevents&task=plugin.jev_customfields.overview", false);
        JFactory::getApplication()->enqueueMessage(JText::_("JEVCF_CUSTOM_FIELDS_UPDATED"),"message");
        JFactory::getApplication()->redirect($return);
        
    }
    
	/**
	 * Clean the cache
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		$conf = JFactory::getConfig();
		$dispatcher = JEventDispatcher::getInstance();

		$options = array(
			'defaultgroup' => ($group) ? $group : (isset($this->option) ? $this->option : JFactory::getApplication()->input->get('option')),
			'cachebase' => ($client_id) ? JPATH_ADMINISTRATOR . '/cache' : $conf->get('cache_path', JPATH_SITE . '/cache'));

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();

		// Trigger the onContentCleanCache event.
		$this->event_clean_cache = 'onContentCleanCache';
		$dispatcher->trigger($this->event_clean_cache, $options);
	}
    
}
