<?php

/**
 * copyright (C) 2012 GWE Systems Ltd - All rights reserved
 * @license GNU/GPLv3 www.gnu.org/licenses/gpl-3.0.html
 * */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class plgjeventsjevnotifyInstallerScript
{

	//
	// Joomla installer functions
	//
	public
			function preflight($type, $parent)
	{
		// Joomla! broke the update call, so we have to create a workaround check.
		$db = JFactory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevents'");
		$is_enabled = $db->loadResult();

		if ($is_enabled == 1)
		{
			// NB Dev sites do not have a copy of manifest.xml file in the component root folder
			$manifest = JPATH_SITE . "/administrator/components/com_jevents/manifest.xml";
			$manifestdata = $this->getValidManifestFile($manifest);
			$app = new stdClass();

			if ($manifestdata)
			{
				$app->name = $manifestdata ["name"];
				$app->version = $manifestdata ["version"];
			}

			if (isset($app->version) && version_compare($app->version, '3.1.14', "lt"))
			{
				Jerror::raiseWarning(null, 'A minimum of JEvents V3.1.14 is required for this addon. <br/>Please update JEvents first.' . $rel);
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
				Jerror::raiseWarning(null, 'JEvents has been disabled, please enable it first.' . $rel);
				return false;
			}
			elseif (!$is_enabled)
			{
				Jerror::raiseWarning(null, 'This Addon Requires JEvents core to be installed.<br/>Please first install JEvents' . $rel);
				return false;
			}
		}

	}

	function install($parent)
	{
		$this->createTables();

		//Whoops! must disable auto enable for now. We need to update the database default params at the same time, or add more fallbacks in code.
		// New install, lets enable the plugin! 
		//$db = JFactory::getDBO();
		//$db->setDebug(0);
		//$sql = "UPDATE #__extensions SET enabled=1 WHERE element='agendaminutes'";
		//$db->setQuery($sql);
		//$db->query();
		//echo $db->getErrorMsg();

		return true;

	}

	function uninstall($parent)
	{
		// No nothing for now, we want to keep the tables just incase they remove the plugin by accident. 

	}

	function update($parent)
	{
		$this->createTables();
		$this->updateTables();

	}

	function createTables()
	{
		$db = JFactory::getDBO();
		// message type 0 = new, 1 = changed
		if (version_compare(JVERSION, "3.3", 'ge'))
		{
			$charset = ($db->hasUTFSupport()) ? ' DEFAULT CHARACTER SET `utf8`' : '';
			$rowcharset = ($db->hasUTFSupport()) ? 'CHARACTER SET utf8' : '';
		}
		else
		{
			$charset = ($db->hasUTF()) ? ' DEFAULT CHARACTER SET `utf8`' : '';
			$rowcharset = ($db->hasUTF()) ? 'CHARACTER SET utf8' : '';
		}
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_notifications(
	id int(11) NOT NULL auto_increment,
	rp_id int(11) NOT NULL default 0,
	messagetype tinyint(1) unsigned NOT NULL default 0,
	user_id int(11) NOT NULL default 0,
	attendee_id int(11) NOT NULL default 0,
	ajeattendee_id int(11) NOT NULL default 0,
	invitee_id int(11) NOT NULL default 0,
	remindee_id int(11) NOT NULL default 0,
	emailaddress varchar(250) NOT NULL default '',
	created datetime  NOT NULL default '0000-00-00 00:00:00',
	sentmessage tinyint(1) unsigned NOT NULL default 0,
	whensent datetime  NOT NULL default '0000-00-00 00:00:00',
	notificationtype int(11) NOT NULL default 0,
	subject text NOT NULL ,
	message text NOT NULL ,
	creatoremail varchar(255) NOT NULL default ''	,
	creatorname varchar(255) NOT NULL default ''	,
	m_ev int(11) NOT NULL default 0,
	PRIMARY KEY  (id),
	UNIQUE INDEX (rp_id, user_id, emailaddress),
	INDEX (rp_id)
) $charset;
SQL;
		$db->setQuery($sql);
		if (!$db->query())
		{
			echo $db->getErrorMsg();
		}

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_notification_map(
	id int(11) NOT NULL auto_increment,
	user_id int(12) NOT NULL  default 0,
	cat_id int(12) NOT NULL  default 0,
	PRIMARY KEY (id),
	INDEX  (user_id),
	INDEX  (cat_id),
	INDEX  (user_id, cat_id)	
);
SQL;
		$db->setQuery($sql);
		$db->query();

	}

	function updatetables()
	{
		$db = JFactory::getDBO();
		$db->setDebug(0);

		if (version_compare(JVERSION, "3.3", 'ge'))
		{
			$charset = ($db->hasUTFSupport()) ? ' DEFAULT CHARACTER SET `utf8`' : '';
			$rowcharset = ($db->hasUTFSupport()) ? 'CHARACTER SET utf8' : '';
		}
		else
		{
			$charset = ($db->hasUTF()) ? ' DEFAULT CHARACTER SET `utf8`' : '';
			$rowcharset = ($db->hasUTF()) ? 'CHARACTER SET utf8' : '';
		}

		$sql = "SHOW COLUMNS FROM #__jev_notifications";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("notificationtype", $cols))
		{
			$sql = "ALTER TABLE #__jev_notifications ADD COLUMN notificationtype int(11) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("subject", $cols))
		{
			$sql = "ALTER TABLE #__jev_notifications ADD COLUMN subject text NOT NULL  ";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("message", $cols))
		{
			$sql = "ALTER TABLE #__jev_notifications ADD COLUMN message text NOT NULL ";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("creatoremail", $cols))
		{
			$sql = "ALTER TABLE #__jev_notifications ADD COLUMN creatoremail varchar(255) NOT NULL default '' ";
			$db->setQuery($sql);
			@$db->query();

			$sql = "ALTER TABLE #__jev_notifications ADD COLUMN creatorname varchar(255) NOT NULL default '' ";
			$db->setQuery($sql);
			@$db->query();
		}
		if (!array_key_exists("m_ev", $cols))
		{
			$sql = "ALTER TABLE #__jev_notifications ADD COLUMN m_ev int(11) NOT NULL default 0 ";
			$db->setQuery($sql);
			@$db->query();
		}

	}

	function postflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<h2>' . JText::_('PLG_INST_JEVENTS_JEVNOTIFY') . ' ' . $parent->get('manifest')->version . ' </h2>';
		echo '<strong>';

		if ($type == "update")
		{
			echo JText::_('PLG_INST_JEVENTS_JEVNOTIFY_UPDATE') . '<br/><br/>';
			echo JText::_('PLG_INST_JEVENTS_JEVNOTIFY_DESC');
		}
		else
		{
			echo JText::_('PLG_INST_JEVENTS_JEVNOTIFY_INSTALL') . '<br/><br/>';
			echo JText::_('PLG_INST_JEVENTS_JEVNOTIFY_DESC');
		}
		echo '</strong><br/><br/>';

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