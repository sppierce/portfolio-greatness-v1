<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class Tableigallery_ratings extends JTable
{
	function Tableigallery_ratings(& $db)
	{
		parent::__construct('#__igallery_ratings', 'id', $db);
	}

}
?>