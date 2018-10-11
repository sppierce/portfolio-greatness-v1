<?php
defined('_JEXEC') or die('Restricted access');

class igUtilityHelper
{
	function getGalleryDimensions($mainFiles, $lboxFiles, $thumbFiles, $lboxThumbFiles, $categoryChildren, $profile)
	{
        /*START DEFINING OF IMAGE PADDING AND MARGIN */

        /*the padding and margin values below are in pixels, later in the script, the gallery
        will convert them to percentages*/

        if($profile->style == 'grey-border-shadow')
        {
            /*IF THE STYLE IN THE PROFILE -> GENERAL TAB IS GREY-BORDER-SHADOW, and you want to
            change the margin or padding on any of the images, please change the values below*/

            /*menu image padding/margin*/
            $menuImgPadding = 4;
            $menuImgMargin = 4;
            $menuDivPadding = 4;

            /*main image padding/margin*/
            $mainImagePadding = 4;
            $mainImageMargin = 4;

            /*thumb padding/margin*/
            $thumbPadding = 4;
            $thumbMargin = 3;

            /*lightbox image padding/margin*/
            $lboxImagePadding = 4;
            $lboxImageMargin = 4;

            /*lightbox thumb padding/margin*/
            $lboxThumbPadding = 4;
            $lboxThumbMargin = 3;

            $mainSlideShowLeftRightWidth = 22;
            $lboxSlideShowLeftRightWidth = 22;
        }
        else
        {
            /*IF THE STYLE IN THE PROFILE -> GENERAL TAB IS PLAIN, and you want to
            change the margin or padding on any of the images, please change the values below*/

            /*menu image padding/margin*/
            $menuImgPadding = 2;
            $menuImgMargin = 2;
            $menuDivPadding = 4;

            /*main image padding/margin*/
            $mainImagePadding = 0;
            $mainImageMargin = 5;

            /*thumb padding/margin*/
            $thumbPadding = 0;
            $thumbMargin = 2;

            /*lightbox iamge padding/margin*/
            $lboxImagePadding = 0;
            $lboxImageMargin = 5;

            /*lightbox thumb padding/margin*/
            $lboxThumbPadding = 0;
            $lboxThumbMargin = 2;

            $mainSlideShowLeftRightWidth = 22;
            $lboxSlideShowLeftRightWidth = 22;
        }

        /*END DEFINING OF IMAGE PADDING AND MARGIN */

        $menuWidths = igUtilityHelper::getMenuWidths($categoryChildren, $profile->columns, $menuImgPadding, $menuImgMargin, $menuDivPadding);

        //work out the largest width/height of the images
        $largestHeight = 0;
		$largestWidth = 0;
		$largestLboxHeight = 0;
		$largestLboxWidth = 0;

        for ($i=0; $i<count($mainFiles); $i++)
		{
		    $largestWidth = $mainFiles[$i]['width'] > $largestWidth ? $mainFiles[$i]['width'] : $largestWidth;
		    $largestHeight = $mainFiles[$i]['height'] > $largestHeight ? $mainFiles[$i]['height'] : $largestHeight;
		    $largestLboxWidth = $lboxFiles[$i]['width'] > $largestLboxWidth ? $lboxFiles[$i]['width'] : $largestLboxWidth;
		    $largestLboxHeight = $lboxFiles[$i]['height'] > $largestLboxHeight ? $lboxFiles[$i]['height'] : $largestLboxHeight;
        }

		//if the width/height has been manually set in the backend then override
        $mainImageDivWidth      = $profile->img_container_width > $largestWidth ? $profile->img_container_width : $largestWidth;
        $largestHeight          = $profile->img_container_height > $largestHeight ? $profile->img_container_height : $largestHeight;
        $lboxImageDivWidth      = $profile->lbox_img_container_width > $largestLboxWidth ? $profile->lbox_img_container_width : $largestLboxWidth;
        $largestLboxHeight     = $profile->lbox_img_container_height > $largestLboxHeight ? $profile->lbox_img_container_height : $largestLboxHeight;

        //add large_img padding
        $mainImageDivWidth = $mainImageDivWidth + ($mainImagePadding * 2) + ($mainImageMargin * 2);
        $lboxImageDivWidth = $lboxImageDivWidth + ($lboxImagePadding * 2) + ($lboxImageMargin * 2);

        $mainSlideShowDivWidth = $mainImageDivWidth;
        $lboxSlideShowDivWidth = $lboxImageDivWidth;

        if($profile->slideshow_position == 'left-right' && $profile->show_slideshow_controls == 1)
        {
            $mainSlideShowDivWidth = $mainSlideShowDivWidth + ($mainSlideShowLeftRightWidth * 2) + 1;
        }

        if($profile->lbox_slideshow_position == 'left-right' && $profile->lbox_show_slideshow_controls == 1)
        {
            $lboxSlideShowDivWidth = $lboxSlideShowDivWidth + ($lboxSlideShowLeftRightWidth * 2) + 1;
        }

        $thumbRowCount = $profile->images_per_row == 0 ? count($mainFiles) : $profile->images_per_row;
        $mainThumbTableWidths = igUtilityHelper::getThumbTableWidth($thumbFiles, $thumbRowCount, $thumbPadding, $thumbMargin);

        $thumbRowCount = $profile->lbox_images_per_row == 0 ? count($mainFiles) : $profile->lbox_images_per_row;
        $lboxThumbTableWidths = igUtilityHelper::getThumbTableWidth($lboxThumbFiles, $thumbRowCount, $lboxThumbPadding, $lboxThumbMargin);


        //work out the overall gallery width
        if($profile->show_large_image == 1)
        {
			$galleryWidth = $mainSlideShowDivWidth;
		}
        else
        {
        	if($profile->thumb_container_width != 0)
        	{
        		$galleryWidth = $profile->thumb_container_width;
        	}
        	else
        	{
        		$galleryWidth = $mainThumbTableWidths['overallWidth'];
        	}
        }

		//if the thumbs or descriptions are set to left/right, add on some width
		if( ($profile->photo_des_position == 'left' || $profile->photo_des_position == 'right') && $profile->show_descriptions == 1 && $profile->show_large_image == 1)
		{
		    $desWidth = $profile->photo_des_width == 0 ? 200 : $profile->photo_des_width;
		    $galleryWidth = $galleryWidth + $desWidth;
		}

		if( ($profile->thumb_position == 'left' || $profile->thumb_position == 'right') && $profile->show_thumbs == 1 && $profile->show_large_image == 1)
		{
            $extra = 4;
            $scrollBarWidth = $profile->thumb_scrollbar == 1 ? 26 : 0;

            $thumbTableWidth = $mainThumbTableWidths['overallWidth'];
            $thumbContainerWidth = $profile->thumb_container_width == 0 ? $thumbTableWidth + $scrollBarWidth + $extra : $profile->thumb_container_width;
            $thumbContainerWidthNoScroll = $thumbContainerWidth - $scrollBarWidth;

            $galleryWidth = $galleryWidth + $thumbContainerWidth;
		}
        else
        {
            $thumbTableWidth = $mainThumbTableWidths['overallWidth'];
            $thumbContainerWidth = $profile->thumb_container_width == 0 ? 0 : $profile->thumb_container_width;
            $thumbContainerWidthNoScroll = $thumbContainerWidth;
        }

        $galleryLboxWidth = $lboxSlideShowDivWidth;

		if( ($profile->lbox_photo_des_position == 'left' || $profile->lbox_photo_des_position == 'right') && $profile->lbox_show_descriptions == 1)
		{
		    $desWidth = $profile->lbox_photo_des_width == 0 ? 200 : $profile->lbox_photo_des_width;
			$galleryLboxWidth = $galleryLboxWidth + $desWidth;
		}

		if( ($profile->lbox_thumb_position == 'left' || $profile->lbox_thumb_position == 'right') && $profile->lbox_show_thumbs == 1)
		{
            $extra = 4;
            $scrollBarWidth = $profile->lbox_thumb_scrollbar == 1 ? 26 : 0;

            $lboxThumbTableWidth = $lboxThumbTableWidths['overallWidth'];
            $lboxThumbContainerWidth = $profile->lbox_thumb_container_width == 0 ? $lboxThumbTableWidth + $scrollBarWidth + $extra : $profile->lbox_thumb_container_width;
            $lboxThumbContainerWidthNoScroll = $lboxThumbContainerWidth - $scrollBarWidth;

			$galleryLboxWidth = $galleryLboxWidth + $lboxThumbContainerWidth;
		}
        else
        {
            $lboxThumbTableWidth = $lboxThumbTableWidths['overallWidth'];
            $lboxThumbContainerWidth = $profile->lbox_thumb_container_width == 0 ? 0 : $profile->lbox_thumb_container_width;
            $lboxThumbContainerWidthNoScroll = $lboxThumbContainerWidth;
        }

	    if($profile->photo_des_width > $galleryWidth && $profile->show_descriptions == 1 && ($profile->photo_des_position == 'above' || $profile->photo_des_position == 'below') )
	    {
	       $galleryWidth =  $profile->photo_des_width;
	    }

	    if($profile->thumb_container_width > $galleryWidth && $profile->show_thumbs == 1 && ($profile->thumb_position == 'above' || $profile->thumb_position == 'below'))
	    {
	       $galleryWidth =  $profile->thumb_container_width;
	    }

	    if($profile->lbox_photo_des_width > $galleryLboxWidth && $profile->lbox_show_descriptions == 1 && ($profile->lbox_photo_des_position == 'above' || $profile->lbox_photo_des_position == 'below') )
	    {
	       $galleryLboxWidth =  $profile->lbox_photo_des_width;
	    }

	    if($profile->lbox_thumb_container_width > $galleryLboxWidth && $profile->lbox_show_thumbs == 1 && ($profile->lbox_thumb_position == 'above' || $profile->lbox_thumb_position == 'below'))
	    {
	       $galleryLboxWidth =  $profile->lbox_thumb_container_width;
	    }

		$sizesArray = array();
        $sizesArray['menuOverallWidth'] = $menuWidths['overallWidth'];
        $sizesArray['menuColumns'] = $menuWidths['columns'];
        $sizesArray['menuImgPadding'] = $menuImgPadding;
        $sizesArray['menuImgMargin'] = $menuImgMargin;
        $sizesArray['menuDivPadding'] = $menuDivPadding;

        $sizesArray['largestWidth'] = $largestWidth;
		$sizesArray['largestHeight'] = $largestHeight;
        $sizesArray['mainImgPadding'] = $mainImagePadding;
        $sizesArray['mainImageMargin'] = $mainImageMargin;
        
        $sizesArray['mainImageDivWidth'] = $mainImageDivWidth;
        $sizesArray['mainSlideShowDivWidth'] = $mainSlideShowDivWidth;
        $sizesArray['galleryWidth'] = $galleryWidth;

        $sizesArray['thumbPadding'] = $thumbPadding;
        $sizesArray['thumbMargin'] = $thumbMargin;
        $sizesArray['thumbTableWidth'] = $thumbTableWidth;
        $sizesArray['thumbContainerWidth'] = $thumbContainerWidth;
        $sizesArray['thumbContainerWidthNoScroll'] = $thumbContainerWidthNoScroll;

        $sizesArray['thumbColumnWidths'] = $mainThumbTableWidths['columns'];

        $sizesArray['mainSlideShowLeftRightWidth'] = $mainSlideShowLeftRightWidth;


        //lbox
        $sizesArray['largestLboxWidth'] = $largestLboxWidth;
		$sizesArray['largestLboxHeight'] = $largestLboxHeight;
        $sizesArray['lboxImagePadding'] = $lboxImagePadding;
        $sizesArray['lboxImageMargin'] = $lboxImageMargin;
        
        $sizesArray['lboxImageDivWidth'] = $lboxImageDivWidth;
        $sizesArray['lboxSlideShowDivWidth'] = $lboxSlideShowDivWidth;
		$sizesArray['galleryLboxWidth'] = $galleryLboxWidth;

        $sizesArray['lboxThumbPadding'] = $lboxThumbPadding;
        $sizesArray['lboxThumbMargin'] = $lboxThumbMargin;
        $sizesArray['lboxThumbTableWidth'] = $lboxThumbTableWidth;
        $sizesArray['lboxThumbContainerWidth'] = $lboxThumbContainerWidth;
        $sizesArray['lboxThumbContainerWidthNoScroll'] = $lboxThumbContainerWidthNoScroll;
        $sizesArray['lboxThumbColumnWidths'] = $lboxThumbTableWidths['columns'];

        $sizesArray['lboxSlideShowLeftRightWidth'] = $lboxSlideShowLeftRightWidth;

        return $sizesArray;

	}

