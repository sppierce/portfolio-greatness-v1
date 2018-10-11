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
include_once ("jevcflist.php");

class JFormFieldJevcfmultilist extends JFormFieldJevcflist
{

	const name = 'jevcfmultilist';

	public static function loadScript($field=false)
	{
		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcfmultilist.js');

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
				JFormFieldJevcfmultilist::getElementOptions($field?$field->element:null, $options, $maxvalue);

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

				?>
				<input type="button" value="<?php echo JText::_("JEVCF_NEW_OPTION") ?>" onclick="jevcfmultilist.newOption('<?php echo $id; ?>');"/>
				<table id="options<?php echo $id; ?>" class="jevcfoptions">
					<tr >
						<th><?php echo JText::_("JEVCF_OPTION_TEXT") ?></th>
						<th><?php echo JText::_("JEVCF_OPTION_VALUE") ?></th>
						<th><?php echo JText::_("JEVCF_DEFAULT_VALUE") ?></th>
						<th><?php echo JText::_("JEVCF_ARCHIVED_OPTION") ?></th>
						<th/>
					</tr>
					<?php
					JFormFieldJevcfmultilist::generateOptionRows($options, $field?$field->attribute('default'):"", $id);
					?>
				</table>

			</div>
			<div class="jevcfclear"></div>

			<?php
                        CustomFieldsHelper::size($id, $field, self::name);                        
			CustomFieldsHelper::required($id, $field);
			CustomFieldsHelper::requiredMessage($id, $field);
                        
			CustomFieldsHelper::filterOptions($id, $field);
			CustomFieldsHelper::filtermenuOptions($id, $field);
                	CustomFieldsHelper::filterDefault($id, $field);                        
                        CustomFieldsHelper::multiFilter($id, $field);
                        CustomFieldsHelper::filterSize($id, $field);

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
			<select name="pdv[<?php echo $id; ?>]" id="pdv<?php echo $id; ?>" multiple="multiple" size="<?php echo ($field && $field->attribute('size')>0)?$field->attribute('size'):3;?> ">
				<?php
				JFormFieldJevcfmultilist::generateOptionsPreview($options, $field?$field->attribute('default'):"", $id);
				?>
			</select>

		</div>
		<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id, $field, $html, self::name);

	}
    
        public function getFilterInput($name, $value, &$node, $control_name, $allowmultiple = true)
	{
		if ($value != ""){
			$value = explode(",",$value);
			JArrayHelper::toInteger($value);
		}
		else {
			$value = array();
		}
		$class = ( $this->element['class'] ? 'class="'.$this->element['class'].'"' : 'class="inputbox"' );
		$size =  ( $this->attribute('size') ? ' size="'.$this->attribute('size').'"' : '' );

		if ($size=="" && isset($this->filtersize)){
			$size = ' size="'.$this->filtersize.'"';
		}

		$multiple = "";
		if ($this->attribute('size')>0 || ($this->attribute('size')=="" && isset($this->filtersize) && $this->filtersize>1) ){
			$multiple = ' multiple="multiple"';
		}
		if ($this->attribute("multifilter")==-1){
			$multiple =  "";
		}

		$options = array ();
		foreach ($this->element->children() as $option)
		{
			if ((int) $option['archive']) continue;
			$val	= (string) $option["value"];
			$text	= (string) $option;
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}

		if ($multiple) {
			return JHTML::_('select.genericlist',  $options, ''.$control_name.$name."[]", $class.$size.$multiple, 'value', 'text', $value, $control_name.$name);
		}
		else {
			return JHTML::_('select.genericlist',  $options, ''.$control_name.$name, $class.$size.$multiple, 'value', 'text', $value, $control_name.$name);
		}
	}

	public function fetchRequiredScript($name, &$node, $control_name)
	{
		return "JevStdRequiredFields.fields.push({'name':'".$control_name.$name."', 'default' :'".$this->attribute('default') ."' ,'reqmsg':'".trim(JText::_($this->attribute('requiredmessage'),true))."'}); ";
	}

	public function getInput() {
		// make sure we have a helpful class set to get the width
		if (!$this->element['class'] ){
			$this->element['class'] =" jevminwidth";
		}
		$this->multiple=true;
		$this->name .= "[]";
		$this->value = is_string($this->value)? explode(",",$this->value ) : $this->value ;
		return parent::getInput();
	}

	public function convertValue($value, $node){
		 if ($value =="") return;
		 $value = explode(",",$value);
		 JArrayHelper::toInteger($value);

		 static $values;
		 if (!isset($values)){
			$values =  array();
		 }
		$hiddenvalue = $this->attribute('hiddenvalue');
		 if (!isset($values[$this->attribute('name')])){
			$values[$this->attribute('name')]=array();
			foreach ($this->element->children() as $option)
			{
				$val   = (string) $option['value'];
				$text   = (string) $option;
				if ($text == $this->attribute('hiddenvalue')){
					$hiddenvalue = $val;
				}
                                if (JText::_($text)!=$text){
                                        $text = JText::_($text);
                                }

				$values[$this->attribute('name')][$val] = $text;
			}
		 }

		 $output = array();
		 foreach ($value as $v) {
			if ($v==$hiddenvalue) continue;
			if (array_key_exists($v,$values[$this->attribute('name')])) $output[] = $values[$this->attribute('name')][$v];
		 }

		 return implode(", ",$output);
	  }

	public function constructFilter($node){
		$this->node = $node;
		$specialCharacters = array(" ", "-" , ",", ".");
		$this->filterType = str_replace($specialCharacters,"",$this->attribute("name"));
		$this->filterLabel = JevcfField::varempty($this->attribute("filterlabel"))?$this->attribute("label"):$this->attribute("filterlabel");
		$this->filterNullValue = JevcfField::varempty($this->attribute("filterdefault"))?(JevcfField::varempty($this->attribute("default"))?"":$this->attribute("default")):$this->attribute("filterdefault");
		$this->filterNullValue = array($this->filterNullValue);
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

		if ((int) JRequest::getVar('filter_reset',0)){
			JFactory::getApplication()->setUserState( $this->filterType.'_fv_ses', $this->filterNullValue );
			$this->filter_value = $this->filterNullValue;
		}
		// ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
		else if (!$this->visible)
		{
			$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue,"request", "array" );
			if (!is_array($this->filter_value)){
				$this->filter_value = array($this->filter_value);
			}
			JArrayHelper::toInteger($this->filter_value);
		}
		else {
			$this->filter_value = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );
			if (!is_array($this->filter_value)){
				$this->filter_value = array($this->filter_value);
			}
			JArrayHelper::toInteger($this->filter_value);
		}

		/*
		$this->filter_value = JRequest::getVar($this->filterType.'_fv', $this->filterNullValue ,"request", "array");
		JArrayHelper::toInteger($this->filter_value);
		*/
	}

	public function createJoinFilter(){
		if ($this->filter_value==$this->filterNullValue) return "";
		if (count($this->filter_value)==0) return "";
		$join =  " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
		$db = JFactory::getDBO();
		// This could be done as a single regexp - would it be faster?
		$filter =  "$this->map.name=".$db->Quote($this->attribute('name')). " AND ( ";
		$bits = array();
		foreach ($this->filter_value as $fv) {
			$bits[] = " $this->map.value RLIKE ".$db->Quote("[[:<:]]".$fv."[[:>:]]");
		}
		$filter .= implode(" OR ",$bits);
		$filter .= ")";
		$join .= " AND " . $filter;
		return $join;
	}

	public function createFilter(){
		if ($this->filter_value==$this->filterNullValue) return "";
		if (count($this->filter_value)==0) return "";
		return "$this->map.id IS NOT NULL";
	}

	public function createFilterHTML(){
		if ($this->element->attributes()->filtersize){
			$this->element->attributes()->size = (string) ( $this->element->attributes()->filtersize ? $this->element->attributes()->filtersize  : $this->element->attributes()->size);
			$this->filtersize = (string) ( $this->element->attributes()->filtersize ? $this->element->attributes()->filtersize  : $this->element->attributes()->size);
		}
		$filterList=array();
		$filterList["title"]="<label class='evdate_label' for='".$this->filterType."_fv'>".JText::_($this->filterLabel)."</label>";
		$filterList["html"] =  $this->getFilterInput($this->filterType."_fv", implode(",",$this->filter_value), $this->node, "");

		$script = "try{JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:'".$this->filterNullValue[0] ."'});} catch (e) {}";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;
	}

	public function getOptions(){
		return parent::getOptions();
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

	public static function generateOptionRows($options, $selected, $id){

		$opt_cnt = count($options);

		for ($op = 0; $op < $opt_cnt; $op++)
		{
			$option = $options[$op];
			$style = "";
			if ($op > 0 && $option->isBlankRow)
			{
				$style = "style='display:none;' class='blankrow_$op blankrow'";
			}

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
                                        <input type="text" class="inputlabel" name="options[<?php echo $id; ?>][label][]" id="options<?php echo $id; ?>_t_<?php echo $op; ?>" value="<?php echo $option->label; ?>" <?php JFormFieldJevcfmultilist::buttonAction($id, $op); ?>/>
                                </td>
                                <td>
                                        <input type="text" name="options[<?php echo $id; ?>][value][]" id="options<?php echo $id; ?>_v_<?php echo $op; ?>" value="<?php echo $option->value; ?>" <?php JFormFieldJevcfmultilist::buttonAction($id, $op); ?> class="jevoption_value"/>
                                </td>
                                <td>
                                        <input type="checkbox" value="1" onclick="jevcfmultilist.defaultOption(this, '<?php echo $id; ?>', '<?php echo $op; ?>');"  name="default<?php echo $id; ?>" id="options<?php echo $id; ?>_d_<?php echo $op; ?>" <?php echo $checked; ?> />
                                </td>
                                <td>
                                        <input type="checkbox" name="options[<?php echo $id; ?>][archive][<?php echo $op; ?>]" id="archive<?php echo $id; ?>_r_<?php echo $op; ?>" value="1"   <?php echo $archivechecked; ?> onclick="jevcfmultilist.updatePreview('<?php echo $id;?>' , '<?php echo $op;?>');" />
                                </td>
                                <td>
                                        <input type="button" value="<?php echo JText::_("JEVCF_DELETE_OPTION") ?>" onclick="jevcfmultilist.deleteOption(this);jevcfmultilist.updatePreview('<?php echo $id;?>')"/>
                                </td>
                        </tr>
                        <?php
			
		}
	}
 
	public static function generateOptionsPreview($options, $selectedValue, $id){
		foreach ($options as $option)
		{
			if ($option->label == "")
				continue;
			if ($option->archive == 1)
				continue;
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
 
	public static function buttonAction($id, $op)
	{
		echo 'onkeyup="jevcfmultilist.updatePreview( \'' . $id . '\');" '; //onblur="jevcfmultilist.updatePreview( \''.$id.'\');"';
		return "";
		echo 'onkeyup="jevcfmultilist.showNext(this, \'' . $id . '\', ' . $op . ');" onblur="jevcfmultilist.showNext(this, \'' . $id . '\', ' . $op . ');"';
	}

	public static function buttonActionOptgroup($id, $op)
	{
		echo 'onkeyup="jevcfmultilist.updatePreview( \'' . $id . '\');jevcfmultilist.updateCloseGroup( \'' . $id . '\',  \'' . $op . '\');" ';
	}

        
}
