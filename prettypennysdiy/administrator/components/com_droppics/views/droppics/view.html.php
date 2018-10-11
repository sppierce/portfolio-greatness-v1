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


class DroppicsViewDroppics extends JViewLegacy
{
	
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
                $model = $this->getModel('categories');
//                $this->setState('list.limit',100000000);
                JFactory::getApplication()->setUserState('list.limit', 100000);
                $this->categories = $model->getItems();

                $this->canDo = DroppicsHelper::getActions();
                $user = JFactory::getUser();
                $params = JComponentHelper::getParams('com_droppics');
                // get value new_image_on_top in params
                $this->new_image_on_top = $params->get('new_image_on_top', 1);
                if($params->get('import') && $user->authorise('core.admin')){
                    $this->importFiles = true;
                }else{
                    $this->importFiles = false;
                }
                
                $this->setLayout(JRequest::getCmd('layout','default'));
                
		parent::display($tpl);
                
                $app = JFactory::getApplication();
                if($app->isAdmin()){
                    $this->addToolbar();
                }
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= DroppicsHelper::getActions();
		JToolBarHelper::title(JText::_('COM_DROPPICS_MAIN_PAGE'), 'droppics.png');


		if ($canDo->get('core.admin')) {
                        if(droppicsBase::checkConfigUpdate()==true){
                            $toolbar = JToolBar::getInstance();
                            $toolbar->appendButton( 'popup', 'loop', JText::_('COM_DROPPICS_VIEW_DROPPICS_CHECKERROR'), 'index.php?option=com_droppics&view=droppics&layout=configupdated&tmpl=component', 500, 150,0,0,'window.location.reload();' );
                        }
			JToolBarHelper::preferences('com_droppics');
            JToolbarHelper::custom('files.paste', 'archive', 'archive', 'Paste', false);
            JToolbarHelper::custom('files.movefile', 'scissors', 'scissors', 'Cut', false);
            JToolbarHelper::custom('files.copyfile', 'copy', 'copy', 'Copy', false);
            JToolbarHelper::custom('imagesinfo', 'info', 'info', 'Image SEO Optimizer', false);
		}

		JToolBarHelper::divider();
	}
}
