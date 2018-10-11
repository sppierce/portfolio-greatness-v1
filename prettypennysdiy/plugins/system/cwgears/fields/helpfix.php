<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Header Element
 * @author              Steven Palmer
 * @author url          https://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Gears is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

JFormHelper::loadFieldClass('list');

class JFormFieldHelpfix extends JFormFieldList
{
	public $type = 'Helpfix';

	
	/*
	 * Workaround for missing online help for CoalaWeb components
	 * Although we can easily control "Help" button display and behavior in all
	 * backend views in the component, we have no power to change "Help" button
	 * that appears in CoalaWeb Members configuration (options) view.
	 * This button is generated automatically by com_config core component and
	 * will try to reference joomla online help server (configured in helpsites.xml),
	 * thus leading to a page that will never exist.
	 * 
	 * The solution is to hide "help" button in this case. We rely on this custom
	 * field, as it will always be rendered on CoalaWeb Members comfiguration
	 * screen and the script below will be executed.
	 * 
	 * Later we can render our own "help" button, using JToolbar::help('some_link', true)
	 * to force local help files to be loaded.
	 */
	protected function getInput()
	{
		$html = parent::getInput();
		
		if(JFactory::getApplication()->input->getCmd('option', '') == 'com_config')
		{
			JFactory::getDocument()->addScriptDeclaration('
				jQuery(document).ready(function($) {
					$("button[rel=\'help\']").hide();
					
				});
			');
		}
		return $html;
	}
}
