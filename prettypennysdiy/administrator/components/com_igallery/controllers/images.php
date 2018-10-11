<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class igalleryControllerimages extends JControllerAdmin
{
	public function &getModel($name = 'Image', $prefix = 'IgalleryModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function copy_move()
	{
		if(!igGeneralHelper::authorise('core.create', JRequest::getInt('catid', 0)))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model = $this->getModel();
		
		$msg = '';
		if(!$model->copy_move()) 
		{
			JError::raise(2, 500, $model->getError() );
		}
		else
		{
			$copyMove = JRequest::getWord('copy_move', 'copy');
			$msg = $copyMove == 'move' ? JText::_('SUCCESSFULLY_MOVED') : JText::_('SUCCESSFULLY_COPIED');
		}
	
		$this->setRedirect('index.php?option=com_igallery&view=images&catid='.JRequest::getInt('catid'), $msg);
	}
	
	function add_tags()
	{
		if( !igGeneralHelper::authorise('core.edit') )
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model = $this->getModel();
		$msg = '';
		if( !$model->add_tags() ) 
		{
			JError::raise(2, 500, $model->getError() );
		}
		else
		{
			$msg = JText::_('SUCCESSFULLY_ADDED');
		}
	
		$this->setRedirect('index.php?option=com_igallery&view=images&catid='.JRequest::getInt('catid'), $msg);
	}
	
	function remove_tags()
	{
		if( !igGeneralHelper::authorise('core.edit') )
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model = $this->getModel();
		$msg = '';
		if( !$model->remove_tags() ) 
		{
			JError::raise(2, 500, $model->getError() );
		}
		else
		{
			$msg = JText::_('SUCCESSFULLY_REMOVED');
		}
	
		$this->setRedirect('index.php?option=com_igallery&view=images&catid='.JRequest::getInt('catid'), $msg);
	}
	
	function browserUpload()
	{
		if(!igGeneralHelper::authorise('core.create', JRequest::getInt('catid', 0) ) )
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		for($i=0; $i<count($_FILES['uploads']['name']); $i++)
		{
			$fileName = $_FILES['uploads']['name'][$i];
			$tmpPath = $_FILES['uploads']['tmp_name'][$i];
			$uploadError = $_FILES['uploads']['error'][$i];
			
			if(! $fileData = igFileHelper::processUploadedImage($fileName, $tmpPath, $uploadError, 'igallery_img', true) )
			{
				$this->setRedirect('index.php?option=com_igallery&controller=igphoto&catid='.JRequest::getInt('catid'));
				return false;
			}
			
			$model = $this->getModel();
			
			if (!$model->store($fileData) ) 
			{
				return false;
			}
		}
		
		$this->setRedirect( JRoute::_('index.php?option=com_igallery&view=images&catid='.JRequest::getInt('catid'), false) );
	}
	
	function publish()
	{
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		for($i=0; $i<count($cid); $i++)
		{
			if(!igGeneralHelper::authorise('core.edit.state', null, $cid[$i]))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
		
		$this->view_list = 'images&catid='.JRequest::getInt('catid');
		parent::publish();
	}
	
	function reorder()
	{	
		$cid = JRequest::getVar('cid', array(), '', 'array');
		
		if(!igGeneralHelper::authorise('core.edit.state', null, (int)$cid[0]))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$this->view_list = 'images&catid='.JRequest::getInt('catid');
		parent::reorder();
	}
	
	function saveorder()
	{
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		for($i=0; $i<count($cid); $i++)
		{
			if(!igGeneralHelper::authorise('core.edit.state', null, $cid[$i]))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
		
		$this->view_list = 'images&catid='.JRequest::getInt('catid');
		parent::saveorder();
	}
	
	function delete()
	{
		if(!igGeneralHelper::authorise('core.delete', JRequest::getInt('catid', 0) ) )
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$this->view_list = 'images&catid='.JRequest::getInt('catid');
		parent::delete();
	}
	
	function rotate()
	{
		if(!igGeneralHelper::authorise('core.edit.state', JRequest::getInt('catid', 0) ) )
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model = $this->getModel();
	
		if( !$model->rotate() ) 
		{
			$msg = '';
			JError::raise(2, 500, $model->getError() );
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_igallery&view=images&catid='.JRequest::getInt('catid'), false) );
	}
	
	function assignMenuImage()
	{
		if(!igGeneralHelper::authorise('core.edit.state', JRequest::getInt('catid', 0) ) )
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model = $this->getModel();
	
		if( !$model->assignMenuImage() ) 
		{
			JError::raise(2, 500, $model->getError() );
		}
		
		$this->setRedirect( JRoute::_('index.php?option=com_igallery&view=images&catid='.JRequest::getInt('catid'), false) );
	}
	
	function moderate()
	{
		if(!igGeneralHelper::authorise('core.admin'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model = $this->getModel();
		if(!$model->moderate(1)) 
		{
			JError::raiseError(2, 500, $model->getError() );
		}
		
		$this->setRedirect('index.php?option=com_igallery&view=images&catid='.JRequest::getInt('catid'));
	}
	
	
	function unmoderate()
	{
		if(!igGeneralHelper::authorise('core.admin'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$model = $this->getModel();
		if(!$model->moderate(0)) 
		{
			JError::raiseError(2, 500, $model->getError() );
		}
		
		$this->setRedirect('index.php?option=com_igallery&view=images&catid='.JRequest::getInt('catid'));
	}
	
	public function setRedirect($url, $msg = null, $type = null)
	{
		if( JFactory::getApplication()->isSite() == true )
		{
			if(!strpos($url,'Itemid'))
			{
				$url = $url.'&Itemid='.JRequest::getInt('Itemid', '');
			}
		}
		
		parent::setRedirect($url, $msg, $type);
	}

}	