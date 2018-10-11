<?php
 /**
 * @package Gallery WD Lite
 * @author Web-Dorado
 * @copyright (C) 2014 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
 

// No direct access allowed to this file

defined( '_JEXEC' ) or die( 'Restricted access' );

 

// Import Joomla! Plugin library file

jimport('joomla.plugin.plugin');

jimport('joomla.filesystem.file');

jimport('joomla.filesystem.folder');
JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_gallery_wd/tables');
JHTML::_('behavior.modal'); 



 

class plgContentLoadgallery_wd extends JPlugin

{

	/**

	* Plugin that loads module positions within content

	*/

// onPrepareContent, meaning the plugin is rendered at the first stage in preparing content for output

	public function onContentPrepare($context, &$row, &$params, $page=0 )

	{

      

	    // A database connection is created

		$db = JFactory::getDBO();

		// simple performance check to determine whether bot should process further

		if ( JString::strpos( $row->text, 'Loadgallery_wd' ) === false ) {

			return true;

		}

	 	// expression to search for

	 	$regex = '/{Loadgallery_wd\sgallery_type=*.*?}/i';

 

		// check whether plugin has been unpublished

		if ( !$this->params->get( 'enabled', 1 ) ) {

			$row->text = preg_replace( $regex, '', $row->text );

			return true;

		}

 

	 	// find all instances of plugin and put in $matches

		preg_match_all( $regex, $row->text, $matches );

		//print_r($matches);

		// Number of plugins

	 	$count = count( $matches[0] );

	 	// plugin only processes if there are any instances of the plugin in the text

	 	if ( $count ) {

			// Get plugin parameters

	 		$this->_process( $row, $matches, $count, $regex );

		}

		// No return value

	}

// The proccessing function

	protected function _process( &$row, &$matches, $count, $regex )

	{

		$style=-1;

		 
		        ob_start();

		
	$bwg=0;
	 	for ( $i=0; $i < $count; $i++ )

		{

	 		$load = str_replace( 'Loadgallery_wd', '', $matches[0][$i] );

	 		$load = str_replace( '{', '', $load );

	 		$load = str_replace( '}', '', $load );

 			$load = trim( $load );

			$values=explode(' ',$load);

			$params=array();
				   foreach($values as $param)
				   {
				   $par_s=explode('=',$param);
				   if(isset($par_s))
				   $params[$par_s[0]]=$par_s[1];
				   }
			

			/*$calendar=explode('=',$params[0]);
			$theme=explode('=',$params[1]);
			$view=explode('=',$params[2]);
			$views=explode('=',$params[3]);*/


			/*if($calendar[0]!='calendar' || $theme[0]!='theme' || $view[0]!='view' || $views[0]!='views' )

				continue;*/

			$modules	= $this->_load($params, $bwg);
$bwg++;
			$row->text 	= preg_replace( '{'. $matches[0][$i] .'}', $modules, $row->text );

	 	}

 

	  	// removes tags without matching module positions

		$row->text = preg_replace( $regex, '', $row->text );

	}

// The function who takes care for the 'completing' of the plugins' actions : loading the module(s)

	protected function _load( $params,$bwg)

	{

        ob_start();

        static $embedded;

                if(!$embedded)

        {

            $embedded=true;

        }

$lang = JFactory::getLanguage();
$extension = 'com_gallery_wd';
$base_dir = JPATH_SITE;
$language_tag = $lang->getTag();
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);






$document		=JFactory::getDocument();


$document->addScript(JURI::root() . 'components/com_gallery_wd/js/bwg_frontend.js');
$document->addStyleSheet(JURI::root() .'components/com_gallery_wd/css/bwg_frontend.css" type="text/css" rel="stylesheet');
$document->addStyleSheet(JURI::root() .'components/com_gallery_wd/css/jquery.mCustomScrollbar.css" type="text/css" rel="stylesheet');
$document->addStyleSheet(JURI::root() .'components/com_gallery_wd/css/jquery-ui-1.10.3.custom.css" type="text/css" rel="stylesheet');

$document->addStyleSheet(JURI::root() .'components/com_gallery_wd/css/font-awesome-4.0.1/font-awesome.css" type="text/css" rel="stylesheet');
$document->addScript(JURI::root() . 'components/com_gallery_wd/js/jquery.js');
$document->addScript(JURI::root() . 'components/com_gallery_wd/js/jquery.ui.js');
$document->addScript(JURI::root() . 'components/com_gallery_wd/js/jquery-migrate-1.2.1.js');

$document->addScript(JURI::root() . 'components/com_gallery_wd/js/jquery.fullscreen-0.4.1.js');

$document->addScript(JURI::root() . 'components/com_gallery_wd/js/bwg_gallery_box.js');
$document->addScript(JURI::root() . 'components/com_gallery_wd/js/3DEngine/3DEngine.js');
$document->addScript(JURI::root() . 'components/com_gallery_wd/js/3DEngine/Sphere.js');
$document->addScript(JURI::root() . 'components/com_gallery_wd/js/jquery.mCustomScrollbar.concat.min.js');
echo "<script src='".JURI::root() ."components/com_gallery_wd/js/jquery.raty.js'></script>";
		


if (isset($params['type'])) {
			  $type = $params['type'];
			}
			else {
			  $type = "";
			}
			  if (isset($params['type'])) {
			  $images_per_page = $params['images_per_page'];
			}
			else {
			  $images_per_page = "";
			}
			
			if($params['gallery_type']=='slideshow' OR $params['gallery_type']=='image_browser' OR $params['gallery_type']=='blog_style')
			{
			  $images_per_page = '';
			}
			else {
			  $images_per_page = $params['images_per_page'];
			}
			
			
			
			if($params['gallery_type']=='image_browser')
			$images_per_page=1;
			if($params['gallery_type']=='blog_style')
			$images_per_page=$params['blog_style_images_per_page'];
		

			$this->params=$params;
			switch($params['gallery_type'])
			{
			case 'thumbnails':
			case 'thumbnails_masonry':
			case 'slideshow':
			case 'image_browser':
			case 'blog_style':
			$this->get_gallery_row_data=$this->get_gallery_row_data($params['gallery_id']);
			$this->get_image_rows_data=$this->get_image_rows_data($params['gallery_id'],  $images_per_page, $params['sort_by'], $bwg,$type,$params['order_by']);
			$this->page_nav=$this->page_nav($params['gallery_id'],  $images_per_page, $bwg,$type);
			break;
			
			
			case 'album_compact_preview':
			case 'album_extended_preview':

			$type_compat = (isset($_POST['type_' . $bwg]) ? htmlspecialchars($_POST['type_' . $bwg]) : (isset($params['type']) ? $params['type'] : 'album'));
			if ($type_compat == 'gallery') {
			  $items_per_page = $params['images_per_page'];
			  }
			else
			{
			      $items_per_page = $params['albums_per_page'];
			}
			$album_gallery_id = (isset($_POST['album_gallery_id_' . $bwg]) ? htmlspecialchars($_POST['album_gallery_id_' . $bwg]) : $params['album_id']);
			$this->get_image_rows_data=$this->get_image_rows_data($album_gallery_id, $items_per_page, $params['sort_by'], $bwg,'',$params['order_by']);
			$this->gallery_page_nav=$this->gallery_page_nav($album_gallery_id, $items_per_page, $bwg);
			$this->get_alb_gals_row=$this->get_alb_gals_row($album_gallery_id, $items_per_page, $params['sort_by'], $bwg);
			$this->album_page_nav=$this->album_page_nav($album_gallery_id, $items_per_page, $bwg);
		    break;
			}
			
			
			$this->get_theme_row_data=$this->get_theme_row_data($params['theme_id']);
			$this->get_options_row_data=$this->get_options_row_data();

		
		
		switch($params['gallery_type'])
			{
			case 'thumbnails':
			$this->thumbnails($bwg);
			break;

			case 'thumbnails_masonry':
			$this->not_available(1);
			break;

			case 'slideshow':
			$this->slideshow($bwg);
			break;

			case 'image_browser':
			$this->image_browser($bwg);
			break;

			case 'album_compact_preview':
			$this->album_compact_preview($bwg);
			break;

			case 'album_extended_preview':
			$this->album_extended_preview($bwg);
			break;

			case 'blog_style':
			$this->not_available(2);
			break;

			}			
			
			
		
			
	

        $content=ob_get_contents();

                ob_end_clean();

                return $content;



	}


