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
JFormHelper::loadFieldClass('text');

class JevcfFieldText extends JFormFieldText
{
	function getInput(){
		if (get_class($this)!="JFormFieldJevcftextarea"){
			echo "should not call this directly ".  get_class($this)." is using JevcfFieldText<br/>";
		}
		else {
			return parent::getInput();
		}
	}
	
	function toXML($field){
		$result = array();
		$result[] = "<field ";
		foreach (get_object_vars($field) as $k => $v){
			if ( $k=="options" || $k=="html"  || $k=="defaultvalue" || $k=="name") continue;
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
		$result[] = " />";
		$xml =  implode(" " , $result);
		return $xml;
	}
			
	public function addAttribute($name, $value)
	{
		// Add the attribute to the element, override if it already exists
		$this->element->attributes()->$name = $value;
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

}