<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class IgController extends JControllerLegacy
{
	function __construct($config = array())
	{	
		$config['base_path'] = JPATH_SITE.'/components/com_igallery';
		parent::__construct($config);
	}
	
	function display()
	{
		parent::display();
	}
}
