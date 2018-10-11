<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevboolean.php 1569 2009-09-16 06:22:03Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

include_once("JevcfFieldCheckbox.php");

class JFormFieldJevcfcheckbox extends JevcfFieldCheckbox //JFormFieldCheckboxes
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'jevcfcheckbox';
	const name = 'jevcfcheckbox';

	public static function loadScript($field = false)
	{
		if ($field)
		{
			if (strpos($field->attribute('default'), "[") === 0 || strpos($field->attribute('default'), "{") === 0)
			{
				$field->attribute('default', json_decode($field->attribute('default')));
			}
			else
			{
				$field->attribute('default',  array($field->attribute('default')));
			}
		}

		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcfcheckbox.js');

		if ($field)
		{
			$id = 'field' . $field->fieldname;
		}
		else
		{
			$id = '###';
		}
		ob_start();
		?>
		<div class='jevcffieldinput'>

			<?php
                        CustomFieldsHelper::fieldtype($id, $field, self::name );                                                
			CustomFieldsHelper::hidden($id, $field, self::name);
			CustomFieldsHelper::label($id, $field, self::name);
                        CustomFieldsHelper::name($id, $field, self::name);
			CustomFieldsHelper::tooltip($id, $field);
			?>

			<div class="jevcflabel"><?php echo JText::_("JEVCF_OPTIONS"); ?></div>
			<div class="jevcfinputs">
				<?php echo JText::_("JEVCF_NUMERIC_OPTION_NOTES"); ?><br/>
				<?php
				$options = array();
				$maxvalue = -1;
				JFormFieldJevcfcheckbox::getElementOptions($field?$field->element:null, $options, $maxvalue);
				?>
				<input type="button" value="<?php echo JText::_("JEVCF_NEW_OPTION") ?>" onclick="jevcfcheckbox.newOption('<?php echo $id; ?>');"/>
				<table id="options<?php echo $id; ?>" class="jevcfoptions">
					<tr >
						<th><?php echo JText::_("JEVCF_OPTION_TEXT") ?></th>
						<th><?php echo JText::_("JEVCF_OPTION_VALUE") ?></th>
						<th><?php echo JText::_("JEVCF_DEFAULT_VALUE") ?></th>
						<th><?php echo JText::_("JEVCF_ARCHIVED_OPTION") ?></th>
						<th/>
					</tr>
					<?php
					JFormFieldJevcfcheckbox::generateOptionRows($options, $id);
					?>
				</table>

			</div>
			<div class="jevcfclear"></div>

			<?php
			CustomFieldsHelper::required($id, $field);
			CustomFieldsHelper::requiredMessage($id, $field);
			CustomFieldsHelper::conditional($id, $field);
			CustomFieldsHelper::allowoverride($id, $field);
			CustomFieldsHelper::accessOptions($id, $field);
			CustomFieldsHelper::applicableCategories($id, $field);
                        CustomFieldsHelper::fieldclass($id, $field);
			CustomFieldsHelper::universal($id, $field);
			?>

			<div class="jevcfclear"></div>

		</div>
		<div class='jevcffieldpreview' id='<?php echo $id; ?>preview'>
			<div class="previewlabel"><?php echo JText::_("JEVCF_PREVIEW"); ?></div>
			<div class="jevcflabel jevcfpl" id='pl<?php echo $id; ?>' ><?php echo $field ? $field->attribute('label') : JText::_("JEVCF_FIELD_LABEL"); ?></div>
			<div id="pdv<?php echo $id; ?>" >
				<?php
				for ($op = 0; $op < count($options); $op++)
				{
					$option = $options[$op];
					if ($option->label == "")
						continue;
					$checked = "";
					// default value is not the correct value and not just an index!
					if (($field && $option->checked) || (!$field && $option->value == ""))
					{
						$checked = "checked='checked'";
					}
					if ($field && $option->archive){
						continue;
					}
					?>
					<label><?php echo $option->label; ?><input type="checkbox" <?php echo $checked; ?>  value="<?php echo $option->value; ?>"/></label>
			<?php
		}
		?>
			</div>

		</div>
		<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id, $field, $html, self::name);

	}

	function getInput()
	{
	
		if (strpos($this->element['class']," checkbox")===false){
			$this->element['class'] .= " checkbox";
		}
		
		$class = ( $this->element['class'] ? 'class="'.$this->element['class'].'"' : '' );
		
		$inputoptions = $this->getOptions();
		
		if ($this->value != ""){
			$this->value = explode(",",$this->value);
			JArrayHelper::toInteger($this->value);
		}
		else {
			if (count ($inputoptions)==0){
				$this->value = array($this->element['default']);
			}
			else {
				$this->value = array();
				foreach ($inputoptions as $opt)
				{
					if ($opt->archive) continue;
					if ($opt->default==1){
						$this->value[] = $opt->value;
					}
				}
			}
		}
		
		$options = array ();
		if (count ($inputoptions)==0){
			$option = new stdClass();
			$option->label = str_replace(" *", "", strip_tags($this->getLabel()));
			$option->value = 1;
			$option->checked = in_array($option->value, $this->value) ? 'checked="checked"':'';
			$option->disabled = "";
			$option->class = "";
			$options[] = $option;
		}
		else {
			foreach ($inputoptions as $opt)
			{
				if ($opt->archive) continue;
				$option = new stdClass();
				$option->label = JText::_((string)$opt->text);
				$option->value = (int)$opt->value;
				
				$option->checked = (in_array((string) $opt->value, (array) $this->value) ? ' checked="checked"' : '');
				$option->class = !empty($opt->class) ? ' class="' . $opt->class . '"' : '';
				$option->disabled = !empty($opt->disable) ? ' disabled="disabled"' : '';
				
				$options[] = $option;
			}
		}
		
		// Jform auto includes the [] in this element name - but we want to specify an array index!
		$name = str_replace("[]","",$this->name);
		$html = "<input type='hidden'  name='".$name."[-1]' value='-1' />";
		foreach ($options as $option){
			if ($option->label != ""){
				$html .= "<label for='$this->id"."_$option->value' class='checkbox btn '>".$option->label;
			}
			$html .= "<input type='checkbox'  $class $option->checked $option->disabled $option->class  name='".$name."[$option->value]' value='$option->value' id='$this->id"."_$option->value'  />";
			if ($option->label != ""){
				$html .= "</label >";
			}
		}
		
		$html = '<div class="checkbox btn-group ">'.$html.'</div>';
		return $html;
	}
	
	public function getOptions()
	{
		$options = array();

		foreach ($this->element->children() as $option)
		{

			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
				((string) $option['disabled'] == 'true')
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Set some JEvents specific fields
			$tmp->archive = (int) $option['archive'];
			$tmp->default = (int) $option['default'];
			
			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}	

	public function convertValue($value, $node){
		static $values;
		if (!isset($values)){
			$values =  array();
		}
		if (!isset($values[$this->attribute('name')])){
			$values[$this->attribute('name')]=array();
			if (count($this->element->children())>0){
				foreach ($this->element->children() as $key=>$option)
				{
					$val	= (string) $option["value"];
					$text	= (string) $option;
					$values[$this->attribute('name')][$val] = JText::_($text);
				}
			} 
			else {
				//$val	= -1;	$values[$this->attribute('name')][$val] = JText::_("JEV_NO") ;
				$val	= 0;
				$values[$this->attribute('name')][$val] = JText::_("JEV_NO") ;
				$val	= 1;
				$values[$this->attribute('name')][$val] = JText::_("JEV_YES") ;
			}
		}
		
		$eventvalues = explode(",",$value);
		$html = array();
		foreach ($eventvalues as $val){
			if (array_key_exists($val,$values[$this->attribute('name')])){
				$html[] =  $values[$this->attribute('name')][$val];
			}

		}
		return implode(", ", $html);
	}

	function fetchRequiredScript($name, &$node, $control_name)
	{
		$script =  "JevStdRequiredFields.fields.push({'name':'".$control_name.$name."', 'default' :'".$this->attribute('default') ."' ,'reqmsg':'".trim(JText::_($this->attribute('requiredmessage'),true))."'}); ";
		return $script ;
	}


	/*
	public function constructFilter($node){
		$this->node = $node;
		$specialCharacters = array(" ", "-" , ",", ".");
		$this->filterType = str_replace($specialCharacters,"",$this->attribute("name"));
		$this->filterLabel = JevcfField::varempty($this->attribute("filterlabel"))?$this->attribute("label"):$this->attribute("filterlabel");
		// implement filter default value at a later date for checkboxes - its not trivial
		$this->filterNullValue = JevcfField::varempty($this->attribute("filterdefault"))?(JevcfField::varempty($this->attribute("default"))?"":$this->attribute("default")):$this->attribute("filterdefault");
		$this->filter_value = $this->filterNullValue;
		$this->map = "csf".$this->filterType;

		$registry	= JRegistry::getInstance("jevents");
		$this->indexedvisiblefilters = $registry->get("indexedvisiblefilters",false);
		if ($this->indexedvisiblefilters === false) return;
		
		// This is our best guess as to whether this filter is visible on this page.
		$this->visible = in_array("customfield",$this->indexedvisiblefilters);
		
		// If using caching should disable session filtering if not logged in
		$cfg	 = JEVConfig::getInstance();
		$useCache = intval($cfg->get('com_cache', 0));
		$user = JFactory::getUser();
		$mainframe = JFactory::getApplication();
		if (intval(JRequest::getVar('filter_reset',0))){
			JFactory::getApplication()->setUserState( $this->filterType.'_fv_ses', $this->filterNullValue );
			$this->filter_value = $this->filterNullValue;
		}
		// ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
		else if (!$this->visible)
		{
			$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue,"request", "int" );
		}
		else {
			$this->filter_value = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );
		}
		$this->filter_value = intval($this->filter_value );
		
		//$this->filter_value = JRequest::getInt($this->filterType.'_fv', $this->filterNullValue );
}

	public function createJoinFilter(){
		if (trim($this->filter_value)==$this->filterNullValue) return "";
		$join =  " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
		$db = JFactory::getDBO();
		$filter =  "$this->map.name=".$db->Quote($this->attribute('name'))." AND $this->map.value=".$db->Quote($this->filter_value);
		return $join . " AND ". $filter;
	}

	public function createFilter(){
		if (trim($this->filter_value)==$this->filterNullValue) return "";
		return "$this->map.id IS NOT NULL";
	}

	public function createFilterHTML(){
		$filterList=array();
		$filterList["title"]="<label class='evdate_label' for='".$this->filterType."_fv'>".JText::_($this->filterLabel)."</label>";
		$name = $this->filterType."_fv";
		$filterList["html"] =  $this->getFilterInput($name, $this->filter_value, $this->node, "");

		$name .= $this->filterNullValue;
		$script = "function reset".$this->filterType."_fv(){\$('$name').checked=true;};\n";
		$script .= "try {JeventsFilters.filters.push({action:'reset".$this->filterType."_fv()',id:'".$this->filterType."_fv',value:".$this->filterNullValue."});} catch (e) {}";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;
	}
	 */


	/**
	 * Magic setter; allows us to set protected values
	 * @param string $name
	 * @return nothing
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	public static function buttonAction($id, $op)
	{
		echo 'onkeyup="jevcfcheckbox.updatePreview( \'' . $id . '\', \'' . $op . '\');" '; //onblur="jevcfcheckbox.updatePreview( \''.$id.'\');"';
		return "";
		echo 'onkeyup="jevcfcheckbox.showNext(this, \'' . $id . '\', ' . $op . ');" onblur="jevcfcheckbox.showNext(this, \'' . $id . '\', ' . $op . ');"';

	}

	public function bindField($fieldid)	{
		include_once("JevcfField.php");
		return JevcfField::bindFieldWithVarkeys($fieldid, get_object_vars($this));
	}

	protected static function getElementOptions($element, &$options,  &$maxvalue) {

		if ($element && $element->count())
		{

			$optionvalues = $element->children();
			if (isset($optionvalues))
			{
				foreach ($optionvalues as $optionelem)
				{
					$val = (string)$optionelem["value"];
					$maxvalue = $maxvalue > $val ? $maxvalue : $val;
				}

				foreach ($optionvalues as $optionelem)
				{
					$option = new stdClass();
					$option->value = (string)$optionelem["value"];
					$option->archive = (int)$optionelem["archive"];
					$option->checked = (int)$optionelem["default"];
					$lab = (string)$optionelem;
					$option->label = $lab;
					$option->isBlankRow = false;

					$options[] = $option;
				}
			}
		}
		// First entry for new field
		else {
			$option = new stdClass();
			$option->value = $maxvalue+1;
			$option->checked = 0;
			$option->archive = 0;
			$maxvalue++;
			$option->label = "";
			$option->isBlankRow = false;
			$options[] = $option;
		}

		// add a blank option at the end
		$option = new stdClass();
		$option->value = $maxvalue + 1;
		$option->checked = 0;
		$option->archive = 0;
		$maxvalue++;
		$option->label = "";
		$option->isBlankRow = true;
		$options[] = $option;

	}

	static function generateOptionRows($options,  $id){
		static $currentgroup = 0;
		for ($op = 0; $op < count($options); $op++)
		{
			$option = $options[$op];
			$style = "";
			if ($op > 0 && $option->isBlankRow)
			{
				$style = "style='display:none;' class='blankrow$op blankrow'";
			}

			$checked = "";
			$archivechecked = "";

                        // default value is not the correct value and not just an index!
                        if ($option->checked)
                        {
                                $checked = "checked='checked'";
                        }

			if ($option->archive)
			{
				$archivechecked = "checked='checked'";
			}
			/*
			if (($field && $option->value == $field->attribute('default')) || (!$field && $option->value == ""))
			{
				$checked = "checked='checked'";
			}
			 */
			?>
			<tr <?php echo $style; ?> >
				<td>
                                    <input type="text" class="inputlabel" name="options[<?php echo $id; ?>][label][]" id="options<?php echo $id; ?>_t_<?php echo $op; ?>" value="<?php echo htmlspecialchars($option->label); ?>" <?php JFormFieldJevcfcheckbox::buttonAction($id, $op); ?>/>
				</td>
				<td>
					<input type="text" name="options[<?php echo $id; ?>][value][]" id="options<?php echo $id; ?>_v_<?php echo $op; ?>" value="<?php echo htmlspecialchars($option->value); ?>" <?php JFormFieldJevcfcheckbox::buttonAction($id, $op); ?>  class="jevoption_value" />
				</td>
				<td>
					<input type="checkbox" name="options[<?php echo $id; ?>][default][<?php echo $op; ?>]" id="default<?php echo $id; ?>_r_<?php echo $op; ?>" value="1" onclick="jevcfcheckbox.defaultOption(this, '<?php echo $id; ?>', '<?php echo $op; ?>');"   <?php echo $checked; ?>/>
				</td>
				<td>
					<input type="checkbox" name="options[<?php echo $id; ?>][archive][<?php echo $op; ?>]" id="archive<?php echo $id; ?>_r_<?php echo $op; ?>" value="1"   <?php echo $archivechecked; ?>/>
				</td>
				<td>
					<input type="button" value="<?php echo JText::_("JEVCF_DELETE_OPTION") ?>" onclick="jevcfcheckbox.deleteOption(this);"/>
				</td>
			</tr>
			<?php

		}
	}

}