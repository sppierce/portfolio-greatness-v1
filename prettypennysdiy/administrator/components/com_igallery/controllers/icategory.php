<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class IgalleryControllerICategory extends JControllerForm
{
	function save()
	{
		$data = JRequest::getVar('jform', array(), 'post' ,'NONE', 4);
		$id = (int)$data['id'];
			
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
		
		$model = $this->getModel();
		
		$msg = '';
		if(!$model->save($data)) 
		{
			JError::raise(2, 500, $model->getError() );
		}
		else
		{
			$msg = JText::_('SUCCESSFULLY_SAVED');
		}
		
		switch($this->task)
		{
			case 'apply':
			$url = 'index.php?option=com_igallery&view=icategory&id='.$id;
			break;
			
			case 'save':
			$url = 'index.php?option=com_igallery&view=categories';
		}
		
		$this->setRedirect( JRoute::_($url, false), $msg);
	}
	
	public function cancel()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_igallery&view=categories', false) );
	}
}