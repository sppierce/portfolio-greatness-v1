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

class DroppicsViewImagesinfo extends JViewLegacy
{

    protected $state;
    protected $items = null;
    protected $pagination = null;
    protected $total = null;
    protected $limitstart = null;
    protected $limit = null;
    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        //Pagination
        $modelcat = $this->getModel('categories');
        JFactory::getApplication()->setUserState('list.limit', 100000);
        $this->categories = $modelcat->getItems();


//        $jinput = JFactory::getApplication()->input;
//        $dataf = $jinput->post->get('jform', array(), 'array');
//        $gmodel = $this->getModel('gallery');
//        $this->pictures = $gmodel->getAllPictureGallery($dataf);


        $this->pictures         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');


        $this->f_search_drp	= $this->state->get('filter.search_drp');
        $this->f_gallery_id	= $this->state->get('filter.gallery_id');
        $this->f_image_mbulk_copy = $this->state->get('filter.image_mbulk_copy');
        $this->f_limit_drp = $this->state->get('list.limit');
        $this->f_order_by = $this->state->get('filter.order_by');
        if (empty($this->f_order_by)){
            $this->f_order_by ='id.DESC';
        }



        parent::display($tpl);
        $app = JFactory::getApplication();
        if($app->isAdmin()){
            $this->addToolbar();
        }
    }

    protected function addToolbar()
    {

        $canDo	= DroppicsHelper::getActions();

        JToolBarHelper::title(JText::_('Droppics Image information'), 'droppics.png');


        if ($canDo->get('core.admin')) {
            if(droppicsBase::checkConfigUpdate()==true){
                $toolbar = JToolBar::getInstance();
                $toolbar->appendButton( 'popup', 'loop', JText::_('COM_DROPPICS_VIEW_DROPPICS_CHECKERROR'), 'index.php?option=com_droppics&view=droppics&layout=configupdated&tmpl=component', 500, 150,0,0,'window.location.reload();' );
            }
            JToolBarHelper::preferences('com_droppics');
            JToolbarHelper::custom('com.droppics', 'home-2', 'home-2s', 'Gallery Manager', false);
        }

        JToolBarHelper::divider();
    }
}