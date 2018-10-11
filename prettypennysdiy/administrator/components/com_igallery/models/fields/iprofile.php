<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');


class JFormFieldIprofile extends JFormField
{
	protected $type = 'Iprofile';

	protected function getInput()
	{
		$option = JRequest::getVar('option');
		if($option == 'com_config')
		{
			$params =& JComponentHelper::getParams('com_igallery'); 
			$selected = $params->get('default_profile', 0);
			
		}
		else if(strpos($option, 'module') )
		{
			$igparams = $this->form->getValue('params');
			$selected = isset($igparams->profile_id) ? $igparams->profile_id : 0;
		}
		else
		{
			$selected = $this->form->getValue('profile');
		}
		
		$db =& JFactory::getDBO();
	    $query = "SELECT id as value, name as text FROM #__igallery_profiles WHERE published = 1 ORDER BY ordering";
	    $db->setQuery( $query );
	    $profiles = $db->loadObjectList();
		
	    $selectHTML = JHTML::_("select.genericlist", $profiles, $this->name, 'class="inputbox"', 'value', 'text', $selected);
	
	    return $selectHTML;
	}
}