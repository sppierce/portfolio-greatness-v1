<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: abstract.php 1400 2009-03-30 08:45:17Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML Abstract view class for the component frontend
 *
 * @static
 */
JLoader::register('JEventsDefaultView',JEV_VIEWS."/default/abstract/abstract.php");

class JEventsFlatplusView extends JEventsDefaultView
{
	var $jevlayout = null;
        var $colourcode = "#ff3b30";

	function __construct($config = null)
	{
		parent::__construct($config);

		$this->jevlayout="flatplus";

		$this->addHelperPath(dirname(__FILE__)."/../helpers/");
		$this->addHelperPath( JPATH_BASE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.JEV_COM_COMPONENT.'/'."helpers");
		$document = JFactory::getDocument();
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
                $browser = JBrowser::getInstance();
                $browserType = $browser->getBrowser();
                $browserVersion = $browser->getMajor();
                if(($browserType == 'msie') && ($browserVersion < 9))
                        {
                             JEVHelper::componentStylesheet($this,"scalable_ie8.css");

                        } else {
                            JEVHelper::componentStylesheet($this,"scalable.css");
                        }

                JEVHelper::componentStylesheet($this);

		if ($params->get("darktemplate",0)) JEVHelper::componentStylesheet($this,"dark.css");

		$document = JFactory::getDocument();
		$stylelink = '<!--[if lte IE 6]>' ."\n";
		$stylelink .= '<link rel="stylesheet" href="'.JURI::root().'components/com_jevents/views/flatplus/assets/css/ie6.css" />' ."\n";
		$stylelink .= '<![endif]-->' ."\n";
		$document->addCustomTag($stylelink);
		
                $this->colourcode = $params->get("colourcode", "#ff3b30");
                
                // Lets setup the color based on the hex code now.
                $style = '#jevents_body .jev_toprow div.previousmonth, #jevents_body .jev_toprow div.nextmonth {'
                        . 'background-color: ' . $this->colourcode . ' !important;'
                        . 'border: 1px solid ' . $this->colourcode . ' !important;'
                    . '}'
                    . '#jevents_body .jev_toprow div.currentmonth {'
                        . 'border-top: 1px solid ' . $this->colourcode . ' !important;'
                        . 'border-bottom: 1px solid ' . $this->colourcode . ' !important;'
                    . '}'
                    . '#jevents_body .nav-items div.active a, .jev_header2 .active a {'
                        . 'background-color: ' . $this->colourcode . ' !important;'
                    . '}'
                    . '#jevents_body .nav-items > div {'
                    
                        . 'border-color: ' . $this->colourcode . ' !important;'
                    . '}'
                    . '.jevtip-tip, .jevtip { border-color:' . $this->colourcode . ' !important;}'
                    . '#jevents_body .nav-items > div a:hover {'
                        . 'border-left-color: ' . $this->colourcode . ' !important;'
                    . '}'
                    .'#flatcal_minical .flatcal_todaycell a {'
                        .'background-color: ' . $this->colourcode . ' !important;'
                        .'border-color: ' . $this->colourcode . ' !important;'
                    .'}'
                    .'#flatcal_minical .flatcal_todaycell a:hover {'
                    .'background-color: #FFF !important;'
                    .'color: ' . $this->colourcode . ' !important;'
                    .'}'
                ;
                $document->addStyleDeclaration($style);

	}
/*
	function viewNavTableBarIconic( $today_date, $this_date, $dates, $alts, $option, $task, $Itemid ){
		$this->loadHelper("FlatplusViewNavTableBarIconic");
		$var = new FlatplusViewNavTableBarIconic($this, $today_date, $this_date, $dates, $alts, $option, $task, $Itemid );
	}
*/
}
