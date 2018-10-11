<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class Tableigallery_img extends JTable
{
	function Tableigallery_img(& $db) 
	{
		parent::__construct('#__igallery_img', 'id', $db);
	}

}
?>
