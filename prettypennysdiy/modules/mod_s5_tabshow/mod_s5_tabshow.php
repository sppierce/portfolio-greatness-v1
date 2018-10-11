<?php
/**
@version 1.0: mod_S5_tabshow
Author: Shape 5 - Professional Template Community
Available for download at www.shape5.com
Copyright Shape 5 LLC
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$LiveSite = JURI::base();
$pretext		= $params->get( 'pretext', '' );
$buttonheight	= $params->get( 's5_buttonheight', '' );
$lineheight		= $params->get( 's5_lineheight', '' );
$text1line		= $params->get( 'text1line', '' );
$text2line		= $params->get( 'text2line', '' );
$text3line		= $params->get( 'text3line', '' );
$text4line		= $params->get( 'text4line', '' );
$text5line		= $params->get( 'text5line', '' );
$text6line		= $params->get( 'text6line', '' );
$text7line		= $params->get( 'text7line', '' );
$text8line		= $params->get( 'text8line', '' );
$text9line		= $params->get( 'text9line', '' );
$text10line		= $params->get( 'text10line', '' );
$s5_buttoncolor = $params->get( 's5_buttoncolor', '' );
$s5_hoverimage = $params->get( 's5_hoverimage', '' );
$s5_hovercolor = $params->get( 's5_hovercolor', '' );
$s5_buttonimage = $params->get( 's5_buttonimage', '' );
$width  = $params->get( 's5_width', '' );
$display_time  = $params->get( 's5_display_time', '' );
$s5_buttoncolumnwidth = ($width) - 10;
$s5_contentwidth = ($width - $s5_buttoncolumnwidth) - 60;
$s5_aligncolumn = $params->get( 's5_aligncolumn', '' );
$s5_fontcolor = $params->get( 's5_fontcolor', '' );
$s5_javascript = $params->get( 's5_javascript', '' );
$s5_mootoolsmouse = $params->get( 's5_mootoolsmouse', '' );
$s5_effectsani = $params->get( 's5_effectsani', '' );
$s5_effectmouse = $params->get( 's5_effectmouse', '' );

require(JModuleHelper::getLayoutPath('mod_s5_tabshow'));