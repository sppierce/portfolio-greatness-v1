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


//-- No direct access
defined('_JEXEC') || die('=;)');


jimport('joomla.plugin.plugin');

/**
 * Content Plugin.
 *
 * @package    droppics
 * @subpackage Plugin
 */
class plgK2droppics extends JPlugin
{
  
    /**
     * Example before display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param  string  $context     The context for the content passed to the plugin.
     * @param  object  &$article    The content object.  Note $article->text is also available
     * @param  object  &$params     The content params
     * @param  int     $limitstart  The 'page' number
     *
     * @return string
     */
    public function onK2PrepareContent(&$item,&$params,$limitstart)
    {
//        $cont = explode('.', $context);
//        if($cont[0]=='com_content'){
            //Replace gallery
            $item->text = preg_replace_callback('@<img.*data\-droppicsgallery="([0-9]+)".*?>@', array($this,'replace'),$item->text);
            //Replace single image
            $item->text = preg_replace_callback('@<img.*data\-droppicspicture="([0-9]+)".*?>@', array($this,'replaceSingle'),$item->text);
//        }
        return true;
    }

    public function root_folder(){ return  droppicsBase::getParam('change_root_folder_old', 'com_droppics');}

    private function replace($match){
        jimport('joomla.application.component.model');
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_droppics/models/','droppicsModel');
        
        $model = JModelLegacy::getInstance('Gallery','droppicsModel');
        
        JPluginHelper::importPlugin('droppics');
        $dispatcher = JDispatcher::getInstance();

        $pictures = $model->getGallery((int)$match[1]);

        if(!empty($pictures)){
            $gallery = $dispatcher->trigger('onShowFrontGallery',array(array('id_gallery'=>$pictures[0]->id_gallery,'theme'=>$pictures[0]->theme,'pictures'=>$pictures)));
            return $gallery[0];
        }
        return '';
    }
    
    /**
     * Replace a single image
     * @param type $match
     * @return string 
     */
    private function replaceSingle($match){
        JLoader::register('DroppicsBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/droppicsBase.php');
        droppicsBase::setDefine();
        jimport('joomla.application.component.model');
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_droppics/models/','droppicsModel');
        
        $model = JModelLegacy::getInstance('Files','droppicsModel');
        $picture = $model->getPicture((int)$match[1]);

        $doc = JFactory::getDocument();
        if(droppicsBase::isJoomla30()){
	    JHtml::_('jquery.framework');
	}else{
	    $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/jquery-1.8.3.js');
	    $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/jquery-noconflict.js');
	}
        $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/jquery.colorbox-min.js');
        $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/colorbox.init.js');
        $doc->addStyleSheet(JURI::base('true').'/components/com_droppics/assets/css/colorbox.css');

        if(!empty($picture)){
            $img = str_replace('/>', 'alt="'.$picture->alt.'" />', $match[0]);
            if(droppicsBase::getParam('showlightboxtitle',1)){
                $datatitle = 'data-title="'.$picture->title.'"';
            }else{
                $datatitle = '';
            }
           return '<a href="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$picture->id_gallery.'/'.$picture->filever.'" rel="lightbox" class="droppicssingleimage" '.$datatitle.'>'.$img.'</a>';
        }
        return '';
    }

}
