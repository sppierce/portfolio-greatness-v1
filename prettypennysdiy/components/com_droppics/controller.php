<?php
/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

//-- No direct access
defined('_JEXEC') || die('=;)');


jimport('joomla.application.component.controller');

class droppicsController extends JControllerLegacy
{
    function __construct($config = array()) {
        $view = JFactory::getApplication()->input->get('view');;
        if(!preg_match('/^front.*/', $view)){
            $config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;   
            $this->front = false;
        }else{
            $this->front = true;
        }        
        parent::__construct($config);
    }
    
    /**
     * Method to display the view.
     *
     * @access	public
     */
    function display($cachable = false, $urlparams = false) 
    {
        if($this->front===false){
            // Load the submenu.
            DroppicsHelper::addSubmenu(JRequest::getCmd('view', $this->default_view ));

            $vName = JRequest::getCmd('view', $this->default_view);
    //        $format = JRequest::getCmd('format', 'html');
    //        if($format=='html'){
                if($vName=='droppics'){
                    $view = $this->getView($vName,'html');        
                    $model = $this->getModel('gallery');
                    $view->setModel($model, false);
                }
    //        }
        }
        parent::display($cachable, $urlparams);
        return $this;
    }
}