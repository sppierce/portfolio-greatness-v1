<?php
/**
@licenseGNU General Public License version 2 or later; see LICENSE.txt
Author: Shape 5 - Professional Template Community
Available for download at www.shape5.com
Copyright Shape5 LLC
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$LiveSite = JURI::base();

$custom_class = $params->get( 'custom_class' );
$mouse_effect_tabs = $params->get( 'mouse_effect_tabs' );
$mouse_effect_arrows = $params->get( 'mouse_effect_arrows' );
$tab1_text = $params->get( 'tab1_text' );
$tab2_text = $params->get( 'tab2_text' );
$tab3_text = $params->get( 'tab3_text' );
$tab4_text = $params->get( 'tab4_text' );
$tab5_text = $params->get( 'tab5_text' );
$tab6_text = $params->get( 'tab6_text' );
$tab7_text = $params->get( 'tab7_text' );
$tab8_text = $params->get( 'tab8_text' );
$tab9_text = $params->get( 'tab9_text' );
$tab10_text = $params->get( 'tab10_text' );
$tab11_text = $params->get( 'tab11_text' );
$tab12_text = $params->get( 'tab12_text' );
$tab13_text = $params->get( 'tab13_text' );
$tab14_text = $params->get( 'tab14_text' );
$tab15_text = $params->get( 'tab15_text' );
$tab16_text = $params->get( 'tab16_text' );
$tab17_text = $params->get( 'tab17_text' );
$tab18_text = $params->get( 'tab18_text' );
$tab19_text = $params->get( 'tab19_text' );
$tab20_text = $params->get( 'tab20_text' );
$tab1_img = $params->get( 'tab1_img' );
$tab2_img = $params->get( 'tab2_img' );
$tab3_img = $params->get( 'tab3_img' );
$tab4_img = $params->get( 'tab4_img' );
$tab5_img = $params->get( 'tab5_img' );
$tab6_img = $params->get( 'tab6_img' );
$tab7_img = $params->get( 'tab7_img' );
$tab8_img = $params->get( 'tab8_img' );
$tab9_img = $params->get( 'tab9_img' );
$tab10_img = $params->get( 'tab10_img' );
$tab11_img = $params->get( 'tab11_img' );
$tab12_img = $params->get( 'tab12_img' );
$tab13_img = $params->get( 'tab13_img' );
$tab14_img = $params->get( 'tab14_img' );
$tab15_img = $params->get( 'tab15_img' );
$tab16_img = $params->get( 'tab16_img' );
$tab17_img = $params->get( 'tab17_img' );
$tab18_img = $params->get( 'tab18_img' );
$tab19_img = $params->get( 'tab19_img' );
$tab20_img = $params->get( 'tab20_img' );
$tab_show_positions = $params->get( 'tab_show_positions' );
$opacity = $params->get( 'opacity' );
$slide_snap = $params->get( 'slide_snap' );
$duration = $params->get( 'duration' );
$pause_onhover = $params->get( 'pause_onhover' );
$button_location = $params->get( 'button_location' );
$arrow_location = $params->get( 'arrow_location' );
$padding = $params->get( 'padding' );
$hide_tabs = $params->get( 'hide_tabs' );
$hide_arrows = $params->get( 'hide_arrows' );
$show_mobile_tabs = $params->get( 'show_mobile_tabs' );
$show_mobile_arrows = $params->get( 'show_mobile_arrows' );
$pretext_inside = $params->get( 'pretext_inside' );
$posttext_inside = $params->get( 'posttext_inside' );
$pretext_outside = $params->get( 'pretext_outside' );
$posttext_outside = $params->get( 'posttext_outside' );
$center_arrows = $params->get( 'center_arrows' );
$center_tabs = $params->get( 'center_tabs' );
$tab_text_hover = $params->get( 'tab_text_hover' );
$tab1_background = $params->get( 'tab1_background' );
$tab1_background_color = $params->get( 'tab1_background_color' );
if ($tab1_background_color[0] != "#") {$tab1_background_color = "#".$tab1_background_color;}
$tab1_background_image = $params->get( 'tab1_background_image' );
$tab1_background_image_repeat = $params->get( 'tab1_background_image_repeat' );
$tab1_background_image_position = $params->get( 'tab1_background_image_position' );
$tab1_background_image_size = $params->get( 'tab1_background_image_size' );
$tab1_background_image_size_custom = $params->get( 'tab1_background_image_size_custom' );
$tab1_background_image_attachment = $params->get( 'tab1_background_image_attachment' );
$tab2_background = $params->get( 'tab2_background' );
$tab2_background_color = $params->get( 'tab2_background_color' );
if ($tab2_background_color[0] != "#") {$tab2_background_color = "#".$tab2_background_color;}
$tab2_background_image = $params->get( 'tab2_background_image' );
$tab2_background_image_repeat = $params->get( 'tab2_background_image_repeat' );
$tab2_background_image_position = $params->get( 'tab2_background_image_position' );
$tab2_background_image_size = $params->get( 'tab2_background_image_size' );
$tab2_background_image_size_custom = $params->get( 'tab2_background_image_size_custom' );
$tab2_background_image_attachment = $params->get( 'tab2_background_image_attachment' );
$tab3_background = $params->get( 'tab3_background' );
$tab3_background_color = $params->get( 'tab3_background_color' );
if ($tab3_background_color[0] != "#") {$tab3_background_color = "#".$tab3_background_color;}
$tab3_background_image = $params->get( 'tab3_background_image' );
$tab3_background_image_repeat = $params->get( 'tab3_background_image_repeat' );
$tab3_background_image_position = $params->get( 'tab3_background_image_position' );
$tab3_background_image_size = $params->get( 'tab3_background_image_size' );
$tab3_background_image_size_custom = $params->get( 'tab3_background_image_size_custom' );
$tab3_background_image_attachment = $params->get( 'tab3_background_image_attachment' );
$tab4_background = $params->get( 'tab4_background' );
$tab4_background_color = $params->get( 'tab4_background_color' );
if ($tab4_background_color[0] != "#") {$tab4_background_color = "#".$tab4_background_color;}
$tab4_background_image = $params->get( 'tab4_background_image' );
$tab4_background_image_repeat = $params->get( 'tab4_background_image_repeat' );
$tab4_background_image_position = $params->get( 'tab4_background_image_position' );
$tab4_background_image_size = $params->get( 'tab4_background_image_size' );
$tab4_background_image_size_custom = $params->get( 'tab4_background_image_size_custom' );
$tab4_background_image_attachment = $params->get( 'tab4_background_image_attachment' );
$tab5_background = $params->get( 'tab5_background' );
$tab5_background_color = $params->get( 'tab5_background_color' );
if ($tab5_background_color[0] != "#") {$tab5_background_color = "#".$tab5_background_color;}
$tab5_background_image = $params->get( 'tab5_background_image' );
$tab5_background_image_repeat = $params->get( 'tab5_background_image_repeat' );
$tab5_background_image_position = $params->get( 'tab5_background_image_position' );
$tab5_background_image_size = $params->get( 'tab5_background_image_size' );
$tab5_background_image_size_custom = $params->get( 'tab5_background_image_size_custom' );
$tab5_background_image_attachment = $params->get( 'tab5_background_image_attachment' );
$tab6_background = $params->get( 'tab6_background' );
$tab6_background_color = $params->get( 'tab6_background_color' );
if ($tab6_background_color[0] != "#") {$tab6_background_color = "#".$tab6_background_color;}
$tab6_background_image = $params->get( 'tab6_background_image' );
$tab6_background_image_repeat = $params->get( 'tab6_background_image_repeat' );
$tab6_background_image_position = $params->get( 'tab6_background_image_position' );
$tab6_background_image_size = $params->get( 'tab6_background_image_size' );
$tab6_background_image_size_custom = $params->get( 'tab6_background_image_size_custom' );
$tab6_background_image_attachment = $params->get( 'tab6_background_image_attachment' );
$tab7_background = $params->get( 'tab7_background' );
$tab7_background_color = $params->get( 'tab7_background_color' );
if ($tab7_background_color[0] != "#") {$tab7_background_color = "#".$tab7_background_color;}
$tab7_background_image = $params->get( 'tab7_background_image' );
$tab7_background_image_repeat = $params->get( 'tab7_background_image_repeat' );
$tab7_background_image_position = $params->get( 'tab7_background_image_position' );
$tab7_background_image_size = $params->get( 'tab7_background_image_size' );
$tab7_background_image_size_custom = $params->get( 'tab7_background_image_size_custom' );
$tab7_background_image_attachment = $params->get( 'tab7_background_image_attachment' );
$tab8_background = $params->get( 'tab8_background' );
$tab8_background_color = $params->get( 'tab8_background_color' );
if ($tab8_background_color[0] != "#") {$tab8_background_color = "#".$tab8_background_color;}
$tab8_background_image = $params->get( 'tab8_background_image' );
$tab8_background_image_repeat = $params->get( 'tab8_background_image_repeat' );
$tab8_background_image_position = $params->get( 'tab8_background_image_position' );
$tab8_background_image_size = $params->get( 'tab8_background_image_size' );
$tab8_background_image_size_custom = $params->get( 'tab8_background_image_size_custom' );
$tab8_background_image_attachment = $params->get( 'tab8_background_image_attachment' );
$tab9_background = $params->get( 'tab9_background' );
$tab9_background_color = $params->get( 'tab9_background_color' );
if ($tab9_background_color[0] != "#") {$tab9_background_color = "#".$tab9_background_color;}
$tab9_background_image = $params->get( 'tab9_background_image' );
$tab9_background_image_repeat = $params->get( 'tab9_background_image_repeat' );
$tab9_background_image_position = $params->get( 'tab9_background_image_position' );
$tab9_background_image_size = $params->get( 'tab9_background_image_size' );
$tab9_background_image_size_custom = $params->get( 'tab9_background_image_size_custom' );
$tab9_background_image_attachment = $params->get( 'tab9_background_image_attachment' );
$tab10_background = $params->get( 'tab10_background' );
$tab10_background_color = $params->get( 'tab10_background_color' );
if ($tab10_background_color[0] != "#") {$tab10_background_color = "#".$tab10_background_color;}
$tab10_background_image = $params->get( 'tab10_background_image' );
$tab10_background_image_repeat = $params->get( 'tab10_background_image_repeat' );
$tab10_background_image_position = $params->get( 'tab10_background_image_position' );
$tab10_background_image_size = $params->get( 'tab10_background_image_size' );
$tab10_background_image_size_custom = $params->get( 'tab10_background_image_size_custom' );
$tab10_background_image_attachment = $params->get( 'tab10_background_image_attachment' );
$tab11_background = $params->get( 'tab11_background' );
$tab11_background_color = $params->get( 'tab11_background_color' );
if ($tab11_background_color[0] != "#") {$tab11_background_color = "#".$tab11_background_color;}
$tab11_background_image = $params->get( 'tab11_background_image' );
$tab11_background_image_repeat = $params->get( 'tab11_background_image_repeat' );
$tab11_background_image_position = $params->get( 'tab11_background_image_position' );
$tab11_background_image_size = $params->get( 'tab11_background_image_size' );
$tab11_background_image_size_custom = $params->get( 'tab11_background_image_size_custom' );
$tab11_background_image_attachment = $params->get( 'tab11_background_image_attachment' );
$tab12_background = $params->get( 'tab12_background' );
$tab12_background_color = $params->get( 'tab12_background_color' );
if ($tab12_background_color[0] != "#") {$tab12_background_color = "#".$tab12_background_color;}
$tab12_background_image = $params->get( 'tab12_background_image' );
$tab12_background_image_repeat = $params->get( 'tab12_background_image_repeat' );
$tab12_background_image_position = $params->get( 'tab12_background_image_position' );
$tab12_background_image_size = $params->get( 'tab12_background_image_size' );
$tab12_background_image_size_custom = $params->get( 'tab12_background_image_size_custom' );
$tab12_background_image_attachment = $params->get( 'tab12_background_image_attachment' );
$tab13_background = $params->get( 'tab13_background' );
$tab13_background_color = $params->get( 'tab13_background_color' );
if ($tab13_background_color[0] != "#") {$tab13_background_color = "#".$tab13_background_color;}
$tab13_background_image = $params->get( 'tab13_background_image' );
$tab13_background_image_repeat = $params->get( 'tab13_background_image_repeat' );
$tab13_background_image_position = $params->get( 'tab13_background_image_position' );
$tab13_background_image_size = $params->get( 'tab13_background_image_size' );
$tab13_background_image_size_custom = $params->get( 'tab13_background_image_size_custom' );
$tab13_background_image_attachment = $params->get( 'tab13_background_image_attachment' );
$tab14_background = $params->get( 'tab14_background' );
$tab14_background_color = $params->get( 'tab14_background_color' );
if ($tab14_background_color[0] != "#") {$tab14_background_color = "#".$tab14_background_color;}
$tab14_background_image = $params->get( 'tab14_background_image' );
$tab14_background_image_repeat = $params->get( 'tab14_background_image_repeat' );
$tab14_background_image_position = $params->get( 'tab14_background_image_position' );
$tab14_background_image_size = $params->get( 'tab14_background_image_size' );
$tab14_background_image_size_custom = $params->get( 'tab14_background_image_size_custom' );
$tab14_background_image_attachment = $params->get( 'tab14_background_image_attachment' );
$tab15_background = $params->get( 'tab15_background' );
$tab15_background_color = $params->get( 'tab15_background_color' );
if ($tab15_background_color[0] != "#") {$tab15_background_color = "#".$tab15_background_color;}
$tab15_background_image = $params->get( 'tab15_background_image' );
$tab15_background_image_repeat = $params->get( 'tab15_background_image_repeat' );
$tab15_background_image_position = $params->get( 'tab15_background_image_position' );
$tab15_background_image_size = $params->get( 'tab15_background_image_size' );
$tab15_background_image_size_custom = $params->get( 'tab15_background_image_size_custom' );
$tab15_background_image_attachment = $params->get( 'tab15_background_image_attachment' );
$tab16_background = $params->get( 'tab16_background' );
$tab16_background_color = $params->get( 'tab16_background_color' );
if ($tab16_background_color[0] != "#") {$tab16_background_color = "#".$tab16_background_color;}
$tab16_background_image = $params->get( 'tab16_background_image' );
$tab16_background_image_repeat = $params->get( 'tab16_background_image_repeat' );
$tab16_background_image_position = $params->get( 'tab16_background_image_position' );
$tab16_background_image_size = $params->get( 'tab16_background_image_size' );
$tab16_background_image_size_custom = $params->get( 'tab16_background_image_size_custom' );
$tab16_background_image_attachment = $params->get( 'tab16_background_image_attachment' );
$tab17_background = $params->get( 'tab17_background' );
$tab17_background_color = $params->get( 'tab17_background_color' );
if ($tab17_background_color[0] != "#") {$tab17_background_color = "#".$tab17_background_color;}
$tab17_background_image = $params->get( 'tab17_background_image' );
$tab17_background_image_repeat = $params->get( 'tab17_background_image_repeat' );
$tab17_background_image_position = $params->get( 'tab17_background_image_position' );
$tab17_background_image_size = $params->get( 'tab17_background_image_size' );
$tab17_background_image_size_custom = $params->get( 'tab17_background_image_size_custom' );
$tab17_background_image_attachment = $params->get( 'tab17_background_image_attachment' );
$tab18_background = $params->get( 'tab18_background' );
$tab18_background_color = $params->get( 'tab18_background_color' );
if ($tab18_background_color[0] != "#") {$tab18_background_color = "#".$tab18_background_color;}
$tab18_background_image = $params->get( 'tab18_background_image' );
$tab18_background_image_repeat = $params->get( 'tab18_background_image_repeat' );
$tab18_background_image_position = $params->get( 'tab18_background_image_position' );
$tab18_background_image_size = $params->get( 'tab18_background_image_size' );
$tab18_background_image_size_custom = $params->get( 'tab18_background_image_size_custom' );
$tab18_background_image_attachment = $params->get( 'tab18_background_image_attachment' );
$tab19_background = $params->get( 'tab19_background' );
$tab19_background_color = $params->get( 'tab19_background_color' );
if ($tab19_background_color[0] != "#") {$tab19_background_color = "#".$tab19_background_color;}
$tab19_background_image = $params->get( 'tab19_background_image' );
$tab19_background_image_repeat = $params->get( 'tab19_background_image_repeat' );
$tab19_background_image_position = $params->get( 'tab19_background_image_position' );
$tab19_background_image_size = $params->get( 'tab19_background_image_size' );
$tab19_background_image_size_custom = $params->get( 'tab19_background_image_size_custom' );
$tab19_background_image_attachment = $params->get( 'tab19_background_image_attachment' );
$tab20_background = $params->get( 'tab20_background' );
$tab20_background_color = $params->get( 'tab20_background_color' );
if ($tab20_background_color[0] != "#") {$tab20_background_color = "#".$tab20_background_color;}
$tab20_background_image = $params->get( 'tab20_background_image' );
$tab20_background_image_repeat = $params->get( 'tab20_background_image_repeat' );
$tab20_background_image_position = $params->get( 'tab20_background_image_position' );
$tab20_background_image_size = $params->get( 'tab20_background_image_size' );
$tab20_background_image_size_custom = $params->get( 'tab20_background_image_size_custom' );
$tab20_background_image_attachment = $params->get( 'tab20_background_image_attachment' );
$main_background = $params->get( 'main_background' );
$main_background_color = $params->get( 'main_background_color' );
if ($main_background_color[0] != "#") {$main_background_color = "#".$main_background_color;}
$main_background_image = $params->get( 'main_background_image' );
$main_background_image_repeat = $params->get( 'main_background_image_repeat' );
$main_background_image_position = $params->get( 'main_background_image_position' );
$main_background_image_size = $params->get( 'main_background_image_size' );
$main_background_image_size_custom = $params->get( 'main_background_image_size_custom' );
$main_background_image_attachment = $params->get( 'main_background_image_attachment' );
$main_slider_background = $params->get( 'main_slider_background' );
$main_slider_background_color = $params->get( 'main_slider_background_color' );
if ($main_slider_background_color[0] != "#") {$main_slider_background_color = "#".$main_slider_background_color;}
$main_slider_background_image = $params->get( 'main_slider_background_image' );
$main_slider_background_image_repeat = $params->get( 'main_slider_background_image_repeat' );
$main_slider_background_image_position = $params->get( 'main_slider_background_image_position' );
$main_slider_background_image_size = $params->get( 'main_slider_background_image_size' );
$main_slider_background_image_size_custom = $params->get( 'main_slider_background_image_size_custom' );
$main_slider_background_image_attachment = $params->get( 'main_slider_background_image_attachment' );

require(JModuleHelper::getLayoutPath('mod_s5_tab_show'));