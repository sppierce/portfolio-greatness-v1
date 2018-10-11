<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class IgControllerImage extends JControllerForm
{
	function __construct($config = array())
	{
		$config['base_path'] = JPATH_SITE.'/components/com_igallery';
		parent::__construct($config);
	}
	
	function reportImage()
	{
		$model = $this->getModel();
		
		$msg = '';
		if( !$model->reportImage() ) 
		{
			JError::raise(2, 500, $model->getError() );
		}
		else
		{
			$msg = JText::_('YOUR_MESSAGE_SENT');
		}
		
		$this->setRedirect('index.php?option=com_igallery&view=category&igid='.JRequest::getInt('catid').'&Itemid='.JRequest::getInt('Itemid'), $msg);
	}
}
