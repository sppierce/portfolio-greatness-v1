<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class mod_MetamodInstallerScript {

	function postflight($parent) {
		// here we check that we have the right version of the XML file installed.
		// This module may have been installed on J2.5 which gets upgraded to J3.0.
		// In that case, we may be running in J3.0 with the wrong XML file. So we
		// check this on update.
		
		// Copy the J3.0 module xml file over the top of the older one, if necessary

		if ( version_compare( JVERSION, '3.0', '>=' ) ) { // J3.0+
			JFile::copy(JPATH_ADMINISTRATOR.'/../modules/mod_metamod/xml/mod_metamod30.xml',
						JPATH_ADMINISTRATOR.'/../modules/mod_metamod/mod_metamod.xml');
		}
	}		
}
