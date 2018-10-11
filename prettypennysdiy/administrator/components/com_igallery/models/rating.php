<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');

class igalleryModelRating extends JModelAdmin
{
	function getForm($data = array(), $loadData = true)
	{
		return null;
	}

	public function getTable($type = 'igallery_ratings', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
}
