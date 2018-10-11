<?php 
defined('_JEXEC') or die('Restricted access');

$this->_header();

$this->_showNavTableBar();

$cfg	 = JEVConfig::getInstance();
if ($cfg->get('eplistmonth',0)){
	echo $this->loadTemplate("bodylist");
}
else if ($cfg->get('epscalable',0)==1 || $cfg->get("extpluswidth",905)=="scalable"){
	echo $this->loadTemplate("responsive");	
}
else {
	echo $this->loadTemplate("body");
}

$this->_viewNavAdminPanel();

$this->_footer();

