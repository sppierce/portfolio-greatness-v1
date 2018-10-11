<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevtextarea.php 1569 2009-09-16 06:22:03Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('textarea');

class JFormFieldJevcftextarea extends JFormFieldTextarea
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevcftextarea';
	const name = 'jevcftextarea';

	public static function loadScript($field=false){
		JHtml::script( 'plugins/jevents/jevcustomfields/customfields/js/jevcftextarea.js' );

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

	<div class="jevcflabel"><?php echo JText::_("JEVCF_DEFAULT_VALUE");?></div>
	<div class="jevcfinputs">
		<textarea name="dv[<?php echo $id;?>]" id="dv<?php echo $id;?>" onchange="jevcftextarea.setvalue('<?php echo $id;?>');" onkeyup="jevcftextarea.setvalue('<?php echo $id;?>');"
				rows="<?php echo $field?$field->attribute('rows'):5;?>" cols="<?php echo $field?$field->attribute('cols'):20;?>" ><?php echo $field?$field->attribute('default'):"";?></textarea>
	</div>
	<div class="jevcfclear"></div>

	<?php
        CustomFieldsHelper::booleanField($field,"allowhtml[$id]", "allowhtml0$id", "allowhtml2$id", JText::_("JEVCF_ALLOW_HTML_CONTENT"), "allowhtml", JText::_("JEVCF_ALLOW_HTML_CONTENT_DESC"),"","", 0);
        CustomFieldsHelper::booleanField($field,"allowraw[$id]", "allowraw", "allowraw2$id", JText::_("JEVCF_ALLOW_UNFILTERED_HTML_CONTENT"), "allowraw", JText::_("JEVCF_ALLOW_UNFILTERED_HTML_CONTENT_DESC"),"","", 0);
	CustomFieldsHelper::cols($id,  $field, self::name);
	CustomFieldsHelper::rows($id,  $field, self::name);
	CustomFieldsHelper::required($id,  $field);
	CustomFieldsHelper::requiredMessage($id,  $field);
	CustomFieldsHelper::searchable($id,  $field);
	CustomFieldsHelper::filterOptions($id, $field);
	CustomFieldsHelper::filtermenuOptions($id, $field);
        CustomFieldsHelper::filterDefault($id, $field);        
	CustomFieldsHelper::conditional($id,  $field);
	CustomFieldsHelper::allowoverride($id,  $field);
	CustomFieldsHelper::accessOptions($id,  $field);
        CustomFieldsHelper::readaccessOptions($id,  $field);
	CustomFieldsHelper::applicableCategories($id, $field) ;
        CustomFieldsHelper::fieldclass($id, $field);
	CustomFieldsHelper::universal($id, $field);
	CustomFieldsHelper::inputConditional($id, $field);
	?>

	<div class="jevcfclear"></div>

</div>
<div class='jevcffieldpreview'  id='<?php echo $id;?>preview'>
	<div class="previewlabel"><?php echo JText::_("JEVCF_PREVIEW");?></div>
	<div class="jevcflabel jevcfpl" id='pl<?php echo $id;?>'><?php echo $field?$field->attribute('label'):JText::_("JEVCF_FIELD_LABEL");?></div>
	<textarea id="pdv<?php echo $id;?>"
				rows="<?php echo $field?$field->attribute('rows'):5;?>" cols="<?php echo $field?$field->attribute('cols'):20;?>" ><?php echo $field?$field->attribute('default'):"";?></textarea>
</div>
<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id,  $field, $html, self::name	);

	}
	       
	function getFilterInput($name, $value, &$node, $control_name)
	{
		$size = ( $this->attribute('size') ? 'size="'.$this->attribute('size').'"' : '' );
		$class = ( $this->element['class'] ? 'class="'.$this->element['class'].'"' : 'class="text_area"' );
		/*
		* Required to avoid a cycle of encoding &
		* html_entity_decode was used in place of htmlspecialchars_decode because
		* htmlspecialchars_decode is not compatible with PHP 4
		*/
		$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '<input type="text" name="'.$control_name.$name.'" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' />';

	}
	        
	
	function fetchRequiredScript($name, &$node, $control_name) 
	{
		return "JevStdRequiredFields.fields.push({'name':'".$control_name.$name."', 'default' :'".$this->attribute('default') ."' ,'reqmsg':'".trim(JText::_($this->attribute('requiredmessage'),true))."'}); ";
	}

	public function convertValue($value, $node)
	{
		if ($this->attribute("metadescription") && $value != ""){
			$document = JFactory::getDocument();
			$document->setDescription($value);
			return "";
		}
                if ($this->attribute("icvar")){
                    $icvar =  $this->attribute("icvar");
                    $icval =  $this->attribute("icval");
                    if (JFactory::getApplication()->input->getVar($icvar, "@$%FGH$%Y") != $icval){
                        return "don't show";
                    }
                }
		// remove hard coded \n in the text
		return str_replace('\n',"",$value);
	}
	
	public function constructFilter($node){
		$this->node = $node;
		$specialCharacters = array(" ", "-" , ",", ".");
		$this->filterType = str_replace($specialCharacters,"",$this->attribute("name"));
		$this->filterLabel = JevcfField::varempty($this->attribute("filterlabel"))?$this->attribute("label"):$this->attribute("filterlabel");
		//$this->filterNullValue = JevcfField::varempty($this->attribute("filterdefault"))?(JevcfField::varempty($this->attribute("default"))?"":$this->attribute("default")):$this->attribute("filterdefault");
                $this->filterNullValue = $this->attribute("filterdefault");
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
		if ((int) JRequest::getVar('filter_reset',0)) {
			JFactory::getApplication()->setUserState( $this->filterType.'_fv_ses', $this->filterNullValue );
			$this->filter_value = $this->filterNullValue;
		}
		// ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
		else if (!$this->visible)
		{
			$this->filter_value =  JRequest::getVar($this->filterType.'_fv', $this->filterNullValue,"request", "string" );
		}
		else {
			$this->filter_value = JFactory::getApplication()->getUserStateFromRequest( $this->filterType.'_fv_ses', $this->filterType.'_fv', $this->filterNullValue , "string");
		}
		
	}
		
	public function createJoinFilter(){
		if (trim($this->filter_value)==$this->filterNullValue) return "";
		$join =  "#__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
		if ($this->element->attributes()->fullsearch)
		{
			$allowfullsearch = "%";
		}
		else {
			$allowfullsearch = "";
		}
		$db = JFactory::getDBO();
		$filter =  "$this->map.name=".$db->Quote($this->attribute('name'))." AND $this->map.value LIKE (".$db->Quote($allowfullsearch.$this->filter_value."%").")";
		
		return $join . " AND ". $filter;
	}
	
	public function createFilter(){
		if (trim($this->filter_value)==$this->filterNullValue) return "";
		return "$this->map.id IS NOT NULL";
	}
	
	public function setSearchKeywords( &$extrajoin ){
		if ( $this->attribute('searchable')){
			$db = JFactory::getDBO();
			if (strpos($extrajoin, " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id")===false){
				$extrajoin .= "\nLEFT JOIN #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id AND $this->map.name=".$db->Quote($this->attribute('name'));
			}
			if ($this->element->attributes()->fullsearch){			
				return "$this->map.value LIKE (".$db->Quote("%".'###'."%").")";
			}
			else {
				return "$this->map.value LIKE (".$db->Quote('###'."%").")";
			}
		}
	}
	

	public function createFilterHTML(){
		$filterList=array();
		$filterList["title"]="<label class='evdate_label' for='".$this->filterType."_fv'>".JText::_($this->filterLabel)."</label>";
		$filterList["html"] =  $this->getFilterInput($this->filterType."_fv", $this->filter_value, $this->node, "");
		
		$script = "try {JeventsFilters.filters.push({id:'".$this->filterType."_fv',value:'".addslashes($this->filterNullValue)."'});} catch (e) {}";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
		
		return $filterList;
	}

	public function attribute($attr){
		$val = $this->element->attributes()->$attr;
		$val = !is_null($val)?(string)$val:null;
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

	public function toXML($field){
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

	public function getInput() {
            // Should the default value be translated
            if (is_callable(array($this->event, "ev_id")) && $this->event->ev_id()==0 && $this->value == $this->attribute('default') && strpos($this->value, "_")>0 && strtoupper($this->value)==$this->value)
            {
                $transval = JText::_($this->value);
                if ($transval != $this->value) {
                    $this->value = $transval;
                }
            }
            return parent::getInput();
	}
        
        public function addAttribute($name, $value)
	{
		// Add the attribute to the element, override if it already exists
		$this->element->attributes()->$name = $value;
	}

}
