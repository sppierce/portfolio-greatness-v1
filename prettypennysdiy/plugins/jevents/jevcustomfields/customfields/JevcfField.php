<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JevcfField extends JFormField
{

	function getInput(){
		echo "should not call this directly ".  get_class($this)."  is using JevrField<br/>";
	}
	        
	function toXML($field)
	{
		$result = array();
		$result[] = "<field ";
		foreach (get_object_vars($field) as $k => $v)
		{
			if ($k == "options" || $k == "html" || $k == "defaultvalue" || $k == "name")
				continue;
			if ($k == "field_id")
			{
				$k = "name";
			}
			if ($k == "params")
			{
				if (is_string($field->params))
				{
					$field->params = @json_decode($field->params);
				}
				if (is_object($field->params))
				{
					foreach (get_object_vars($field->params) as $label => $value)
					{
						$result[] = $label . '="' . htmlspecialchars($value, ENT_QUOTES|ENT_XML1, "UTF-8") . '" ';                                                
					}
				}
				continue;
			}                        

			$result[] = $k . '="' . htmlspecialchars($v, ENT_QUOTES|ENT_XML1, "UTF-8") . '" ';
		}
		$result[] = " />";
		$xml = implode(" ", $result);
		return $xml;

	}

	public function addAttribute($name, $value)
	{
		// Add the attribute to the element, override if it already exists
		@$this->element->addAttribute($name, $value);
	}

	
	
	public function attribute($attr, $default=""){
		if (!$this->element){
			return $default;
		}
		$val = $this->element->attributes()->$attr;
		$val = !is_null($val)?(string)$val:$default;
		return $val;
	}

	/**
	 * Magic setter; allows us to set protected values
	 * @param string $name
	 * @return nothing
	 */
	public function setValue($value) {
		$this->value = $value;
	}


	public function bindField($fieldid)	{
		include_once("JevcfField.php");
		return JevcfField::bindFieldWithVarkeys($fieldid, get_object_vars($this));
	}

	public static function bindFieldWithVarkeys($fieldid, $keys = array())
	{

		$myXml = new stdClass();
		$input = JFactory::getApplication()->input;
		$filter = JFilterInput::getInstance();

		//JevcfField::bindString($myXml, $fieldid, "fn", "name");
                JevcfField::bindString($myXml, $fieldid, "fn", "field_id");
		JevcfField::bindCommand($myXml, $fieldid, "type");
		JevcfField::bindString($myXml, $fieldid, "fl", "label");
		JevcfField::bindNonBlankString($myXml, $fieldid, "ft", "description");
		JevcfField::bindNonBlankString($myXml, $fieldid, "fieldname");

		JevcfField::bindHTML($myXml, $fieldid, "dv", "default");

		JevcfField::bindPositiveInteger($myXml, $fieldid, "fa","access");
		JevcfField::bindPositiveInteger($myXml, $fieldid, "fra","readaccess");

		JevcfField::bindBoolean($myXml, $fieldid, "ao", "allowoverride");
		JevcfField::bindBoolean($myXml, $fieldid, "searchable");
		JevcfField::bindBoolean($myXml, $fieldid, "scramble");
		JevcfField::bindBoolean($myXml, $fieldid, "fo", "filter");
		JevcfField::bindBoolean($myXml, $fieldid, "fmo", "filtermenusandmodules");
		JevcfField::bindBoolean($myXml, $fieldid, "mf", "multifilter");
		JevcfField::bindBoolean($myXml, $fieldid, "ct", "contact");
                JevcfField::bindBoolean($myXml, $fieldid, "hfboc","hidefilterbasedoncategory");
                
		JevcfField::bindPositiveInteger($myXml, $fieldid, "fs","filtersize");
		JevcfField::bindPositiveInteger($myXml, $fieldid, "size");
		JevcfField::bindPositiveInteger($myXml, $fieldid, "maxlength");

		// category constraints
		if (($val = $input->get("facc", false, 'array')) && isset($val[$fieldid]) && $val[$fieldid]=="select"){
			if (($val = $input->get("facs$fieldid", false, 'array')) && count($val)>0){
				JArrayHelper::toInteger($val);
				$myXml->categoryrestrictions= implode(",",$val);;
			}
		}

		// usergroups
		if (($val = $input->get("ug", false, 'array')) && isset($val[$fieldid]) && count($val[$fieldid])>0){
			$val = $val[$fieldid];
			JArrayHelper::toInteger($val);
			$myXml->usergroups= implode(",",$val);;
		}

		// userids - currently a text field 
                if (($val = $input->get("userid", false, 'array')) && isset($val[$fieldid]) && !empty($val[$fieldid])){
			$val = explode(",",$val[$fieldid]);
			JArrayHelper::toInteger($val);
			$myXml->userid = implode(",",$val);;
		}
                
                /* 
                if (($val = $input->get("us", false, 'array')) && isset($val[$fieldid]) && count($val[$fieldid])>0){
			$val = $val[$fieldid];
			JArrayHelper::toInteger($val);
			$myXml->userid = implode(",",$val);;
		}
                 */
                                
		// buttons
		if (($val = $input->get("buttons", false, 'array')) && isset($val[$fieldid]) && count($val[$fieldid])>0){
			$val = implode(",",$val[$fieldid]);
                   	$filter = JFilterInput::getInstance();
			$filter->clean($val, 'STRING');
			$myXml->buttons = $val;
		}
                
		JevcfField::bindNonBlankString($myXml, $fieldid, "hiddenvalue");
		JevcfField::bindNonBlankString($myXml, $fieldid, "placeholder");
		JevcfField::bindNonBlankString($myXml, $fieldid, "linktext");
		JevcfField::bindString($myXml, $fieldid, "filterdefault");
                
		JevcfField::bindNonBlankString($myXml, $fieldid, "target");
                JevcfField::bindNonBlankString($myXml, $fieldid, "class");

		JevcfField::bindBoolean($myXml, $fieldid, "attendeesonly");                
		JevcfField::bindBoolean($myXml, $fieldid, "hidefilterbasedoncategory");
		JevcfField::bindBoolean($myXml, $fieldid, "matchevents");
		JevcfField::bindBoolean($myXml, $fieldid, "rr", "required");
                		
                JevcfField::bindBoolean($myXml, $fieldid, "redirect");

		// Only set required message if field is required
		if (isset($myXml->required) && $myXml->required){
			JevcfField::bindNonBlankString($myXml, $fieldid, "rm", "requiredmessage");
		}

                // No need to output ordering in XML file 
		//JevcfField::bindPositiveInteger($myXml, $fieldid, "ordering");
		JevcfField::bindPositiveInteger($myXml, $fieldid, "rows");
		JevcfField::bindPositiveInteger($myXml, $fieldid, "cols");
		JevcfField::bindPositiveInteger($myXml, $fieldid, "height");
		JevcfField::bindPositiveInteger($myXml, $fieldid, "width");

		JevcfField::bindNonBlankString($myXml, $fieldid, "class");
                
		if (($val = $input->get("options", false, 'array')) && isset($val[$fieldid])){
			$myXml->options = new stdClass();
			if (is_array($val[$fieldid]) && count($val[$fieldid])>0){
				foreach ($val[$fieldid] as $key => $values) {
					$myXml->options->$key = $values;
				}
			}
		}

		if (($val = $input->get("params", false, 'array')) && isset($val[$fieldid])){
			$myXml->params = json_encode( $val[$fieldid]);
		}

                // processed - associative keys
                $handledKeys = array("fn","fl","ft","dv","fa","fra", "ao", "fo", "fmo", "facc", "facs", "ug", "us", "rr", "rm", "mf", "fs", "hfboc");
                // skipped
                $handledKeys[]="ordering";
                $handledKeys[]="fid";
                $handledKeys[]="defaultvalue";
                $handledKeys[]="fieldsetmap";
                $handledKeys[]="pdv";
                
                // These should really be handled by the descendent classes to make sure the data is clean
		foreach ($keys as $key) {
                        if ( in_array($key, $handledKeys) || isset($myXml->$key) ) continue;
			if (($val = $input->get($key, false, 'array')) && isset($val[$fieldid]) && !empty($val[$fieldid])){
				$myXml->$key = $val[$fieldid];
			}
		}

                // Final catch up - just in case
                $keys = $input->getArray();

		// These should really be handled by the descendent classes to make sure the data is clean
		foreach ($keys as $key => $val) {
			if (!is_array($val) || in_array($key, $handledKeys) || isset($myXml->$key) ) continue;

			if (isset($val[$fieldid]) && !empty($val[$fieldid])){
				$myXml->$key = $val[$fieldid];
			}
		}
                                
		return $myXml;
	}

	public static function bindBoolean(&$myXml, $fieldid, $postname, $xmlname = false)
	{
		$input = JFactory::getApplication()->input;
		$filter = JFilterInput::getInstance();

		if (!$xmlname) {
			$xmlname = $postname;
		}
		if (($val = $input->get($postname, false, 'array')) && isset($val[$fieldid])){
			$filter->clean($val[$fieldid], 'INT');
			// only set boolean fields if the value is 1
			if ($val[$fieldid]>0) {
				$myXml->$xmlname = 1;
			}
		}
	}

	public static function bindPositiveInteger(&$myXml, $fieldid, $postname, $xmlname=false)
	{
		$input = JFactory::getApplication()->input;
		$filter = JFilterInput::getInstance();

		if (!$xmlname) {
			$xmlname = $postname;
		}
		if (($val = $input->get($postname, false, 'array')) && isset($val[$fieldid])){
			$filter->clean($val[$fieldid], 'INT');
			// only set if the value is 1
			if ($val[$fieldid]>0) {
				$myXml->$xmlname = $val[$fieldid];
			}
		}
	}

	public static function bindString(&$myXml, $fieldid, $postname, $xmlname=false)
	{
		$input = JFactory::getApplication()->input;
		$filter = JFilterInput::getInstance();

		if (!$xmlname) {
			$xmlname = $postname;
		}
		if (($val = $input->get($postname, false, 'array')) && isset($val[$fieldid])){
			$filter->clean($val[$fieldid], 'STRING');
                        $myXml->$xmlname = htmlspecialchars($val[$fieldid], ENT_XML1, "UTF-8");
		}
	}

	public static function bindNonBlankString(&$myXml, $fieldid, $postname, $xmlname=false)
	{
		$input = JFactory::getApplication()->input;
		$filter = JFilterInput::getInstance();

		if (!$xmlname) {
			$xmlname = $postname;
		}
		if (($val = $input->get($postname, false, 'array')) && isset($val[$fieldid])){
			$filter->clean($val[$fieldid], 'STRING');
			// only set if not blank
			if (trim($val[$fieldid])!=""){
                            $myXml->$xmlname = htmlspecialchars($val[$fieldid], ENT_XML1, "UTF-8");
			}
		}
	}

	public static function bindCommand(&$myXml, $fieldid, $postname, $xmlname=false)
	{
		$input = JFactory::getApplication()->input;
		$filter = JFilterInput::getInstance();

		if (!$xmlname) {
			$xmlname = $postname;
		}
		if (($val = $input->get($postname, false, 'array')) && isset($val[$fieldid])){
			$filter->clean($val[$fieldid], 'CMD');
                        $myXml->$xmlname = htmlspecialchars($val[$fieldid], ENT_XML1, "UTF-8");
		}
	}

	public static function bindHTML(&$myXml, $fieldid, $postname, $xmlname=false)
	{
		$input = JFactory::getApplication()->input;
		$filter = JFilterInput::getInstance();

		if (!$xmlname) {
			$xmlname = $postname;
		}
		if (($val = $input->get($postname, false, 'array')) && isset($val[$fieldid])){
			$filter->clean($val[$fieldid], 'HTML');
                        $myXml->$xmlname = htmlspecialchars($val[$fieldid], ENT_XML1, "UTF-8");
		}
	}

        public static function varempty($var = false) {
            if (version_compare(PHP_VERSION, "5.5", "ge")){
                return empty($var);
            }
            else {
                return !isset($var) || $var == false;
            }
        }
}