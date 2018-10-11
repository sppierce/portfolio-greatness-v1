<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Editor Jevents buton
 *
 * @package		Joomla.Plugin
 * @subpackage	Editors-xtd.jevents
 * @since 1.5
 */
class plgButtonJEvents extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}


	/**
	 * Display the button
	 */
	function onDisplay($name)
	{
		/*
		 * Javascript to insert the link
		 * View element calls jSelectEvent when an event is clicked
		 * jSelectEvent creates the link tag, sends it to the editor,
		 * and closes the select frame.
		 */
		$target = $this->params->get("target_itemid",1);
		$js = "
		function jSelectEvent(link, title, Itemid, rpid) {
			if (Itemid>0){
				link = link.replace(/Itemid=1/, 'Itemid='+Itemid);
			}
			else {
				link = link.replace(/Itemid=1/, 'Itemid=$target');
			}
			var tag = '<a href=\"' + link + '\">' + title + '</a>';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
			return false;
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		
		JHtml::_('behavior.modal');

		$link = 'index.php?option=com_jevents&amp;task=icalevent.select&amp;tmpl=component&amp;'.JSession::getFormToken().'=1';

		$button = new JObject();
		$button->set('modal', true);				
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_EVENT_BUTTON_EVENT'));
		// use article name to pickup article icon!
		if (version_compare(JVERSION, "3.0.0", 'ge'))
		{
			$button->set('class','btn');
			$button->set('name','calendar');
		}
		else
		{
			$style = '.button2-left .article.jevents a{background:url("'.JUri::root().'/plugins/editors-xtd/jevents/jevents_event_sml_24x24.png") no-repeat scroll 88% 3px / 27% auto rgba(0, 0, 0, 0)}';
			$doc->addStyleDeclaration($style);
			$button->set('name','article jevents');
		}
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 600}}");

		return $button;
	}
}
