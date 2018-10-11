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
jimport('joomla.access.access');


class DroppicsModelFiles extends JModelLegacy
{       
    /**
     * Method to add a file into database
     * @param string $file 
     * @param int   $id_gallery
     * @return inserted row id, false if an error occurs
     */
    public function addPicture($file,$id_gallery,$alt='',$params=''){
        $dbo = $this->getDbo();
        $position = $this->getNextPosition($id_gallery);
        $query = 'INSERT INTO #__droppics_pictures (file,id_gallery,position,alt,upload_date,params) VALUES ('.$dbo->quote
            ($file).','
            .$dbo->quote($id_gallery).','.$dbo->quote($position).','.$dbo->quote(htmlspecialchars($alt)).','
            .$dbo->quote(date('Y-m-d H:i:s')).','.$dbo->quote(json_encode($params)).')';
        $dbo->setQuery($query);
        if(!$dbo->query()){
//            $dbo->getErrorMsg();
            return false;
        }
        return $dbo->insertid();
    }
// add image on top (admin) in gallery
    public function addPictureCheckParam($file,$id_gallery,$alt='',$params=''){
        $dbo = $this->getDbo();
        $query_up = 'UPDATE #__droppics_pictures SET position = position + 1 WHERE id_gallery ='.(int)($id_gallery);
        $dbo->setQuery($query_up);
        $dbo->query();
        $query = ' INSERT INTO #__droppics_pictures (file,id_gallery,position,alt,upload_date,params) VALUES ('
            .$dbo->quote
            ($file).','
            .$dbo->quote($id_gallery).','.'0'.','.$dbo->quote(htmlspecialchars($alt)).','
            .$dbo->quote(date('Y-m-d H:i:s')).','.$dbo->quote(json_encode($params)).')';
        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        return $dbo->insertid();
    }

     /**
     * Method to add a file into database
     * @param string $file 
     * @param int   $id_gallery
     * @return inserted row id, false if an error occurs
     */
    public function addVideo($file,$params,$id_gallery,$alt=''){
        $dbo = $this->getDbo();
        $position = $this->getNextPosition($id_gallery);
       
        $query = 'INSERT INTO #__droppics_pictures (file,id_gallery,position,alt,title,params) VALUES ('.
                $dbo->quote($file).','.$dbo->quote($id_gallery).','.$dbo->quote($position).','.$dbo->quote(htmlspecialchars($alt)).','.$dbo->quote(htmlspecialchars($alt)).','. $dbo->quote(json_encode($params) ) .')';
        $dbo->setQuery($query);
        if(!$dbo->query()){
//            $dbo->getErrorMsg();
            return false;
        }
        return $dbo->insertid();
    }
    
    /**
     * Methode to retrieve the next picture position for a gallery
     * @param int $id_gallery 
     * @return int next position
     */
    private function getNextPosition($id_gallery){
        $dbo = $this->getDbo();
        $query = 'SELECT position FROM #__droppics_pictures WHERE id_gallery='.$dbo->quote($id_gallery).' ORDER BY position DESC LIMIT 0,1';
        $dbo->setQuery($query);
        if($dbo->query() && $dbo->getNumRows()>0){
           return $dbo->loadResult()+1; 
        }
        return 0;
    }
    
    /**
     * Methode to retrieve picture information
     * @param int $id_picture 
     * @return object picture, false if an error occurs
     */
    public function getPicture($id_picture){
        $dbo = $this->getDbo();

        $params = JComponentHelper::getParams('com_droppics');
        $t = $params->get('lastmodified','');
        if($t) {
            $t = '?'.$t;
        }
        $query = 'SELECT '
            .'p.id, '
            .'p.id_gallery, '
            .'p.file,'
            .'concat(p.file,"'.$t.'") as filever,'
            .'p.position, '
            .'p.title, '
            .'p.alt, '
            .'p.params, '
            .'p.upload_date '
            .'FROM #__droppics_pictures as p WHERE p.id='.$dbo->quote($id_picture);
        $dbo->setQuery($query);
        if(!$dbo->query()){
           return false; 
        }
        return $dbo->loadObject();
    }
    
