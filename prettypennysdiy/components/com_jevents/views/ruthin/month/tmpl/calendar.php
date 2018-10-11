<?php 
defined('_JEXEC') or die('Restricted access');

$this->_header();

$this->_showNavTableBar();

$cfg	 = JEVConfig::getInstance();

if ($cfg->get('rulistmonth',0)){
	echo $this->loadTemplate("bodylist");
}
else if ($cfg->get("ruscalable","1")=="1"){
	echo $this->loadTemplate("responsive");	
}
else {
	echo $this->loadTemplate("body");
}

$this->_viewNavAdminPanel();

$this->_footer();