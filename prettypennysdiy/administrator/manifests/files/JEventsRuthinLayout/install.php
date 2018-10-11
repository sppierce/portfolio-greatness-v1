<?php

/**
 * copyright (C) 2015 GWE Systems Ltd - All rights reserved
 * @license GNU/GPLv3 www.gnu.org/licenses/gpl-3.0.html
 * */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// This will only be triggered in Joomla 3.4.0 or later after a change in the name convension of file packages
class JEventsRuthinLayoutInstallerScript
{

	private $oldelement = "ruthin";
	private $newelement = "JEventsRuthinLayout";
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		// We are a file installer, so we need to manually delete the files.
		// $parent is the class calling this method
		if (JFolder::exists(JPATH_SITE."/components/com_jevents/views/ruthin")){
			JFolder::delete(JPATH_SITE."/components/com_jevents/views/ruthin");
		}
		if (JFolder::exists(JPATH_SITE."/modules/mod_jevents_cal/tmpl/ruthin")){
			JFolder::delete(JPATH_SITE."/modules/mod_jevents_cal/tmpl/ruthin");
		}
		if (JFolder::exists(JPATH_SITE."/modules/mod_jevents_latest/tmpl/ruthin")){
			JFolder::delete(JPATH_SITE."/modules/mod_jevents_latest/tmpl/ruthin");
		}
		if (JFolder::exists(JPATH_SITE."/modules/mod_jevents_legend/tmpl/ruthin")){
			JFolder::delete(JPATH_SITE."/modules/mod_jevents_legend/tmpl/ruthin");
		}
	}
	//
	// Joomla installer functions
	//
	public function preflight($type, $parent)
	{
	}

	public function postflight($type, $parent)
	{
		//Lower case file names means a lower case element name, lets clean that up.
		$db = JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__extensions WHERE element =".$db->quote(strtolower($this->newelement)));
		$extension = $db->loadobject();

		if ($extension) {
			$db->setQuery("UPDATE #__extensions SET element =".$db->quote($this->newelement)." WHERE element =".$db->quote(strtolower($this->newelement)));
			$db->query();
		}

		// Joomla! broke the update call, so we have to create a workaround check.
		$db = JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__extensions WHERE element =".$db->quote($this->oldelement). " OR element=".$db->quote($this->newelement));
		$extensions = $db->loadObjectList();

		if (count($extensions)>1){
			$hasold = false;
			$hasnew = false;
			foreach ($extensions as $extension){
				if ($extension->element == $this->oldelement) {
					$hasold = $extension;
				}
				else if ($extension->element == $this->newelement) {
					$hasnew = $extension ;
				}
			}
			if ($hasold && $hasnew){
				$db->setQuery("DELETE FROM #__extensions WHERE element =".$db->quote($this->oldelement));
				$db->query();
			}
		}
		if (JFile::exists(JPATH_ADMINISTRATOR."/manifests/files/ruthin.xml")){
			JFile::delete(JPATH_ADMINISTRATOR."/manifests/files/ruthin.xml");
		}
		return;
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<h2>' . JText::_('PLG_INST_JEVENTS_JEVEXTPLUS') . ' ' . $parent->get('manifest')->version . ' </h2>';
		echo '<strong>';

		if ($type == "update")
		{
			echo JText::_('PLG_INST_JEVENTS_JEVEXTPLUS_SUCC1') . '<br/>';
			echo JText::_('PLG_INST_JEVENTS_JEVEXTPLUS_DESC');
		}
		else
		{
			echo JText::_('PLG_INST_JEVENTS_JEVEXTPLUS_SUCC2') . '<br/>';
			echo JText::_('PLG_INST_JEVENTS_JEVEXTPLUS_DESC');
		}
		echo '</strong><br/><br/>';

	}

}
