<?php

/**

 * @version		$Id: mod_menu.php 19594 2010-11-20 05:06:08Z ian $

 * @package		Joomla.Site

 * @subpackage	mod_menu

 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.

 * @license		GNU General Public License version 2 or later; see LICENSE.txt

 */



// no direct access

defined('_JEXEC') or die;

// Include the syndicate functions only onceif ( !defined( 'DS')) define('DS', DIRECTORY_SEPARATOR);

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php';



$list	= modMenuHelper2::getList($params);

$app	= JFactory::getApplication();

$menu	= $app->getMenu();

$active	= $menu->getActive();

$active_id = isset($active) ? $active->id : $menu->getDefault()->id;

$path	= isset($active) ? $active->tree : array();

$showAll	= $params->get('showAllChildren'); 

$closed_or_open	= $params->get('closed_or_open'); 

if (!$closed_or_open) {
$closed_or_open = "open";
}

$parentlinks = $params->get('parentlinks');



require(JModuleHelper::getLayoutPath('mod_s5_accordion_menu'));

$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser.



$browser = "other";



if(strrpos($br,"msie 6") > 1) {

$browser = "ie6";

} 

if(strrpos($br,"msie 7") > 1) {

$browser = "ie7";

} 



$version = new JVersion();



if($version->RELEASE <= '2.5'){

JHTML::_('behavior.mootools');}



if($version->RELEASE >= '3.0'){

JHtml::_('jquery.framework');}

$template_vertex = "no";

$app = JFactory::getApplication();

$template = $app->getTemplate();

$template_json_location = $_SERVER['DOCUMENT_ROOT'].JURI::root(true).'/templates/'.$template.'/vertex.json';

if(file_exists($template_json_location)) { 

$template_vertex = "yes";

}

?>



<script type="text/javascript">			

var s5_am_parent_link_enabled = "<?php echo $parentlinks ?>";	

var s5_closed_or_open = "<?php echo $closed_or_open ?>";

<?php if ($browser == "ie6" || $browser == "ie7") { ?>

var s5_accordion_menu_display = "inline";

<?php } ?>	

<?php if ($browser != "ie6" && $browser != "ie7") { ?>

var s5_accordion_menu_display = "block";

<?php } ?>		

</script>



<?php if($version->RELEASE <= '2.5'){ ?>

 
		<script src="<?php echo $mod_s5_accordionurl?>js/s5_accordion_menu.js" type="text/javascript"></script>	


<?php } ?>	



<?php if($version->RELEASE >= '3.0'){ ?>

	<?php if($template_vertex == "no"){ ?>

	<script type="text/javascript">//<![CDATA[

	if(jQuery.easing.easeOutExpo==undefined){

    document.write('<script src="<?php echo $mod_s5_accordionurl; ?>js/jquery-ui.min.js"><\/script>');

    }

	//]]></script>

	<?php } ?>	

<script src="<?php echo $mod_s5_accordionurl?>js/s5_accordion_menu_jquery.js" type="text/javascript"></script>

<script type="text/javascript">jQuery.noConflict();</script>

<?php } ?>	
