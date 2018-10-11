<?php

/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;

/**
 * Utility class for Bootstrap elements.
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       3.0
 */
class JevIsotope
{

	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static
			$loaded = array();

	/**
	 * Method to load the Bootstrap JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of Bootstrap is included for easier debugging.
	 *
	 * @param   mixed  $debug  Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static
			function framework($params = array(), $debug = null)
	{
		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		// Load jQuery
		JHtml::_('jquery.framework');
		JHtml::stylesheet('com_jevents/lib_jevisotope/jevisotope.css', array(), true);

		//We could run a more sophistecated check if needed later.
		if (isset($params['jevlayout']) && $params['jevlayout'] == "float") {
			JHtml::stylesheet('com_jevents/float/float.css', array(), true);
		}
		JHtml::script('com_jevents/lib_jevisotope/jevisotope.pkgd.js', false, true, false, false, true);
		// We will likely be using images, so load the images is loaded library:
		JHtml::script('com_jevents/lib_jevisotope/jevimagesloaded.pkgd.js', false, true, false, false, true);
		if ($opt['layoutMode'] = isset($params['layoutMode']) ? $params['layoutMode'] : 'packery')
		{
			JHtml::script('com_jevents/lib_jevisotope/packery-mode.pkgd.js', false, true, false, false, true);
		}
		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = JFactory::getConfig();
			$debug = (boolean) $config->get('debug');
		}

		static::$loaded[__METHOD__] = true;

		return;

	}

	// isotopep - Loading Isotope in Packery Mode.
	public static
			function isotopep($data, $params = array(), $filters = array())
	{
		$cfg = JEVConfig::getInstance();
		// Include Typeahead framework
		static::framework($params);

		// Setup options object
		// Container almost always: .jeviso_container
		$opt['itemSelector'] = isset($params['itemSelector']) ? $params['itemSelector'] : '.jeviso_item';
		$opt['layoutMode'] = isset($params['layoutMode']) ? $params['layoutMode'] : 'packery';
		$opt['layoutOpts'] = isset($params['layoutOpts']) ? $params['layoutOpts'] : '';

		$jeviso_script = "jQuery(document).ready(function(){
            jQuery('.jeviso_container').isotope({
                //options
                layoutMode: '" . $opt['layoutMode'] . "',
                " . $opt['layoutOpts'] . "
                itemSelector: '" . $opt['itemSelector'] . "',
            })
        });";
		// Attach script to document
		JFactory::getDocument()->addScriptDeclaration($jeviso_script);

		return;

	}

	public static
			function itembody($row, $params = array(), $Itemid = "", $item_body = "", $view = false)
	{
		include_once(JEV_PATH . "/views/default/helpers/defaultloadedfromtemplate.php");
		ob_start();
		DefaultLoadedFromTemplate($view, 'icalevent.list_block1', $row, 0, $item_body, true);
		$item_body_output = ob_get_clean();

		return $item_body_output;

	}

}
