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

require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_categories'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'category.php');

class DroppicsControllerCategory extends CategoriesControllerCategory
{

    private $savedId;


    /**
     * Set a file title
     */
    public function setTitle(){
        $id_category = JRequest::getInt('id_gallery',0);
        
        $model = $this->getModel();
        $canDo = DroppicsHelper::getActions();
        if(!$canDo->get('core.edit')){
            if($canDo->get('core.edit.own')){
                $gallery = $model->getItem($id_category);
                if($gallery->created_user_id != JFactory::getUser()->id){
                    $this->exit_status('not permitted');
                }
            }else{
                $this->exit_status('not permitted');
            }
        }

        
        
        $title = JFactory::getApplication()->input->getString('title');
        
        $model = $this->getModel();
        if($model->setTitle($id_category,$title)){
            $return = true;
        }else{
            $return = false;
        }
        echo json_encode($return);
        JFactory::getApplication()->close();
    }

    /**
     * Method to add a category 
     */
    public function addCategory(){
        $canDo = DroppicsHelper::getActions();
        if(!$canDo->get('core.create')){
            $this->exit_status('not permitted');
        }
        
        $datas = array();
        $datas['jform']['extension'] = 'com_droppics';
        $datas['jform']['title'] = JText::_('COM_DROPPICS_MODEL_GALLERY_DEFAULT_NAME');
        $datas['jform']['alias'] = JText::_('COM_DROPPICS_MODEL_GALLERY_DEFAULT_NAME').'-'.date('dmY-h-m-s',time());
        $datas['jform']['parent_id'] = 1;
        $datas['jform']['published'] = 1;
        $datas['jform']['language'] = '*';
        $datas['jform']['metadata']['tags'] = '';
        
        //Set state value to retreive the correct table
        $model = $this->getModel();
        $model->setState('category.extension', 'category');
        
        $app   = JFactory::getApplication();
        $app->input->set('id',0);
        foreach ($datas as $data => $val) {
            JRequest::setVar($data, $val, 'POST');
        }
        if($this->save())
        {
            $this->exit_status(true,array('id_category'=> $this->savedId ,'name'=>JText::_('COM_DROPPICS_MODEL_GALLERY_DEFAULT_NAME')));
        }
        $this->exit_status('error while adding gallery');
    }
    

    protected function postSaveHookJ25(&$model, $validData = array()) {
        $this->savedId = $model->getState($model->getName().'.id');
        parent::postSaveHook($model, $validData);
    }
    
    protected function postSaveHookJ3(JModelLegacy $model, $validData = array()) {
        $this->savedId = $model->getState($model->getName().'.id');
        parent::postSaveHook($model, $validData);
    }
    
    
    public function setParams() {
        $datas = JRequest::getVar('jform', null, 'default', 'array');
        
        $model = $this->getModel();
        $canDo = DroppicsHelper::getActions();
        if(!$canDo->get('core.edit')){
            if($canDo->get('core.edit.own')){
                $gallery = $model->getItem((int)$datas['id']);
                if($gallery->created_user_id != JFactory::getUser()->id){
                    $this->exit_status('not permitted');
                }
            }else{
                $this->exit_status('not permitted');
            }
        }
        
        $item = get_object_vars($model->getItem((int)$datas['id']));
        $item['access'] = (int)$datas['access'];
        

        //Set state value to retreive the correct table
        $model->setState('category.extension', 'categoryparams');
        $this->input->post->set('jform', $item);
        $id = parent::save();
        if($id)
        {
            $this->exit_status(true);
        }
        $this->exit_status('error while saving params');
    }


    /**
     * Method to add a category 
     */
//    public function delCategory(){
//        $id_category = JRequest::getInt('id_category');
//        if($id_category<=0){
//            $this->exit_status('error');
//        }
//        $model = $this->getModel();
//        
//        $path = droppicsBase::getFilesPath($id_category);
//        if(is_dir($path)){
//            if(!JFile::delete($path)){
//                $this->exit_status('error while deleting directory');
//            }
//        }
//        
//        if(!$model->delCategory($id_category)){
//            $this->exit_status('error while deleting category');
//        }
//        $this->exit_status(true);
//    }
        
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
    
