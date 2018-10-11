<?php
/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */


defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_messages
 * @since		1.6
 */
class DroppicsHelper
{
    
        /**
	 * @var    JObject  A cache for the available actions.
	 * @since  1.6
	 */
	protected static $actions;
        
        
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */

	public static function addSubmenu($vName)
	{
//		JSubMenuHelper::addEntry(
//			JText::_('COM_MESSAGES_ADD'),
//			'index.php?option=com_messages&view=message&layout=edit',
//			$vName == 'message'
//		);
//
//		JSubMenuHelper::addEntry(
//			JText::_('COM_MESSAGES_READ'),
//			'index.php?option=com_messages',
//			$vName == 'messages'
//		);
	}


	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject
	 *
	 * @since   1.6
	 * @todo    Refactor to work with notes
	 */
	public static function getActions()
	{
		if (empty(self::$actions))
		{
			$user = JFactory::getUser();
			self::$actions = new JObject;

			$actions = JAccess::getActions('com_droppics');

			foreach ($actions as $action)
			{
				self::$actions->set($action->name, $user->authorise($action->name, 'com_droppics'));
			}
		}

		return self::$actions;
	}
	
}
