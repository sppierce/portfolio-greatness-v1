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
JFormHelper::loadFieldClass('text');

include_once(JPATH_SITE."/plugins/jevents/jevcustomfields/customfields/JevcfFieldText.php");

class JFormFieldJevcfguid extends JevcfFieldText
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'Jevcfguid';
	const name = 'jevcfguid';

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
	CustomFieldsHelper::maxlength($id,  $field, self::name);
	CustomFieldsHelper::required($id,  $field);
	CustomFieldsHelper::requiredMessage($id,  $field);
	CustomFieldsHelper::conditional($id,  $field);
	CustomFieldsHelper::allowoverride($id,  $field);
	CustomFieldsHelper::hiddenValue($id, $field);
	CustomFieldsHelper::searchable($id,  $field);
	CustomFieldsHelper::filterOptions($id, $field);
	CustomFieldsHelper::filtermenuOptions($id, $field);
	CustomFieldsHelper::applicableCategories($id, $field) ;
	CustomFieldsHelper::accessOptions($id,  $field);
	CustomFieldsHelper::readaccessOptions($id,  $field);
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

	function getInput()
	{		
		$this->value = $this->value=="" ? $this->create_guid("bviv") : $this->value;
		return parent::getInput();

	}

	
	public function setSearchKeywords(&$extrajoin)
	{
		if ( $this->attribute('searchable'))
		{
			$db = JFactory::getDBO();
			if (strpos($extrajoin, " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id") === false)
			{
				$extrajoin .= "\nLEFT JOIN #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
			}
			return "($this->map.name=" . $db->Quote($this->attribute('name')) . " AND $this->map.value LIKE (" . $db->Quote('###' . "%") . "))";
		}

	}

	public function create_guid($namespace = '')
	{
		static $guid = '';
		$uid = uniqid("", true);
		$data = $namespace;
		$data .= JRequest::getString('REQUEST_TIME', '', 'server');
		$data .= JRequest::getString('HTTP_USER_AGENT', '', 'server');
		$data .= JRequest::getString('LOCAL_ADDR', '', 'server');
		$data .= JRequest::getString('LOCAL_PORT', '', 'server');
		$data .= JRequest::getString('REMOTE_ADDR', '', 'server');
		$data .= JRequest::getString('REMOTE_PORT', '', 'server');
		$hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
		/*
		$guid = '{' .
				substr($hash, 0, 8) .
				'-' .
				substr($hash, 8, 4) .
				'-' .
				substr($hash, 12, 4) .
				'-' .
				substr($hash, 16, 4) .
				'-' .
				substr($hash, 20, 12) .
				'}';
		 */
		$guid = 	substr($hash, 0, 8) .
				'-' .
				substr($hash, 8, 4) .
				'-' .
				substr($hash, 12, 4) .
				'-' .
				substr($hash, 16, 4) .
				'-' .
				substr($hash, 20, 12) 
				;
		return $guid;

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