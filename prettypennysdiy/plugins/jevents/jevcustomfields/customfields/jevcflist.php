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
include_once ("JevcfFieldList.php");

class JFormFieldJevcflist extends JevcfFieldList
{

	protected $node;
	const name = 'jevcflist';

	public static function loadScript($field=false)
	{
		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcflist.js');

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

			if ($field)
			{
				try {
					$params = json_decode($field->attribute('params'));
				}
				catch (Exception $e) {
					$params = array();
				}
			}
			?>

			<div class="jevcflabel"><?php echo JText::_("JEVCF_OPTIONS"); ?></div>
			<div class="jevcfinputs">
				<?php echo JText::_("JEVCF_NUMERIC_OPTION_NOTES"); ?><br/>
				<!-- Put the selected option here //-->
				<input type="hidden" name="dv[<?php echo $id; ?>]" id="dv<?php echo $id; ?>" value="<?php echo $field ? $field->attribute('default') : ""; ?>" />
				<?php
				$options = array();
				$maxvalue = -1;
				JFormFieldJevcflist::getElementOptions($field?$field->element:null, $options, $maxvalue);

				// we can't add thse in getElementOptions since its called recursively
				// add a blank option at the end
				$option = new stdClass();
				$option->value = $maxvalue + 1;
				$option->checked = 0;
				$option->archive = 0;
				$maxvalue++;
				$option->label = "";
				// Is this is an OptGroup
				$option->isOptGroup = false;
				$option->isBlankRow = true;
				$options[] = $option;

				// add a blank optgroup row of each type
				$option = new stdClass();
				$option->value = 0;
				$option->checked = 0;
				$option->archive = 0;
				$maxvalue++;
				$option->label = "";
				// Is this is an OptGroup
				$option->isOptGroup = true;
				$option->options = array();
				$option->isBlankRow = true;
				$options[] = $option;


				?>
				<input type="button" value="<?php echo JText::_("JEVCF_NEW_OPTION") ?>" onclick="jevcflist.newOption('<?php echo $id; ?>');"/>
				<input type="button" value="<?php echo JText::_("JEVCF_START_NEW_OPTGROUP") ?>" onclick="jevcflist.startOptgroup('<?php echo $id; ?>');" class="jevcf_startoptgroup"/>
				<input type="button" value="<?php echo JText::_("JEVCF_CLOSE_OPTGROUP") ?>" onclick="jevcflist.endOptgroup('<?php echo $id; ?>');" class="jevcf_endoptgroup"/>
				<table id="options<?php echo $id; ?>" class="jevcfoptions">
					<tr >
						<th><?php echo JText::_("JEVCF_OPTION_TEXT") ?></th>
						<th><?php echo JText::_("JEVCF_OPTION_VALUE") ?></th>
						<th><?php echo JText::_("JEVCF_DEFAULT_VALUE") ?></th>
						<th><?php echo JText::_("JEVCF_ARCHIVED_OPTION") ?></th>
						<th/>
					</tr>
					<?php
					JFormFieldJevcflist::generateOptionRows($options, $field?$field->attribute('default'):"", $id);
					?>
				</table>

			</div>
			<div class="jevcfclear"></div>

			<?php
			CustomFieldsHelper::filterOptions($id, $field);
			CustomFieldsHelper::filtermenuOptions($id, $field);
                	CustomFieldsHelper::filterDefault($id, $field);                        
                        CustomFieldsHelper::multiFilter($id, $field);
                        CustomFieldsHelper::filterSize($id, $field);
                        
			CustomFieldsHelper::required($id, $field);
			CustomFieldsHelper::requiredMessage($id, $field);
			CustomFieldsHelper::conditional($id, $field);
			CustomFieldsHelper::allowoverride($id, $field);
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
			<select name="pdv[<?php echo $id; ?>]" id="pdv<?php echo $id; ?>" >
				<?php
				JFormFieldJevcflist::generateOptionsPreview($options, $field?$field->attribute('default'):"", $id);
				?>
			</select>

		</div>
		<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id, $field, $html, self::name);

	}

	public function getInput() {
		// make sure we have a helpful class set to get the width
		if (!$this->element['class'] ){
			$this->element['class'] =" jevminwidth";
		}
		return parent::getInput();
	}
	
	function getFilterInput($name, $value, &$node, $control_name, $allowmultiple = false)
	{
		$class = ( $this->element['class'] ? 'class="'.$this->element['class'].'"' : 'class="inputbox"' );

		$options = array ();
		foreach ($this->element->children() as $option)
		{
			if ((int) $option['archive']) continue;
			$val	= (string) $option["value"];
			$text	= (string) $option;
			// Joomla translation splits strings with commas in them
			if (strpos($text, ",")===false){
				$options[] = JHTML::_('select.option', $val, JText::_($text));
			}
			else {
				$options[] = JHTML::_('select.option', $val, $text);
			}

		}

		if ($allowmultiple && $this->attribute('multifilter',0)==1){
			if ($value != ""){
				$value = explode(",",$value);
				// these do not have to be integer values ??
				JArrayHelper::toInteger($value);
			}
			else {
				$value = array();
			}

			$size =  ( $this->attribute('filtersize') ? ' size="'.$this->attribute('filtersize').'"' : '' );
			$multiple = ' multiple="multiple"';
			return JHTML::_('select.genericlist',  $options, ''.$control_name.$name."[]", $class.$size.$multiple, 'value', 'text', $value, $control_name.$name);
		}
		else {
			return JHTML::_('select.genericlist',  $options, ''.$control_name.$name, $class, 'value', 'text', $value, $control_name.$name);
		}

	}

	function fetchRequiredScript($name, &$node, $control_name)
	{
		$script =  "JevStdRequiredFields.fields.push({'name':'".$control_name.$name."', 'default' :'".$this->attribute('default') ."' ,'reqmsg':'".trim(JText::_($this->attribute('requiredmessage'),true))."'}); ";
		return $script ;
	}
