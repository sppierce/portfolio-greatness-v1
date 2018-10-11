<?php

/**
 * copyright (C) 2012 GWE Systems Ltd - All rights reserved
 * @license GNU/GPLv3 www.gnu.org/licenses/gpl-3.0.html
 * */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class plgjeventsjevusersInstallerScript
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
		$app = new stdClass ();

		if ($is_enabled == 1)
		{
			// NB Dev sites do not have a copy of manifest.xml file in the component root folder
			$manifest = JPATH_SITE . "/administrator/components/com_jevents/manifest.xml";
			$manifestdata = $this->getValidManifestFile($manifest);
			if ($manifestdata)
			{
				$app->name = $manifestdata ["name"];
				$app->version = $manifestdata ["version"];
			}

			if (isset($app->version) && version_compare($app->version, '3.1.16', "lt"))
			{
				Jerror::raiseWarning(null, JText::_('JEV_MIN_VERSION') . $rel);
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
				Jerror::raiseWarning(null, JText::_('JEV_DISABLED') . $rel);
				return false;
			}
			elseif (!$is_enabled)
			{
				Jerror::raiseWarning(null, JText::_('JEV_REQUIRED') . $rel);
				return false;
			}
		}

	}

	public function install($parent)
	{

		$this->createTables();

		return true;

	}

	public function uninstall($parent)
	{
		// No nothing for now, we want to keep the tables just incase they remove the plugin by accident. 

	}

	public function update($parent)
	{
		$this->createTables();

		$db = JFactory::getDBO();

		$sql = "SHOW COLUMNS FROM `#__jev_usereventsmap`";
		$db->setQuery($sql);
		$cols     = $db->loadObjectList();
		$uptodate = false;
		foreach ($cols as $col)
		{
			if ($col->Field == "groupid")
			{
				$uptodate = true;
				break;
			}
		}
		if (!$uptodate)
		{
			$sql = "Alter table #__jev_usereventsmap ADD COLUMN groupid int(12) NOT NULL default 0 ";
			$db->setQuery($sql);
			if (!$db->execute())
			{
				echo $db->getErrorMsg();
			}

			$sql = "alter table #__jev_usereventsmap DROP PRIMARY KEY";
			$db->setQuery($sql);
			if (!$db->execute())
			{
				echo $db->getErrorMsg();
			}

			$sql = "alter table #__jev_usereventsmap ADD PRIMARY KEY (`user_id`,`evdet_id`, `groupid`)";
			$db->setQuery($sql);
			if (!$db->execute())
			{
				echo $db->getErrorMsg();
			}

		}

		return true;

	}

	private function createTables()
	{

		$db = JFactory::getDBO();
		if (version_compare(JVERSION, "3.3", 'ge'))
		{
			$charset    = ($db->hasUTFSupport()) ? ' DEFAULT CHARACTER SET `utf8`' : '';
			$rowcharset = ($db->hasUTFSupport()) ? 'CHARACTER SET utf8' : '';
		}
		else
		{
			$charset    = ($db->hasUTF()) ? ' DEFAULT CHARACTER SET `utf8`' : '';
			$rowcharset = ($db->hasUTF()) ? 'CHARACTER SET utf8' : '';
		}
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_usereventsmap(
	user_id int(12) NOT NULL  default 0,
	evdet_id int(12) NOT NULL  default 0,
	privateevent tinyint(2) NOT NULL default 1,
	groupid int(12) NOT NULL default 0,

	PRIMARY KEY (user_id,evdet_id, groupid),
	INDEX  (evdet_id),
	INDEX  (user_id)
) $charset;
SQL;
		$db->setQuery($sql);
		if (!$db->execute())
		{
			echo $db->getErrorMsg();
		}

	}

	// Manifest validation
	private function getValidManifestFile($manifest)
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

