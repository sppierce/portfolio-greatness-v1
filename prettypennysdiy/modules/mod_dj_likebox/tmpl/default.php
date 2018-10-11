<?php 
/**
* @version 2.0
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
defined('_JEXEC') or die('Restricted access'); ?>

<?php

if(!$params->get('add_css')) {
	
	if(!$params->get('footer')) {
		echo '<div style="overflow:hidden; height: '.($params->get('height') - 25).'px;">';
	}
	
	echo '<iframe src="//www.facebook.com/plugins/likebox.php?';
	echo 'href='.$params->get('href');
	echo '&amp;width='.$params->get('width');
	echo '&amp;colorscheme='.$params->get('colorscheme');
	echo '&amp;show_faces='.$params->get('show_faces');
	echo '&amp;stream='.$params->get('stream');
	echo '&amp;force_wall='.$params->get('force_wall');
	echo '&amp;show_border='.$params->get('show_border');
	echo '&amp;header='.$params->get('header');
	echo '&amp;height='.$params->get('height');
	echo '&amp;border_color='.str_replace('#','%23',$params->get('border_color'));
	echo '" scrolling="no" ';
	echo 'frameborder="0" style="border:none; overflow:hidden; width: '.$params->get('width').'px; ';
	if(!$params->get('footer')) echo ' margin: 0 -5px;';
	echo 'height: '.$params->get('height').'px;" allowTransparency="true"></iframe>';
	
	if(!$params->get('footer')) echo '</div>';

} else {
?>

<div id="fb-root"></div>
<script src="//connect.facebook.net/<?php echo $language_tag; ?>/all.js#xfbml=1"></script>
<fb:fan
	profile_id="<?php echo $params->get('profile_id'); ?>"
	width="<?php echo $params->get('width'); ?>"
	colorscheme="<?php echo $params->get('colorscheme'); ?>"
	connections="<?php echo $params->get('connections'); ?>"
	stream="<?php echo $params->get('stream'); ?>" 
	header="<?php echo $params->get('header'); ?>" 
	height="<?php echo $params->get('height'); ?>"
	css="<?php echo $css_path; ?>">
</fb:fan>

<?php } ?>


