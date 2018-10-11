<?php
defined('_JEXEC') or die( 'Restricted access' );

class igalleryModeleditorbutton extends igalleryModelBase
{
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		return $item;
	}
	
	public function getTable($type = 'igallery_img', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFieldPath(IG_ADMINISTRATOR_COMPONENT.'/models/fields');
		$form = $this->loadForm('com_igallery.editorbutton', IG_ADMINISTRATOR_COMPONENT.'/models/forms/editorbutton.xml', array('control' => 'jform', 'load_data' => $loadData));
		
		if( empty($form) )
		{
			return false;
		}
		
		return $form;
	}
	
	protected function loadFormData()
	{
		$data = $this->getItem();
		return $data;
	}
}
