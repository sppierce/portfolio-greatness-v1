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

class JFormFieldJevcftext extends JevcfFieldText
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'ievcftext';
	const name = 'jevcftext';

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
        $defaultvalue = $field?$field->attribute('default'):"";
        
	?>

	<div class="jevcflabel">
		<?php echo JText::_("JEVCF_DEFAULT_VALUE");?>
		<?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_DEFAULT_VALUE_DESC'));?>
	</div>
	<div class="jevcfinputs">
		<input type="text" name="dv[<?php echo $id;?>]" id="dv<?php echo $id;?>" size="<?php echo $field?$field->attribute('size'):5;?>"  value="<?php echo $defaultvalue;?>"  onchange="jevcftext.setvalue('<?php echo $id;?>');"  onkeyup="jevcftext.setvalue('<?php echo $id;?>');"/>
	</div>
	<div class="jevcfclear"></div>

	<?php
        CustomFieldsHelper::textField($id, $field, JText::_("JEVCF_PLACEHOLDER"), JText::_('JEVCF_PLACEHOLDER_DESC'), "placeholder", "placeholder","", ' onchange="jevcftext.setplaceholder(\''. $id .'\');"  onkeyup="jevcftext.setplaceholder(\''. $id .'\');"');
		CustomFieldsHelper::scramble($id,  $field, self::name);
		CustomFieldsHelper::size($id,  $field, self::name);
		CustomFieldsHelper::maxlength($id,  $field, self::name);
		CustomFieldsHelper::required($id,  $field);
		CustomFieldsHelper::requiredMessage($id,  $field);
		CustomFieldsHelper::conditional($id,  $field);
		CustomFieldsHelper::allowoverride($id,  $field);
		CustomFieldsHelper::hiddenvalue($id, $field);
		CustomFieldsHelper::searchable($id,  $field);
		CustomFieldsHelper::filterOptions($id, $field);
		CustomFieldsHelper::filtermenuOptions($id, $field);
		CustomFieldsHelper::filterDefault($id, $field);
		CustomFieldsHelper::applicableCategories($id, $field) ;
		CustomFieldsHelper::accessOptions($id,  $field);
		CustomFieldsHelper::readaccessOptions($id,  $field);
        CustomFieldsHelper::fieldclass($id, $field);
        
        CustomFieldsHelper::booleanField($field,"metakey[$id]", "metakey0$id", "metakey1$id", JText::_("JEVCF_META_KEY"), "metakey", JText::_("JEVCF_META_KEY_DESC"));
        CustomFieldsHelper::booleanField($field,"metadescription[$id]", "metadescription0$id", "metadescription1$id", JText::_("JEVCF_META_DESCRIPTION"), "metakey", JText::_("JEVCF_META_DESCRIPTION_DESC"));
        CustomFieldsHelper::universal($id, $field);
        $label = $field?$field->attribute('label'):JText::_("JEVCF_FIELD_LABEL");
        $default = $field?$field->attribute('default'):"";
        $placeholder = $field?$field->attribute('placeholder'):""
        ?>

	<div class="jevcfclear"></div>

</div>
<div class='jevcffieldpreview'  id='<?php echo $id;?>preview'>
	<div class="previewlabel"><?php echo JText::_("JEVCF_PREVIEW");?></div>
	<div class="jevcflabel jevcfpl" id='pl<?php echo $id;?>' ><?php echo $label;?></div>
	<input type="text"  id="pdv<?php echo $id;?>" value="<?php echo $default;?>" placeholder="<?php echo $placeholder;?>" size="<?php echo $field?$field->attribute('size'):5;?>"  />
