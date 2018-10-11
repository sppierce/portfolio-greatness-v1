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

jimport('joomla.application.component.viewlegacy');

class DroppicsViewPicture extends JViewLegacy
{
        protected $state;
	protected $item;
	protected $form;
        protected $pic;
        protected $imgpath;
        

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->_layout = 'edit';
                
		$this->state            = $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
        $componentParams = JComponentHelper::getParams('com_droppics');
        if(empty($this->item->params)){
            $this->item->params =$componentParams->toArray();
        }
                $this->canDo = DroppicsHelper::getActions();
                
                $pmodel = $this->getModel();
                $this->pic = $pmodel->getPicture($this->item->id);
                if(!empty($this->item->params['imgp_source'])){
                    $this->imgpath = $pmodel->getPictureUrl($this->item->params['imgp_source'],$this->item,$this->pic);
                }else{
                    $this->imgpath = $pmodel->getPictureUrl('thumbnail',$this->item,$this->pic);
                }
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
	}

}