    function getThumbTableWidth($fileArray, $thumbRowCount, $thumbPadding, $thumbMargin)
    {
        $columnCounter = 0;
        $columnWidths = array();
        $thumbRowCount = $thumbRowCount == 0 ? 5000 : $thumbRowCount;

        for($i=0; $i<count($fileArray); $i++)
        {
            if(!isset($columnWidths[$columnCounter]))
            {
                $columnWidths[$columnCounter] = 0;
            }

            if($fileArray[$i]['width'] > $columnWidths[$columnCounter] )
            {
                $columnWidths[$columnCounter] = $fileArray[$i]['width'] + ($thumbPadding * 2) + ($thumbMargin * 2);
            }

            $columnCounter++;

            if( $columnCounter == $thumbRowCount)
            {
                $columnCounter = 0;
            }
        }

        $thumbTableWidths = array();
        $thumbTableWidths['overallWidth'] = array_sum($columnWidths);
        $thumbTableWidths['columns'] = $columnWidths;

        return $thumbTableWidths;
    }

    function getMenuWidths($categoryChildren, $columns, $menuImgPadding, $menuImgMargin, $menuDivPadding)
    {
        $columnCounter = 0;
        $columnWidths = array();
        $columns = $columns == 0 ? 5000 : $columns;

        if( count($categoryChildren) )
        {
            for($i=0; $i<count($categoryChildren); $i++)
            {
                if(!isset($columnWidths[$columnCounter]))
                {
                    $columnWidths[$columnCounter] = 0;
                }

                $currentWidth = isset($categoryChildren[$i]->fileArray['width']) ? $categoryChildren[$i]->fileArray['width'] : $categoryChildren[$i]->menu_max_width;

                if($currentWidth > $columnWidths[$columnCounter] )
                {
                    $columnWidths[$columnCounter] = $currentWidth + ($menuImgPadding * 2) + ($menuImgMargin * 2) + ($menuDivPadding * 2);
                }

                $columnCounter++;

                if( $columnCounter == $columns)
                {
                    $columnCounter = 0;
                }
            }

            $menuWidths = array();
            $menuWidths['overallWidth'] = array_sum($columnWidths);
            $menuWidths['columns'] = $columnWidths;
        }
        else
        {
            $menuWidths = array();
            $menuWidths['overallWidth'] = 0;
            $menuWidths['columns'] = array();
        }

        return $menuWidths;
    }
	
