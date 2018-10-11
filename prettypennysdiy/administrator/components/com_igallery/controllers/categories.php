<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class igalleryControllerCategories extends JControllerAdmin
{
    public function &getModel($name = 'Icategory', $prefix = 'IgalleryModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function add_category_redirect()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_igallery&view=icategory', false) );
	}
	
	function copy()
	{
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
		
		$this->setRedirect( JRoute::_('index.php?option=com_igallery&view=categories'.$this->Itemid, false), $msg);
	}
	
	function delete()
	{
		$cid = JRequest::getVar('cid', array(), '', 'array');
		
		if(!igGeneralHelper::authorise('core.delete', (int)$cid[0]))
		{
			return JError::raiseWarning(404, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
		}
		
		parent::delete();
	}
	
	function publish()
	{
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		for($i=0; $i<count($cid); $i++)
		{
			if(!igGeneralHelper::authorise('core.edit.state', $cid[$i]))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
		
		parent::publish();
		
	}
	
	function saveorder()
	{
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		
		for($i=0; $i<count($cid); $i++)
		{
			if(!igGeneralHelper::authorise('core.edit.state', $cid[$i]))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
		
		parent::saveorder();
		
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
		
		$this->setRedirect( JRoute::_('index.php?option=com_igallery&view=categories', false) );
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
		
		$this->setRedirect( JRoute::_('index.php?option=com_igallery&view=categories', false) );
	}
	
	
}