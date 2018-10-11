<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class IgalleryController extends JControllerLegacy
{
	function display()
	{
		$view = JRequest::getCmd('view','categories');
		$id = JRequest::getInt('id', 0);
		
		if($view == 'icategory')
		{
			if( empty($id) )
			{
				if(!igGeneralHelper::authorise('core.create'))
				{
					return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
				}
			}
			else
			{
				if(!igGeneralHelper::authorise('core.edit', $id))
				{
					return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
				}
			}
			
			$model = $this->getModel('icategory');
		
			if( !$model->checkProfileExists() )
			{
				JFactory::getApplication()->enqueueMessage( JText::_( 'PLEASE_CREATE_PROFILE_FIRST' ) );
				$this->setRedirect('index.php?option=com_igallery&view=profiles');
				return;
			}
		}
		
		if($view == 'image')
		{
			if(!igGeneralHelper::authorise('core.edit', null, JRequest::getInt('id', 0) ))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
		
		
		if($view == 'profiles')
		{
			if(!igGeneralHelper::authorise('core.admin'))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
		
		if($view == 'profile')
		{
			if(!igGeneralHelper::authorise('core.admin'))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
			
			if( empty($id) )
			{
				if(!igGeneralHelper::authorise('core.create'))
				{
					return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
				}
			}
			else
			{
				if(!igGeneralHelper::authorise('core.edit'))
				{
					return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
				}
			}
		}
		
		if($view == 'serverimport')
		{
			if(!igGeneralHelper::authorise('core.admin'))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
	
		parent::display();
	}
}