	function writeBreadcrumbs($category)
	{
        $app	= JFactory::getApplication();
        $pathway = $app->getPathway();
        $breadCrumbNames = array();
        $breadCrumbLinks = array();
        $Itemid = JRequest::getInt('Itemid', 0);
        $catIdOfActive = 0;

        if( $Itemid > 0 )
        {
            $menu	= $app->getMenu();
            $activeMenu = $menu->getItem($Itemid);
            if( isset($activeMenu->query['igid']) )
            {
                $catIdOfActive = $activeMenu->query['igid'];
            }
        }

        $categories = igStaticHelper::getCategories();
        $parents = igTreeHelper::getParentPath($categories, $category->id, true);

        foreach($parents as $parent)
        {
            if($parent->id != $catIdOfActive)
            {
                array_unshift($breadCrumbNames, $parent->name);
                if($parent->id != $category->id)
                {
                    array_unshift($breadCrumbLinks, JRoute::_('index.php?option=com_igallery&view=category&igid='.$parent->id.'&Itemid='.JRequest::getInt('Itemid', '') ) );
                }
                else
                {
                    array_unshift($breadCrumbLinks, '');
                }
            }
            else
            {
                break;
            }
        }

        for($i=0; $i<count($breadCrumbNames); $i++)
        {
            $pathway->addItem($breadCrumbNames[$i], $breadCrumbLinks[$i]);
        }
	}
	
