<?php /**
 * @title		Shape 5 Box Module
 * @version		1.0
 * @package		Joomla
 * @website		http://www.shape5.com
 * @copyright	Copyright (C) 2009 Shape 5 LLC. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
$url = JURI::root().'modules/mod_s5_box/';
print '<script type="text/javascript" src="'.$url.'js/s5_box_hide_div.js"></script>';
$s5_boxeffect = $params->get('s5_boxeffect', '');
$s5_jsversion = $params->get('s5_jsversion', '');
$version = new JVersion();
if($version->RELEASE >= '3.0') {
  $s5_jsversion = "jquery";
}
for($i=1;$i<13;$i++) {
  $k = 's5boxwidth'.$i;
  $v = (int)$params->get($k, '');
  $$k = $v.($v <= 100 ? '%' : 'px');
}
require (JModuleHelper::getLayoutPath('mod_s5_box'));