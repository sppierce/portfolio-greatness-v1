<?php
/**
 * @package      ITPSocialSubscribe
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

JLoader::register('ItpSocialSubscribeHelper', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helper.php');

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

if ($params->get("loadCss")) {

    $doc = JFactory::getDocument();
    /** $doc JDocumentHTML **/

    $doc->addStyleSheet("modules/mod_itpsocialsubscribe/style.css");
}

require JModuleHelper::getLayoutPath('mod_itpsocialsubscribe', $params->get('layout', 'default'));
