<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
include_once('jevcftextarea.php');

class JFormFieldJevcfnotes extends JFormFieldJevcftextarea 
{
    
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var   $_name = 'Jevcfnotes';
	const name = 'jevcfnotes';

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
      	CustomFieldsHelper::usergroups($id,  $field,false, JText::_("JEVCF_CREATOR_NOTES_APPLICABLE_USERGROUPS_DESC"));
        // Would be too many entries on many sites.
        //CustomFieldsHelper::userids($id,  $field,JText::_("JEVCF_CREATOR_NOTES_VALID_IDS"), JText::_("JEVCF_CREATOR_NOTES_VALID_IDS_DESC"));
	CustomFieldsHelper::textField($id, $field, JText::_("JEVCF_CREATOR_NOTES_VALID_IDS"), JText::_('JEVCF_CREATOR_NOTES_VALID_IDS_DESC'), "userid", "userid");
        
	CustomFieldsHelper::accessOptions($id,  $field);
        CustomFieldsHelper::readaccessOptions($id,  $field);
	CustomFieldsHelper::applicableCategories($id, $field) ;
        CustomFieldsHelper::fieldclass($id, $field);
        CustomFieldsHelper::universal($id, $field);
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
    
	public function getInput(){
		
		$this->_validuser = explode(",",$this->attribute('userid'));
		
		$user = JFactory::getUser();
		if (!in_array($user->id, $this->_validuser)) {
			$this->element->attributes()->label = "";
			// set access to something impossible too!
			$this->element->attributes()->access = -999;
			return "";
		}
		
		$this->value = str_replace('<br />', "\n", $this->value);
		return parent::getInput();		
	}
	
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var $_validuser = 0;
		
	public function convertValue($value, $node){
		$this->_validuser = explode(",",$this->attribute('userid'));
		
		$user = JFactory::getUser();
		if (count($this->_validuser)>0 && !in_array($user->id, $this->_validuser)) {
			$this->element->attributes()->label = "";
			// set access to something impossible too!
			$this->element->attributes()->access = -999;
			return "";
		}

		$this->_validgroups = explode(",",$this->attribute('usergoups'));
        	$userGroups = $user->getAuthorisedGroups();
		if (count($this->_validgroups) > 0 && !JEVHelper::isAdminUser($user) && array_intersect($this->_validgroups, array_values($userGroups)) && !JEVHelper::canEditEvent($repeat))
		{
			$this->element->attributes()->label = "";
			// set access to something impossible too!
			$this->element->attributes()->access = -999;
			return "";
                }                
                
		return $value;
	}
	
	function fetchRequiredScript($name, &$node, $control_name) 
	{
		$this->_validuser = explode(",",$this->attribute('userid'));
		$this->_validgroups = explode(",",$this->attribute('usergroups'));
		
		$user = JFactory::getUser();
		if (count($this->_validuser)>0 && !in_array($user->id, $this->_validuser)) {
                    return "";  
                }
		$this->_validgroups = explode(",",$this->attribute('usergoups'));
        	$userGroups = $user->getAuthorisedGroups();
		if (count($this->_validgroups) > 0 && !JEVHelper::isAdminUser($user) && array_intersect($this->_validgroups, array_values($userGroups)) && !JEVHelper::canEditEvent($repeat))
		{
                       return "";
                }
				
		return "JevStdRequiredFields.fields.push({'name':'".$control_name.$name."', 'default' :'".$this->attribute('default') ."' ,'reqmsg':'".trim(JText::_($this->attribute('requiredmessage'),true))."'}); ";
	}

	public function constructFilter($node){

		$user = JFactory::getUser();
		$this->_validuser = explode(",",$this->attribute('userid'));
                
		if (count($this->_validuser)>0 && !in_array($user->id, $this->_validuser)) {
                    return "";
                }
		
		$this->_validgroups = explode(",",$this->attribute('usergoups'));
        	$userGroups = $user->getAuthorisedGroups();
		if (count($this->_validgroups) > 0 && !JEVHelper::isAdminUser($user) && array_intersect($this->_validgroups, array_values($userGroups)) && !JEVHelper::canEditEvent($repeat))
		{
                    return "";
                }                
                
		$this->node = $node;
		$specialCharacters = array(" ", "-" , ",", ".");
		$this->filterType = str_replace($specialCharacters,"",$this->attribute("name"));
		$this->filterLabel = JevcfField::varempty($this->attribute("filterlabel"))?$this->attribute("label"):$this->attribute("filterlabel");
		//$this->filterNullValue = JevcfField::varempty($this->attribute("filterdefault"))?$this->attribute("default"):$this->attribute("filterdefault");
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
		
	}
		
	public function createJoinFilter(){

		$user = JFactory::getUser();
		if ($user->id != $this->_validuser) return "";

		if (trim($this->filter_value)==$this->filterNullValue) return "";
		$join = " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
		$db = JFactory::getDBO();
		$filter =  "$this->map.name=".$db->Quote($this->attribute('name'))." AND $this->map.value LIKE (".$db->Quote($this->filter_value."%").")";
		return $join . " AND ". $filter;
	}
	
	public function createFilter(){

		$user = JFactory::getUser();
		if ($user->id != $this->_validuser) return "";

		if (trim($this->filter_value)==$this->filterNullValue) return "";
		return "$this->map.id IS NOT NULL";
		
	}
	
	function getFilterInput($name, $value, &$node, $control_name)
	{

		$user = JFactory::getUser();
		if ($user->id != $this->_validuser) return "";
						
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
	

	public function createFilterHTML(){

		$user = JFactory::getUser();
		if ($user->id != $this->_validuser) return "";
						
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

	public function bindField($fieldid)	{
		include_once("JevcfField.php");
		return JevcfField::bindFieldWithVarkeys($fieldid, get_object_vars($this));
	}

}