	function getItemid($catId)
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$menuItems = $menu->getMenu();
        $categories = igStaticHelper::getCategories();
        $parents = igTreeHelper::getParentPath($categories, $catId, false);

		foreach($menuItems as $menu)
		{
			if($menu->component == 'com_igallery' && $menu->query['view'] == 'category' && $menu->query['igid'] == $catId)
			{
                return $menu->id;
            }
        }

        foreach($menuItems as $menu)
        {
            if($menu->component == 'com_igallery' && $menu->query['view'] == 'category')
            {
                foreach($parents as $parent)
                {
                    if($parent->id == $menu->query['igid'])
                    return $menu->id;
                }
            }
        }

        return JRequest::getVar('Itemid', 1);
	}
	
	function makeHeadJs($category, $profile, $photoList, $galleryWidth,
	$galleryLboxWidth, $mainFiles, $lboxFiles, $thumbFiles, $lboxThumbFiles,
	$source, $catid, $uniqueid, $activeImage, $dimensions)
	{
		
		$params =& JComponentHelper::getParams('com_igallery');
		
		$headJs = '
//make arrays and classes for the category: '.$category->name.' (id= '.$catid.')
window.addEvent(\'load\', function()
{
if( !!(document.id(\'main_images_wrapper'.$uniqueid.'\') || document.id(\'main_images_wrapper'.$uniqueid.'\') === 0 ) )
{
';

		$headJs .= 		'
	var idArray'.$uniqueid.' = [';
		for($n=0;$n<count($photoList);$n++)
		{
			$headJs .= $photoList[$n]->id.',';
		}
		$headJs = substr($headJs,0,-1);
		$headJs .= '];
		';

		$headJs .= '
	var jsonImagesObject'.$uniqueid.' =
	{';
		
		$headJs .= '
		"general":
		[
		';

		for($i=0; $i < count($photoList); $i++)
		{
			$row =& $photoList[$i];

			if(JRequest::getInt('igaddlinks', 0) == 1)
        	{
        		if(strlen($row->link) < 2)
        		{
	        		$limitStart = '';
					if($row->thumb_pagination == 1)
					{
						if($row->ordering > $row->thumb_pagination_amount)
						{
							$group = ceil( $row->ordering / $row->thumb_pagination_amount ) - 1;
							
							if($group > 0)
							{
								$limitStart = '&limitstart='.($group * $row->thumb_pagination_amount);
							}
						}
					}

                    $fileHashNoExt = JFile::stripExt($row->filename);
                    $fileHashNoRef = substr($fileHashNoExt, 0, strrpos($fileHashNoExt, '-') );

        			$linkItemid = igUtilityHelper::getItemid($row->gallery_id);
        			$photoList[$i]->link = JRoute::_('index.php?option=com_igallery&view=category&igid='.$row->gallery_id.'&Itemid='.$linkItemid.$limitStart.'#!'.$fileHashNoRef, false);
        			$photoList[$i]->target_blank = 0;
        		}
        	}
			$headJs .= '	{"filename": "'.$photoList[$i]->filename.'", "url": "'.$photoList[$i]->link.'", "targetBlank": '.$photoList[$i]->target_blank.', "hits": '.$photoList[$i]->hits.', "alt": "'.$photoList[$i]->alt_text.'", "ratingAverage": "'.(empty($photoList[$i]->rating_average) ? 0 : $photoList[$i]->rating_average).'", "ratingCount": "'.(isset($photoList[$i]->rating_count) ? $photoList[$i]->rating_count : 0).'"}';
			if($i < (count($photoList) - 1) ){$headJs .= ',
		';}
		}
		
		$headJs .= '
		]';
		
		if($profile->show_large_image == 1)
		{
			$headJs .= ',
		
		"main":
		[
		';

		for($i=0; $i<count($photoList); $i++)
		{
			$headJs .= '	{"filename": "'.$mainFiles[$i]['folderName'].'/'.$mainFiles[$i]['fullFileName'].'", "width": '.$mainFiles[$i]['width'].', "height": '.$mainFiles[$i]['height'].'}';
			if($i < (count($photoList) - 1) ){$headJs .= ',
		';}
		}
		$headJs .= '
		]';
		}
		
		if($profile->show_thumbs == 1)
		{
		$headJs .= ',

		"thumbs":
		[
		';

		for($i=0; $i < count($photoList); $i++)
		{
			$headJs .= '	{"filename": "'.$thumbFiles[$i]['folderName'].'/'.$thumbFiles[$i]['fullFileName'].'", "width": '.$thumbFiles[$i]['width'].', "height": '.$thumbFiles[$i]['height'].'}';
			if($i < (count($photoList) - 1) ){$headJs .= ',
		';}
		}
		$headJs .= '
		]
		';
		}
		
		if($profile->lightbox == 1)
		{
		$headJs .= ',

		"lbox":
		[
		';

		for($i=0; $i < count($photoList); $i++)
		{
			$headJs .= '	{"filename": "'.$lboxFiles[$i]['folderName'].'/'.$lboxFiles[$i]['fullFileName'].'", "width": '.$lboxFiles[$i]['width'].', "height": '.$lboxFiles[$i]['height'].'}';
		if($i < (count($photoList) - 1) ){$headJs .= ',
		';}
		}
		$headJs .= '
		]';
		}
		
		if($profile->lbox_show_thumbs == 1  && $profile->lightbox == 1)
		{
		$headJs .= ',

		"lboxThumbs":
		[
		';

		for($i=0; $i < count($photoList); $i++)
		{
			$headJs .= '	{"filename": "'.$lboxThumbFiles[$i]['folderName'].'/'.$lboxThumbFiles[$i]['fullFileName'].'", "width": '.$lboxThumbFiles[$i]['width'].', "height": '.$lboxThumbFiles[$i]['height'].'}';
			if($i < (count($photoList) - 1) ){$headJs .= ',
		';}
		}
		$headJs .= '
		]
		';
		}
		
		$headJs .= '
	};

	var igalleryInt = new Class(igalleryClass);

	var igalleryMain'.$uniqueid.' = new igalleryInt
	({
	    activeImage: '.$activeImage.',
		allowComments: '.$profile->allow_comments.',
		allowRating: '.$profile->allow_rating.',
		ratingsContainer: \'main_ratings_container'.$uniqueid.'\',
		catid: \''.$catid.'\',
		calledFrom: \''.$source.'\',
		collectImageViews: '.$params->get('image_view_stats', 1).',
		desContainer: \'main_des_container'.$uniqueid.'\',
		desPostion: \''.$profile->photo_des_position.'\',
		downArrow: \'main_down_arrow'.$uniqueid.'\',
		downloadType: \''.$profile->download_image.'\',
		downloadId: \'main_download_button'.$uniqueid.'\',
		host: \''.IG_HOST.'\',
		fadeDuration: '.$profile->fade_duration.',
		facebookContainer: \'main_facebook_share'.$uniqueid.'\',
		facebookTempContainer: \'main_facebook_share_temp'.$uniqueid.'\',
		facebookShare: '.$profile->share_facebook.',
		facebookCommentsContainer: \'main_fbcomments'.$uniqueid.'\',
		facebookCommentsNumPosts: '.$params->get('fb_comments_postcount', 5).',
		fbLikeWidth: '.$params->get('fb_like_width', 80).',
		facebookColor: \''.$params->get('fb_colorscheme', 'light').'\',
		facebookAppid: '.$params->get('fb_comments_appid', 0).',
		facebookLegacy: '.$params->get('fb_legacy_urls', 0).',
		galleryWidth: '.$galleryWidth.',
        idArray: idArray'.$uniqueid.',
        imageSlideshowContainer: \'main_image_slideshow_wrapper'.$uniqueid.'\',
		imageAssetPath: \''.IG_IMAGE_ASSET_PATH.'\',
		imageAlignHoriz: \''.$profile->main_image_align_horiz.'\',
		imageAlignVert: \''.$profile->main_image_align_vert.'\',
		jCommentsMain: \'main_jcomments_wrapper'.$uniqueid.'\',
		jCommentsLbox: \'lbox_jcomments_wrapper'.$uniqueid.'\',
		joomCommentContainer: \'main_joomcomment_wrapper'.$uniqueid.'\',
		jsonImages: jsonImagesObject'.$uniqueid.',
		jsonImagesImageType: jsonImagesObject'.$uniqueid.'.main,
		largeImage: \'main_large_image'.$uniqueid.'\',
		largestWidth: '.$dimensions['largestWidth'].',
		largestHeight: '.$dimensions['largestHeight'].',
		largeImageDivWidth: '.$dimensions['mainImageDivWidth'].',
		largeImagePadding: '.$dimensions['mainImgPadding'].',
		largeImageMargin: '.$dimensions['mainImageMargin'].',
		lboxDark: \'lbox_dark'.$uniqueid.'\',
		lboxWhite: \'lbox_white'.$uniqueid.'\',
		lboxScalable: '.$profile->lbox_scalable.',
		lightboxWidth: '.$galleryLboxWidth.',
		lightboxOn: '.$profile->lightbox.',
		leftArrow: \'main_left_arrow'.$uniqueid.'\',
		magnify: '.$profile->magnify.',
		main: 1,
		mainWrapper: \'main_images_wrapper'.$uniqueid.'\',
		numPics: '.count($photoList).',
		numberingOn: '.$profile->show_numbering.',
		numberingContainer: \'main_img_numbering'.$uniqueid.'\',
		showPlusOne: '.$profile->plus_one.',
		plusOneDiv: \'main_plus_one_div'.$uniqueid.'\',
		preload: '.$profile->preload.',
		prefix: \'main\',
		resizePath: \''.IG_IMAGE_HTML_RESIZE.'\',
		refreshMode: \''.$profile->refresh_mode.'\',
		reportImage: '.$profile->report_image.',
		reportContainer: \'main_report'.$uniqueid.'\',
		rightArrow: \'main_right_arrow'.$uniqueid.'\',
		scrollBoundary: '.$profile->scroll_boundary.',
		scrollSpeed: '.$profile->scroll_speed.',
		showDescriptions: '.$profile->show_descriptions.',
		showUpDown: '.$profile->arrows_up_down.',
		showLeftRight: '.$profile->arrows_left_right.',
		showLargeImage: '.$profile->show_large_image.',
		showThumbs: '.$profile->show_thumbs.',
		showSlideshowControls: '.$profile->show_slideshow_controls.',
		slideshowPosition: \''.$profile->slideshow_position.'\',
		showTags: '.$profile->show_tags.',
		slideshowAutostart: '.$profile->slideshow_autostart.',
		slideshowPause: '.$profile->slideshow_pause.',
		slideshowForward: \'slideshow_forward'.$uniqueid.'\',
		slideshowPlay: \'slideshow_play'.$uniqueid.'\',
		slideshowRewind: \'slideshow_rewind'.$uniqueid.'\',
		tagsContainer: \'main_tags_container'.$uniqueid.'\',
		thumbContainer: \'main_thumb_container'.$uniqueid.'\',
		thumbPostion: \''.$profile->thumb_position.'\',
		thumbTable: \'main_thumb_table'.$uniqueid.'\',
		twitterButton: '.$profile->twitter_button.',
		twitterButtonDiv: \'main_twitter_button'.$uniqueid.'\',
		uniqueid: \''.$uniqueid.'\',
		style: \''.$profile->style.'\',
		upArrow: \'main_up_arrow'.$uniqueid.'\',
		ref: \'35\'
	});
	';
	if($profile->lightbox == 1)
	{
		$headJs .= '
	var igalleryLbox'.$uniqueid.' = new igalleryInt
	({
	    activeImage: '.$activeImage.',
		allowComments: '.$profile->lbox_allow_comments.',
		allowRating: '.$profile->lbox_allow_rating.',
		ratingsContainer: \'lbox_ratings_container'.$uniqueid.'\',
		catid: \''.$catid.'\',
		calledFrom: \''.$source.'\',
		collectImageViews: '.$params->get('image_view_stats', 1).',
		closeImage: \'closeImage'.$uniqueid.'\',
		desContainer: \'lbox_des_container'.$uniqueid.'\',
		desPostion: \''.$profile->lbox_photo_des_position.'\',
		downArrow: \'lbox_down_arrow'.$uniqueid.'\',
		downloadId: \'lbox_download_button'.$uniqueid.'\',
		downloadType: \''.$profile->lbox_download_image.'\',
		host: \''.IG_HOST.'\',
		fadeDuration: '.$profile->lbox_fade_duration.',
		facebookShare: '.$profile->lbox_share_facebook.',
		facebookContainer: \'lbox_facebook_share'.$uniqueid.'\',
		facebookTempContainer: \'lbox_facebook_share_temp'.$uniqueid.'\',
		facebookCommentsContainer: \'lbox_fbcomments'.$uniqueid.'\',
		facebookCommentsNumPosts: '.$params->get('fb_comments_postcount', 5).',
		fbLikeWidth: '.$params->get('fb_like_width', 80).',
		facebookAppid: '.$params->get('fb_comments_appid', 0).',
		facebookLegacy: '.$params->get('fb_legacy_urls', 0).',
		facebookColor: \''.$params->get('fb_colorscheme', 'light').'\',
		idArray: idArray'.$uniqueid.',
		imageSlideshowContainer: \'lbox_image_slideshow_wrapper'.$uniqueid.'\',
		imageAssetPath: \''.IG_IMAGE_ASSET_PATH.'\',
		imageAlignHoriz: \''.$profile->lbox_image_align_horiz.'\',
		imageAlignVert: \''.$profile->lbox_image_align_vert.'\',
		jsonImages: jsonImagesObject'.$uniqueid.',
		jsonImagesImageType: jsonImagesObject'.$uniqueid.'.lbox,
		joomCommentContainer: \'lbox_joomcomment_wrapper'.$uniqueid.'\',
		largeImage: \'lbox_large_image'.$uniqueid.'\',
		largestWidth: '.$dimensions['largestLboxWidth'].',
		largestHeight: '.$dimensions['largestLboxHeight'].',
		largeImageDivWidth: '.$dimensions['lboxImageDivWidth'].',
		largeImagePadding: '.$dimensions['lboxImagePadding'].',
		largeImageMargin: '.$dimensions['lboxImageMargin'].',
		lboxDark: \'lbox_dark'.$uniqueid.'\',
		lboxWhite: \'lbox_white'.$uniqueid.'\',
		lboxScalable: '.$profile->lbox_scalable.',
		lightboxWidth: '.$galleryLboxWidth.',
		leftArrow: \'lbox_left_arrow'.$uniqueid.'\',
		lightboxOn: '.$profile->lightbox.',
		mainWrapper: \'lbox_white'.$uniqueid.'\',
		magnify: '.$profile->magnify.',
		main: 0,
		numPics: '.count($photoList).',
		numberingOn: '.$profile->lbox_show_numbering.',
		numberingContainer: \'lbox_img_numbering'.$uniqueid.'\',
		showPlusOne: '.$profile->lbox_plus_one.',
		plusOneDiv: \'lbox_plus_one_div'.$uniqueid.'\',
		preload: '.$profile->lbox_preload.',
		prefix: \'lbox\',
		resizePath: \''.IG_IMAGE_HTML_RESIZE.'\',
		reportImage: '.$profile->lbox_report_image.',
		reportContainer: \'lbox_report'.$uniqueid.'\',
		rightArrow: \'lbox_right_arrow'.$uniqueid.'\',
		refreshMode: \''.$profile->refresh_mode.'\',
		scrollBoundary: '.$profile->lbox_scroll_boundary.',
		scrollSpeed: '.$profile->lbox_scroll_speed.',
		showDescriptions: '.$profile->lbox_show_descriptions.',
		showUpDown: '.$profile->lbox_arrows_up_down.',
		showLeftRight: '.$profile->lbox_arrows_left_right.',
		showLargeImage: 1,
		showThumbs: '.$profile->lbox_show_thumbs.',
		showTags: '.$profile->lbox_show_tags.',
		showSlideshowControls: '.$profile->lbox_show_slideshow_controls.',
		slideshowPosition: \''.$profile->lbox_slideshow_position.'\',
		slideshowAutostart: '.$profile->lbox_slideshow_autostart.',
		slideshowForward: \'lbox_slideshow_forward'.$uniqueid.'\',
		slideshowPlay: \'lbox_slideshow_play'.$uniqueid.'\',
		slideshowRewind: \'lbox_slideshow_rewind'.$uniqueid.'\',
		slideshowPause: '.$profile->lbox_slideshow_pause.',
		style: \''.$profile->style.'\',
		tagsContainer: \'lbox_tags_container'.$uniqueid.'\',
		thumbContainer: \'lbox_thumb_container'.$uniqueid.'\',
		thumbPostion: \''.$profile->lbox_thumb_position.'\',
		thumbTable: \'lbox_thumb_table'.$uniqueid.'\',
		twitterButton: '.$profile->lbox_twitter_button.',
		twitterButtonDiv: \'lbox_twitter_button'.$uniqueid.'\',
		uniqueid: \''.$uniqueid.'\',
		upArrow: \'lbox_up_arrow'.$uniqueid.'\'
	});

	igalleryMain'.$uniqueid.'.lboxGalleryObject = igalleryLbox'.$uniqueid.';
	igalleryLbox'.$uniqueid.'.mainGalleryObject = igalleryMain'.$uniqueid.';
	';
	}
	$headJs .= '
}
});

';

		return $headJs;
	}
}