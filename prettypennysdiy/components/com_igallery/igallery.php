<?php
/**
* @package		Ignite Gallery
* @copyright	Copyright (C) 2012 Matthew Thomson. All rights reserved.
* @license		GNU/GPLv2
*/

defined('_JEXEC') or die('Restricted access');

//load the backend language file, theres only one language file for the component
$lang =& JFactory::getLanguage();
$lang->load('com_igallery', JPATH_ADMINISTRATOR);

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/defines.php');

$task = JRequest::getCmd('task', 'display');
$view = JRequest::getCmd('view', 'category');

if( strpos($task,'.') )
{
	$task = substr($task, strpos($task,'.') + 1);
}

$frontendTasks = array('display','reportImage','addHit','download','addRating');
$taskMatch = false;
$frontend = false;

foreach($frontendTasks as $key => $value)
{
	if($value == $task)
	{
		$taskMatch = true;
		break;
	}
}

if($taskMatch == true)
{
	if($task == 'display')
	{
		if($view == 'category')
		{
			$frontend = true;
		}
	}
	else
	{
		$frontend = true;
	}
}

if($frontend == true)
{
	$controller	= JControllerLegacy::getInstance('Ig', array('default_view'=>'category'));
    $task = JRequest::getCmd('task');
    if( strpos($task, '.') )
    {
        $task = substr($task, strpos($task,'.') + 1);
    }
    $controller->execute($task);
	$controller->redirect();
}
else
{
	$app = JFactory::getApplication();
	$params = $app->getParams();
	
	if($params->get('allow_frontend_creation', 0) == 0)
	{
		return JError::raiseWarning(404, JText::_('PLEASE_ENABLE_FRONTEND'));
	}
	
	if( JFactory::getUser()->get('guest') )
	{
		return JError::raiseWarning(404, 'Please login to manage images from the frontend');
	}
	
	$document =& JFactory::getDocument();
	$document->addStyleSheet(IG_HOST.'media/com_igallery/css/admin.css');
	
	$lang->load('', JPATH_ADMINISTRATOR);
	$lang->load('lib_joomla', JPATH_ADMINISTRATOR);
	
	jimport('joomla.html.toolbar');
	
	require_once(JPATH_ADMINISTRATOR.'/includes/toolbar.php');
	require_once(IG_ADMINISTRATOR_COMPONENT.'/igallery.php');
}
?>