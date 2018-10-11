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

class droppicsBase {

    /**
     * 
     */
    public static function initComponent(){
        //Load language from non default position
        self::loadLanguage();
        $input = JFactory::getApplication()->input;
        $view = $input->get('view');
        // Register helper class
        JLoader::register('DroppicsHelper', JPATH_ADMINISTRATOR.'/components/com_droppics/helpers/droppics.php');
        // Register helper class
        JLoader::register('DroppicsComponentHelper', JPATH_ADMINISTRATOR.'/components/com_droppics/helpers/component.php');
        //Register wideimage
        JLoader::register('WideImage', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/wideimage/WideImage.php');
        
        //Load scripts and stylesheets
        $document = JFactory::getDocument();
        $document->addScript(JURI::root().'components/com_droppics/assets/js/droppicsHelper.js');
        // root folder js
            JHtml::_('jquery.framework');
            if(JFactory::getApplication()->isSite()){
                $document->addScript(JURI::root().'components/com_droppics/assets/js/modal.min.js');
                $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/modal.min.css');
            }
            $document->addScript(JURI::root().'components/com_droppics/assets/js/jquery-ui-1.9.2.custom.min.js');
            $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/ui-lightness/jquery-ui-1.9.2.custom.min.css');
            $app = JFactory::getApplication();
            if($app->isSite()){
                $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/bootstrap.min.css');
            }
        $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/slider.css');
        $document->addScript(JURI::root().'components/com_droppics/assets/js/bootstrap-slider.js');
        $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/icons.min.css');
        //For touch devices
        $document->addScript(JURI::root().'components/com_droppics/assets/js/jquery.ui.touch-punch.min.js');

        if (isset($view) && $view == 'imagesinfo') {
            $document->addScript(JURI::root(). 'components/com_droppics/assets/js/information.js');
        }

        $app = JFactory::getApplication();
        if($app->isSite()){
            $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/frontstyle.css');
        }
        $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/upload.min.css');
        $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/style.css');
		$document->addScript(JURI::root().'components/com_droppics/assets/js/jquery.imagesloaded.min.js');
        $document->addScript(JURI::root().'components/com_droppics/assets/js/droppics.js');      	
        $document->addScript(JURI::root().'components/com_droppics/assets/js/jquery.filedrop.min.js');
        $document->addScript(JURI::root().'components/com_droppics/assets/js/jquery.textselect.min.js');
        $document->addScript(JURI::root().'components/com_droppics/assets/js/bootbox.min.js');
        $document->addScript(JURI::root().'components/com_droppics/assets/js/jquery.nestable.js');
        $document->addScript(JURI::root().'components/com_droppics/assets/js/jquery.gritter.min.js');        
        $document->addScript(JURI::root().'components/com_droppics/assets/js/jquery.ba-throttle-debounce.min.js');
        $document->addScript(JURI::root().'components/com_droppics/assets/js/caman.full.min.js');
        $document->addScript(JURI::root().'components/com_droppics/assets/js/jquery.Jcrop.min.js');
        $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/jquery.Jcrop.min.css');
        $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/jquery.gritter.css');
                
        $document->addScript(JURI::root().'components/com_droppics/assets/js/jaofiletree.js');
        $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/jaofiletree.css');
        $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/material-design-iconic-font.min.css');
        if (isset($view) && $view == 'imagesinfo') {
            $document->addStyleSheet(JURI::root() . 'components/com_droppics/assets/css/information.css');
        }
        droppicsBase::setDefine();
        self::checkRootFolder();
        $root_folder = self::getParam('change_root_folder_old', 'com_droppics');
        $document->addScriptDeclaration('var drp_root_folder ="'.$root_folder.'";');
        if($app->isAdmin()){
            droppicsBase::updateConfigUpdate();
        }
    }
    
    /**
     * Define values needed by the component and plugin
     */
    public static function setDefine(){
        $path = "file_path";
        $paramsmedia = JComponentHelper::getParams('com_media');
        if(!defined('COM_MEDIA_BASE')){
            define('COM_MEDIA_BASE',	JPATH_ROOT.'/'.$paramsmedia->get($path, 'images'));
        }
        if(!defined('COM_MEDIA_BASEURL')){    
            define('COM_MEDIA_BASEURL', JURI::root().$paramsmedia->get($path, 'images'));
        }
        
    }

    /**
     * Search a param into the component config
     * @param string $path
     * @param type $default
     * @return param 
     */
    public static function getParam($path,$default=null){
        $params = JComponentHelper::getParams('com_droppics');
        return $params->get($path,$default);
    }
    
    /**
     * method to retrieve the path to the component full width image directory
     * @param type $id_gallery 
     * @return string directory path
     */
    public static function getFullPicturePath($id_gallery){
        $root_folder = self::getParam('change_root_folder_old', 'com_droppics');
        if($id_gallery>0){
            return COM_MEDIA_BASE.'/'.$root_folder.'/'.(int)$id_gallery.'/full/';
        } else {
            return '';
        }
    }
    
    /**
     * method to retrieve the path to the component image directory
     * @param type $id_gallery 
     * @return string directory path
     */
    public static function getPicturePath($id_gallery=null){
        $root_folder = self::getParam('change_root_folder_old', 'com_droppics');
        if($id_gallery===null){
            return COM_MEDIA_BASE.'/'.$root_folder.'/';
        }
        return COM_MEDIA_BASE.'/'.$root_folder.'/'.$id_gallery.'/';
    }
    
    /**
     * method to retrieve the path to the component image directory
     * @param type $id_gallery 
     * @return string directory path
     */
    public static function getThumbnailPath($id_gallery){
        $root_folder = self::getParam('change_root_folder_old', 'com_droppics');
        if($id_gallery>0){
        return COM_MEDIA_BASE.'/'.$root_folder.'/'.(int)$id_gallery.'/thumbnails/';
        } else {
            return '';
        }
    }
    
    /**
     * method to retrieve the path to the component image directory
     * @param type $id_gallery 
     * @return string directory path
     */
    public static function getCustomPath($id_gallery){
        $root_folder = self::getParam('change_root_folder_old', 'com_droppics');
        if($id_gallery>0){
        return COM_MEDIA_BASE.'/'.$root_folder.'/'.(int)$id_gallery.'/custom/';
        } else {
            return '';
        }
    }

    /**
     * method to retrieve the url to the component image directory
     * @param type $id_gallery 
     * @return string directory url
     */
    public static function getPictureURL($id_gallery=null){
        $root_folder = self::getParam('change_root_folder_old', 'com_droppics');
        if($id_gallery===null){
            return COM_MEDIA_BASEURL.'/'.$root_folder.'/';
        }
        return COM_MEDIA_BASEURL.'/'.$root_folder.'/'.$id_gallery.'/';
    }
    
    /**
     * method to retrieve the url to the component image directory
     * @param type $id_gallery 
     * @return string directory url
     */
    public static function getThumbnailURL($id_gallery){
        $root_folder = self::getParam('change_root_folder_old', 'com_droppics');
        if($id_gallery>0){
        return COM_MEDIA_BASEURL.'/'.$root_folder.'/'.(int)$id_gallery.'/thumbnails/';
        } else {
            return '';
        }
    }
    
    /**
     * method to retrieve the url to the component image directory
     * @param type $id_gallery 
     * @return string directory url
     */
    public static function getCustomURL($id_gallery){
        $root_folder = self::getParam('change_root_folder_old', 'com_droppics');
        if($id_gallery>0){
        return COM_MEDIA_BASEURL.'/'.$root_folder.'/'.(int)$id_gallery.'/custom/';
        } else {
            return '';
        }
    }
    
    /**
     * Sanitize a file name to get only one extension
     * @param type $filename
     * @return false if failed string otherwise
     */
    public static function makeSafeFilename($filename){
        $replace = array(
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'Ae', 'Å'=>'A', 'Æ'=>'A', 'Ă'=>'A',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'ae', 'å'=>'a', 'ă'=>'a', 'æ'=>'ae',
            'þ'=>'b', 'Þ'=>'B',
            'Ç'=>'C', 'ç'=>'c',
            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 
            'Ğ'=>'G', 'ğ'=>'g',
            'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'ı'=>'i', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
            'Ñ'=>'N',
            'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe', 'Ø'=>'O', 'ö'=>'oe', 'ø'=>'o',
            'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'Š'=>'S', 'š'=>'s', 'Ş'=>'S', 'ș'=>'s', 'Ș'=>'S', 'ş'=>'s', 'ß'=>'ss',
            'ț'=>'t', 'Ț'=>'T',
            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'Ue',
            'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'ue', 
            'Ý'=>'Y',
            'ý'=>'y', 'ÿ'=>'y',
            'Ž'=>'Z', 'ž'=>'z'
        );
        $safe = strtr($filename, $replace);
        
        //get last extension
        $exploded = explode('.', $safe);
        $ext = $exploded[count($exploded)-1];
        
        $name = substr($safe, 0,strlen($safe)-strlen($ext)-1);
        $name = preg_replace('/([^a-zA-Z0-9-]+)/', '_', $name);
        
        if($ext==='' || $name === ''){
            return false;
        }
        
        return $name.'.'.$ext;
    }
    
    /**
     * Method to return the current joomla version
     * @param string $format
     * @return string version
     */
    public static function getJoomlaVersion($format='short'){
        $method = 'get' . ucfirst($format) . "Version";

        // Get the joomla version
        $instance = new JVersion();
        $version = call_user_func(array($instance, $method));

        return $version;        
    }
    
    /**
     * Method to check if current joomla version is 3.X
     * @return boolean 
     */
    public static function isJoomla30(){
        if(version_compare(self::getJoomlaVersion(),'3.0')>=0){
            return true;
        }
        return false;
    }
       
    /**
     * Method to check if current joomla version is 3.X
     * @return boolean 
     */
    public static function isJoomla31(){
        if(version_compare(self::getJoomlaVersion(),'3.1')>=0){
            return true;
        }
        return false;
    }
       
    /**
     * Method to check if current joomla version is 3.2
     * @return boolean 
     */
    public static function isJoomla32(){
        if(version_compare(self::getJoomlaVersion(),'3.2')>=0){
            return true;
        }
        return false;
    }
       
    
    /**
     * Check if a component is installed and activated 
     * @param string $extension
     * @param string $type
     * @return boolean 
     */
    public static function isExtensionActivated($extension,$type=''){
        $db = JFactory::getDbo();
        $query = 'SELECT extension_id FROM #__extensions WHERE element='.$db->quote($extension);

        if($type!=''){
            $query.=' AND type='.$db->quote($type);
        }
        $query.=' AND enabled=1';
        $db->setQuery($query);
        if($db->query()){
            if($db->getNumRows()>0){
                return true;
            }
        }
        return false;
    }
    
    /**
     * Method to set config parameters
     * @param array $datas
     * @return boolean 
     */
    public static function setParams($datas){ 
        return droppicsComponentHelper::setParams($datas);
    }
    
    /**
     * Check if config file has been updated and user need to be informed
     * @return boolean 
     */
    public static function checkConfigUpdate(){
        $watermarks_images = self::getParam('watermarks_images',0);
        if(self::getParam('updated',false)){
            if ($watermarks_images){
                if (
                    self::getParam('thumbnail_width', 0) != self::getParam('thumbnail_width_old', 0) ||
                    self::getParam('thumbnail_height', 0) != self::getParam('thumbnail_height_old', 0) ||
                    self::getParam('picture_width', 0) != self::getParam('picture_width_old', 0) ||
                    self::getParam('picture_height', 0) != self::getParam('picture_height_old', 0) ||
                    self::getParam('full_width', 0) != self::getParam('full_width_old', 0) ||
                    self::getParam('full_height', 0) != self::getParam('full_height_old', 0) ||
                    self::getParam('jpg_quality', 0) != self::getParam('jpg_quality_old', 0) ||
                    self::getParam('png_quality', 0) != self::getParam('png_quality_old', 0) ||
                    self::getParam('watermarks_category_images',0)!=  self::getParam('watermarks_category_images_old', 0)||
                    self::getParam('watermark_position',0) != self::getParam('watermark_position_old',0)||
                    implode(",",self::getParam('apply_watermark_on',array(0))) != self::getParam('apply_watermark_on_old',0)||
                    self::getParam('watermarks_images',0) != self::getParam('watermarks_images_old',0)
                ) {
                    return true;
                }
            }else {
                if (
                    self::getParam('thumbnail_width', 0) != self::getParam('thumbnail_width_old', 0) ||
                    self::getParam('thumbnail_height', 0) != self::getParam('thumbnail_height_old', 0) ||
                    self::getParam('picture_width', 0) != self::getParam('picture_width_old', 0) ||
                    self::getParam('picture_height', 0) != self::getParam('picture_height_old', 0) ||
                    self::getParam('full_width', 0) != self::getParam('full_width_old', 0) ||
                    self::getParam('full_height', 0) != self::getParam('full_height_old', 0) ||
                    self::getParam('jpg_quality', 0) != self::getParam('jpg_quality_old', 0) ||
                    self::getParam('png_quality', 0) != self::getParam('png_quality_old', 0) ||
                    self::getParam('watermarks_images',0) != self::getParam('watermarks_images_old',0)
                ) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public static function updateConfigUpdate(){
        $watermarks_images = self::getParam('watermarks_images',0);
        if(self::getParam('updated',false)){
            if ($watermarks_images){
                if( self::getParam('thumbnail_width_old')==self::getParam('thumbnail_width')
                    && self::getParam('thumbnail_height_old')==self::getParam('thumbnail_height')
                    && self::getParam('picture_width_old')==self::getParam('picture_width')
                    && self::getParam('picture_height_old')==self::getParam('picture_height')
                    && self::getParam('full_width_old')==self::getParam('full_width')
                    && self::getParam('full_height_old')==self::getParam('full_height')
                    && self::getParam('jpg_quality_old')==self::getParam('jpg_quality')
                    && self::getParam('png_quality_old')==self::getParam('png_quality')
                    && self::getParam('watermarks_category_images_old') == self::getParam('watermarks_category_images')
                    && self::getParam('watermark_position_old') == self::getParam('watermark_position')
                    && self::getParam('apply_watermark_on_old') == implode(",",self::getParam('apply_watermark_on',array(0)))
                    && self::getParam('watermarks_images_old')==self::getParam('watermarks_images')
                ){
                self::setParams(array('updated'=>''));
                }
            }else{
                if( self::getParam('thumbnail_width_old')==self::getParam('thumbnail_width')
                    && self::getParam('thumbnail_height_old')==self::getParam('thumbnail_height')
                    && self::getParam('picture_width_old')==self::getParam('picture_width')
                    && self::getParam('picture_height_old')==self::getParam('picture_height')
                    && self::getParam('full_width_old')==self::getParam('full_width')
                    && self::getParam('full_height_old')==self::getParam('full_height')
                    && self::getParam('jpg_quality_old')==self::getParam('jpg_quality')
                    && self::getParam('png_quality_old')==self::getParam('png_quality')
                    && self::getParam('watermarks_images_old')==self::getParam('watermarks_images')
                ){
                    self::setParams(array('updated'=>''));
                }
            }
        }
    }
    
    /**
     * Load global file language
     */
    public static function loadLanguage(){
        $lang = JFactory::getLanguage();
        $lang->load('com_droppics',JPATH_ADMINISTRATOR.'/components/com_droppics',null,true);
        $lang->load('com_droppics.override',JPATH_ADMINISTRATOR.'/components/com_droppics',null,true);
        $lang->load('com_droppics.sys',JPATH_ADMINISTRATOR.'/components/com_droppics',null,true);
    }
    
    public static function loadValue($var,$value,$default=''){
        if(is_object($var) && isset($var->$value)){
            return $var->$value;
        }elseif(is_array($var) && isset($var[$value])){
            return $var[$value];
        }
        return $default;
    }
    
    /**
    * Joomla 3.2 method used for Joomla 2.5 too
    * Method to get an attribute of the field
    *
    * @param   string  $name     Name of the attribute to get
    * @param   mixed   $default  Optional value to return if attribute not found
    *
    * @return  mixed             Value of the attribute / default
    *
    * @since   3.2
    */
   public static function getAttribute($xmlelement,$name, $default = null)
   {
       if(self::isJoomla32()){
           return $xmlelement->getAttribute($name, $default);
       }else{
           if(property_exists($xmlelement, $name)){
                return strtolower($xmlelement->$name);
           }
           return $default;
       }
   }
   
   /**
    * Check on Joomunited website the latest version number of the component
    * @param string $extension
    * @return false or version number (string)
    */
   public static function getLastExtensionVersion($extension=null){
        if($extension===null){
            $extension = JFactory::getApplication()->input->getString('option', '');
        }
        if (ini_get("allow_url_fopen") == 1) {
            $content = file_get_contents('http://www.joomunited.com/UPDATE-INFO/updates.json');
        }else{
            return false;
        } 
        $json = json_decode($content);
        return $json->extensions->$extension->version;       
   }
   
   public static function getExtensionVersion($extension=null,$type=''){
        if($extension===null){
             $extension = JFactory::getApplication()->input->getString('option', '');
        }
        $db = JFactory::getDbo();
        $query = 'SELECT manifest_cache FROM #__extensions WHERE element='.$db->quote($extension);

        if($type!=''){
            $query.=' AND type='.$db->quote($type);
        }
        $db->setQuery($query);
        if($db->query()){
            $manifest = $db->loadResult();
            $json = json_decode($manifest);
            if(property_exists($json, 'version')){
                return $json->version;
            }
        }
        return false;
   }

   //check root folder
    public static function checkRootFolder()
    {
        $change_root_folder_new = self::getParam('change_root_folder', 'com_droppics');
        $change_root_folder_old = self::getParam('change_root_folder_old', 'com_droppics');
        $path_change_root_folder = COM_MEDIA_BASE . '/'.$change_root_folder_new.'/';
        $path_change_root_folder_old = COM_MEDIA_BASE . '/'.$change_root_folder_old.'/';
        if ($change_root_folder_old != $change_root_folder_new) {
            if (file_exists($path_change_root_folder_old)){
                rename($path_change_root_folder_old,$path_change_root_folder);
                self::setParams(array('change_root_folder_old'=>$change_root_folder_new));
            }
        }
    }

    public static function getParamsWithTheme($theme = 'default') {
        $ob = new stdClass();
        $global_params = JComponentHelper::getParams('com_droppics');
        if ($theme == '') $theme = 'default';
        foreach ($global_params->toArray() as $key => $val) {
            if(strpos($key,$theme."_") !== false) {
                $ob->$key = $global_params->get($key);
            }
        }
        if ($theme == 'default'){
            $ob = self::getParamsThemeDefault($ob);
        }
        return $ob;
    }
    public static function getParamsThemeDefault($obj) {
        $ob = new stdClass();
        $keys = array_keys((array)$obj);
        $values = array_values((array)$obj);
        $new_keys = str_replace('default_', '', $keys);
        $data_params = array_combine($new_keys, $values);
        foreach ($data_params as $key => $val) {
                $ob->$key = $data_params[$key];
        }
        return $ob;
    }

}

?>
