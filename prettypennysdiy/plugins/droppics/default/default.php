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
class plgDroppicsDefault extends droppicsPluginBase
{
    
    public $name = 'default';
    protected $options;
    public function root_folder(){ return  droppicsBase::getParam('change_root_folder_old', 'com_droppics');}

    public function getThemeName(){
        $doc = JFactory::getDocument();
        $doc->addStyleDeclaration('.themesblock a.themebtn.'.$this->name.' {background-image: url('.JURI::root().'plugins/droppics/'.$this->name.'/btn.gif) }');
        return array('name'=>ucfirst($this->name),'id'=>$this->name);
    }
    
    public function onShowAdminGallery($options){
        $this->options = $options;
        if($this->options['theme']==''){
            $this->options['theme']='default';
        }
        return parent::onShowAdminGallery($this->options);
    }    
    
    public function onShowFrontGallery($options){
        $this->options = $options;
        if($this->options['theme']!='' && $this->options['theme']!= $this->name){
            return null;
        }
        $doc = JFactory::getDocument();
        $params = JComponentHelper::getParams('com_droppics');
        
        $scripts = array();
        $stylesheets = array();
        
        $scripts[] = JURI::base('true').'/components/com_droppics/assets/js/jquery.imagesloaded.min.js';        
        $stylesheets[] = JURI::base('true').'/plugins/droppics/default/style.css';
        $scripts[] = JURI::base('true').'/components/com_droppics/assets/js/jquery.colorbox-min.js';
        $scripts[] = JURI::base('true').'/components/com_droppics/assets/js/colorbox.init.js';
        $scripts[] = JURI::base('true').'/components/com_droppics/assets/js/spin.min.js';
        $scripts[] = JURI::base('true').'/components/com_droppics/assets/js/jquery.esn.autobrowse.js';        
        $scripts[] = JURI::base('true').'/plugins/droppics/default/script.js';
        $stylesheets[] = JURI::base('true').'/components/com_droppics/assets/css/colorbox.css';
        
        $useInfinite = droppicsBase::getParam('infinitescroll',1);
        $infiniteFirstLoad = droppicsBase::getParam('infinitescroll_first_load',10);
        $infiniteAjaxLoad = droppicsBase::getParam('infinitescroll_ajax_load',10);

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

        $listchid = '';
        if(!empty($this->options['pictures']) || !empty($this->options['categories'])){
            if ($this->options['listchid']){
                $listchid = implode(',',$this->options['listchid']);
            }
            $showtitle = droppicsBase::getParam('showlightboxtitle',1);
            $content = '';
            $content .= '<div id="droppicsgallery'.$this->options['id_gallery'].'" data-id="'.$this->options['id_gallery'].'" class="droppicsgallery droppicsgallerydefault droppicslightbox" data-useinfinite="'.$useInfinite.'" data-infiniteajax="'.(int)$infiniteAjaxLoad.'" data-infinitefirst="'.(int)$infiniteFirstLoad.'" data-listchid="'.$listchid.'">';
            $content .= '<div class="droppicspictures">';
            $ij = 0;
            $script = array();
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
                
                if($showtitle){
                    $datatitle = 'data-title="'.$picture->title.'"';
                }else{
                    $datatitle = '';
                }
               
                $cls = "";
                $img = '<img class="img" src="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$picture->id_gallery.'/thumbnails/'
                    .$picture->filever.'" alt="'.$picture->alt.'" title="'.$picture->title.'" />';
                if(is_object($pparams) && isset($pparams->show_caption) && $pparams->show_caption) {
                    $caption = (empty($pparams->content_custom_title) )? $picture->title : $pparams->content_custom_title ;                                        
                    $img .= "<span>". $caption. "</span>";
                    $cls .= "showcaption";                    
                }
                                
                if(isset($pparams->is_video) && $pparams->is_video=="1" ) {
                    $vwidth = (int)$params->get('video_width',640);                    
                    $height = (isset($pparams->ratio) && $pparams->ratio ) ? $vwidth/$pparams->ratio : 390 ;     
                    $cls .= ' '.$pparams->video_type;
                    $contentp = '<div class="wimg droppicsvideo">';
                    $contentp .=    '<a data-vwidth="'.$vwidth.'" data-vheight="'.$height.'" class="'.$cls.'" href="'.$pparams->directLink .'" rel="lightbox['.$picture->id_gallery.']" '.$datatitle.'>'.$img.'<i class="video"></i></a>';
                    $contentp .= '</div>';
                }
                elseif($custom_link) {
                    if(isset($pparams->imgp_click_target) && $pparams->imgp_click_target=="_blank") {
                        $datatitle .= ' target="_blank"';
                    }
                    $contentp = '<div class="wimg">';
                    $contentp .=    '<a class="'.$cls.'" href="'.$custom_link.'"  '.$datatitle.'>'.$img.'</a>';
                    $contentp .= '</div>';
                }else {
                    $contentp = '<div class="wimg droppicslightbox">';
                    $contentp .=    '<a class="'.$cls.'" href="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$picture->id_gallery.'/'.$picture->filever.'" rel="lightbox['.$picture->id_gallery.']" '.$datatitle.'>'.$img.'</a>';
                    $contentp .= '</div>';
                }
                
                if($useInfinite && $ij > ($infiniteFirstLoad-1)){
                    $script[] = $contentp;
                }elseif($useInfinite && $ij==($infiniteFirstLoad-1)){
                    $content .= $contentp;
                    $content .= '<div class="clr"></div>';
                }else{
                    $content .= $contentp;
                }
                $ij++;
            }
            $content .= '</div>';
            $content .= '<div class="clr"></div>';
            if((!empty($this->options['categories']) || !empty($this->options['parent'])) && droppicsBase::loadValue($options['params'],'show_subcategories','1')){
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
                $alt = $alt = pathinfo($src, PATHINFO_FILENAME);
                $title = '';
                if(isset($this->options['parent']) && !empty($this->options['parent']->id_picture)) {
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

            if(isset($script) && !empty($script)){
                $content .= '<script type="text/javascript">if(typeof(droppicsAutobrowse)==="undefined"){var droppicsAutobrowse = [];}droppicsAutobrowse['.$this->options['id_gallery'].']='.json_encode($script).'</script>';
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

    protected function addAdminScriptDeclaration($params){
        $script  = '';
        $script .= '#preview .highlight {';
        $script .= 'height:'.(droppicsBase::getParam('thumbnail_height','0')-62*droppicsBase::loadValue($params, 'border_size','1')).'px;';
        $script .= 'width:'.(droppicsBase::getParam('thumbnail_width','0')-15*droppicsBase::loadValue($params, 'border_size','1')).'px;';
        $script .= '}';

        $script .= '#preview .img {';
        $script .= 'border-width:'.droppicsBase::loadValue($params,'border_size','1').'px;';
        $script .= 'border-radius:'.droppicsBase::loadValue($params,'border_radius','3').'px;';
        $script .= 'border-color:'.droppicsBase::loadValue($params,'border_color','#EEE').';';
        $script .= 'border-style: solid;';
        //todo insert shadow size
        if(droppicsBase::loadValue($params,'shadow_size','3')!='0'){
            $script .= 'box-shadow: '.(droppicsBase::loadValue($params,'shadow_size','1')-1).'px '.droppicsBase::loadValue($params,'shadow_size','1').'px '.droppicsBase::loadValue($params,'shadow_size','1').'px 1px '.droppicsBase::loadValue($params,'shadow_color','#EEE').';';
            $script .= '-moz-box-shadow: '.(droppicsBase::loadValue($params,'shadow_size','3')-1).'px '.droppicsBase::loadValue($params,'shadow_size','3').'px '.droppicsBase::loadValue($params,'shadow_size','3').'px 1px '.droppicsBase::loadValue($params,'shadow_color','#EEE').';';
            $script .= '-webkit-box-shadow: '.(droppicsBase::loadValue($params,'shadow_size','3')-1).'px '.droppicsBase::loadValue($params,'shadow_size','3').'px '.droppicsBase::loadValue($params,'shadow_size','3').'px 1px '.droppicsBase::loadValue($params,'shadow_color','#EEE').';';
        }
        $script .= '}';
                
        return '<style type="text/css">'.$script.'</style>';
    }
    
    protected function addStyleDeclaration($options,$dynamic = false){
            $params = $options['params'];
            $style  = '';
            
            $style .= '#droppicsgallery'.$this->options['id_gallery'].'.droppicsgallerydefault .wimg {';
            $height  = droppicsBase::getParam('thumbnail_height','0')+droppicsBase::loadValue($params,'margin-top','10')+droppicsBase::loadValue($params,'margin-bottom','10')+droppicsBase::loadValue($params,'border_size','1')*2;
            $style .= 'height : '.$height.'px;';
            $width   = droppicsBase::getParam('thumbnail_width','0')+droppicsBase::loadValue($params,'margin-left','10')+droppicsBase::loadValue($params,'margin-right','10')+droppicsBase::loadValue($params,'border_size','1')*2;
            $style .= 'width : '.$width.'px;';
            $style .= '}';
            
            $style .= '#droppicsgallery'.$this->options['id_gallery'].'.droppicsgallerydefault .img {';
            $style .= 'border-width:'.droppicsBase::loadValue($params,'border_size','1').'px;';
            $style .= 'border-radius:'.droppicsBase::loadValue($params,'border_radius','5').'px;';
            $style .= 'border-color:'.droppicsBase::loadValue($params,'border_color','#EEE').';';
            $style .= 'border-style: solid;';
            //todo insert shadow size
            if(droppicsBase::loadValue($params,'shadow_size','0')!='0'){
                $style .= 'box-shadow: '.(droppicsBase::loadValue($params,'shadow_size','0')-1).'px '.droppicsBase::loadValue($params,'shadow_size','0').'px '.droppicsBase::loadValue($params,'shadow_size','0').'px 1px '.droppicsBase::loadValue($params,'shadow_color','#EEE').';';
                $style .= '-moz-box-shadow: '.(droppicsBase::loadValue($params,'shadow_size','0')-1).'px '.droppicsBase::loadValue($params,'shadow_size','0').'px '.droppicsBase::loadValue($params,'shadow_size','0').'px 1px '.droppicsBase::loadValue($params,'shadow_color','#EEE').';';
                $style .= '-webkit-box-shadow: '.(droppicsBase::loadValue($params,'shadow_size','0')-1).'px '.droppicsBase::loadValue($params,'shadow_size','0').'px '.droppicsBase::loadValue($params,'shadow_size','0').'px 1px '.droppicsBase::loadValue($params,'shadow_color','#EEE').';';
            }
            $style .= 'margin-top:'.droppicsBase::loadValue($params,'margin-top','10').'px;';
            $style .= 'margin-right:'.droppicsBase::loadValue($params,'margin-right','10').'px;';
            $style .= 'margin-bottom:'.droppicsBase::loadValue($params,'margin-bottom','10').'px;';
            $style .= 'margin-left:'.droppicsBase::loadValue($params,'margin-left','10').'px;';
            $style .= '}';
            $style .= '.droppicsgallerydefault .droppicscatslink span {';
            $style .= 'margin-right:'.droppicsBase::loadValue($params,'margin-right','10').'px;';
            $style .= 'margin-left:'.droppicsBase::loadValue($params,'margin-left','10').'px;';
            $style .= '}';
            if($dynamic===false){
                $doc = JFactory::getDocument();
                $doc->addStyleDeclaration($style);
            }else{
                return 'loadHeadStyle("'.$style.'","droppicsgalleryStyle'.$options['id_gallery'].'")';
            }
            return '';
            
    }
    
}
