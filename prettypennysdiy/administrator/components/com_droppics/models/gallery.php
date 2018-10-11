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


class DroppicsModelGallery extends JModelAdmin
{   
    public function getGalleries(){
        $dbo = $this->getDbo();
        $query = 'SELECT * FROM #__droppics';        
        if($dbo->setQuery($query)){
            return $dbo->loadObjectList();
        }
        return false;
    }
    
    /**
     * Get gallery picture
     * @param type $id
     * @return boolean
     */
    public function getGallery($id){
        $dbo = $this->getDbo();

        $params = JComponentHelper::getParams('com_droppics');
        $t = $params->get('lastmodified','');
        if($t) {
            $t = '?'.$t;
        }
        $query = 'SELECT '
                    . 'p.id,'
                    . 'p.file, '
                    . 'concat(p.file,"'.$t.'") as filever,'
                    . 'p.position,'
                    . 'p.alt,'
                    . 'p.title,'
                    . 'p.params as picture_params,'
                    . 'g.theme,'
                    . 'g.params,'
                    . 'g.id AS id_gallery '
                . 'FROM #__droppics AS g '
                . 'RIGHT JOIN #__droppics_pictures as p '
                    . 'ON g.id=p.id_gallery '
                . 'WHERE '
                    . 'g.id='.$dbo->quote($id).' '
                . 'ORDER BY position ASC';
        $dbo->setQuery($query);
        if($dbo->query()){
            return $dbo->loadObjectList();
        }
        return false;
    }

    //get all image
    public function getAllPictureGallery($dataf){
        $id_gallery = 0;
        $image_mbulk_copy ='all';
        $image_search_text = '';
        $imgs_per_page = '0';
        if (!empty($dataf)){
            $dataform = $dataf['params'];
            $id_gallery = $dataform['gallery_id'];
            $image_mbulk_copy = $dataform['image_mbulk_copy'];
            $image_search_text = $dataform['image_search_text'];
        }
        $dbo = $this->getDbo();
        $params = JComponentHelper::getParams('com_droppics');
        $t = $params->get('lastmodified','');
        if($t) {
            $t = '?'.$t;
        }
        $query = 'SELECT '
            . 'p.id,'
            . 'p.file, '
            . 'concat(p.file,"'.$t.'") as filever,'
            . 'p.position,'
            . 'p.alt,'
            . 'p.title,'
            . 'p.params as picture_params,'
            . 'p.upload_date, '
            . 'g.theme,'
            . 'g.params,'
            . 'g.id AS id_gallery '
            . 'FROM #__droppics AS g '
            . 'RIGHT JOIN #__droppics_pictures as p '
            . 'ON g.id=p.id_gallery ';
            $query.= 'WHERE 1 = 1 ';
            if($id_gallery){
                $query.= ' And g.id='.$dbo->quote($id_gallery).' ';
            }
            if($image_search_text){
                $query.= " And p.file LIKE '%".$image_search_text."%'";
            }
            $query.= ' ORDER BY position ASC';
        $dbo->setQuery($query);
        if($dbo->query()){
            return $dbo->loadObjectList();
        }
        return false;
    }

//get gallery by prams and theme id
    public function getGalleryByPrams($id , $params,$theme)
    {
        $dbo = $this->getDbo();

        $params_com = JComponentHelper::getParams('com_droppics');
        $t = $params_com->get('lastmodified','');
        if($t) {
            $t = '?'.$t;
        }
        $query = 'SELECT '
            . 'p.id,'
            . 'p.file,'
            . 'concat(p.file,"'.$t.'") as filever,'
            . 'p.position,'
            . 'p.alt,'
            . 'p.title,'
            . 'p.params as picture_params,'
            . 'g.theme,'
            . 'g.params,'
            . 'g.id AS id_gallery '
            . 'FROM #__droppics AS g '
            . 'RIGHT JOIN #__droppics_pictures as p '
            . 'ON g.id=p.id_gallery '
            . 'WHERE '
            . 'g.id=' . $dbo->quote($id) . ' '
            . $this->whereClauseParams($params,$theme);

        $dbo->setQuery($query);
        if ($dbo->query()) {
            return $dbo->loadObjectList();
        }
        return false;
    }



    public function getGalleryFromOldId($id)
    {
        $dbo = $this->getDbo();
        $query = 'SELECT '
                    . 'd.id '
                . 'FROM #__droppics AS d '
                . 'WHERE '
                    . 'd.old_id='.(int)$id;
        $dbo->setQuery($query);
        if($dbo->query()){
            return $dbo->loadResult();
        }
        return false;
    }
    
    /**
     * Get the current theme from a gallery id
     * @param type $id
     * @return boolean
     */
    public function getGalleryTheme($id){
        $dbo = $this->getDbo();
        $query = 'SELECT theme FROM #__droppics WHERE id='.$dbo->quote($id);
        $dbo->setQuery($query);
        if($dbo->query()){
            return $dbo->loadResult();
        }
        return false;
    }
    
    /**
     * Get the params from a gallery id
     * @param type $id
     * @return boolean
     */
    public function getGalleryParams($id){
        $dbo = $this->getDbo();
        $query = 'SELECT params FROM #__droppics WHERE id='.$dbo->quote($id);
        $dbo->setQuery($query);
        if($dbo->query()){
            return json_decode($dbo->loadResult());
        }
        return false;
    }
    
