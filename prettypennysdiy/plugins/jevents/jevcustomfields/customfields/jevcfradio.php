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

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('radio');

include_once("JevcfFieldRadio.php");

class JFormFieldJevcfradio  extends JevcfFieldRadio
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'jevcfradio';
	const name = 'jevcfradio';

	public static function loadScript($field = false)
	{
		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcfradio.js');

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

			<div class="jevcflabel"><?php echo JText::_("JEVCF_FIELD_TYPE"); ?></div>
			<div class="jevcfinputs" style="font-weight:bold;"><?php echo JText::_("CUSTOM_FIELD_TYPE_jevcfradio"); ?><?php CustomFieldsHelper::fieldId($id); ?><?php CustomFieldsHelper::fieldId($id); ?></div>
			<div class="jevcfclear"></div>

			<?php
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
				JFormFieldJevcfradio::getElementOptions($field?$field->element:null, $options, $maxvalue);

				?>
				<input type="button" value="<?php echo JText::_("JEVCF_NEW_OPTION") ?>" onclick="jevcfradio.newOption('<?php echo $id; ?>');"/>
				<table id="options<?php echo $id; ?>">
					<tr >
						<th><?php echo JText::_("JEVCF_OPTION_TEXT") ?></th>
						<th><?php echo JText::_("JEVCF_OPTION_VALUE") ?></th>
						<th><?php echo JText::_("JEVCF_DEFAULT_VALUE") ?></th>
						<th><?php echo JText::_("JEVCF_ARCHIVED_OPTION") ?></th>
						<th/>
					</tr>
					<?php
					JFormFieldJevcfradio::generateOptionRows($options, $field?$field->attribute('default'):"", $id);
					?>
				</table>

			</div>
			<div class="jevcfclear"></div>

			<?php
			CustomFieldsHelper::required($id, $field);
			CustomFieldsHelper::requiredMessage($id, $field);
                                
			CustomFieldsHelper::conditional($id, $field);
			CustomFieldsHelper::allowoverride($id, $field);
                        CustomFieldsHelper::hiddenValue($id, $field);
			CustomFieldsHelper::filterOptions($id, $field);
			CustomFieldsHelper::filtermenuOptions($id, $field);
                       	CustomFieldsHelper::filterDefault($id, $field);
                        CustomFieldsHelper::multiFilter($id, $field);

			CustomFieldsHelper::searchable($id, $field);
			CustomFieldsHelper::accessOptions($id, $field);
                        CustomFieldsHelper::readaccessOptions($id,  $field);
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
					if ($option->archive == 1)
						continue;
					$checked = "";
					// default value is not the correct value and not just an index!
					//if (($field && $op==$field->attribute('default')) || (!$field && $option->value == ""))
					if (($field && $option->value == $field->attribute('default')) || (!$field && $option->value == ""))
					{
						$checked = "checked='checked'";
					}
					?>
					<label><?php echo $option->label; ?><input name='pvname<?php echo $field->fieldname;?>' type="radio" <?php echo $checked; ?> value="<?php echo $option->value; ?>" /></label>
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

	function getFilterInput($name, $value, &$node, $control_name, $allowmultiple = false)
	{
		$class = ( $this->element['class'] ? 'class="'.$this->element['class'].' btn-group"' : 'class=" btn-group"');
		$options = array ();
		$options = $this->getOptions();

		// remove archive elements if appropriate and check for existence of filter default value
                $filterdefault = $this->attribute("filterdefault")==""?"xysd8876534%^*":$this->attribute("filterdefault");
                $filterdefaultoption = false;
                $filterhasminusone = false;
		foreach ($options as $index => $option){
			if (isset($option->archive) && $option->archive) {
				unset($options[$index]);
			}
                        if ($option->value == $filterdefault){
                            $filterdefaultoption = $option;
                        }
                        if ($option->value == $filterdefault){
                            $filterhasminusone = true;
                        }		                       
                }
                
		$options = array_values($options);

                // Do we need to add a hidden filter option in the filter
                if ($filterdefaultoption===false){
                    $option = new stdClass();
                    // no need to set the option text - this is invisible
                    if ($filterdefault == "xysd8876534%^*"){
                        if ($filterhasminusone){
                            $option->value = -3141592;
                        }
                        else {
                            $option->value = -1;
                        }
                    }
                    else {
                        $option->value = $filterdefault;
                    }
                    $option->hidden = true;
                    $option->text = "";
                    $options[] = $option;
                }
                
		if ($allowmultiple && $this->attribute('multifilter',0)==1){
			if ($value != ""){
				$value = explode(",",$value);
				JArrayHelper::toInteger($value);
			}
			else {
				$value = array();
			}
			$html = "";
			foreach ($options as $option){
				$option->checked = (in_array($option->value, $value)) ? "checked='checked'":"";
                                if (isset($option->hidden) &&  $option->hidden== true){
                                    if ($option->checked == "" && in_array($filterdefault, $value)){
                                        $option->checked =  "checked='checked'";
                                    }
                                    $option->checked .= " style='display:none'";
                                }
				$optiontext = $option->text;
				if ($optiontext != ""){
					$html .= "<label for='$control_name".$name."_$option->value' id='$control_name".$name."$option->value-lbl' >";
				}
				$html .= "<input type='checkbox'  $class $option->checked name='".$control_name.$name."[$option->value]' value='$option->value' id='$control_name".$name."_$option->value'  />";
				if ($optiontext != ""){
					$html .= "<span>".$optiontext. "</span></label >";
				}
			}
			return $html;
			
			$multiple = ' multiple="multiple"';
			$size =  ( $this->attribute('filtersize') ? ' size="'.$this->attribute('filtersize').'"' : '' );
			return JHTML::_('select.genericlist',  $options, ''.$control_name.$name."[]", $class.$size.$multiple, 'value', 'text', $value, $control_name.$name);
		}
		else {
			$html =  JHTML::_('select.radiolist', $options, ''.$control_name.$name, $class, 'value', 'text', $value, $control_name.$name );
                        // See http://stackoverflow.com/questions/15117974/str-replace-or-preg-replace-only-replacing-last-match
                        // replace last input field to make it hidden
                        if ($filterdefaultoption===false){
                            $html =  preg_replace('#.*\K<label #s' , '$1<label style="display:none!important" ', $html);
                        }
                        return $html;
		}
	}

	/**
	 * Method to get the radio button field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		if ($params->get("bootstrapchosen", 1) && strpos($this->element['class'],"btn-group")===false){
			$this->element['class'] .= " btn-group";
		}
		if (!$params->get("bootstrapchosen", 1) && strpos($this->element['class'],"btn-group")!==false){
			$this->element['class'] = str_replace("btn-group", "",$this->element['class']);
		}

		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio"';

		// Start the radio field output.
		$html[] = '<div id="' . $this->id . '"' . $class . '>';

		// Get the field options.
		$options = $this->getOptions();

		// remove archive elements if appropriate
		foreach ($options as $index => $option){
			if (isset($option->archive) && $option->archive && ((string) $option->value != (string) $this->value)) {
				unset($options[$index]);
			}
		}

		// Build the radio field output.
		foreach ($options as $i => $option)
		{

			// Initialize some option attributes.
			$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
			$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';
			$required = !empty($option->required) ? ' required="required" aria-required="true"' : '';

			// Set some JEvents specific fields
			//$tmp->archive = (int) $option['archive'];
			//$tmp->default = (int) $option['default'];

			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

			$temp = '<input type="radio" id="' . $this->id ."_". $i . '" name="' . $this->name . '" value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . $required . '/>';

			$html[] = '<label for="' . $this->id ."_" . $i . '"' . $class . '>'
				. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . $temp. '</label>';
		}

		// End the radio field output.
		$html[] = '</div>';

		return implode($html);
	}

	public function getOptions()
	{
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option)
		{

			if ((int) $option['default']) {
				continue;
			}
			
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
			$tmp->class = (string) $option['class']. " btn radio";

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Set some JavaScript option attributes.
			$tmp->archive = @(int) $option['archive'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);


		return $options;
	}

	public function convertValue($value, $node){
                $hiddenvalue = $this->attribute('hiddenvalue');
		if (!empty($hiddenvalue) && $value==$hiddenvalue) return "";
		static $values;
		if (!isset($values)){
			$values =  array();
		}
		if (!isset($values[$this->attribute('name')])){
			$values[$this->attribute('name')]=array();
			foreach ($this->element->children() as $option)
			{
				$val	= (string) $option['value'];
				$text	= (string) $option;
				$values[$this->attribute('name')][$val] = $text;
			}
		}
		if (array_key_exists($value,$values[$this->attribute('name')])){
			return $values[$this->attribute('name')][$value];
		}
		else {
			return "";
		}
	}

	public function fetchRequiredScript($name, &$node, $control_name)
	{
		$script =  "JevStdRequiredFields.fields.push({'name':'".$control_name.$name."', 'default' :'".$this->attribute('default') ."' ,'reqmsg':'".trim(JText::_($this->attribute('requiredmessage'),true))."'}); ";
		return $script ;
	}


	public function constructFilter($node){
		$this->node = $node;
		$specialCharacters = array(" ", "-" , ",", ".");
		$this->filterType = str_replace($specialCharacters,"",$this->attribute("name"));
		$this->filterLabel = JevcfField::varempty($this->attribute("filterlabel"))?$this->attribute("label"):$this->attribute("filterlabel");
		$this->filterNullValue = JevcfField::varempty($this->attribute("filterdefault"))?(JevcfField::varempty($this->attribute("default"))?"":$this->attribute("default")):$this->attribute("filterdefault");
                // varempty reports zero as a blank!
                if ($this->filterNullValue == ""){
                    if ($this->attribute("filterdefault")==='0'){
                        $this->filterNullValue = 0;
                    }
                    else if ($this->attribute("default")==='0'){
                        $this->filterNullValue = 0;
                    }
                }
                
		$this->filter_value = $this->filterNullValue;
		$this->map = "csf".$this->filterType;

		$registry	= JRegistry::getInstance("jevents");
		$this->indexedvisiblefilters = $registry->get("indexedvisiblefilters",false);
		if ($this->indexedvisiblefilters === false) return;
		
		// This is our best guess as to whether this filter is visible on this page.
		$this->visible = in_array("customfield",$this->indexedvisiblefilters);
		
		// If using caching should disable session filtering if not logged in
		$cfg	 = JEVConfig::getInstance();
		$useCache = (int) $cfg->get('com_cache', 0);
		$user = JFactory::getUser();
		$mainframe = JFactory::getApplication();

		if ($this->attribute("multifilter")==1){
			$this->filterNullValue = array($this->filterNullValue );
			if ((int) JRequest::getVar('filter_reset',0)){
				JFactory::getApplication()->setUserState( $this->filterType.'_fv_ses', $this->filterNullValue );
				$this->filter_value = $this->filterNullValue;
				if (is_string($this->filter_value)){
					$this->filter_value=  array($this->filter_value);
				}
			}
			// ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
			else if (!$this->visible)
			{
				$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue,"request", "array" );
				if (is_string($this->filter_value)){
					$this->filter_value=  array($this->filter_value);
				}
				JArrayHelper::toInteger($this->filter_value);
			}
			else {
				$this->filter_value = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );
				if (is_string($this->filter_value)){
					$this->filter_value=  array($this->filter_value);
				}
				JArrayHelper::toInteger($this->filter_value);
			}
		}
		else {
			if ((int) JRequest::getVar('filter_reset',0)){
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
			$this->filter_value = (int) $this->filter_value;
		}		
		//$this->filter_value = JRequest::getInt($this->filterType.'_fv', $this->filterNullValue );
}

	public function createJoinFilter(){
		if ($this->attribute("multifilter")==1){
			if ($this->filter_value==$this->filterNullValue) return "";
			if ($this->filter_value==array(-1=>-1)) return "";
			if (count($this->filter_value)==0) return "";
		}
		else {
			if (isset($this->filter_value) && trim($this->filter_value)==$this->filterNullValue) return "";
		}
		$join =  " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";

		$db = JFactory::getDBO();
		if ($this->attribute("multifilter")==1){
			$filter =  "$this->map.name=".$db->Quote($this->attribute('name')). " AND ( ";
			$bits = array();
			foreach ($this->filter_value as $fv) {
				$bits[] = " $this->map.value RLIKE ".$db->Quote("[[:<:]]".$fv."[[:>:]]");
			}
			$filter .= implode(" OR ",$bits);
			$filter .= ")";
		}
		else {
			$filter =  "$this->map.name=".$db->Quote($this->attribute('name'))." AND $this->map.value=".$db->Quote($this->filter_value);
		}
		return $join . " AND ". $filter;
	}

	public function createFilter(){
		if ($this->attribute("multifilter")==1){
			if ($this->filter_value==$this->filterNullValue) return "";
			if ($this->filter_value==array(-1=>-1)) return "";
			if (count($this->filter_value)==0) return "";
		}
		else {
			if (trim($this->filter_value)==$this->filterNullValue) return "";
		}
		return "$this->map.id IS NOT NULL";
	}

	public function createFilterHTML(){
		$filterList=array();
		$filterList["title"]="<label class='evdate_label' for='".$this->filterType."_fv'>".JText::_($this->filterLabel)."</label>";
		if ($this->attribute("multifilter")==1){
			// allow multiple select!
			$name = $this->filterType."_fv";
			$filterList["html"] =  $this->getFilterInput($name, implode(",",$this->filter_value), $this->node, "", true);
			$script = "function reset".$this->filterType."_fv(){
						for (i=0;i<100;i++){
							if (\$('$name'+'_'+i)){
								\$('$name'+'_'+i).checked=false;
							}
							else {
								break;
							}
						}
					};\n";
			$script .= "try {JeventsFilters.filters.push({action:'reset".$this->filterType."_fv()',id:'".$this->filterType."_fv',value:".implode(",",$this->filterNullValue)."});} catch (e) {}";			
		}
		else {
			$name = $this->filterType."_fv";
			$filterList["html"] =  $this->getFilterInput($name, $this->filter_value, $this->node, "");
			$name .= $this->filterNullValue;
			$script = "function reset".$this->filterType."_fv(){\$('$name').checked=true;};\n";
			$script .= "try {JeventsFilters.filters.push({action:'reset".$this->filterType."_fv()',id:'".$this->filterType."_fv',value:".$this->filterNullValue."});} catch (e) {}";
		}

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;
	}
	

	/**
	 * Magic setter; allows us to set protected values
	 * @param string $name
	 * @return nothing
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	public function bindField($fieldid)
	{
		include_once("JevcfField.php");
		return JevcfField::bindFieldWithVarkeys($fieldid, array_keys(get_object_vars($this)));
	}

	public static function buttonAction($id, $op)
	{
		echo 'onkeyup="jevcfradio.updatePreview( \'' . $id . '\', \'' . $op . '\');" '; //onblur="jevcfradio.updatePreview( \''.$id.'\');"';
		return "";
		echo 'onkeyup="jevcfradio.showNext(this, \'' . $id . '\', ' . $op . ');" onblur="jevcfradio.showNext(this, \'' . $id . '\', ' . $op . ');"';

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

	static function generateOptionRows($options, $selected, $id){
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
			if ($option->value == $selected)
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
					<input type="text" class="inputlabel" name="options[<?php echo $id; ?>][label][]" id="options<?php echo $id; ?>_t_<?php echo $op; ?>" value="<?php echo $option->label; ?>" <?php JFormFieldJevcfradio::buttonAction($id, $op); ?>/>
				</td>
				<td>
					<input type="text" name="options[<?php echo $id; ?>][value][]" id="options<?php echo $id; ?>_v_<?php echo $op; ?>" value="<?php echo $option->value; ?>" <?php JFormFieldJevcfradio::buttonAction($id, $op); ?>  class="jevoption_value" />
				</td>
				<td>
					<input type="radio" value="<?php echo $option->value; ?>" onclick="jevcfradio.defaultOption(this, '<?php echo $id; ?>', '<?php echo $op; ?>');"  name="dv[<?php echo $id; ?>]" id="default<?php echo $id; ?>_r_<?php echo $op; ?>" <?php echo $checked; ?>/>
				</td>
				<td>
					<input type="checkbox" name="options[<?php echo $id; ?>][archive][<?php echo $op; ?>]" id="archive<?php echo $id; ?>_r_<?php echo $op; ?>" value="1"   <?php echo $archivechecked; ?> onclick="jevcfradio.updatePreview('<?php echo $id;?>' , '<?php echo $op;?>');" />
				</td>
				<td>
					<input type="button" value="<?php echo JText::_("JEVCF_DELETE_OPTION") ?>" onclick="jevcfradio.deleteOption(this);"/>
				</td>
			</tr>
			<?php

		}
	}

}
