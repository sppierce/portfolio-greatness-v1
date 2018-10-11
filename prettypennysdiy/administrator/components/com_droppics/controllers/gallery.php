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
jimport('joomla.filesystem.folder');

class DroppicsControllerGallery extends JControllerForm
{

    /**
     * Set a picture title
     */
    
    /**
     * Method to change a gallery theme
     */
    public function setTheme(){
        $id_gallery = JRequest::getInt('id_gallery');
        
        $model = $this->getModel('category');
        $canDo = DroppicsHelper::getActions();
        if(!$canDo->get('core.edit')){
            if($canDo->get('core.edit.own')){
                $gallery = $model->getItem($id_gallery);
                if($gallery->created_user_id != JFactory::getUser()->id){
                    $this->exit_status('not permitted');
                }
            }else{
                $this->exit_status('not permitted');
            }
        }

        
        $theme = JRequest::getCmd('theme');
        if($id_gallery<=0){
            $this->exit_status('error');
        }
        $model = $this->getModel();
        
        if(!$model->setTheme($id_gallery,$theme)){
            $this->exit_status('error while changing theme');
        }
        $this->exit_status(true);
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
    
    protected function checkEditId($context, $id){
                return true;
    }
    
    public function save($key = null, $urlVar = null) {
        $model = $this->getModel('category');
        $canDo = DroppicsHelper::getActions();
        if(!$canDo->get('core.edit')){
            if($canDo->get('core.edit.own')){
                $gallery = $model->getItem(JFactory::getApplication()->input->getInt('id', 0));
                if($gallery->created_user_id != JFactory::getUser()->id){
                    $this->exit_status('not permitted');
                }
            }else{
                $this->exit_status('not permitted');
            }
        }
        
        if(parent::save($key, $urlVar)){
                $this->exit_status(true);
        }
        $errormsg = $this->getErrors();
        if($errormsg){
            $this->exit_status($errormsg);
        }
        $app = JFactory::getApplication();
        $errors = $app->getMessageQueue();        
        if(!empty($errors)){
            $errormsg='';
            foreach ($errors as $error) {
                $errormsg .= $error['message'].'<br/>';
            }
            $this->exit_status($errormsg);
        }
        
        $this->exit_status('error');
    }
    
    public function getRedirectToItemAppend($recordId = null, $urlVar = 'id'){
        $append = parent::getRedirectToItemAppend($recordId, $urlVar);
        
        $format = JRequest::getCmd('format', 'raw');

        // Setup redirect info.
        if ($format)
        {
                $append .= '&format=' . $format;
        }
        return $append; 
    }    
    
    protected function allowEdit($data = array(), $key = 'id')
    {
        return true;
    }
    //check exit gallery
    public function ajaxCheckExitGallery(){
        $model = $this->getModel('gallery');
        $exist = false;
        if ($model->getGalleryTheme(JFactory::getApplication()->input->getInt('id_gallery', 0))){
            $exist = true;
        }
        else{
            $exist = false;
        }
        $this->exit_status(true, array('exist'=> $exist));
        JFactory::getApplication()->close();
    }
}