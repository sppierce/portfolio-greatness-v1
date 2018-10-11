<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevtext.php 1569 2009-09-16 06:22:03Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
include_once('jevcftext.php');

class JFormFieldJevcfyoutube extends JFormFieldJevcftext
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'Jevcfyoutube';
	const name = 'jevcfyoutube';

	public static function loadScript($field=false){
		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcftext.js');

		if ($field){
			$id = 'field'.$field->fieldname;
		}
		else {
			$id = '###';
		}
		ob_start();
?>
<div class='jevcffieldinput'>

	<?php
        CustomFieldsHelper::fieldtype($id, $field, self::name );        
	CustomFieldsHelper::hidden($id,  $field, self::name);
	CustomFieldsHelper::label($id,  $field, self::name);
        CustomFieldsHelper::name($id, $field, self::name);
	CustomFieldsHelper::tooltip($id,  $field);
	?>

	<div class="jevcflabel">
		<?php echo JText::_("JEVCF_DEFAULT_VALUE");?>
		<?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_DEFAULT_VALUE_DESC'));?>
	</div>
	<div class="jevcfinputs">
		<input type="text" name="dv[<?php echo $id;?>]" id="dv<?php echo $id;?>" size="<?php echo $field?$field->attribute('size'):5;?>"  value="<?php echo $field?$field->attribute('default'):"";?>"  onchange="jevcftext.setvalue('<?php echo $id;?>');"  onkeyup="jevcftext.setvalue('<?php echo $id;?>');"/>
	</div>
	<div class="jevcfclear"></div>

	<?php
	CustomFieldsHelper::size($id,  $field, self::name);
	CustomFieldsHelper::height($id,  $field, self::name);
	CustomFieldsHelper::width($id,  $field, self::name);
	CustomFieldsHelper::maxlength($id,  $field, self::name);
	CustomFieldsHelper::required($id,  $field);
	CustomFieldsHelper::requiredMessage($id,  $field);
	CustomFieldsHelper::conditional($id,  $field);
	CustomFieldsHelper::allowoverride($id,  $field);
	CustomFieldsHelper::hiddenValue($id, $field);
	CustomFieldsHelper::searchable($id,  $field);
	CustomFieldsHelper::filterOptions($id, $field);
	CustomFieldsHelper::filtermenuOptions($id, $field);
	CustomFieldsHelper::filterDefault($id, $field);
	CustomFieldsHelper::applicableCategories($id, $field) ;
	CustomFieldsHelper::accessOptions($id,  $field);
	CustomFieldsHelper::readaccessOptions($id,  $field);
        CustomFieldsHelper::fieldclass($id, $field);
	CustomFieldsHelper::universal($id, $field);
	?>

	<div class="jevcfclear"></div>

</div>
<div class='jevcffieldpreview'  id='<?php echo $id;?>preview'>
	<div class="previewlabel"><?php echo JText::_("JEVCF_PREVIEW");?></div>
	<div class="jevcflabel jevcfpl" id='pl<?php echo $id;?>' ><?php echo $field?$field->attribute('label'):JText::_("JEVCF_FIELD_LABEL");?></div>
	<input type="text"  id="pdv<?php echo $id;?>" value="<?php echo $field?$field->attribute('default'):"";?>" size="<?php echo $field?$field->attribute('size'):5;?>"  />
</div>
<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id,  $field, $html, self::name	);

	}


	function fetchRequiredScript($name, &$node, $control_name)
	{
		return "JevStdRequiredFields.fields.push({'name':'" . $control_name . $name . "', 'default' :'" . $this->attribute('default') . "' ,'reqmsg':'" . trim(JText::_($this->attribute('requiredmessage'), true)) . "'}); ";

	}

	public function convertValue($value, $node)
	{
		$width = $this->attribute("width") ? $this->attribute("width") : 420;
		$height = $this->attribute("height") ? $this->attribute("height") : 315;
		if ($value == "" ||  $value == $this->attribute('hiddenvalue'))
			return $value;
		else if (strpos(strtolower($value), "vimeo=")===0){
			$value = str_ireplace("vimeo=","", $value);
			return '<iframe width="'.$width.'" height="'.$height.'" src="https://player.vimeo.com/video/' . $value . '?color=#339900" frameborder="0" allowfullscreen class="jevvimeo"></iframe>';
		}
		else {
			$value = str_ireplace("http://www.youtube.com/watch?v=","", $value);
			return '<iframe width="'.$width.'" height="'.$height.'" src="https://www.youtube.com/embed/' . $value . '" frameborder="0" allowfullscreen class="jevyoutube"></iframe>';
		}
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