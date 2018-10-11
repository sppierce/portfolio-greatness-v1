<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');

class igalleryModelprofiles extends JModelList
{
	function getListQuery()
	{
		$query = 'SELECT * from #__igallery_profiles ORDER BY ordering';
		return $query;
	}
}