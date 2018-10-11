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
class plgContentdroppics extends JPlugin
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
    public function onContentPrepare($context, &$article, &$params, $limitstart)
    {
        //Replace gallery
	$text = preg_replace_callback('@<img.*?data\-droppicsgallery="([0-9]+)".*?>@', array($this,'replace'),$article->text);
	if($text!==null){
	    $article->text = $text;
	}
        //Replace single old versions image
        $text = preg_replace_callback('@<img.*?data\-droppicspicture="([0-9]+)".*?>@', array($this,'replaceSingleOld'),$article->text);
	if($text!==null){
	    $article->text = $text;
	}
        //Add lightbox to image or links
        $text = preg_replace_callback('@<[img|a].*?class=["\'].*droppicslightbox.*["\'].*?>@', array($this,'includeLightbox'),$article->text);
	if($text!==null){
	    $article->text = $text;
	}
        return true;
    }

    public function root_folder(){ return  droppicsBase::getParam('change_root_folder_old', 'com_droppics');}
    private function replace($match){
        jimport('joomla.application.component.model');
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_droppics/models/','droppicsModel');
        JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_droppics/models/','droppicsModel');
        
        $model = JModelLegacy::getInstance('Gallery','droppicsModel');
        $models = JModelLegacy::getInstance('Frontcategories','droppicsModel');
        $modelc = JModelLegacy::getInstance('Frontcategory','droppicsModel');
        
        JPluginHelper::importPlugin('droppics');
        $dispatcher = JDispatcher::getInstance();
        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration('droppicsBaseUrl="'.JURI::base(true).'";');
        preg_match('@droppicsversion=\"([0-9.]+)\"@', $match[0],$version);
        if(isset($version[1]) && version_compare($version[1], '2.0','ge')){
            //greater than 2.0.2
            $category = (int)$match[1];
        }elseif(isset($version[0]) && strstr($version[0], 'administrator/components/com_droppics/assets/images/gallery.png')){
            //lower than 2.0.2
            $category = $model->getGalleryFromOldId((int)$match[1]);
        }else{
            //version 2.0.1
            $gal = $model->getGallery((int)$match[1]);
            if(!empty($gal)){
                $category = (int)$match[1];
            }else{            
                $category = $model->getGalleryFromOldId((int)$match[1]);
            }
        }
        $listchid = array();
        $listchid[] = $category;
        $models->getAllChildOrParent($category,$listchid,true);
        $models->getAllChildOrParent($category,$listchid,false);
        $theme = $model->getGalleryTheme($category);
        $componentParams = JComponentHelper::getParams('com_droppics');
        $cat_params = $model->getGalleryParams($category);
        //add function getGalleryByPrams display sort by on Front-End
        if ($componentParams->get('loadthemecategory', 1) == 0) {
            $cat_params = $this->loadParams($theme, $cat_params, $componentParams);
            $pictures = $model->getGalleryByPrams($category,$cat_params,$theme);
        }else{
            $pictures = $model->getGalleryByPrams($category,$cat_params,$theme);
        }
        $categories = $models->getCategories($category);
        $parent = $models->getParent($category);
        if(!empty($pictures) || !empty($categories)){
            $gallery = $dispatcher->trigger('onShowFrontGallery',
                    array(
                        array(
                            'from_plugin'=>true,
                            'id_gallery'=>$category,
                            'theme'=> $theme,
                            'pictures'=>$pictures,
                            'categories'=>$categories,
                            'parent'=>$parent,
                            'params'=> $cat_params,
                            'listchid'=>$listchid
                            )
                        )
                    );
            return $gallery[0];
        }
        return '';
    }
    
    /**
     * load theme params
     * @param string $theme
     * @param null $cat_params
     * @param null $global_params
     * @return stdClass
     */
    function loadParams($theme = 'default', $cat_params = null, $global_params = null) {
        $ob = new stdClass();
        if ($theme == '') $theme = 'default';      
        foreach ($global_params->toArray() as $key => $val) {           
            if(strpos($key,$theme."_") !== false) {
                $ob->$key = $global_params->get($key);
            }
        }

        return $ob;
    }
    
    private function includeLightbox($match){
        $doc = JFactory::getDocument();
        $params = JComponentHelper::getParams('com_droppics');
        JLoader::register('DroppicsBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/droppicsBase.php');
        JHtml::_('jquery.framework');
        $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/jquery.colorbox-min.js');
        $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/colorbox.init.js');
        $doc->addStyleSheet(JURI::base('true').'/components/com_droppics/assets/css/colorbox.css');
        return $match[0];
    }
    
    /**
     * Replace a single image
     * @param type $match
     * @return string 
     * used for older version compatibilites
     */
    private function replaceSingleOld($match){
        JLoader::register('DroppicsBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/droppicsBase.php');
        droppicsBase::setDefine();
        jimport('joomla.application.component.model');
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_droppics/models/','droppicsModel');
        
        $modelC = JModelLegacy::getInstance('Gallery','droppicsModel');
        $model = JModelLegacy::getInstance('Files','droppicsModel');
        $picture = $model->getPicture((int)$match[1]);

        if(!empty($picture)){
            $category = $picture->id_gallery;
            if(strpos($match[0],'data-droppicssource')===false && strpos($match[0],'data-droppicscategory')===false){                
                //case before 2.0.2
                preg_match('@src=\"([^"]+)\"@', $match[0],$src);
                if(!JURI::isInternal($src[1])){
                    $url = parse_url($src);
                    $src = $url['path'];                    
                }else{
                    $src = $src[1];
                }   
                if(strpos($src,'thumbnails')!==false){
                    //case thumbnail
                    if(!file_exists(COM_MEDIA_BASE.DIRECTORY_SEPARATOR.  str_replace('/', DIRECTORY_SEPARATOR, $src))){
                        $match[0] = preg_replace('@src=\"[^"]+\"@', 'src="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$category.'/thumbnails/'.$picture->filever.'"', $match[0]);
                    }                    
                }
            }
            
            $pparams = json_decode($picture->params);
            $isVideo = false;
            if(is_object($pparams) && isset($pparams->is_video) && $pparams->is_video) {
                $isVideo = true;
                $vwidth = (int)droppicsBase::getParam('video_width', 640);                    
                if(isset($pparams->ratio) && $pparams->ratio ) {
                    $height = $vwidth /$pparams->ratio ;                             
                } else {
                    $height = 390; 
                }
                if(preg_match('@data-click=\"([^"]+)\"@', $match[0],$click)){
                    $imgp_click =  $click[1];
                }else{
                     $imgp_click =  $pparams->imgp_click;
                }
                if($imgp_click=="" || $imgp_click=="current") {             
                    $content = '<div class="videoWrapper"><iframe src="'.$pparams->directLink .'" width="'.$vwidth.'" height="'.$height.'"  frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen  title="'.$picture->title.'" ></iframe></div>';
                    return $content;
                }                                                                                
            }
            
            $img = str_replace('<img', '<img alt="'.$picture->alt.'" ', $match[0]); 
            if(droppicsBase::getParam('single_image_settting',1)==0){
                $style = "";
                $radius = (int)droppicsBase::getParam('imgp_radius',3);
                if($radius > 0){
                    $style .='border-radius: '. $radius.'px;';
                    $style .='-webkit-border-radius: '. $radius.'px;';
                    $style .='-moz-border-radius: '. $radius.'px;';
                }
                $border = (int)droppicsBase::getParam('imgp_border',0);
                if($border>0){
                    $style .='border-width: '.$border.'px; border-style:solid;';
                    $bordercolor = droppicsBase::getParam('imgp_border_color','#CCCCCC'); 
                    $style .='border-color: '. $bordercolor .';';    
                }
                $shadow =(int)droppicsBase::getParam('imgp_shadow',0);
                $shadowcolor =droppicsBase::getParam('imgp_shadow_color','#CCCCCC');                
                if($shadowcolor!='' && $shadow>0){
                    $style .= 'shadow-color: '.$shadowcolor.';';
                    $style .= 'box-shadow: '.$shadow.'px '.$shadow.'px '.$shadow.'px 1px '.$shadowcolor.';';
                    $style .= '-moz-box-shadow: '.$shadow.'px '.$shadow.'px '.$shadow.'px 1px '.$shadowcolor.';';
                    $style .= '-webkit-box-shadow: '.$shadow.'px '.$shadow.'px '.$shadow.'px 1px '.$shadowcolor.';';
                }
                
                $margin_left =(int)droppicsBase::getParam('imgp_margin_left',0);                
                if($margin_left>0){
                        $style .='margin-left: '.$margin_left.'px;';
                }
                $margin_top =(int)droppicsBase::getParam('imgp_margin_top',0);                
                if($margin_top>0){
                        $style .='margin-top: '.$margin_top.'px;';
                }
                $margin_right =(int)droppicsBase::getParam('imgp_margin_right',0);                
                if($margin_right>0){
                        $style .='margin-right: '.$margin_right.'px;';
                }
                $margin_bottom =(int)droppicsBase::getParam('imgp_margin_bottom',0);                
                if($margin_bottom>0){
                        $style .='margin-bottom: '.$margin_bottom.'px;';
                }
                                    
               
                libxml_use_internal_errors(true);
                $domdoc = new DOMDocument();
                $domdoc->loadHTML($img) ;
                foreach ($domdoc->getElementsByTagName('img') as $item) {
                    //parse float in style
                    $float = "";
                    $oldStyle =  $item->getAttribute('style');
                    $temp_arr = explode(";",$oldStyle);
                    if(count($temp_arr)) {
                        foreach ($temp_arr as $temp) {                            
                            if(strpos($temp, 'float') !== false) {
                               $float = $temp;
                            }
                        }
                    }
                    if($float) {
                        $style .= ";".$float;
                    }
                    $item->setAttribute('style', $style);
                    $img = $domdoc->saveHTML();                    
                }                
            }
         
            if(droppicsBase::getParam('showlightboxtitle',1)){
                if(preg_match('@data-title=\"([^"]+)\"@', $match[0],$title)){
                    $datatitle = 'data-title="'.$title[1].'"';
                }else{
                    $datatitle = 'data-title="'.$picture->title.'"';
                }
            }else{
                $datatitle = '';
            }
            
            $cls = "droppicssingleimage";
            if(strpos($match[0],'data-show-caption') !==false ){  
                $caption = $picture->title ;
                $pparams = json_decode($picture->params);
                if(is_object($pparams) && isset($pparams->show_caption) && $pparams->show_caption) {
                    $caption = (empty($pparams->content_custom_title) )? $picture->title : $pparams->content_custom_title ;                                                                          
                }
                 
                $img .= "<span>". $caption. "</span>";
                $cls .= " showcaption";
            }
             
            if(strpos($match[0], 'rel="lightbox"')!==false || strpos($match[0], 'data-droppicslightbox="lightbox"')!==false){
                $doc = JFactory::getDocument();
		        JHtml::_('jquery.framework');
                $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/jquery.colorbox-min.js');
                $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/colorbox.init.js');
                $doc->addStyleSheet(JURI::base('true').'/components/com_droppics/assets/css/colorbox.css');
                if($isVideo) { //video open in lightbox      
                    $content = '<div class="droppicssinglevideo">' .
                        '<a data-vwidth="'.$vwidth.'" data-vheight="'.$height.'" title="'. $picture->title .'" href="'. $pparams->directLink .'" rel="lightbox" class="'.$cls.'" '.$datatitle.'>'.$img.'</a>';
                    $content .= "</div>";
                    return $content;
                }else {
                    return '<a title="'. $picture->title .'" href="'.COM_MEDIA_BASEURL.'/'.$this->root_folder().'/'.$category.'/'
                        .$picture->filever.'" rel="lightbox" class="'.$cls.'" '.$datatitle.'>'.$img.'</a>';
                }
            }else if(strpos($match[0],'data-show-caption') !==false ){                
                $img = '<span class="showcaption">'.$img.'</span>';
            }
          
            $doc = JFactory::getDocument();
            $doc->addStyleSheet(JURI::base('true').'/components/com_droppics/assets/css/colorbox.css');
            if($isVideo) { //video open in new tab
                $content = 
                    '<a data-vwidth="'.$vwidth.'" data-vheight="'.$height.'" title="'. $picture->title .'" href="'. $pparams->directLink .'" target="_blank" class="droppicssingleimage" '.$datatitle.'>'.$img.'</a>';
                    
                return $content; 
            }
            return $img;
        }
        return '';
    }

}