/*
	function fetchCategoryRestrictionScript($name, &$node, $control_name, $cats)
	{
		$script = "JevrCategoryFields.fields.push({'name':'".$name."', 'default' :'".$this->attribute('default') ."' ,'catids':".  json_encode($cats)."}); ";
		return $script;
	}
 */
	public function convertValue($value, $node){
		static $values;
		if (!isset($values)){
			$values =  array();
		}
		if (!isset($values[$this->attribute('name')])){
			$values[$this->attribute('name')]=array();
			foreach ($this->element->children() as $option)
			{
				// Only add <option /> and <optgroup> elements.
				if ($option->getName() == 'optgroup')
				{
					foreach ($option->children() as $ogoption)
					{
						$val	= (string) $ogoption['value'];
						$text	= (string) $ogoption;
						if (JText::_($text)!=$text){
							$text = JText::_($text);
						}
						$values[$this->attribute('name')][$val] = $text;
					}

				}
				else {
					$val	= (string) $option['value'];
					$text	= (string) $option;
					if (JText::_($text)!=$text){
						$text = JText::_($text);
					}
					$values[$this->attribute('name')][$val] = $text;
				}
			}
		}
		if (array_key_exists($value,$values[$this->attribute('name')])){
			return $values[$this->attribute('name')][$value];
		}
		else {
			return "";
		}
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
				// These do not have to be integer values ??
				JArrayHelper::toInteger($this->filter_value);
			}
			else {
				$this->filter_value = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );
				if (is_string($this->filter_value)){
					$this->filter_value=  array($this->filter_value);
				}
				// These do not have to be integer values ??
				JArrayHelper::toInteger($this->filter_value);
			}
		}
		else {
			if ((int) JRequest::getVar('filter_reset',0)) {
				JFactory::getApplication()->setUserState( $this->filterType.'_fv_ses', $this->filterNullValue );
				$this->filter_value = $this->filterNullValue;
			}
			// ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
			else if (!$this->visible )
			{
				// These do not have to be integer values ??
				$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue,"request", "int" );
				//$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue,"request", "string" );
			}
			else {
				$this->filter_value = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );
			}
			// These do not have to be integer values ??
			$this->filter_value = (int) $this->filter_value;
			//$this->filter_value = trim($this->filter_value );
		}
		
		//$this->filter_value = JRequest::getInt($this->filterType.'_fv', $this->filterNullValue );
		
	}


	public function createJoinFilter(){
		if ($this->attribute("multifilter")==1){
			if ($this->filter_value==$this->filterNullValue) return "";
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
			$filter =  "$this->map.name=".$db->Quote($this->attribute("name"))." AND $this->map.value=".$db->Quote($this->filter_value);
		}
		return $join . " AND ". $filter;
		
	}

	public function createFilter(){
		if ($this->attribute("multifilter")==1){
			if ($this->filter_value==$this->filterNullValue) return "";
			if (count($this->filter_value)==0) return "";
		}
		else {
			if (isset($this->filter_value) && trim($this->filter_value)==$this->filterNullValue) return "";
		}
		return "$this->map.id IS NOT NULL";		
	}

	public function createFilterHTML(){
		$filterList=array();
		$filterList["title"]="<label class='evdate_label' for='".$this->filterType."_fv'>".JText::_($this->filterLabel)."</label>";
		if ($this->attribute("multifilter")==1){
			// allow multiple select!
			$nullvalue = is_array($this->filterNullValue) ? $this->filterNullValue : array($this->filterNullValue);
			$value = is_array($this->filter_value) ? $this->filter_value : array($this->filter_value);
			$filterList["html"] =  $this->getFilterInput($this->filterType."_fv", implode(",",$value), $this->node, "", true);
			$script = "try {JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:'".implode(",",$nullvalue) ."'});} catch (e) {}";
		}
		else {
			$filterList["html"] =  $this->getFilterInput($this->filterType."_fv", $this->filter_value, $this->node, "");
			$script = "try {JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:'".$this->filterNullValue ."'});} catch (e) {}";
		}

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	function getOptions()
	{
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option)
		{
			if ($option['archived'] || $option['archive']) {
				continue;
			}

			// Only add <option /> and <optgroup> elements.
			if ($option->getName() == 'optgroup')
			{

				if (strpos((string)$option['label'], ",")===false){
					$label = JText::alt(trim((string) $option['label']), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname));
				}
				else {
					$label = trim((string) $option['label']);
				}
				$tmp = JHtml::_(	'select.optgroup', $label, 'value', 'text');

				// Set some option attributes.
				$tmp->class = (string) $option['class'];

				// Set some JavaScript option attributes.
				$tmp->onclick = (string) $option['onclick'];

				// Add the option object to the result set.
				$options[] = $tmp;

				foreach ($option->children() as $ogoption)
				{
					if ($ogoption['archived'] || $ogoption['archive'] || $ogoption->getName() != 'option') {
						continue;
					}
					// Create a new option object based on the <option /> element.
					// Joomla translation splits strings with commas in them
					if (strpos((string)$ogoption, ",")===false){
						$tmp = JHtml::_(
							'select.option', (string) $ogoption['value'],
							JText::alt(trim((string) $ogoption), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text',
							((string) $ogoption['disabled'] == 'true')
						);
					}
					else {
						$tmp = JHtml::_(
							'select.option', (string) $ogoption['value'],
							trim((string) $ogoption), 'value', 'text',
							((string) $ogoption['disabled'] == 'true')
						);
					}
					// Set some option attributes.
					$tmp->class = (string) $ogoption['class'];

					// Set some JavaScript option attributes.
					$tmp->onclick = (string) $ogoption['onclick'];

					// Add the option object to the result set.
					$options[] = $tmp;
				}
				// close the optgroup
				$tmp = JHtml::_(	'select.optgroup', $label, 'value', 'text');
				// Add the option object to the result set.
				$options[] = $tmp;
				continue;
			}
			else if ($option->getName() != 'option')
			{
				continue;
			}
			else {

				// Create a new option object based on the <option /> element.
				// Joomla translation splits strings with commas in them
				if (strpos((string)$option, ",")===false){
					$tmp = JHtml::_(
						'select.option', (string) $option['value'],
						JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text',
						((string) $option['disabled'] == 'true')
					);
				}
				else {
					$tmp = JHtml::_(
						'select.option', (string) $option['value'],
						trim((string) $option), 'value', 'text',
						((string) $option['disabled'] == 'true')
					);
				}
			}
			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
        
	public function publicGetOptions(){
		return $this->getOptions();
	}
	
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
		echo 'onkeyup="jevcflist.updatePreview( \'' . $id . '\');" '; //onblur="jevcflist.updatePreview( \''.$id.'\');"';
		return "";
		echo 'onkeyup="jevcflist.showNext(this, \'' . $id . '\', ' . $op . ');" onblur="jevcflist.showNext(this, \'' . $id . '\', ' . $op . ');"';
	}

	public static function buttonActionOptgroup($id, $op)
	{
		echo 'onkeyup="jevcflist.updatePreview( \'' . $id . '\');jevcflist.updateCloseGroup( \'' . $id . '\',  \'' . $op . '\');" ';
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
					$option->checked = (int)$optionelem["default"];
					$option->archive = (int)$optionelem["archive"];
					// Is this is an OptGroup
					$option->isOptGroup = $optionelem->getname()=="optgroup";
					if ($option->isOptGroup && $optionelem->count()) {
						$lab = (string)$optionelem["label"];
						$option->options = array();
						// get optgroup sub-options
						JFormFieldJevcflist::getElementOptions($optionelem, $option->options, $maxvalue);
					}
					else {
						$lab = (string)$optionelem;
					}
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
			// Is this is an OptGroup
			$option->isOptGroup = false;
			$option->isBlankRow = false;
			$options[] = $option;
		}
		
	}

	static function generateOptionsPreview($options, $selectedValue, $id){
		foreach ($options as $option)
		{
			if ($option->label == "")
				continue;
			if ($option->archive == 1)
				continue;
			if ($option->isOptGroup){
				?>
				<optgroup label="<?php echo htmlspecialchars_decode($option->label); ?>">
					<?php
					JFormFieldJevcflist::generateOptionsPreview($option->options, $selectedValue, $id);
					?>
				</optgroup>
				<?php
			}
			else {
				$selected = "";
				if ($option->value ==$selectedValue || $option->value == "")
				{
					$selected = "selected='selected'";
				}
				?>
			<option value="<?php echo $option->value; ?>" <?php echo $selected; ?> ><?php echo htmlspecialchars_decode($option->label); ?></option>
				<?php
			}
		}
	}

	static function generateOptionRows($options, $selected, $id){
                
		static $currentgroup = array();
                if (!isset($currentgroup[$id])){
                    $currentgroup[$id]=0;
                }
		for ($op = 0; $op < count($options); $op++)
		{
			$option = $options[$op];
			$style = "";
			if ($op > 0 && $option->isBlankRow)
			{
				$nextgroup = $currentgroup[$id] +1;
				$style = "style='display:none;' class='blankrow".$currentgroup[$id]."_$op blankrow'";
			}

			if ($option->isOptGroup){
				$currentgroup[$id]++;
				if ($op > 0 && $option->isBlankRow)
				{
					$style = "style='display:none;' class='blankoptgroupstart blankoptgroupstart$currentgroup[$id]'";
				}

				?>
				<tr <?php echo $style; ?> >
					<td>
						<?php echo JText::_("JEVCF_START_OPTGROUP");?>
					</td>
					<td >
						<input type="text" class="inputlabel" name="options[<?php echo $id; ?>][label][]"  value="<?php echo $option->label; ?>" <?php JFormFieldJevcflist::buttonActionOptgroup($id, $currentgroup[$id]); ?> id="options<?php echo $id; ?>_og_<?php echo $currentgroup[$id]; ?>_<?php echo $op; ?>" />
						<input type="hidden" name="options[<?php echo $id; ?>][optgroup][]"  value="<?php echo $currentgroup[$id];?>" class="currentgroup currentgroup_<?php echo $currentgroup[$id];?>"/>
						<input type="hidden" name="options[<?php echo $id; ?>][value][]"  value="0" />
					</td>
					<td colspan="2" />
					<td>
						<input type="button" value="<?php echo JText::_("JEVCF_DELETE_OPTION") ?>" onclick="jevcflist.deleteOption(this);"/>
					</td>
				</tr>
				<?php
				JFormFieldJevcflist::generateOptionRows($option->options, $selected, $id);
				if ($op > 0 && $option->isBlankRow)
				{
					$style = "style='display:none;' class='blankoptgroupend  blankoptgroupend$currentgroup[$id]''";
				}
				else if (!$option->isBlankRow)
				{
					$style = " class='optgroupend_$currentgroup[$id]'";
				}
				?>
				<tr <?php echo $style; ?> >
					<td>
						<?php echo JText::_("JEVCF_END_OPTGROUP");?>
					</td>
					<td class="optgrouplabel" >
						<?php echo $option->label; ?>
						<input type="hidden" name="options[<?php echo $id; ?>][label][]"  value="" />
						<input type="hidden" name="options[<?php echo $id; ?>][optgroup][]"  value="<?php echo -$currentgroup[$id];?>" />
						<input type="hidden" name="options[<?php echo $id; ?>][value][]"  value="0" />
					</td>
					<td colspan="2" />
					<td>
						<input type="button" value="<?php echo JText::_("JEVCF_DELETE_OPTION") ?>" onclick="jevcflist.deleteOption(this);"/>
					</td>
				</tr>
				<?php
			}
			else {
				$checked = "";
				$archivechecked = "";
				// default value is not the correct value and not just an index!
				//if (($field && in_array($op, $field->attribute('default'))) || (!$field && $option->value == ""))
				if ($option->checked || $option->value == $selected)
				{
					$checked = "checked='checked'";
				}
				if ($option->archive)
				{
					$archivechecked = "checked='checked'";
				}
				?>
				<tr <?php echo $style; ?> >
					<td>
						<input type="text" class="inputlabel" name="options[<?php echo $id; ?>][label][]" id="options<?php echo $id; ?>_t_<?php echo $currentgroup[$id]; ?>_<?php echo $op; ?>" value="<?php echo $option->label; ?>" <?php JFormFieldJevcflist::buttonAction($id, $op); ?>/>
						<input type="hidden" name="options[<?php echo $id; ?>][optgroup][]"  value="<?php echo $currentgroup[$id];?>" />
					</td>
					<td>
						<input type="text" name="options[<?php echo $id; ?>][value][]" id="options<?php echo $id; ?>_v_<?php echo $currentgroup[$id]; ?>_<?php echo $op; ?>" value="<?php echo $option->value; ?>" <?php JFormFieldJevcflist::buttonAction($id, $op); ?> class="jevoption_value"/>
					</td>
					<td>
						<input type="radio" value="1" onclick="jevcflist.defaultOption(this, '<?php echo $id; ?>', '<?php echo $currentgroup[$id]."_".$op; ?>');"  name="default<?php echo $id; ?>" id="options<?php echo $id; ?>_d_<?php echo $op; ?>" <?php echo $checked; ?> />
					</td>
					<td>
						<input type="checkbox" name="options[<?php echo $id; ?>][archive][<?php echo $op; ?>]" id="archive<?php echo $id; ?>_r_<?php echo $currentgroup[$id]; ?>_<?php echo $op; ?>" value="1"   <?php echo $archivechecked; ?> onclick="jevcflist.updatePreview('<?php echo $id;?>' , '<?php echo $op;?>');" />
					</td>
					<td>
						<input type="button" value="<?php echo JText::_("JEVCF_DELETE_OPTION") ?>" onclick="jevcflist.deleteOption(this);jevcflist.updatePreview('<?php echo $id;?>');"/>
					</td>
				</tr>
				<?php
			}
		}
	}

}
