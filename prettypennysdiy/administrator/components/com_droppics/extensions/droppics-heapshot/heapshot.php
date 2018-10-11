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


JLoader::register('droppicsPluginBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/droppicsPluginBase.php');

/**
 * Content Plugin.
 *
 * @package    droppics
 * @subpackage Plugin
 */
class plgDroppicsHeapshot extends droppicsPluginBase
{
    
    public $name = 'heapshot';
    protected $options;
    public function root_folder(){ return  droppicsBase::getParam('change_root_folder_old', 'com_droppics');}
        
    public function onShowFrontGallery($options){
        $this->options = $options;
        if($this->options['theme']!='' && $this->options['theme']!= $this->name){
            return null;
        }
        $doc = JFactory::getDocument();
        //Dont need imagesloaded jquery plugin cause heapshot include it
        $params = JComponentHelper::getParams('com_droppics');
        
        $scripts = array();
        $stylesheets = array();
        
        $scripts[] = JURI::base('true').'/components/com_droppics/assets/js/jquery.imagesloaded.min.js';        
        $stylesheets[] = JURI::base('true').'/plugins/droppics/heapshot/style.css';
        $scripts[] = JURI::base('true').'/plugins/droppics/heapshot/jQueryRotate.min.js';
        $scripts[] = JURI::base('true').'/plugins/droppics/heapshot/script.js';
        $scripts[] = JURI::base('true').'/plugins/droppics/heapshot/heapshot.js';
        
        $headLoading = '';
        if($this->options['from_plugin']===true){
            $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/droppicsHelper.js');
            JLoader::register('DroppicsBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/droppicsBase.php');
            JHtml::_('jquery.framework');
            foreach ($scripts as $script) {
                $doc->addScript($script);
            }
            foreach ($stylesheets as $stylesheet) {
                $doc->addStyleSheet($stylesheet);
            }
        }else{
            $files = array();
            foreach ($scripts as $script) {
                $files[] = '["'.$script.'","js"]';
            }
            foreach ($stylesheets as $stylesheet) {
                $files[] = '["'.$stylesheet.'","css"]';
            }
            $files = implode(',', $files);
            $headLoading .= "loadHeadFiles('[".$files."]');";
        }
        
        $content = '';
        $listchid = '';
        if(!empty($this->options['pictures']) || !empty($this->options['categories'])){
            if ($this->options['listchid']){
                $listchid = implode(',',$this->options['listchid']);
            }
            $content .= '<div id="droppicsgallery'.$this->options['id_gallery'].'" class="droppicsgallery droppicsgalleryheapshot" data-listchid="'.$listchid.'">';
            $content .= '<ul data-overflowparents="'.droppicsBase::loadValue($options['params'],'heapshot_fix_overflow','1').'">';
            foreach ($this->options['pictures'] as $picture){
                $content .= '<li>';
                $content .=    '<img class="img" src="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$picture->id_gallery.'/'.$picture->filever.'" alt="'.$picture->alt.'" title="'.$picture->title.'" />';
                $content .= '</li>';            
            }
            $content .= '</ul>';
            
            if((!empty($this->options['categories']) || !empty($this->options['parent'])) && droppicsBase::loadValue($options['params'],'heapshot_show_subcategories','1')){
                $content .= '<div class="droppicscats">';
                $content .= '<h2>'.JText::_('COM_DROPPICS_SUBCGALLERIES').'</h2>';
                $catimage = 0;
                if(!empty($this->options['parent'])){    
                    if($this->options['parent']->id_picture === null){
                        $params = JComponentHelper::getParams('com_droppics');
                        $src = JURI::base().$params->get('catimage','components/com_droppics/assets/images/back-gallery.png');
                        $catimage = 1;
                        $alt = $this->options['parent']->category_title;
                    }else{
                        $src = COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$this->options['parent']->id_category.'/thumbnails/'.$this->options['parent']->picture_ver;
                        $catimage = 0;
                        $title = $this->options['parent']->picture_title;
                        $alt = $this->options['parent']->picture_alt;
                    }
                    if (!isset($src) || !getimagesize($src)){
                        $src = JURI::base().'components/com_droppics/assets/images/back-gallery.png';
                        $catimage = 1;
                    }
                    if(!isset($title)){
                        $title = pathinfo($src, PATHINFO_FILENAME);
                    }
                    if(!isset($alt)){
                        $alt = pathinfo($src, PATHINFO_FILENAME);
                    }
                    $content .= '<div class="wcat wimg wparent">';
                    $content .=    '<a class="droppicscatslink" data-id="'.$this->options['parent']->id_category.'" href="" data-categorytitle="'.$this->options['parent']->category_title.'" data-catimage ="'.$catimage.'" >';
                    $content .=         '<img class="img" src="'.$src.'" alt="'.$alt.'" title="'.$title.'" />';
                    $content .=     '</a>';
                    $content .=         '<span>'.JText::_('COM_DROPPICS_BACK_TO').' '.$this->options['parent']->category_title.'</span>';
                    $content .= '</div>';
                }
                foreach ($this->options['categories'] as $category){
                    $content .= '<div class="wcat wimg">';
                    $content .=    '<a class="droppicscatslink" data-id="'.$category->id_category.'" href="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$category->id_category.'/'.$category->picture_ver.'" data-categorytitle="'.$category->category_title.'">';
                    $content .=         '<img class="img" src="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$category->id_category.'/thumbnails/'.$category->picture_ver.'" alt="'.$category->picture_alt.'" title="'.$category->picture_title.'" />';
                    $content .=     '</a>';
                    $content .=         '<span>'.$category->category_title.'</span>';
                    $content .= '</div>';
                }
                $content .= '</div>';
                $content .= '<div class="clr"></div>';
            }else{
                $params = JComponentHelper::getParams('com_droppics');
                $catimage = $params->get('catimage','components/com_droppics/assets/images/back-gallery.png');
                $src = JURI::base().$params->get('catimage','components/com_droppics/assets/images/back-gallery.png');
                if (!$catimage){
                    $src = JURI::base().'components/com_droppics/assets/images/back-gallery.png';
                }
                if (isset( $this->options['parent']->category_title)){
                    $alt = $this->options['parent']->category_title;
                }
                else{
                    $alt = pathinfo($src, PATHINFO_FILENAME);
                }
                if(isset($this->options['parent']) && !empty($this->options['parent']->id_picture)) {
                    $title = '';
                    $content .= '<div class="droppicscats">';
                    $content .= '<div class="wcat wimg wparent">';
                    $content .= '<a class="droppicscatslink" data-id="' . $this->options['parent']->id_category . '" href="" data-categorytitle="' . $this->options['parent']->category_title.'" data-catimage ="1" >';
                    $content .= '<img class="img" src="' . $src . '" alt="' . $alt . '" title="' . $title . '" />';
                    $content .= '</a>';
                    $content .= '<span>' . JText::_('COM_DROPPICS_BACK_TO') . ' ' . $this->options['parent']->category_title . '</span>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $content .= '<div class="clr"></div>';
                }

            }
            //check hash url category
            if($this->options['from_plugin']===true) {
                $content .= '<script type="text/javascript">var initGallery = true; var '.$this->name.'_category_id= ' .
                    $this->options['id_gallery'] . '; var '.$this->name.'_hash = window.location.hash; '.$this->name.'_hash = '.
                    $this->name.'_hash.replace(\'#\',\'\'); var '.$this->name.'_cat_id_click= \'\';
                if ('.$this->name.'_hash != \'\') {
                    var hasha = '.$this->name.'_hash.split(\'-\');
                    var re = new RegExp("^([0-9]+)$");
                    var hash_category_id = hasha[0];        
                    if (!re.test(hash_category_id)) { hash_category_id = 0;}
                    if(hash_category_id) {initGallery = false; }
                }
                  </script>';
            }else{
                $content .= '<script type="text/javascript"> var '.$this->name.'_cat_id_click= ' .$this->options['id_gallery'] . ';</script>';
            }

            if(empty($this->options['categories']) && empty($this->options['parent']) && $this->options['from_plugin']===true){
            //Include style in the head with joomla
                $this->addStyleDeclaration(@$this->options,false);
            }else{
                //Include style in the head dynamically
                $headLoading .= $this->addStyleDeclaration(@$this->options,true);
            }

            if($headLoading!==''){
                $headLoading = '<script type="text/javascript">'.$headLoading.'</script>';
            }
            $content .= $headLoading ;
            
            $content .= '</div>';
        }
        return $content;
    }    
    
    protected function addStyleDeclaration($options,$dynamic = false){
            $params = $options['params'];
            $style  = '';
            $style .= '#droppicsgallery'.$this->options['id_gallery'].'.droppicsgalleryheapshot ul {';
            $style .= '    margin-left : '.droppicsBase::loadValue($params,'heapshot_margin_left',10).'px;';
            $style .= '    margin-top : '.droppicsBase::loadValue($params,'heapshot_margin_top',10).'px;';
            $style .= '    margin-right : '.droppicsBase::loadValue($params,'heapshot_margin_right',10).'px;';
            $style .= '    margin-bottom : '.droppicsBase::loadValue($params,'heapshot_margin_bottom',10).'px;';
            $style .= '}';
            $style .= '#droppicsgallery'.$this->options['id_gallery'].'.droppicsgalleryheapshot ul .img {';
            $style .= '    width : '.droppicsBase::loadValue($params,'heapshot_image_width',600).'px;';
            $style .= '}';
            
            $style .= '#droppicsgallery'.$this->options['id_gallery'].'.droppicsgalleryheapshot .droppicscats .wimg {';
            $height  = droppicsBase::getParam('thumbnail_height','0');
            $style .= 'height : '.$height.'px;';
            $width   = droppicsBase::getParam('thumbnail_width','0');
            $style .= 'width : '.$width.'px;';
            $style .= '}';
            
            if($dynamic===false){
                $doc = JFactory::getDocument();
                $doc->addStyleDeclaration($style);
            }else{
                return 'loadHeadStyle("'.$style.'","droppicsgalleryStyle'.$options['id_gallery'].'")';
            }
            return '';
    }
    
    protected function addAdminScriptDeclaration(){
            $script  = '';
            $script .= '#preview .highlight {';
            $script .= 'height:'.(droppicsBase::getParam('thumbnail_height','0')-62).'px;';
            $script .= 'width:'.(droppicsBase::getParam('thumbnail_width','0')-15).'px;';
            $script .= '}';

            return '<style type="text/css">'.$script.'</style>';
            
    }
    
}
