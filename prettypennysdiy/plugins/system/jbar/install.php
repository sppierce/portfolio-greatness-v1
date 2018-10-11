<?php
/*****************************************************************
 * @package jBar
 * @version 1.5
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *****************************************************************/

// No direct access
defined('_JEXEC') or die;

/**
 * @package		ThemeXpert
 * @subpackage	plg_system_jbar
 */
class plgSystemJbarInstallerScript
{
	/**
	 * Post-flight extension installer method.
	 *
	 * This method runs after all other installation code.
	 *
	 * @param	$type
	 * @param	$parent
	 */
	function postflight($type, $parent)
	{
		// Display a nice installed message.
		echo JText::sprintf(
			'PLG_JBAR_INSTALLED',
			'1.5'
		);
	}
}
