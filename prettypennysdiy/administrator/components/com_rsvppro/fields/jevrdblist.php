<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevboolean.php 1569 2009-09-16 06:22:03Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

include_once(JPATH_ADMINISTRATOR ."/components/com_rsvppro/fields/JevrFieldList.php");
$language = JFactory::getLanguage();
$language->load('com_rsvppro', JPATH_ADMINISTRATOR);

class JFormFieldJevrdblist extends JevrFieldList
{

	protected $node;
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'jevrdblist';
	const name = 'jevrdblist';

	public static function loadScript($field=false){

		if ($field)
		{
			$id = 'field' . $field->field_id;
		}
		else
		{
			$id = '###';
		}
		ob_start();
?>
<div class='rsvpfieldinput'>
        <div class="rsvplabel"><?php echo JText::_("RSVP_FIELD_TYPE"); ?></div>
        <div class="rsvpinputs" style="font-weight:bold;"><?php echo JText::_("RSVP_TEMPLATE_TYPE_jevrdblist"); ?><?php RsvpHelper::fieldId($id); ?></div>
        <div class="rsvpclear"></div>
    
	<?php
	RsvpHelper::hidden($id,  $field, self::name);
	RsvpHelper::label($id,  $field, self::name);
	RsvpHelper::tooltip($id, $field);

        if ($field)
        {
                try {
                        $params = json_decode($field->params);
                }
                catch (Exception $e) {
                        $params = array();
                }
        }
        $fieldquery = isset($params->fieldquery) ? $params->fieldquery : "";
        
	RsvpHelper::textArea("fieldquery$id", "params[$id][fieldquery]", $field, JText::_("JEVR_DBLIST_QUERY_FIELD"), JText::_('JEVR_DBLIST_QUERY_FIELD_DESC'),"fieldquery", "SELECT ug.title FROM #__usergroups AS ug \nINNER JOIN #__user_usergroup_map AS ugm ON ugm.group_id = ug.id \nWHERE ugm.user_id={ATTENDEE_USER_ID}");

        RsvpHelper::required($id, $field);
        RsvpHelper::requiredMessage($id, $field);
        RsvpHelper::conditional($id, $field);
        RsvpHelper::className($id, $field);
        RsvpHelper::peruser($id, $field);
        RsvpHelper::formonly($id, $field);
        RsvpHelper::showinform($id, $field);
        RsvpHelper::showindetail($id, $field);
        RsvpHelper::showinlist($id, $field);
        RsvpHelper::allowoverride($id, $field);
        RsvpHelper::accessOptions($id, $field);
        RsvpHelper::applicableCategories("facc[$id]", "facs[$id]", $id, $field ? $field->applicablecategories : "all");
	?>

	<div class="rsvpclear"></div>

</div>
    <div class='rsvpfieldpreview'  id='<?php echo $id;?>preview'>
            <div class="previewlabel"><?php echo JText::_("RSVP_PREVIEW");?></div>
            <div class="rsvplabel rsvppl" id='pl<?php echo $id;?>' ><?php echo $field ? $field->label : JText::_("RSVP_FIELD_LABEL"); ?></div>
            <div id="pdv<?php echo $id;?>">
            </div>
    </div>
    <div class="rsvpclear"></div>
		<?php
		$html = ob_get_clean();

		return RsvpHelper::setField($id,  $field, $html, self::name	);

	}

	function getInput()
	{
		$name = $this->name;
		$value =  $this->value;
		$class = ( $this->element['class'] ? 'class="'.$this->element['class'].'"' : 'class="inputbox"' );

		$db = JFactory::getDbo();
		$fieldquery = $this->attribute('fieldquery',0);
		if (!$fieldquery) {
			return "<strong>Invalid attributes - please specify field query</strong>";
		}
                $user = JFactory::getUser();
                $id = $user->id;
                if (isset($this->attendee->user_id)){
                    $id = $this->attendee->user_id;
                }
                $fieldquery = str_replace("{ATTENDEE_USER_ID}", $id, stripslashes($fieldquery));
		$db->setQuery($fieldquery);
		$data = $db->loadColumn();
		$options = array();
		$options[] = JHTML::_('select.option', 0, " -- ");
		foreach ($data as $option)
		{
			$options[] = JHTML::_('select.option', $option, $option);
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
/*
	function fetchRequiredScript($name, &$node, $control_name)
	{
		$script =  "JevStdRequiredFields.fields.push({'name':'".$control_name.$name."', 'default' :'".$this->attribute('default') ."' ,'reqmsg':'".trim(JText::_($this->attribute('requiredmessage'),true))."'}); ";
		return $script ;
	}
	function fetchCategoryRestrictionScript($name, &$node, $control_name, $cats)
	{
		$script = "JevrCategoryFields.fields.push({'name':'".$name."', 'default' :'".$this->attribute('default') ."' ,'catids':".  json_encode($cats)."}); ";
		return $script;
	}
 */
	

}
