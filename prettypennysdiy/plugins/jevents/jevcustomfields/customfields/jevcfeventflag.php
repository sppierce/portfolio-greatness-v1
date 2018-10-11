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

/**
 * This class handles users adding events to lists e.g. list of favourite events or to a planner
 */
include_once("JevcfField.php");

class JFormFieldJevcfeventflag extends JevcfField 
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'jevcfeventflag';
	const name = 'jevcfeventflag';

	public static function loadScript($field=false){
		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcfeventflag.js');

		if ($field){
			$id = 'field'.$field->fieldname;
		}
		else {
			$id = '###';
		}
		ob_start();
?>
<div class='jevcffieldinput'>

	<div class="jevcflabel"><?php echo JText::_("JEVCF_FIELD_TYPE");?></div>
	<div class="jevcfinputs" style="font-weight:bold;">
		<?php echo JText::_("CUSTOM_FIELD_TYPE_JEVCFEVENTFLAG");?>
			<?php CustomFieldsHelper::fieldId($id);?>
		<br/>
		<?php echo JText::_("JEVCF_JEVCFEVENTFLAG_NOT_IN_EDIT_FORM");?>
	</div>
	<div class="jevcfclear"></div>

	<?php
	CustomFieldsHelper::hidden($id,  $field, self::name);
	CustomFieldsHelper::label($id,  $field, self::name);
        CustomFieldsHelper::name($id, $field, self::name);

	CustomFieldsHelper::booleanField($field,"dv[$id]", "dv0$id", "dv1$id", JText::_("JEVCF_DEFAULT_VALUE"), "dv", JText::_("JEVCF_DEFAULT_VALUE_DESC"));
	CustomFieldsHelper::booleanField($field,"filterdefault[$id]", "filterdefault0$id", "filterdefault1$id", JText::_("JEVCF_FILTER_DEFAULT_VALUE"), "filterdefault", JText::_("JEVCF_FILTER_DEFAULT_VALUE_DESC"));
	CustomFieldsHelper::textField($id, $field, JText::_("JEVCF_UPDATED_MESSAGE"), JText::_('JEVCF_UPDATED_MESSAGE_DESC'), "updatedmessage", "updatedmessage");
	CustomFieldsHelper::textField($id, $field, JText::_("JEVCF_BUTTON_LABEL"), JText::_('JEVCF_BUTTON_LABEL_DESC'), "buttonlabel", "buttonlabel");

	CustomFieldsHelper::textField($id, $field, JText::_("JEVCF_HOVER_MESSAGE"), JText::_('JEVCF_HOVER_MESSAGE_DESC'), "hovermessage", "hovermessage", JText::_("JEVCF_DEFAULT_HOVER_MESSAGE"));

	CustomFieldsHelper::setImage($id, $field, JText::_("JEVCF_ACTIVE_IMAGE"), JText::_('JEVCF_ACTIVE_IMAGE_DESC'), "activeimage", "activeimage", "/plugins/jevents/jevcustomfields/customfields/images/favourite.png");
	CustomFieldsHelper::setImage($id, $field, JText::_("JEVCF_ADD_IMAGE"), JText::_('JEVCF_ADD_IMAGE_DESC'), "addimage", "addimage", "/plugins/jevents/jevcustomfields/customfields/images/addasfavourite.png");
	CustomFieldsHelper::setImage($id, $field, JText::_("JEVCF_INACTIVE_IMAGE"), JText::_('JEVCF_INACTIVE_IMAGE_DESC'), "inactiveimage", "inactiveimage", "/plugins/jevents/jevcustomfields/customfields/images/notfavourite.png");
	CustomFieldsHelper::setImage($id, $field, JText::_("JEVCF_REMOVE_IMAGE"), JText::_('JEVCF_REMOVE_IMAGE_DESC'), "removeimage", "removeimage", "/plugins/jevents/jevcustomfields/customfields/images/removeasfavourite.png");

	CustomFieldsHelper::booleanField($field,"separaterepeats[$id]", "separaterepeats0$id", "separaterepeats2$id", JText::_("JEVCF_SEPARATE_REPEATS"), "separaterepeats", JText::_("JEVCF_SEPARATE_REPEATS_DESC"));
	CustomFieldsHelper::allowoverride($id,  $field);
	//CustomFieldsHelper::hiddenValue($id, $field);
	//CustomFieldsHelper::searchable($id,  $field);
	CustomFieldsHelper::filterOptions($id, $field);
	CustomFieldsHelper::filtermenuOptions($id, $field);
	CustomFieldsHelper::filterDefault($id, $field);
        //CustomFieldsHelper::booleanField($field,"guestseeall[$id]", "guestseeall0$id", "guestseeall1$id", JText::_("JEVCF_FILTER_GUEST_SEE_ALL_IN_PLANNER"), "guestseeall", JText::_("JEVCF_FILTER_GUEST_SEE_ALL_IN_PLANNER_DESC"));
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
	<img src="<?php echo $field ? $field->attribute("activeimage") : "plugins/jevents/jevcustomfields/customfields/images/favourite.png"; ?>" class="jevcf_setimage jevcf_setimage<?php echo "activeimage".$id; ?>"/>
</div>
<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id,  $field, $html, self::name	);

	}

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		if (JRequest::getCmd("option") == "com_menus" || JRequest::getCmd("option") == "com_modules")
		{
			$class = ( $this->element['class'] ? 'class="' . $this->element['class'] . '"' : '' );

			// Must load admin language files
			$lang = JFactory::getLanguage();
			$lang->load("com_jevents", JPATH_ADMINISTRATOR);

			$options = array();
			$options[] = JHTML::_('select.option', -1, JText::_("JEVCF_IGNORE_FILTER"));
			$options[] = JHTML::_('select.option', 0, JText::_("JEV_No"));
			$options[] = JHTML::_('select.option', 1, JText::_("JEV_Yes"));

			return JHTML::_('select.genericlist', $options, $this->name, $class, 'value', 'text', $value, $this->id);
		}
		return "";
	}

	protected function getLabel()
	{
		if (JRequest::getCmd("option") == "com_menus" || JRequest::getCmd("option") == "com_modules")
		{
			return parent::getLabel();
		}
		else {
			return "";
		}
	}

	public function convertValue($value, &$node)
	{
		$user = JFactory::getUser();
		if ($user->get('id') == 0)
		{
			$node->element->attributes()->label = "";
			// set access to something impossible too!
			$node->element->attributes()->access = -999;
			return "";
		}

		$separaterepeats = (int) $this->attribute("separaterepeats");
		
		if (is_null($this->event) || !is_a($this->event, "jIcalEventRepeat" )  || JRequest::getCmd('task') == 'defaults.edit')
		{
			return "";
		}
				
		// For lists we would have a separator or 2 fields for this
		$value = (int) $value;

		$root = JURI::root(false);

		$fieldname = $this->attribute("name");
		$activeimage = $this->attribute('activeimage');
		$inactiveimage = $this->attribute('inactiveimage');
		$addimage = $this->attribute('addimage');
		$removeimage = $this->attribute('removeimage');
		if (strpos($activeimage, 'http:') === false)
		{
			$activeimage = $root . $activeimage;
		}

		if (strpos($inactiveimage, "http:") === false)
		{
			$inactiveimage = $root . $inactiveimage;
		}
		if (strpos($addimage, "http:") === false)
		{
			$addimage = $root . $addimage;
		}
		if (strpos($removeimage, "http:") === false)
		{
			$removeimage = $root . $removeimage;
		}

		$hovermessage = $this->attribute('hovermessage');

		$class = ( $this->element['class'] ? 'class="' . $this->element['class'] . '"' : '' );
		$buttonlabel = $this->attribute("buttonlabel");
		$append = (int) $this->attribute("append");
		$separaterepeats = (int) $this->attribute("separaterepeats");
                
		$user = JFactory::getUser();
		if ($separaterepeats) {
			$value = ($value == $node->event->rp_id()) ? 1 : 0;
		}
		$currentimage = $value > 0 ? $activeimage : $inactiveimage;
		$overimage = $value > 0 ? $removeimage : $addimage;
		$rpid = $this->event->rp_id();
		
		static $scriptloaded;
		if (!isset($scriptloaded))
		{
			$scriptloaded = true;
			$url = JURI::root() . "plugins/jevents/jevcustomfields/customfields/updateeventflag.php";
			$updatedmessage = $this->attribute("updatedmessage");
					
			$doc = JFactory::getDocument();
			$script = <<<SCRIPT

var updatingPlannerImage = false;

function updatePlannerImage(fieldname, rpid){
	if (updatingPlannerImage) {
		return true;
	}
	var planner = jQuery('#jecfeventflag_'+fieldname+'_'+rpid);
	if (planner.attr('src')=='$activeimage'){
		planner.attr('src','$removeimage');
	}
	else if (planner.attr('src')=='$inactiveimage'){
		planner.attr('src','$addimage');
	}
	else if (planner.attr('src')=='$removeimage' ){
		planner.attr('src','$activeimage');
	}
	else if (planner.attr('src')=='$addimage'){
		planner.attr('src','$inactiveimage');
	}
	else if (planner.attr('src')=='$addimage'){
		planner.attr('src','$inactiveimage');
	}
}

function updateEventFlag(fieldname, userid, rpid){
	updatingPlannerImage = true;
	action = jQuery('#jecfeventflag_'+fieldname+'_value'+'_'+rpid).val();
	var requestObject = {};
	requestObject.error = false;
	requestObject.field = fieldname;
	requestObject.task = "updateEventFlag";
	requestObject.value = action;
	requestObject.userid = userid;
	requestObject.rpid = rpid;
	requestObject.separaterepeats= $separaterepeats;

        var jSonRequest = jQuery.ajax({
                    type : 'GET',
                    dataType : 'json',
                    cache : false,                        
                    url : '$url',
                    data : {'json':JSON.stringify(requestObject)},
                    contentType : "application/json; charset=utf-8",
                    scriptCharset : "utf-8"
        })
        .done(function(json){
                if (!json){
                        alert('Update Failed');
                }
                if (json.error){
                        try {
                                eval(json.error);
                        }
                        catch (e){
                                alert('could not process error handler');
                        }
                }
                else {
                        if ( json.newvalue){
                                jQuery('#jecfeventflag_'+fieldname+'_'+rpid).src = '$activeimage';
                        }
                        else {
                                jQuery('#jecfeventflag_'+fieldname+'_'+rpid).src = '$inactiveimage';
                        }
                        jQuery('#jecfeventflag_'+fieldname+'_value'+'_'+rpid).value = json.newvalue;
                        if ('$updatedmessage'!=''){
                                alert('$updatedmessage');
                        }
                }
                updatingPlannerImage = false;
        })
        .fail( function( jqxhr, textStatus, error){
		alert('Something went wrong... '+textStatus + ", " + error);
                updatingPlannerImage = false;
        });
}
SCRIPT;
			$doc->addScriptDeclaration($script);
		}

		$html = "";
		$img = '<img  ' . $class . ' alt="' . htmlspecialchars($hovermessage, ENT_COMPAT, 'UTF-8') . '"  src="' . $currentimage . '"  id="jecfeventflag_' . $fieldname . '_' . $rpid . '" onclick="updateEventFlag(\'' . $fieldname . '\', ' . $user->id . ', ' . $rpid . ');return false;" onmouseover="updatePlannerImage(\'' . $fieldname . '\', ' . $rpid . ')" onmouseout="updatePlannerImage(\'' . $fieldname . '\', ' . $rpid . ')" style="pointer:cursor" />';
		if ($hovermessage)
		{
			$html .= '<span class="hasplannertip" title="' . htmlspecialchars($hovermessage, ENT_COMPAT, 'UTF-8') . '" >' . $img . '</span>';
		}
		else
		{
			$html .= $img;
		}

		$html .= '<input type="hidden" id="jecfeventflag_' . $fieldname . '_value_' . $rpid . '" value="' . $value . '"/>';

                JevHtmlBootstrap::popover('.hasplannertip' , array("trigger"=>"hover focus", "placement"=>"top", "container"=>"#jevents_body", "delay"=> array( "show"=> 150, "hide"=> 150 )));
                
		return $html;

	}

	function fetchRequiredScript($name, &$node, $control_name)
	{
		return "";

	}

	public function constructFilter($node)
	{

		$user = JFactory::getUser();
		if ($user->id == 0)
			return "";

		$this->node = $node;
		$specialCharacters = array(" ", "-" , ",", ".");
		$this->filterType = str_replace($specialCharacters,"",$this->attribute("name"));
		$this->filterLabel = JevcfField::varempty($this->attribute("filterlabel")) ? $this->attribute("label") : $this->attribute("filterlabel");
		$this->filterNullValue = $this->attribute("filterdefault")==""?($this->attribute("default")==""?0:$this->attribute("default")):$this->attribute("filterdefault");
    		$this->filter_value = $this->filterNullValue;
		$this->map = "csf" . $this->filterType;

		$this->separaterepeats =(int) $this->attribute("separaterepeats");

		$registry = JRegistry::getInstance("jevents");
		$this->indexedvisiblefilters = $registry->get("indexedvisiblefilters", false);
		if ($this->indexedvisiblefilters === false)
			return;

		// This is our best guess as to whether this filter is visible on this page.
		$this->visible = in_array("customfield", $this->indexedvisiblefilters);

		// If using caching should disable session filtering if not logged in
		$cfg = JEVConfig::getInstance();
		$useCache = (int) $cfg->get('com_cache', 0);
		$user = JFactory::getUser();
		$mainframe = JFactory::getApplication();
		if ((int) JRequest::getVar('filter_reset', 0))
		{
			JFactory::getApplication()->setUserState( $this->filterType.'_fv_ses', $this->filterNullValue );
			$this->filter_value = $this->filterNullValue;
		}
		// ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
		else if (!$this->visible)
		{
			$this->filter_value = JRequest::getVar($this->filterType . '_fv', $this->filterNullValue, "request", "string");
		}
		else
		{
			$this->filter_value = JFactory::getApplication()->getUserStateFromRequest($this->filterType . '_fv_ses', $this->filterType . '_fv', $this->filterNullValue, "string");
		}

	}

	public function createJoinFilter()
	{

		$user = JFactory::getUser();
		if ($user->id == 0)
			return "";

		if (trim($this->filter_value) == $this->filterNullValue || trim($this->filter_value) == -1)
			return "";
		$join = " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";

		$db = JFactory::getDBO();
		if ($this->separaterepeats) {
			$filter = "$this->map.name=" . $db->Quote($this->attribute('name')) . " AND $this->map.value = rpt.rp_id AND $this->map.user_id =" . $user->id;
		}
		else {
			$filter = "$this->map.name=" . $db->Quote($this->attribute('name')) . " AND $this->map.value =1 AND $this->map.user_id =" . $user->id;
		}
		return $join . " AND " . $filter;

	}

	public function createFilter()
	{

		$user = JFactory::getUser();
		if ($user->id == 0)
			return "";

		if (!isset($this->filter_value)  || trim($this->filter_value) == $this->filterNullValue || trim($this->filter_value) == -1)
			return "";
		return "$this->map.id IS NOT NULL";

	}

	function getFilterInput($name, $value, &$node, $control_name)
	{

		$user = JFactory::getUser();
		if ($user->id == 0)
			return "";

		$size = ( $this->attribute('size') ? 'size="' . $this->attribute('size') . '"' : '' );
		$class = ( $this->element['class'] ? 'class="' . $this->element['class'] . '"' : 'class="text_area"' );
		/*
		 * Required to avoid a cycle of encoding &
		 * html_entity_decode was used in place of htmlspecialchars_decode because
		 * htmlspecialchars_decode is not compatible with PHP 4
		 */
		$value = (int) $value;

		$checked = $value ? " checked='checked' " : "";

		if ($value == 0)
		{
			$res = '<input type="checkbox" value="1" ' . $class . ' ' . $size . ' ' . $checked . ' onclick="jQuery(\'#hiddenevflag\').val(this.checked?1:0);submit(this.form)" />';
			$res .= '<input type="hidden" name="' . $control_name . $name . '" id="hiddenevflag" value="' . $value . '"/>';
		}
		else
		{
			$res = '<input type="hidden" name="' . $control_name . $name . '" id="hiddenevflag" value="' . $value . '"/>';
			$res.= "<span class='filtervalue' onmousedown='jQuery(\"#hiddenevflag\").val(0);document.jeventspost.submit();'>Yes</span>";
		}

		return $res;

	}

	public function createFilterHTML()
	{

		$user = JFactory::getUser();
		if ($user->id == 0)
			return "";

		$filterList = array();
		$filterList["title"] = "<label class='evdate_label' for='" . $this->filterType . "_fv'>" . $this->filterLabel . "</label>";
		$filterList["html"] = $this->getFilterInput($this->filterType . "_fv", $this->filter_value, $this->node, "");

		$script = "try {JeventsFilters.filters.push({id:'" . $this->filterType . "_fv',value:'" . addslashes($this->filterNullValue) . "'});} catch (e) {}";
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
		$myXML = JevcfField::bindFieldWithVarkeys($fieldid, get_object_vars($this));
                
        JevcfField::bindNonBlankString($myXML, $fieldid, "updatedmessage");
        JevcfField::bindNonBlankString($myXML, $fieldid, "buttonlabel");
        JevcfField::bindNonBlankString($myXML, $fieldid, "hovermessage");
        JevcfField::bindNonBlankString($myXML, $fieldid, "activeimage");
        JevcfField::bindNonBlankString($myXML, $fieldid, "addimage");
        JevcfField::bindNonBlankString($myXML, $fieldid, "inactiveimage");
        JevcfField::bindNonBlankString($myXML, $fieldid, "removeimage");
        JevcfField::bindBoolean($myXML, $fieldid, "separaterepeats");

        return $myXML;
	}

}
