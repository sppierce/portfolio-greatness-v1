<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class IgalleryControllerImage extends JControllerForm
{
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('save_and_next', 'save');
	}
	
	function save()
	{
		$data = JRequest::getVar('jform', array(), 'post' ,'NONE', 4);
		$id = (int)$data['id'];
			
		if(!igGeneralHelper::authorise('core.edit', null, $id))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model = $this->getModel();
		
		$nextOrder = $model->save($data);
		
		switch($this->task)
		{
			case 'apply':
			$url = 'index.php?option=com_igallery&view=image&id='.$id;
			break;
			
			case 'save_and_next':
			$url = empty($nextOrder) ? $url = 'index.php?option=com_igallery&view=images&catid='.JRequest::GetInt('catid') : 'index.php?option=com_igallery&view=image&id='.$nextOrder;
			break;
			
			case 'save':
			$url = 'index.php?option=com_igallery&view=images&catid='.JRequest::GetInt('catid');
		}
		
		$this->setRedirect( JRoute::_($url, false), JText::_('SUCCESSFULLY_SAVED'));
	}
	
	function cancel()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_igallery&view=images&catid='.JRequest::GetInt('catid'), false) );
	}
}