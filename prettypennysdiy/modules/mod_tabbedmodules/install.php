<?php

/**
 * copyright (C) 2012 GWE Systems Ltd - All rights reserved
 * @license GNU/GPLv3 www.gnu.org/licenses/gpl-3.0.html
 * */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class mod_tabbedmodulesInstallerScript
{
	//
	// Joomla installer functions
	//
	public function preflight($type, $parent) {

                
		// Joomla! broke the update call, so we have to create a workaround check.
		$db = JFactory::getDbo ();
		$db->setQuery ( "SELECT enabled FROM #__extensions WHERE element = 'com_jevents' AND type='component' " );
		$is_enabled = $db->loadResult ();

		if ($is_enabled == 1) {
			$manifest  =  JPATH_SITE . "/administrator/components/com_jevents/manifest.xml";
			if (!JFile::exists($manifest) || ! $manifestdata = $this->getValidManifestFile ( $manifest )) {
				$manifest  =  JPATH_SITE . "/administrator/manifests/packages/pkg_jevents.xml";
				if (!JFile::exists($manifest) ||  ! $manifestdata = $this->getValidManifestFile ( $manifest )) {
					Jerror::raiseWarning ( null, 'JEvents Must be installed first to use this addon.');
					return false;
				}
			}

			$app = new stdClass ();
			$app->name = $manifestdata ["name"];
			$app->version = $manifestdata ["version"];

			if (version_compare( $app->version , '3.1.14', "lt")) {
				Jerror::raiseWarning ( null, 'A minimum of JEvents V3.1.14 is required for this addon. <br/>Please update JEvents first.' . $rel );
				return false;
			} else {
				$this->hasJEventsInst = 1;
				return;
			}
		} else {
                    $this->hasJEventsInst = 0;
                    if ($is_enabled == 0) {
                        Jerror::raiseWarning ( null, 'JEvents has been disabled, please enable it first.' . $rel );
                        return false;
                    } elseif(!$is_enabled) {
                        Jerror::raiseWarning ( null, 'This Addon Requires JEvents Core to be installed.<br/>Please first install JEvents' . $rel );
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
		// Nothing to do for now, tables should be created on install.
	}
	
	function createTables() {
		// Nothing to do for now, tables should be created on install.
	}
	function postflight($type, $parent) 
    {
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<h2>'.JText::_('JEV_TABBED_MOD') . ' ' . $parent->get('manifest')->version.' </h2>';
		echo '<strong>';

		if ($type == "update") {
			echo JText::_('JEV_TABBED_INSTALL_SUCCESS_1') . '<br/>';
			echo JText::_('JEV_TABBED_MOD_DESC');
		} else {
			echo JText::_('JEV_TABBED_INSTALL_SUCCESS_2') . '<br/>';
			echo JText::_('JEV_TABBED_MOD_DESC');
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
