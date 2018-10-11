<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 1406 2009-04-04 09:54:18Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


/**
 * HTML View class for the component frontend
 *
 * @static
 */
class FlatplusViewMonth extends JEventsFlatplusView
{

    function calendar($tpl = null)
    {
        $params = JComponentHelper::getParams(JEV_COM_COMPONENT);
        $browser = JBrowser::getInstance();
        $browserType = $browser->getBrowser();
        $browserVersion = $browser->getMajor();
        if (($browserType == 'msie') && ($browserVersion < 9)) {
            JEVHelper::componentStylesheet($this, "scalable_ie8.css");

        } else JEVHelper::componentStylesheet($this, "scalable.css");
        JEVHelper::componentStylesheet($this);
        JEVHelper::script('layout.js', 'components/' . JEV_COM_COMPONENT . "/views/" . $this->jevlayout . "/assets/js/");

        // TODO do this properly
        //$document->setTitle(JText::_( 'BROWSER_TITLE' ));

        //$this->assign("introduction", $params->get("intro",""));

        $this->data = $this->datamodel->getCalendarData($this->year, $this->month, $this->day);

        // for adding events in day cell
        $this->popup = false;
        if ($params->get("editpopup", 0) && JEVHelper::isEventCreator()) {
            JevHtmlBootstrap::modal();
            JEVHelper::script('editpopup.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
            $this->popup = true;
            $this->popupw = $params->get("popupw", 800);
            $this->popuph = $params->get("popuph", 600);
        }

        $this->is_event_creator = JEVHelper::isEventCreator();

    }
    /*
        public function sortjevents($a,$b){
            if ($a->_publish_up == $b->_publish_up) {
                return 0;
            }
            return ($a->_publish_up < $b->_publish_up) ? -1 : 1;
        }
        */
}