    /**
     * We cannot checkin and checkout because we use ajax
     */
    protected function checkEditId($context, $id){
                return true;
    }
    
    
    public function getRedirectToItemAppend($recordId = NULL, $urlVar = 'id'){
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
        $canDo	= DroppicsHelper::getActions();
        if($canDo->get('core.edit') || $canDo->get('core.edit.own')){
            return true;
        }
    }
    
    protected function allowAdd($data=array()) {
        $canDo	= DroppicsHelper::getActions();
        if((int)($canDo->get('core.create'))){
            return true;
        }
        return false;
    }
    
    	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   12.2
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$lang  = JFactory::getLanguage();
		$model = $this->getModel();
		$table = $model->getTable();
		$data  = $app->input->get('jform', array(), 'array');
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";
		$task = $this->getTask();

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		$recordId = $app->input->getInt($urlVar);

//		if (!$this->checkEditId($context, $recordId))
//		{
//			// Somehow the person just went to the form and tried to save it. We don't allow that.
//			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
//			$this->setMessage($this->getError(), 'error');
//
//			$this->setRedirect(
//				JRoute::_(
//					'index.php?option=' . $this->option . '&view=' . $this->view_list
//					. $this->getRedirectToListAppend(), false
//				)
//			);
//
//			return false;
//		}

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// The save2copy task needs to be handled slightly differently.
//		if ($task == 'save2copy')
//		{
//			// Check-in the original row.
//			if ($checkin && $model->checkin($data[$key]) === false)
//			{
//				// Check-in failed. Go back to the item and display a notice.
//				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
//				$this->setMessage($this->getError(), 'error');
//
//				$this->setRedirect(
//					JRoute::_(
//						'index.php?option=' . $this->option . '&view=' . $this->view_item
//						. $this->getRedirectToItemAppend($recordId, $urlVar), false
//					)
//				);
//
//				return false;
//			}
//
//			// Reset the ID and then treat the request as for Apply.
//			$data[$key] = 0;
//			$task = 'apply';
//		}

		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}

		// Save succeeded, so check-in the record.
//		if ($checkin && $model->checkin($validData[$key]) === false)
//		{
//			// Save the data in the session.
//			$app->setUserState($context . '.data', $validData);
//
//			// Check-in failed, so go back to the record and display a notice.
//			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
//			$this->setMessage($this->getError(), 'error');
//
//			$this->setRedirect(
//				JRoute::_(
//					'index.php?option=' . $this->option . '&view=' . $this->view_item
//					. $this->getRedirectToItemAppend($recordId, $urlVar), false
//				)
//			);
//
//			return false;
//		}

		$this->setMessage(
			JText::_(
				($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
					? $this->text_prefix
					: 'JLIB_APPLICATION') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
			)
		);

		// Redirect the user and adjust session state based on the chosen task.
//		switch ($task)
//		{
//			case 'apply':
//				// Set the record data in the session.
//				$recordId = $model->getState($this->context . '.id');
//				$this->holdEditId($context, $recordId);
//				$app->setUserState($context . '.data', null);
//				$model->checkout($recordId);
//
//				// Redirect back to the edit screen.
//				$this->setRedirect(
//					JRoute::_(
//						'index.php?option=' . $this->option . '&view=' . $this->view_item
//						. $this->getRedirectToItemAppend($recordId, $urlVar), false
//					)
//				);
//				break;
//
//			case 'save2new':
//				// Clear the record id and data from the session.
//				$this->releaseEditId($context, $recordId);
//				$app->setUserState($context . '.data', null);
//
//				// Redirect back to the edit screen.
//				$this->setRedirect(
//					JRoute::_(
//						'index.php?option=' . $this->option . '&view=' . $this->view_item
//						. $this->getRedirectToItemAppend(null, $urlVar), false
//					)
//				);
//				break;
//
//			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);

				// Redirect to the list screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
//				break;
//		}
        // Invoke the postSave method to allow for the child class to access the model.
        $this->postSaveHookJ3($model, $validData);
        return true;
    }
}