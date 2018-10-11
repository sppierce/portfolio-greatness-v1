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

defined('_JEXEC') or die;

/**
 */
class DroppicsViewFrontManage extends JViewLegacy
{
   function display($tpl = null) {

       $app = JFactory::getApplication();

       $user        = JFactory::getUser();
       $loginUserId = (int) $user->get('id');
       if (!$loginUserId) {
           $app->redirect(JRoute::_('index.php?option=com_users&view=login'));
       }
       
       $this->initComponentFront();
       $this->canDo = DroppicsHelper::getActions();
       $this->params = $params = JComponentHelper::getParams('com_droppics');
       JModelLegacy::addIncludePath(JPATH_ROOT . '/administrator/components/com_droppics/models/', 'DroppicsModelCategories');
       $model = JModelLegacy::getInstance('Categories', 'droppicsModel');
       $model->setState('category.frontcategories',true);
//                $this->setState('list.limit',100000000);
       JFactory::getApplication()->setUserState('list.limit', 100000);
       $this->categories = $model->getItems();


       $user = JFactory::getUser();
       $params = JComponentHelper::getParams('com_droppics');
       // get value new_image_on_top in params
       $this->new_image_on_top = $params->get('new_image_on_top', 1);
       if($params->get('import') && !JRequest::getBool('caninsert',0) && $user->authorise('core.admin')){
           $this->importFiles = true;
       }else{
           $this->importFiles = false;
       }

       $this->setLayout(JRequest::getCmd('layout','default'));

       parent::display($tpl);
   }
   
   public function initComponentFront() {
       
       //Register  base class
        JLoader::register('DroppicsBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/droppicsBase.php');

       droppicsBase::initComponent();
       
   }
}