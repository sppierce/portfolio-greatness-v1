<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

$modid = $module->id;
$position = $params->get("position", "left");
$preloaded = $params->get("preload", 1) ? "true" : "false";
$user = JFactory::getUser();
$url = JURI::base(false) . "modules/mod_tabbedmodules/fetchmodule.php";

$tabtitles = array();
$tabcontents = array();

foreach (JModuleHelper::getModules($position) as $mod)
{
	if (!is_object($mod))
	{
		$mod = JModuleHelper::getModule($mod, $title);
	}

	if (is_object($mod))
	{

		// Latest Events Title fix!
		modTabbedmodulesHelper::fixLatestEventsModule($mod);

		$document = JFactory::getDocument();
		$renderer = $document->loadRenderer('module');
		$tempparams = array('style' => -2);
		// always preload the first tab
		if ($params->get("preload", 1) || count($tabtitles) == 0)
		{
			$mod->tabcontent = $renderer->render($mod, $tempparams);
		}
		else
		{
			$mod->tabcontent = "";
		}

		// wrap latest events module
		modTabbedmodulesHelper::wrapLatestEventsModule($mod);

		$tabtitles[] = $mod;
	}
}

// only output if there are any modules to display
if (count($tabtitles)==0) return "";

modTabbedmodulesHelper::script("tabs.js");
modTabbedmodulesHelper::stylesheet("mod_tabbedmodules.css");

$tabselection = $params->get("tabbedorselect", 0);

if ($tabselection == 1) {
	$taborselect = 'form';
	$taborselect1 = 'option';
} else {
	$taborselect = 'ul';
	$taborselect1 = 'li';
}


$html = '<div class="tab-page jevtabbedmodules-tab-page '.($tabselection==2?"jevtabslider":"").' " id="tabbedmodules-pane' . $modid . '">';
$html .= '<'.$taborselect.' class="moduletabs_title">';
if ($tabselection == 1) {
	$html .= '<select class="tabbed_events">';
}

$active = true;
$count = 0;
foreach ($tabtitles as $tabtitle)
{
	$title = isset($tabtitle->tabtitle) ? $tabtitle->tabtitle : $tabtitle->title;
	$html .= '<'.$taborselect1.' class=" ' . ($active ? 'active' : '') .  ($tabtitle->module=="mod_jevents_latest" ? ' events' : '') .' " title="' . addslashes(strip_tags($title)) . '"  id="tbmd' . $modid . "_" . $tabtitle->id . '" >' . $title . '</'.$taborselect1.'>';
	$count++;
	$active = false;
}
if ($tabselection == 1) {
	$html .= '</select>';
}
$html .= '</'.$taborselect.'>';
$active = true;
$count = 0;
foreach ($tabtitles as $tabtitle)
{
	if ($tabselection==2){
		$activeclass = $active ? 'onscreen': 'offscreen';
	}
	else {
		$activeclass = $active ? 'active' : 'inactive';
	}
	$html .= '<div class="moduletabs_panel ' . $activeclass . '">';
	$html .= '<div class="moduletabs_inner " id="tbmd' . $modid . "_" . $tabtitle->id . '_content">';
	$html .= $tabtitle->tabcontent;
	$html .= '</div >';
	$html .= '</div >';
	$active = false;
}

$html .= '</div >';
$html .= '<script type="text/javascript">var tabsOrselect = '.$tabselection.'; var modTabs = new moduletabs("tabbedmodules-pane' . $modid . '",{mouseOverClass:"active",	activateOnLoad:"tab0"	, url:"' . $url . '", "tabstyle":'.$tabselection.'}) ;</script>';echo $html;

