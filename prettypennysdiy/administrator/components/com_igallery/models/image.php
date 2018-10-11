<?php
defined('_JEXEC') or die( 'Restricted access' );

class igalleryModelimage extends igalleryModelBase
{
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		return $item;
	}
    
	function getPhoto($id)
	{
        $db	= $this->getDbo();
		$query = 'SELECT * FROM #__igallery_img WHERE id = '. (int)$id;
		$db->setQuery($query);
		$photo = $db->loadObject();
		return $photo;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
        $form = $this->loadForm('com_igallery.image', IG_ADMINISTRATOR_COMPONENT.'/models/forms/image.xml', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
        {
			return false;
		}
		
		return $form;
	}
	
	protected function loadFormData()
	{
		$data = $this->getItem();
		return $data;
	}
	
	public function getTable($type = 'igallery_img', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	protected function getReorderConditions($table = null)
	{
		$condition = array();
		$condition[] = 'gallery_id = '.(int)$table->gallery_id;
		
		return $condition;
	}
	
	function store($fileData)
	{
	    $row =& $this->getTable('igallery_img');
		$user =& JFactory::getUser();
		$configArray =& JComponentHelper::getParams('com_igallery');
		$app =& JFactory::getApplication();
		$isSite =  $app->isSite();
		
		$row->id = null;
        $row->user = $user->id;
		$row->target_blank = $configArray->get('target_blank', 1);
		$row->access = $configArray->get('new_image_access', 1);
		$row->published = $configArray->get('new_image_published', 1);
		$row->gallery_id = JRequest::getInt('catid',0);
		$row->filename = $fileData['filename'];
		$row->moderate = $configArray->get('moderate_img', 0) == 0 || $isSite == false ? 1 : 0;

		$firstLast = $configArray->get('new_image_ordering', 'last');
		$row->ordering = $firstLast == 'first' ? 0 : $row->getNextOrder('gallery_id = '.JRequest::getInt('catid',0) );
		
		if( isset($fileData['exif_des']) )
		{
			$row->description = strlen($fileData['exif_des']) > 0 ? $fileData['exif_des'] : $row->description;
		}
		
		if( isset($fileData['iptc_des']) )
		{
			$row->description = strlen($fileData['iptc_des']) > 0 ? $fileData['iptc_des'] : $row->description;
		}
			
        if( !$row->store() )
		{
		  	echo $row->getError();
		  	return false;
		}
		

		if($firstLast == 'first')
	    {
	       $row->reorder('gallery_id = '.(int)$row->gallery_id );
	    }
	    
	    $sessionValue = $app->getUserState('com_igallery.moderateimg'.$row->gallery_id, 0);
	   
	    if( $isSite && empty($sessionValue) )
	    {
			if($configArray->get('notify_new_image', 0) == 1)
			{
				$siteConfig =& JFactory::getConfig();
				$from = $siteConfig->get('config.mailfrom');
				$fromname = $siteConfig->get('config.fromname');
				$recipient = explode(',', $configArray->get('notify_emails', '') );
		    	$subject = $siteConfig->get('config.sitename').' : '.JText::_('NEW_IMAGES_ADDED');
				
				$body   = 
				JText::_('JGLOBAL_USERNAME').': '.$user->name." \n\n ".
				IG_HOST.'administrator/index.php?option=com_igallery&view=images&catid='.$row->gallery_id." \n\n ".
				IG_HOST.'index.php?option=com_igallery&view=category&igid='.$row->gallery_id;

                $mail = JFactory::getMailer();

                for($i=0; $i<count($recipient); $i++)
				{
                    $mail->sendMail($from, $fromname, $recipient[$i], $subject, $body);
					if($i > 5){break;}
				}
				
				$app->setUserState( 'com_igallery.moderateimg'.$row->gallery_id, 1);
			}
	    }
	    
	    return true;
	}
	
	function delete($cid)
	{
        $db	= $this->getDbo();
		JArrayHelper::toInteger($cid);
		
		for($i=0; $i<count($cid); $i++)
		{
		    $query = 'SELECT filename FROM #__igallery_img WHERE id = '.(int)$cid[$i];
            $db->setQuery($query);
            $photo = $db->loadObject();
            
		    $query = 'SELECT filename FROM #__igallery_img WHERE filename = '.$db->Quote($photo->filename);
    		$db->setQuery($query);
    		$db->query();
    		$numRows = $db->getNumRows();
    		$deleteOrig = $numRows > 1 ? false: true;
    		
    	    igFileHelper::deleteImage($photo->filename, $deleteOrig);

    		$query = 'DELETE FROM #__igallery_img WHERE id = '.(int)$cid[$i];
    		$db->setQuery($query);
    		if(!$db->query())
    		{
    			$this->setError($db->getErrorMsg());
    			return false;
    		}

            $commentsPath = JPATH_SITE.'/components/com_jcomments/jcomments.php';
            if (file_exists($commentsPath))
            {
                require_once($commentsPath);
                JCommentsModel::deleteComments( (int)$cid[$i], 'com_igallery');
            }

            $alPath = JPATH_SITE.'/components/com_alratings/classes/alratings.php';
            if( file_exists($alPath) )
            {
                require_once($alPath);
                ALRatings::deleteRating( 'com_igallery', (int)$cid[$i] );
            }

    	}
		
		$row =& $this->getTable('igallery_img');
    	$row->reorder('gallery_id = '.JRequest::getInt('catid',0) );
		
		return true;
	}
	
	function copy_move()
	{
        $db	= $this->getDbo();
	    $targetCat = JRequest::getInt('cat_id_copy_move');
	    if($targetCat == 0)
        {
            JError::raise(2, 500, JText::_('JERROR_NO_ITEMS_SELECTED') );
            return false; 
        }
        
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$copyMove = JRequest::getWord('copy_move','copy');
		
		$row =& $this->getTable('igallery_img');
        
        for($i=0; $i<count($cid); $i++)
		{
            $row->load( (int)$cid[$i]);
            $origId = $row->id;
            
            $row->id = null;
            $row->gallery_id = (int)$targetCat;
            $row->ordering = $row->getNextOrder('gallery_id = '.(int)$targetCat );
		   
            if(!$row->store())
            {
               $this->setError($db->getErrorMsg());
               return false;
            }

            if($copyMove == 'move')
            {
                $query = 'DELETE FROM #__igallery_img WHERE id = '.(int)$origId;
        		$db->setQuery($query);
        		if(!$db->query())
        		{
        			$this->setError($db->getErrorMsg());
        			return false;
        		}
            }
		}
		return true;
	}
	
	function add_tags()
	{
        $db	= $this->getDbo();
	    $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$tagsToAdd = JRequest::getVar('add_tags', '');
		
		if( substr($tagsToAdd, 0, 1) == ',')
		{
			$tagsToAdd = substr($tagsToAdd, 1);
		}
		
		if( substr($tagsToAdd, -1) == ',')
		{
			$tagsToAdd = substr($tagsToAdd, 0, -1);
		}
		
		$row =& $this->getTable('igallery_img');
        
        for($i=0; $i<count($cid); $i++)
		{
            $row->load( (int)$cid[$i] );
            
            $row->tags = empty($row->tags) ? $tagsToAdd : $row->tags.','.$tagsToAdd;
            
            if(!$row->store())
            {
               $this->setError($db->getErrorMsg());
               return false;
            }

		}
		return true;
	}
	
	function remove_tags()
	{
        $db	= $this->getDbo();
	    $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$tagsToRemove = JRequest::getVar('remove_tags', '');
		
		if( substr($tagsToRemove, 0, 1) == ',')
		{
			$tagsToRemove = substr($tagsToRemove, 1);
		}
		
		if( substr($tagsToRemove, -1) == ',')
		{
			$tagsToRemove = substr($tagsToRemove, 0, -1);
		}
		
		$tagsToRemoveArray = explode(',', $tagsToRemove );
		
		$row =& $this->getTable('igallery_img');
        
        for($i=0; $i<count($cid); $i++)
		{
            $row->load( (int)$cid[$i] );
            
            $currentTagsArray = explode(',', $row->tags );
            
            for($r=0; $r<count($tagsToRemoveArray); $r++)
			{
				for($c=0; $c<count($currentTagsArray); $c++)
				{
					if($tagsToRemoveArray[$r] == $currentTagsArray[$c])
					{
						unset($currentTagsArray[$c]);
					}
				}
			}
			
			$row->tags = implode(',', $currentTagsArray);
            
            if(!$row->store())
            {
               $this->setError($db->getErrorMsg());
               return false;
            }

		}
		return true;
	}
	
	function save($data)
	{
        $db	= $this->getDbo();
	    $row =& $this->getTable('igallery_img');
	    
	    if (!$row->bind($data)) 
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		if( strpos($row->description, 'class="des_div"') > 0 )
		{
			JError::raise(2, 500, 'Error: Html formatting has been copied from the gallery frontend into the description, please paste plain text');
		}

		$row->alt_text = htmlspecialchars($row->alt_text, ENT_QUOTES);

		if (!$row->store()) 
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		$query = 'SELECT gallery_id, ordering from #__igallery_img WHERE id = '.(int)JRequest::getInt('id',0);;
    	$db->setQuery($query);
    	$currentRow = $db->loadObject();
		
		$nextOrdering = $currentRow->ordering + 1;
		$query = 'SELECT id from #__igallery_img WHERE gallery_id = '.(int)$currentRow->gallery_id.' AND ordering = '.(int)$nextOrdering.' LIMIT 1';
    	$db->setQuery($query);
    	$nextRow = $db->loadObject();
    	
		return $nextRow->id;
	}
	
	function rotate()
	{
        $db	= $this->getDbo();
		$id = JRequest::getInt('id', 0);
		$rvalue = JRequest::getInt('rvalue', 0);
		
		$row =& $this->getTable('igallery_img');
		$row->load( (int)$id );
		
		if($rvalue == 0)
		{
			$row->rotation = $row->rotation == 0 ? 270 : $row->rotation - 90;
		}
		else
		{
			$row->rotation = $row->rotation == 270 ? 0 : $row->rotation + 90;
		}
		
		if (!$row->store()) 
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		return true;
	}
	
	function assignMenuImage()
	{
        $db	= $this->getDbo();
		$id = JRequest::getInt('id', 0);
		$catid = JRequest::getInt('catid', 0);
		$cvalue = JRequest::getInt('cvalue', 0);
		
		$query = 'UPDATE #__igallery_img SET menu_image = 0 WHERE gallery_id = '.(int)$catid;
		$db->setQuery($query);
		if(!$db->query())
    	{
    		$this->setError($db->getErrorMsg());
    		return false;
    	}
    	
		$query = 'UPDATE #__igallery_img SET menu_image = '.(int)$cvalue.' WHERE id = '.(int)$id;
		$db->setQuery($query);
		if(!$db->query())
    	{
    		$this->setError($db->getErrorMsg());
    		return false;
    	}
		
    	return true;
	}
	
	function moderate($moderate)
	{
        $db	= $this->getDbo();
	    $cid = JRequest::getVar( 'cid', array(), '', 'array' );
		JArrayHelper::toInteger($cid);
		
		if (count($cid))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__igallery_img SET moderate = '.(int)$moderate
			. ' WHERE id IN ( '.$cids.' )';
			$db->setQuery($query);
			if (!$db->query()) 
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
		
}