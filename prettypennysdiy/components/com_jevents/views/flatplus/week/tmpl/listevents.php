<?php

defined('_JEXEC') or die('Restricted access');

$this->_header();
$this->_showNavTableBar();

$cfg = JEVConfig::getInstance();

if (intval($cfg->get('rollingweeks', 1) > 1)) {
    $rolling = "rolling";
} else {
    $rolling = "";
}
if ($cfg->get('fptabularweek', 0)) {
    echo $this->loadTemplate($rolling . "bodygridresponsive");
} else {
    echo $this->loadTemplate($rolling . "responsive");
    
}

$this->_viewNavAdminPanel();

$this->_footer();


