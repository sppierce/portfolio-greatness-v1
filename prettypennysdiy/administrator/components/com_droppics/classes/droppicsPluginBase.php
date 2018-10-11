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
jimport('joomla.plugin.plugin');

class droppicsPluginBase extends JPlugin {

    public $name;
    
    public function __construct(&$subject, $config = array()) {
        JLoader::register('DroppicsBase', JPATH_ADMINISTRATOR . '/components/com_droppics/classes/droppicsBase.php');
        DroppicsBase::setDefine();
        //Load language from non default position
        DroppicsBase::loadLanguage();
        parent::__construct($subject, $config);
    }
    
    public function getThemeName(){
        $doc = JFactory::getDocument();
        $doc->addStyleDeclaration('.themesblock a.themebtn.'.$this->name.' {background-image: url('.JURI::root().'plugins/droppics/'.$this->name.'/btn.gif) }');
        return array('name'=>ucfirst($this->name),'id'=>$this->name);
    }
    
    public function onShowAdminGallery($options){
        $this->options = $options;
        if($this->options['theme']!= $this->name){
            return null;
        }

        JLoader::register('DroppicsModelFiles', JPATH_ADMINISTRATOR . '/components/com_droppics/models/files.php');
        $filesModel = JModelLegacy::getInstance('DroppicsModelFiles');

        $root_folder = droppicsBase::getParam('change_root_folder_old', 'com_droppics');
        $content = '';        
        $content .= $this->addAdminScriptDeclaration(@$this->options['params']);
        $content .= '<div id="droppicsgallery'.$this->options['id_gallery'].'" class="droppicsgallery droppicsgallery'.$this->name.'">';
        if(!empty($this->options['pictures'])){
            foreach ($this->options['pictures'] as $picture){
                    $pparams = json_decode($picture->picture_params);
                    $customPictures = htmlentities(json_encode($filesModel->getCustomPictures($picture->id)));
                    $content .= '<div class="wimg">';
                    $content .= '   <div class="selimg">';
                    $content .= '       <img class="img" data-file="'.$picture->file.'" data-id-picture="'
                        .$picture->id.'" data-picture="'.COM_MEDIA_BASEURL.'/'.$root_folder.'/'.$picture->id_gallery.'/'
                        .$picture->filever.'" data-customs="'.$customPictures.'" src="'.COM_MEDIA_BASEURL.'/'.$root_folder.'/'.$picture->id_gallery.'/thumbnails/'.$picture->filever.'"  alt="" />';
                    if(isset($pparams->is_video) && $pparams->is_video=="1" ) {
                        $content .= '   <i class="video"></i>';
                    }
                    $content .= '   </div>';
                    $content .= '</div>';
            }
        }
        $content .= '<input type="hidden" name="theme" value="'.$this->name.'" />';
        $content .= '</div>';
        return $content;
    }
    
    /*
     * Load the form fields for the plugin
     */
    public function getParamForm($theme,&$form){
        if($theme===''){
            $theme = 'default';
        }
        if($theme!='' && $theme!= $this->name){
            return null;
        }
        $formfile = JPATH_PLUGINS.DIRECTORY_SEPARATOR.$this->_type.DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR.'/form.xml';
        if(!file_exists($formfile)){
            return null;
        }
        $form->loadFile($formfile);
        return ;
    }
    
        /*
     * Load the form fields for the plugin image
     */
    public function getImageParamForm($theme,&$form){
        if($theme!= $this->name){
            return null;
        }
        $formfile = JPATH_PLUGINS.DIRECTORY_SEPARATOR.$this->_type.DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR.'/iform.xml';
        if(file_exists($formfile)){
            $form->loadFile($formfile);
        }
        return ;
    }

}

?>