function not_available($view)
{

if($view==1)
echo "<strong>Masonry view is disabled in free version</strong>";

if($view==2)
echo "<strong>Blog style view is disabled in free version</strong>";

}	
	
	
function album_extended_preview($bwg)
{
$WD_BWG_DIR=JPATH_BASE.'/components/com_gallery_wd';
$WD_BWG_URL=JURI::root().'components/com_gallery_wd';	

$db		=JFactory::getDBO();
$query="SHOW TABLES LIKE '#__bwg_option'";
$db->setQuery($query);


if ($db->query()) {

$query='SELECT images_directory FROM #__bwg_option WHERE id=1';
$db->setQuery($query);
$WD_BWG_UPLOAD_DIR= $db->loadResult() . '/com_gallery_wd/uploads';
}
else {
 
$WD_BWG_UPLOAD_DIR="administrator/components/com_gallery_wd/uploads";
}

$params=$this->params;

$uri	= JFactory::getURI();
		$current_url=$uri->toString();


$session = JFactory::getSession(); 
$session->set('current_url',$current_url);
    require_once($WD_BWG_DIR . '/framework/WDWLibrary.php');

    if (!isset($params['image_title_show_hover'])) {
      $params['image_title_show_hover'] = 'none';
    }
	

    if (!isset($params['popup_fullscreen'])) {
      $params['popup_fullscreen'] = 0;
    }
    if (!isset($params['popup_autoplay'])) {
      $params['popup_autoplay'] = 0;
    }
    if (!isset($params['popup_enable_pinterest'])) {
      $params['popup_enable_pinterest'] = 0;
    }
    if (!isset($params['popup_enable_tumblr'])) {
      $params['popup_enable_tumblr'] = 0;
    }
    if (!isset($params['show_search_box'])) {
      $params['show_search_box'] = 0;
    }
    if (!isset($params['search_box_width'])) {
      $params['search_box_width'] = 180;
    }
    if (!isset($params['popup_enable_info'])) {
      $params['popup_enable_info'] = 0;
    }
    if (!isset($params['popup_info_always_show'])) {
      $params['popup_info_always_show'] = 0;
    }
    if (!isset($params['popup_enable_rate'])) {
      $params['popup_enable_rate'] = 0;
    }
    if (!isset($params['thumb_click_action']) || $params['thumb_click_action'] == 'undefined') {
      $params['thumb_click_action'] = 'open_lightbox';
    }
    if (!isset($params['thumb_link_target'])) {
      $params['thumb_link_target'] = 1;
    }
    if (!isset($params['popup_hit_counter'])) {
      $params['popup_hit_counter'] = 0;
    }
    if (!isset($params['order_by'])) {
      $params['order_by'] = ' ASC ';
    }   
    if (!isset($params['popup_fullscreen'])) {
      $params['popup_fullscreen'] = 0;
    }
    if (!isset($params['popup_autoplay'])) {
      $params['popup_autoplay'] = 0;
    }
    if (!isset($params['popup_enable_pinterest'])) {
      $params['popup_enable_pinterest'] = 0;
    }
    if (!isset($params['popup_enable_tumblr'])) {
      $params['popup_enable_tumblr'] = 0;
    }
    if (!isset($params['show_search_box'])) {
      $params['show_search_box'] = 0;
    }
    if (!isset($params['search_box_width'])) {
      $params['search_box_width'] = 180;
    }
    if (!isset($params['popup_enable_info'])) {
      $params['popup_enable_info'] = 0;
    }
    if (!isset($params['popup_info_always_show'])) {
      $params['popup_info_always_show'] = 0;
    }
    if (!isset($params['popup_enable_rate'])) {
      $params['popup_enable_rate'] = 0;
    }
    if (!isset($params['thumb_click_action']) || $params['thumb_click_action'] == 'undefined') {
      $params['thumb_click_action'] = 'open_lightbox';
    }
    if (!isset($params['thumb_link_target'])) {
      $params['thumb_link_target'] = 1;
    }
    if (!isset($params['popup_hit_counter'])) {
      $params['popup_hit_counter'] = 0;
    }
    if (!isset($params['order_by'])) {
      $params['order_by'] = ' ASC ';
    }
	
	
		$album_view_type = 'thumbnail';
    $theme_row = $this->get_theme_row_data;
    if (!$theme_row) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_THEME'), 'error');
      return;
    }
    $type = (isset($_POST['type_' . $bwg]) ? htmlspecialchars($_POST['type_' . $bwg]) : 'album');

	
    $album_gallery_id = (isset($_POST['album_gallery_id_' . $bwg]) ? htmlspecialchars($_POST['album_gallery_id_' . $bwg]) : $params['album_id']);
    $album_gallery_id = (isset($_POST['album_gallery_id_' . $bwg]) ? htmlspecialchars($_POST['album_gallery_id_' . $bwg]) : $params['album_id']);
    if (!$album_gallery_id) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_ALBUM'), 'error');
      return;
    }
    if ($type == 'gallery') {
	
      $items_per_page = $params['images_per_page'];
      $items_col_num = $params['image_column_number'];
      $image_rows = $this->get_image_rows_data;
	  $images_count = count($image_rows);
      $page_nav = $this->gallery_page_nav;
      $album_gallery_div_id = 'bwg_album_extended_' . $bwg;
      $album_gallery_div_class = 'bwg_standart_thumbnails_' . $bwg;
    }
    else {
      $items_per_page = $params['albums_per_page'];
      $items_col_num = 1;
      $album_galleries_row = $this->get_alb_gals_row;
      $page_nav = $this->album_page_nav;
      $album_gallery_div_id = 'bwg_album_extended_' . $bwg;
      $album_gallery_div_class = 'bwg_album_extended_thumbnails_' . $bwg;
    }
    $bwg_previous_album_id = (isset($_POST['bwg_previous_album_id_' . $bwg]) ? htmlspecialchars($_POST['bwg_previous_album_id_' . $bwg]) : 0);
    $bwg_previous_album_page_number = (isset($_POST['bwg_previous_album_page_number_' . $bwg]) ? htmlspecialchars($_POST['bwg_previous_album_page_number_' . $bwg]) : 0);

    $rgb_page_nav_font_color = WDWLibrary::spider_hex2rgb($theme_row->page_nav_font_color);
    $rgb_album_extended_thumbs_bg_color = WDWLibrary::spider_hex2rgb($theme_row->album_extended_thumbs_bg_color);
    $rgb_album_extended_div_bg_color = WDWLibrary::spider_hex2rgb($theme_row->album_extended_div_bg_color);
    $rgb_thumbs_bg_color = WDWLibrary::spider_hex2rgb($theme_row->thumbs_bg_color);
	
	
	 if ($type == 'gallery' ) { 
	  if($album_view_type == 'masonry') {
        $form_child_div_id = 'bwg_masonry_thumbnails_div_' . $bwg;
        $form_child_div_style = 'background-color:rgba(0, 0, 0, 0); position:relative; text-align:' . $theme_row->masonry_thumb_align . '; width:100%;';	  
        $album_gallery_div_id = 'bwg_masonry_thumbnails_' . $bwg;
        $album_gallery_div_class = 'bwg_masonry_thumbnails_' . $bwg;
	  }
	  else {
	    $form_child_div_style = 'background-color:rgba(0, 0, 0, 0); position:relative; text-align:' . $theme_row->thumb_align . '; width:100%;';
		$form_child_div_id = '';
	  }
    }
    else {
      $form_child_div_id = '';
      $form_child_div_style = 'background-color:rgba(0, 0, 0, 0); position:relative; text-align:' . $theme_row->album_extended_thumb_align . '; width:100%;';
    }
	
	
	///////////////////////////////////////////////////////
	$params_array = array(
      'view' => 'GalleryBox',
      'current_view' => $bwg,
      'theme_id' => $params['theme_id'],
      'thumb_width' => $params['thumb_width'],
      'thumb_height' => $params['thumb_height'],
      'open_with_fullscreen' => $params['popup_fullscreen'],
      'open_with_autoplay' => $params['popup_autoplay'],
      'image_width' => $params['popup_width'],
      'image_height' => $params['popup_height'],
      'image_effect' => $params['popup_type'],
      'sort_by' => $params['sort_by'],
      'enable_image_filmstrip' => $params['popup_enable_filmstrip'],
      'image_filmstrip_height' => $params['popup_filmstrip_height'],
      'enable_image_ctrl_btn' => $params['popup_enable_ctrl_btn'],
      'enable_image_fullscreen' => $params['popup_enable_fullscreen'],
      'popup_enable_info' => $params['popup_enable_info'],
      'popup_info_always_show' => $params['popup_info_always_show'],
      'popup_hit_counter' => $params['popup_hit_counter'],
      'popup_enable_rate' => $params['popup_enable_rate'],
      'slideshow_interval' => $params['popup_interval'],
      'enable_comment_social' => $params['popup_enable_comment'],
      'enable_image_facebook' => $params['popup_enable_facebook'],
      'enable_image_twitter' => $params['popup_enable_twitter'],
      'enable_image_google' => $params['popup_enable_google'],
      'enable_image_pinterest' => $params['popup_enable_pinterest'],
      'enable_image_tumblr' => $params['popup_enable_tumblr'],
      'watermark_type' => $params['watermark_type'],
      'current_url' =>$this->bgw_url_encode($current_url)
    );

    if ($params['watermark_type'] != 'none') {
      $params_array['watermark_link'] = $params['watermark_link'];
      $params_array['watermark_opacity'] = $params['watermark_opacity'];
      $params_array['watermark_position'] = $params['watermark_position'];
    }
    if ($params['watermark_type'] == 'text') {
      $params_array['watermark_text'] = $params['watermark_text'];
      $params_array['watermark_font_size'] = $params['watermark_font_size'];
      $params_array['watermark_font'] = $params['watermark_font'];
      $params_array['watermark_color'] = $params['watermark_color'];
    }
    elseif ($params['watermark_type'] == 'image') {
      $params_array['watermark_url'] = $params['watermark_url'];
      $params_array['watermark_width'] = $params['watermark_width'];
      $params_array['watermark_height'] = $params['watermark_height'];
    }
    $params_array_hash = $params_array;


	
    ?>
   <style>
		  /* Style for masonry view.*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumbnails_<?php echo $bwg; ?> * {
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumb_<?php echo $bwg; ?> {
        visibility: hidden;
        text-align: center;
        display: inline-block;
        vertical-align: middle;     
        width: <?php echo $params['thumb_width']; ?>px !important;
        border-radius: <?php echo $theme_row->masonry_thumb_border_radius; ?>;
        border: <?php echo $theme_row->masonry_thumb_border_width; ?>px <?php echo $theme_row->masonry_thumb_border_style; ?> #<?php echo $theme_row->masonry_thumb_border_color; ?>;
        background-color: #<?php echo $theme_row->thumb_bg_color; ?>;
        margin: 0;
        padding: <?php echo $theme_row->masonry_thumb_padding; ?>px !important;
        opacity: <?php echo $theme_row->masonry_thumb_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->masonry_thumb_transparent; ?>);
        <?php echo ($theme_row->masonry_thumb_transition) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
        z-index: 100;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumb_<?php echo $bwg; ?>:hover {
        opacity: 1;
        filter: Alpha(opacity=100);
        transform: <?php echo $theme_row->masonry_thumb_hover_effect; ?>(<?php echo $theme_row->masonry_thumb_hover_effect_value; ?>);
        -ms-transform: <?php echo $theme_row->masonry_thumb_hover_effect; ?>(<?php echo $theme_row->masonry_thumb_hover_effect_value; ?>);
        -webkit-transform: <?php echo $theme_row->masonry_thumb_hover_effect; ?>(<?php echo $theme_row->masonry_thumb_hover_effect_value; ?>);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        -ms-backface-visibility: hidden;
        z-index: 102;
        position: absolute;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumbnails_<?php echo $bwg; ?> {
        -moz-box-sizing: border-box;
        background-color: rgba(<?php echo $rgb_thumbs_bg_color['red']; ?>, <?php echo $rgb_thumbs_bg_color['green']; ?>, <?php echo $rgb_thumbs_bg_color['blue']; ?>, <?php echo $theme_row->masonry_thumb_bg_transparent / 100; ?>);
        box-sizing: border-box;
        display: inline-block;
        font-size: 0;
        /*width: <?php echo $params['image_column_number'] * ($params['thumb_width'] + 2 * ($theme_row->masonry_thumb_padding + $theme_row->masonry_thumb_border_width)); ?>px;*/
        width: 100%;
        position: relative;
        text-align: <?php echo $theme_row->masonry_thumb_align; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumbnails_<?php echo $bwg; ?> a {
        cursor: pointer;
        text-decoration: none;
      }
      @media only screen and (max-width : <?php echo $params['image_column_number'] * ($params['thumb_width'] + 2 * ($theme_row->masonry_thumb_padding + $theme_row->masonry_thumb_border_width)); ?>px) {
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumbnails_<?php echo $bwg; ?> {
          width: inherit;
        }
      }	  	  
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_popup_overlay_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->lightbox_overlay_bg_color; ?>;
        opacity: <?php echo $theme_row->lightbox_overlay_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->lightbox_overlay_bg_transparent; ?>);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumb_spun_<?php echo $bwg; ?> {
        position: absolute;
      }
      /* Style for thumbnail view.*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_extended_thumbnails_<?php echo $bwg; ?> * {
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_extended_thumbnails_<?php echo $bwg; ?> {
        display: block;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background-color: rgba(<?php echo $rgb_album_extended_thumbs_bg_color['red']; ?>, <?php echo $rgb_album_extended_thumbs_bg_color['green']; ?>, <?php echo $rgb_album_extended_thumbs_bg_color['blue']; ?>, <?php echo $theme_row->album_extended_thumb_bg_transparent / 100; ?>);
        font-size: 0;
        text-align: <?php echo $theme_row->album_extended_thumb_align; ?>;
        max-width: inherit;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_extended_thumbnails_<?php echo $bwg; ?> a {
        cursor: pointer;
        text-decoration: none;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_extended_div_<?php echo $bwg; ?> {
        display: table;
        width: 100%;
        height: <?php echo $params['extended_album_height']; ?>px;
        border-spacing: <?php echo $theme_row->album_extended_div_padding; ?>px;
        border-bottom: <?php echo $theme_row->album_extended_div_separator_width; ?>px <?php echo $theme_row->album_extended_div_separator_style; ?> #<?php echo $theme_row->album_extended_div_separator_color; ?>;
        background-color: rgba(<?php echo $rgb_album_extended_div_bg_color['red']; ?>, <?php echo $rgb_album_extended_div_bg_color['green']; ?>, <?php echo $rgb_album_extended_div_bg_color['blue']; ?>, <?php echo $theme_row->album_extended_div_bg_transparent / 100; ?>);
        border-radius: <?php echo $theme_row->album_extended_div_border_radius; ?>;
        margin: <?php echo $theme_row->album_extended_div_margin; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_extended_thumb_div_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->album_extended_thumb_div_bg_color; ?>;
        border-radius: <?php echo $theme_row->album_extended_thumb_div_border_radius; ?>;
        text-align: center;
        border: <?php echo $theme_row->album_extended_thumb_div_border_width; ?>px <?php echo $theme_row->album_extended_thumb_div_border_style; ?> #<?php echo $theme_row->album_extended_thumb_div_border_color; ?>;
        display: table-cell;
        vertical-align: middle;
        padding: <?php echo $theme_row->album_extended_thumb_div_padding; ?>;
      }
      @media only screen and (max-width : 320px) {
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_extended_thumb_div_<?php echo $bwg; ?> {
          display: table-row;
        }
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_extended_text_div_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->album_extended_text_div_bg_color; ?>;
        border-radius: <?php echo $theme_row->album_extended_text_div_border_radius; ?>;
        border: <?php echo $theme_row->album_extended_text_div_border_width; ?>px <?php echo $theme_row->album_extended_text_div_border_style; ?> #<?php echo $theme_row->album_extended_text_div_border_color; ?>;
        display: table-cell;
        width: 100%;
        border-collapse: collapse;
        vertical-align: middle;
        padding: <?php echo $theme_row->album_extended_text_div_padding; ?>;
      }
      @media only screen and (max-width : 320px) {
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_extended_text_div_<?php echo $bwg; ?> {
          display: table-row;
        }
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_title_spun_<?php echo $bwg; ?> {
        border: <?php echo $theme_row->album_extended_title_span_border_width; ?>px <?php echo $theme_row->album_extended_title_span_border_style; ?> #<?php echo $theme_row->album_extended_title_span_border_color; ?>;
        color: #<?php echo $theme_row->album_extended_title_font_color; ?>;
        display: block;
        font-family: <?php echo $theme_row->album_extended_title_font_style; ?>;
        font-size: <?php echo $theme_row->album_extended_title_font_size; ?>px;
        font-weight: <?php echo $theme_row->album_extended_title_font_weight; ?>;
        height: inherit;
        margin-bottom: <?php echo $theme_row->album_extended_title_margin_bottom; ?>px;
        padding: <?php echo $theme_row->album_extended_title_padding; ?>;
        text-align: left;
        vertical-align: middle;
        width: inherit;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_description_spun1_<?php echo $bwg; ?> {
        border: <?php echo $theme_row->album_extended_desc_span_border_width; ?>px <?php echo $theme_row->album_extended_desc_span_border_style; ?> #<?php echo $theme_row->album_extended_desc_span_border_color; ?>;
        display: inline-block;
        color: #<?php echo $theme_row->album_extended_desc_font_color; ?>;
        font-size: <?php echo $theme_row->album_extended_desc_font_size; ?>px;
        font-weight: <?php echo $theme_row->album_extended_desc_font_weight; ?>;
        font-family: <?php echo $theme_row->album_extended_desc_font_style; ?>;
        height: inherit;
        padding: <?php echo $theme_row->album_extended_desc_padding; ?>;
        vertical-align: middle;
        width: inherit;
        word-wrap: break-word;
        word-break: break-word;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_description_spun1_<?php echo $bwg; ?> * {
        margin: 0;
        text-align: left !important;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_description_spun2_<?php echo $bwg; ?> {
        float: left;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_description_short_<?php echo $bwg; ?> {
        display: inline;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_description_full_<?php echo $bwg; ?> {
        display: none;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_description_more_<?php echo $bwg; ?> {
        clear: both;
        color: #<?php echo $theme_row->album_extended_desc_more_color; ?>;
        cursor: pointer;
        float: right;
        font-size: <?php echo $theme_row->album_extended_desc_more_size; ?>px;
        font-weight: normal;
      }
      /*Album thumbs styles.*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumb_<?php echo $bwg; ?> {
        display: inline-block;
        text-align: center;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumb_spun1_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->album_extended_thumb_bg_color; ?>;
        border-radius: <?php echo $theme_row->album_extended_thumb_border_radius; ?>;
        border: <?php echo $theme_row->album_extended_thumb_border_width; ?>px <?php echo $theme_row->album_extended_thumb_border_style; ?> #<?php echo $theme_row->album_extended_thumb_border_color; ?>;
        box-shadow: <?php echo $theme_row->album_extended_thumb_box_shadow; ?>;
        display: inline-block;
        height: <?php echo $params['album_thumb_height']; ?>px;
        margin: <?php echo $theme_row->album_extended_thumb_margin; ?>px;
        opacity: <?php echo $theme_row->album_extended_thumb_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->album_extended_thumb_transparent; ?>);
        <?php echo ($theme_row->album_extended_thumb_transition) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
        padding: <?php echo $theme_row->album_extended_thumb_padding; ?>px;
        text-align: center;
        vertical-align: middle;
        width: <?php echo $params['album_thumb_width']; ?>px;
        z-index: 100;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumb_spun1_<?php echo $bwg; ?>:hover {
        opacity: 1;
        filter: Alpha(opacity=100);
        transform: <?php echo $theme_row->album_extended_thumb_hover_effect; ?>(<?php echo $theme_row->album_extended_thumb_hover_effect_value; ?>);
        -ms-transform: <?php echo $theme_row->album_extended_thumb_hover_effect; ?>(<?php echo $theme_row->album_extended_thumb_hover_effect_value; ?>);
        -webkit-transform: <?php echo $theme_row->album_extended_thumb_hover_effect; ?>(<?php echo $theme_row->album_extended_thumb_hover_effect_value; ?>);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        -ms-backface-visibility: hidden;
        z-index: 102;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumb_spun2_<?php echo $bwg; ?> {
        display: inline-block;
        height: <?php echo $params['album_thumb_height']; ?>px;
        overflow: hidden;
        width: <?php echo $params['album_thumb_width']; ?>px;
      }
      /*Image thumbs styles.*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun1_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->thumb_bg_color; ?>;
        border-radius: <?php echo $theme_row->thumb_border_radius; ?>;
        border: <?php echo $theme_row->thumb_border_width; ?>px <?php echo $theme_row->thumb_border_style; ?> #<?php echo $theme_row->thumb_border_color; ?>;
        box-shadow: <?php echo $theme_row->thumb_box_shadow; ?>;
        display: inline-block;
        height: <?php echo $params['thumb_height']; ?>px;
        margin: <?php echo $theme_row->thumb_margin; ?>px;
        opacity: <?php echo $theme_row->thumb_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->thumb_transparent; ?>);
        <?php echo ($theme_row->thumb_transition) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
        padding: <?php echo $theme_row->thumb_padding; ?>px;
        text-align: center;
        vertical-align: middle;
        width: <?php echo $params['thumb_width']; ?>px;
        z-index: 100;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun1_<?php echo $bwg; ?>:hover {
        -ms-transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        -webkit-transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        -ms-backface-visibility: hidden;
        opacity: 1;
        filter: Alpha(opacity=100);
        transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        z-index: 102;
        position: relative;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun2_<?php echo $bwg; ?> {
        display: inline-block;
        height: <?php echo $params['thumb_height']; ?>px;
        overflow: hidden;
        width: <?php echo $params['thumb_width']; ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumbnails_<?php echo $bwg; ?> {
        -moz-box-sizing: border-box;
        display: inline-block;
        background-color: rgba(<?php echo $rgb_thumbs_bg_color['red']; ?>, <?php echo $rgb_thumbs_bg_color['green']; ?>, <?php echo $rgb_thumbs_bg_color['blue']; ?>, <?php echo $theme_row->thumb_bg_transparent / 100; ?>);
        box-sizing: border-box;
        font-size: 0;
        max-width: <?php echo $params['image_column_number'] * ($params['thumb_width'] + 2 * (2 + $theme_row->thumb_margin + $theme_row->thumb_padding + $theme_row->thumb_border_width)); ?>px;
        text-align: <?php echo $theme_row->thumb_align; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumbnails_<?php echo $bwg; ?> a {
        cursor: pointer;
        text-decoration: none;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_<?php echo $bwg; ?> {
        display: inline-block;
        text-align: center;
      }
      <?php
      if ($params['image_title_show_hover'] == 'show') { /* Show image title at the bottom.*/
        ?>
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_title_spun1_<?php echo $bwg; ?> {
          display: block;
          margin: 0 auto;
          opacity: 1;
          filter: Alpha(opacity=100);
          text-align: center;
          width: <?php echo $params['thumb_width']; ?>px;
        }
        <?php
      }
      elseif ($params['image_title_show_hover'] == 'hover') { /* Show image title on hover.*/
        ?>
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_title_spun1_<?php echo $bwg; ?> {
          display: table;
          height: inherit;
          left: -3000px;
          opacity: 0;
          filter: Alpha(opacity=0);
          position: absolute;
          top: 0px;
          width: inherit;
        }
        <?php
      }
      ?>
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun1_<?php echo $bwg; ?>:hover .bwg_image_title_spun1_<?php echo $bwg; ?> {
        left: <?php echo $theme_row->thumb_padding; ?>px;
        top: <?php echo $theme_row->thumb_padding; ?>px;
        opacity: 1;
        filter: Alpha(opacity=100);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_title_spun2_<?php echo $bwg; ?> {
        color: #<?php echo $theme_row->thumb_title_font_color; ?>;
        display: table-cell;
        font-family: <?php echo $theme_row->thumb_title_font_style; ?>;
        font-size: <?php echo $theme_row->thumb_title_font_size; ?>px;
        font-weight: <?php echo $theme_row->thumb_title_font_weight; ?>;
        height: inherit;
        margin: <?php echo $theme_row->thumb_title_margin; ?>;
        text-shadow: <?php echo $theme_row->thumb_title_shadow; ?>;
        vertical-align: middle;
        width: inherit;
        word-break: break-all;
        word-wrap: break-word;
      }
      /*Pagination styles.*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> {
        text-align: <?php echo $theme_row->page_nav_align; ?>;
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        margin: 6px 0 4px;
        display: block;
        height: 30px;
        line-height: 30px;
      }
      @media only screen and (max-width : 320px) {
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .displaying-num_<?php echo $bwg; ?> {
          display: none;
        }
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .displaying-num_<?php echo $bwg; ?> {
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        margin-right: 10px;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .paging-input_<?php echo $bwg; ?> {
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled:hover,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled:focus {
        cursor: default;
        color: rgba(<?php echo $rgb_page_nav_font_color['red']; ?>, <?php echo $rgb_page_nav_font_color['green']; ?>, <?php echo $rgb_page_nav_font_color['blue']; ?>, 0.5);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a {
        cursor: pointer;
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        text-decoration: none;
        padding: <?php echo $theme_row->page_nav_padding; ?>;
        margin: <?php echo $theme_row->page_nav_margin; ?>;
        border-radius: <?php echo $theme_row->page_nav_border_radius; ?>;
        border-style: <?php echo $theme_row->page_nav_border_style; ?>;
        border-width: <?php echo $theme_row->page_nav_border_width; ?>px;
        border-color: #<?php echo $theme_row->page_nav_border_color; ?>;
        background-color: #<?php echo $theme_row->page_nav_button_bg_color; ?>;
        opacity: <?php echo $theme_row->page_nav_button_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->page_nav_button_bg_transparent; ?>);
        box-shadow: <?php echo $theme_row->page_nav_box_shadow; ?>;
        <?php echo ($theme_row->page_nav_button_transition ) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_back_<?php echo $bwg; ?> {
        background-color: rgba(0, 0, 0, 0);
        color: #<?php echo $theme_row->album_extended_back_font_color; ?> !important;
        cursor: pointer;
        display: block;
        font-family: <?php echo $theme_row->album_extended_back_font_style; ?>;
        font-size: <?php echo $theme_row->album_extended_back_font_size; ?>px;
        font-weight: <?php echo $theme_row->album_extended_back_font_weight; ?>;
        text-decoration: none;
        padding: <?php echo $theme_row->album_extended_back_padding; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_popup_overlay_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->lightbox_overlay_bg_color; ?>;
        opacity: <?php echo $theme_row->lightbox_overlay_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->lightbox_overlay_bg_transparent; ?>);
      }
    </style>
    <div id="bwg_container1_<?php echo $bwg; ?>">
      <div id="bwg_container2_<?php echo $bwg; ?>">
        <form id="gal_front_form_<?php echo $bwg; ?>" method="post" action="#">
          <?php
          if ($params['show_search_box'] && $type == 'gallery') {
            WDWLibrary::ajax_html_frontend_search_box('gal_front_form_' . $bwg, $bwg, $album_gallery_div_id, $images_count, $params['search_box_width']);
          }
          ?>
          <div id="<?php echo $form_child_div_id; ?>" style="<?php echo $form_child_div_style; ?>">
            <div id="ajax_loading_<?php echo $bwg; ?>" style="position:absolute;width: 100%; z-index: 115; text-align: center; height: 100%; vertical-align: middle; display: none;">
              <div style="display: table; vertical-align: middle; width: 100%; height: 100%; background-color:#FFFFFF; opacity:0.7; filter:Alpha(opacity=70);">
                <div style="display: table-cell; text-align: center; position: relative; vertical-align: middle;" >
                  <div id="loading_div_<?php echo $bwg; ?>" style="display: inline-block; text-align: center; position: relative; vertical-align: middle;">
                    <img src="<?php echo $WD_BWG_URL . '/images/ajax_loader.png'; ?>" class="spider_ajax_loading" style="float: none; width:50px;">
                  </div>
                </div>
              </div>
            </div>
            <?php
            if ($params['album_enable_page']  && $items_per_page && ($theme_row->page_nav_position == 'top') && $page_nav['total']) {
              WDWLibrary::ajax_html_frontend_page_nav($theme_row, $page_nav['total'], $page_nav['limit'], 'gal_front_form_' . $bwg, $items_per_page, $bwg, $album_gallery_div_id, $params['album_id'], $type);
            }
            if ($bwg_previous_album_id) {
              ?>
              <a class="bwg_back_<?php echo $bwg; ?>" onclick="spider_frontend_ajax('gal_front_form_<?php echo $bwg; ?>', '<?php echo $bwg; ?>', '<?php echo $album_gallery_div_id; ?>', 'back')"><?php echo JText::_('BACK'); ?></a>
              <?php
            }
            ?>
            <div id="<?php echo $album_gallery_div_id; ?>" class="<?php echo $album_gallery_div_class; ?>">
              <input type="hidden" id="bwg_previous_album_id_<?php echo $bwg; ?>" name="bwg_previous_album_id_<?php echo $bwg; ?>" value="<?php echo $bwg_previous_album_id; ?>" />
              <input type="hidden" id="bwg_previous_album_page_number_<?php echo $bwg; ?>" name="bwg_previous_album_page_number_<?php echo $bwg; ?>" value="<?php echo $bwg_previous_album_page_number; ?>" />
              <?php
              if ($type != 'gallery') {
                if (!$page_nav['total']) {
                  ?>
                  <span class="bwg_back_<?php echo $bwg; ?>"><?php echo JText::_('EMPTY_ALBUM'); ?></span>
                  <?php
                }
                foreach ($album_galleries_row as $album_galallery_row) {
                  if ($album_galallery_row->is_album) {
                    $album_row = $this->get_album_row_data($album_galallery_row->alb_gal_id);
                    if (!$album_row) {
                      continue;
                    }
                    $preview_image = $album_row->preview_image;
                    if (!$preview_image) {
                      $preview_image = $album_row->random_preview_image;
                    }
                    $def_type = 'album';
                    $title = $album_row->name;
                    $description = $album_row->description;
                  }
                  else {
                    $gallery_row = $this->get_gallery_row_data($album_galallery_row->alb_gal_id);
                    if (!$gallery_row) {
                      continue;
                    }
                    $preview_image = $gallery_row->preview_image;
                    if (!$preview_image) {
                      $preview_image = $gallery_row->random_preview_image;
                    }
                    $def_type = 'gallery';
                    $title = $gallery_row->name;
                    $description = $gallery_row->description;
                  }
                  if (!$preview_image) {
                    $preview_url = $WD_BWG_URL . '/images/no-image.png';
                    $preview_path = $WD_BWG_DIR . '/images/no-image.png';
                  }
                  else {
                    $preview_url = JURI::root() . $WD_BWG_UPLOAD_DIR.'/' . $preview_image;
                    $preview_path = JPATH_SITE.'/' . $WD_BWG_UPLOAD_DIR.'/' . $preview_image;
                  }
				 				  $preview_url=htmlspecialchars($preview_url);
				  $preview_path=htmlspecialchars($preview_path);
 
                  list($image_thumb_width, $image_thumb_height) = getimagesize(htmlspecialchars_decode($preview_path, ENT_COMPAT | ENT_QUOTES));
                  $scale = max($params['album_thumb_width'] / $image_thumb_width, $params['album_thumb_height'] / $image_thumb_height);
                  $image_thumb_width *= $scale;
                  $image_thumb_height *= $scale;
                  $thumb_left = ($params['album_thumb_width'] - $image_thumb_width) / 2;
                  $thumb_top = ($params['album_thumb_height'] - $image_thumb_height) / 2;
                  if ($type != 'gallery') {
                    ?>
                    <div class="bwg_album_extended_div_<?php echo $bwg; ?>">
                      <div class="bwg_album_extended_thumb_div_<?php echo $bwg; ?>">
                        <a style="font-size: 0;" onclick="spider_frontend_ajax('gal_front_form_<?php echo $bwg; ?>', '<?php echo $bwg; ?>', 'bwg_album_extended_<?php echo $bwg; ?>', '<?php echo $album_galallery_row->alb_gal_id; ?>', '<?php echo $album_gallery_id; ?>', '<?php echo $def_type; ?>')">
                          <span class="bwg_album_thumb_<?php echo $bwg; ?>" style="height:inherit;">
                            <span class="bwg_album_thumb_spun1_<?php echo $bwg; ?>">
                              <span class="bwg_album_thumb_spun2_<?php echo $bwg; ?>">
                                <img style="padding: 0; max-height:none; max-width:none; width:<?php echo $image_thumb_width; ?>px; height:<?php echo $image_thumb_height; ?>px; margin-left: <?php echo $thumb_left; ?>px; margin-top: <?php echo $thumb_top; ?>px;" src="<?php echo $preview_url; ?>" alt="<?php echo $title; ?>" />
                              </span>
                            </span>
                          </span>
                        </a>
                      </div>
                      <div class="bwg_album_extended_text_div_<?php echo $bwg; ?>">
                        <?php
                        if ($title) {
                          ?>
                          <span class="bwg_title_spun_<?php echo $bwg; ?>"><?php echo $title; ?></span>
                          <?php
                        }
                        if ($params['extended_album_description_enable'] && $description) {
                          if (stripos($description, '<!--more-->') !== FALSE) {
                            $description_array = explode('<!--more-->', $description);
                            $description_short = $description_array[0];
                            $description_full = $description_array[1];
                            ?>
                            <span class="bwg_description_spun1_<?php echo $bwg; ?>">
                              <span class="bwg_description_spun2_<?php echo $bwg; ?>">
                                <span class="bwg_description_short_<?php echo $bwg; ?>">
                                  <?php echo $description_short; ?>
                                </span>
                                <span class="bwg_description_full_<?php echo $bwg; ?>">
                                  <?php echo $description_full; ?>
                                </span>
                              </span>
                              <span class="bwg_description_more_<?php echo $bwg; ?> bwg_more"><?php echo JText::_('MORE'); ?></span>
                            </span>
                            <?php
                          }
                          else {
                            ?>
                            <span class="bwg_description_spun1_<?php echo $bwg; ?>">
                              <span class="bwg_description_short_<?php echo $bwg; ?>">
                                <?php echo $description; ?>
                              </span>
                            </span>
                            <?php
                          }
                        }
                        ?>
                      </div>
                    </div>
                    <?php
                  }
                }
              }
              elseif ($type == 'gallery') {
                if (!$page_nav['total']) {
                  if ($bwg_search != '') {
                    ?>
                    <span class="bwg_back_<?php echo $bwg; ?>"><?php echo JText::_('THERE_ARE_NO_IMAGE'); ?></span>
                    <?php
                  }
                  else {
                    ?>
                    <span class="bwg_back_<?php echo $bwg; ?>"><?php echo JText::_('EMPTY_GALLERY'); ?></span>
                    <?php
                  }
                }
                
                foreach ($image_rows as $image_row) {
					  		$image_row->thumb_url=htmlspecialchars($image_row->thumb_url);
			  $image_row->image_url=htmlspecialchars($image_row->image_url);
			  $image_row->alt=htmlspecialchars($image_row->alt);
			  $image_row->filename=htmlspecialchars($image_row->filename);
 
                  $params_array['image_id'] = (isset($_POST['image_id']) ? esc_html($_POST['image_id']) : $image_row->id);
                  $params_array['gallery_id'] = $album_gallery_id;
                  $is_video = $image_row->filetype == "YOUTUBE" || $image_row->filetype == "VIMEO";
                  if (!$is_video) {
                    list($image_thumb_width, $image_thumb_height) = getimagesize(htmlspecialchars_decode(JPATH_SITE.'/' . $WD_BWG_UPLOAD_DIR.'/' . $image_row->thumb_url, ENT_COMPAT | ENT_QUOTES));
                  }
                  else {
                    $image_thumb_width = $params['thumb_width'];
                    $image_thumb_height = $params['thumb_height'];
                  }
                  $scale = max($params['thumb_width'] / $image_thumb_width, $params['thumb_height'] / $image_thumb_height);
                  $image_thumb_width *= $scale;
                  $image_thumb_height *= $scale;
                  $thumb_left = ($params['thumb_width'] - $image_thumb_width) / 2;
                  $thumb_top = ($params['thumb_height'] - $image_thumb_height) / 2;
                  if ($album_view_type == 'thumbnail') {
                    ?>
                  <a style="font-size: 0;" <?php echo ($params['thumb_click_action'] == 'open_lightbox' ? ('onclick="spider_createpopup(\'' . addslashes($this->array_to_url($params_array)) . '\', ' . $bwg . ', ' . $params['popup_width'] . ', ' . $params['popup_height'] . ', 1, \'testpopup\', 5); return false;"') : ('href="' . $image_row->redirect_url . '" target="' .  ($params['thumb_link_target'] ? '_blank' : '')  . '"')) ?>>
                    <span class="bwg_standart_thumb_<?php echo $bwg; ?>">
                      <span class="bwg_standart_thumb_spun1_<?php echo $bwg; ?>">
                        <span class="bwg_standart_thumb_spun2_<?php echo $bwg; ?>">
                          <img style="max-height:none; max-width:none; width:<?php echo $image_thumb_width; ?>px; height:<?php echo $image_thumb_height; ?>px; margin-left: <?php echo $thumb_left; ?>px; margin-top: <?php echo $thumb_top; ?>px;" id="<?php echo $image_row->id; ?>" src="<?php echo ($is_video ? "" : JURI::root() . $WD_BWG_UPLOAD_DIR.'/') . $image_row->thumb_url; ?>" alt="<?php echo $image_row->alt; ?>" />
                          <?php
                          if ($params['image_title_show_hover'] == 'hover') {
                            ?>
                            <span class="bwg_image_title_spun1_<?php echo $bwg; ?>">
                              <span class="bwg_image_title_spun2_<?php echo $bwg; ?>">
                                <?php echo $image_row->alt; ?>
                              </span>
                            </span>
                            <?php
                          }
                          ?>
                        </span>
                      </span>
                      <?php
                      if ($params['image_title_show_hover'] == 'show') {
                        ?>
                        <span class="bwg_image_title_spun1_<?php echo $bwg; ?>">
                          <span class="bwg_image_title_spun2_<?php echo $bwg; ?>">
                            <?php echo $image_row->alt; ?>
                          </span>
                        </span>
                        <?php
                      }
                      ?>
                    </span>
                  </a>
                    <?php
                  } 			  
                  else {
                    ?>
                  <span class="bwg_masonry_thumb_spun_<?php echo $bwg; ?>">
                    <a style="font-size: 0;" <?php echo ($params['thumb_click_action'] == 'open_lightbox' ? ('onclick="spider_createpopup(\'' . addslashes($this->array_to_url($params_array)) . '\', ' . $bwg . ', ' . $params['popup_width'] . ', ' . $params['popup_height'] . ', 1, \'testpopup\', 5); return false;"') : ('href="' . $image_row->redirect_url . '" target="' .  ($params['thumb_link_target'] ? '_blank' : '')  . '"')) ?>>
                      <img class="bwg_masonry_thumb_<?php echo $bwg; ?>" id="<?php echo $image_row->id; ?>" src="<?php echo ($is_video ? "" : JURI::root() . $WD_BWG_UPLOAD_DIR.'/') . $image_row->thumb_url; ?>" alt="<?php echo $image_row->alt; ?>" style="max-height: none !important;  max-width: none !important;" />
                    </a>
                  </span>
                    <?php
                  }
                }
              }
              ?>
              <script>
                jQuery(".bwg_description_more_<?php echo $bwg; ?>").click(function () {
                  if (jQuery(this).hasClass("bwg_more")) {
                    jQuery(this).parent().find(".bwg_description_full_<?php echo $bwg; ?>").show();
                    jQuery(this).attr("class", "bwg_description_more_<?php echo $bwg; ?> bwg_hide");
                    jQuery(this).html("<?php echo JText::_('HIDE'); ?>");
                  }
                  else {
                    jQuery(this).parent().find(".bwg_description_full_<?php echo $bwg; ?>").hide();
                    jQuery(this).attr("class", "bwg_description_more_<?php echo $bwg; ?> bwg_more");
                    jQuery(this).html("<?php echo JText::_('MORE'); ?>");
                  }
                });
                <?php 
                if ($album_view_type == 'masonry' && $type == 'gallery' ) {
                  ?>
                function bwg_masonry_<?php echo $bwg; ?>() { 
                  var image_width = <?php echo $params['thumb_width']; ?>;
                  var cont_div_width = <?php echo $params['image_column_number'] * ($params['thumb_width'] + 2 * ($theme_row->thumb_padding + $theme_row->thumb_border_width)); ?>;
                  if (cont_div_width > jQuery("#bwg_masonry_thumbnails_div_<?php echo $bwg; ?>").width()) {
                    cont_div_width = jQuery("#bwg_masonry_thumbnails_div_<?php echo $bwg; ?>").width();
                  }
                  var col_count = parseInt(cont_div_width / image_width);
                  if (!col_count) {
                    col_count = 1;
                  }
                  var top = new Array();
                  var left = new Array();
                  for (var i = 0; i < col_count; i++) {
                    top[i] = 0;
                    left[i] = i * image_width;
                  }
                  var div_width = col_count * image_width;
                  if (div_width > jQuery(window).width()) {
                    div_width = jQuery(window).width();
                    jQuery(".bwg_masonry_thumb_<?php echo $bwg; ?>").attr("style", "max-width: " + div_width + "px");
                  }
                  else {
                    div_width = col_count * image_width;
                  }
                  jQuery(".bwg_masonry_thumb_spun_<?php echo $bwg; ?>").each(function() {
                    min_top = Math.min.apply(Math, top);
                    index_min_top = jQuery.inArray(min_top, top);
                    jQuery(this).css({left: left[index_min_top], top: top[index_min_top]});
                    top[index_min_top] += jQuery(this).height();
                  });
                  jQuery(".bwg_masonry_thumbnails_<?php echo $bwg; ?>").width(div_width);
                  jQuery(".bwg_masonry_thumbnails_<?php echo $bwg; ?>").height(Math.max.apply(Math, top));
                  jQuery(".bwg_masonry_thumb_<?php echo $bwg; ?>").css({visibility: 'visible'});
                }
                jQuery(window).load(function() {
                  bwg_masonry_<?php echo $bwg; ?>();
                });
                jQuery(window).resize(function() {
                  bwg_masonry_<?php echo $bwg; ?>();
                });
                  <?php
                }
                ?>
              </script>
            </div>
            <?php
            if ($params['album_enable_page']  && $items_per_page && ($theme_row->page_nav_position == 'bottom') && $page_nav['total']) {
              WDWLibrary::ajax_html_frontend_page_nav($theme_row, $page_nav['total'], $page_nav['limit'], 'gal_front_form_' . $bwg, $items_per_page, $bwg, $album_gallery_div_id, $params['album_id'], $type);
            }
            ?>
          </div>
        </form>
        <div id="spider_popup_loading_<?php echo $bwg; ?>" class="spider_popup_loading"></div>
        <div id="spider_popup_overlay_<?php echo $bwg; ?>" class="spider_popup_overlay" onclick="spider_destroypopup(1000)"></div>
      </div>
    </div>
    <script>
      var bwg_current_url = '<?php echo $current_url; ?>';
      <?php
      if (isset($params_array_hash)) {
      ?>
      var bwg_hash = window.location.hash.substring(1);
      if (bwg_hash && bwg_hash.indexOf("bwg") != "-1") {
        bwg_hash_array = bwg_hash.replace("bwg", "").split("/");
        console.log(bwg_hash_array);
        spider_createpopup('<?php echo addslashes($this->array_to_url($params_array_hash)); ?>&gallery_id=' + bwg_hash_array[0] + '&image_id=' + bwg_hash_array[1], '<?php echo $bwg; ?>', '<?php echo $params['popup_width']; ?>', '<?php echo $params['popup_height']; ?>', 1, 'testpopup', 5);
      }
      <?php
      }
      ?>
    </script>
    <?php


}	
	
	
function album_compact_preview($bwg)
{
		$WD_BWG_DIR=JPATH_BASE.'/components/com_gallery_wd';
$WD_BWG_URL=JURI::root().'components/com_gallery_wd';	

$db		=JFactory::getDBO();
$query="SHOW TABLES LIKE '#__bwg_option'";
$db->setQuery($query);

$params=$this->params;
if ($db->query()) {

$query='SELECT images_directory FROM #__bwg_option WHERE id=1';
$db->setQuery($query);
$WD_BWG_UPLOAD_DIR= $db->loadResult() . '/com_gallery_wd/uploads';
}
else {
 
$WD_BWG_UPLOAD_DIR="administrator/components/com_gallery_wd/uploads";
}

$uri	= JFactory::getURI();
		$current_url=$uri->toString();
$session = JFactory::getSession(); 
$session->set('current_url',$current_url);
    require_once($WD_BWG_DIR . '/framework/WDWLibrary.php');

    if (!isset($params['image_title_show_hover'])) {
      $params['image_title_show_hover'] = 'none';
    }

 
    if (!isset($params['popup_fullscreen'])) {
      $params['popup_fullscreen'] = 0;
    }
    if (!isset($params['popup_autoplay'])) {
      $params['popup_autoplay'] = 0;
    }
    if (!isset($params['popup_enable_pinterest'])) {
      $params['popup_enable_pinterest'] = 0;
    }
    if (!isset($params['popup_enable_tumblr'])) {
      $params['popup_enable_tumblr'] = 0;
    }
    if (!isset($params['show_search_box'])) {
      $params['show_search_box'] = 0;
    }
    if (!isset($params['search_box_width'])) {
      $params['search_box_width'] = 180;
    }
    if (!isset($params['popup_enable_info'])) {
      $params['popup_enable_info'] = 0;
    }
    if (!isset($params['popup_info_always_show'])) {
      $params['popup_info_always_show'] = 0;
    }
    if (!isset($params['popup_enable_rate'])) {
      $params['popup_enable_rate'] = 0;
    }
    if (!isset($params['thumb_click_action']) || $params['thumb_click_action'] == 'undefined') {
      $params['thumb_click_action'] = 'open_lightbox';
    }
    if (!isset($params['thumb_link_target'])) {
      $params['thumb_link_target'] = 1;
    }
    if (!isset($params['popup_hit_counter'])) {
      $params['popup_hit_counter'] = 0;
    }
    if (!isset($params['order_by'])) {
      $params['order_by'] = ' ASC ';
    }
	
	$album_view_type = 'thumbnail';
	$from = (isset($params['from']) ? htmlspecialchars($params['from']) : 0);
    $type = (isset($_POST['type_' . $bwg]) ? htmlspecialchars($_POST['type_' . $bwg]) : (isset($params['type']) ? $params['type'] : 'album'));


    if ($from === "widget") {
      $options_row = $this->get_options_row_data;
      $params['album_id'] = $params['id'];
      $params['sort_by'] = $params['show'] == 'random' ? 'RAND()' : 'order';
      $params['albums_per_page'] = $params['count'];
      $params['album_column_number'] = $options_row->album_column_number;
      $params['album_thumb_height'] = $params['height'];
      $params['album_thumb_width'] = $params['width'];
      $params['album_title_show_hover'] = $options_row->album_title_show_hover;
      $params['album_enable_page'] = 0;  
      $params['image_title_show_hover'] = $options_row->image_title_show_hover;
    }

    $theme_row = $this->get_theme_row_data;
    if (!$theme_row) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_THEME'), 'error');
      return;
    }
    $album_gallery_id = (isset($_POST['album_gallery_id_' . $bwg]) ? htmlspecialchars($_POST['album_gallery_id_' . $bwg]) : $params['album_id']);
    if (!$album_gallery_id) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_ALBUM'), 'error');
      return;
    }
    if ($type == 'gallery') {

      $items_per_page = $params['images_per_page'];
      $items_col_num = $params['image_column_number'];
      $image_rows = $this->get_image_rows_data;
	  $images_count=count($image_rows);
      $page_nav = $this->gallery_page_nav;
      $album_gallery_div_id = 'bwg_album_compact_' . $bwg;
      $album_gallery_div_class = 'bwg_standart_thumbnails_' . $bwg;
    }
    else {
      $items_per_page = $params['albums_per_page'];
      $items_col_num = $params['album_column_number'];
      $album_galleries_row = $this->get_alb_gals_row;
      $page_nav = $this->album_page_nav;
      $album_gallery_div_id = 'bwg_album_compact_' . $bwg;
      $album_gallery_div_class = 'bwg_album_thumbnails_' . $bwg;
    }
    $bwg_previous_album_id = (isset($_POST['bwg_previous_album_id_' . $bwg]) ? htmlspecialchars($_POST['bwg_previous_album_id_' . $bwg]) : 0);
    $bwg_previous_album_page_number = (isset($_POST['bwg_previous_album_page_number_' . $bwg]) ? htmlspecialchars($_POST['bwg_previous_album_page_number_' . $bwg]) : 0);

    $rgb_page_nav_font_color = WDWLibrary::spider_hex2rgb($theme_row->page_nav_font_color);
    $rgb_album_compact_thumbs_bg_color = WDWLibrary::spider_hex2rgb($theme_row->album_compact_thumbs_bg_color);
    $rgb_thumbs_bg_color = WDWLibrary::spider_hex2rgb($theme_row->thumbs_bg_color);
	
	    if ($type == 'gallery' ) {
      if($album_view_type == 'masonry') {
          $form_child_div_style = 'background-color:rgba(0, 0, 0, 0); position:relative; text-align:' . $theme_row->masonry_thumb_align . '; width:100%;';
          $form_child_div_id = 'bwg_masonry_thumbnails_div_' . $bwg;
        $album_gallery_div_id = 'bwg_masonry_thumbnails_' . $bwg;
          $album_gallery_div_class = 'bwg_masonry_thumbnails_' . $bwg;		
      }
      else { 
        $form_child_div_style = 'background-color:rgba(0, 0, 0, 0); position:relative; text-align:' . $theme_row->thumb_align . '; width:100%;';
      $form_child_div_id = '';
      }
    }
    else {
      $form_child_div_id = '';
      $form_child_div_style = 'background-color:rgba(0, 0, 0, 0); position:relative; text-align:' . $theme_row->album_compact_thumb_align . '; width:100%;';
    }
	
	
	
		///////////////////////////////////////////////////////
	$params_array = array(
      'action' => 'GalleryBox',
      'current_view' => $bwg,
      'theme_id' => $params['theme_id'],
      'thumb_width' => $params['thumb_width'],
      'thumb_height' => $params['thumb_height'],
      'open_with_fullscreen' => $params['popup_fullscreen'],
      'open_with_autoplay' => $params['popup_autoplay'],
      'image_width' => $params['popup_width'],
      'image_height' => $params['popup_height'],
      'image_effect' => $params['popup_type'],
      'sort_by' => $params['sort_by'],
      'enable_image_filmstrip' => $params['popup_enable_filmstrip'],
      'image_filmstrip_height' => $params['popup_filmstrip_height'],
      'enable_image_ctrl_btn' => $params['popup_enable_ctrl_btn'],
      'enable_image_fullscreen' => $params['popup_enable_fullscreen'],
      'popup_enable_info' => $params['popup_enable_info'],
      'popup_info_always_show' => $params['popup_info_always_show'],
      'popup_hit_counter' => $params['popup_hit_counter'],
      'popup_enable_rate' => $params['popup_enable_rate'],
      'slideshow_interval' => $params['popup_interval'],
      'enable_comment_social' => $params['popup_enable_comment'],
      'enable_image_facebook' => $params['popup_enable_facebook'],
      'enable_image_twitter' => $params['popup_enable_twitter'],
      'enable_image_google' => $params['popup_enable_google'],
      'enable_image_pinterest' => $params['popup_enable_pinterest'],
      'enable_image_tumblr' => $params['popup_enable_tumblr'],
      'watermark_type' => $params['watermark_type'],
      'current_url' => $this->bgw_url_encode($current_url)
    );

    if ($params['watermark_type'] != 'none') {
      $params_array['watermark_link'] = $params['watermark_link'];
      $params_array['watermark_opacity'] = $params['watermark_opacity'];
      $params_array['watermark_position'] = $params['watermark_position'];
    }
    if ($params['watermark_type'] == 'text') {
      $params_array['watermark_text'] = $params['watermark_text'];
      $params_array['watermark_font_size'] = $params['watermark_font_size'];
      $params_array['watermark_font'] = $params['watermark_font'];
      $params_array['watermark_color'] = $params['watermark_color'];
    }
    elseif ($params['watermark_type'] == 'image') {
      $params_array['watermark_url'] = $params['watermark_url'];
      $params_array['watermark_width'] = $params['watermark_width'];
      $params_array['watermark_height'] = $params['watermark_height'];
    }
    $params_array_hash = $params_array;

	
	
    ?>
   <style>
      /* Style for masonry view.*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumbnails_<?php echo $bwg; ?> * {
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumb_<?php echo $bwg; ?> {
        visibility: hidden;
        text-align: center;
        display: inline-block;
        vertical-align: middle;     
        width: <?php echo $params['thumb_width']; ?>px !important;
        border-radius: <?php echo $theme_row->masonry_thumb_border_radius; ?>;
        border: <?php echo $theme_row->masonry_thumb_border_width; ?>px <?php echo $theme_row->masonry_thumb_border_style; ?> #<?php echo $theme_row->masonry_thumb_border_color; ?>;
        background-color: #<?php echo $theme_row->thumb_bg_color; ?>;
        margin: 0;
        padding: <?php echo $theme_row->masonry_thumb_padding; ?>px !important;
        opacity: <?php echo $theme_row->masonry_thumb_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->masonry_thumb_transparent; ?>);
        <?php echo ($theme_row->masonry_thumb_transition) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
        z-index: 100;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumb_<?php echo $bwg; ?>:hover {
        opacity: 1;
        filter: Alpha(opacity=100);
        transform: <?php echo $theme_row->masonry_thumb_hover_effect; ?>(<?php echo $theme_row->masonry_thumb_hover_effect_value; ?>);
        -ms-transform: <?php echo $theme_row->masonry_thumb_hover_effect; ?>(<?php echo $theme_row->masonry_thumb_hover_effect_value; ?>);
        -webkit-transform: <?php echo $theme_row->masonry_thumb_hover_effect; ?>(<?php echo $theme_row->masonry_thumb_hover_effect_value; ?>);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        -ms-backface-visibility: hidden;
        z-index: 102;
        position: absolute;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumbnails_<?php echo $bwg; ?> {
        -moz-box-sizing: border-box;
        background-color: rgba(<?php echo $rgb_thumbs_bg_color['red']; ?>, <?php echo $rgb_thumbs_bg_color['green']; ?>, <?php echo $rgb_thumbs_bg_color['blue']; ?>, <?php echo $theme_row->masonry_thumb_bg_transparent / 100; ?>);
        box-sizing: border-box;
        display: inline-block;
        font-size: 0;
        /*width: <?php echo $params['image_column_number'] * ($params['thumb_width'] + 2 * ($theme_row->masonry_thumb_padding + $theme_row->masonry_thumb_border_width)); ?>px;*/
        width: 100%;
        position: relative;
        text-align: <?php echo $theme_row->masonry_thumb_align; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumbnails_<?php echo $bwg; ?> a {
        cursor: pointer;
        text-decoration: none;
      }
      @media only screen and (max-width : <?php echo $params['image_column_number'] * ($params['thumb_width'] + 2 * ($theme_row->masonry_thumb_padding + $theme_row->masonry_thumb_border_width)); ?>px) {
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumbnails_<?php echo $bwg; ?> {
          width: inherit;
        }
      }	  	  
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_popup_overlay_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->lightbox_overlay_bg_color; ?>;
        opacity: <?php echo $theme_row->lightbox_overlay_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->lightbox_overlay_bg_transparent; ?>);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_masonry_thumb_spun_<?php echo $bwg; ?> {
        position: absolute;
      }
      /* Style for thumbnail view.*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumb_<?php echo $bwg; ?> {
        display: inline-block;
        text-align: center;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .<?php echo $album_gallery_div_class; ?> * {
        -moz-box-sizing: content-box;
        box-sizing: content-box;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumb_spun1_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->album_compact_thumb_bg_color; ?>;
        display: inline-block;
        height: <?php echo $params['album_thumb_height']; ?>px;
        margin: <?php echo $theme_row->album_compact_thumb_margin; ?>px;
        opacity: <?php echo $theme_row->album_compact_thumb_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->album_compact_thumb_transparent; ?>);
        <?php echo ($theme_row->album_compact_thumb_transition) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
        padding: <?php echo $theme_row->album_compact_thumb_padding; ?>px;
        text-align: center;
        vertical-align: middle;
        width: <?php echo $params['album_thumb_width']; ?>px;
        z-index: 100;
        -webkit-backface-visibility: visible;
        -ms-backface-visibility: visible;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumb_spun1_<?php echo $bwg; ?>:hover {
        opacity: 1;
        filter: Alpha(opacity=100);
        transform: <?php echo $theme_row->album_compact_thumb_hover_effect; ?>(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
        -ms-transform: <?php echo $theme_row->album_compact_thumb_hover_effect; ?>(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
        -webkit-transform: <?php echo $theme_row->album_compact_thumb_hover_effect; ?>(<?php echo $theme_row->album_compact_thumb_hover_effect_value; ?>);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        -ms-backface-visibility: hidden;
        z-index: 102;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumb_spun2_<?php echo $bwg; ?> {
        border-radius: <?php echo $theme_row->album_compact_thumb_border_radius; ?>;
        border: <?php echo $theme_row->album_compact_thumb_border_width; ?>px <?php echo $theme_row->album_compact_thumb_border_style; ?> #<?php echo $theme_row->album_compact_thumb_border_color; ?>;
        box-shadow: <?php echo $theme_row->album_compact_thumb_box_shadow; ?>;
        display: inline-block;
        height: <?php echo $params['album_thumb_height']; ?>px;
        overflow: hidden;
        width: <?php echo $params['album_thumb_width']; ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumbnails_<?php echo $bwg; ?> {
        display: inline-block;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background-color: rgba(<?php echo $rgb_album_compact_thumbs_bg_color['red']; ?>, <?php echo $rgb_album_compact_thumbs_bg_color['green']; ?>, <?php echo $rgb_album_compact_thumbs_bg_color['blue']; ?>, <?php echo $theme_row->album_compact_thumb_bg_transparent / 100; ?>);
        font-size: 0;
        text-align: <?php echo $theme_row->album_compact_thumb_align; ?>;
        max-width: <?php echo $items_col_num * ($params['album_thumb_width'] + 2 * (2 + $theme_row->album_compact_thumb_margin + $theme_row->album_compact_thumb_padding + $theme_row->album_compact_thumb_border_width)); ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumbnails_<?php echo $bwg; ?> a {
        cursor: pointer;
        text-decoration: none;
      }
      <?php
      if ($params['album_title_show_hover'] == 'show') { /* Show album/gallery title at the bottom.*/
        ?>
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_title_spun1_<?php echo $bwg; ?> {
          display: block;
          opacity: 1;
          filter: Alpha(opacity=100);
          text-align: center;
          width: <?php echo $params['album_thumb_width']; ?>px;
        }
        <?php
      }
      elseif ($params['album_title_show_hover'] == 'hover') { /* Show album/gallery title on hover.*/
        ?>
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_title_spun1_<?php echo $bwg; ?> {
          display: table;
          height: inherit;
          left: -3000px;
          opacity: 0;
          filter: Alpha(opacity=0);
          position: absolute;
          top: 0px;
          width: inherit;
        }
        <?php
      }
      ?>
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumb_spun1_<?php echo $bwg; ?>:hover .bwg_title_spun1_<?php echo $bwg; ?> {
        left: <?php echo $theme_row->album_compact_thumb_padding; ?>px;
        top: <?php echo $theme_row->album_compact_thumb_padding; ?>px;
        opacity: 1;
        filter: Alpha(opacity=100);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_title_spun2_<?php echo $bwg; ?> {
        color: #<?php echo $theme_row->album_compact_title_font_color; ?>;
        display: table-cell;
        font-family: <?php echo $theme_row->album_compact_title_font_style; ?>;
        font-size: <?php echo $theme_row->album_compact_title_font_size; ?>px;
        font-weight: <?php echo $theme_row->album_compact_title_font_weight; ?>;
        height: inherit;
        padding: <?php echo $theme_row->album_compact_title_margin; ?>;
        text-shadow: <?php echo $theme_row->album_compact_title_shadow; ?>;
        vertical-align: middle;
        width: inherit;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumbnails_<?php echo $bwg; ?> {
        display: inline-block;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background-color: rgba(<?php echo $rgb_album_compact_thumbs_bg_color['red']; ?>, <?php echo $rgb_album_compact_thumbs_bg_color['green']; ?>, <?php echo $rgb_album_compact_thumbs_bg_color['blue']; ?>, <?php echo $theme_row->album_compact_thumb_bg_transparent / 100; ?>);
        font-size: 0;
        text-align: <?php echo $theme_row->album_compact_thumb_align; ?>;
        max-width: <?php echo $items_col_num * ($params['album_thumb_width'] + 2 * (2 + $theme_row->album_compact_thumb_margin + $theme_row->album_compact_thumb_padding + $theme_row->album_compact_thumb_border_width)); ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_album_thumbnails_<?php echo $bwg; ?> a {
        cursor: pointer;
        text-decoration: none;
      }
      /*Image thumbs styles.*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun1_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->thumb_bg_color; ?>;
        display: inline-block;
        height: <?php echo $params['thumb_height']; ?>px;
        margin: <?php echo $theme_row->thumb_margin; ?>px;
        opacity: <?php echo $theme_row->thumb_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->thumb_transparent; ?>);
        <?php echo ($theme_row->thumb_transition) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
        padding: <?php echo $theme_row->thumb_padding; ?>px;
        text-align: center;
        vertical-align: middle;
        width: <?php echo $params['thumb_width']; ?>px;
        z-index: 100;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun1_<?php echo $bwg; ?>:hover {
        -ms-transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        -webkit-transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        -ms-backface-visibility: hidden;
        opacity: 1;
        filter: Alpha(opacity=100);
        transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        z-index: 102;
        position: relative;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun2_<?php echo $bwg; ?> {
        border-radius: <?php echo $theme_row->thumb_border_radius; ?>;
        border: <?php echo $theme_row->thumb_border_width; ?>px <?php echo $theme_row->thumb_border_style; ?> #<?php echo $theme_row->thumb_border_color; ?>;
        box-shadow: <?php echo $theme_row->thumb_box_shadow; ?>;
        display: inline-block;
        height: <?php echo $params['thumb_height']; ?>px;
        overflow: hidden;
        width: <?php echo $params['thumb_width']; ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumbnails_<?php echo $bwg; ?> {
        -moz-box-sizing: border-box;
        display: inline-block;
        background-color: rgba(<?php echo $rgb_thumbs_bg_color['red']; ?>, <?php echo $rgb_thumbs_bg_color['green']; ?>, <?php echo $rgb_thumbs_bg_color['blue']; ?>, <?php echo $theme_row->thumb_bg_transparent / 100; ?>);
        box-sizing: border-box;
        font-size: 0;
        max-width: <?php echo $params['image_column_number'] * ($params['thumb_width'] + 2 * (2 + $theme_row->thumb_margin + $theme_row->thumb_padding + $theme_row->thumb_border_width)); ?>px;
        text-align: <?php echo $theme_row->thumb_align; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumbnails_<?php echo $bwg; ?> a {
        cursor: pointer;
        text-decoration: none;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_<?php echo $bwg; ?> {
        display: inline-block;
        text-align: center;
      }
      <?php
      if ($params['image_title_show_hover'] == 'show') { /* Show image title at the bottom.*/
        ?>
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_title_spun1_<?php echo $bwg; ?> {
          display: block;
          margin: 0 auto;
          opacity: 1;
          filter: Alpha(opacity=100);
          text-align: center;
          width: <?php echo $params['thumb_width']; ?>px;
        }
        <?php
      }
      elseif ($params['image_title_show_hover'] == 'hover') { /* Show image title on hover.*/
        ?>
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_title_spun1_<?php echo $bwg; ?> {
          display: table;
          height: inherit;
          left: -3000px;
          opacity: 0;
          filter: Alpha(opacity=0);
          position: absolute;
          top: 0px;
          width: inherit;
        }
        <?php
      }
      ?>
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun1_<?php echo $bwg; ?>:hover .bwg_image_title_spun1_<?php echo $bwg; ?> {
        left: <?php echo $theme_row->thumb_padding; ?>px;
        top: <?php echo $theme_row->thumb_padding; ?>px;
        opacity: 1;
        filter: Alpha(opacity=100);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_title_spun2_<?php echo $bwg; ?> {
        color: #<?php echo $theme_row->thumb_title_font_color; ?>;
        display: table-cell;
        font-family: <?php echo $theme_row->thumb_title_font_style; ?>;
        font-size: <?php echo $theme_row->thumb_title_font_size; ?>px;
        font-weight: <?php echo $theme_row->thumb_title_font_weight; ?>;
        height: inherit;
        margin: <?php echo $theme_row->thumb_title_margin; ?>;
        text-shadow: <?php echo $theme_row->thumb_title_shadow; ?>;
        vertical-align: middle;
        width: inherit;
        word-break: break-all;
        word-wrap: break-word;
      }
      /*Pagination styles.*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> {
        text-align: <?php echo $theme_row->page_nav_align; ?>;
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        margin: 6px 0 4px;
        display: block;
        height: 30px;
        line-height: 30px;
      }
      @media only screen and (max-width : 320px) {
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .displaying-num_<?php echo $bwg; ?> {
          display: none;
        }
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .displaying-num_<?php echo $bwg; ?> {
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        margin-right: 10px;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .paging-input_<?php echo $bwg; ?> {
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled:hover,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled:focus {
        cursor: default;
        color: rgba(<?php echo $rgb_page_nav_font_color['red']; ?>, <?php echo $rgb_page_nav_font_color['green']; ?>, <?php echo $rgb_page_nav_font_color['blue']; ?>, 0.5);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a {
        cursor: pointer;
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        text-decoration: none;
        padding: <?php echo $theme_row->page_nav_padding; ?>;
        margin: <?php echo $theme_row->page_nav_margin; ?>;
        border-radius: <?php echo $theme_row->page_nav_border_radius; ?>;
        border-style: <?php echo $theme_row->page_nav_border_style; ?>;
        border-width: <?php echo $theme_row->page_nav_border_width; ?>px;
        border-color: #<?php echo $theme_row->page_nav_border_color; ?>;
        background-color: #<?php echo $theme_row->page_nav_button_bg_color; ?>;
        opacity: <?php echo $theme_row->page_nav_button_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->page_nav_button_bg_transparent; ?>);
        box-shadow: <?php echo $theme_row->page_nav_box_shadow; ?>;
        <?php echo ($theme_row->page_nav_button_transition ) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_back_<?php echo $bwg; ?> {
        background-color: rgba(0, 0, 0, 0);
        color: #<?php echo $theme_row->album_compact_back_font_color; ?> !important;
        cursor: pointer;
        display: block;
        font-family: <?php echo $theme_row->album_compact_back_font_style; ?>;
        font-size: <?php echo $theme_row->album_compact_back_font_size; ?>px;
        font-weight: <?php echo $theme_row->album_compact_back_font_weight; ?>;
        text-decoration: none;
        padding: <?php echo $theme_row->album_compact_back_padding; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_popup_overlay_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->lightbox_overlay_bg_color; ?>;
        opacity: <?php echo $theme_row->lightbox_overlay_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->lightbox_overlay_bg_transparent; ?>);
      }
    </style>

    <div id="bwg_container1_<?php echo $bwg; ?>">
      <div id="bwg_container2_<?php echo $bwg; ?>">
        <form id="gal_front_form_<?php echo $bwg; ?>" method="post" action="#">
          <?php
          if ($params['show_search_box'] && $type == 'gallery') {
            WDWLibrary::ajax_html_frontend_search_box('gal_front_form_' . $bwg, $bwg, $album_gallery_div_id, $images_count, $params['search_box_width']);
          }
          ?>
          <div id="<?php echo $form_child_div_id; ?>" style="<?php echo $form_child_div_style; ?>">
            <div id="ajax_loading_<?php echo $bwg; ?>" style="position:absolute;width: 100%; z-index: 115; text-align: center; height: 100%; vertical-align: middle; display: none;">
              <div style="display: table; vertical-align: middle; width: 100%; height: 100%; background-color: #FFFFFF; opacity: 0.7; filter: Alpha(opacity=70);">
                <div style="display: table-cell; text-align: center; position: relative; vertical-align: middle;" >
                  <div id="loading_div_<?php echo $bwg; ?>" style="display: inline-block; text-align:center; position:relative; vertical-align:middle;">
                    <img src="<?php echo $WD_BWG_URL . '/images/ajax_loader.png'; ?>" class="spider_ajax_loading" style="float: none; width:50px;">
                  </div>
                </div>
              </div>
            </div>
            <?php
            if ($params['album_enable_page'] && $items_per_page && ($theme_row->page_nav_position == 'top') && $page_nav['total']) {
              WDWLibrary::ajax_html_frontend_page_nav($theme_row, $page_nav['total'], $page_nav['limit'], 'gal_front_form_' . $bwg, $items_per_page, $bwg, $album_gallery_div_id, $params['album_id'], $type);
            }
            if ($bwg_previous_album_id) {
              ?>
              <a class="bwg_back_<?php echo $bwg; ?>" onclick="spider_frontend_ajax('gal_front_form_<?php echo $bwg; ?>', '<?php echo $bwg; ?>', '<?php echo $album_gallery_div_id; ?>', 'back')"><?php echo JText::_('BACK'); ?></a>
              <?php
            }
            ?>
            <div id="<?php echo $album_gallery_div_id; ?>" class="<?php echo $album_gallery_div_class; ?>" >
              <input type="hidden" id="bwg_previous_album_id_<?php echo $bwg; ?>" name="bwg_previous_album_id_<?php echo $bwg; ?>" value="<?php echo $bwg_previous_album_id; ?>" />
              <input type="hidden" id="bwg_previous_album_page_number_<?php echo $bwg; ?>" name="bwg_previous_album_page_number_<?php echo $bwg; ?>" value="<?php echo $bwg_previous_album_page_number; ?>" />
              <?php
              if ($type != 'gallery') {
                if (!$page_nav['total']) {
                  ?>
                  <span class="bwg_back_<?php echo $bwg; ?>"><?php echo JText::_('EMPTY_ALBUM'); ?></span>
                  <?php
                }
                foreach ($album_galleries_row as $album_galallery_row) {
                  if ($album_galallery_row->is_album) {
                    $album_row =$this->get_album_row_data($album_galallery_row->alb_gal_id);
                    if (!$album_row) {
                      continue;
                    }
                    $preview_image = $album_row->preview_image;
                    if (!$preview_image) {
                      $preview_image = $album_row->random_preview_image;
                    }
                    $def_type = 'album';
                    $title = $album_row->name;
                    $permalink ='';// $album_row->permalink;
                  }
                  else {
                    $gallery_row = $this->get_gallery_row_data($album_galallery_row->alb_gal_id);
                    if (!$gallery_row) {
                      continue;
                    }
                    $preview_image = $gallery_row->preview_image;
                    if (!$preview_image) {
                      $preview_image = $gallery_row->random_preview_image;
                    }
                    $def_type = 'gallery';
                    $title = $gallery_row->name;
                $permalink ='';// $gallery_row->permalink;
                  }
                  if (!$preview_image) {
                    $preview_url = $WD_BWG_URL . '/images/no-image.png';
                    $preview_path = $WD_BWG_DIR . '/images/no-image.png';
                  }
                  else {
                    $preview_url = JURI::root().$WD_BWG_UPLOAD_DIR.'/'. $preview_image;
                    $preview_path = JPATH_SITE.'/' . $WD_BWG_UPLOAD_DIR.'/' . $preview_image;
                  }
				  				  $preview_url=htmlspecialchars($preview_url);
				  $preview_path=htmlspecialchars($preview_path);

                  list($image_thumb_width, $image_thumb_height) = getimagesize(htmlspecialchars_decode($preview_path, ENT_COMPAT | ENT_QUOTES));
                  $scale = max($params['thumb_width'] / $image_thumb_width, $params['thumb_height'] / $image_thumb_height);
                  $image_thumb_width *= $scale;
                  $image_thumb_height *= $scale;
                  $thumb_left = ($params['thumb_width'] - $image_thumb_width) / 2;
                  $thumb_top = ($params['thumb_height'] - $image_thumb_height) / 2;
                  if ($type != 'gallery') {
                    ?>
                    <a style="font-size: 0;" <?php echo ($from !== "widget" ? "onclick=\"spider_frontend_ajax('gal_front_form_" . $bwg . "', '" . $bwg . "', 'bwg_album_compact_" . $bwg . "', '" . $album_galallery_row->alb_gal_id . "', '" . $album_gallery_id . "', '" . $def_type . "')\"" : "href='" . $permalink . "'") ?>>
                      <span class="bwg_album_thumb_<?php echo $bwg; ?>">
                        <?php
                        if ($params['album_title_show_hover'] == 'show' && $theme_row->album_compact_thumb_title_pos == 'top') {
                          ?>
                          <span class="bwg_title_spun1_<?php echo $bwg; ?>">
                            <span class="bwg_title_spun2_<?php echo $bwg; ?>">
                              <?php echo $title; ?>
                            </span>
                          </span>
                          <?php
                        }
                        ?>
                        <span class="bwg_album_thumb_spun1_<?php echo $bwg; ?>">
                          <span class="bwg_album_thumb_spun2_<?php echo $bwg; ?>">
                            <img style="padding: 0 !important; max-height: none !important; max-width: none !important; width: <?php echo $image_thumb_width; ?>px; height:<?php echo $image_thumb_height; ?>px; margin-left: <?php echo $thumb_left; ?>px; margin-top: <?php echo $thumb_top; ?>px;" src="<?php echo $preview_url; ?>" alt="<?php echo $title; ?>" />
                            <?php
                            if ($params['album_title_show_hover'] == 'hover') {
                              ?>
                              <span class="bwg_title_spun1_<?php echo $bwg; ?>">
                                <span class="bwg_title_spun2_<?php echo $bwg; ?>">
                                  <?php echo $title; ?>
                                </span>
                              </span>
                              <?php
                            }
                            ?>
                          </span>
                        </span>
                        <?php
                        if ($params['album_title_show_hover'] == 'show' && $theme_row->album_compact_thumb_title_pos == 'bottom') {
                          ?>
                          <span class="bwg_title_spun1_<?php echo $bwg; ?>">
                            <span class="bwg_title_spun2_<?php echo $bwg; ?>">
                              <?php echo $title; ?>
                            </span>
                          </span>
                          <?php
                        }
                        ?>
                      </span>
                    </a>
                    <?php
                  }
                }
              }
              elseif ($type == 'gallery') {
                if (!$page_nav['total']) {
                  if ($bwg_search != '') {
                    ?>
                    <span class="bwg_back_<?php echo $bwg; ?>"><?php echo JText::_('THERE_IS_NO_SEARCH'); ?></span>
                    <?php
                  }
                  else {
                    ?>
                    <span class="bwg_back_<?php echo $bwg; ?>"><?php echo JText::_('EMPTY_GALLERY'); ?></span>
                    <?php
                  }
                }
                
                foreach ($image_rows as $image_row) {
					  		$image_row->thumb_url=htmlspecialchars($image_row->thumb_url);
			  $image_row->image_url=htmlspecialchars($image_row->image_url);
			  $image_row->alt=htmlspecialchars($image_row->alt);
			  $image_row->filename=htmlspecialchars($image_row->filename);
 
                  $params_array['image_id'] = (isset($_POST['image_id']) ? esc_html($_POST['image_id']) : $image_row->id);
                  $params_array['gallery_id'] = $album_gallery_id;
                  $is_video = $image_row->filetype == "YOUTUBE" || $image_row->filetype == "VIMEO";
                  if (!$is_video) {
                    list($image_thumb_width, $image_thumb_height) = getimagesize(htmlspecialchars_decode(JPATH_SITE.'/' . $WD_BWG_UPLOAD_DIR.'/' . $image_row->thumb_url, ENT_COMPAT | ENT_QUOTES));
                  }
                  else {
                    $image_thumb_width = $params['thumb_width'];
                    $image_thumb_height = $params['thumb_height'];
                  }
                  $scale = max($params['thumb_width'] / $image_thumb_width, $params['thumb_height'] / $image_thumb_height);
                  $image_thumb_width *= $scale;
                  $image_thumb_height *= $scale;
                  $thumb_left = ($params['thumb_width'] - $image_thumb_width) / 2;
                  $thumb_top = ($params['thumb_height'] - $image_thumb_height) / 2;
                  if ($album_view_type == 'thumbnail') {
                    ?>
                  <a style="font-size: 0;" <?php echo ($params['thumb_click_action'] == 'open_lightbox' ? ('onclick="spider_createpopup(\'' . addslashes($this->array_to_url($params_array)) . '\', ' . $bwg . ', ' . $params['popup_width'] . ', ' . $params['popup_height'] . ', 1, \'testpopup\', 5); return false;"') : ('href="' . $image_row->redirect_url . '" target="' .  ($params['thumb_link_target'] ? '_blank' : '')  . '"')) ?>>
                    <span class="bwg_standart_thumb_<?php echo $bwg; ?>">
                      <?php
                      if ($params['image_title_show_hover'] == 'show' && $theme_row->album_compact_thumb_title_pos == 'top') {
                        ?>
                        <span class="bwg_image_title_spun1_<?php echo $bwg; ?>">
                          <span class="bwg_image_title_spun2_<?php echo $bwg; ?>">
                            <?php echo $image_row->alt; ?>
                          </span>
                        </span>
                        <?php
                      }
                      ?>
                      <span class="bwg_standart_thumb_spun1_<?php echo $bwg; ?>">
                        <span class="bwg_standart_thumb_spun2_<?php echo $bwg; ?>">
                          <img style="max-height:none; max-width:none; width:<?php echo $image_thumb_width; ?>px; height:<?php echo $image_thumb_height; ?>px; margin-left: <?php echo $thumb_left; ?>px; margin-top: <?php echo $thumb_top; ?>px;" id="<?php echo $image_row->id; ?>" src="<?php echo ($is_video ? "" : JURI::root().$WD_BWG_UPLOAD_DIR.'/') . $image_row->thumb_url; ?>" alt="<?php echo $image_row->alt; ?>" />
                          <?php
                          if ($params['image_title_show_hover'] == 'hover') {
                            ?>
                            <span class="bwg_image_title_spun1_<?php echo $bwg; ?>">
                              <span class="bwg_image_title_spun2_<?php echo $bwg; ?>">
                                <?php echo $image_row->alt; ?>
                              </span>
                            </span>
                            <?php
                          }
                          ?>
                        </span>
                      </span>
                      <?php
                      if ($params['image_title_show_hover'] == 'show' && $theme_row->album_compact_thumb_title_pos == 'bottom') {
                        ?>
                        <span class="bwg_image_title_spun1_<?php echo $bwg; ?>">
                          <span class="bwg_image_title_spun2_<?php echo $bwg; ?>">
                            <?php echo $image_row->alt; ?>
                          </span>
                        </span>
                        <?php
                      }
                      ?>
                    </span>
                  </a>
                    <?php
                  }
                  else {
                    ?>
                  <span class="bwg_masonry_thumb_spun_<?php echo $bwg; ?>">
                    <a style="font-size: 0;" <?php echo ($params['thumb_click_action'] == 'open_lightbox' ? ('onclick="spider_createpopup(\'' . addslashes($this->array_to_url($params_array)) . '\', ' . $bwg . ', ' . $params['popup_width'] . ', ' . $params['popup_height'] . ', 1, \'testpopup\', 5); return false;"') : ('href="' . $image_row->redirect_url . '" target="' .  ($params['thumb_link_target'] ? '_blank' : '')  . '"')) ?>>
                      <img class="bwg_masonry_thumb_<?php echo $bwg; ?>" id="<?php echo $image_row->id; ?>" src="<?php echo ($is_video ? "" : JURI::root() . $WD_BWG_UPLOAD_DIR.'/') . $image_row->thumb_url; ?>" alt="<?php echo $image_row->alt; ?>" style="max-height: none !important;  max-width: none !important;" />
                    </a>
                  </span>
                    <?php
                  }
                }
              }
              ?>
            </div>
            <?php 
            if ($album_view_type == 'masonry' && $type == 'gallery' ) {
              ?>
              <script>
                function bwg_masonry_<?php echo $bwg; ?>() { 
                  var image_width = <?php echo $params['thumb_width']; ?>;
                  var cont_div_width = <?php echo $params['image_column_number'] * ($params['thumb_width'] + 2 * ($theme_row->thumb_padding + $theme_row->thumb_border_width)); ?>;
                  if (cont_div_width > jQuery("#bwg_masonry_thumbnails_div_<?php echo $bwg; ?>").width()) {
                    cont_div_width = jQuery("#bwg_masonry_thumbnails_div_<?php echo $bwg; ?>").width();
                  }
                  var col_count = parseInt(cont_div_width / image_width);
                  if (!col_count) {
                    col_count = 1;
                  }
                  var top = new Array();
                  var left = new Array();
                  for (var i = 0; i < col_count; i++) {
                    top[i] = 0;
                    left[i] = i * image_width;
                  }
                  var div_width = col_count * image_width;
                  if (div_width > jQuery(window).width()) {
                    div_width = jQuery(window).width();
                    jQuery(".bwg_masonry_thumb_<?php echo $bwg; ?>").attr("style", "max-width: " + div_width + "px");
                  }
                  else {
                    div_width = col_count * image_width;
                  }
                  jQuery(".bwg_masonry_thumb_spun_<?php echo $bwg; ?>").each(function() {
                    min_top = Math.min.apply(Math, top);
                    index_min_top = jQuery.inArray(min_top, top);
                    jQuery(this).css({left: left[index_min_top], top: top[index_min_top]});
                    top[index_min_top] += jQuery(this).height();
                  });
                  jQuery(".bwg_masonry_thumbnails_<?php echo $bwg; ?>").width(div_width);
                  jQuery(".bwg_masonry_thumbnails_<?php echo $bwg; ?>").height(Math.max.apply(Math, top));
                  jQuery(".bwg_masonry_thumb_<?php echo $bwg; ?>").css({visibility: 'visible'});
                }
                jQuery(window).load(function() {
                  bwg_masonry_<?php echo $bwg; ?>();
                });
                jQuery(window).resize(function() {
                  bwg_masonry_<?php echo $bwg; ?>();
                });
              </script>
              <?php
            }
            if ($params['album_enable_page'] && $items_per_page && ($theme_row->page_nav_position == 'bottom') && $page_nav['total']) {
              WDWLibrary::ajax_html_frontend_page_nav($theme_row, $page_nav['total'], $page_nav['limit'], 'gal_front_form_' . $bwg, $items_per_page, $bwg, $album_gallery_div_id, $params['album_id'], $type);
            }
            ?>
          </div>
        </form>
        <div id="spider_popup_loading_<?php echo $bwg; ?>" class="spider_popup_loading"></div>
        <div id="spider_popup_overlay_<?php echo $bwg; ?>" class="spider_popup_overlay" onclick="spider_destroypopup(1000)"></div>
      </div>
    </div>
    <script>
      var bwg_current_url = '<?php echo $current_url; ?>';
      <?php
      if (isset($params_array_hash)) {
      ?>
      var bwg_hash = window.location.hash.substring(1);
      if (bwg_hash && bwg_hash.indexOf("bwg") != "-1") {
        bwg_hash_array = bwg_hash.replace("bwg", "").split("/");
        spider_createpopup('<?php echo addslashes($this->array_to_url($params_array_hash)); ?>&gallery_id=' + bwg_hash_array[0] + '&image_id=' + bwg_hash_array[1], '<?php echo $bwg; ?>', '<?php echo $params['popup_width']; ?>', '<?php echo $params['popup_height']; ?>', 1, 'testpopup', 5);
      }
      <?php
      }
      ?>
    </script>
    <?php

}	
	
	
	
	function image_browser($bwg)
	{
		$WD_BWG_DIR=JPATH_BASE.'/components/com_gallery_wd';
$WD_BWG_URL=JURI::root().'components/com_gallery_wd';	

$db		=JFactory::getDBO();
$query="SHOW TABLES LIKE '#__bwg_option'";
$db->setQuery($query);


if ($db->query()) {

$query='SELECT images_directory FROM #__bwg_option WHERE id=1';
$db->setQuery($query);
$WD_BWG_UPLOAD_DIR= $db->loadResult() . '/com_gallery_wd/uploads';
}
else {
 
$WD_BWG_UPLOAD_DIR="administrator/components/com_gallery_wd/uploads";
}


require_once($WD_BWG_DIR . '/framework/WDWLibrary.php');

$params=$this->params;

$uri	= JFactory::getURI();
		$current_url=$uri->toString();
$session = JFactory::getSession();
$session->set('current_url',$current_url);

    $theme_row = $this->get_theme_row_data;
	
if (!isset($params['order_by'])) {
      $order_by = 'asc';
    }
    else {
      $order_by = $params['order_by'];
    }
    if (!isset($params['popup_enable_pinterest'])) {
      $params['popup_enable_pinterest'] = 0;
    }
    if (!isset($params['popup_enable_tumblr'])) {
      $params['popup_enable_tumblr'] = 0;
    }
    if (!isset($params['show_search_box'])) {
      $params['show_search_box'] = 0;
    }
    if (!isset($params['search_box_width'])) {
      $params['search_box_width'] = 180;
    }
    if (!isset($params['popup_enable_info'])) {
      $params['popup_enable_info'] = 1;
    }
    if (!isset($params['popup_info_always_show'])) {
      $params['popup_info_always_show'] = 0;
    }
    if (!isset($params['popup_enable_rate'])) {
      $params['popup_enable_rate'] = 0;
    }
    if (!isset($params['thumb_click_action']) || $params['thumb_click_action'] == 'undefined') {
      $params['thumb_click_action'] = 'open_lightbox';
    }
    if (!isset($params['thumb_link_target'])) {
      $params['thumb_link_target'] = 1;
    }
    if (!isset($params['popup_hit_counter'])) {
      $params['popup_hit_counter'] = 0;
    }
	
	    if (!isset($params['popup_fullscreen'])) {
      $params['popup_fullscreen'] = 0;
    }
	
    if (!isset($params['popup_autoplay'])) {
      $params['popup_autoplay'] = 0;
    }
	
	
	
	
    if (!$theme_row) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_THEME'), 'error');
      return;
    }
    $gallery_row = $this->get_gallery_row_data;
    if (!$gallery_row) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_GALLERY'), 'error');
      return;
    }
    $image_rows = $this->get_image_rows_data;
	    $images_count = count($image_rows); 
    if (!$image_rows) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_IMAGE'), 'error');
    }
    $page_nav = $this->page_nav;
    $rgb_page_nav_font_color = WDWLibrary::spider_hex2rgb($theme_row->page_nav_font_color);
    $image_browser_images_conteiner = WDWLibrary::spider_hex2rgb($theme_row->image_browser_full_bg_color);
    $bwg_image_browser_image = WDWLibrary::spider_hex2rgb($theme_row->image_browser_bg_color);
    $image_title = $params['image_browser_title_enable'];
    $enable_image_description = $params['image_browser_description_enable'];	
     $params_array = array(
      'view' => 'GalleryBox',
      'current_view' => $bwg,
      'gallery_id' => $params['gallery_id'],
      'theme_id' => $params['theme_id'],
      'open_with_fullscreen' => $params['popup_fullscreen'],
      'open_with_autoplay' => $params['popup_autoplay'],
      'image_width' => $params['popup_width'],
      'image_height' => $params['popup_height'],
      'image_effect' => $params['popup_type'],
      'sort_by' => $params['sort_by'],
      'order_by' => $order_by,
      'enable_image_filmstrip' => $params['popup_enable_filmstrip'],
      'image_filmstrip_height' => $params['popup_filmstrip_height'],
      'enable_image_ctrl_btn' => $params['popup_enable_ctrl_btn'],
      'enable_image_fullscreen' => $params['popup_enable_fullscreen'],
      'popup_enable_info' => $params['popup_enable_info'],
      'popup_info_always_show' => $params['popup_info_always_show'],
      'popup_hit_counter' => $params['popup_hit_counter'],
      'popup_enable_rate' => $params['popup_enable_rate'],
      'slideshow_interval' => $params['popup_interval'],
      'enable_comment_social' => $params['popup_enable_comment'],
      'enable_image_facebook' => $params['popup_enable_facebook'],
      'enable_image_twitter' => $params['popup_enable_twitter'],
      'enable_image_google' => $params['popup_enable_google'],
      'enable_image_pinterest' => $params['popup_enable_pinterest'],
      'enable_image_tumblr' => $params['popup_enable_tumblr'],
      'watermark_type' => $params['watermark_type'],
      'current_url' => $this->bgw_url_encode($current_url)
    );	
    if ($params['watermark_type'] == 'none') {
      $show_watermark = FALSE;
    }
    if ($params['watermark_type'] != 'none') {
      $params_array['watermark_link'] = $params['watermark_link'];
      $params_array['watermark_opacity'] = $params['watermark_opacity'];
      $params_array['watermark_position'] =(($params['watermark_position'] != 'undefined') ? $params['watermark_position'] : 'top-center');
			$position = explode('-', $params_array['watermark_position']);
			$vertical_align = $position[0];
			$text_align = $position[1];
    }
    if ($params['watermark_type'] == 'text') {
      $show_watermark = TRUE;
      $watermark_text_image = TRUE;
      $params_array['watermark_text'] = $params['watermark_text'];
      $params_array['watermark_font_size'] = $params['watermark_font_size'];
      $params_array['watermark_font'] = $params['watermark_font'];
      $params_array['watermark_color'] = $params['watermark_color'];
			$params_array['watermark_width'] = '';
			$watermark_image_or_text = $params_array['watermark_text'];
			$watermark_a = 'bwg_watermark_text_' . $bwg;
			$watermark_div = 'class="bwg_image_browser_watermark_text_' . $bwg . '"';
    }
    elseif ($params['watermark_type'] == 'image') {
      $show_watermark = TRUE;
      $watermark_text_image = FALSE;
      $params_array['watermark_url'] = $params['watermark_url'];
      $params_array['watermark_width'] = $params['watermark_width'];
      $params_array['watermark_height'] = $params['watermark_height'];
			$watermark_image_or_text = '<img class="bwg_image_browser_watermark_img_' . $bwg . '" src="' . $params_array['watermark_url'] . '" />';
			$watermark_a = '';
			$watermark_div = 'class="bwg_image_browser_watermark_' . $bwg . '"';
    }
	
	    $params_array_hash = $params_array;
    ?>
        <style>
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .image_browser_images_conteiner_<?php echo $bwg; ?> * {
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .image_browser_images_conteiner_<?php echo $bwg; ?>{
				background-color: rgba(<?php echo $image_browser_images_conteiner['red']; ?>, <?php echo $image_browser_images_conteiner['green']; ?>, <?php echo $image_browser_images_conteiner['blue']; ?>, <?php echo $theme_row->image_browser_full_transparent / 100; ?>);
				text-align: center;
				width: 100%;
				border-style: <?php echo $theme_row->image_browser_full_border_style;?>;
				border-width: <?php echo $theme_row->image_browser_full_border_width;?>px;
				border-color: #<?php echo $theme_row->image_browser_full_border_color;?>;
				padding: <?php echo $theme_row->image_browser_full_padding; ?>;
				border-radius: <?php echo $theme_row->image_browser_full_border_radius; ?>;
				position:relative;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .image_browser_images_<?php echo $bwg; ?> {
				display: inline-block;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
				font-size: 0;
				text-align: center;
				max-width: 100%;
				width: <?php echo $params['image_browser_width']; ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .image_browser_image_buttons_conteiner_<?php echo $bwg; ?> {
				text-align: <?php echo $theme_row->image_browser_align; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .image_browser_image_buttons_<?php echo $bwg; ?> {
				display: inline-block;
				width:100%;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_image_<?php echo $bwg; ?> {
        background-color: rgba(<?php echo $bwg_image_browser_image['red']; ?>, <?php echo $bwg_image_browser_image['green']; ?>, <?php echo $bwg_image_browser_image['blue']; ?>, <?php echo $theme_row->image_browser_transparent / 100; ?>);
				text-align: center;
				/*display: inline-block;*/
				vertical-align: middle;
				margin: <?php echo $theme_row->image_browser_margin; ?>;
				padding: <?php echo $theme_row->image_browser_padding; ?>;
				border-radius: <?php echo $theme_row->image_browser_border_radius; ?>;
				border: <?php echo $theme_row->image_browser_border_width; ?>px <?php echo $theme_row->image_browser_border_style; ?> #<?php echo $theme_row->image_browser_border_color; ?>;
				box-shadow: <?php echo $theme_row->image_browser_box_shadow; ?>;
				/*z-index: 100;*/
				position: relative;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_alt_<?php echo $bwg; ?>{
				display: table;
				width: 100%;
				font-size: <?php echo $theme_row->image_browser_img_font_size; ?>px;
				font-family: <?php echo $theme_row->image_browser_img_font_family; ?>;
				color: #<?php echo $theme_row->image_browser_img_font_color; ?>;
				text-align:<?php echo $theme_row->image_browser_image_description_align; ?>;
				padding-left: 8px;
        word-break: break-word;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_img_<?php echo $bwg; ?> {
        padding: 0 !important;
				max-width: 100% !important;
				height: inherit !important;
				width: 100%;				
      }
      @media only screen and (max-width : 320px) {
				#bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .displaying-num_<?php echo $bwg; ?> {
				  display: none;
				}
				#bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_alt_<?php echo $bwg; ?> {
				  font-size: 10px !important;
				}
				#bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_watermark_text_<?php echo $bwg; ?>,
				#bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_watermark_text_<?php echo $bwg; ?>:hover {
				  font-size: 10px !important;
				  text-decoration: none;
				  margin: 4px;
				  font-family: <?php echo $params_array['watermark_font']; ?>;
				  color: #<?php echo $params_array['watermark_color']; ?> !important;
				  opacity: <?php echo $params_array['watermark_opacity'] / 100; ?>;
			  	filter: Alpha(opacity=<?php echo $params_array['watermark_opacity']; ?>);
          text-decoration: none;
				  position: relative;
				  z-index: 10141;
				}
				#bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_image_description_<?php echo $bwg; ?> {
          color: #<?php echo $theme_row->image_browser_img_font_color; ?>;
				  display: table;
				  width: 100%;
				  text-align: left;
				  font-size: 8px !important;
				  font-family: <?php echo $theme_row->image_browser_img_font_family; ?>;
				  padding: <?php echo $theme_row->image_browser_image_description_padding; ?>;
				  /*word-break: break-all;*/
				  border-style: <?php echo $theme_row->image_browser_image_description_border_style; ?>;
				  background-color: #<?php echo $theme_row->image_browser_image_description_bg_color; ?>;
				  border-radius: <?php echo $theme_row->image_browser_image_description_border_radius; ?>;
				  border-width: <?php echo $theme_row->image_browser_image_description_border_width; ?>px;
				}
				#bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a {
				  font-size: 10px !important;
				}				
      }
      /*pagination styles*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> {
				text-align: <?php echo $theme_row->page_nav_align; ?>;
				font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
				font-family: <?php echo $theme_row->page_nav_font_style; ?>;
				font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
				color: #<?php echo $theme_row->page_nav_font_color; ?>;
				margin: 6px 0 4px;
				display: block;
				height: 30px;
				line-height: 30px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .displaying-num_<?php echo $bwg; ?> {
				font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
				font-family: <?php echo $theme_row->page_nav_font_style; ?>;
				font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
				color: #<?php echo $theme_row->page_nav_font_color; ?>;
				margin-right: 10px;
				vertical-align: middle;
				display: none;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .paging-input_<?php echo $bwg; ?> {
				font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
				font-family: <?php echo $theme_row->page_nav_font_style; ?>;
				font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
				color: #<?php echo $theme_row->page_nav_font_color; ?>;
				vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled:hover,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled:focus {
				cursor: default;
				color: rgba(<?php echo $rgb_page_nav_font_color['red']; ?>, <?php echo $rgb_page_nav_font_color['green']; ?>, <?php echo $rgb_page_nav_font_color['blue']; ?>, 0.5);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.next-page:hover,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.prev-page:hover {
        color: #000000;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a {
				cursor: pointer;
				font-size: 15px;
				font-family: <?php echo $theme_row->page_nav_font_style; ?>;
				font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
				color: #<?php echo $theme_row->page_nav_font_color; ?>;
				text-decoration: none;
				padding: 0% 7%;
				margin: <?php echo $theme_row->page_nav_margin; ?>;
				border-radius: <?php echo $theme_row->page_nav_border_radius; ?>;
				border-style: none;
				border-width: <?php echo $theme_row->page_nav_border_width; ?>px;
				border-color: #<?php echo $theme_row->page_nav_border_color; ?>;
				background-color: #<?php echo $theme_row->page_nav_button_bg_color; ?>;
				opacity: <?php echo $theme_row->page_nav_button_bg_transparent / 100; ?>;
				filter: Alpha(opacity=<?php echo $theme_row->page_nav_button_bg_transparent; ?>);
				<?php echo ($theme_row->page_nav_button_transition ) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> .first-page,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> .last-page {
        padding: 0% 2%; 		        
      }
	    #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> .next-page {
        margin: 0% 4% 0% 0%; 		        
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> .prev-page {
        margin: 0% 0% 0% 4%; 		        
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_popup_overlay_<?php echo $bwg; ?> {
				background-color: #<?php echo $theme_row->lightbox_overlay_bg_color; ?>;
        opacity: <?php echo $theme_row->lightbox_overlay_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->lightbox_overlay_bg_transparent; ?>);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_image_desp_<?php echo $bwg; ?> {
				display: table;
				clear: both;
				text-align: center;
        padding: <?php echo $theme_row->image_browser_image_description_margin; ?>;
				width: 100%;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_image_description_<?php echo $bwg; ?> {
        color: #<?php echo $theme_row->image_browser_img_font_color; ?>;
				display: table;
				width: 100%;
				text-align: left;
				font-size: <?php echo $theme_row->image_browser_img_font_size; ?>px;
				font-family: <?php echo $theme_row->image_browser_img_font_family; ?>;
				padding: <?php echo $theme_row->image_browser_image_description_padding; ?>;
				word-break: break-word;
				border-style: <?php echo $theme_row->image_browser_image_description_border_style; ?>;
				background-color: #<?php echo $theme_row->image_browser_image_description_bg_color; ?>;
				border-radius: <?php echo $theme_row->image_browser_image_description_border_radius; ?>;
				border-width: <?php echo $theme_row->image_browser_image_description_border_width; ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_image_alt_<?php echo $bwg; ?> {
      	display:table;
        clear: both;
        text-align: center;
        padding: 8px;
        width: 100%;
      }
      /*watermark*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_watermark_text_<?php echo $bwg; ?>,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_watermark_text_<?php echo $bwg; ?>:hover {
				text-decoration: none;
				margin: 4px;
				font-size: <?php echo $params_array['watermark_font_size']; ?>px;
				font-family: <?php echo $params_array['watermark_font']; ?>;
				color: #<?php echo $params_array['watermark_color']; ?> !important;
				opacity: <?php echo $params_array['watermark_opacity'] / 100; ?>;
				filter: Alpha(opacity=<?php echo $params_array['watermark_opacity']; ?>);
				position: relative;
				z-index: 10141;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_image_contain_<?php echo $bwg; ?>{
				position: absolute;
				text-align: center;
				vertical-align: middle;
				width: 100%;
				height: 100%;
				cursor: pointer;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_watermark_contain_<?php echo $bwg; ?>{
        display: table;
				vertical-align: middle;
				width: 100%;
				height: 100%;
      }	 
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_watermark_cont_<?php echo $bwg; ?>{
        display: table-cell;
				text-align: <?php echo $text_align; ?>;
				position: relative;
				vertical-align: <?php echo $vertical_align; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_watermark_<?php echo $bwg; ?>{
				display: inline-block;
				overflow: hidden;
				position: relative;
				vertical-align: middle;
				z-index: 10140;
				width: <?php echo $params_array['watermark_width'];?>px;
				max-width: <?php echo (($params_array['watermark_width']) / ($params['image_browser_width'])) * 100 ; ?>%;
				margin: 10px 10px 10px 10px ;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_watermark_text_<?php echo $bwg; ?>{
        display: inline-block;
				overflow: hidden;
				position: relative;
				vertical-align: middle;
				z-index: 10140;
				margin: 10px 10px 10px 10px ;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_image_browser_watermark_img_<?php echo $bwg; ?>{
				max-width: 100%;
				opacity: <?php echo $params_array['watermark_opacity'] / 100; ?>;
				filter: Alpha(opacity=<?php echo $params_array['watermark_opacity']; ?>);
				position: relative;
				z-index: 10141;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_none_selectable {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }
    </style>
   <div id="bwg_container1_<?php echo $bwg; ?>">
      <div id="bwg_container2_<?php echo $bwg; ?>">
        <form id="gal_front_form_<?php echo $bwg; ?>" method="post" action="#">
          <?php
          if ($params['show_search_box']) {
            WDWLibrary::ajax_html_frontend_search_box('gal_front_form_' . $bwg, $bwg, 'bwg_standart_thumbnails_' . $bwg, $images_count, $params['search_box_width']);
          }
          ?>
          <div class="image_browser_images_conteiner_<?php echo $bwg; ?>">
            <div id="ajax_loading_<?php echo $bwg; ?>" style="position:absolute;width: 100%; z-index: 115; text-align: center; height: 100%; vertical-align: middle; display:none;">
              <div style="display: table; vertical-align: middle; width: 100%; height: 100%; background-color: #FFFFFF; opacity: 0.7; filter: Alpha(opacity=70);">
                <div style="display: table-cell; text-align: center; position: relative; vertical-align: middle;" >
                  <div id="loading_div_<?php echo $bwg; ?>" style="display: inline-block; text-align:center; position: relative; vertical-align: middle;">
                    <img src="<?php echo $WD_BWG_URL . '/images/ajax_loader.png'; ?>" class="spider_ajax_loading" style="float: none; width:50px;">
                  </div>
                </div>
              </div>
            </div>
            <div class="image_browser_images_<?php echo $bwg; ?>" id="bwg_standart_thumbnails_<?php echo $bwg; ?>" >
              <?php
              if ( $theme_row->page_nav_position == 'top') {
                WDWLibrary::ajax_html_frontend_page_nav($theme_row, $page_nav['total'], $page_nav['limit'], 'gal_front_form_' . $bwg,1, $bwg, 'bwg_standart_thumbnails_' . $bwg);
              }
              foreach ($image_rows as $image_row) {
			  	  		$image_row->thumb_url=htmlspecialchars($image_row->thumb_url);
			  $image_row->image_url=htmlspecialchars($image_row->image_url);
			  $image_row->alt=htmlspecialchars($image_row->alt);
			  $image_row->filename=htmlspecialchars($image_row->filename);
 
                $params_array['image_id'] = (isset($_POST['image_id']) ? esc_html($_POST['image_id']) : $image_row->id);
                $popup_url = $this->array_to_url(array($params_array));
                $is_video = $image_row->filetype == "YOUTUBE" || $image_row->filetype == "VIMEO";
                ?>  
                <div class="image_browser_image_buttons_conteiner_<?php echo $bwg; ?>">
                  <div class="image_browser_image_buttons_<?php echo $bwg;?>">
                    <div class="bwg_image_browser_image_alt_<?php echo $bwg; ?>">
                      <?php
                      if ($image_title) {
                        ?>
                        <div class="bwg_image_alt_<?php echo $bwg; ?>" id="alt<?php echo $image_row->id; ?>">
                          <?php echo html_entity_decode($image_row->alt); ?>
                        </div>
                      <?php
                      }
                      ?>
                    </div> 
                    <div class="bwg_image_browser_image_<?php echo $bwg; ?>">
                      <?php
                      if ($show_watermark) {
                        ?>
                        <div class="bwg_image_browser_image_contain_<?php echo $bwg; ?>" id="bwg_image_browser_image_contain_<?php echo $image_row->id ?>">
                          <div class="bwg_image_browser_watermark_contain_<?php echo $bwg; ?>">
                            <div class="bwg_image_browser_watermark_cont_<?php echo $bwg; ?>">
                              <div <?php echo $watermark_div; ?> >
                                <a class="bwg_none_selectable <?php echo $watermark_a; ?>" id="watermark_a<?php echo $image_row->id; ?>" href="<?php echo $params_array['watermark_link']; ?>" target="_blank">
                                  <?php echo $watermark_image_or_text; ?>
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php
                      }
                      if (!$is_video) {
                      ?>
                        <a style="position:relative;" <?php echo ($params['thumb_click_action'] == 'open_lightbox' ? ('onclick="spider_createpopup(\'' . addslashes($this->array_to_url($params_array)) . '\', ' . $bwg . ', ' . $params['popup_width'] . ', ' . $params['popup_height'] . ', 1, \'testpopup\', 5); return false;"') : ('href="' . $image_row->redirect_url . '" target="' .  ($params['thumb_link_target'] ? '_blank' : '')  . '"')) ?>>
                          <img class="bwg_image_browser_img_<?php echo $bwg; ?>" src="<?php echo JURI::root(). $WD_BWG_UPLOAD_DIR. '/' . $image_row->image_url; ?>" alt="<?php echo $image_row->alt; ?>" />
                        </a>
                      <?php 
                      }
                      else { ?>
                        <iframe id="bwg_video_frame_<?php echo $bwg; ?>" src="<?php echo ($image_row->filetype == "YOUTUBE" ? "//www.youtube.com/embed/" . $image_row->filename : "//player.vimeo.com/video/" . $image_row->filename); ?>" width="<?php echo $params['image_browser_width']; ?>" height="<?php echo $params['image_browser_width'] * 0.5625; ?>" frameborder="0" allowfullscreen style="position: relative;"></iframe>
                      <?php
                      }
                      ?>
                    <script>	
                      setTimeout(function(){
                        jQuery('#bwg_video_frame_<?php echo $bwg; ?>').height(jQuery('#bwg_video_frame_<?php echo $bwg; ?>').width() * 0.5625);
                        if (jQuery('.image_browser_images_<?php echo $bwg; ?>').width() <= 108) {
                          jQuery('.paging-input_<?php echo $bwg; ?>').css('display', 'none');
                        }
                        if (jQuery('.image_browser_images_<?php echo $bwg; ?>').width() <= 200 && jQuery('.image_browser_images_<?php echo $bwg; ?>').width() > 108) {
                          jQuery('.paging-input_<?php echo $bwg; ?>').css('display', 'inline');
                          jQuery('.paging-input_<?php echo $bwg; ?>').css('margin', '0% 3% 0% 3%');
                          jQuery('.tablenav-pages_<?php echo $bwg; ?> .next-page').css('margin', '0% 0% 0% 0%');
                          jQuery('.tablenav-pages_<?php echo $bwg; ?> .prev-page').css('margin', '0% 0% 0% 0%');
                        }
                        else {
                          if (jQuery('.image_browser_images_<?php echo $bwg; ?>').width() > 200 && jQuery('.image_browser_images_<?php echo $bwg; ?>').width() <= 580) {
                            jQuery('.paging-input_<?php echo $bwg; ?>').css('display', 'inline');
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> a').css('font-size', '13px');
                            jQuery('.paging-input_<?php echo $bwg; ?>').css('margin', '0% 10% 0% 10%');
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> .next-page').css('margin', '0% 3% 0% 0%');
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> .prev-page').css('margin', '0% 0% 0% 3%');
                          }
                          if (jQuery('.image_browser_images_<?php echo $bwg; ?>').width() > 580) {
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> a').css('font-size', '15px');
                            jQuery('.paging-input_<?php echo $bwg; ?>').css('margin', '0%  17% 0%  17%');
                            jQuery('.paging-input_<?php echo $bwg; ?>').css('display', 'inline');
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> .next-page').css('margin', '0% 4% 0% 0%');
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> .prev-page').css('margin', '0% 0% 0% 4%');
                          }
                        }
                      }, 3);
                      jQuery(window).resize(function() {
                        jQuery('#bwg_video_frame_<?php echo $bwg; ?>').height(jQuery('#bwg_video_frame_<?php echo $bwg; ?>').width() * 0.5625);
                        if (jQuery('.image_browser_images_<?php echo $bwg; ?>').width() <= 108) {
                          jQuery('.paging-input_<?php echo $bwg; ?>').css('display', 'none');					  
                        }
                        if (jQuery('.image_browser_images_<?php echo $bwg; ?>').width() <= 200 && jQuery('.image_browser_images_<?php echo $bwg; ?>').width() > 108) {
                          jQuery('.paging-input_<?php echo $bwg; ?>').css('margin', '0% 2% 0% 2%');
                          jQuery('.paging-input_<?php echo $bwg; ?>').css('display', 'inline');
                          jQuery('.tablenav-pages_<?php echo $bwg; ?> .next-page').css('margin', '0% 0% 0% 0%');
                          jQuery('.tablenav-pages_<?php echo $bwg; ?> .prev-page').css('margin', '0% 0% 0% 0%');						
                        }
                        else {
                          if (jQuery('.image_browser_images_<?php echo $bwg; ?>').width() > 200 && jQuery('.image_browser_images_<?php echo $bwg; ?>').width() <= 580) {
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> a').css('font-size', '13px');
                            jQuery('.paging-input_<?php echo $bwg; ?>').css('margin', '0% 10% 0% 10%');
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> .next-page').css('margin', '0% 3% 0% 0%');
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> .prev-page').css('margin', '0% 0% 0% 3%');
                            jQuery('.paging-input_<?php echo $bwg; ?>').css('display', 'inline');
                          }
                          else if (jQuery('.image_browser_images_<?php echo $bwg; ?>').width() > 580) {
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> a').css('font-size', '15px');
                            jQuery('.paging-input_<?php echo $bwg; ?>').css('margin', '0%  17% 0%  17%');
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> .next-page').css('margin', '0% 4% 0% 0%');
                            jQuery('.tablenav-pages_<?php echo $bwg; ?> .prev-page').css('margin', '0% 0% 0% 4%');
                            jQuery('.paging-input_<?php echo $bwg; ?>').css('display', 'inline');
                          }
                        }
                      });
                    </script>				  
                    </div>
                      <?php
                      if ($enable_image_description && ($image_row->description != "")) {
                        ?>
                      <div class="bwg_image_browser_image_desp_<?php echo $bwg; ?>">                    
                        <div class="bwg_image_browser_image_description_<?php echo $bwg; ?>" id="alt<?php echo $image_row->id; ?>">
                          <?php echo html_entity_decode($image_row->description); ?>
                        </div>                  
                      </div>
                      <?php
                      }
                      ?>
                  </div>
                </div>
                <?php
              }
              if ( $theme_row->page_nav_position == 'bottom') {
                WDWLibrary::ajax_html_frontend_page_nav($theme_row, $page_nav['total'], $page_nav['limit'], 'gal_front_form_' . $bwg, 1, $bwg, 'bwg_standart_thumbnails_' . $bwg);
              }
              ?>
            </div>
          </div>
        </form>
        <div id="spider_popup_loading_<?php echo $bwg; ?>" class="spider_popup_loading"></div>
        <div id="spider_popup_overlay_<?php echo $bwg; ?>" class="spider_popup_overlay" onclick="spider_destroypopup(1000)"></div>
      </div>
    </div>
    <script>
      jQuery(window).load(function () {
        <?php
        if ($image_right_click) {
          ?>
          /* Disable right click.*/
          jQuery('div[id^="bwg_container"]').bind("contextmenu", function (e) {
            return false;
          });
          <?php
        }
        ?>
      });
      var bwg_current_url = '<?php echo $current_url; ?>';
      <?php
      if (isset($params_array_hash)) {
      ?>
      var bwg_hash = window.location.hash.substring(1);
      if (bwg_hash && bwg_hash.indexOf("bwg") != "-1") {
        bwg_hash_array = bwg_hash.replace("bwg", "").split("/");
        spider_createpopup('<?php echo addslashes($this->array_to_url($params_array_hash)); ?>&image_id=' + bwg_hash_array[1], '<?php echo $bwg; ?>', '<?php echo $params['popup_width']; ?>', '<?php echo $params['popup_height']; ?>', 1, 'testpopup', 5);
      }
      <?php
      }
      ?>
    </script>
    <?php
	
	}
	
	
	
	function slideshow($bwg)
	{
	
	$WD_BWG_DIR=JPATH_BASE.'/components/com_gallery_wd';
$WD_BWG_URL=JURI::root().'components/com_gallery_wd';	

$db		=JFactory::getDBO();
$query="SHOW TABLES LIKE '#__bwg_option'";
$db->setQuery($query);


if ($db->query()) {

$query='SELECT images_directory FROM #__bwg_option WHERE id=1';
$db->setQuery($query);
$WD_BWG_UPLOAD_DIR= $db->loadResult() . '/com_gallery_wd/uploads';
}
else {
 
$WD_BWG_UPLOAD_DIR="administrator/components/com_gallery_wd/uploads";
}
	
	
	require_once($WD_BWG_DIR . '/framework/WDWLibrary.php');

$params=$this->params;

    $from = (isset($params['from']) ? htmlspecialchars($params['from']) : 0);
    $options_row = $this->get_options_row_data;
	
	    $image_right_click = $options_row->image_right_click;
    $filmstrip_direction = 'horizontal';
	
    if (!$from) {
      $theme_id = (isset($params['theme_id']) ? htmlspecialchars($params['theme_id']) : 1);
      $gallery_id = (isset($params['gallery_id']) ? htmlspecialchars($params['gallery_id']) : 0);
      $sort_by = (isset($params['sort_by']) ? htmlspecialchars($params['sort_by']) : 'order');
      $slideshow_effect = (isset($params['slideshow_type']) ? htmlspecialchars($params['slideshow_type']) : 'fade');
      $enable_slideshow_autoplay = (isset($params['slideshow_enable_autoplay']) ? htmlspecialchars($params['slideshow_enable_autoplay']) : 0);
      $enable_slideshow_shuffle = (isset($params['slideshow_enable_shuffle']) ? htmlspecialchars($params['slideshow_enable_shuffle']) : 0);
      $enable_slideshow_ctrl = (isset($params['slideshow_enable_ctrl']) ? htmlspecialchars($params['slideshow_enable_ctrl']) : 0);
      $enable_slideshow_filmstrip = FALSE;
      if ($enable_slideshow_filmstrip) {
        $thumb_width = $options_row->thumb_width;
        $thumb_height = $options_row->thumb_height;
        $slideshow_filmstrip_height = (isset($params['slideshow_filmstrip_height']) ? htmlspecialchars($params['slideshow_filmstrip_height']) : '50');
        $thumb_ratio = $thumb_width / $thumb_height;
        $slideshow_filmstrip_width = round($thumb_ratio * $slideshow_filmstrip_height);
      }
      else {
        $slideshow_filmstrip_height = 0;
        $slideshow_filmstrip_width = 0;
      }

      $enable_image_title = (isset($params['slideshow_enable_title']) ? htmlspecialchars($params['slideshow_enable_title']) : 0);
      $slideshow_title_position = explode('-', (isset($params['slideshow_title_position']) ? htmlspecialchars($params['slideshow_title_position']) : 'bottom-right'));
      $enable_image_description = (isset($params['slideshow_enable_description']) ? htmlspecialchars($params['slideshow_enable_description']) : 0);
      $slideshow_description_position = explode('-', (isset($params['slideshow_description_position']) ? htmlspecialchars($params['slideshow_description_position']) : 'bottom-right'));
      $enable_slideshow_music = (isset($params['slideshow_enable_music']) ? htmlspecialchars($params['slideshow_enable_music']) : 0);
      $slideshow_music_url = (isset($params['slideshow_audio_url']) ? htmlspecialchars($params['slideshow_audio_url']) : '');

      $image_width = (isset($params['slideshow_width']) ? htmlspecialchars($params['slideshow_width']) : '800');
      $image_height = (isset($params['slideshow_height']) ? htmlspecialchars($params['slideshow_height']) : '600');
      $slideshow_interval = (isset($params['slideshow_interval']) ? htmlspecialchars($params['slideshow_interval']) : 5);

      $watermark_type = (isset($params['watermark_type']) ? htmlspecialchars($params['watermark_type']) : 'none');
      $watermark_text = (isset($params['watermark_text']) ? htmlspecialchars($params['watermark_text']) : '');
      $watermark_font_size = (isset($params['watermark_font_size']) ? htmlspecialchars($params['watermark_font_size']) : 12);
      $watermark_font = (isset($params['watermark_font']) ? htmlspecialchars($params['watermark_font']) : 'Arial');
      $watermark_color = (isset($params['watermark_color']) ? htmlspecialchars($params['watermark_color']) : 'FFFFFF');
      $watermark_opacity = (isset($params['watermark_opacity']) ? htmlspecialchars($params['watermark_opacity']) : 30);
      $watermark_position = explode('-', (isset($params['watermark_position']) ? htmlspecialchars($params['watermark_position']) : 'bottom-right'));
      $watermark_link = (isset($params['watermark_link']) ? htmlspecialchars($params['watermark_link']) : '');
      $watermark_url = (isset($params['watermark_url']) ? htmlspecialchars($params['watermark_url']) : '');
      $watermark_width = (isset($params['watermark_width']) ? htmlspecialchars($params['watermark_width']) : 90);
      $watermark_height = (isset($params['watermark_height']) ? htmlspecialchars($params['watermark_height']) : 90);
    }
    else {      
      $theme_id = (isset($params['theme_id']) ? htmlspecialchars($params['theme_id']) : 0);
      $gallery_id = (isset($params['gallery_id']) ? htmlspecialchars($params['gallery_id']) : 0);
      $sort_by = 'order';
      $slideshow_effect = (isset($params['effect']) ? htmlspecialchars($params['effect']) : 'fade');
      $enable_slideshow_autoplay = $options_row->slideshow_enable_autoplay;
      $enable_slideshow_shuffle = (isset($params['slideshow_enable_shuffle']) ? htmlspecialchars($params['slideshow_enable_shuffle']) : 0);
      $enable_slideshow_ctrl = $options_row->slideshow_enable_ctrl;
      $enable_slideshow_filmstrip = $options_row->slideshow_enable_filmstrip;
      if ($enable_slideshow_filmstrip) {
        $thumb_width = $options_row->thumb_width;
        $thumb_height = $options_row->thumb_height;
        $slideshow_filmstrip_height = $options_row->slideshow_filmstrip_height;
        $thumb_ratio = $thumb_width / $thumb_height;
        $slideshow_filmstrip_width = round($thumb_ratio * $slideshow_filmstrip_height);
      }
      else {
        $slideshow_filmstrip_height = 0;
        $slideshow_filmstrip_width = 0;
      }

      $enable_image_title = $options_row->slideshow_enable_title;
      $slideshow_title_position = explode('-', $options_row->slideshow_title_position);
      $enable_image_description = $options_row->slideshow_enable_description;
      $slideshow_description_position = explode('-', $options_row->slideshow_description_position);
      $enable_slideshow_music = $options_row->slideshow_enable_music;
      $slideshow_music_url = $options_row->slideshow_audio_url;

      $image_width = (isset($params['width']) ? htmlspecialchars($params['width']) : '800');
      $image_height = (isset($params['height']) ? htmlspecialchars($params['height']) : '600');
      $slideshow_interval = (isset($params['interval']) ? htmlspecialchars($params['interval']) : 5);

      $watermark_type = $options_row->watermark_type;
      $watermark_text = $options_row->watermark_text;
      $watermark_font_size = $options_row->watermark_font_size;
      $watermark_font = $options_row->watermark_font;
      $watermark_color = $options_row->watermark_color;
      $watermark_opacity = $options_row->watermark_opacity;
      $watermark_position = explode('-', $options_row->watermark_position);
      $watermark_link = $options_row->watermark_link;
      $watermark_url = $options_row->watermark_url;
      $watermark_width = $options_row->watermark_width;
      $watermark_height = $options_row->watermark_height;
    }

    $theme_row = $this->get_theme_row_data;
    if (!$theme_row) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_THEME'), 'error');
      return;
    }
    $gallery_row = $this->get_gallery_row_data;
    if (!$gallery_row) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_GALLERY'), 'error');
      return;
    }
    $image_rows = $this->get_image_rows_data;
    if (!$image_rows) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_IMAGE'), 'error');
    }
    $current_image_id = ($image_rows ? $image_rows[0]->id : 0);
	    $play_pause_button_display = 'undefined';
		
	$filmstrip_thumb_margin = $theme_row->slideshow_filmstrip_thumb_margin;
    $margins_split = explode(" ", $filmstrip_thumb_margin);
    $temp_iterator = ($filmstrip_direction == 'horizontal' ? 1 : 0);
    if (isset($margins_split[$temp_iterator])) {
      $filmstrip_thumb_margin_right = (int) $margins_split[$temp_iterator];
      if (isset($margins_split[$temp_iterator + 2])) {
        $filmstrip_thumb_margin_left = (int) $margins_split[$temp_iterator + 2];
      }
      else {
        $filmstrip_thumb_margin_left = $filmstrip_thumb_margin_right;
      }
    }
    elseif (isset($margins_split[0])) {
      $filmstrip_thumb_margin_right = (int) $margins_split[0];
      $filmstrip_thumb_margin_left = $filmstrip_thumb_margin_right;
    }
    $filmstrip_thumb_margin_hor = $filmstrip_thumb_margin_right + $filmstrip_thumb_margin_left;
    if (!$enable_slideshow_filmstrip) {
      if ($theme_row->slideshow_filmstrip_pos == 'left') {
        $theme_row->slideshow_filmstrip_pos = 'top';
      }
      if ($theme_row->slideshow_filmstrip_pos == 'right') {
        $theme_row->slideshow_filmstrip_pos = 'bottom';
      }
    }
    $left_or_top = 'left';
    $width_or_height = 'width';
    $outerWidth_or_outerHeight = 'outerWidth';
    if (!($filmstrip_direction == 'horizontal')) {
      $left_or_top = 'top';
      $width_or_height = 'height';
      $outerWidth_or_outerHeight = 'outerHeight';
    }
	
    ?>
    <style>
      #bwg_container1_<?php echo $bwg; ?> {
        visibility: hidden;
      }
      #bwg_container1_<?php echo $bwg; ?> * {
        -moz-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_wrap_<?php echo $bwg; ?> * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        /*backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        -ms-backface-visibility: hidden;*/
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_wrap_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->slideshow_cont_bg_color; ?>;
        border-collapse: collapse;
        display: table;
        position: relative;
        text-align: center;
        width: <?php echo $image_width; ?>px;
        height: <?php echo $image_height; ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_<?php echo $bwg; ?> {
        padding: 0 !important;
        margin: 0 !important;
        float: none !important;
        max-width: <?php echo $image_width - ($filmstrip_direction == 'vertical' ? $slideshow_filmstrip_width : 0); ?>px;
        max-height: <?php echo $image_height - ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : 0); ?>px;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_video_<?php echo $bwg; ?> {
        padding: 0 !important;
        margin: 0 !important;
        float: none !important;
        width: <?php echo $image_width - ($filmstrip_direction == 'vertical' ? $slideshow_filmstrip_width : 0); ?>px;
        height: <?php echo $image_height - ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : 0); ?>px;
        vertical-align: middle;
        display: inline-block;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_prev_btn_<?php echo $bwg; ?>,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_next_btn_<?php echo $bwg; ?> {
        /*opacity: <?php echo $theme_row->slideshow_close_btn_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->slideshow_close_btn_transparent; ?>);*/
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_watermark_<?php echo $bwg; ?> {
        position: relative;
        z-index: 15;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #bwg_slideshow_play_pause_<?php echo $bwg; ?> {
        background: transparent url("<?php echo $WD_BWG_URL . '/images/blank.gif'; ?>") repeat scroll 0 0;
        bottom: 0;
        cursor: pointer;
        display: table;
        height: inherit;
        outline: medium none;
        position: absolute;
        width: 30%;
        left: 35%;
        z-index: 13;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #bwg_slideshow_play_pause_<?php echo $bwg; ?>:hover #bwg_slideshow_play_pause-ico_<?php echo $bwg; ?> {
        display: inline-block !important;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #bwg_slideshow_play_pause_<?php echo $bwg; ?>:hover span {
        position: relative;
        z-index: 13;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #bwg_slideshow_play_pause_<?php echo $bwg; ?> span {
        display: table-cell;
        text-align: center;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #bwg_slideshow_play_pause-ico_<?php echo $bwg; ?> {  
        display: none !important;
        color: #<?php echo $theme_row->slideshow_rl_btn_color; ?>;        
        font-size: <?php echo $theme_row->slideshow_play_pause_btn_size; ?>px;
        cursor: pointer;
        position: relative;
        z-index: 13;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #bwg_slideshow_play_pause-ico_<?php echo $bwg; ?>:hover {  
        color: #<?php echo $theme_row->slideshow_close_rl_btn_hover_color; ?>;
        display: inline-block;
        position: relative;
        z-index: 13;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_left_<?php echo $bwg; ?>,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_right_<?php echo $bwg; ?> {
        background: transparent url("<?php echo $WD_BWG_URL . '/images/blank.gif'; ?>") repeat scroll 0 0;
        bottom: 35%;
        cursor: pointer;
        display: inline;
        height: 30%;
        outline: medium none;
        position: absolute;
        width: 35%;
        /*z-index: 10130;*/
        z-index: 13;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_left_<?php echo $bwg; ?> {
        left: 0;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_right_<?php echo $bwg; ?> {
        right: 0;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_left_<?php echo $bwg; ?>:hover,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_right_<?php echo $bwg; ?>:hover {
        visibility: visible;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_left_<?php echo $bwg; ?>:hover span {
        left: 20px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_right_<?php echo $bwg; ?>:hover span {
        left: auto;
        right: 20px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_left-ico_<?php echo $bwg; ?> span,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_right-ico_<?php echo $bwg; ?> span {
        display: table-cell;
        text-align: center;
        vertical-align: middle;
        z-index: 13;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_left-ico_<?php echo $bwg; ?>,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_right-ico_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->slideshow_rl_btn_bg_color; ?>;
        border-radius: <?php echo $theme_row->slideshow_rl_btn_border_radius; ?>;
        border: <?php echo $theme_row->slideshow_rl_btn_border_width; ?>px <?php echo $theme_row->slideshow_rl_btn_border_style; ?> #<?php echo $theme_row->slideshow_rl_btn_border_color; ?>;
        box-shadow: <?php echo $theme_row->slideshow_rl_btn_box_shadow; ?>;
        color: #<?php echo $theme_row->slideshow_rl_btn_color; ?>;
        height: <?php echo $theme_row->slideshow_rl_btn_height; ?>px;
        font-size: <?php echo $theme_row->slideshow_rl_btn_size; ?>px;
        width: <?php echo $theme_row->slideshow_rl_btn_width; ?>px;
        z-index: 13;
        -moz-box-sizing: content-box;
        box-sizing: content-box;
        cursor: pointer;
        display: table;
        left: -9999px;
        line-height: 0;
        margin-top: -15px;
        position: absolute;
        top: 50%;
        /*z-index: 10135;*/
        opacity: <?php echo $theme_row->slideshow_close_btn_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->slideshow_close_btn_transparent; ?>);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_left-ico_<?php echo $bwg; ?>:hover,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_slideshow_right-ico_<?php echo $bwg; ?>:hover {
        color: #<?php echo $theme_row->slideshow_close_rl_btn_hover_color; ?>;
        cursor: pointer;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_container_<?php echo $bwg; ?> {
        display: table;
        position: absolute;
        text-align: center;
        <?php echo $theme_row->slideshow_filmstrip_pos; ?>: <?php echo ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : $slideshow_filmstrip_width); ?>px;
        vertical-align: middle;
        width: <?php echo $image_width; ?>px;
        height: <?php echo $image_height; ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_container_<?php echo $bwg; ?> {
        display: <?php echo ($filmstrip_direction == 'horizontal'? 'table' : 'block'); ?>;
        height: <?php echo ($filmstrip_direction == 'horizontal'? $slideshow_filmstrip_height : $image_height); ?>px;
        position: absolute;
        width: <?php echo ($filmstrip_direction == 'horizontal' ? $image_width : $slideshow_filmstrip_width); ?>px;
        /*z-index: 10105;*/
        <?php echo $theme_row->slideshow_filmstrip_pos; ?>: 0;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_<?php echo $bwg; ?> {
        <?php echo $left_or_top; ?>: 20px;
        overflow: hidden;
        position: absolute;
        <?php echo $width_or_height; ?>: <?php echo ($filmstrip_direction == 'horizontal' ? $image_width - 40 : $image_height - 40); ?>px;
        /*z-index: 10106;*/
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?> {
        height: <?php echo ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : ($slideshow_filmstrip_height + $filmstrip_thumb_margin_hor) * count($image_rows)); ?>px;
        <?php echo $left_or_top; ?>: 0px;
        margin: 0 auto;
        overflow: hidden;
        position: relative;
        width: <?php echo ($filmstrip_direction == 'horizontal' ? ($slideshow_filmstrip_width + $filmstrip_thumb_margin_hor) * count($image_rows) : $slideshow_filmstrip_width); ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_thumbnail_<?php echo $bwg; ?> {
        position: relative;
        background: none;
        border: <?php echo $theme_row->slideshow_filmstrip_thumb_border_width; ?>px <?php echo $theme_row->slideshow_filmstrip_thumb_border_style; ?> #<?php echo $theme_row->slideshow_filmstrip_thumb_border_color; ?>;
        border-radius: <?php echo $theme_row->slideshow_filmstrip_thumb_border_radius; ?>;
        cursor: pointer;
        float: left;
        height: <?php echo $slideshow_filmstrip_height; ?>px;
        margin: <?php echo $theme_row->slideshow_filmstrip_thumb_margin; ?>;
        width: <?php echo $slideshow_filmstrip_width; ?>px;
        overflow: hidden;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_thumb_active_<?php echo $bwg; ?> {
        opacity: 1;
        filter: Alpha(opacity=100);
        border: <?php echo $theme_row->slideshow_filmstrip_thumb_active_border_width; ?>px solid #<?php echo $theme_row->slideshow_filmstrip_thumb_active_border_color; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_thumb_deactive_<?php echo $bwg; ?> {
        opacity: <?php echo $theme_row->slideshow_filmstrip_thumb_deactive_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->slideshow_filmstrip_thumb_deactive_transparent; ?>);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_thumbnail_img_<?php echo $bwg; ?> {
        display: block;
        opacity: 1;
        filter: Alpha(opacity=100);
        padding: 0 !important;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_left_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->slideshow_filmstrip_rl_bg_color; ?>;
        cursor: pointer;
        display: <?php echo ($filmstrip_direction == 'horizontal' ? 'table-cell' : 'block') ?>;
        vertical-align: middle;
        <?php echo $width_or_height; ?>: 20px;
        /*z-index: 10106;*/
        <?php echo $left_or_top; ?>: 0;
        <?php echo ($filmstrip_direction == 'horizontal' ? '' : 'position: absolute;') ?>
        <?php echo ($filmstrip_direction == 'horizontal' ? '' : 'width: 100%;') ?> 
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_right_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->slideshow_filmstrip_rl_bg_color; ?>;
        cursor: pointer;
        <?php echo($filmstrip_direction == 'horizontal' ? 'right' : 'bottom') ?>: 0;
        <?php echo $width_or_height; ?>: 20px;
        display: <?php echo ($filmstrip_direction == 'horizontal' ? 'table-cell' : 'block') ?>;
        vertical-align: middle;
        /*z-index: 10106;*/
        <?php echo ($filmstrip_direction == 'horizontal' ? '' : 'position: absolute;') ?>
        <?php echo ($filmstrip_direction == 'horizontal' ? '' : 'width: 100%;') ?>
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_left_<?php echo $bwg; ?> i,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_filmstrip_right_<?php echo $bwg; ?> i {
        color: #<?php echo $theme_row->slideshow_filmstrip_rl_btn_color; ?>;
        font-size: <?php echo $theme_row->slideshow_filmstrip_rl_btn_size; ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_none_selectable_<?php echo $bwg; ?> {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_watermark_container_<?php echo $bwg; ?> {
        display: table-cell;
        margin: 0 auto;
        position: relative;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_watermark_spun_<?php echo $bwg; ?> {
        display: table-cell;
        overflow: hidden;
        position: relative;
        text-align: <?php echo $watermark_position[1]; ?>;
        vertical-align: <?php echo $watermark_position[0]; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_title_spun_<?php echo $bwg; ?> {
        display: table-cell;
        overflow: hidden;
        position: relative;
        text-align: <?php echo $slideshow_title_position[1]; ?>;
        vertical-align: <?php echo $slideshow_title_position[0]; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_description_spun_<?php echo $bwg; ?> {
        display: table-cell;
        overflow: hidden;
        position: relative;
        text-align: <?php echo $slideshow_description_position[1]; ?>;
        vertical-align: <?php echo $slideshow_description_position[0]; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_watermark_image_<?php echo $bwg; ?> {
        padding: 0 !important;
        float: none !important;
        margin: 4px !important;
        max-height: <?php echo $watermark_height; ?>px;
        max-width: <?php echo $watermark_width; ?>px;
        opacity: <?php echo $watermark_opacity / 100; ?>;
        filter: Alpha(opacity=<?php echo $watermark_opacity; ?>);
        position: relative;
        z-index: 15;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_watermark_text_<?php echo $bwg; ?>,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_watermark_text_<?php echo $bwg; ?>:hover {
        text-decoration: none;
        margin: 4px;
        font-size: <?php echo $watermark_font_size; ?>px;
        font-family: <?php echo $watermark_font; ?>;
        color: #<?php echo $watermark_color; ?> !important;
        opacity: <?php echo $watermark_opacity / 100; ?>;
        filter: Alpha(opacity=<?php echo $watermark_opacity; ?>);
        position: relative;
        z-index: 15;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_title_text_<?php echo $bwg; ?> {
        text-decoration: none;
        font-size: <?php echo $theme_row->slideshow_title_font_size; ?>px;
        font-family: <?php echo $theme_row->slideshow_title_font; ?>;
        color: #<?php echo $theme_row->slideshow_title_color; ?> !important;
        opacity: <?php echo $theme_row->slideshow_title_opacity / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->slideshow_title_opacity; ?>);
        position: relative;
        z-index: 11;
        border-radius: <?php echo $theme_row->slideshow_title_border_radius; ?>;
        background-color: #<?php echo $theme_row->slideshow_title_background_color; ?>;
        padding: <?php echo $theme_row->slideshow_title_padding; ?>;
        margin: 5px;
        display: inline-block;
        word-wrap: break-word;
        word-break: break-word;
        <?php if (!$enable_slideshow_filmstrip && $slideshow_title_position[0] == $theme_row->slideshow_filmstrip_pos) echo $theme_row->slideshow_filmstrip_pos . ':' . ($theme_row->slideshow_dots_height + 4) . 'px;'; ?>
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_description_text_<?php echo $bwg; ?> {
        text-decoration: none;
        font-size: <?php echo $theme_row->slideshow_description_font_size; ?>px;
        font-family: <?php echo $theme_row->slideshow_description_font; ?>;
        color: #<?php echo $theme_row->slideshow_description_color; ?> !important;
        opacity: <?php echo $theme_row->slideshow_description_opacity / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->slideshow_description_opacity; ?>);
        position: relative;
        z-index: 15;
        border-radius: <?php echo $theme_row->slideshow_description_border_radius; ?>;
        background-color: #<?php echo $theme_row->slideshow_description_background_color; ?>;
        padding: <?php echo $theme_row->slideshow_description_padding; ?>;
        margin: 5px;
        display: inline-block;
        word-wrap: break-word;
        word-break: break-word;
        <?php if (!$enable_slideshow_filmstrip && $slideshow_description_position[0] == $theme_row->slideshow_filmstrip_pos) echo $theme_row->slideshow_filmstrip_pos . ':' . ($theme_row->slideshow_dots_height + 4) . 'px;'; ?>        
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_description_text_<?php echo $bwg; ?> * {
        text-decoration: none;
        color: #<?php echo $theme_row->slideshow_description_color; ?> !important;                
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slide_container_<?php echo $bwg; ?> {
        display: table-cell;
        margin: 0 auto;
        position: absolute;
        vertical-align: middle;
        width: 100%;
        height: 100%;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slide_bg_<?php echo $bwg; ?> {
        margin: 0 auto;
        width: inherit;
        height: inherit;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slider_<?php echo $bwg; ?> {
        height: inherit;
        width: inherit;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_spun_<?php echo $bwg; ?> {
        width: inherit;
        height: inherit;
        display: table-cell;
        filter: Alpha(opacity=100);
        opacity: 1;
        position: absolute;
        vertical-align: middle;
        z-index: 2;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_second_spun_<?php echo $bwg; ?> {
        width: inherit;
        height: inherit;
        display: table-cell;
        filter: Alpha(opacity=0);
        opacity: 0;
        position: absolute;
        vertical-align: middle;
        z-index: 1;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_grid_<?php echo $bwg; ?> {
        display: none;
        height: 100%;
        overflow: hidden;
        position: absolute;
        width: 100%;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_gridlet_<?php echo $bwg; ?> {
        opacity: 1;
        filter: Alpha(opacity=100);
        position: absolute;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_dots_<?php echo $bwg; ?> {
        display: inline-block;
        position: relative;
        width: <?php echo $theme_row->slideshow_dots_width; ?>px;
        height: <?php echo $theme_row->slideshow_dots_height; ?>px;
        border-radius: <?php echo $theme_row->slideshow_dots_border_radius; ?>;
        background: #<?php echo $theme_row->slideshow_dots_background_color; ?>;
        margin: <?php echo $theme_row->slideshow_dots_margin; ?>px;
        cursor: pointer;
        overflow: hidden;
        z-index: 17;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_dots_container_<?php echo $bwg; ?> {
        display: block;
        overflow: hidden;
        position: absolute;
        width: <?php echo $image_width; ?>px;
        <?php echo $theme_row->slideshow_filmstrip_pos; ?>: 0;
        z-index: 17;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_dots_thumbnails_<?php echo $bwg; ?> {
        left: 0px;
        font-size: 0;
        margin: 0 auto;
        overflow: hidden;
        position: relative;
        height: <?php echo ($theme_row->slideshow_dots_height + $theme_row->slideshow_dots_margin * 2); ?>px;
        width: <?php echo ($theme_row->slideshow_dots_width + $theme_row->slideshow_dots_margin * 2 + 4) * count($image_rows); ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_dots_active_<?php echo $bwg; ?> {
        background: #<?php echo $theme_row->slideshow_dots_active_background_color; ?>;
        opacity: 1;
        filter: Alpha(opacity=100);
        border: <?php echo $theme_row->slideshow_dots_active_border_width; ?>px solid #<?php echo $theme_row->slideshow_dots_active_border_color; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_dots_deactive_<?php echo $bwg; ?> {
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_spun1_<?php echo $bwg; ?> {
        display: table; 
        width: inherit; 
        height: inherit;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_slideshow_image_spun2_<?php echo $bwg; ?> {
        display: table-cell; 
        vertical-align: middle; 
        text-align: center;
      }
    </style>
    <script>
      var data_<?php echo $bwg; ?> = [];
      var event_stack_<?php echo $bwg; ?> = [];
      <?php
      foreach ($image_rows as $key => $image_row) {
	  	  		$image_row->thumb_url=htmlspecialchars($image_row->thumb_url);
			  $image_row->image_url=htmlspecialchars($image_row->image_url);
			  $image_row->alt=htmlspecialchars($image_row->alt);
			  $image_row->filename=htmlspecialchars($image_row->filename);
 
        if ($image_row->id == $current_image_id) {
          $current_image_alt = $image_row->alt;
          $current_image_description = str_replace(array("\r\n", "\n", "\r"), htmlspecialchars('<br />'), $image_row->description);
        }
        ?>
        data_<?php echo $bwg; ?>["<?php echo $key; ?>"] = [];
        data_<?php echo $bwg; ?>["<?php echo $key; ?>"]["id"] = "<?php echo $image_row->id; ?>";
        data_<?php echo $bwg; ?>["<?php echo $key; ?>"]["alt"] = "<?php echo $image_row->alt; ?>";
        data_<?php echo $bwg; ?>["<?php echo $key; ?>"]["description"] = "<?php echo str_replace(array("\r\n", "\n", "\r"), htmlspecialchars('<br />'), $image_row->description); ?>";
        data_<?php echo $bwg; ?>["<?php echo $key; ?>"]["image_url"] = "<?php echo $image_row->image_url; ?>";
        data_<?php echo $bwg; ?>["<?php echo $key; ?>"]["thumb_url"] = "<?php echo $image_row->thumb_url; ?>";
        data_<?php echo $bwg; ?>["<?php echo $key; ?>"]["date"] = "<?php echo $image_row->date; ?>";
        data_<?php echo $bwg; ?>["<?php echo $key; ?>"]["is_video"] = "<?php echo $image_row->filetype == "YOUTUBE" || $image_row->filetype == "VIMEO"; ?>";
        <?php
      }
      ?>    
    </script>
    <div id="bwg_container1_<?php echo $bwg; ?>">
      <div id="bwg_container2_<?php echo $bwg; ?>">
        <div class="bwg_slideshow_image_wrap_<?php echo $bwg; ?>">
          <?php
          $current_pos = 0;
          if ($enable_slideshow_filmstrip) {
            ?>
            <div class="bwg_slideshow_filmstrip_container_<?php echo $bwg; ?>">
              <div class="bwg_slideshow_filmstrip_left_<?php echo $bwg; ?>"><i class="fa <?php echo ($filmstrip_direction == 'horizontal'? 'fa-angle-left' : 'fa-angle-up'); ?>"></i></div>
              <div class="bwg_slideshow_filmstrip_<?php echo $bwg; ?>">
                <div class="bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>">
                  <?php
                  foreach ($image_rows as $key => $image_row) {
				  	  		$image_row->thumb_url=htmlspecialchars($image_row->thumb_url);
			  $image_row->image_url=htmlspecialchars($image_row->image_url);
			  $image_row->alt=htmlspecialchars($image_row->alt);
			  $image_row->filename=htmlspecialchars($image_row->filename);
 
                    if ($image_row->id == $current_image_id) {
                      $current_pos = $key * (($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_width : $slideshow_filmstrip_height) + $filmstrip_thumb_margin_hor);
                      $current_key = $key;
                    }
                    $is_video = $image_row->filetype == "YOUTUBE" || $image_row->filetype == "VIMEO";
                    if ($play_pause_button_display === 'undefined') {
                      if ($is_video) {
                        $play_pause_button_display = 'none';
                      }
                      else {
                        $play_pause_button_display = '';
                      }
                    }
                    if (!$is_video) {
                      list($image_thumb_width, $image_thumb_height) = getimagesize(htmlspecialchars_decode(JPATH_SITE .'/' . $WD_BWG_UPLOAD_DIR.'/' . $image_row->thumb_url, ENT_COMPAT | ENT_QUOTES));
                    }
                    else {
                      $image_thumb_width = $slideshow_filmstrip_width;
                      $image_thumb_height = $slideshow_filmstrip_height;
                    }
                    $scale = max($slideshow_filmstrip_width / $image_thumb_width, $slideshow_filmstrip_height / $image_thumb_height);
                    $image_thumb_width *= $scale;
                    $image_thumb_height *= $scale;
                    $thumb_left = ($slideshow_filmstrip_width - $image_thumb_width) / 2;
                    $thumb_top = ($slideshow_filmstrip_height - $image_thumb_height) / 2;
                  ?>
                  <div id="bwg_filmstrip_thumbnail_<?php echo $key; ?>_<?php echo $bwg; ?>" class="bwg_slideshow_filmstrip_thumbnail_<?php echo $bwg; ?> <?php echo (($image_row->id == $current_image_id) ? 'bwg_slideshow_thumb_active_' . $bwg : 'bwg_slideshow_thumb_deactive_' . $bwg); ?>">
                    <img style="width:<?php echo $image_thumb_width; ?>px; height:<?php echo $image_thumb_height; ?>px; margin-left: <?php echo $thumb_left; ?>px; margin-top: <?php echo $thumb_top; ?>px;" class="bwg_slideshow_filmstrip_thumbnail_img_<?php echo $bwg; ?>" src="<?php echo ($is_video ? "" : JURI::root(). $WD_BWG_UPLOAD_DIR.'/') . $image_row->thumb_url; ?>" onclick="bwg_change_image_<?php echo $bwg; ?>(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), '<?php echo $key; ?>', data_<?php echo $bwg; ?>)" image_id="<?php echo $image_row->id; ?>" image_key="<?php echo $key; ?>" />
                  </div>
                  <?php
                  }
                  ?>
                </div>
              </div>
              <div class="bwg_slideshow_filmstrip_right_<?php echo $bwg; ?>"><i class="fa <?php echo ($filmstrip_direction == 'horizontal'? 'fa-angle-right' : 'fa-angle-down'); ?>"></i></div>
            </div>
            <?php
          }
          else {
            ?>
            <div class="bwg_slideshow_dots_container_<?php echo $bwg; ?>">
              <div class="bwg_slideshow_dots_thumbnails_<?php echo $bwg; ?>">
                <?php
                foreach ($image_rows as $key => $image_row) {
					  		$image_row->thumb_url=htmlspecialchars($image_row->thumb_url);
			  $image_row->image_url=htmlspecialchars($image_row->image_url);
			  $image_row->alt=htmlspecialchars($image_row->alt);
			  $image_row->filename=htmlspecialchars($image_row->filename);
 
                  if ($image_row->id == $current_image_id) {
                    $current_pos = $key * ($slideshow_filmstrip_width + 2);
                    $current_key = $key;
                  }
                ?>
                <span id="bwg_dots_<?php echo $key; ?>_<?php echo $bwg; ?>" class="bwg_slideshow_dots_<?php echo $bwg; ?> <?php echo (($image_row->id == $current_image_id) ? 'bwg_slideshow_dots_active_' . $bwg : 'bwg_slideshow_dots_deactive_' . $bwg); ?>" onclick="bwg_change_image_<?php echo $bwg; ?>(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), '<?php echo $key; ?>', data_<?php echo $bwg; ?>)" image_id="<?php echo $image_row->id; ?>" image_key="<?php echo $key; ?>"></span>
                <?php
                }
                ?>
              </div>
            </div>
            <?php
          }
          ?>
          <div id="bwg_slideshow_image_container_<?php echo $bwg; ?>" class="bwg_slideshow_image_container_<?php echo $bwg; ?>">        
            <div class="bwg_slide_container_<?php echo $bwg; ?>">
              <div class="bwg_slide_bg_<?php echo $bwg; ?>">
                <div class="bwg_slider_<?php echo $bwg; ?>">
                <?php
                foreach ($image_rows as $key => $image_row) {
					  		$image_row->thumb_url=htmlspecialchars($image_row->thumb_url);
			  $image_row->image_url=htmlspecialchars($image_row->image_url);
			  $image_row->alt=htmlspecialchars($image_row->alt);
			  $image_row->filename=htmlspecialchars($image_row->filename);
 
                  $is_video = $image_row->filetype == "YOUTUBE" || $image_row->filetype == "VIMEO";
                  if ($image_row->id == $current_image_id) {
                    $current_key = $key;
                    ?>
                    <span class="bwg_slideshow_image_spun_<?php echo $bwg; ?>" id="image_id_<?php echo $bwg; ?>_<?php echo $image_row->id; ?>">
                      <span class="bwg_slideshow_image_spun1_<?php echo $bwg; ?>">
                        <span class="bwg_slideshow_image_spun2_<?php echo $bwg; ?>">
                          <?php 
                            if (!$is_video) {
                            ?>
                            <img id="bwg_slideshow_image_<?php echo $bwg; ?>" class="bwg_slideshow_image_<?php echo $bwg; ?>" src="<?php echo JURI::root(). $WD_BWG_UPLOAD_DIR.'/' . $image_row->image_url; ?>" image_id="<?php echo $image_row->id; ?>" />
                            <?php 
                            }
                            else { ?>
                              <span id="bwg_slideshow_image_<?php echo $bwg; ?>" class="bwg_slideshow_video_<?php echo $bwg; ?>" image_id="<?php echo $image_row->id; ?>">
                                <iframe class="bwg_video_frame_<?php echo $bwg; ?>" src="<?php echo ($image_row->filetype == "YOUTUBE" ? "//www.youtube.com/embed/" . $image_row->filename . "?enablejsapi=1" : "//player.vimeo.com/video/" . $image_row->filename . "?api=1"); ?>" frameborder="0" allowfullscreen style="width:100%; height:100%;"></iframe>
                              </span>
                            <?php
                            }
                          ?>
                        </span>
                      </span>
                    </span>
                    <input type="hidden" id="bwg_current_image_key_<?php echo $bwg; ?>" value="<?php echo $key; ?>" />
                    <?php
                  }
                  else {
                    ?>
                    <span class="bwg_slideshow_image_second_spun_<?php echo $bwg; ?>" id="image_id_<?php echo $bwg; ?>_<?php echo $image_row->id; ?>">
                      <span class="bwg_slideshow_image_spun1_<?php echo $bwg; ?>">
                        <span class="bwg_slideshow_image_spun2_<?php echo $bwg; ?>">
                          <?php 
                            if (!$is_video) {
                            ?>
                            <img id="bwg_slideshow_image_second_<?php echo $bwg; ?>" class="bwg_slideshow_image_<?php echo $bwg; ?>" src="<?php echo JURI::root(). $WD_BWG_UPLOAD_DIR.'/' . $image_row->image_url; ?>" />
                          <?php 
                            }
                            else { ?>
                              <span id="bwg_slideshow_image_second_<?php echo $bwg; ?>" class="bwg_slideshow_video_<?php echo $bwg; ?>">
                                <iframe class="bwg_video_frame_<?php echo $bwg; ?>" src="<?php echo ($image_row->filetype == "YOUTUBE" ? "//www.youtube.com/embed/" . $image_row->filename . "?enablejsapi=1" : "//player.vimeo.com/video/" . $image_row->filename . "?api=1"); ?>" frameborder="0" allowfullscreen style="width:100%; height:100%;"></iframe>
                              </span>
                            <?php
                            }
                          ?>
                        </span>
                      </span>
                    </span>
                    <?php
                  }
                }
                ?>
                </div>
              </div>
            </div>
            <?php
              if ($enable_slideshow_ctrl) {
                ?>
              <a id="spider_slideshow_left_<?php echo $bwg; ?>" onclick="bwg_change_image_<?php echo $bwg; ?>(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), (parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()) - iterator_<?php echo $bwg; ?>()) >= 0 ? (parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()) - iterator_<?php echo $bwg; ?>()) % data_<?php echo $bwg; ?>.length : data_<?php echo $bwg; ?>.length - 1, data_<?php echo $bwg; ?>); return false;"><span id="spider_slideshow_left-ico_<?php echo $bwg; ?>"><span><i class="bwg_slideshow_prev_btn_<?php echo $bwg; ?> fa <?php echo $theme_row->slideshow_rl_btn_style; ?>-left"></i></span></span></a>
              <span id="bwg_slideshow_play_pause_<?php echo $bwg; ?>" style="display: <?php echo $play_pause_button_display; ?>;"><span><span id="bwg_slideshow_play_pause-ico_<?php echo $bwg; ?>"><i class="bwg_ctrl_btn_<?php echo $bwg; ?> bwg_slideshow_play_pause_<?php echo $bwg; ?> fa fa-play"></i></span></span></span>
              <a id="spider_slideshow_right_<?php echo $bwg; ?>" onclick="bwg_change_image_<?php echo $bwg; ?>(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), (parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()) + iterator_<?php echo $bwg; ?>()) % data_<?php echo $bwg; ?>.length, data_<?php echo $bwg; ?>); return false;"><span id="spider_slideshow_right-ico_<?php echo $bwg; ?>"><span><i class="bwg_slideshow_next_btn_<?php echo $bwg; ?> fa <?php echo $theme_row->slideshow_rl_btn_style; ?>-right"></i></span></span></a>
              <?php
              }
            ?>
          </div>
          <?php
          if ($watermark_type != 'none') {
          ?>
          <div class="bwg_slideshow_image_container_<?php echo $bwg; ?>" style="position: absolute;">
            <div class="bwg_slideshow_watermark_container_<?php echo $bwg; ?>">
              <div style="display:table; margin:0 auto;">
                <span class="bwg_slideshow_watermark_spun_<?php echo $bwg; ?>" id="bwg_slideshow_watermark_container_<?php echo $bwg; ?>">
                  <?php
                  if ($watermark_type == 'image') {
                  ?>
                  <a href="<?php echo $watermark_link; ?>" target="_blank">
                    <img class="bwg_slideshow_watermark_image_<?php echo $bwg; ?> bwg_slideshow_watermark_<?php echo $bwg; ?>" src="<?php echo $watermark_url; ?>" />
                  </a>
                  <?php
                  }
                  elseif ($watermark_type == 'text') {
                  ?>
                  <a class="bwg_none_selectable_<?php echo $bwg; ?> bwg_slideshow_watermark_text_<?php echo $bwg; ?> bwg_slideshow_watermark_<?php echo $bwg; ?>" target="_blank" href="<?php echo $watermark_link; ?>"><?php echo $watermark_text; ?></a>
                  <?php
                  }
                  ?>
                </span>
              </div>
            </div>
          </div>      
          <?php
          }
          if ($enable_image_title) {
          ?>
          <div class="bwg_slideshow_image_container_<?php echo $bwg; ?>" style="position: absolute;">
            <div class="bwg_slideshow_watermark_container_<?php echo $bwg; ?>">
              <div style="display:table; margin:0 auto;">
                <span class="bwg_slideshow_title_spun_<?php echo $bwg; ?>">
                  <div class="bwg_slideshow_title_text_<?php echo $bwg; ?>" style="<?php if (!$current_image_alt) echo 'display:none;'; ?>">
                    <?php echo html_entity_decode($current_image_alt); ?>
                  </div>
                </span>
              </div>
            </div>
          </div>
          <?php 
          }
          if ($enable_image_description) {
          ?>
          <div class="bwg_slideshow_image_container_<?php echo $bwg; ?>" style="position: absolute;">
            <div class="bwg_slideshow_watermark_container_<?php echo $bwg; ?>">
              <div style="display:table; margin:0 auto;">
                <span class="bwg_slideshow_description_spun_<?php echo $bwg; ?>">
                  <div class="bwg_slideshow_description_text_<?php echo $bwg; ?>" style="<?php if (!$current_image_description) echo 'display:none;'; ?>">
                    <?php echo html_entity_decode(str_replace("\r\n", htmlspecialchars('<br />'), $current_image_description)); ?>
                  </div>
                </span>
              </div>
            </div>
          </div>
          <?php 
          }
          if ($enable_slideshow_music) {
            ?>
            <audio id="bwg_audio_<?php echo $bwg; ?>" src="<?php echo JURI::root(). $WD_BWG_UPLOAD_DIR.'/' . $slideshow_music_url ?>" loop volume="1.0"></audio>
            <?php 
          }
          ?>
        </div>
      </div>
    </div>

    <script>
      var bwg_trans_in_progress_<?php echo $bwg; ?> = false;
      var bwg_transition_duration_<?php echo $bwg; ?> = <?php echo (($slideshow_interval < 4) && ($slideshow_interval != 0)) ? ($slideshow_interval * 1000) / 4 : 800; ?>;
      var bwg_playInterval_<?php echo $bwg; ?>;
      /* Stop autoplay.*/
      window.clearInterval(bwg_playInterval_<?php echo $bwg; ?>);
      /* Set watermark container size.*/
      function bwg_change_watermark_container_<?php echo $bwg; ?>() {
        jQuery(".bwg_slider_<?php echo $bwg; ?>").children().each(function() {
          if (jQuery(this).css("zIndex") == 2) {
            var bwg_current_image_span = jQuery(this).find("img");
            if (!bwg_current_image_span.length) {
              bwg_current_image_span = jQuery(this).find("iframe");
            }
            var width = bwg_current_image_span.width();
            var height = bwg_current_image_span.height();
            jQuery(".bwg_slideshow_watermark_spun_<?php echo $bwg; ?>").width(width);
            jQuery(".bwg_slideshow_watermark_spun_<?php echo $bwg; ?>").height(height);
            jQuery(".bwg_slideshow_title_spun_<?php echo $bwg; ?>").width(width);
            jQuery(".bwg_slideshow_title_spun_<?php echo $bwg; ?>").height(height);
            jQuery(".bwg_slideshow_description_spun_<?php echo $bwg; ?>").width(width);
            jQuery(".bwg_slideshow_description_spun_<?php echo $bwg; ?>").height(height);
            jQuery(".bwg_slideshow_watermark_<?php echo $bwg; ?>").css({display: ''});
            if (jQuery.trim(jQuery(".bwg_slideshow_title_text_<?php echo $bwg; ?>").text())) {
              jQuery(".bwg_slideshow_title_text_<?php echo $bwg; ?>").css({display: ''});
            }
            if (jQuery.trim(jQuery(".bwg_slideshow_description_text_<?php echo $bwg; ?>").text())) {
              jQuery(".bwg_slideshow_description_text_<?php echo $bwg; ?>").css({display: ''});
            }
          }
        });
      }
      var bwg_current_key_<?php echo $bwg; ?> = '<?php echo (isset($current_key) ? $current_key : ''); ?>';
      var bwg_current_filmstrip_pos_<?php echo $bwg; ?> = <?php echo $current_pos; ?>;
      /* Set filmstrip initial position.*/
      function bwg_set_filmstrip_pos_<?php echo $bwg; ?>(filmStripWidth) {
        var selectedImagePos = -bwg_current_filmstrip_pos_<?php echo $bwg; ?> - (jQuery(".bwg_slideshow_filmstrip_thumbnail_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>() + <?php echo $filmstrip_thumb_margin_hor; ?>) / 2;
        var imagesContainerLeft = Math.min(0, Math.max(filmStripWidth - jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>(), selectedImagePos + filmStripWidth / 2));
        jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").animate({
            <?php echo $left_or_top; ?>: imagesContainerLeft
          }, {
            duration: 500,
            complete: function () { bwg_filmstrip_arrows_<?php echo $bwg; ?>(); }
          });
      }
      function bwg_move_filmstrip_<?php echo $bwg; ?>() {
        var image_left = jQuery(".bwg_slideshow_thumb_active_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?>;
        var image_right = jQuery(".bwg_slideshow_thumb_active_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?> + jQuery(".bwg_slideshow_thumb_active_<?php echo $bwg; ?>").<?php echo $outerWidth_or_outerHeight; ?>(true);
        var bwg_filmstrip_width = jQuery(".bwg_slideshow_filmstrip_<?php echo $bwg; ?>").<?php echo $outerWidth_or_outerHeight; ?>(true);
        var bwg_filmstrip_thumbnails_width = jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").<?php echo $outerWidth_or_outerHeight; ?>(true);
        var long_filmstrip_cont_left = jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?>;
        var long_filmstrip_cont_right = Math.abs(jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?>) + bwg_filmstrip_width;
        if (bwg_filmstrip_width > bwg_filmstrip_thumbnails_width) {
          return;
        }
        if (image_left < Math.abs(long_filmstrip_cont_left)) {
          jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").animate({
            <?php echo $left_or_top; ?>: -image_left
          }, {
            duration: 500,
            complete: function () { bwg_filmstrip_arrows_<?php echo $bwg; ?>(); }
          });
        }
        else if (image_right > long_filmstrip_cont_right) {
          jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").animate({
            <?php echo $left_or_top; ?>: -(image_right - bwg_filmstrip_width)
          }, {
            duration: 500,
            complete: function () { bwg_filmstrip_arrows_<?php echo $bwg; ?>(); }
          });
        }
      }
      function bwg_move_dots_<?php echo $bwg; ?>() {
        var image_left = jQuery(".bwg_slideshow_dots_active_<?php echo $bwg; ?>").position().left;
        var image_right = jQuery(".bwg_slideshow_dots_active_<?php echo $bwg; ?>").position().left + jQuery(".bwg_slideshow_dots_active_<?php echo $bwg; ?>").outerWidth(true);
        var bwg_dots_width = jQuery(".bwg_slideshow_dots_container_<?php echo $bwg; ?>").outerWidth(true);
        var bwg_dots_thumbnails_width = jQuery(".bwg_slideshow_dots_thumbnails_<?php echo $bwg; ?>").outerWidth(true);
        var long_filmstrip_cont_left = jQuery(".bwg_slideshow_dots_thumbnails_<?php echo $bwg; ?>").position().left;
        var long_filmstrip_cont_right = Math.abs(jQuery(".bwg_slideshow_dots_thumbnails_<?php echo $bwg; ?>").position().left) + bwg_dots_width;
        if (bwg_dots_width > bwg_dots_thumbnails_width) {
          return;
        }
        if (image_left < Math.abs(long_filmstrip_cont_left)) {
          jQuery(".bwg_slideshow_dots_thumbnails_<?php echo $bwg; ?>").animate({
            left: -image_left
          }, {
            duration: 500,
            complete: function () {  }
          });
        }
        else if (image_right > long_filmstrip_cont_right) {
          jQuery(".bwg_slideshow_dots_thumbnails_<?php echo $bwg; ?>").animate({
            left: -(image_right - bwg_dots_width)
          }, {
            duration: 500,
            complete: function () {  }
          });
        }
      }
      /* Show/hide filmstrip arrows.*/
      function bwg_filmstrip_arrows_<?php echo $bwg; ?>() {
        if (jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>() < jQuery(".bwg_slideshow_filmstrip_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>()) {
          jQuery(".bwg_slideshow_filmstrip_left_<?php echo $bwg; ?>").hide();
          jQuery(".bwg_slideshow_filmstrip_right_<?php echo $bwg; ?>").hide();
        }
        else {
          jQuery(".bwg_slideshow_filmstrip_left_<?php echo $bwg; ?>").show();
          jQuery(".bwg_slideshow_filmstrip_right_<?php echo $bwg; ?>").show();
        }
      }
      function bwg_testBrowser_cssTransitions_<?php echo $bwg; ?>() {
        return bwg_testDom_<?php echo $bwg; ?>('Transition');
      }
      function bwg_testBrowser_cssTransforms3d_<?php echo $bwg; ?>() {
        return bwg_testDom_<?php echo $bwg; ?>('Perspective');
      }
      function bwg_testDom_<?php echo $bwg; ?>(prop) {
        /* Browser vendor CSS prefixes.*/
        var browserVendors = ['', '-webkit-', '-moz-', '-ms-', '-o-', '-khtml-'];
        /* Browser vendor DOM prefixes.*/
        var domPrefixes = ['', 'Webkit', 'Moz', 'ms', 'O', 'Khtml'];
        var i = domPrefixes.length;
        while (i--) {
          if (typeof document.body.style[domPrefixes[i] + prop] !== 'undefined') {
            return true;
          }
        }
        return false;
      }
      function bwg_cube_<?php echo $bwg; ?>(tz, ntx, nty, nrx, nry, wrx, wry, current_image_class, next_image_class, direction) {
        /* If browser does not support 3d transforms/CSS transitions.*/
        if (!bwg_testBrowser_cssTransitions_<?php echo $bwg; ?>()) {
          return bwg_fallback_<?php echo $bwg; ?>(current_image_class, next_image_class, direction);
        }
        if (!bwg_testBrowser_cssTransforms3d_<?php echo $bwg; ?>()) {
          return bwg_fallback3d_<?php echo $bwg; ?>(current_image_class, next_image_class, direction);
        }
        bwg_trans_in_progress_<?php echo $bwg; ?> = true;
        /* Set active thumbnail.*/
        jQuery(".bwg_slideshow_filmstrip_thumbnail_<?php echo $bwg; ?>").removeClass("bwg_slideshow_thumb_active_<?php echo $bwg; ?>").addClass("bwg_slideshow_thumb_deactive_<?php echo $bwg; ?>");
        jQuery("#bwg_filmstrip_thumbnail_" + bwg_current_key_<?php echo $bwg; ?> + "_<?php echo $bwg; ?>").removeClass("bwg_slideshow_thumb_deactive_<?php echo $bwg; ?>").addClass("bwg_slideshow_thumb_active_<?php echo $bwg; ?>");
        jQuery(".bwg_slideshow_dots_<?php echo $bwg; ?>").removeClass("bwg_slideshow_dots_active_<?php echo $bwg; ?>").addClass("bwg_slideshow_dots_deactive_<?php echo $bwg; ?>");
        jQuery("#bwg_dots_" + bwg_current_key_<?php echo $bwg; ?> + "_<?php echo $bwg; ?>").removeClass("bwg_slideshow_dots_deactive_<?php echo $bwg; ?>").addClass("bwg_slideshow_dots_active_<?php echo $bwg; ?>");
        jQuery(".bwg_slide_bg_<?php echo $bwg; ?>").css('perspective', 1000);
        jQuery(current_image_class).css({
          transform : 'translateZ(' + tz + 'px)',
          backfaceVisibility : 'hidden'
        });
        jQuery(next_image_class).css({
          opacity : 1,
          filter: 'Alpha(opacity=100)',
          backfaceVisibility : 'hidden',
          transform : 'translateY(' + nty + 'px) translateX(' + ntx + 'px) rotateY('+ nry +'deg) rotateX('+ nrx +'deg)'
        });
        jQuery(".bwg_slider_<?php echo $bwg; ?>").css({
          transform: 'translateZ(-' + tz + 'px)',
          transformStyle: 'preserve-3d'
        });
        /* Execution steps.*/
        setTimeout(function () {
          jQuery(".bwg_slider_<?php echo $bwg; ?>").css({
            transition: 'all ' + bwg_transition_duration_<?php echo $bwg; ?> + 'ms ease-in-out',
            transform: 'translateZ(-' + tz + 'px) rotateX('+ wrx +'deg) rotateY('+ wry +'deg)'
          });
        }, 20);
        /* After transition.*/
        jQuery(".bwg_slider_<?php echo $bwg; ?>").one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(bwg_after_trans));
        function bwg_after_trans() {
          /*if (bwg_from_focus_<?php echo $bwg; ?>) {
            bwg_from_focus_<?php echo $bwg; ?> = false;
            return;
          }*/
          jQuery(current_image_class).removeAttr('style');
          jQuery(next_image_class).removeAttr('style');
          jQuery(".bwg_slider_<?php echo $bwg; ?>").removeAttr('style');
          jQuery(current_image_class).css({'opacity' : 0, filter: 'Alpha(opacity=0)', 'z-index': 1});
          jQuery(next_image_class).css({'opacity' : 1, filter: 'Alpha(opacity=100)', 'z-index' : 2});
          bwg_change_watermark_container_<?php echo $bwg; ?>();
          bwg_trans_in_progress_<?php echo $bwg; ?> = false;
          if (typeof event_stack_<?php echo $bwg; ?> !== 'undefined' && event_stack_<?php echo $bwg; ?>.length > 0) {
            key = event_stack_<?php echo $bwg; ?>[0].split("-");
            event_stack_<?php echo $bwg; ?>.shift();
            bwg_change_image_<?php echo $bwg; ?>(key[0], key[1], data_<?php echo $bwg; ?>, true);
          }
        }
      }
      function bwg_cubeH_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        /* Set to half of image width.*/
        var dimension = jQuery(current_image_class).width() / 2;
        if (direction == 'right') {
          bwg_cube_<?php echo $bwg; ?>(dimension, dimension, 0, 0, 90, 0, -90, current_image_class, next_image_class, direction);
        }
        else if (direction == 'left') {
          bwg_cube_<?php echo $bwg; ?>(dimension, -dimension, 0, 0, -90, 0, 90, current_image_class, next_image_class, direction);
        }
      }
      function bwg_cubeV_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        /* Set to half of image height.*/
        var dimension = jQuery(current_image_class).height() / 2;
        /* If next slide.*/
        if (direction == 'right') {
          bwg_cube_<?php echo $bwg; ?>(dimension, 0, -dimension, 90, 0, -90, 0, current_image_class, next_image_class, direction);
        }
        else if (direction == 'left') {
          bwg_cube_<?php echo $bwg; ?>(dimension, 0, dimension, -90, 0, 90, 0, current_image_class, next_image_class, direction);
        }
      }
      /* For browsers that does not support transitions.*/
      function bwg_fallback_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        bwg_fade_<?php echo $bwg; ?>(current_image_class, next_image_class, direction);
      }
      /* For browsers that support transitions, but not 3d transforms (only used if primary transition makes use of 3d-transforms).*/
      function bwg_fallback3d_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        bwg_sliceV_<?php echo $bwg; ?>(current_image_class, next_image_class, direction);
      }
      function bwg_none_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
        jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
        bwg_change_watermark_container_<?php echo $bwg; ?>();
        /* Set active thumbnail.*/
        jQuery(".bwg_slideshow_filmstrip_thumbnail_<?php echo $bwg; ?>").removeClass("bwg_slideshow_thumb_active_<?php echo $bwg; ?>").addClass("bwg_slideshow_thumb_deactive_<?php echo $bwg; ?>");
        jQuery("#bwg_filmstrip_thumbnail_" + bwg_current_key_<?php echo $bwg; ?> + "_<?php echo $bwg; ?>").removeClass("bwg_slideshow_thumb_deactive_<?php echo $bwg; ?>").addClass("bwg_slideshow_thumb_active_<?php echo $bwg; ?>");
        jQuery(".bwg_slideshow_dots_<?php echo $bwg; ?>").removeClass("bwg_slideshow_dots_active_<?php echo $bwg; ?>").addClass("bwg_slideshow_dots_deactive_<?php echo $bwg; ?>");
        jQuery("#bwg_dots_" + bwg_current_key_<?php echo $bwg; ?> + "_<?php echo $bwg; ?>").removeClass("bwg_slideshow_dots_deactive_<?php echo $bwg; ?>").addClass("bwg_slideshow_dots_active_<?php echo $bwg; ?>");
      }
      function bwg_fade_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        /* Set active thumbnail.*/
        jQuery(".bwg_slideshow_filmstrip_thumbnail_<?php echo $bwg; ?>").removeClass("bwg_slideshow_thumb_active_<?php echo $bwg; ?>").addClass("bwg_slideshow_thumb_deactive_<?php echo $bwg; ?>");
        jQuery("#bwg_filmstrip_thumbnail_" + bwg_current_key_<?php echo $bwg; ?> + "_<?php echo $bwg; ?>").removeClass("bwg_slideshow_thumb_deactive_<?php echo $bwg; ?>").addClass("bwg_slideshow_thumb_active_<?php echo $bwg; ?>");
        jQuery(".bwg_slideshow_dots_<?php echo $bwg; ?>").removeClass("bwg_slideshow_dots_active_<?php echo $bwg; ?>").addClass("bwg_slideshow_dots_deactive_<?php echo $bwg; ?>");
        jQuery("#bwg_dots_" + bwg_current_key_<?php echo $bwg; ?> + "_<?php echo $bwg; ?>").removeClass("bwg_slideshow_dots_deactive_<?php echo $bwg; ?>").addClass("bwg_slideshow_dots_active_<?php echo $bwg; ?>");
        if (bwg_testBrowser_cssTransitions_<?php echo $bwg; ?>()) {
          jQuery(next_image_class).css('transition', 'opacity ' + bwg_transition_duration_<?php echo $bwg; ?> + 'ms linear');
          jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
          jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
          bwg_change_watermark_container_<?php echo $bwg; ?>();
        }
        else {
          jQuery(current_image_class).animate({'opacity' : 0, 'z-index' : 1}, bwg_transition_duration_<?php echo $bwg; ?>);
          jQuery(next_image_class).animate({
              'opacity' : 1,
              'z-index': 2
            }, {
              duration: bwg_transition_duration_<?php echo $bwg; ?>,
              complete: function () { bwg_change_watermark_container_<?php echo $bwg; ?>(); }
            });
          /* For IE.*/
          jQuery(current_image_class).fadeTo(bwg_transition_duration_<?php echo $bwg; ?>, 0);
          jQuery(next_image_class).fadeTo(bwg_transition_duration_<?php echo $bwg; ?>, 1);
        }
      }
      function bwg_grid_<?php echo $bwg; ?>(cols, rows, ro, tx, ty, sc, op, current_image_class, next_image_class, direction) {
        /* If browser does not support CSS transitions.*/
        if (!bwg_testBrowser_cssTransitions_<?php echo $bwg; ?>()) {
          return bwg_fallback_<?php echo $bwg; ?>(current_image_class, next_image_class, direction);
        }
        bwg_trans_in_progress_<?php echo $bwg; ?> = true;
        /* Set active thumbnail.*/
        jQuery(".bwg_slideshow_filmstrip_thumbnail_<?php echo $bwg; ?>").removeClass("bwg_slideshow_thumb_active_<?php echo $bwg; ?>").addClass("bwg_slideshow_thumb_deactive_<?php echo $bwg; ?>");
        jQuery("#bwg_filmstrip_thumbnail_" + bwg_current_key_<?php echo $bwg; ?> + "_<?php echo $bwg; ?>").removeClass("bwg_slideshow_thumb_deactive_<?php echo $bwg; ?>").addClass("bwg_slideshow_thumb_active_<?php echo $bwg; ?>");
        jQuery(".bwg_slideshow_dots_<?php echo $bwg; ?>").removeClass("bwg_slideshow_dots_active_<?php echo $bwg; ?>").addClass("bwg_slideshow_dots_deactive_<?php echo $bwg; ?>");
        jQuery("#bwg_dots_" + bwg_current_key_<?php echo $bwg; ?> + "_<?php echo $bwg; ?>").removeClass("bwg_slideshow_dots_deactive_<?php echo $bwg; ?>").addClass("bwg_slideshow_dots_active_<?php echo $bwg; ?>");
        /* The time (in ms) added to/subtracted from the delay total for each new gridlet.*/
        var count = (bwg_transition_duration_<?php echo $bwg; ?>) / (cols + rows);
        /* Gridlet creator (divisions of the image grid, positioned with background-images to replicate the look of an entire slide image when assembled)*/
        function bwg_gridlet(width, height, top, img_top, left, img_left, src, imgWidth, imgHeight, c, r) {
          var delay = (c + r) * count;
          /* Return a gridlet elem with styles for specific transition.*/
          return jQuery('<div class="bwg_gridlet_<?php echo $bwg; ?>" />').css({
            width : width,
            height : height,
            top : top,
            left : left,
            backgroundImage : 'url("' + src + '")',
            backgroundColor: jQuery(".bwg_slideshow_image_wrap_<?php echo $bwg; ?>").css("background-color"),
            /*backgroundColor: rgba(0, 0, 0, 0),*/
            backgroundRepeat: 'no-repeat',
            backgroundPosition : img_left + 'px ' + img_top + 'px',
            backgroundSize : imgWidth + 'px ' + imgHeight + 'px',
            transition : 'all ' + bwg_transition_duration_<?php echo $bwg; ?> + 'ms ease-in-out ' + delay + 'ms',
            transform : 'none'
          });
        }
        /* Get the current slide's image.*/
        var cur_img = jQuery(current_image_class).find('img');
        /* Create a grid to hold the gridlets.*/
        var grid = jQuery('<div />').addClass('bwg_grid_<?php echo $bwg; ?>');
        /* Prepend the grid to the next slide (i.e. so it's above the slide image).*/
        jQuery(current_image_class).prepend(grid);
        /* vars to calculate positioning/size of gridlets*/
        var cont = jQuery(".bwg_slide_bg_<?php echo $bwg; ?>");
        var imgWidth = cur_img.width();
        var imgHeight = cur_img.height();
        var contWidth = cont.width(),
            contHeight = cont.height(),
            imgSrc = cur_img.attr('src'),/*.replace('/thumb', ''),*/
            colWidth = Math.floor(contWidth / cols),
            rowHeight = Math.floor(contHeight / rows),
            colRemainder = contWidth - (cols * colWidth),
            colAdd = Math.ceil(colRemainder / cols),
            rowRemainder = contHeight - (rows * rowHeight),
            rowAdd = Math.ceil(rowRemainder / rows),
            leftDist = 0,
            img_leftDist = (jQuery(".bwg_slide_bg_<?php echo $bwg; ?>").width() - cur_img.width()) / 2;
        /* tx/ty args can be passed as 'auto'/'min-auto' (meaning use slide width/height or negative slide width/height).*/
        tx = tx === 'auto' ? contWidth : tx;
        tx = tx === 'min-auto' ? - contWidth : tx;
        ty = ty === 'auto' ? contHeight : ty;
        ty = ty === 'min-auto' ? - contHeight : ty;
        /* Loop through cols*/
        for (var i = 0; i < cols; i++) {
          var topDist = 0,
              img_topDst = (jQuery(".bwg_slide_bg_<?php echo $bwg; ?>").height() - cur_img.height()) / 2,
              newColWidth = colWidth;
          /* If imgWidth (px) does not divide cleanly into the specified number of cols, adjust individual col widths to create correct total.*/
          if (colRemainder > 0) {
            var add = colRemainder >= colAdd ? colAdd : colRemainder;
            newColWidth += add;
            colRemainder -= add;
          }
          /* Nested loop to create row gridlets for each col.*/
          for (var j = 0; j < rows; j++)  {
            var newRowHeight = rowHeight,
                newRowRemainder = rowRemainder;
            /* If contHeight (px) does not divide cleanly into the specified number of rows, adjust individual row heights to create correct total.*/
            if (newRowRemainder > 0) {
              add = newRowRemainder >= rowAdd ? rowAdd : rowRemainder;
              newRowHeight += add;
              newRowRemainder -= add;
            }
            /* Create & append gridlet to grid.*/
            grid.append(bwg_gridlet(newColWidth, newRowHeight, topDist, img_topDst, leftDist, img_leftDist, imgSrc, imgWidth, imgHeight, i, j));
            topDist += newRowHeight;
            img_topDst -= newRowHeight;
          }
          img_leftDist -= newColWidth;
          leftDist += newColWidth;
        }
        /* Set event listener on last gridlet to finish transitioning.*/
        var last_gridlet = grid.children().last();
        /* Show grid & hide the image it replaces.*/
        grid.show();
        cur_img.css('opacity', 0);
        /* Add identifying classes to corner gridlets (useful if applying border radius).*/
        grid.children().first().addClass('rs-top-left');
        grid.children().last().addClass('rs-bottom-right');
        grid.children().eq(rows - 1).addClass('rs-bottom-left');
        grid.children().eq(- rows).addClass('rs-top-right');
        /* Execution steps.*/
        setTimeout(function () {
          grid.children().css({
            opacity: op,
            transform: 'rotate('+ ro +'deg) translateX('+ tx +'px) translateY('+ ty +'px) scale('+ sc +')'
          });
        }, 1);
        jQuery(next_image_class).css('opacity', 1);
        /* After transition.*/
        jQuery(last_gridlet).one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(bwg_after_trans));
        function bwg_after_trans() {
          /*if (bwg_from_focus_<?php echo $bwg; ?>) {
            bwg_from_focus_<?php echo $bwg; ?> = false;
            return;
          }*/
          jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
          jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
          cur_img.css('opacity', 1);
          bwg_change_watermark_container_<?php echo $bwg; ?>();
          grid.remove();
          bwg_trans_in_progress_<?php echo $bwg; ?> = false;
          if (typeof event_stack_<?php echo $bwg; ?> !== 'undefined' && event_stack_<?php echo $bwg; ?>.length > 0) {
            key = event_stack_<?php echo $bwg; ?>[0].split("-");
            event_stack_<?php echo $bwg; ?>.shift();
            bwg_change_image_<?php echo $bwg; ?>(key[0], key[1], data_<?php echo $bwg; ?>, true);
          }
        }
      }
      function bwg_sliceH_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        if (direction == 'right') {
          var translateX = 'min-auto';
        }
        else if (direction == 'left') {
          var translateX = 'auto';
        }
        bwg_grid_<?php echo $bwg; ?>(1, 8, 0, translateX, 0, 1, 0, current_image_class, next_image_class, direction);
      }
      function bwg_sliceV_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        if (direction == 'right') {
          var translateY = 'min-auto';
        }
        else if (direction == 'left') {
          var translateY = 'auto';
        }
        bwg_grid_<?php echo $bwg; ?>(10, 1, 0, 0, translateY, 1, 0, current_image_class, next_image_class, direction);
      }
      function bwg_slideV_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        if (direction == 'right') {
          var translateY = 'auto';
        }
        else if (direction == 'left') {
          var translateY = 'min-auto';
        }
        bwg_grid_<?php echo $bwg; ?>(1, 1, 0, 0, translateY, 1, 1, current_image_class, next_image_class, direction);
      }
      function bwg_slideH_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        if (direction == 'right') {
          var translateX = 'min-auto';
        }
        else if (direction == 'left') {
          var translateX = 'auto';
        }
        bwg_grid_<?php echo $bwg; ?>(1, 1, 0, translateX, 0, 1, 1, current_image_class, next_image_class, direction);
      }
      function bwg_scaleOut_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        bwg_grid_<?php echo $bwg; ?>(1, 1, 0, 0, 0, 1.5, 0, current_image_class, next_image_class, direction);
      }
      function bwg_scaleIn_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        bwg_grid_<?php echo $bwg; ?>(1, 1, 0, 0, 0, 0.5, 0, current_image_class, next_image_class, direction);
      }
      function bwg_blockScale_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        bwg_grid_<?php echo $bwg; ?>(8, 6, 0, 0, 0, .6, 0, current_image_class, next_image_class, direction);
      }
      function bwg_kaleidoscope_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        bwg_grid_<?php echo $bwg; ?>(10, 8, 0, 0, 0, 1, 0, current_image_class, next_image_class, direction);
      }
      function bwg_fan_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        if (direction == 'right') {
          var rotate = 45;
          var translateX = 100;
        }
        else if (direction == 'left') {
          var rotate = -45;
          var translateX = -100;
        }
        bwg_grid_<?php echo $bwg; ?>(1, 10, rotate, translateX, 0, 1, 0, current_image_class, next_image_class, direction);
      }
      function bwg_blindV_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        bwg_grid_<?php echo $bwg; ?>(1, 8, 0, 0, 0, .7, 0, current_image_class, next_image_class);
      }
      function bwg_blindH_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        bwg_grid_<?php echo $bwg; ?>(10, 1, 0, 0, 0, .7, 0, current_image_class, next_image_class);
      }
      function bwg_random_<?php echo $bwg; ?>(current_image_class, next_image_class, direction) {
        var anims = ['sliceH', 'sliceV', 'slideH', 'slideV', 'scaleOut', 'scaleIn', 'blockScale', 'kaleidoscope', 'fan', 'blindH', 'blindV'];
        /* Pick a random transition from the anims array.*/
        this["bwg_" + anims[Math.floor(Math.random() * anims.length)] + "_<?php echo $bwg; ?>"](current_image_class, next_image_class, direction);
      }
      function iterator_<?php echo $bwg; ?>() {
        var iterator = 1;
        if (<?php echo $enable_slideshow_shuffle; ?>) {
          iterator = Math.floor((data_<?php echo $bwg; ?>.length - 1) * Math.random() + 1);
        }
        return iterator;
      }
      function bwg_change_image_<?php echo $bwg; ?>(current_key, key, data_<?php echo $bwg; ?>, from_effect) {
        /* Pause videos.*/
        jQuery("#bwg_slideshow_image_container_<?php echo $bwg; ?>").find("iframe").each(function () {
          jQuery(this)[0].contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
          jQuery(this)[0].contentWindow.postMessage('{ "method": "pause" }', "*");
        });
        if (data_<?php echo $bwg; ?>[key]) {
          if (jQuery('.bwg_ctrl_btn_<?php echo $bwg; ?>').hasClass('fa-pause')) {
            play_<?php echo $bwg; ?>();
          }
          if (!from_effect) {
            /* Change image key.*/
            jQuery("#bwg_current_image_key_<?php echo $bwg; ?>").val(key);
            if (current_key == '-1') { /* Filmstrip.*/
              current_key = jQuery(".bwg_slideshow_thumb_active_<?php echo $bwg; ?>").children("img").attr("image_key");
            }
            else if (current_key == '-2') { /* Dots.*/
              current_key = jQuery(".bwg_slideshow_dots_active_<?php echo $bwg; ?>").attr("image_key");
            }
          }
          if (bwg_trans_in_progress_<?php echo $bwg; ?>) {
            event_stack_<?php echo $bwg; ?>.push(current_key + '-' + key);
            return;
          }
          var direction = 'right';
          if (bwg_current_key_<?php echo $bwg; ?> > key) {
            var direction = 'left';
          }
          else if (bwg_current_key_<?php echo $bwg; ?> == key) {
            return;
          }
          jQuery(".bwg_slideshow_watermark_<?php echo $bwg; ?>").css({display: 'none'});
          jQuery(".bwg_slideshow_title_text_<?php echo $bwg; ?>").css({display: 'none'});
          jQuery(".bwg_slideshow_description_text_<?php echo $bwg; ?>").css({display: 'none'});
          /* Set active thumbnail position.*/
          bwg_current_filmstrip_pos_<?php echo $bwg; ?> = key * (jQuery(".bwg_slideshow_filmstrip_thumbnail_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>() + 2 + 2 * <?php echo $theme_row->lightbox_filmstrip_thumb_border_width; ?>);
          bwg_current_key_<?php echo $bwg; ?> = key;
          /* Change image id, title, description.*/
          jQuery("#bwg_slideshow_image_<?php echo $bwg; ?>").attr('image_id', data_<?php echo $bwg; ?>[key]["id"]);
          jQuery(".bwg_slideshow_title_text_<?php echo $bwg; ?>").html(jQuery('<div />').html(data_<?php echo $bwg; ?>[key]["alt"]).text());
          jQuery(".bwg_slideshow_description_text_<?php echo $bwg; ?>").html(jQuery('<div />').html(data_<?php echo $bwg; ?>[key]["description"]).text());
          var current_image_class = "#image_id_<?php echo $bwg; ?>_" + data_<?php echo $bwg; ?>[current_key]["id"];
          var next_image_class = "#image_id_<?php echo $bwg; ?>_" + data_<?php echo $bwg; ?>[key]["id"];
          bwg_<?php echo $slideshow_effect; ?>_<?php echo $bwg; ?>(current_image_class, next_image_class, direction);
          <?php
          if ($enable_slideshow_filmstrip) {
            ?>
            bwg_move_filmstrip_<?php echo $bwg; ?>();
            <?php
          }
          else {            
            ?>
            bwg_move_dots_<?php echo $bwg; ?>();
            <?php
          }
          ?>
          if (data_<?php echo $bwg; ?>[key]["is_video"]) {
            jQuery("#bwg_slideshow_play_pause_<?php echo $bwg; ?>").css({display: 'none'});
          }
          else {
            jQuery("#bwg_slideshow_play_pause_<?php echo $bwg; ?>").css({display: ''});            
          }
        }
      }
      function bwg_popup_resize_<?php echo $bwg; ?>() {
        var parent_width = jQuery(".bwg_slideshow_image_wrap_<?php echo $bwg; ?>").parent().width();
        if (parent_width >= <?php echo $image_width; ?>) {
          jQuery(".bwg_slideshow_image_wrap_<?php echo $bwg; ?>").css({width: <?php echo $image_width; ?>});
          jQuery(".bwg_slideshow_image_wrap_<?php echo $bwg; ?>").css({height: <?php echo $image_height; ?>});
          jQuery(".bwg_slideshow_image_container_<?php echo $bwg; ?>").css({width: <?php echo ($filmstrip_direction == 'horizontal' ? $image_width : $image_width - $slideshow_filmstrip_width); ?>});
          jQuery(".bwg_slideshow_image_container_<?php echo $bwg; ?>").css({height: (<?php echo ($filmstrip_direction == 'horizontal' ? $image_height - $slideshow_filmstrip_height : $image_height); ?>)});
          jQuery(".bwg_slideshow_image_<?php echo $bwg; ?>").css({
            cssText: "max-width: <?php echo ($filmstrip_direction == 'horizontal' ? $image_width : $image_width - $slideshow_filmstrip_width); ?>px !important; max-height: <?php echo ($filmstrip_direction == 'horizontal' ? $image_height - $slideshow_filmstrip_height : $image_height); ?>px !important;"
          });
          jQuery(".bwg_slideshow_video_<?php echo $bwg; ?>").css({
            cssText: "width: <?php echo ($filmstrip_direction == 'horizontal' ? $image_width : $image_width - $slideshow_filmstrip_width); ?>px !important; height: <?php echo ($filmstrip_direction == 'horizontal' ? $image_height - $slideshow_filmstrip_height : $image_height); ?>px !important;"
          });
          /* Set watermark container size.*/
          bwg_change_watermark_container_<?php echo $bwg; ?>();
          jQuery(".bwg_slideshow_filmstrip_container_<?php echo $bwg; ?>").css({<?php echo ($filmstrip_direction == 'horizontal' ? 'width: ' . $image_width : 'height: ' . $image_height); ?>});
          jQuery(".bwg_slideshow_filmstrip_<?php echo $bwg; ?>").css({<?php echo ($filmstrip_direction == 'horizontal' ? 'width: ' . ($image_width - 40) : 'height: ' . ($image_height - 40)); ?>});
          jQuery(".bwg_slideshow_dots_container_<?php echo $bwg; ?>").css({width: <?php echo $image_width; ?>});
          jQuery("#bwg_slideshow_play_pause-ico_<?php echo $bwg; ?>").css({fontSize: (<?php echo $theme_row->slideshow_play_pause_btn_size; ?>)});
          jQuery(".bwg_slideshow_watermark_image_<?php echo $bwg; ?>").css({maxWidth: <?php echo $watermark_width; ?>, maxHeight: <?php echo $watermark_height; ?>});
          jQuery(".bwg_slideshow_watermark_text_<?php echo $bwg; ?>, .bwg_slideshow_watermark_text_<?php echo $bwg; ?>:hover").css({fontSize: (<?php echo $watermark_font_size; ?>)});
          jQuery(".bwg_slideshow_title_text_<?php echo $bwg; ?>").css({fontSize: (<?php echo $theme_row->slideshow_title_font_size ; ?>)});
          jQuery(".bwg_slideshow_description_text_<?php echo $bwg; ?>").css({fontSize: (<?php echo $theme_row->slideshow_description_font_size ; ?>)});
        }
        else {
          jQuery(".bwg_slideshow_image_wrap_<?php echo $bwg; ?>").css({width: (parent_width)});
          jQuery(".bwg_slideshow_image_wrap_<?php echo $bwg; ?>").css({height: ((parent_width) * <?php echo $image_height / $image_width ?>)});
          jQuery(".bwg_slideshow_image_container_<?php echo $bwg; ?>").css({width: (parent_width - <?php echo ($filmstrip_direction == 'horizontal' ? 0 : $slideshow_filmstrip_width); ?>)});
          jQuery(".bwg_slideshow_image_container_<?php echo $bwg; ?>").css({height: ((parent_width) * <?php echo $image_height / $image_width ?> - <?php echo ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : 0); ?>)});
          jQuery(".bwg_slideshow_image_<?php echo $bwg; ?>").css({
            cssText: "max-width: " + (parent_width - <?php echo ($filmstrip_direction == 'horizontal' ? 0 : $slideshow_filmstrip_width) ?>) + "px !important; max-height: " + (parent_width * (<?php echo $image_height / $image_width ?>) - <?php echo ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : 0); ?> - 1) + "px !important;"
          });
          jQuery(".bwg_slideshow_video_<?php echo $bwg; ?>").css({
            cssText: "width: " + (parent_width - <?php echo ($filmstrip_direction == 'horizontal' ? 0 : $slideshow_filmstrip_width) ?>) + "px !important; height: " + (parent_width * (<?php echo $image_height / $image_width ?>) - <?php echo ($filmstrip_direction == 'horizontal' ? $slideshow_filmstrip_height : 0); ?> - 1) + "px !important;"
          });
          /* Set watermark container size.*/
          bwg_change_watermark_container_<?php echo $bwg; ?>();
          <?php if ($filmstrip_direction == 'horizontal') { ?>
          jQuery(".bwg_slideshow_filmstrip_container_<?php echo $bwg; ?>").css({width: (parent_width)});
          jQuery(".bwg_slideshow_filmstrip_<?php echo $bwg; ?>").css({width: (parent_width - 40)});
          <?php }
          else {
          ?>
          jQuery(".bwg_slideshow_filmstrip_container_<?php echo $bwg; ?>").css({height: (parent_width * <?php echo $image_height / $image_width ?>)});          
          jQuery(".bwg_slideshow_filmstrip_<?php echo $bwg; ?>").css({height: (parent_width * <?php echo $image_height / $image_width ?> - 40)});
          <?php
          }
          ?>
          jQuery(".bwg_slideshow_dots_container_<?php echo $bwg; ?>").css({width: (parent_width)});
          jQuery("#bwg_slideshow_play_pause-ico_<?php echo $bwg; ?>").css({fontSize: ((parent_width) * <?php echo $theme_row->slideshow_play_pause_btn_size / $image_width; ?>)});
          jQuery(".bwg_slideshow_watermark_image_<?php echo $bwg; ?>").css({maxWidth: ((parent_width) * <?php echo $watermark_width / $image_width; ?>), maxHeight: ((parent_width) * <?php echo $watermark_height / $image_width; ?>)});
          jQuery(".bwg_slideshow_watermark_text_<?php echo $bwg; ?>, .bwg_slideshow_watermark_text_<?php echo $bwg; ?>:hover").css({fontSize: ((parent_width) * <?php echo $watermark_font_size / $image_width; ?>)});
          jQuery(".bwg_slideshow_title_text_<?php echo $bwg; ?>").css({fontSize: ((parent_width) * <?php echo  $theme_row->slideshow_title_font_size / $image_width; ?>)});
          jQuery(".bwg_slideshow_description_text_<?php echo $bwg; ?>").css({fontSize: ((parent_width) * <?php echo  $theme_row->slideshow_description_font_size / $image_width; ?>)});
        }
      }
      jQuery(window).resize(function() {
        bwg_popup_resize_<?php echo $bwg; ?>();
      });
      jQuery(window).load(function () {
      	<?php
        if ($image_right_click) {
          ?>
          /* Disable right click.*/
          jQuery('div[id^="bwg_container"]').bind("contextmenu", function () {
            return false;
          });
          <?php
        }
        ?>
        if (typeof jQuery().swiperight !== 'undefined' && jQuery.isFunction(jQuery().swiperight)) {
          jQuery('#bwg_container1_<?php echo $bwg; ?>').swiperight(function () {
            bwg_change_image_<?php echo $bwg; ?>(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), (parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()) - iterator_<?php echo $bwg; ?>()) >= 0 ? (parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()) - iterator_<?php echo $bwg; ?>()) % data_<?php echo $bwg; ?>.length : data_<?php echo $bwg; ?>.length - 1, data_<?php echo $bwg; ?>);
            return false;
          });
        }
        if (typeof jQuery().swipeleft !== 'undefined' && jQuery.isFunction(jQuery().swipeleft)) {
          jQuery('#bwg_container1_<?php echo $bwg; ?>').swipeleft(function () {
            bwg_change_image_<?php echo $bwg; ?>(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), (parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()) + iterator_<?php echo $bwg; ?>()) % data_<?php echo $bwg; ?>.length, data_<?php echo $bwg; ?>);
            return false;
          });
        }

        var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));
        var bwg_click = isMobile ? 'touchend' : 'click';
        bwg_popup_resize_<?php echo $bwg; ?>();
        jQuery("#bwg_container1_<?php echo $bwg; ?>").css({visibility: 'visible'});
        jQuery(".bwg_slideshow_watermark_<?php echo $bwg; ?>").css({display: 'none'});
        jQuery(".bwg_slideshow_title_text_<?php echo $bwg; ?>").css({display: 'none'});
        jQuery(".bwg_slideshow_description_text_<?php echo $bwg; ?>").css({display: 'none'});
        setTimeout(function () {
          bwg_change_watermark_container_<?php echo $bwg; ?>();
        }, 500);
        /* Set image container height.*/
        <?php if ($filmstrip_direction == 'horizontal') { ?>
          jQuery(".bwg_slideshow_image_container_<?php echo $bwg; ?>").height(jQuery(".bwg_slideshow_image_wrap_<?php echo $bwg; ?>").height() - <?php echo $slideshow_filmstrip_height; ?>);
          <?php }
        else {
          ?>
          jQuery(".bwg_slideshow_image_container_<?php echo $bwg; ?>").width(jQuery(".bwg_slideshow_image_wrap_<?php echo $bwg; ?>").width() - <?php echo $slideshow_filmstrip_width; ?>);
          <?php
        } ?>
        var mousewheelevt = (/Firefox/i.test(navigator.userAgent)) ? "DOMMouseScroll" : "mousewheel"; /* FF doesn't recognize mousewheel as of FF3.x */
        jQuery('.bwg_slideshow_filmstrip_<?php echo $bwg; ?>').bind(mousewheelevt, function(e) {
          var evt = window.event || e; /* Equalize event object.*/
          evt = evt.originalEvent ? evt.originalEvent : evt; /* Convert to originalEvent if possible.*/
          var delta = evt.detail ? evt.detail*(-40) : evt.wheelDelta; /* Check for detail first, because it is used by Opera and FF.*/
          if (delta > 0) {
            /* Scroll up.*/
            jQuery(".bwg_slideshow_filmstrip_left_<?php echo $bwg; ?>").trigger("click");
          }
          else {
            /* Scroll down.*/
            jQuery(".bwg_slideshow_filmstrip_right_<?php echo $bwg; ?>").trigger("click");
          }
          return false;
        });
        jQuery(".bwg_slideshow_filmstrip_right_<?php echo $bwg; ?>").on(bwg_click, function () {
          jQuery( ".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>" ).stop(true, false);
          if (jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?> >= -(jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>() - jQuery(".bwg_slideshow_filmstrip_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>())) {
            jQuery(".bwg_slideshow_filmstrip_left_<?php echo $bwg; ?>").css({opacity: 1, filter: "Alpha(opacity=100)"});
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?> < -(jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>() - jQuery(".bwg_slideshow_filmstrip_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>() - <?php echo $filmstrip_thumb_margin_hor + $slideshow_filmstrip_width; ?>)) {
              jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").animate({<?php echo $left_or_top; ?>: -(jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>() - jQuery(".bwg_slideshow_filmstrip_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>())}, 500, 'linear');
            }
            else {
              jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").animate({<?php echo $left_or_top; ?>: (jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?> - <?php echo $filmstrip_thumb_margin_hor + $slideshow_filmstrip_width; ?>)}, 500, 'linear');
            }
          }
          /* Disable right arrow.*/
          window.setTimeout(function(){
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?> == -(jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>() - jQuery(".bwg_slideshow_filmstrip_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>())) {
              jQuery(".bwg_slideshow_filmstrip_right_<?php echo $bwg; ?>").css({opacity: 0.3, filter: "Alpha(opacity=30)"});
            }
          }, 500);
        });
        jQuery(".bwg_slideshow_filmstrip_left_<?php echo $bwg; ?>").on(bwg_click, function () {
          jQuery( ".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>" ).stop(true, false);
          if (jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?> < 0) {
            jQuery(".bwg_slideshow_filmstrip_right_<?php echo $bwg; ?>").css({opacity: 1, filter: "Alpha(opacity=100)"});
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?> > - <?php echo $filmstrip_thumb_margin_hor + $slideshow_filmstrip_width; ?>) {
              jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").animate({<?php echo $left_or_top; ?>: 0}, 500, 'linear');
            }
            else {
              jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").animate({<?php echo $left_or_top; ?>: (jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?> + <?php echo $filmstrip_thumb_margin_hor + $slideshow_filmstrip_width; ?>)}, 500, 'linear');
            }
          }
          /* Disable left arrow.*/
          window.setTimeout(function(){
            if (jQuery(".bwg_slideshow_filmstrip_thumbnails_<?php echo $bwg; ?>").position().<?php echo $left_or_top; ?> == 0) {
              jQuery(".bwg_slideshow_filmstrip_left_<?php echo $bwg; ?>").css({opacity: 0.3, filter: "Alpha(opacity=30)"});
            }
          }, 500);
        });
        /* Set filmstrip initial position.*/
        bwg_set_filmstrip_pos_<?php echo $bwg; ?>(jQuery(".bwg_slideshow_filmstrip_<?php echo $bwg; ?>").<?php echo $width_or_height; ?>());
        /* Play/pause.*/
        jQuery("#bwg_slideshow_play_pause_<?php echo $bwg; ?>").on(bwg_click, function () {
          if (jQuery(".bwg_ctrl_btn_<?php echo $bwg; ?>").hasClass("fa-play")) {
            play_<?php echo $bwg; ?>();
            jQuery(".bwg_slideshow_play_pause_<?php echo $bwg; ?>").attr("title", "<?php echo JText::_('PAUSE'); ?>");
            jQuery(".bwg_slideshow_play_pause_<?php echo $bwg; ?>").attr("class", "bwg_ctrl_btn_<?php echo $bwg; ?> bwg_slideshow_play_pause_<?php echo $bwg; ?> fa fa-pause");
            if (<?php echo $enable_slideshow_music ?>) {
              document.getElementById("bwg_audio_<?php echo $bwg; ?>").play();
            }
          }
          else {
            /* Pause.*/
            window.clearInterval(bwg_playInterval_<?php echo $bwg; ?>);
            jQuery(".bwg_slideshow_play_pause_<?php echo $bwg; ?>").attr("title", "<?php echo JText::_('PLAY'); ?>");
            jQuery(".bwg_slideshow_play_pause_<?php echo $bwg; ?>").attr("class", "bwg_ctrl_btn_<?php echo $bwg; ?> bwg_slideshow_play_pause_<?php echo $bwg; ?> fa fa-play");
            if (<?php echo $enable_slideshow_music ?>) {
              document.getElementById("bwg_audio_<?php echo $bwg; ?>").pause();
            }
          }
        });
        if (<?php echo $enable_slideshow_autoplay; ?>) {
          play_<?php echo $bwg; ?>();
          jQuery(".bwg_slideshow_play_pause_<?php echo $bwg; ?>").attr("title", "<?php echo JText::_('PAUSE'); ?>");
          jQuery(".bwg_slideshow_play_pause_<?php echo $bwg; ?>").attr("class", "bwg_ctrl_btn_<?php echo $bwg; ?> bwg_slideshow_play_pause_<?php echo $bwg; ?> fa fa-pause");
          if (<?php echo $enable_slideshow_music ?>) {
            document.getElementById("bwg_audio_<?php echo $bwg; ?>").play();
          }
        }
      });
      function play_<?php echo $bwg; ?>() {
        window.clearInterval(bwg_playInterval_<?php echo $bwg; ?>);
        /* Play.*/
        bwg_playInterval_<?php echo $bwg; ?> = setInterval(function () {
          var iterator = 1;
          if (<?php echo $enable_slideshow_shuffle; ?>) {
            iterator = Math.floor((data_<?php echo $bwg; ?>.length - 1) * Math.random() + 1);
          }
          bwg_change_image_<?php echo $bwg; ?>(parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()), (parseInt(jQuery('#bwg_current_image_key_<?php echo $bwg; ?>').val()) + iterator) % data_<?php echo $bwg; ?>.length, data_<?php echo $bwg; ?>)
        }, '<?php echo $slideshow_interval * 1000; ?>');
      }
      jQuery(window).focus(function() {
        /* event_stack_<?php echo $bwg; ?> = [];*/
        if (!jQuery(".bwg_ctrl_btn_<?php echo $bwg; ?>").hasClass("fa-play")) {
          play_<?php echo $bwg; ?>();
        }
        var i_<?php echo $bwg; ?> = 0;
        jQuery(".bwg_slider_<?php echo $bwg; ?>").children("span").each(function () {
          if (jQuery(this).css('opacity') == 1) {
            jQuery("#bwg_current_image_key_<?php echo $bwg; ?>").val(i_<?php echo $bwg; ?>);
          }
          i_<?php echo $bwg; ?>++;
        });
      });
      jQuery(window).blur(function() {
        event_stack_<?php echo $bwg; ?> = [];
        window.clearInterval(bwg_playInterval_<?php echo $bwg; ?>);
      });
    </script>
    <?php

	}
	
	
	
	
	function thumbnails($bwg)
	{

$WD_BWG_DIR=JPATH_BASE.'/components/com_gallery_wd';
$WD_BWG_URL=JURI::root().'components/com_gallery_wd';	

$db		=JFactory::getDBO();
$query="SHOW TABLES LIKE '#__bwg_option'";
$db->setQuery($query);


if ($db->query()) {

$query='SELECT images_directory FROM #__bwg_option WHERE id=1';
$db->setQuery($query);
$WD_BWG_UPLOAD_DIR= $db->loadResult() . '/com_gallery_wd/uploads';
}
else {
 
$WD_BWG_UPLOAD_DIR="administrator/components/com_gallery_wd/uploads";
}
	
$uri	= JFactory::getURI();
		$current_url=$uri->toString();
		$session = JFactory::getSession();
		$session->set('current_url',$current_url);
 //$bwg=0;
 $from=JRequest::getVar('from',0);
    require_once($WD_BWG_DIR . '/framework/WDWLibrary.php');
$params=$this->params;
    if (!isset($params['image_title_show_hover'])) {
      $params['image_title'] = 'none';
    }
	else
	      $params['image_title'] = $params['image_title_show_hover'];
   
   if (!isset($params['image_title'])) {
      $params['image_title'] = 'none';
    }
    if (!isset($params['popup_fullscreen'])) {
      $params['popup_fullscreen'] = 0;
    }
    if (!isset($params['popup_autoplay'])) {
      $params['popup_autoplay'] = 0;
    }
    if (!isset($params['order_by'])) {
      $params['order_by'] = ' asc ';
    }
    if (!isset($params['popup_enable_pinterest'])) {
      $params['popup_enable_pinterest'] = 0;
    }
    if (!isset($params['popup_enable_tumblr'])) {
      $params['popup_enable_tumblr'] = 0;
    }
    if (!isset($params['show_search_box'])) {
      $params['show_search_box'] = 0;
    }
    if (!isset($params['search_box_width'])) {
      $params['search_box_width'] = 180;
    }
    if (!isset($params['popup_enable_info'])) {
      $params['popup_enable_info'] = 0;
    }
    if (!isset($params['popup_info_always_show'])) {
      $params['popup_info_always_show'] = 0;
    }
    if (!isset($params['popup_enable_rate'])) {
      $params['popup_enable_rate'] = 0;
    }
    if (!isset($params['thumb_click_action']) || $params['thumb_click_action'] == 'undefined') {
      $params['thumb_click_action'] = 'open_lightbox';
    }
    if (!isset($params['thumb_link_target'])) {
      $params['thumb_link_target'] = 1;
    }
    if (!isset($params['popup_hit_counter'])) {
      $params['popup_hit_counter'] = 0;
    }
  
    if ($from) {
	$session = JFactory::getSession();
	
      $options_row = $this->get_options_row_data;

      $params['gallery_id'] = JRequest::getVar('gallery_id');
      $params['images_per_page'] =$options_row->images_per_page;
      $params['sort_by'] = (($session->get('show') == 'random') ? 'RAND()' : 'date');
	  $params['theme_id']=JRequest::getVar('theme_id');
      $params['image_enable_page'] = 0;
      $params['image_title'] = $options_row->image_title_show_hover;
      $params['thumb_height'] = $options_row->thumb_height;
      $params['thumb_width'] = $options_row->thumb_width;
      $params['image_column_number'] = $options_row->image_column_number;
      $params['popup_width'] = $options_row->popup_width;
      $params['popup_height'] = $options_row->popup_height;
      $params['popup_type'] = $options_row->popup_type;
      $params['popup_enable_filmstrip'] = $options_row->popup_enable_filmstrip;
      $params['popup_filmstrip_height'] = $options_row->popup_filmstrip_height;
      $params['popup_enable_ctrl_btn'] = $options_row->popup_enable_ctrl_btn;
      $params['popup_enable_fullscreen'] = $options_row->popup_enable_fullscreen;
      $params['popup_interval'] = $options_row->popup_interval;
      $params['popup_enable_comment'] = $options_row->popup_enable_comment;
      $params['popup_enable_facebook'] = $options_row->popup_enable_facebook;
      $params['popup_enable_twitter'] = $options_row->popup_enable_twitter;
      $params['popup_enable_google'] = $options_row->popup_enable_google;
      $params['watermark_type'] = $options_row->watermark_type;
      $params['watermark_link'] = $options_row->watermark_link;
      $params['watermark_opacity'] = $options_row->watermark_opacity;
      $params['watermark_position'] = $options_row->watermark_position;
      $params['watermark_text'] = $options_row->watermark_text;
      $params['watermark_font_size'] = $options_row->watermark_font_size;
      $params['watermark_font'] = $options_row->watermark_font;
      $params['watermark_color'] = $options_row->watermark_color;
      $params['watermark_url'] = $options_row->watermark_url;
      $params['watermark_width'] = $options_row->watermark_width;
      $params['watermark_height'] = $options_row->watermark_height;
	  $params['popup_hit_counter'] = $options_row->popup_hit_counter;
      $params['popup_enable_rate'] = $options_row->popup_enable_rate;
    }
    $theme_row = $this->get_theme_row_data;
    if (!$theme_row) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_THEME'), 'error');
      return;
    }
    if (isset($params['type'])) {
      $type = $params['type'];
    }
    else {
      $type = "";
    }
    $gallery_row = $this->get_gallery_row_data;
    if (!$gallery_row && ($type == '')) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_GALLERY'), 'error');
      return;
    }
    $image_rows = $this->get_image_rows_data;
	$images_count=count(  $image_rows);
    if (!$image_rows) {
      echo WDWLibrary::message(JText::_('THERE_IS_NO_IMAGE'), 'error');
    }
    if ($params['image_enable_page'] && $params['images_per_page']) {
      $page_nav = $this->page_nav;
    }
    $rgb_page_nav_font_color = WDWLibrary::spider_hex2rgb($theme_row->page_nav_font_color);
    $rgb_thumbs_bg_color = WDWLibrary::spider_hex2rgb($theme_row->thumbs_bg_color);

    ?>
	
    <style>
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumbnails_<?php echo $bwg; ?> * {
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun1_<?php echo $bwg; ?> {
        -moz-box-sizing: content-box;
        box-sizing: content-box;
        background-color: #<?php echo $theme_row->thumb_bg_color; ?>;
        display: inline-block;
        height: <?php echo $params['thumb_height']; ?>px;
        margin: <?php echo $theme_row->thumb_margin; ?>px;
        padding: <?php echo $theme_row->thumb_padding; ?>px;
        opacity: <?php echo $theme_row->thumb_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->thumb_transparent; ?>);
        text-align: center;
        vertical-align: middle;
        <?php echo ($theme_row->thumb_transition) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
        width: <?php echo $params['thumb_width']; ?>px;
        z-index: 100;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun1_<?php echo $bwg; ?>:hover {
        -ms-transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        -webkit-transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        -ms-backface-visibility: hidden;
        opacity: 1;
        filter: Alpha(opacity=100);
        transform: <?php echo $theme_row->thumb_hover_effect; ?>(<?php echo $theme_row->thumb_hover_effect_value; ?>);
        z-index: 102;
        position: relative;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun2_<?php echo $bwg; ?> {
        border: <?php echo $theme_row->thumb_border_width; ?>px <?php echo $theme_row->thumb_border_style; ?> #<?php echo $theme_row->thumb_border_color; ?>;
        border-radius: <?php echo $theme_row->thumb_border_radius; ?>;
        box-shadow: <?php echo $theme_row->thumb_box_shadow; ?>;
        display: inline-block;
        height: <?php echo $params['thumb_height']; ?>px;
        overflow: hidden;
        width: <?php echo $params['thumb_width']; ?>px;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumbnails_<?php echo $bwg; ?> {
        background-color: rgba(<?php echo $rgb_thumbs_bg_color['red']; ?>, <?php echo $rgb_thumbs_bg_color['green']; ?>, <?php echo $rgb_thumbs_bg_color['blue']; ?>, <?php echo $theme_row->thumb_bg_transparent / 100; ?>);
        display: inline-block;
        font-size: 0;
        max-width: <?php echo $params['image_column_number'] * ($params['thumb_width'] + 2 * (2 + $theme_row->thumb_margin + $theme_row->thumb_padding + $theme_row->thumb_border_width)); ?>px;
        text-align: <?php echo $theme_row->thumb_align; ?>;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumbnails_<?php echo $bwg; ?> a {
        cursor: pointer;
        text-decoration: none;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_<?php echo $bwg; ?> {
        display: inline-block;
        text-align: center;
      }
      <?php
      if ($params['image_title'] == 'show') { /* Show image title at the bottom.*/
        ?>
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_title_spun1_<?php echo $bwg; ?> {
          display: block;
          margin: 0 auto;
          opacity: 1;
          filter: Alpha(opacity=100);
          text-align: center;
          width: <?php echo $params['thumb_width']; ?>px;
        }
        <?php
      }
      elseif ($params['image_title'] == 'hover') { /* Show image title on hover.*/
        ?>
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_title_spun1_<?php echo $bwg; ?> {
          display: table;
          height: inherit;
          left: -3000px;
          opacity: 0;
          filter: Alpha(opacity=0);
          position: absolute;
          top: 0px;
          width: inherit;
        }
        <?php
      }
      ?>
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_standart_thumb_spun1_<?php echo $bwg; ?>:hover .bwg_title_spun1_<?php echo $bwg; ?> {
        left: <?php echo $theme_row->thumb_padding; ?>px;
        top: <?php echo $theme_row->thumb_padding; ?>px;
        opacity: 1;
        filter: Alpha(opacity=100);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .bwg_title_spun2_<?php echo $bwg; ?> {
        color: #<?php echo $theme_row->thumb_title_font_color; ?>;
        display: table-cell;
        font-family: <?php echo $theme_row->thumb_title_font_style; ?>;
        font-size: <?php echo $theme_row->thumb_title_font_size; ?>px;
        font-weight: <?php echo $theme_row->thumb_title_font_weight; ?>;
        height: inherit;
        padding: <?php echo $theme_row->thumb_title_margin; ?>;
        text-shadow: <?php echo $theme_row->thumb_title_shadow; ?>;
        vertical-align: middle;
        width: inherit;
        word-break: break-all;
        word-wrap: break-word;
      }
      /*pagination styles*/
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> {
        text-align: <?php echo $theme_row->page_nav_align; ?>;
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        margin: 6px 0 4px;
        display: block;
        height: 30px;
        line-height: 30px;
      }
      @media only screen and (max-width : 320px) {
        #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .displaying-num_<?php echo $bwg; ?> {
          display: none;
        }
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .displaying-num_<?php echo $bwg; ?> {
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        margin-right: 10px;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .paging-input_<?php echo $bwg; ?> {
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        vertical-align: middle;
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled:hover,
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a.disabled:focus {
        cursor: default;
        color: rgba(<?php echo $rgb_page_nav_font_color['red']; ?>, <?php echo $rgb_page_nav_font_color['green']; ?>, <?php echo $rgb_page_nav_font_color['blue']; ?>, 0.5);
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> .tablenav-pages_<?php echo $bwg; ?> a {
        cursor: pointer;
        font-size: <?php echo $theme_row->page_nav_font_size; ?>px;
        font-family: <?php echo $theme_row->page_nav_font_style; ?>;
        font-weight: <?php echo $theme_row->page_nav_font_weight; ?>;
        color: #<?php echo $theme_row->page_nav_font_color; ?>;
        text-decoration: none;
        padding: <?php echo $theme_row->page_nav_padding; ?>;
        margin: <?php echo $theme_row->page_nav_margin; ?>;
        border-radius: <?php echo $theme_row->page_nav_border_radius; ?>;
        border-style: <?php echo $theme_row->page_nav_border_style; ?>;
        border-width: <?php echo $theme_row->page_nav_border_width; ?>px;
        border-color: #<?php echo $theme_row->page_nav_border_color; ?>;
        background-color: #<?php echo $theme_row->page_nav_button_bg_color; ?>;
        opacity: <?php echo $theme_row->page_nav_button_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->page_nav_button_bg_transparent; ?>);
        box-shadow: <?php echo $theme_row->page_nav_box_shadow; ?>;
        <?php echo ($theme_row->page_nav_button_transition ) ? 'transition: all 0.3s ease 0s;-webkit-transition: all 0.3s ease 0s;' : ''; ?>
      }
      #bwg_container1_<?php echo $bwg; ?> #bwg_container2_<?php echo $bwg; ?> #spider_popup_overlay_<?php echo $bwg; ?> {
        background-color: #<?php echo $theme_row->lightbox_overlay_bg_color; ?>;
        opacity: <?php echo $theme_row->lightbox_overlay_bg_transparent / 100; ?>;
        filter: Alpha(opacity=<?php echo $theme_row->lightbox_overlay_bg_transparent; ?>);
      }      
    </style>

   <div id="bwg_container1_<?php echo $bwg; ?>">
      <div id="bwg_container2_<?php echo $bwg; ?>">
        <form id="gal_front_form_<?php echo $bwg; ?>" method="post" action="#">
          <?php
          if ($params['show_search_box']) {
            WDWLibrary::ajax_html_frontend_search_box('gal_front_form_' . $bwg, $bwg, 'bwg_standart_thumbnails_' . $bwg, $images_count, $params['search_box_width']);
          }
          ?>
          <div style="background-color:rgba(0, 0, 0, 0); text-align: <?php echo $theme_row->thumb_align; ?>; width:100%; position: relative;">
            <div id="ajax_loading_<?php echo $bwg; ?>" style="position:absolute;width: 100%; z-index: 115; text-align: center; height: 100%; vertical-align: middle; display:none;">
              <div style="display: table; vertical-align: middle; width: 100%; height: 100%; background-color: #FFFFFF; opacity: 0.7; filter: Alpha(opacity=70);">
                <div style="display: table-cell; text-align: center; position: relative; vertical-align: middle;" >
                  <div id="loading_div_<?php echo $bwg; ?>" style="display: inline-block; text-align:center; position:relative; vertical-align: middle;">
                    <img src="<?php echo $WD_BWG_URL . '/images/ajax_loader.png'; ?>" class="spider_ajax_loading" style="float: none; width:50px;">
                  </div>
                </div>
              </div>
            </div>
            <?php
            if ($params['image_enable_page']  && $params['images_per_page'] && ($theme_row->page_nav_position == 'top')) {
              WDWLibrary::ajax_html_frontend_page_nav($theme_row, $page_nav['total'], $page_nav['limit'], 'gal_front_form_' . $bwg, $params['images_per_page'], $bwg, 'bwg_standart_thumbnails_' . $bwg);
            }
            ?>
            <div id="bwg_standart_thumbnails_<?php echo $bwg; ?>" class="bwg_standart_thumbnails_<?php echo $bwg; ?>">
              <?php
              foreach ($image_rows as $image_row) {
			  	  		$image_row->thumb_url=htmlspecialchars($image_row->thumb_url);
			  $image_row->image_url=htmlspecialchars($image_row->image_url);
			  $image_row->alt=htmlspecialchars($image_row->alt);
			  $image_row->filename=htmlspecialchars($image_row->filename);
 
                $params_array = array(
                  'tag_id' => (isset($params['type']) ? $params['gallery_id'] : 0),
                  'view' => 'GalleryBox',
                  'current_view' => $bwg,
                  'gallery_id' => $params['gallery_id'],
                  'theme_id' => $params['theme_id'],
                  'thumb_width' => $params['thumb_width'],
                  'thumb_height' => $params['thumb_height'],
                  'open_with_fullscreen' => $params['popup_fullscreen'],
                  'open_with_autoplay' => $params['popup_autoplay'],
                  'image_width' => $params['popup_width'],
                  'image_height' => $params['popup_height'],
                  'image_effect' => $params['popup_type'],
                  'sort_by' => (isset($params['type']) ? 'date' : (($params['sort_by'] == 'RAND()') ? 'order' : $params['sort_by'])),
                  'enable_image_filmstrip' => $params['popup_enable_filmstrip'],
                  'image_filmstrip_height' => $params['popup_filmstrip_height'],
                  'enable_image_ctrl_btn' => $params['popup_enable_ctrl_btn'],
                  'enable_image_fullscreen' => $params['popup_enable_fullscreen'],
                  'popup_enable_info' => $params['popup_enable_info'],
                  'popup_info_always_show' => $params['popup_info_always_show'],
                  'popup_hit_counter' => $params['popup_hit_counter'],
                  'popup_enable_rate' => $params['popup_enable_rate'],
                  'slideshow_interval' => $params['popup_interval'],
                  'enable_comment_social' => $params['popup_enable_comment'],
                  'enable_image_facebook' => $params['popup_enable_facebook'],
                  'enable_image_twitter' => $params['popup_enable_twitter'],
                  'enable_image_google' => $params['popup_enable_google'],
                  'enable_image_pinterest' => $params['popup_enable_pinterest'],
                  'enable_image_tumblr' => $params['popup_enable_tumblr'],
                  'watermark_type' => $params['watermark_type'],
                  'current_url' => $this->bgw_url_encode($current_url)
                );
                if ($params['watermark_type'] != 'none') {
                  $params_array['watermark_link'] = $params['watermark_link'];
                  $params_array['watermark_opacity'] = $params['watermark_opacity'];
                  $params_array['watermark_position'] = $params['watermark_position'];
                }
                if ($params['watermark_type'] == 'text') {
                  $params_array['watermark_text'] = $params['watermark_text'];
                  $params_array['watermark_font_size'] = $params['watermark_font_size'];
                  $params_array['watermark_font'] = $params['watermark_font'];
                  $params_array['watermark_color'] = $params['watermark_color'];
                }
                elseif ($params['watermark_type'] == 'image') {
                  $params_array['watermark_url'] = $params['watermark_url'];
                  $params_array['watermark_width'] = $params['watermark_width'];
                  $params_array['watermark_height'] = $params['watermark_height'];
                }
                $params_array_hash = $params_array;
                $params_array['image_id'] = (isset($_POST['image_id']) ? esc_html($_POST['image_id']) : $image_row->id);
                $is_video = $image_row->filetype == "YOUTUBE" || $image_row->filetype == "VIMEO";
                if (!$is_video) {
                  list($image_thumb_width, $image_thumb_height) = getimagesize(htmlspecialchars_decode(JPATH_SITE.'/' . $WD_BWG_UPLOAD_DIR.'/' . $image_row->thumb_url, ENT_COMPAT | ENT_QUOTES));
                }
                else {
                  $image_thumb_width = $params['thumb_width'];
                  $image_thumb_height = $params['thumb_height'];
                }
                $scale = max($params['thumb_width'] / $image_thumb_width, $params['thumb_height'] / $image_thumb_height);
                $image_thumb_width *= $scale;
                $image_thumb_height *= $scale;
                $thumb_left = ($params['thumb_width'] - $image_thumb_width) / 2;
                $thumb_top = ($params['thumb_height'] - $image_thumb_height) / 2;
                ?>
                <a style="font-size: 0;" <?php echo ($params['thumb_click_action'] == 'open_lightbox' ? ('onclick="spider_createpopup(\'' . addslashes($this->array_to_url($params_array)) . '\', ' . $bwg . ', ' . $params['popup_width'] . ', ' . $params['popup_height'] . ', 1, \'testpopup\', 5); return false;"') : ('href="' . $image_row->redirect_url . '" target="' .  ($params['thumb_link_target'] ? '_blank' : '')  . '"')) ?>>
                  <span class="bwg_standart_thumb_<?php echo $bwg; ?>">
                    <?php
                    if ($params['image_title'] == 'show' and $theme_row->thumb_title_pos == 'top') {
                      ?>
                      <span class="bwg_title_spun1_<?php echo $bwg; ?>">
                        <span class="bwg_title_spun2_<?php echo $bwg; ?>">
                          <?php echo $image_row->alt; ?>
                        </span>
                      </span>
                      <?php
                    }
                    ?>
                    <span class="bwg_standart_thumb_spun1_<?php echo $bwg; ?>">
                      <span class="bwg_standart_thumb_spun2_<?php echo $bwg; ?>">
                        <img class="bwg_standart_thumb_img_<?php echo $bwg; ?>" style="max-height: none !important;  max-width: none !important; padding: 0 !important; width:<?php echo $image_thumb_width; ?>px; height:<?php echo $image_thumb_height; ?>px; margin-left: <?php echo $thumb_left; ?>px; margin-top: <?php echo $thumb_top; ?>px;" id="<?php echo $image_row->id; ?>" src="<?php echo ($is_video ? "" : JURI::root() . $WD_BWG_UPLOAD_DIR.'/') . $image_row->thumb_url; ?>" alt="<?php echo $image_row->alt; ?>" />
                        <?php
                        if ($params['image_title'] == 'hover') {
                          ?>
                          <span class="bwg_title_spun1_<?php echo $bwg; ?>">
                            <span class="bwg_title_spun2_<?php echo $bwg; ?>">
                              <?php echo $image_row->alt; ?>
                            </span>
                          </span>
                          <?php
                        }
                        ?>
                      </span>
                    </span>
                    <?php
                    if ($params['image_title'] == 'show' and $theme_row->thumb_title_pos == 'bottom') {
                      ?>
                      <span class="bwg_title_spun1_<?php echo $bwg; ?>">
                        <span class="bwg_title_spun2_<?php echo $bwg; ?>">
                          <?php echo $image_row->alt; ?>
                        </span>
                      </span>
                      <?php
                    }
                    ?>
                  </span>
                </a>
                <?php
              }
              ?>
            </div>
            <?php
            if ($params['image_enable_page']  && $params['images_per_page'] && ($theme_row->page_nav_position == 'bottom')) {
              WDWLibrary::ajax_html_frontend_page_nav($theme_row, $page_nav['total'], $page_nav['limit'], 'gal_front_form_' . $bwg, $params['images_per_page'], $bwg, 'bwg_standart_thumbnails_' . $bwg);
            }
            ?>
          </div>
        </form>
        <div id="spider_popup_loading_<?php echo $bwg; ?>" class="spider_popup_loading"></div>
        <div id="spider_popup_overlay_<?php echo $bwg; ?>" class="spider_popup_overlay" onclick="spider_destroypopup(1000)"></div>
      </div>
    </div>
    <script>
      var bwg_current_url = '<?php echo $current_url; ?>';
      <?php
      if (isset($params_array_hash)) {
      ?>
      var bwg_hash = window.location.hash.substring(1);
      if (bwg_hash && bwg_hash.indexOf("bwg") != "-1") {
        bwg_hash_array = bwg_hash.replace("bwg", "").split("/");
        spider_createpopup('<?php echo addslashes($this->array_to_url($params_array_hash)); ?>&image_id=' + bwg_hash_array[1], '<?php echo $bwg; ?>', '<?php echo $params['popup_width']; ?>', '<?php echo $params['popup_height']; ?>', 1, 'testpopup', 5);
      }
      <?php
      }
      ?>
    </script>
	<?php
	
	
	
	}
	
	

	
	
	
	
	function array_to_url($array)
	{
	$url='index.php?option=com_gallery_wd&';
	foreach($array as $key=>$params_value)
	{
	$url.=$key.'='.$params_value.'&';
	}
	
	return (substr($url,0,-1));
	}
	

	  function bgw_url_encode($url)
{
$url=str_replace(':','bwg_dots',$url);
$url=str_replace('/','bwg_slash',$url);
$url=str_replace('=','bwg_equal',$url);
$url=str_replace('&','bwg_amp',$url);
$url=str_replace('#','bwg_sharp',$url);
$url=str_replace('?','bwg_quest',$url);
return $url;

}

function bgw_url_decode($url)
{
$url=str_replace('bwg_dots',':',$url);
$url=str_replace('bwg_slash','/',$url);
$url=str_replace('bwg_equal','=',$url);
$url=str_replace('bwg_amp','&',$url);
$url=str_replace('bwg_sharp','#',$url);
$url=str_replace('bwg_quest','?',$url);
return $url;

}
	
	
	
	
	
	public function get_theme_row_data($id) {
	$db =JFactory::getDBO();
	   if ($id) {
	$query="SELECT * FROM #__bwg_theme WHERE id=".$db->escape($id);
	}
	else
	{
	$query="SELECT * FROM #__bwg_theme WHERE default_theme=1";

	}
	$db->setQuery($query);
    $row = $db->loadObject();
    return $row;
  }

  public function get_gallery_row_data($id) {
     $db =JFactory::getDBO(); 
	 $query="SELECT * FROM #__bwg_gallery WHERE published=1 AND id=".$db->escape($id);
	 $db->setQuery($query);
     $row = $db->loadObject();

    return $row;
  }
  
  
    public function get_alb_gals_row($id, $albums_per_page, $sort_by, $bwg) {
     $db =JFactory::getDBO(); 
    if (isset($_POST['page_number_' . $bwg]) && $_POST['page_number_' . $bwg]) {
      $limit = ((int) $_POST['page_number_' . $bwg] - 1) * $albums_per_page;
    }
    else {
      $limit = 0;
    }
    if ($albums_per_page) {
      $limit_str = 'LIMIT ' . $limit . ',' . $albums_per_page;
    }
    else {
      $limit_str = '';
    }
	 $query='SELECT * FROM #__bwg_album_gallery WHERE album_id='.$db->escape($id).' ORDER BY ' . ($sort_by == "RAND()" ? '' : '`') . $sort_by . ($sort_by == "RAND()" ? '' : '`') . ' ASC ' . $limit_str;
    $db->setQuery($query);
	$row = $db->loadObjectList();
    return $row;
  }
  
    
	public function get_album_row_data($id) {
     $db =JFactory::getDBO(); 
	 
	 $query="SELECT * FROM #__bwg_album WHERE published=1 AND id=".$db->escape($id);
	 $db->setQuery($query);
    $row =$db->loadObject();
    //$row->permalink = $this->bwg_create_post($row->name, $row->slug, "album", $id);
    return $row;
  }
  
  
  

   public function get_image_rows_data($id, $images_per_page, $sort_by, $bwg, $type='',$order_by="ASC") {
     $db =JFactory::getDBO();   
	 $bwg_search = ((isset($_POST['bwg_search_' . $bwg]) && htmlspecialchars($_POST['bwg_search_' . $bwg]) != '') ? htmlspecialchars($_POST['bwg_search_' . $bwg]) : '');	 
    if ($bwg_search != '') {
      $where = 'AND alt LIKE "%%' . $bwg_search . '%%"';  
      }
    else {
      $where = '';
    }
	
	 
	 if ($sort_by == 'size' || $sort_by == 'resolution') {
      $sort_by = ' CAST(' . $sort_by . ' AS SIGNED) ';
    }
    elseif (($sort_by != 'alt') && ($sort_by != 'date') && ($sort_by != 'filetype') && ($sort_by != 'RAND()') && ($sort_by != 'filename')) {
      $sort_by = '`order`';
    }
	
	if($sort_by == 'date')
	$sort_by="dateandtime";
	
	if($sort_by == 'random')
	$sort_by = 'RAND()';
	
	
	
    if (isset($_POST['page_number_' . $bwg]) && $_POST['page_number_' . $bwg]) {
      $limit = ((int) $_POST['page_number_' . $bwg] - 1) * $images_per_page;
    }
    else {
      $limit = 0;
    }
    if ($images_per_page) {
      $limit_str = 'LIMIT ' . $limit . ',' . $images_per_page;
    }
    else {
      $limit_str = '';
    }
	
	
	if($type == 'tag') {
      $query = 'SELECT image.* FROM #__bwg_image as image INNER JOIN #__bwg_image_tag as tag ON image.id=tag.image_id WHERE image.published=1 AND tag.tag_id='.$db->escape($id).' ORDER BY ' . $sort_by . ' ASC';      
    }
	else
	{
	$query='SELECT *,STR_TO_DATE(date, "%d %M %Y,%H:%i") as dateandtime  FROM #__bwg_image WHERE published=1 ' . $where . ' AND gallery_id='.$db->escape($id).' ORDER BY ' . $sort_by . ' '.$order_by.' ' . $limit_str;
	}
	$db->setQuery($query);
    $row = $db->loadObjectList();
    return $row;
  }
  
  
  
    public function gallery_page_nav($id, $images_per_page, $bwg) {
    $db =JFactory::getDBO(); 
	$query="SELECT COUNT(*) FROM #__bwg_image WHERE published=1 AND gallery_id=".$db->escape($id);
	$db->setQuery($query);
	$total=$db->loadResult();
    $page_nav['total'] = $total;
    if (isset($_POST['page_number_' . $bwg]) && $_POST['page_number_' . $bwg]) {
      $limit = ((int) $_POST['page_number_' . $bwg] - 1) * $images_per_page;
    }
    else {
      $limit = 0;
    }
    if ($images_per_page) {
      $page_nav['limit'] = (int) ($limit / $images_per_page + 1);
    }
    return $page_nav;
  }
  
  
    public function album_page_nav($id, $albums_per_page, $bwg) {
    $db =JFactory::getDBO(); 
	$query="SELECT COUNT(*) FROM #__bwg_album_gallery WHERE  album_id=".$db->escape($id);
	$db->setQuery($query);
	$total=$db->loadResult();
    $page_nav['total'] = $total;
    if (isset($_POST['page_number_' . $bwg]) && $_POST['page_number_' . $bwg]) {
      $limit = ((int) $_POST['page_number_' . $bwg] - 1) * $albums_per_page;
    }
    else {
      $limit = 0;
    }
    if ($albums_per_page) {
      $page_nav['limit'] = (int) ($limit / $albums_per_page + 1);
    }
    return $page_nav;
  }
  
  

  public function page_nav($id, $images_per_page, $bwg, $type='') {
     $db =JFactory::getDBO();  
	 
	 $bwg_search = ((isset($_POST['bwg_search_' . $bwg]) && htmlspecialchars($_POST['bwg_search_' . $bwg]) != '') ? htmlspecialchars($_POST['bwg_search_' . $bwg]) : '');
    if ($bwg_search != '') {
      $where = 'AND alt LIKE "%%' . $bwg_search . '%%"';  
    }
    else {
      $where = '';
	 }
	 
	if ($type == 'tag') {
      $query = 'SELECT COUNT(*) FROM #__bwg_image as image INNER JOIN #__bwg_image_tag as tag ON image.id=tag.image_id WHERE image.published=1 '.$where.' AND tag.tag_id='.$db->escape($id) ;
    }
	else
	{
    $query="SELECT COUNT(*) FROM #__bwg_image WHERE published=1 ".$where." AND gallery_id=".$db->escape($id) ;
	}
	$db->setQuery($query);
	$total = $db->loadResult();
    $page_nav['total'] = $total;
    if (isset($_POST['page_number_' . $bwg]) && $_POST['page_number_' . $bwg]) {
      $limit = ((int) $_POST['page_number_' . $bwg] - 1) * $images_per_page;
    }
    else {
      $limit = 0;
    }
    if ($images_per_page) {
      $page_nav['limit'] = (int) ($limit / $images_per_page + 1);
    }
    return $page_nav;
  }
  
  
    
  public function get_options_row_data() {
     $db =JFactory::getDBO();
	 $query="SELECT * FROM #__bwg_option WHERE id=1";
	 $db->setQuery($query);
	$row = $db->loadObject();
	return $row;
  }
	
	
	
	
	
	

}

?>