    /**
     * Set the title of a gallery
     * @param int $id_gallery
     * @param string $title
     * @return int 
     */
    public function setTitle($id_gallery,$title){
        $dbo = $this->getDbo();
        if($title==''){
            return false;
        }
        $filter = JFilterInput::getInstance();
        $title = $filter->clean($title);
        
        $query = 'UPDATE #__droppics SET name='.$dbo->quote($title).' WHERE id='.$dbo->quote($id_gallery);
        $dbo->setQuery($query);
        if($dbo->query()){
            return $dbo->getAffectedRows();
        }
        return false;
    }

    /**
     * Set the theme of a gallery
     * @param int $id_gallery
     * @param string $theme
     * @return int 
     */
    public function setTheme($id_gallery,$theme){
        $dbo = $this->getDbo();
        if($theme==''){
            return false;
        }
        $params = (array)droppicsBase::getParamsWithTheme($theme);
        $params = json_encode(json_encode($params));
        $query = 'UPDATE #__droppics SET theme='.$dbo->quote($theme).', params='.$params.' WHERE id='.$dbo->quote($id_gallery);
        $dbo->setQuery($query);
        if($dbo->query()){
            return true;
        }
        return false;
    }
    
//    public function getTempGallery(){
//        $dbo = $this->getDbo();
//        $query = 'SELECT id FROM #__droppics WHERE tmp=1';
//        if($dbo->setQuery($query)){
//            return $dbo->loadResult();
//        }
//        return false;
//    }
    
//    public function addGallery(){
//        $dbo = $this->getDbo();
//        $query = 'INSERT INTO #__droppics (name) VALUES ('.$dbo->quote(JText::_('COM_DROPPICS_MODEL_GALLERY_DEFAULT_NAME')).')';
//        $dbo->setQuery($query);
//        if($dbo->query()){
//            return $dbo->insertid();
//        }
//        return false;
//    }
    
    /**
     * Method to delete a gallery and all pictures
     * @param int $id_gallery
     * @return boolean 
     */
    public function delGallery($id_gallery){
        $dbo = $this->getDbo();
        $query = 'DELETE FROM #__droppics_pictures WHERE id_gallery='.$dbo->quote($id_gallery);
        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        $query = 'DELETE FROM #__droppics WHERE id='.$dbo->quote($id_gallery);
        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        return true;
    }

   public function getForm($data = array(), $loadData = true){
        $id_gallery = JRequest::getInt('id',0);
        if(!$id_gallery){
            return false;
        }
        //Get the theme
        $dbo = $this->getDbo();
        $query = 'SELECT theme,params FROM #__droppics WHERE id='.$dbo->quote($id_gallery);
        $componentParams = JComponentHelper::getParams('com_droppics');

        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        $gallery = $dbo->loadObject();
        
        // Get the form.
        $xmlform = '<form>
            <fieldset>
                <field name="id" type="hidden" default="0" />
            </fieldset>
        </form>';
        $form = $this->loadForm('com_droppics.galery', $xmlform, array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
                return false;
        }

        // If type is already known we can load the plugin form
        if(isset($gallery->theme)){
            JPluginHelper::importPlugin('droppics');
            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger('getParamForm',array($gallery->theme,&$form));
        }
        if (isset($loadData) && $loadData) {
            // Get the data for the form.
            $data = $this->loadFormData();
            // add params main default
            if(empty($data->params )){
                $data->params = $componentParams->toArray();
                if ($data->theme =='default') {
                    $keys = array_keys($data->params);
                    $values = array_values($data->params);
                    $new_keys = str_replace('default_', '', $keys);
                    $data->params = array_combine($new_keys, $values);
                }
            }
            $form->bind($data);
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

    
//    public function getParams($id_gallery){
//        $dbo = $this->getDbo();
//        $query = 'SELECT params FROM #__droppics WHERE id='.$dbo->quote($id_gallery);
//        $dbo->setQuery($query);
//        if(!$dbo->query()){
//            return false;
//        }
//        $params = $dbo->loadResult();
//        return json_decode($params);
//    }
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Gallery', $prefix = 'DroppicsTable', $config = array()){
            return JTable::getInstance($type, $prefix, $config);
    }

    //add where clause sort by for gallery
    public function whereClauseParams($params,$theme)
    {
        $sqlcloser ='  ';
        $app = JFactory::getApplication();
        $sort_by = '0';
        if ($app->isSite()) {
            $sort_by = droppicsBase::loadValue((array)$params,$theme.'_sort_by',0);
            if (!$sort_by){
                $sort_by = droppicsBase::loadValue((array)$params,'sort_by',0);
            }
        }
        if (isset($sort_by)) {
            switch ($sort_by) {
                case "1":
                    $sqlcloser = ' ORDER BY upload_date ASC ';
                    break;
                case "2":
                    $sqlcloser = ' ORDER BY upload_date DESC ';
                    break;
                case "3":
                    $sqlcloser = ' ORDER BY file ASC ';
                    break;
                case "4":
                    $sqlcloser = ' ORDER BY file DESC ';
                    break;
                case "5":
                    $sqlcloser = ' ORDER BY RAND() ';
                    break;
                default:
                    $sqlcloser = ' ORDER BY position ASC ';
            }
        }else{
            $sqlcloser = ' ORDER BY position ASC ';
        }
        return $sqlcloser;
    }

}