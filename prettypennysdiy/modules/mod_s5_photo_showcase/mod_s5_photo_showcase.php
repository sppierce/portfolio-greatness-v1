<?php
/**
@version 1.0: mod_S5_photo_showcase
Author: Shape 5 - Professional Template Community
Available for download at www.shape5.com
Copyright Shape 5 LLC
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$LiveSite = JURI::base();
$width  = $params->get( 's5_width', '' );
$max_width  = $params->get( 's5_max_width', '' );$background  = $params->get( 's5_background', '' );$bars_background  = $params->get( 's5_bars_background', '' );
$display_time  = $params->get( 's5_display_time', '' );
$picture1  = $params->get( 's5_picture1', '' );
$picture2  = $params->get( 's5_picture2', '' );
$picture3  = $params->get( 's5_picture3', '' );
$picture4  = $params->get( 's5_picture4', '' );
$picture5  = $params->get( 's5_picture5', '' );
$picture6  = $params->get( 's5_picture6', '' );
$picture7  = $params->get( 's5_picture7', '' );
$picture8  = $params->get( 's5_picture8', '' );
$picture9  = $params->get( 's5_picture9', '' );
$picture10  = $params->get( 's5_picture10', '' );$picture11  = $params->get( 's5_picture11', '' );$picture12  = $params->get( 's5_picture12', '' );$picture13  = $params->get( 's5_picture13', '' );$picture14  = $params->get( 's5_picture14', '' );$picture15  = $params->get( 's5_picture15', '' );$content_type  = $params->get( 's5_content_type', '' );$picture1_title  = $params->get( 's5_picture1_title', '' );$picture1_text  = $params->get( 's5_picture1_text', '' );$picture2_title  = $params->get( 's5_picture2_title', '' );$picture2_text  = $params->get( 's5_picture2_text', '' );$picture3_title  = $params->get( 's5_picture3_title', '' );$picture3_text  = $params->get( 's5_picture3_text', '' );$picture4_title  = $params->get( 's5_picture4_title', '' );$picture4_text  = $params->get( 's5_picture4_text', '' );$picture5_title  = $params->get( 's5_picture5_title', '' );$picture5_text  = $params->get( 's5_picture5_text', '' );$picture6_title  = $params->get( 's5_picture6_title', '' );$picture6_text  = $params->get( 's5_picture6_text', '' );$picture7_title  = $params->get( 's5_picture7_title', '' );$picture7_text  = $params->get( 's5_picture7_text', '' );$picture8_title  = $params->get( 's5_picture8_title', '' );$picture8_text  = $params->get( 's5_picture8_text', '' );$picture9_title  = $params->get( 's5_picture9_title', '' );$picture9_text  = $params->get( 's5_picture9_text', '' );$picture10_title  = $params->get( 's5_picture10_title', '' );$picture10_text  = $params->get( 's5_picture10_text', '' );$picture11_title  = $params->get( 's5_picture11_title', '' );$picture11_text  = $params->get( 's5_picture11_text', '' );$picture12_title  = $params->get( 's5_picture12_title', '' );$picture12_text  = $params->get( 's5_picture12_text', '' );$picture13_title  = $params->get( 's5_picture13_title', '' );$picture13_text  = $params->get( 's5_picture13_text', '' );$picture14_title  = $params->get( 's5_picture14_title', '' );$picture14_text  = $params->get( 's5_picture14_text', '' );$picture15_title  = $params->get( 's5_picture15_title', '' );$picture15_text  = $params->get( 's5_picture15_text', '' );$text_background  = $params->get( 's5_text_background', '' );$text_padding  = $params->get( 's5_text_padding', '' );$text_color  = $params->get( 's5_text_color', '' );$title_color  = $params->get( 's5_title_color', '' );$text_size  = $params->get( 's5_text_size', '' );$title_size  = $params->get( 's5_title_size', '' );$title_margin  = $params->get( 's5_title_margin', '' );$title_bold  = $params->get( 's5_title_bold', '' );$text_line_height  = $params->get( 's5_text_line_height', '' );
require(JModuleHelper::getLayoutPath('mod_s5_photo_showcase'));