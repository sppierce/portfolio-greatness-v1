<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 3155 2012-01-05 12:01:16Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class jevcustomfieldsViewjevcustomfields extends JViewLegacy
{
	
	function overview($tpl = null)
	{
		$this->vName = "plugin.jev_customfields.overview";

		JEVHelper::stylesheet('eventsadmin.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
		
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		JLoader::register('JToolbarButtonJevconfirm', JEV_ADMINPATH . "libraries/jevtoolbarbuttons.php");

		// Set toolbar items for the page
		JToolBarHelper::title($this->title);
		JFactory::getDocument()->setTitle($this->title);

		JToolBarHelper::addNew('plugin.jev_customfields.edit');
		JToolBarHelper::editList('plugin.jev_customfields.edit');
		JToolBarHelper::custom('plugin.jev_customfields.editcopy', 'copy.png', 'copy.png', 'JEV_ADMIN_COPYEDIT');
		//function fetchButton( $type='Confirm', $msg='', $name = '', $text = '', $task = '', $list = true, $hideMenu = false , $jstestvar = false)
		JToolBar::getInstance('toolbar')->appendButton('Jevconfirm', "JEV_ARE_YOU_SURE_DELETE_CUSTOM", "trash", "JTOOLBAR_DELETE", 'plugin.jev_customfields.delete', true, false, 1);

                JEventsHelper::addSubmenu();
                $this->sidebar = JHtmlSidebar::render();                
	}

	function edit($tpl = null)
	{
		$this->vName = "plugin.jev_customfields.edit";

		JHtml::_('jquery.framework');
		JHtml::_('jquery.ui', array('core', 'sortable'));

		JEVHelper::stylesheet('eventsadmin.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
		JEVHelper::stylesheet('jevcfadmin.css', 'plugins/jevents/jevcustomfields/views/assets/css/');
		JEVHelper::script('forms.js', 'plugins/jevents/jevcustomfields/views/assets/js/');

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		// Set toolbar items for the page
		JToolBarHelper::title($this->title);
		JFactory::getDocument()->setTitle($this->title);

		JToolBarHelper::save('plugin.jev_customfields.save');
		JToolBarHelper::apply('plugin.jev_customfields.apply');
		JToolBarHelper::cancel('plugin.jev_customfields.overview');

	}

	protected
			function getSidebar()
	{

		// could be called from categories component
		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");

		JHtmlSidebar::addEntry(
				JText::_('CONTROL_PANEL'), 'index.php?option=com_jevents', $this->vName == 'cpanel.cpanel'
		);

		JHtmlSidebar::addEntry(
				JText::_('JEV_ADMIN_ICAL_EVENTS'), 'index.php?option=com_jevents&task=icalevent.list', $this->vName == 'icalevent.list'
		);

		if (JEVHelper::isAdminUser())
		{
			JHtmlSidebar::addEntry(
					JText::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS'), 'index.php?option=com_jevents&task=icals.list', $this->vName == 'icals.list'
			);
		}
		JHtmlSidebar::addEntry(
				JText::_('JEV_INSTAL_CATS'), "index.php?option=com_categories&extension=com_jevents", $this->vName == 'categories'
		);
		if (JEVHelper::isAdminUser())
		{
			JHtmlSidebar::addEntry(
					JText::_('JEV_MANAGE_USERS'), 'index.php?option=com_jevents&task=user.list', $this->vName == 'user.list'
			);
			JHtmlSidebar::addEntry(
					JText::_('JEV_INSTAL_CONFIG'), 'index.php?option=com_jevents&task=params.edit', $this->vName == 'params.edit'
			);
			JHtmlSidebar::addEntry(
					JText::_('JEV_LAYOUT_DEFAULTS'), 'index.php?option=com_jevents&task=defaults.list', in_array($this->vName, array('defaults.list', 'defaults.overview'))
			);

			//Support & CSS Customs should only be for Admins really.
			JHtmlSidebar::addEntry(
					JText::_('SUPPORT_INFO'), 'index.php?option=com_jevents&task=cpanel.support', $this->vName == 'cpanel.support'
			);
			JHtmlSidebar::addEntry(
					JText::_('JEV_CUSTOM_CSS'), 'index.php?option=com_jevents&task=cpanel.custom_css', $this->vName == 'cpanel.custom_css'
			);
		}

		$this->sidebar = JHtmlSidebar::render();

	}
}
