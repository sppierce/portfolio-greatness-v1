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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
include_once ("JevcfFieldList.php");

class JFormFieldJevcfuser extends JevcfFieldList
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'jevcfuser';
	const name = 'jevcfuser';

	public static function loadScript($field=false){
		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcfuser.js');

		if ($field){
			$id = 'field'.$field->fieldname;
                        $selectedField = $field->attribute('fieldname');
		}
		else {
			$id = '###';
                        $selectedField = "";
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

        CustomFieldsHelper::contact($id,  $field);
        ?>

	<div class="jevcfnotranslate profilefield pf<?php echo $id; ?>" style="display:none;">
	<div class="jevcflabel">
		<?php echo JText::_("JEVCF_PROFILE_FIELD");?>
		<?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_PROFILE_FIELD_DESC'));?>
	</div>
	<div class="jevcfinputs">
		<select name="fieldname[<?php echo $id; ?>]" id="fieldname<?php echo $id; ?>"  onchange="jevruser.setvalue('<?php echo $id; ?>');">
		<?php
		// get the community builder language file - in the vain hope they have moved to Joomla 1.5 system
		$lang = JFactory::getLanguage();
		$lang->load("plg_user_profile", JPATH_ADMINISTRATOR);

		$plugin = JPluginHelper::getPlugin('user', 'profile');
		if (isset($plugin->params) && $plugin->params) {
			$params = new JRegistry($plugin->params);
			$hasPlugin = true;
		}
		else {
			$params = new JRegistry(null);
			$hasPlugin = false;
		}

		$fieldname = "";
		$activeField = "";

		// Add the registration fields to the form.
		if ($hasPlugin) {
			JForm::addFormPath(JPATH_SITE. '/plugins/user/profile/profiles');

			$form = JForm::getInstance("profileform", "profile");
			$fieldsets = $form->getFieldsets();

			foreach ($fieldsets as $key=>$fieldset) {
				$fieldsetFields = $form->getFieldset($key);
				foreach ($fieldsetFields as $fsfield){
					$label = $fsfield->getLabel();
					$name = $fsfield->fieldname;
					$selected = "";
					if ($field && $fieldname == $name)
					{
						$selected = "selected='selected'";
						$activeField = $label;
					}
			?>
						<option value="<?php echo $fsfield->fieldname; ?>" <?php echo $selected; ?> ><?php echo $label ?></option>
			<?php
				}
			}
		}

		// Get the core JEvents user fields form.
		JForm::addFormPath(JPATH_ADMINISTRATOR. '/components/com_users/models/forms');
		JForm::addFieldPath(JPATH_ADMINISTRATOR. '/components/com_users/models/fields');

		$lang = JFactory::getLanguage();
		$lang->load("com_users", JPATH_ADMINISTRATOR);

		$form = JForm::getInstance('com_users.user', 'user');
		$fieldsets = $form->getFieldsets();

		foreach ($fieldsets as $key=>$fieldset) {
			$fieldsetFields = $form->getFieldset($key);
			foreach ($fieldsetFields as $fsfield){
				if ($fsfield->element->attributes()->readonly){
					continue;
				}
				$label = $fsfield->getLabel();
				$name = $fsfield->fieldname;
				$selected = "";
				if ($field && $selectedField == $name)
				{
					$selected = "selected='selected'";
					$activeField = $label;
				}
		?>
					<option value="<?php echo $fsfield->fieldname; ?>" <?php echo $selected; ?> ><?php echo $label ?></option>
		<?php
			}
		}

	?>
		</select>
	</div>
	</div>
	<div class="jevcfclear"></div>

	<?php
	if ($field){
		?>
	<script type="text/javascript">
		jevcfforms.revealConditionalDisplayField('ct<?php echo $id;?>', 'pf<?php echo $id;?>' , 0);
	</script>
		<?php
	}

	CustomFieldsHelper::usergroups($id,  $field);

	CustomFieldsHelper::required($id,  $field);
	CustomFieldsHelper::requiredMessage($id,  $field);
	CustomFieldsHelper::conditional($id,  $field);
	CustomFieldsHelper::allowoverride($id,  $field);
	CustomFieldsHelper::applicableCategories($id, $field) ;
	CustomFieldsHelper::accessOptions($id,  $field);
	CustomFieldsHelper::readaccessOptions($id,  $field);
        CustomFieldsHelper::fieldclass($id, $field);
	CustomFieldsHelper::universal($id, $field);
	?>

	<div class="jevcfclear"></div>

</div>
<div class='jevcffieldpreview'  id='<?php echo $id;?>preview'>
	<div class="previewlabel"><?php echo JText::_("JEVCF_PREVIEW");?></div>
	<div class="jevcflabel jevcfpl" id='pl<?php echo $id;?>' ><?php echo $field?$field->attribute('label'):JText::_("JEVCF_FIELD_LABEL");?></div>
	<select name="pdv[<?php echo $id; ?>]" id="pdv<?php echo $id; ?>" >
		<option value="0" ><?php echo htmlspecialchars_decode(JText::_("JEVCF_SELECT_USER")); ?></option>
	</select>
</div>
<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id,  $field, $html, self::name	);

	}


	protected function getInput()
	{
		// make sure we have a helpful class set to get the width
		if (!$this->element['class'] ){
			$this->element['class'] =" jevminwidth";
		}
                // multiple choice version
                /*
                $this->multiple=true;
                $this->name .= "[]";
                $this->value = is_string($this->value)? explode(",",$this->value ) : $this->value ;
                 */
		return parent::getInput();
	}

        // multiple selection version
        /*
	public function convertValue($value, $node){
		if (is_object($value) && isset($value->id)){
			$value = $value->id;
		}
                // multiple version                 
                $value = explode(",",$value);
		JArrayHelper::toInteger($value);
                $value[] = -1;
                        
		$profileField = $this->attribute('profilefield');

		if ($profileField)
		{
			// Load the profile data from the database.
			$db = JFactory::getDbo();
			$db->setQuery(
				'SELECT profile_key, profile_value FROM #__user_profiles' .
					' WHERE user_id IN ( ' . implode(",", $value) . ") AND profile_key LIKE '".$profileField.".%'" .
					' ORDER BY ordering'
			);

			try
			{
				$profile = $db->loadRowList();
			}
			catch (RuntimeException $e)
			{
				$this->_subject->setError($e->getMessage());

				return false;
			}

			$user = JEVHelper::getUser($value);

			foreach($profile as $profileRow)
			{
				$profileValue = substr($profileRow[1], 1, strlen($profileRow[1])-2);
				if($profileValue)
				{
					$profileHtmlArray[] = '<div class="jev_cfuser_'.strtolower($profileRow[0]).'">'
							. '<span class="jev_cfuser_label">'.JText::_(strtoupper('JEV_CFUSER_'.str_replace(".", "_", $profileRow[0]))).'</span>'
							. ' <span class="jev_cfuser_value">'.$profileValue.'</span>'
							. '</div>';
				}
			}

			$profileHtml = '<div class="jev_cfuser_profile"><div class="jev_cfuser_name">'.$user->name.'</div>';
			
			if(isset($profileHtmlArray))
			{
				$profileHtml .= implode('',$profileHtmlArray);
			}

			$profileHtml .= "</div>";
			
			return $profileHtml;
		}
		
		if ($this->attribute('contact'))
		{
			$userdet = JEVHelper::getContact($value);
			$contactlink = "";
			if ($userdet)
			{
				if (isset($userdet->slug) && $userdet->slug )
				{
					$contactlink = JRoute::_('index.php?option=com_contact&view=contact&id=' . $userdet->slug . '&catid=' . $userdet->catslug);
					$contactlink = '<a href="' . $contactlink . '"  target="_blank" >' . $userdet->contactname . '</a>';
					return $contactlink;
				}
			}
		}

		$user = JEVHelper::getUser($value);
		return $user->name;

	}                
        */
        
	public function convertValue($value, $node){
		if (is_object($value) && isset($value->id)){
			$value = $value->id;
		}
		$value = (int) $value;
		if ($value<=0) return "";

		$profileField = $this->attribute('profilefield');

		if ($profileField)
		{
			// Load the profile data from the database.
			$db = JFactory::getDbo();
			$db->setQuery(
				'SELECT profile_key, profile_value FROM #__user_profiles' .
					' WHERE user_id = ' . (int) $value . " AND profile_key LIKE '".$profileField.".%'" .
					' ORDER BY ordering'
			);

			try
			{
				$profile = $db->loadRowList();
			}
			catch (RuntimeException $e)
			{
				$this->_subject->setError($e->getMessage());

				return false;
			}

			$user = JEVHelper::getUser($value);

			foreach($profile as $profileRow)
			{
				$profileValue = substr($profileRow[1], 1, strlen($profileRow[1])-2);
				if($profileValue)
				{
					$profileHtmlArray[] = '<div class="jev_cfuser_'.strtolower($profileRow[0]).'">'
							. '<span class="jev_cfuser_label">'.JText::_(strtoupper('JEV_CFUSER_'.str_replace(".", "_", $profileRow[0]))).'</span>'
							. ' <span class="jev_cfuser_value">'.$profileValue.'</span>'
							. '</div>';
				}
			}

			$profileHtml = '<div class="jev_cfuser_profile"><div class="jev_cfuser_name">'.$user->name.'</div>';
			
			if(isset($profileHtmlArray))
			{
				$profileHtml .= implode('',$profileHtmlArray);
			}

			$profileHtml .= "</div>";
			
			return $profileHtml;
		}
		
		if ($this->attribute('contact'))
		{
			$userdet = JEVHelper::getContact($value);
			$contactlink = "";
			if ($userdet)
			{
				if (isset($userdet->slug) && $userdet->slug )
				{
					$contactlink = JRoute::_('index.php?option=com_contact&view=contact&id=' . $userdet->slug . '&catid=' . $userdet->catslug);
					$contactlink = '<a href="' . $contactlink . '"  target="_blank" >' . $userdet->contactname . '</a>';
					return $contactlink;
				}
			}
		}

		$user = JEVHelper::getUser($value);
		return $user->name;

	}
	
	public function getOptions()
	{
		$db = JFactory::getDBO();

		$sql = "SELECT u.id AS value, u.name AS text  FROM #__users as u";
		$usergroups = $this->attribute("usergroups");
		if (!empty($usergroups))
		{
			$sql .= " LEFT JOIN #__user_usergroup_map as map ON map.user_id=u.id"
					. " WHERE map.group_id IN ($usergroups)";
		}
		$sql .= " ORDER BY u.name asc";

		$db->setQuery($sql);
		$users = $db->loadObjectList();

		$nulluser = new stdClass();
		$nulluser->value = 0;
		$nulluser->text = JText::_("JEVCF_SELECT_USER");
		array_unshift($users,$nulluser);
		
		return $users;
		
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