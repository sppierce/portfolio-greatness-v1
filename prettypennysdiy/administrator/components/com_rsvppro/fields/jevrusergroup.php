<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

include_once(JPATH_ADMINISTRATOR ."/components/com_rsvppro/fields/JevrField.php");

class JFormFieldJevrusergroup extends JevrField
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'Jevrusergroup';
	const name = 'jevrusergroup';

	static function isEnabled()
	{
            return 1;

	}

	public static function loadScript($field=false)
	{
		JHtml::script('administrator/components/' . RSVP_COM_COMPONENT . '/fields/js/jevrusergroup.js');

		if ($field)
		{
			$id = 'field' . $field->field_id;
        		$jusergroup = "";
        		$jusergroup = "";
			try {
				$params = json_decode($field->params);
				$jusergroup = isset($params->groupname) ? $params->groupname : "";
			}
			catch (Exception $e) {				
			}
		}
		else
		{
			$id = '###';
        		$jusergroup = "";
		}
                
		ob_start();
?>
	<div class='rsvpfieldinput'>
		<div class="rsvplabel"><?php echo JText::_("RSVP_FIELD_TYPE"); ?></div>
		<div class="rsvpinputs" style="font-weight:bold;"><?php echo JText::_("RSVP_TEMPLATE_TYPE_JEVRUSERGROUP"); ?><?php RsvpHelper::fieldId($id);?></div>
		<input type="hidden" name="dv[<?php echo $id; ?>]"  value="" />                
                <div class="rsvpclear"></div>
                <h4><?php echo JText::_("RSVP_TEMPLATE_FIELD_JEVUSERGROUP_EXPLANATION");?></h4>
                <div class="rsvpclear"></div>
                <div class="rsvplabel"><?php echo JText::_("RSVP_TEMPLATE_TYPE_JEVRUSERGROUP_SELECTION");?></div>
                <div class="rsvpinputs" style="font-weight:bold;">
                    <select name="params[<?php echo $id; ?>][groupname]" id="groupname<?php echo $id; ?>"  onchange="jevrusergroup.setvalue('<?php echo $id; ?>');">
                       <?php
                       	//$groups = JHelperUsergroups::getInstance()->getAll();
                        
                // Joomla 3.6.4 required for the above?
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level')
			->from($db->quoteName('#__usergroups') . ' AS a')
			->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id, a.title, a.lft, a.rgt')
			->order('a.lft ASC');
		$db->setQuery($query);
		$groups = $db->loadObjectList();
                        
			$options = array();
			foreach ($groups as $group)
			{
				$options[] = (object) array(
					'text'  => str_repeat('- ', $group->level) . $group->title,
					'value' => $group->id,
					'level' => $group->level
				);
			}
                        echo JHtml::_('select.options', $options, "value", "text", $jusergroup);

                       ?>
                    </select>
                </div>
		<div class="rsvpclear"></div>
                
                <?php
		RsvpHelper::hidden($id, $field, self::name);
?>
        	<div class="rsvpclear"></div>
    </div>
    <div class='rsvpfieldpreview'  id='<?php echo $id;?>preview'>
            <div class="previewlabel"><?php echo JText::_("RSVP_PREVIEW");?></div>
            <div class="rsvplabel rsvppl" id='pl<?php echo $id;?>' ><?php echo JText::_("RSVP_TEMPLATE_TYPE_JEVRUSERGROUP"); ?></div>
            <div id="pdv<?php echo $id;?>">
            </div>
    </div>
    <div class="rsvpclear"></div>
<?php
		$html = ob_get_clean();

		return RsvpHelper::setField($id, $field, $html, self::name);

	}

	function getInput()
	{
            return "";
	}

	function toXML($field)
	{
		$result = array();
		$result[] = "<field ";
		if (is_string($field->params) && strpos($field->params, "{") === 0)
		{
			$field->params = json_decode($field->params);
		}
		foreach (get_object_vars($field) as $k => $v)
		{
			if ($k == "options" || $k == "html" || $k == "defaultvalue" || $k == "name")
				continue;
			if ($k == "field_id")
			{
				$k = "name";
				$v = "field" . $v;
			}
			if ($k == "params")
			{
				if (is_object($field->params))
				{
				   foreach (get_object_vars($field->params) as $label=>$value)
				   {
					   $result[] = $label . '="' . addslashes(htmlspecialchars($value)) . '" ';
				   }
				}
				continue;
			}
			$result[] = $k . '="' . addslashes(htmlspecialchars($v)) . '" ';
		}
		$result[] = " />";
		$xml = implode(" ", $result);
		return $xml;

	}

	public function convertValue($value)
	{
            return "";
	}

	public  function postUpdateAction($node)
	{
		$attendeeparams = new JRegistry($this->attendee->params);		
//var_dump($attendeeparams );
		$field = $this->attribute("name");
		$value = $attendeeparams->get($field);
		if (!$value){
			$value = array(0);
		}
		if (!is_array($value)){
			$value = array($value);
		}

		$groupname = $this->attribute('groupname');

                // is this a new user
                $registry = JRegistry::getInstance("jevents");
                $rsvpdata = $registry->get("rsvpdata");                    
                $newid = isset($rsvpdata->newid) ? $rsvpdata->newid : 0;
                if ($newid && $this->attendee->user_id==0){
                    $user_id = $newid;
                }
                else {
                    $user_id = $this->attendee->user_id;
                }
                
		$attendee = false;
		if ($this->attendee && $user_id>0 && $groupname && $this->attendee->attendstate==1 && $this->attendee->confirmed==1){
			JUserHelper::addUserToGroup($user_id, $groupname);
		}
                else if ($this->attendee && $user_id>0 && $groupname) {
                        JUserHelper::removeUserFromGroup($user_id, $groupname);
                }

	}
        
}