    /**
     * Methode to reorder 
     * @param int $id_gallery 
     * @param array $pictures
     * @return boolean result
     */
    public function reorder($pictures){
        $dbo = $this->getDbo();
        foreach ($pictures as $key => $picture) {
            $query = 'UPDATE #__droppics_pictures SET position = '.intval($key).' WHERE id='.intval($picture);
            $dbo->setQuery($query);
            if(!$dbo->query()){
                return false; 
            }
        }
        return true;
    }
    
    /**
     * Method to delete a file from the database
     * @param int   $id_file
     * @return number of affected rows, false if an error occurs
     */
    public function removePicture($id_file){
        $dbo = $this->getDbo();
        $query = 'DELETE FROM #__droppics_pictures WHERE id='.$dbo->quote($id_file);
        $dbo->setQuery($query);
        if(!$dbo->query()){
//            $dbo->getErrorMsg();
            return false;
        }
        return $dbo->getAffectedRows();
    }
    
    
    
    
    private function getPath($file){
        $slash = strrpos($file, '/');
        if ($slash !== false) {
             return substr($file, 0, $slash);
        }
        return '';
    }    
    
    
    /**
     * Methode to retrieve all pictures information
     * @return object picture, false if an error occurs
     */
    public function getAllPictures(){
        $dbo = $this->getDbo();
        $query = 'SELECT * FROM #__droppics_pictures';
        $dbo->setQuery($query);
        if(!$dbo->query()){
           return false; 
        }
        return $dbo->loadObjectList();
    }

    /**
     * Method to add a custom file into database
     * @param string $file 
     * @param int   $id_gallery
     * @return inserted row id, false if an error occurs
     */
    public function addCustomPicture($file,$custom_title,$id_picture,$width,$height){
        $dbo = $this->getDbo();
        $query = 'INSERT INTO #__droppics_custom (file,title,id_picture,width,height) VALUES ('.$dbo->quote($file).','.$dbo->quote($custom_title).','.(int)$id_picture.','.(int)$width.','.(int)$height.')';
        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        return $dbo->insertid();
    }

     /**
     * Method to change filename of a custom file in database     
     * @param int   $id
     * @param string $file 
     * @return true on success, false if an error occurs
     */
    public function changeCustomPicture($id, $file){
        $dbo = $this->getDbo();
        $query = 'UPDATE #__droppics_custom SET file='.$dbo->quote($file).' WHERE id='.$id ;
        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        return true;
    }
    
    /**
     * Load all custom picture for a file
     * @param int $picture
     * @return boolean
     */
    public function getCustomPictures($picture){
        $dbo = $this->getDbo();
        $query = 'SELECT * FROM #__droppics_custom WHERE id_picture='.$dbo->quote($picture);
        $dbo->setQuery($query);
        if(!$dbo->query()){
           return false; 
        }
        return $dbo->loadObjectList();
    }

    /**
     * Load a custom picture
     * @param int $id_custom
     * @return boolean
     */
    public function getCustomPicture($id_custom){
        $dbo = $this->getDbo();
        $query = 'SELECT * FROM #__droppics_custom WHERE id='.$dbo->quote($id_custom);
        $dbo->setQuery($query);
        if(!$dbo->query()){
           return false; 
        }
        return $dbo->loadObject();
    }

    /**
     * Method to delete a custom file from the database
     * @param int   $id_file
     * @return number of affected rows, false if an error occurs
     */
    public function removeCustom($id_custom){
        $dbo = $this->getDbo();
        $query = 'DELETE FROM #__droppics_custom WHERE id='.$dbo->quote($id_custom);
        $dbo->setQuery($query);
        if(!$dbo->query()){
//            $dbo->getErrorMsg();
            return false;
        }
        return $dbo->getAffectedRows();
    }

