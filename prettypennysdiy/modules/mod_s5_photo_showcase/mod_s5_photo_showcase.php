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
$max_width  = $params->get( 's5_max_width', '' );
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
$picture10  = $params->get( 's5_picture10', '' );
require(JModuleHelper::getLayoutPath('mod_s5_photo_showcase'));