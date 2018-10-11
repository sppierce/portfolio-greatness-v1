<?php

/**
 * JEvents Component for Joomla 2.5.x
 *
 * @version     3.4.17  -  April 2017
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class pkg_rsvpproInstallerScript
{
	public function preflight ($type, $parent) {
		$db = JFactory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_rsvppro'");
		$is_enabled = $db->loadResult();
		if (!$is_enabled){
			$this->hasJEventsInst = 0;
		} else {
			$this->hasJEventsInst = 1;
		}
		if (version_compare(JVERSION, '3.4', '<')){
			Jerror::raiseWarning(null, 'This version of RSVP Pro  is desgined for Joomla 3.4.0 and later.<br/>Please update Joomla before upgrading RSVP Pro  to this version' );
			return false;
		}

		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_jevents" AND type = "component"');
		$manifestcache = $db->loadResult();
		if (!$manifestcache) {
			Jerror::raiseWarning(null, 'This version of RSVP Pro is desgined for JEvents.<br/>Please install JEvents 3.4.0 or later before installing RSVP Pro' );
			return false;
		}
		else {
			$manifestcache = json_decode( $manifestcache, true );
			if (version_compare($manifestcache["version"], "3.4", "<")){
				Jerror::raiseWarning(null, 'This version of RSVP Pro is desgined for JEvents 3.4.0 or later.<br/>Please upgrade JEvents to version 3.4.0 or later before installing this version of RSVP Pro' );
				return false;
			}
		}

		$validvm = $this->checkVirtuemartVersion();
		if ($validvm){
			return true;
		}
		else {
			// running an old version of VM so install the older version of the scripts!
			// parent of parent is the JInstaller where we can get the source paths!
			$sourcefilepath = $parent->getParent()->getPath("source", false);
			if ($sourcefilepath){
				JFile::copy($sourcefilepath."/virtuemart2.zip", $sourcefilepath."/virtuemart.zip");
				JFile::copy($sourcefilepath."/vmcoupon2.zip", $sourcefilepath."/vmcoupon.zip");
				JFile::copy($sourcefilepath."/vmcustom2.zip", $sourcefilepath."/vmcustom.zip");
			}
		}

	}

	// TODO enable plugins
	public function update()
	{
		//return $this->checkVirtuemartVersion();
	}

	public function install($adapter)
	{
		//return $this->checkVirtuemartVersion();
	}

	private function checkVirtuemartVersion(){
		// TRAP OUT OF DATE VERSIONS OF VIRTUEMART!
		$db = JFactory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_virtuemart'");
		$is_vm= $db->loadResult();

		if (!$is_vm){
			return true;
		}

		$manifest  =  JPATH_SITE . "/administrator/components/com_virtuemart/virtuemart.xml";
		$manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);

		if (!$manifestdata){
			return true;
		}

		if (version_compare( $manifestdata ["version"] , '3.0.6', "lt")) {
			Jerror::raiseWarning(null, '<strong>This version of RSVP Pro is only compatible with versions of Virtuemart later than V3.0.6.<strong><br/>We have therefore installed the older version of the Virtuemart plugins.<br/>You should upgrade Joomla and Virtuemart as soon as is convenient' );
			return false;
		}

		return true;

	}

	public function uninstall($adapter)
	{

	}

	/*
	 * enable the plugins
	 */
	function postflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		?>
		<style type="text/css">
			.adminform tr th:first-child {display:none;}
			table.adminform tr td {padding:15px;}
			div.jev_install {background-color:#f4f4f4;border:1px solid #ccc; border-radius:5px; padding:10px;}
			.installed {clear:both;display:inline-block;}
			.installed ul { width:350px;padding-left:0px;border: 1px solid #ccc;border-radius: 5px;}
			.installed ul li:first-child {border-top-left-radius: 5px;border-top-right-radius: 5px;}
			.installed ul li:last-child {border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;}
			.installed ul li {padding:8px;list-style-type:none;}
			.installed ul li:nth-child(odd) {background-color: #fff;}
			.installed ul li:nth-child(even) {background-color: #D6D6D6;}
			.proceed {display:inline-block; vertical-align:top;}
			div.proceed ul {text-align:left;list-style-type:none;}
			div.proceed ul li {padding:5px;background-color:#fff;border:1px solid #ccc;margin-bottom:10px;border-radius:5px;}
		</style>
				<?PHP
		if ($type == 'install') {
			// enable plugin
			$db = JFactory::getDbo();
			$query = "UPDATE #__extensions SET enabled=1 WHERE folder='jevents' and type='plugin' and element='jevrsvppro'";
			$db->setQuery($query);
			$db->execute();

			$query = "UPDATE #__extensions SET enabled=1 WHERE folder='rsvppro' and type='plugin' and element='manual'";
			$db->setQuery($query);
			$db->execute();

			// clean up any old install with the wrong extension name in the package!
			$query = "DELETE FROM #__extensions WHERE type='package' AND element='com_jevrsvppro' ";
			$db->setQuery($query);
			$db->execute();

		}
		//Lets load the language strings directly from the installed component admin folder, loading the english first then any other after to override to avoid untranslated strings.
		$language = JFactory::getLanguage();
		$language->load('com_rsvppro', JPATH_ADMINISTRATOR, 'en-GB', true);
		$language->load('com_rsvppro', JPATH_ADMINISTRATOR, null, true);

		// Remove component unstyled text
		if ($this->hasJEventsInst == 0) { $inst_text = JText::_('PKG_INST_JEVENTS_RSVPPRO_SUCC2'); } else {  $inst_text = JText::_('PKG_INST_JEVENTS_RSVPPRO_SUCC1') . $parent->get('manifest')->version;}
		echo "<style type='text/css'>table.adminform tbody tr th:first-child {display:none;}</style>";
		echo "<div class='jev_install'>
				<div class='jev_logo'><img src='http://www.jevents.net/images/JeventsTransparent.png' /></div>
				<div class='version'><h2>". $inst_text ."</h2></div>";
			echo '<div class="installed">
						<ul>
							<li>RSVP Pro Component
							<li>RSVP Pro Plugin</li>
							<li>RSVP Pro Virtuemart Tickets Plugin</li>
							<li>Payment Processor Plugin - Manual</li>
							<li>Payment Processor Plugin - PayPal IPN</li>
							<li>Payment Processor Plugin - Authorize.net</li>
							<li>Payment Processor Plugin - Virtuemart 3</li>
							<li>Payment Processor Plugin - HikaShop</li>
						</ul><br/><br/>
					</div>
					<div class="proceed">
					<ul>';
						echo "<li style='background-color: #F5EF62;'>" . JText::_('PKG_INST_JEVENTS_RSVPPRO_NOTE_PLUGINS') . "</li>";
						echo "<li>" . JText::_('PKG_INST_JEVENTS_RSVPPRO_DESC') . "</li>";
					echo "</ul>
					</div>";
		echo "</div>";

                // Clean up stray install files
                if (JFile::exists(JPATH_SITE."/plugins/jevent/jev_rsvppro/rsvppro/rsvp.js.zip")){
                        JFile::delete(JPATH_SITE."/plugins/jevent/jev_rsvppro/rsvppro/rsvp.js.zip");
                }
                
	}

}