    public function updateAltPictures($pics_id = null,$pics_name = null,$id_gallery = null){
        $dbo = $this->getDbo();
        $query ='UPDATE `#__droppics_pictures` ';
        $query .= 'SET `alt` = ';
        if ($pics_id){
            $query .=' '. $dbo->quote($pics_name).' ';
            $query .=' WHERE `id` =' .$dbo->quote($pics_id).'';
            if ($id_gallery){
                $query .= ' AND `id_gallery`= '.$dbo->quote($id_gallery);
            }
        }else{
            $query .= ' SUBSTRING_INDEX(`file`,'.$dbo->quote('.').',1) ';
            if ($id_gallery){
                $query .= ' WHERE `id_gallery`= '.$dbo->quote($id_gallery);
            }
        }
        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        return true;
    }
    public function updateTitlePictures($pics_id,$pics_name,$id_gallery){
        $dbo = $this->getDbo();
        $query ='UPDATE `#__droppics_pictures` ';
        $query .= 'SET `title` = ';
        if ($pics_id){
            $query .=' '. $dbo->quote($pics_name).' ';
            $query .=' WHERE `id` =' .$dbo->quote($pics_id).' ';
            if ($id_gallery){
                $query .= ' AND `id_gallery`= '.$dbo->quote($id_gallery);
            }
        }else{
            $query .= ' SUBSTRING_INDEX(`file`,'.$dbo->quote('.').',1) ';
            if ($id_gallery){
                $query .= ' WHERE `id_gallery`= '.$dbo->quote($id_gallery);
            }
        }
        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        return true;
    }


    public function renamePictures($pics_id,$pics_name,$gallery_id){
        $dbo = $this->getDbo();
        if (!$this->checkPictureName($pics_id,$pics_name,$gallery_id)){
            return false;
        }
        $query ='UPDATE `#__droppics_pictures` ';
        $query .= 'SET `file` = '. $dbo->quote($pics_name).' ';
        $query .=' WHERE `id` =' .$dbo->quote($pics_id).' ';
        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        return true;
    }

    public function getParamsPicture($pics_id){
        $dbo = $this->getDbo();
        $dbo->setQuery('SELECT params FROM #__droppics_pictures WHERE id='.(int)$pics_id);
        return $dbo->loadObject();
    }

    public function checkPictureName($pics_id,$pics_name,$id_gallery) {
        $filenameFull = droppicsBase::getFullPicturePath($id_gallery).$pics_name;
        $filenameThumbnail = droppicsBase::getThumbnailPath($id_gallery).$pics_name;
        $filenamePicture = droppicsBase::getPicturePath($id_gallery).$pics_name;
        $dbo = $this->getDbo();
        $dbo->setQuery('SELECT file FROM #__droppics_pictures WHERE id='.(int)$pics_id);
        $current = $dbo->loadResult();

        if(!$pics_name){
            $this->setError(JText::_('COM_DROPPICS_CTRL_FILES_EMPTY'));
            return false;
        }
        if($current !== $pics_name && file_exists($filenameFull)){
            $this->setError(JText::_('COM_DROPPICS_CTRL_FILES_EXISTS'));
            return false;
        }
        @rename(droppicsBase::getFullPicturePath($id_gallery).$current,$filenameFull);
        @rename(droppicsBase::getThumbnailPath($id_gallery).$current,$filenameThumbnail);
        @rename(droppicsBase::getPicturePath($id_gallery).$current,$filenamePicture);
        return true;
    }

    public function updateParamsPicture($params,$id){
        $dbo = $this->getDbo();
        $query = 'UPDATE #__droppics_pictures SET params='.$dbo->quote(json_encode($params)).' WHERE id='.$id ;
        $dbo->setQuery($query);
        if(!$dbo->query()){
            return false;
        }
        return true;
    }
}