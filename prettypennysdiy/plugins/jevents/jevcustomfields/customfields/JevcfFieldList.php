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

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JevcfFieldList extends JFormFieldList
{	

	/*
	function getInput(){
		echo "should not call this directly ".  get_class($this)."  is using JevcfFieldList<br/>";
	}
	 */
		
	function toXML($field)
	{

		$result = array();
		$result[] = "<field ";
		foreach (get_object_vars($field) as $k => $v)
		{
			if ($k=="options" || $k=="html"  || $k=="defaultvalue" || $k=="name" ) continue;
			if ($k=="field_id") {
				$k="name";
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
		$result[] = " >";
		if (isset($field->options) && is_string($field->options))
		{
			$field->options = @json_decode($field->options);
		}
		if (isset($field->options) && is_object($field->options))
		{
			$currentOptGroup=1;
			$opengroups = 0;
			for ($i = 0; $i < count($field->options->label); $i++)
			{
				if ($field->options->label[$i] == "" && isset($field->options->optgroup) && $field->options->optgroup[$i]>=0 )
					break;
                                // can't have empty values
                                if ($field->options->label[$i] == "") {
                                    continue;
                                }
				if (isset($field->options->optgroup) && $field->options->optgroup[$i] >$currentOptGroup){
					$currentOptGroup = $field->options->optgroup[$i];
					$result[] = "<optgroup ";
					$result[] = 'label="' . htmlspecialchars($field->options->label[$i], ENT_QUOTES|ENT_XML1, "UTF-8") . '" ';
					$result[] = ">\n";
					$opengroups ++;
				}
				else if (isset($field->options->optgroup) && $field->options->optgroup[$i] < 0 ) { //$currentOptGroup){
					$currentOptGroup = -$field->options->optgroup[$i];
					$result[] = "</optgroup>\n";
					$opengroups --;
				}
				else {
					$result[] = "<option ";
					$result[] = 'value="' . htmlspecialchars($field->options->value[$i], ENT_QUOTES|ENT_XML1, "UTF-8") . '" ';
					foreach (array_keys(get_object_vars($field->options)) as $key) {
						$values = $field->options->$key;
						if ($key != "value" && $key !="label" && $key !="optgroup" && isset($values[$i])){
							$result[] = $key.'="' . htmlspecialchars($values[$i], ENT_QUOTES|ENT_XML1, "UTF-8") . '" ';
						}
					}
					$result[] = ">" . htmlspecialchars($field->options->label[$i], ENT_QUOTES|ENT_XML1, "UTF-8") . "</option>\n";
				}
			}
			for ($i =0 ; $i <$opengroups;$i++)  {
				$result[] = "</optgroup>\n";
			}
		}
		$result[] = " </field>";
		$xml = implode("", $result);
		return $xml;

	}

	public function addAttribute($name, $value)
	{
		// Add the attribute to the element, override if it already exists
		$this->element->attributes()->$name = $value;
	}

	
	public function attribute($attr, $default=""){
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
}

include_once("jevcflist.php");
