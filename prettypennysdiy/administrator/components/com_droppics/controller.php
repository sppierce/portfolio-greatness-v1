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

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerlegacy');

class DroppicsController extends JControllerLegacy
{
      
    /**
    * default view for this controller
    */
    protected $default_view = 'droppics';
    
    /**
     * Method to display the view.
     *
     * @access	public
     */
    function display($cachable = false, $urlparams = false) 
    {
        // Load the submenu.
        DroppicsHelper::addSubmenu(JRequest::getCmd('view', $this->default_view ));
        
        
        $vName = JRequest::getCmd('view', $this->default_view);
        $layout = JRequest::getCmd('layout','default');
        
            if($vName=='droppics'){
                $view = $this->getView($vName,'html');        
                $model = $this->getModel('categories');
                $view->setModel($model, false);
            }elseif($vName=='gallery' && $layout=='default'){
                $view = $this->getView($vName,'raw');
                $model = $this->getModel('gallery');
                $view->setModel($model, false);
                $model = $this->getModel('category');
                $view->setModel($model, false);
            }elseif ($vName=='imagesinfo'){
                $view = $this->getView($vName,'html');
                $model = $this->getModel('categories');
                $view->setModel($model, false);
                $model = $this->getModel('gallery');
                $view->setModel($model, false);
                $model = $this->getModel('category');
                $view->setModel($model, false);
                $model = $this->getModel('imagesinfo');
                $view->setModel($model, false);
            }
//        }
        parent::display($cachable, $urlparams);
        return $this;
    }
    
}
