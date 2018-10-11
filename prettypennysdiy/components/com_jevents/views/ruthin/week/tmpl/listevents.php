<?php 
defined('_JEXEC') or die('Restricted access');

$this->_header();
$this->_showNavTableBar();

$cfg	 = JEVConfig::getInstance();

if (intval($cfg->get('rollingweeks',1)>1)){
	$rolling = "rolling";
}
else {
	$rolling = "";
}

if ($cfg->get('ruscalable',0)==1 || $cfg->get("ruthinwidth",905)=="scalable"){
	if ($cfg->get('rutabularweek',0) ){
		echo $this->loadTemplate($rolling."bodygridresponsive");
	}
	else {
		echo $this->loadTemplate($rolling."responsive");
	}
}
else if ($cfg->get('rutabularweek',0) ){
	echo $this->loadTemplate($rolling."bodygrid");
}
else {
	echo $this->loadTemplate($rolling."body");
}

$this->_viewNavAdminPanel();

$this->_footer();


