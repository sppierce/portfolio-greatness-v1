<?php
defined('_JEXEC') or die('Restricted access');

$this->_header();
//Hide Navbar in admin view
//$this->_showNavTableBar();

$user =  JFactory::getUser();
if (JEVHelper::isEventCreator()) {
	echo $this->loadTemplate("body");
}

$this->_viewNavAdminPanel();

$this->_footer();


