<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: mod_jevents_latest.php 1407 2010-11-09 12:03:42Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module Latest JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (JPATH_SITE.'/modules/mod_tabbedmodules/helper.php');

JLoader::register('JEVConfig',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/config.php");
$jevcfg = JEVConfig::getInstance();
$jevcfg->set('dateformat', $params->get("dateformat",""));

// Should we use Bootstrap tabs
if (version_compare(JVERSION, "3.3", "ge") && $params->get("tabbedorselect", 0)==0  && $params->get("usebootstrap", 1) ){
	require(JModuleHelper::getLayoutPath('mod_tabbedmodules', "default_bootstrap"));
}
else {
	require(JModuleHelper::getLayoutPath('mod_tabbedmodules', "default"));
}