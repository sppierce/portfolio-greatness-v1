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

include_once("JevcfField.php");

class JFormFieldJevcfdblist extends JevcfField
{

	protected $node;
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'jevcfdblist';
	const name = 'jevcfdblist';

	public static function loadScript($field=false){
		//JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcfdblist.js');

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

	CustomFieldsHelper::textField($id, $field, JText::_("JEVCF_DBLIST_LABEL_FIELD"), JText::_('JEVCF_DBLIST_LABEL_FIELD_DESC'), "labelfield", "labelfield","User Name");
	CustomFieldsHelper::textField($id, $field, JText::_("JEVCF_DBLIST_TABLE_NAME"), JText::_('JEVCF_DBLIST_TABLE_NAME_DESC'), "tablename", "tablename","#__users");
	CustomFieldsHelper::textField($id, $field, JText::_("JEVCF_DBLIST_VALUE_FIELD"), JText::_('JEVCF_DBLIST_VALUE_FIELD_DESC'), "valuefield", "valuefield", "username");

	CustomFieldsHelper::allowoverride($id,  $field);
	//CustomFieldsHelper::hiddenValue($id, $field);
	//CustomFieldsHelper::searchable($id,  $field);
	CustomFieldsHelper::filterOptions($id, $field);
	CustomFieldsHelper::filtermenuOptions($id, $field);
        CustomFieldsHelper::filterDefault($id, $field);
        CustomFieldsHelper::multiFilter($id, $field);        
	CustomFieldsHelper::applicableCategories($id, $field) ;
	//CustomFieldsHelper::accessOptions($id,  $field);
	CustomFieldsHelper::readaccessOptions($id,  $field);
        CustomFieldsHelper::fieldclass($id, $field);
	CustomFieldsHelper::universal($id, $field);
	?>

	<div class="jevcfclear"></div>

</div>
<div class='jevcffieldpreview'  id='<?php echo $id;?>preview'>
	<div class="previewlabel"><?php echo JText::_("JEVCF_PREVIEW");?></div>
	<div class="jevcflabel jevcfpl" id='pl<?php echo $id;?>' ><?php echo $field?$field->attribute('label'):JText::_("JEVCF_FIELD_LABEL");?></div>	
</div>
<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id,  $field, $html, self::name	);

	}

	function getInput()
	{
		$name = $this->name;
		$value =  $this->value;
		$class = ( $this->element['class'] ? 'class="'.$this->element['class'].'"' : 'class="inputbox"' );

		$db = JFactory::getDbo();
		$tablename = $this->attribute('tablename',0);
		$valuefield = $this->attribute('valuefield',0);
		$labelfield = $this->attribute('labelfield',0);
		if (!$valuefield || !$labelfield || !$tablename) {
			return "<strong>Invalid attributes - please specify tablename, valuefield and labelfield</strong>";
		}
		$db->setQuery("Select $labelfield as text, $valuefield as value FROM $tablename");
		$data = $db->loadObjectList();
		$options = array();
		$options[] = JHTML::_('select.option', 0, " -- ");
		foreach ($data as $option)
		{
			$options[] = JHTML::_('select.option', $option->value, JText::_($option->text));
		}

		if ($this->attribute('multifilter',0)==1){
			if ($value != ""){
				$value = explode(",",$value);
				JArrayHelper::toInteger($value);
			}
			else {
				$value = array();
			}

			$size =  ( $this->attribute('filtersize') ? ' size="'.$this->attribute('filtersize').'"' : '' );
			$multiple = ' multiple="multiple"';
			return JHTML::_('select.genericlist', $options, $this->name, $class.$size.$multiple, 'value', 'text', $value, $this->id);
		}
		else {
			return JHTML::_('select.genericlist', $options, $this->name, $class, 'value', 'text', $value, $this->id);
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
			$db = JFactory::getDbo();
			$tablename = $this->attribute('tablename',0);
			$valuefield = $this->attribute('valuefield',0);
			$labelfield = $this->attribute('labelfield',0);
			if (!$valuefield || !$labelfield || !$tablename) {
				return "<strong>Invalid attributed - please specify tablename, valuefield and labelfield</strong>";
			}
			$db->setQuery("Select $labelfield as text, $valuefield as value FROM $tablename");
			$data = $db->loadObjectList();

			foreach ($data as $option)
			{
				$val	= $option->value;
				$text	= JText::_($option->text);
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

	public function constructFilter($node){
		$this->node = $node;
		$specialCharacters = array(" ", "-" , ",", ".");
		$this->filterType = str_replace($specialCharacters,"",$this->attribute("name"));
		$this->filterLabel = JevcfField::varempty($this->attribute("filterlabel"))?$this->attribute("label"):$this->attribute("filterlabel");
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
		$useCache = (int) $cfg->get('com_cache', 0);
		$user = JFactory::getUser();
		$mainframe = JFactory::getApplication();

		if ($this->attribute("multifilter")==1){
			$this->filterNullValue = array($this->filterNullValue);
			if ((int) JRequest::getVar('filter_reset',0)){
				JFactory::getApplication()->setUserState( $this->filterType.'_fv_ses', $this->filterNullValue );
			$this->filter_value = $this->filterNullValue;
			}
			// ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
			else if (!$this->visible)
			{
				$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue,"request", "array" );
				JArrayHelper::toInteger($this->filter_value);
			}
			else {
				$this->filter_value = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue );
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
				$bits[] = " $this->map.value RLIKE ".$db->Quote(",*".$fv.",*");
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
			if (count($this->filter_value)==0) return "";
		}
		else {
			if (isset($this->filter_value) && trim($this->filter_value)==$this->filterNullValue) return "";
		}
		return "$this->map.id IS NOT NULL";		
	}

	public function createFilterHTML(){
		$filterList=array();
		$filterList["title"]="<label class='evdblist_label' for='".$this->filterType."_fv'>".$this->filterLabel."</label>";
		if ($this->attribute("multifilter")==1){
			// allow multiple select!
			$filterList["html"] =  $this->getFilterInput($this->filterType."_fv", implode(",",$this->filter_value), $this->node, "", true);
			$script = "try {JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:".$this->filterNullValue[0] ."});} catch (e) {}";
		}
		else {
			$this->name = $this->filterType."_fv";
			$this->value = $this->filter_value;
			$filterList["html"] =  $this->getInput();
			$script = "try {JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:".$this->filterNullValue ."});} catch (e) {}";
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

	public function bindField($fieldid)	{
		include_once("JevcfField.php");
		$myXml  = JevcfField::bindFieldWithVarkeys($fieldid, get_object_vars($this));

                $input = JFactory::getApplication()->input;
		$filter = JFilterInput::getInstance();
                
		JevcfField::bindString($myXml, $fieldid, "tablename", "tablename");
		JevcfField::bindString($myXml, $fieldid, "valuefield", "valuefield");
		JevcfField::bindString($myXml, $fieldid, "labelfield", "labelfield");
                
                return $myXml;

	}

}
