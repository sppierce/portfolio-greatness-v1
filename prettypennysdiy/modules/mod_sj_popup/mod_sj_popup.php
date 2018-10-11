<?php
/**
 * @package Sj Popup
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 * 
 */
defined('_JEXEC') or die;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
$document = JFactory::getDocument();
require_once dirname( __FILE__ ).'/core/helper.php';
$layout = 'default';
$tag_id = 'sj-popup-'.$module->id;
if($params->get('position_popup') == 'fullsize'){
	$width = '100%';
}else{
	$width = $params->get('width_popup');
}
$margin = str_replace(',',' ',$params->get('margin_popup'));
$style = '#'.$tag_id.' .sj-popup{'
		.'width:'.$width.';'
		.'background:'.$params->get('color_popup').';'
		.'margin:'.$margin.';'
		.'}';
$document->addStyleDeclaration($style);
$document->addStyleDeclaration($params->get('css_popup'));
require JModuleHelper::getLayoutPath($module->module, $layout);?>