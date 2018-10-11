<?php
/**
 * @version		$Id: default_component.php 19594 2010-11-20 05:06:08Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
$class = $item->params->get('menu-anchor_css', '') ? 'class="'.$item->params->get('menu-anchor_css', '').'" ' : '';
$title = $item->params->get('menu-anchor_title', '') ? 'title="'.$item->params->get('menu-anchor_title', '').'" ' : '';
if ($item->params->get('menu_image', '')) {
		$item->params->get('menu_text', 1 ) ? 
		$linktype = '<img src="'.$item->params->get('menu_image', '').'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' :
		$linktype = '<img src="'.$item->params->get('menu_image', '').'" alt="'.$item->title.'" />';
} 
else { $linktype = $item->title;
}
//$linktype = $item->params->get('menu_image', '') && $item->params->get('menu_text', 1 ) ? '<img src="'.$item->params->get('menu_image', '').'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' : $item->title;

switch ($item->browserNav) :
	default:
	case 0:
?><span class="s5_accordion_menu_left"><a class="mainlevel" href="<?php echo $item->flink; ?>" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a></span><?php
		break;
	case 1:
		// _blank
?><span class="s5_accordion_menu_left"><a class="mainlevel" href="<?php echo $item->flink; ?>" target="_blank" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a></span><?php
		break;
	case 2:
		// window.open
?><span class="s5_accordion_menu_left"><a class="mainlevel handleopen" href='javascript:void(0);'  href2="<?php echo $item->flink; ?>"   <?php echo $title; ?>><span><?php echo $linktype; ?></span></a></span>
<?php
		break;
endswitch;
