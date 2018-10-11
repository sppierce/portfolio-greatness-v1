<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class IgalleryControllerProfile extends JControllerForm
{
	function save()
	{
		if(!igGeneralHelper::authorise('core.admin'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$app = JFactory::getApplication();
		$data = JRequest::getVar('jform', array(), 'post' ,'NONE', 4);
		$id = (int)$data['id'];
			
		if( empty($id[0]) )
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
		 
		if(strlen($_FILES['jform']['name']['watermark_filename']) > 2 )
		{  
			$fileName = $_FILES['jform']['name']['watermark_filename'];
			$tmpPath = $_FILES['jform']['tmp_name']['watermark_filename'];
			$uploadError = $_FILES['jform']['error']['watermark_filename'];
			
			if(!$uploadedFile = igUploadHelper::upload_file($fileName, $tmpPath, $uploadError, IG_WATERMARK_PATH, true) )
			{
				return false;
			}
			
			$data['watermark_filename'] = $uploadedFile;
		}
		
		$model = $this->getModel();
		$form = $model->getForm($data, false);

		if(!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');
			return false;
		}

		$validData = $model->validate($form, $data);

		if($validData === false)
		{
			$errors	= $model->getErrors();
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i]))
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$this->setRedirect('index.php?option=com_igallery&view=profile&id='.$id, false);
			return false;
		}
		
		if( !$model->save($validData) )
		{
			JError::raise(2, 500, $model->getError() );
			$msg='';
		}
		else 
		{
			$msg = JText::_('SUCCESSFULLY_SAVED');
		} 
		
		switch($this->task)
		{
			case 'apply':
			$url = 'index.php?option=com_igallery&view=profile&id='.$id;
			break;
			
			case 'save':
			$url = 'index.php?option=com_igallery&view=profiles';
		}
	
		$this->setRedirect($url, $msg);
	}
	
	function remove()
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
		
		if( !$model->delete() )
		{
			$msg='';
			JError::raise(2, 500, $model->getError() );
		}
		else 
		{
			$msg = JText::_('SUCCESSFULLY_SAVED');
		}
		
		$this->setRedirect('index.php?option=com_igallery&view=profiles', $msg);
	}
	
}