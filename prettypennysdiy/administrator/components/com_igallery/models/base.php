<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');

abstract class igalleryModelBase extends JModelAdmin
{
	function getCategory($id)
	{
        $db	= $this->getDbo();
		$query = 'SELECT * FROM #__igallery WHERE id = '.(int)$id;
        $db->setQuery($query);
		$category = $db->loadObject();
	    return $category;
    }
    
	function getProfile($id)
	{
        $db	= $this->getDbo();
		$query = 'SELECT * FROM #__igallery_profiles WHERE id = '.(int)$id;
        $db->setQuery($query);
		$profile = $db->loadObject();
	    return $profile;
    }		
}