<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class igalleryControllerImage extends JControllerAdmin
{
	public function &getModel($name = 'Image', $prefix = 'IgalleryModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function plUpload()
	{
		if(!igGeneralHelper::authorise('core.create', JRequest::getInt('catid', 0) ) )
		{
			echo JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		}
		
		$fileName = $_FILES['file']['name'];
		$tmpPath = $_FILES['file']['tmp_name'];
		$uploadError = $_FILES['file']['error'];
		
		if(! $fileData = igFileHelper::processUploadedImage($fileName, $tmpPath, $uploadError, 'igallery_img', false) )
		{
			return false;
		}
		
		$model = $this->getModel();
		
		if (!$model->store($fileData) ) 
		{
			return false;
		}
		
		echo 1;
	}
	
	function serverImport()
	{
		if(!igGeneralHelper::authorise('core.admin'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$filePathRaw = JRequest::GetVar('path');
		$filePath = str_replace('*','\\',$filePathRaw);
		
		if(! $fileData = igFileHelper::processImportedImage($filePath) )
		{
			return false;
		}
		
		$model = $this->getModel();
		
		if (!$model->store($fileData) ) 
		{
			echo $model->getError();
			return false;
		}
		
		echo 1;
	}
}