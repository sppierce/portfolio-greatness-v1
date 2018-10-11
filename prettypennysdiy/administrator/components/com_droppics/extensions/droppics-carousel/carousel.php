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
class plgDroppicsCarousel extends droppicsPluginBase
{
    
    public $name = 'carousel';
    protected $options;
    public function root_folder(){ return  droppicsBase::getParam('change_root_folder_old', 'com_droppics');}

    public function onShowFrontGallery($options){
        $this->options = $options;
        if($this->options['theme']!= $this->name){
            return null;
        }
        
        $doc = JFactory::getDocument();
        $params = JComponentHelper::getParams('com_droppics');
        
        $scripts = array();
        $stylesheets = array();
        
        $stylesheets[] = JURI::base('true').'/plugins/droppics/carousel/nivo-slider.css';
        $stylesheets[] = JURI::base('true').'/plugins/droppics/carousel/style.css';
        $stylesheets[] = JURI::base('true').'/plugins/droppics/carousel/themes/default/default.css';
        $scripts[] = JURI::base('true').'/plugins/droppics/carousel/jquery.nivo.slider.pack.js';
        $scripts[] = JURI::base('true').'/plugins/droppics/carousel/script.js';
        
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
        
        
        $content = "";
        $videoHtml = "";
        $listchid = '';
        if(!empty($this->options['pictures']) || !empty($this->options['categories'])){
            if ($this->options['listchid']){
                $listchid = implode(',',$this->options['listchid']);
            }
            $content .= '<div id="droppicsgallery'.$this->options['id_gallery'].'" class="droppicsgallery droppicsgallerycarousel" data-shownav="'.droppicsBase::loadValue($options['params'],'carousel_show_nav','1').'" data-controlnav="'.droppicsBase::loadValue($options['params'],'carousel_show_bottom_nav','1').'" data-listchid="'.$listchid.'">';
            if(!empty($this->options['pictures'])){
                $content .= '<div class="slider-wrapper theme-default">';
                $content .= '<div class="nivoSlider">';
                foreach ($this->options['pictures'] as $picture){
                    $pparams = json_decode($picture->picture_params);
                    $custom_link = "";
                    if(is_object($pparams) && isset($pparams->follow_custom_link) && $pparams->follow_custom_link=="1" && $pparams->imgp_click != 'lightbox'  && $pparams->imgp_click != 'nothing' ) {
                        if($pparams->imgp_click=='article') {
                            $custom_link = $pparams->acticle_link;
                        }elseif($pparams->imgp_click=='menuitem') {
                            $custom_link = $pparams->click_content_menuitem;
                        }elseif($pparams->imgp_click=='custom') {
                            $custom_link = $pparams->custom_link;
                        }
                    }
                                                  
                    if(isset($pparams->is_video) && $pparams->is_video=="1" ) {
                        $content .= '<div class="wimg isVideo">';
                        $vwidth = (int)$params->get('video_width',640);                    
                        $height = (isset($pparams->ratio) && $pparams->ratio ) ? $vwidth/$pparams->ratio : 390 ;
                        $autoPlayLink = (strpos($pparams->directLink,"?") !== false)? $pparams->directLink."&autoplay=1":$pparams->directLink."?autoplay=1";
                        $videoHtml .= '<div id="vframe'.$picture->id.'" class="vframe" style="display:none">'.$autoPlayLink.'</div>';
                        $content .= '<a href="" class="playBtn" data-video="vframe'.$picture->id.'" >';
                        $content .=    '<img src="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$picture->id_gallery.'/'.$picture->filever.'" alt="'.$picture->alt.'" title="" data-thumb="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$picture->id_gallery.'/thumbnails/'.$picture->filever.'"/>';
                        $content .= '</a>';
                    }else if($custom_link) {
                        $content .= '<div class="wimg">';
                        if(isset($pparams->imgp_click_target) && $pparams->imgp_click_target=="_blank") {
                            $target = ' target="_blank"';
                        }else {
                            $target = "";
                        }
                        $content .=  '<a href="'.$custom_link.'" ' .$target. '>'. '<img src="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$picture->id_gallery.'/'.$picture->filever.'" alt="'.$picture->alt.'" title="'.$picture->title.'" data-thumb="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$picture->id_gallery.'/thumbnails/'.$picture->filever.'"/></a>';
                    }else {
                        $content .= '<div class="wimg">';
                        $content .=    '<img src="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$picture->id_gallery.'/'.$picture->filever.'" alt="'.$picture->alt.'" title="'.$picture->title.'" data-thumb="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$picture->id_gallery.'/thumbnails/'.$picture->filever.'"/>';
                    }
                    $content .= '</div>';            
                    
                }
                $content .= '</div>';
                $content .= '</div>';
            }
            if((!empty($this->options['categories']) || !empty($this->options['parent'])) && droppicsBase::loadValue($options['params'],'carousel_show_subcategories','1')){
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
                    $content .= '<div class="clr"></div>';
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
                $alt = $alt = pathinfo($src, PATHINFO_FILENAME);
                $title = '';
                if(isset($this->options['parent']) && !empty($this->options['parent']->id_picture) ) {
                    $content .= '<div class="droppicscats">';
                    $content .= '<div class="wcat wimg wparent">';
                    $content .= '<a class="droppicscatslink" data-id="' . $this->options['parent']->id_category . '" href="" data-categorytitle="' . $this->options['parent']->category_title . '" data-catimage ="1" >';
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
        if($videoHtml) {
            $content .= '<div id="vframeContainer">'. $videoHtml .'</div>';
        }
        return $content;
    }    
    
    protected function addStyleDeclaration($options,$dynamic = false){
            $params = $options['params'];
            
            $style = '';
            if(droppicsBase::loadValue($params,'carousel_align_center',0)==0){
                $style .= '#droppicsgallery'.$this->options['id_gallery'].' {';
                if(droppicsBase::loadValue($params,'carousel_width',0)>0) $style .= '     width : '.droppicsBase::loadValue($params,'carousel_width').'px;';
                $style .= '}';
            }else{
                $style .= '#droppicsgallery '.$this->options['id_gallery'].' .slider-wrapper {';
                if(droppicsBase::loadValue($params,'carousel_width',0)>0) $style .= '     width : '.droppicsBase::loadValue($params,'carousel_width').'px;';
                $style .= '}';
            }
            $style .= '#droppicsgallery'.$this->options['id_gallery'].' .nivoSlider {';
            if(droppicsBase::loadValue($params,'carousel_align_center',0)==1) $style .= 'margin: 0 auto;';
            if(droppicsBase::loadValue($params,'carousel_height',0)>0) $style .= '     height : '.droppicsBase::loadValue($params,'carousel_height').'px;';
            if(droppicsBase::loadValue($params,'carousel_width',0)>0) $style .= '     width : '.droppicsBase::loadValue($params,'carousel_width').'px;';
            if(droppicsBase::loadValue($params,'carousel_background','')!=='') $style .= '     background-color : '.droppicsBase::loadValue($params,'carousel_background').';';
            if(droppicsBase::loadValue($params,'carousel_show_shadow','1')!='1') {
                $style .= '     webkit-box-shadow: none;';
                $style .= '           -moz-box-shadow: none;';
                $style .= '            box-shadow: none;';
            }
            $style .= '}';
            
            $style .= '#droppicsgallery'.$this->options['id_gallery'].'.droppicsgallerycarousel .droppicscats .wimg {';
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
