<?php
/**
* @package		Ignite Gallery
* @copyright	Copyright (C) 2011 Matthew Thomson. All rights reserved.
* @license		GNU/GPLv2
*/

defined('_JEXEC') or die;

if(!JFactory::getUser()->authorise('core.manage', 'com_igallery'))
{
	return JError::raiseWarning(404, JText::_('Please go to the component options and enable the "Access Administration Interface" permission for this group'));
}

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/defines.php');

$controller	= JControllerLegacy::getInstance('Igallery', array('default_view'=>'categories','base_path' => IG_ADMINISTRATOR_COMPONENT) );
$task = JRequest::getCmd('task', 'display');
if( strpos($task,'.') )
{
    $task = substr($task, strpos($task,'.') + 1);
}
$controller->execute($task);
$controller->redirect();
?>