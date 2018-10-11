<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class igalleryControllerProfiles extends JControllerAdmin
{
	public function &getModel($name = 'Profile', $prefix = 'IgalleryModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function add_profile_redirect()
	{
		$this->setRedirect('index.php?option=com_igallery&view=profile');
	}
	
	function copy()
	{
		if(!igGeneralHelper::authorise('core.admin'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		if(!igGeneralHelper::authorise('core.create'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$model = $this->getModel();
		
		$msg = '';
		if ($model->copy()) 
		{
			$msg = JText::_('SUCCESSFULLY_COPIED');
		}
		
		$this->setRedirect('index.php?option=com_igallery&view=profiles', $msg);
	}
	
	function delete()
	{
		if(!igGeneralHelper::authorise('core.admin'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		if(!igGeneralHelper::authorise('core.delete'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model = $this->getModel();
		
		if( !$model->checkAssigned() )
		{
			$this->setRedirect('index.php?option=com_igallery&view=profiles');
			return;
		}
		
		parent::delete();
	}
    
}