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

jimport('joomla.application.component.modeladmin');
//jimport('joomla.access.access');

require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_categories'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'category.php');

class DroppicsModelCategory extends CategoriesModelCategory
{
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Category', $prefix = 'DroppicsTable', $config = array()){
            return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Get category file
     * @param type $id
     * @return boolean
     */
    public function getCategory($id){
        $dbo = $this->getDbo();
        $query = 'SELECT p.id,p.file,p.ordering,p.ext,p.title,p.created_time,p.modified_time,p.hits,p.size,p.version,p.author,g.params,g.id AS catid FROM #__droppics AS g RIGHT JOIN #__droppics_files as p ON g.id=p.catid WHERE g.id='.$dbo->quote($id).' ORDER BY ordering ASC';
        $dbo->setQuery($query);
        if($dbo->query()){
            return $dbo->loadObjectList();
        }
        return false;
    }
    
    /**
     * Get the current theme from a category id
     * @param type $id
     * @return boolean
     */
    public function getCategoryTheme($id){
        $dbo = $this->getDbo();
        $query = 'SELECT theme FROM #__droppics WHERE id='.$dbo->quote($id);
        $dbo->setQuery($query);
        if($dbo->query()){
            return $dbo->loadResult();
        }
        return false;
    }
    
    /**
     * Get the params from a category id
     * @param type $id
     * @return boolean
     */
    public function getCategoryParams($id){
        $dbo = $this->getDbo();
        $query = 'SELECT params FROM #__droppics WHERE id='.$dbo->quote($id);
        $dbo->setQuery($query);
        if($dbo->query()){
            return json_decode($dbo->loadResult());
        }
        return false;
    }
    
    /**
     * Set the title of a category
     * @param int $id_category
     * @param string $title
     * @return int 
     */
    public function setTitle($id_category,$title){
        $dbo = $this->getDbo();
        if($title==''){
            return false;
        }
        $filter = JFilterInput::getInstance();
        $title = $filter->clean($title);
        
        $table = $this->getTable();
        if(!$table->load($id_category)){
            return false;
        }
        if(!$table->bind(array('title'=>$title))){
            return false;
        }
        if(!$table->store()){
            return false;
        }
        return true;
    }

    /**
     * Set the theme of a category
     * @param int $id_category
     * @param string $theme
     * @return int 
     */
    public function setTheme($id_category,$theme){
        $dbo = $this->getDbo();
        if($theme==''){
            return false;
        }        
        $query = 'UPDATE #__droppics SET theme='.$dbo->quote($theme).' WHERE id='.$dbo->quote($id_category);
        $dbo->setQuery($query);
        if($dbo->query()){
            return true;
        }
        return false;
    }

        /**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getFormParams($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_droppics.categoryparams', 'categoryparams', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
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
        // Check the session for previously entered form data.
        $data = $this->getItem();
        return $data;
    }
    
    /**
    * Method to test whether a record can be deleted.
    *
    * @param   object  $record  A record object.
    *
    * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
    *
    * @since	1.6
    */
   protected function canDelete($record)
   {
           if (!empty($record->id))
           {
                   $user = JFactory::getUser();

                   return $user->authorise('core.delete', $record->extension . '.category.' . (int) $record->id);
           }
   }
   
   public function delete(&$pks) {
        if(parent::delete($pks)){
            foreach($pks as $i=>$pk){
                $pks[$i] = (int)$pk;
            }
            //Delete files under category
            $dbo = $this->getDbo();
            $query = 'DELETE FROM #__droppics_pictures WHERE id_gallery IN ('.implode(',', $pks).')';
            $dbo->setQuery($query);
            if(!$dbo->query()){
                return false;
            }
            $query = 'DELETE FROM #__droppics WHERE id IN ('.implode(',', $pks).')';
            $dbo->setQuery($query);
            if(!$dbo->query()){
                return false;
            }
            return true;
        }
        return false;
   }
   
   public function save($data) {
       $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
       if(parent::save($data) && !$id){

           //this is a new category
           $id = (int) $this->getState($this->getName() . '.id');
        // change new category to first position
           $table = $this->getTable("Category","CategoriesTable",  array());
           $ref=1;
           $position = 'first-child';
           if($table->moveByReference($ref,$position,$id)){
                //do nothing
           }

           $dbo = $this->getDbo();
           //$query = 'INSERT INTO #__droppics (id) VALUES ('.$id.')';
           //add new gallery default theme with value "default"
           $themes = "'default'";
           $params = (array)droppicsBase::getParamsWithTheme();
           $params = json_encode(json_encode($params));
           $query = 'INSERT INTO #__droppics (id,theme,params) VALUES ('.$id.','.$themes.','.$params.')';
           $dbo->setQuery($query);
           if(!$dbo->query()){
                return false;
           }
           return true;
       }
   }
   
   //There is no ckeckin or checkout in ajax
   public function checkin($pks = array()) {
       return true;
   }
   
   public function checkout($pk = null) {
       return true;
   }
}