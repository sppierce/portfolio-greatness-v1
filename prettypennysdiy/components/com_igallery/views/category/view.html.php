<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class igViewcategory extends JViewLegacy
{
	function display($tpl = null)
	{
        if( !$this->checkUrl() )
        {
            return;
        }

        if(IG_J30 == true)
		{
			$this->removeCanonical();
		}

		//initialise
	    $app = JFactory::getApplication();
	    $this->user =& JFactory::getUser();
	    $this->guest = $this->user->get('guest') ? true : false;
	    $document =& JFactory::getDocument();
	    $this->params =& JComponentHelper::getParams('com_igallery');
	    $model =& $this->getModel();
        $this->languageTag = JFactory::getLanguage()->getTag();
		
	    //get request vars
	    $this->source = JRequest::getCmd('igsource', 'component');
		$catid = JRequest::getInt('igid', 0);
    	$searchChildren = JRequest::getInt('igchild', 0);
    	$tags = JRequest::getVar('igtags', '');
    	$this->type = JRequest::getCmd('igtype', 'category');
    	$profileId = JRequest::getInt('igpid', 0);
        $limit = JRequest::getInt('iglimit', 0);
        $limit = $limit == 0 ? 1000 : $limit;
        $this->Itemid = JRequest::getInt('Itemid', '');
        $this->currentUrl = & JFactory::getURI()->toString();
        $this->docTitleSet = false;
        $this->docDescriptionSet = false;
        $this->ogImageSet = false;
        $this->ogTitleSet = false;
        $this->ogDescriptionSet = false;
        $this->activeImage = 0;
        
        $uniqueid = JRequest::getCmd('iguniqueid', 0);
        $this->uniqueid = !empty($uniqueid) ? $uniqueid : $catid;
        
		if($this->source != 'component')
		{
			$overridePath = JPATH_BASE.'/templates/'.$app->getTemplate().'/html/com_igallery/'.$this->getName();
			$this->_addPath('template', $overridePath);
		}

        $this->category = $model->getCategory($catid);
		if($this->category == null)
	    {
	    	JError::raise(2, 404, JText::_('JUNPUBLISHED') );
	        return;
	    }
	    
        $profileId = $profileId == 0 ? $this->category->profile : $profileId;
		$this->profile = $model->getProfile($profileId);
		
		if($this->profile == null)
		{
			JError::raise(2, 404, JText::_('Profile Unpublished') );
			return;
		}
		
		if($this->profile->show_large_image == 0 && $this->profile->show_thumbs == 0)
		{
			JError::raise(2, 500, JText::_('Thumbs or main image display must be enabled in profile settings') );
			return;
		}

        $this->profile->refresh_mode = $this->source == 'module' ? 'javascript' : $this->profile->refresh_mode;
        $this->profile->refresh_mode = JRequest::getInt('igplugincalled', 0) == 1 ? 'javascript' : $this->profile->refresh_mode;

        if($this->source == 'plugin')
		{
			JRequest::setVar('igplugincalled', 1);
		}
		
		//check access
		$this->registerLink = $this->params->get('register_link', 'index.php?option=com_user&amp;task=register');
		if( !in_array($this->profile->access, $this->user->getAuthorisedViewLevels() ) )
		{
			if($this->profile->access == 2)
			{
				header("location: ".JRoute::_($this->registerLink) );
				return;
			}
			else
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}

        if($this->source == 'component' || $this->type == 'child_menu_images' || $this->type == 'latest_menu_images' || $this->type == 'hits_menu_images' || $this->type == 'random_menu_images')
        {
            $this->categoryChildren = $model->getCategoryChildren($catid, $this->profile, $this->type, $this->source, $limit);
        }
        else
        {
            $this->categoryChildren = null;
        }

        if($this->source == 'component' || $this->type == 'category' || $this->type == 'latest' || $this->type == 'hits' || $this->type == 'rated' || $this->type == 'random')
        {
            switch($this->type)
            {
                case 'random' : $this->photoList = $model->getRandomList($this->profile, $catid, $tags, $searchChildren, $limit);break;
                case 'latest' : $this->photoList = $model->getLatestList($this->profile, $catid, $tags, $searchChildren, $limit);break;
                case 'hits'   : $this->photoList = $model->getHitsList($this->profile, $catid, $tags, $searchChildren, $limit);break;
                case 'rated'  : $this->photoList = $model->getRatedList($this->profile, $catid, $tags, $searchChildren, $limit);break;
                default       : $this->photoList = $model->getCategoryImagesList($this->profile, $catid, $tags, $searchChildren, $limit);
            }
        }
        else
        {
            $this->photoList = null;
        }
		
    	if($this->profile->menu_pagination == 1)
    	{
	        $this->menuPagination = $model->getPagination($model->menuTotal, $this->profile->menu_pagination_amount);
    	}
    	if($this->profile->thumb_pagination == 1 && $this->photoList != null )
    	{
	        $this->thumbPagination = $model->getPagination($model->thumbTotal,$this->profile->thumb_pagination_amount);
    	}

		if( !empty($this->photoList) )
		{
		    $this->thumbFiles = array();
    	    $this->mainFiles = array();
    	    $this->lboxFiles = array();
    	    $this->lboxThumbFiles = array();
    	    
    	    $this->desVars = new stdClass();
    	    $this->desVars->mainHasDescriptions = $this->profile->show_filename == 'none' ? false : true;
    	    $this->desVars->lboxHasDescriptions = $this->profile->lbox_show_filename == 'none' ? false : true;

    		for($i=0; $i<count($this->photoList); $i++)
    		{
    		    $row =& $this->photoList[$i];

    			if(! $this->thumbFiles[$i] = igFileHelper::originalToResized($row->filename, $this->profile->thumb_width,
    		    $this->profile->thumb_height, $this->profile->img_quality, $this->profile->crop_thumbs, $row->rotation, $this->profile->round_thumb, $this->profile->round_fill) )
    		    {
    		        return false;
    		    }

    		    if(! $this->mainFiles[$i] = igFileHelper::originalToResized($row->filename, $this->profile->max_width,
    		    $this->profile->max_height, $this->profile->img_quality, $this->profile->crop_main, $row->rotation, $this->profile->round_large, $this->profile->round_fill, 
    		    $this->profile->watermark, $this->profile->watermark_text, $this->profile->watermark_text_color, $this->profile->watermark_text_size, $this->profile->watermark_filename,
    		    $this->profile->watermark_position, $this->profile->watermark_transparency, 0) )
    		    {
    		        return false;
    		    }

    		    if(! $this->lboxThumbFiles[$i] = igFileHelper::originalToResized($row->filename, $this->profile->lbox_thumb_width,
    		    $this->profile->lbox_thumb_height, $this->profile->img_quality, $this->profile->lbox_crop_thumbs, $row->rotation, $this->profile->round_thumb, $this->profile->round_fill) )
    		    {
    		        return false;
    		    }

    		    if(! $this->lboxFiles[$i] = igFileHelper::originalToResized($row->filename, $this->profile->lbox_max_width,
    		    $this->profile->lbox_max_height, $this->profile->img_quality, $this->profile->crop_lbox, $row->rotation, $this->profile->round_large, $this->profile->round_fill,
    		    $this->profile->watermark, $this->profile->watermark_text, $this->profile->watermark_text_color, $this->profile->watermark_text_size,  $this->profile->watermark_filename,
    		    $this->profile->watermark_position, $this->profile->watermark_transparency, 0) )
    		    {
    		        return false;
    		    }
    		    
    		    if( strlen($row->description) > 0 )
    		    {
    		    	$this->desVars->mainHasDescriptions = true;
    		    	$this->desVars->lboxHasDescriptions = true;
    		    }
    		}

    		$this->dimensions = igUtilityHelper::getGalleryDimensions($this->mainFiles, $this->lboxFiles, $this->thumbFiles, $this->lboxThumbFiles, $this->categoryChildren, $this->profile);
			
			if($this->profile->share_facebook == 1 || $this->profile->lbox_share_facebook == 1 || $this->profile->allow_comments == 4 || $this->profile->lbox_allow_comments == 4)
			{
				$fb_comments_userid = $this->params->get('fb_comments_userid', '');
				if( !empty($fb_comments_userid) )
				{
					$document->addCustomTag('<meta property="fb:admins" content="'.$fb_comments_userid.'" />');
				}
				
				$fb_comments_appid = $this->params->get('fb_comments_appid', '');
				if( !empty($fb_comments_appid) )
				{
					$document->addCustomTag('<meta property="fb:app_id" content="'.$fb_comments_appid.'" />');
				}
				
				if( (empty($fb_comments_userid) || empty($fb_comments_appid) ) && ($this->profile->allow_comments == 4 || $this->profile->lbox_allow_comments == 4) )
				{
					JError::raise(2, 500, JText::_('Facebook comments are On, Please enter a facebook user id and app into the gallery component options, See support common questions') );
				}
			}
				
			$imageFromUrl = JRequest::getCmd('image', '');

            if(!empty($imageFromUrl))
			{
				for($i=0; $i<count($this->photoList); $i++)
				{
					if( JFile::stripExt($this->photoList[$i]->filename) == $imageFromUrl)
					{
                        $this->activeImage = $i;

						$imagePath = IG_IMAGE_HTML_RESIZE_ABSOLUTE.$this->mainFiles[$i]['folderName'].'/'.$this->mainFiles[$i]['fullFileName'];
						$metaTag = '<meta property="og:image" content="'.$imagePath.'" />';
		                $document->addCustomTag($metaTag);
                        $this->ogImageSet = true;

                        if(!empty($this->photoList[$i]->alt_text))
                        {
                            $pageTitle = $this->photoList[$i]->alt_text;

                            if( $app->getCfg('sitename_pagetitles', 0) == 1 )
                            {
                                $pageTitle = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $pageTitle);
                            }
                            elseif($app->getCfg('sitename_pagetitles', 0) == 2)
                            {
                                $pageTitle = JText::sprintf('JPAGETITLE', $pageTitle, $app->getCfg('sitename'));
                            }

                            $document->addCustomTag('<meta property="og:title" content="'.$pageTitle.'" />');
                            $this->ogTitleSet = true;
                            $document->setTitle($pageTitle);
                            $this->docTitleSet = true;
                        }

                        if(!empty($this->photoList[$i]->description))
                        {
                            $imageDescription = $this->photoList[$i]->description;
                            $document->addCustomTag('<meta property="og:description" content="'.substr( strip_tags( str_replace('"','\'',html_entity_decode($imageDescription, ENT_QUOTES, 'UTF-8') ) ), 0, 200 ).'" />');
                            $this->ogDescriptionSet = true;

                            $document->setDescription( substr( JFilterOutput::cleanText($imageDescription), 0, 200 )  );
                            $this->docDescriptionSet = true;
                        }
						
						break;
					}
				}
			}

            $ajaxUrlImageName = JRequest::getCmd('_escaped_fragment_', '');

            if(!empty($ajaxUrlImageName))
            {
                for($i=0; $i<count($this->photoList); $i++)
                {
                    $fileNameNoExt = JFile::stripExt($this->photoList[$i]->filename);
                    $fileNameNoRef = substr($fileNameNoExt, 0, strpos($fileNameNoExt, '-') );

                    if( $fileNameNoRef == $ajaxUrlImageName)
                    {
                        $this->activeImage = $i;
                        $imagePath = IG_IMAGE_HTML_RESIZE_ABSOLUTE.$this->mainFiles[$i]['folderName'].'/'.$this->mainFiles[$i]['fullFileName'];
                        $metaTag = '<meta property="og:image" content="'.$imagePath.'" />';
                        $document->addCustomTag($metaTag);
                        $this->ogImageSet = true;

                        if(!empty($this->photoList[$i]->alt_text))
                        {
                            $pageTitle = $this->photoList[$i]->alt_text;

                            if( $app->getCfg('sitename_pagetitles', 0) == 1 )
                            {
                                $pageTitle = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $pageTitle);
                            }
                            elseif($app->getCfg('sitename_pagetitles', 0) == 2)
                            {
                                $pageTitle = JText::sprintf('JPAGETITLE', $pageTitle, $app->getCfg('sitename'));
                            }

                            $document->addCustomTag('<meta property="og:title" content="'.$pageTitle.'" />');
                            $this->ogTitleSet = true;
                            $document->setTitle($pageTitle);
                            $this->docTitleSet = true;
                        }

                        if(!empty($this->photoList[$i]->description))
                        {
                            $imageDescription = $this->photoList[$i]->description;
                            $document->addCustomTag('<meta property="og:description" content="'.substr( strip_tags( str_replace('"','\'',html_entity_decode($imageDescription, ENT_QUOTES, 'UTF-8') ) ), 0, 200 ).'" />');
                            $this->ogDescriptionSet = true;

                            $document->setDescription( substr( JFilterOutput::cleanText($imageDescription), 0, 200 )  );
                            $this->docDescriptionSet = true;
                        }

                        break;
                    }
                }
            }

            $headJs = igUtilityHelper::makeHeadJs($this->category, $this->profile, $this->photoList, $this->dimensions['galleryWidth'],
            $this->dimensions['galleryLboxWidth'], $this->mainFiles, $this->lboxFiles, $this->thumbFiles, $this->lboxThumbFiles,
            $this->source, $catid, $this->uniqueid, $this->activeImage, $this->dimensions);

            JHTML::_('behavior.framework', true);
            $document->addScript(JURI::root(true).'/media/com_igallery/js/category_mt13.js');
            $document->addScriptDeclaration($headJs);
			
		}

        if( empty($this->photoList) && !empty($this->categoryChildren) )
        {
            $this->dimensions = igUtilityHelper::getGalleryDimensions(array(), array(), array(), array(), $this->categoryChildren, $this->profile);
        }
		
		//head css
		$document->addStyleSheet(JURI::root(true).'/media/com_igallery/css/'.$this->profile->style.'.css');
		$document->addStyleSheet(JURI::root(true).'/media/com_igallery/css/category.css');

		if($this->profile->lightbox == 1)
		{
		    $document->addStyleSheet(JURI::root(true).'/media/com_igallery/css/lightbox.css');
		}

		if($this->source == 'component')
	    {
    		igUtilityHelper::writeBreadcrumbs($this->category);
    		
    		$description = $this->category->gallery_description;
			if(strlen($description) > 2)
			{
                if($this->docDescriptionSet == false)
                {
		    	    $document->setDescription( substr( JFilterOutput::cleanText($description), 0, 200 )  );
                    $this->docDescriptionSet = true;
                }
                if($this->ogDescriptionSet == false)
                {
                    $document->addCustomTag('<meta property="og:description" content="'.substr( strip_tags( str_replace('"','\'',html_entity_decode($description, ENT_QUOTES, 'UTF-8') ) ), 0, 200 ).'" />');
                    $this->ogDescriptionSet = true;
                }
			}
			
			$pageTitle = $this->category->name;
			
		    if( $app->getCfg('sitename_pagetitles', 0) == 1 )
		    {
				$pageTitle = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $pageTitle);
			}
			elseif($app->getCfg('sitename_pagetitles', 0) == 2)
			{
				$pageTitle = JText::sprintf('JPAGETITLE', $pageTitle, $app->getCfg('sitename'));
			}

            if($this->ogTitleSet == false)
            {
                $document->addCustomTag('<meta property="og:title" content="'.$pageTitle.'" />');
                $this->ogTitleSet = true;
            }
            if($this->docTitleSet == false)
            {
                $document->setTitle($pageTitle);
                $this->docTitleSet = true;
            }

            if( !empty($this->category->menu_image_filename) && $this->ogImageSet == false)
            {
                $menuImageFileArray = igFileHelper::originalToResized($this->category->menu_image_filename, $this->profile->menu_max_width,
                $this->profile->menu_max_height, $this->profile->img_quality, $this->profile->crop_menu, 0, $this->profile->round_menu, $this->profile->round_fill);

                $imagePath = IG_IMAGE_HTML_RESIZE_ABSOLUTE.$menuImageFileArray['folderName'].'/'.$menuImageFileArray['fullFileName'];
                $metaTag = '<meta property="og:image" content="'.$imagePath.'" />';
                $document->addCustomTag($metaTag);
                $this->ogImageSet = true;
            }
		}
	    
	    parent::display('main');
	}

    function checkUrl()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        if( stripos($userAgent, 'MSIE') === false && stripos($userAgent, 'Opera') === false && stripos($userAgent, 'Firefox') === false && stripos($userAgent, 'Chrome') === false && stripos($userAgent, 'Safari') === false)
        {
            return true;
        }

        $escapedFragment = JRequest::getVar('_escaped_fragment_', '');
        $fbActionIds = JRequest::getVar('fb_action_ids', '');
        $fbActionTypes = JRequest::getVar('fb_action_types', '');
        $fbSource = JRequest::getVar('fb_source', '');
        $fbAggregationId = JRequest::getVar('fb_aggregation_id', '');

        $actionObjectMap = JRequest::getVar('action_object_map', '');
        $actionTypeMap = JRequest::getVar('action_type_map', '');
        $actionRefMap = JRequest::getVar('action_ref_map', '');

        if( strlen($escapedFragment) == 0 && strlen($fbActionIds) == 0 && strlen($actionObjectMap) == 0)
        {
            return true;
        }

        $hashToAdd = '';
        $currentUrl = JFactory::getURI()->toString();
        $queryString = substr($currentUrl, (strrpos($currentUrl, '?') + 1) );
        $queryString = urldecode($queryString);
        $beforeQueryString = substr($currentUrl, 0, strrpos($currentUrl, '?') );
        $queryParams = explode('&', $queryString);

        if( strlen($escapedFragment) > 1)
        {
            $hashToAdd = $escapedFragment;
            $key = array_search('_escaped_fragment_='.$escapedFragment, $queryParams);
            if( $key !== false)
            {
                unset($queryParams[$key]);
            }
        }

        if( strlen($fbActionIds) > 1)
        {
            $key = array_search('fb_action_ids='.$fbActionIds, $queryParams);
            if( $key !== false)
            {
                unset($queryParams[$key]);
            }
        }

        if( strlen($fbActionTypes) > 1)
        {
            $key = array_search('fb_action_types='.$fbActionTypes, $queryParams);
            if( $key !== false)
            {
                unset($queryParams[$key]);
            }
        }

        if( strlen($fbSource) > 1)
        {
            $key = array_search('fb_source='.$fbSource, $queryParams);
            if( $key !== false)
            {
                unset($queryParams[$key]);
            }
        }

        if( strlen($fbAggregationId) > 1)
        {
            $key = array_search('fb_aggregation_id='.$fbAggregationId, $queryParams);
            if( $key !== false)
            {
                unset($queryParams[$key]);
            }
        }

        if( strlen($actionObjectMap) > 1)
        {
            $key = array_search('action_object_map='.$actionObjectMap, $queryParams);
            if( $key !== false)
            {
                unset($queryParams[$key]);
            }
        }

        if( strlen($actionTypeMap) > 1)
        {
            $key = array_search('action_type_map='.$actionTypeMap, $queryParams);
            if( $key !== false)
            {
                unset($queryParams[$key]);
            }
        }

        if( strlen($actionRefMap) > 1)
        {
            $key = array_search('action_ref_map='.$actionRefMap, $queryParams);
            if( $key !== false)
            {
                unset($queryParams[$key]);
            }
        }

        if( count($queryParams) )
        {
            $newQueryString = implode('&',$queryParams);
            $newUrl = $beforeQueryString.'?'.$newQueryString;
            $newUrl = strlen($hashToAdd) > 0 ? $newUrl.'#!'.$hashToAdd : $newUrl;
        }
        else
        {
            $newUrl = strlen($hashToAdd) > 0 ? $beforeQueryString.'#!'.$hashToAdd : $beforeQueryString;
        }
        JFactory::getApplication()->redirect($newUrl);
        return false;
    }

    function removeCanonical()
	{
		$document = JFactory::getDocument();

		foreach($document->_links as $key => $value)
		{
			if($value['relation'] == 'canonical')
			{
				unset($document->_links[$key]);
			}
		}

	}



}