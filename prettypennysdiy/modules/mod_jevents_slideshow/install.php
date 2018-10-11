<?php

/**
 * copyright (C) 2012 GWE Systems Ltd - All rights reserved
 * @license GNU/GPLv3 www.gnu.org/licenses/gpl-3.0.html
 * */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class mod_jevents_slideshowInstallerScript
{

	//
	// Joomla installer functions
	//
	public function preflight($type, $parent)
	{

		//Check for the standard image and files plugin first
		if (!JFolder::exists(JPATH_SITE . '/plugins/jevents/jevfiles/')) {
			JError::raiseWarning(null, JText::_('STANDARD_IMAGE_FILES_REQUIRED'));
			return false;
		}
		//Check for the standard image and files plugin first
		if (!JFolder::exists(JPATH_SITE . '/components/com_jevents/views/')) {
			JError::raiseWarning(null, JText::_('JEVENTS_REQUIRED'));
			return false;
		}


		$jversion = new JVersion();
		// Installing component manifest file version
		$this->release = $parent->get("manifest")->version;

		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get("manifest")->attributes()->version;

		// abort if the current Joomla release is older
		if (version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt'))
		{
			Jerror::raiseWarning(null,  JText::_('JEV_SLIDESHOW_CANNOTI_INSTALL_JPRIOR') . $this->minimum_joomla_release);

			return false;
		}

		// Only install on Jevfiles V3.7.3+
		$manifest = JPATH_SITE . "/plugins/jevents/jevfiles/jevfiles.xml";
		if (!JFile::exists($manifest) || !$manifestdata = $this->getValidManifestFile($manifest)) {
			Jerror::raiseWarning(null, JText::_('STANDARD_IMAGE_FILES_REQUIRED'));
			return false;
		}

		$app = new stdClass ();
		$app->name = $manifestdata ["name"];
		$app->version = $manifestdata ["version"];

		if (version_compare($app->version, '3.7.3', "lt")) {
			Jerror::raiseWarning(null, JText::_('JEV_FILES_MIN_VERSION'));
			return false;
		} else {
			return true;
		}

	}

	function install($parent)
	{

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
	function getValidManifestFile($manifest) {
		$filecontent = JFile::read($manifest);
		if (stripos($filecontent, "jevents.net") === false && stripos($filecontent, 'gwesystems.com') === false && stripos($filecontent, 'joomlacontenteditor') === false && stripos($filecontent, 'virtuemart') === false && stripos($filecontent, 'sh404sef') === false) {
			return false;
		}
		// for JCE and Virtuemart only check component version number
		if (stripos($filecontent, 'joomlacontenteditor') !== false || stripos($filecontent, 'virtuemart') !== false || stripos($filecontent, 'sh404sef') !== false || strpos($filecontent, 'JCE') !== false) {
			if (strpos($filecontent, "type='component'") === false && strpos($filecontent, 'type="component"') === false) {
				return false;
			}
		}

		$manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);
		if (!$manifestdata)
			return false;
		if (strpos($manifestdata ['authorUrl'], 'jevents') === false && strpos($manifestdata ['authorUrl'], 'gwesystems') === false && strpos($manifestdata ['authorUrl'], 'joomlacontenteditor') === false && strpos($manifestdata ['authorUrl'], 'virtuemart') === false && strpos($manifestdata ['name'], 'sh404SEF') === false) {
			return false;
		}
		return $manifestdata;
	}

}
