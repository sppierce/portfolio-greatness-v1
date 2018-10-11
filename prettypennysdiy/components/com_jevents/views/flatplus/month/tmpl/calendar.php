<?php 
defined('_JEXEC') or die('Restricted access');

$this->_header();

$this->_showNavTableBar();

$cfg	 = JEVConfig::getInstance();

if ($cfg->get('fplistmonth',0)){
	echo $this->loadTemplate("bodylist");
}
else {
	echo $this->loadTemplate("responsive");	
}

$this->_viewNavAdminPanel();

$this->_footer();