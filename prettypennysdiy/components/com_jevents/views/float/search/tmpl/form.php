<?php 
defined('_JEXEC') or die('Restricted access');

if (file_exists(JEV_VIEWS."/flatplus/search/tmpl/".basename(__FILE__))) {
	include(JEV_VIEWS."/flatplus/search/tmpl/".basename(__FILE__)); 
} else {
	include(JEV_VIEWS."/flat/search/tmpl/".basename(__FILE__)); 
}
