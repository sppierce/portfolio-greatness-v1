<?php
/**
 * @version		$Id: default_separator.php 19594 2010-11-20 05:06:08Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
$title = $item->params->get('menu-anchor_title', '') ? 'title="'.$item->params->get('menu-anchor_title', '').'" ' : '';
if ($item->params->get('menu_image', '')) {
		$item->params->get('menu_text', 1 ) ? 
		$linktype = '<img src="'.$item->params->get('menu_image', '').'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' :
		$linktype = '<img src="'.$item->params->get('menu_image', '').'" alt="'.$item->title.'" />';
} 
else { $linktype = $item->title;
}

?><span class="s5_accordion_menu_left"><a href="javascript:;" class="mainlevel"><?php echo $title; ?><span><?php echo $linktype; ?></span></a></span>