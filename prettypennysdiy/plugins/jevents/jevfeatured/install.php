<?php

/**
 * copyright (C) 2012 GWE Systems Ltd - All rights reserved
 * @license GNU/GPLv3 www.gnu.org/licenses/gpl-3.0.html
 * */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class plgjeventsjevfeaturedInstallerScript
{
	//
	// Joomla installer functions
	//
	public function preflight($type, $parent)
	{
		// Joomla! broke the update call, so we have to create a workaround check.
		$db = JFactory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevents'");
		$is_enabled = $db->loadResult();

		if ($is_enabled == 1)
		{
			$manifest = JPATH_SITE . "/administrator/components/com_jevents/manifest.xml";
			if (!$manifestdata = $this->getValidManifestFile($manifest))
			{
				Jerror::raiseWarning(null, JText::_('JEV_REQUIRED'));

				return false;
			}

			$app          = new stdClass ();
			$app->name    = $manifestdata ["name"];
			$app->version = $manifestdata ["version"];

			if (version_compare($app->version, '3.1.14', "lt"))
			{
				Jerror::raiseWarning(null, JTExt::_('JEV_MIN_VERSION'));

				return false;
			}
			else
			{
				$this->hasJEventsInst = 1;

				return;
			}
		}
		else
		{
			$this->hasJEventsInst = 0;
			if ($is_enabled == 0)
			{
				Jerror::raiseWarning(null, JText::_('JEV_DISABLED'));

				return false;
			}
			elseif (!$is_enabled)
			{
				Jerror::raiseWarning(null, JText::_('JEV_REQUIRED'));

				return false;
			}
		}
	}

	function install($parent)
	{


		return true;

	}

	function uninstall($parent)
	{
		// No nothing for now, we want to keep the tables just incase they remove the plugin by accident.

	}

	function update($parent)
	{
		// Nothing to do for now, tables should be created on install.

	}


	function postflight($type, $parent)
	{

	}

	// Manifest validation
	function getValidManifestFile($manifest)
	{
		$filecontent = JFile::read($manifest);
		if (stripos($filecontent, "jevents.net") === false && stripos($filecontent, "gwesystems.com") === false && stripos($filecontent, "joomlacontenteditor") === false && stripos($filecontent, "virtuemart") === false && stripos($filecontent, "sh404sef") === false)
		{
			return false;
		}
		// for JCE and Virtuemart only check component version number
		if (stripos($filecontent, "joomlacontenteditor") !== false || stripos($filecontent, "virtuemart") !== false || stripos($filecontent, "sh404sef") !== false || strpos($filecontent, "JCE") !== false)
		{
			if (strpos($filecontent, "type='component'") === false && strpos($filecontent, 'type="component"') === false)
			{
				return false;
			}
		}

		$manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);
		if (!$manifestdata)
			return false;
		if (strpos($manifestdata["authorUrl"], "jevents") === false && strpos($manifestdata["authorUrl"], "gwesystems") === false && strpos($manifestdata["authorUrl"], "joomlacontenteditor") === false && strpos($manifestdata["authorUrl"], "virtuemart") === false && strpos($manifestdata['name'], "sh404SEF") === false)
		{
			return false;
		}

		return $manifestdata;

	}

}
