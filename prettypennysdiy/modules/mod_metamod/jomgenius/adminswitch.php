<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JomGenius classes
 * 
 * @package		JomGenius
 * @version		1
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2010-2015 Brandon IT Consulting. All rights reserved.
 */

class JomGeniusClassAdminswitch extends JomGeniusParent {
		
	function __construct() {
	}
	
	function shouldInstantiate() {
		$component = JPATH_SITE . '/administrator/modules/mod_adminswitch';
		if ( file_exists( $admin_component ) or file_exists( $component ) ) {
			return true;
		}
	}

	
	/* particular methods for this component */
	
	/**
	 * A generic function that knows how to get lots of different info about the environment.
	 */
	function info( $type ) {
		if ($type == 'switches') {
			static $allswitches = null;
			if (!is_null($allswitches)) return $allswitches;
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('tag')
			 ->from('#__adminswitch')
			 ->where('value = 1');
			$db->setQuery($query);
			if ( version_compare( JVERSION, '3.0', '<' ) ) { // J1.7, 2.5
				$allswitches = $db->loadResultArray();
			} else {
				$allswitches = $db->loadColumn();
			}
			return $allswitches;
		}
		
		// if it's not "switches", it's a tag type prefixed with switch_
		$tag = $type;
		if ( substr($tag, 0, 7) != 'switch_') return false; // we only handle things starting with switch_
		
		$tag = substr( $tag, 7, strlen($tag) - 7 );
		$tag = preg_replace('#[^a-zA-Z0-9]#','',$tag);
		
		static $switches = null;
		if ($switches == null) $switches = array();
		if (array_key_exists($tag, $switches)) return $switches[$tag];
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('value')
		 ->from('#__adminswitch')
		 ->where('tag = '.$db->quote($tag));
		$db->setQuery($query);
		
		$switches[$tag] = ($db->loadResult() == 1);
		return $switches[$tag];	
	}

}