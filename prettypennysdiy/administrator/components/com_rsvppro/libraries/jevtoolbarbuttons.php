<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevbuttons.php 2749 2011-10-13 08:54:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */


// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
jimport('cms.toolbar.button.standard');


class JToolbarButtonJevedit extends JToolbarButtonStandard
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'JevEdit';

	function fetchButton( $type='JevEdit', $name = '', $text = '', $task = '', $list = true, $hideMenu = false )
	{
		$text	= JText::_($text);
		$class	= $this->fetchIconClass($name);
		$doTask	= $this->_getCommand( $name, $task, $list, $hideMenu);

		if ($name == 'apply' || $name == 'new')
		{
			$btnClass = 'btn btn-small btn-success';
			$class .= ' icon-white';
		}
		else
		{
			$btnClass = 'btn btn-small';
		}
                	
		$html = "<button href=\"#\" onclick=\"jQuery('#adminForm').attr('target','_blank');jQuery('input[name=option]').val('com_jevents');$doTask;jQuery('input[name=option]').val('com_jevents');jQuery('#adminForm').attr('target','_self');\" class=\"$btnClass\">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "</span>\n";
		$html .= "$text\n";
		$html .= "</button>\n";

		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @access	public
	 * @return	string	Button CSS Id
	 * @since	1.5
	 */
	function fetchId( $type='Confirm',  $msg='', $name = '', $text = '', $task = '', $list = true, $hideMenu = false , $jstestvar = false)
	{
		return $this->_parent->getName().'-'.$name;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string   $name  The task name as seen by the user
	 * @param   string   $task  The task used by the application
	 * @param   boolean  $list  True is requires a list confirmation.
	 *
	 * @return  string   JavaScript command string
	 *
	 * @since   3.0
	 */
	protected function _getCommand($name, $task, $list)
	{
		$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
		$message = addslashes($message);

		if ($list)
		{
			$cmd = "if (document.adminForm.boxchecked.value==0){alert('$message');}else{ Joomla.submitbutton('$task')}";
		}
		else
		{
			$cmd = "Joomla.submitbutton('$task')";
		}

		return $cmd;
	}}
