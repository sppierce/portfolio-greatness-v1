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

jimport('joomla.application.component.controllerform');

class droppicsControllerPicture extends JControllerForm
{
    
        public function save($key = null, $urlVar = null) {
            $recordId = JRequest::getInt('id');
            $context = "$this->option.edit.$this->context";
            $this->holdEditId($context, $recordId);
            if(parent::save($key, $urlVar)){
                $model = $this->getModel();
                $item = $model->getItem($recordId);
                $this->exit_status(true,  $item);
            }else{
                $this->exit_status($this->getError());
            }
        }
    
        
    /**
     * Return a json response
     * @param $status
     * @param array $datas array of datas to return with the json string
     * 
     */
    private function exit_status($status,$datas=array()){
            $response = array('response'=>$status,'datas'=>$datas);            
            echo json_encode($response);
            JFactory::getApplication()->close();
    }

    protected function allowEdit($data = array(), $key = 'id')
    {
        $canDo = DroppicsHelper::getActions();
        if(!$canDo->get('core.edit')){
            if($canDo->get('core.edit.own')){
                $category = $model->getItem($data[$key]);
                if($category->created_user_id != JFactory::getUser()->id){
                    return false;
                }
            }else{
                return false;
            }
        }
        return true;
    }
}
