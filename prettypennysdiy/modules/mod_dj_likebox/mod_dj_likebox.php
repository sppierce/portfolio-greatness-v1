<?php
/**
* @version 2.1
* @package DJ Like Box
* @copyright Copyright (C) 2010 Blue Constant Media LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
*
*
* DJ Like Box is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Like Box is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Like Box. If not, see <http://www.gnu.org/licenses/>.
*
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPath::clean(dirname(__FILE__).'/helper.php');

$params->set('href',urlencode($params->get('href','http://www.facebook.com/pages/Joomla-Monster/315021681227')));
$params->set('width',$params->get('width',300));
$params->set('colorscheme',$params->get('colorscheme','light'));
$params->set('show_faces',$params->get('show_faces',1) ? 'true' : 'false');
$params->set('stream',$params->get('stream',0) ? 'true' : 'false');
$params->set('force_wall',$params->get('force_wall',0) ? 'true' : 'false');
$params->set('header',$params->get('header',0) ? 'true' : 'false');
$params->set('footer',$params->get('footer',0) ? 1:0);
$params->set('show_border',$params->get('footer',0) ? 'true' : 'false');
$params->set('border_color',$params->get('border_color',''));
$params->set('add_css',$params->get('add_css',0));

if($params->get('add_css')) {
	$language = JFactory::getLanguage();
	$language_tag = str_replace('-', '_', $language->getTag());
	
	$css_path = modDJLikeBoxHelper::getStyleSheetPath($params);

	if(preg_match('/(^|\/)(\d+)$/', urldecode($params->get('href')), $matches)){
		$params->set('profile_id',$matches[2]);
		if($params->get('show_faces')=='true') {
			$params->set('connections',50);
		} else {
			$params->set('connections',0);
		}		
	} else {
		$params->set('add_css',0);
	}	
}

// calculate height if not specified
if(!$params->get('height',0)){
	$height = 571;
	if($params->get('stream')=='false') $height -= 300;
	if($params->get('show_faces')=='false') {
		if($params->get('add_css')) {
			$height -= 168;
		} else {
			$height -= 168;
		}
	}
	if($params->get('header')=='false' || $params->get('add_css')) $height -= 30;
	$params->set('height',$height);
}

require JModuleHelper::getLayoutPath('mod_dj_likebox', $params->get('layout', 'default'));












