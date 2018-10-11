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

class DroppicsViewGallery extends JViewLegacy
{
	
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null){
            if(JRequest::getCmd('layout')=='form'){   
                $model = $this->getModel();
                $this->form = $model->getForm();
            }else{
                $id_gallery = JRequest::getInt('id_gallery',null);
                $model = $this->getModel();
                $gmodel = $this->getModel('gallery');
                $pictures = $model->getGallery($id_gallery);
                
                JPluginHelper::importPlugin('droppics');
                $dispatcher = JDispatcher::getInstance();
                $content = $dispatcher->trigger('onShowAdminGallery',array(array('id_gallery'=>$id_gallery,'theme'=>$gmodel->getGalleryTheme($id_gallery),'pictures'=>$pictures,'params'=>  $gmodel->getGalleryParams($id_gallery))));
                
                $this->content="";
                if(!empty($content[0])){
                    $this->content = $content[0];
                }
            }
            parent::display($tpl);
	}
}
