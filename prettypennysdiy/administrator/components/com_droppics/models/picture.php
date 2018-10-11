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

jimport('joomla.application.component.modeladmin');

/**
 * Weblinks model.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_weblinks
 * @since		1.5
 */
class DroppicsModelPicture extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_DROPPICS';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id)) {
			if ($record->state != -2) {
				return ;
			}
			$user = JFactory::getUser();

			if ($record->catid) {
				return $user->authorise('core.delete', 'com_droppics.category.'.(int) $record->catid);
			}
			else {
				return parent::canDelete($record);
			}
		}
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_droppics.category.'.(int) $record->catid);
		}
		else {
			return parent::canEditState($record);
		}
	}
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Picture', $prefix = 'DroppicsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_droppics.picture', 'picture', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
           
                //Get the theme
                $id_gallery = JRequest::getInt('id_gallery',0);
                if(!$id_gallery){
                    return false;
                }
                $dbo = $this->getDbo();
                $query = 'SELECT theme FROM #__droppics WHERE id='.$dbo->quote($id_gallery);
                $dbo->setQuery($query);
                if(!$dbo->query()){
                    $theme = 'default';
                }else{
                    $theme = $dbo->loadResult();
                }
                
                
                JPluginHelper::importPlugin('droppics');
                $dispatcher = JDispatcher::getInstance();
                $dispatcher->trigger('getImageParamForm',array($theme,&$form));
                
                // If type is already known we can load the plugin form
                $type = empty($data['type'])?$form->getValue('type'):$data['type'];
                
                if(isset($type)){
                    JPluginHelper::importPlugin('droppics');
                    $dispatcher = JDispatcher::getInstance();
                    $dispatcher->trigger('getPictureForm',array($type,&$form));
                }
                if (isset($loadData) && $loadData){
                        // Get the data for the form.
                        $data = $this->loadFormData();
                        $form->bind($data);
                }

		// Determine correct permissions to check.
		if ($this->getState('picture.id')) {
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
		} else {
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
                $data = $this->getItem();

                $data->file = JFile::stripExt($data->file);
		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
            return parent::getItem($pk);
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table)
	{                    
		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alt		= htmlspecialchars_decode($table->alt, ENT_QUOTES);
                $dbo = $this->getDbo();
                $dbo->setQuery('SELECT file FROM #__droppics_pictures WHERE id='.(int)$table->id);
                $currentFilename = $dbo->loadResult();
                $exploded = explode('.', $currentFilename);
                $currentExt = $exploded[count($exploded)-1];
                $table->file            = DroppicsBase::makeSafeFilename($table->file.'.'.$currentExt);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = '.(int) $table->catid;
		return $condition;
	}

        
        public function validate($form, $data, $group = null) {
            $return = true;
            //fix missing http on custom link
            if(isset($data['params']['custom_link']) ) {
                if( strpos($data['params']['custom_link'],'http') === false) { 
                    $data['params']['custom_link'] = 'http://' . $data['params']['custom_link']  ;                     
                }
            }
            return $return==false ? $return : parent::validate($form, $data, $group);
        }
        
        public function getPicture($id){
            $dbo = $this->getDbo();
            $query = 'SELECT * FROM #__droppics_pictures '
                    . 'WHERE '
                        . 'id='.$dbo->quote($id);
            $dbo->setQuery($query);
            if($dbo->query()){
                return $dbo->loadObject();
            }
            return false;
        }
        
        public function getPictureUrl($imgp_source,$item,$pic){
            if($imgp_source == 'thumbnail'){
                $imgpath = droppicsBase::getThumbnailURL($item->id_gallery).$pic->file;
            }elseif($imgp_source == 'original'){
                //$imgpath = COM_MEDIA_BASEURL.'/com_droppics/'.$item->id_gallery.'/'.$pic->file;
                $imgpath = droppicsBase::getPictureURL($item->id_gallery).$pic->file;
            }elseif($imgp_source == 'custom'){
                $imgpath = '';
            }else{
                $id_custom = str_replace('custom_','',$imgp_source);
                $file = $this->getPictureCustom($id_custom);
                if(!empty($file)){
                    $imgpath = droppicsBase::getCustomURL($item->id_gallery).$file->file;
                }else{
                    $imgpath = droppicsBase::getPictureURL($item->id_gallery).$pic->file;
                }
            }
            
            return $imgpath;
        }
        
        public function getPictureCustom($id){
            $dbo = $this->getDbo();
            $query = 'SELECT * FROM #__droppics_custom '
                    . 'WHERE '
                        . 'id='.$dbo->quote($id);
            $dbo->setQuery($query);
            if($dbo->query()){
                return $dbo->loadObject();
            }
            return false;
        }
}