</div>
<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id,  $field, $html, self::name	);

	}


	function getFilterInput($name, $value, &$node, $control_name)
	{

		$size = ( $this->attribute('size') ? 'size="'.$this->attribute('size').'"' : '' );
		$maxlength = ( $this->attribute('maxlength') ? 'maxlength="'.$this->attribute('maxlength').'"' : '' );
		$class = ( $this->element['class'] ? 'class="'.$this->element['class'].'"' : 'class="text_area"' );
		if (!is_string($value) && !is_numeric($value)) $value= $this->attribute('default');

		$placeholder = ( $this->attribute('placeholder') ? ' placeholder="'.JText::_($this->attribute('placeholder')).'"' : '' );

		/*
		* Required to avoid a cycle of encoding &
		* html_entity_decode was used in place of htmlspecialchars_decode because
		* htmlspecialchars_decode is not compatible with PHP 4
		*/
		$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return '<input type="text" name="'.$control_name.$name.'" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' '.$maxlength.' '.$placeholder.' />';
	}

	function getInput() {
		
		// Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

                // Should the default value be translated
                if (is_callable(array($this->event, "ev_id")) && $this->event->ev_id()==0 && $this->value == $this->attribute('default') && strpos($this->value, "_")>0 && strtoupper($this->value)==$this->value)
                {
                    $transval = JText::_($this->value);
                    if ($transval != $this->value) {
                        $this->value = $transval;
                    }
                }

                // Should the default value be translated
                if (is_callable(array($this->event, "ev_id")) && $this->event->ev_id()==0 && $this->attribute('placeholder')!="" && strpos($this->attribute('placeholder'), "_")>0 && strtoupper($this->attribute('placeholder'))==$this->attribute('placeholder'))
                {
                    $transval = JText::_($this->attribute('placeholder'));
                    if ($transval != $this->attribute('placeholder')) {
                        $this->addAttribute('placeholder', $transval);
                    }
                }
                
                $placeholder = ( $this->attribute('placeholder') ? ' placeholder="'.$this->attribute('placeholder').'"' : '' );

		return '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . $placeholder. '/>';

	}
	
	function fetchRequiredScript($name, &$node, $control_name)
	{
		return "JevStdRequiredFields.fields.push({'name':'".$control_name.$name."', 'default' :'".$this->attribute('default') ."' ,'reqmsg':'".trim(JText::_($this->attribute('requiredmessage'),true))."'}); ";
	}

	public function convertValue($value, $node)
	{
		if ($this->attribute("metakeywords") && $value != ""){
			$document = JFactory::getDocument();
			$document->setMetaData('keywords', $value);
			return "";
		}
		if ($this->attribute("scramble") && $value != ""){
			// do the scrambling and output as a bit of javascript here
			return JFormFieldJevcftext::safe_text($value);
		}
		return $value;
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
		if ((int) JRequest::getVar('filter_reset',0)){
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
		
		//$this->filter_value = JRequest::getString($this->filterType.'_fv', $this->filterNullValue );
		
	}
		
	public function createJoinFilter(){
		if (is_string($this->filter_value) && trim($this->filter_value)==$this->filterNullValue) return "";
		$join =  "#__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
		if ($this->element->attributes()->fullsearch)
		{
			$allowfullsearch = "%";
		}
		else {
			$allowfullsearch = "";
		}
		$db = JFactory::getDBO();
		$filter = "$this->map.name=".$db->Quote($this->attribute('name'))." AND $this->map.value LIKE (".$db->Quote($allowfullsearch.$this->filter_value."%").")";
		return $join . " AND ". $filter;
	}
	
	public function createFilter(){
		if (is_string($this->filter_value) && trim($this->filter_value)==$this->filterNullValue) return "";
		return "$this->map.id IS NOT NULL";
	}

	public function setSearchKeywords( &$extrajoin ){
		if ( $this->attribute('searchable')){
			$db = JFactory::getDBO();
			if (strpos($extrajoin, " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id")===false){
				$extrajoin .= "\nLEFT JOIN #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id AND $this->map.name=".$db->Quote($this->attribute('name'));
			}
			if ($this->element->attributes()->fullsearch){			
				return "$this->map.value RLIKE (".$db->Quote('###').")";
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

	/**
	 * Magic setter; allows us to set protected values
	 * @param string $name
	 * @return nothing
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	public static function safe_text($text)
	{
		if (mb_detect_encoding($text, 'UTF-8', true))
			$text = utf8_decode($text);

		$ret = '
<script type="text/javascript">// <![CDATA[
                    var t=[
                ';
		$chars = str_split($text);

		$enc[] = rand(0, 255);

		foreach ($chars as $char)
		{
			$enc[] = ord($char) - $enc[sizeof($enc) - 1];
		}

		$ret .= join(',', $enc);
		$ret .= '
                ];
		for (var i=1; i<t.length; i++) { document.write(String.fromCharCode(t[i]+t[i-1])); }
// ]]></script>';

		return $ret;

	}

}
