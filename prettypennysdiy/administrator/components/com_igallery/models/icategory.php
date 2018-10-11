<?php
defined('_JEXEC') or die( 'Restricted access' );

class igalleryModelicategory extends igalleryModelBase
{
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			if(empty($item->id))
			{
				$params =& JComponentHelper::getParams('com_igallery');
				
				$item->published = $params->get('cat_published', 0);
				$item->parent    = $params->get('default_parent', 0);
				$item->profile   = $params->get('default_profile', 0);
			}
			
			$item->remove_menu_image = 0;
		}
		
		return $item;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFieldPath(IG_ADMINISTRATOR_COMPONENT.'/models/fields');
		$form = $this->loadForm('com_igallery.category', IG_ADMINISTRATOR_COMPONENT.'/models/forms/category.xml', array('control' => 'jform', 'load_data' => $loadData));
		
		if( empty($form) )
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
	
	public function getTable($type = 'igallery', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	function checkProfileExists()
	{
        $db	= $this->getDbo();
	    $query = 'SELECT id FROM #__igallery_profiles';
		$db->setQuery($query);
		$db->query();
		$numRows = $db->getNumRows();
	    if($numRows == 0)
		{
		    return false;
		}
		
		return true;
	}
	
	protected function getReorderConditions($table = null)
	{
		$condition = array();
		$condition[] = 'parent = '.(int)$table->parent;
		
		return $condition;
	}
	
	function save($data)
	{
        $db	= $this->getDbo();
		$row =& $this->getTable('igallery');
		$user =& JFactory::getUser();
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_igallery');
		$isSite =  JFactory::getApplication()->isSite();
		
		if (!$row->bind($data)) 
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		if(strlen($_FILES['jform']['name']['upload_image']) > 2 )
		{
			$fileName = $_FILES['jform']['name']['upload_image'];
			$tmpPath = $_FILES['jform']['tmp_name']['upload_image'];
			$uploadError = $_FILES['jform']['error']['upload_image'];
			
			if(!$fileArray = igFileHelper::processUploadedImage($fileName, $tmpPath, $uploadError, 'igallery', true) )
			{
				return false;
			}
			
			$row->menu_image_filename = $fileArray['filename'];
			
			$params =& JComponentHelper::getParams('com_igallery');
		}

		if(empty($row->id) ) 
		{
		    $firstLast = $params->get('new_cat_ordering', 'last');
		    $row->ordering = $firstLast == 'first' ? 0 : $row->getNextOrder('parent = '.(int)$row->parent);
		    
		    $row->profile = empty($row->profile) ? $params->get('default_profile', 1) : $row->profile;
		    $row->parent = empty($row->parent) ? $params->get('default_parent', 0) : $row->parent;
		    
		}
		
		$row->moderate = $params->get('moderate_cat', 0) == 0 || $isSite == false ? 1 : 0;
		$row->user = empty($row->user) ? $user->id : $row->user;
		$row->alias = empty($row->alias) ? JFilterOutput::stringURLSafe($row->name) : JFilterOutput::stringURLSafe($row->alias);
		$row->name = empty($row->name) ? '____' : $row->name;
		
		if($data['remove_menu_image'] == 1)
		{
		    $query = 'SELECT menu_image_filename FROM #__igallery WHERE id = '.(int)$data['id'];
            $db->setQuery($query);
            $category = $db->loadObject();
		    
		    $query = 'SELECT menu_image_filename FROM #__igallery WHERE menu_image_filename = '.$this->_db->Quote($category->menu_image_filename);
    		$db->setQuery($query);
    		$db->query();
    		$numRows = $db->getNumRows();
    		$deleteImg = $numRows > 1 ? false: true;
    		
    		if($deleteImg)
    		{
    	       igFileHelper::deleteImage($category->menu_image_filename, $deleteImg);
    		}
    	    
    	    $row->menu_image_filename =  '';
		}
		
		if(!$row->store()) 
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
		
		if(trim(str_replace('-','',$row->alias)) == '' )
		{
			$row->alias = 'category-'.(int)$row->id;
			if(!$row->store()) 
			{
				$this->setError( $db->getErrorMsg() );
				return false;
			}
		}
		
		$query = 'SELECT alias from #__igallery where alias = "'.$db->escape($row->alias).'"';
		$db->setQuery($query);
        $rows = $db->loadObjectList();
        
        if( count($rows) > 1)
        {
        	$row->alias = $row->alias.'-'.(int)$row->id;
			if(!$row->store()) 
			{
				$this->setError( $db->getErrorMsg() );
				return false;
			}
        }
		
		if($firstLast == 'first')
	    {
	       $row->reorder('parent = '.(int)$row->parent);
	    }
	    
	    if($params->get('notify_new_category', 0) == 1 && $isSite == true)
	    {
	    	$siteConfig =& JFactory::getConfig();
			$from = $siteConfig->get('config.mailfrom');
			$fromname = $siteConfig->get('config.fromname');
			$recipient = explode(',', $params->get('notify_emails', '') );
	    	$subject = $siteConfig->get('config.sitename').' : '.JText::_('NEW_CATEGORY_ADDED');
			
			$body   = 
			JText::_('JGLOBAL_USERNAME').': '.$user->name." \n\n ".
			JText::_('JCATEGORY').': '.$row->name." \n\n ".
			IG_HOST.'administrator/index.php?option=com_igallery&view=icategory&id='.$row->id." \n\n ".
			IG_HOST.'index.php?option=com_igallery&view=category&igid='.$row->id;

            $mail = JFactory::getMailer();

		    for($i=0; $i<count($recipient); $i++)
			{
                $mail->sendMail($from, $fromname, $recipient[$i], $subject, $body);
				if($i > 5){break;}
			}
	    }
	    	
		return true;
	}
	
	function delete($cid)
	{
        $db	= $this->getDbo();
		$id = is_array($cid) ? (int)$cid[0] : (int)$cid;
		
		$query = 'SELECT * FROM #__igallery WHERE id = '.(int)$id;
        $db->setQuery($query);
        $category = $db->loadObject();
        
		if( strlen($category->menu_image_filename) > 2 )
        {
            $query = 'SELECT menu_image_filename FROM #__igallery WHERE menu_image_filename = '.$this->_db->Quote($category->menu_image_filename);
    		$db->setQuery($query);
    		$db->query();
    		$numRows = $db->getNumRows();
    		$deleteOrig = $numRows > 1 ? false: true;
    		
            igFileHelper::deleteImage($category->menu_image_filename, $deleteOrig);
        }
        
        $query = 'SELECT id, filename FROM #__igallery_img WHERE gallery_id = '.(int)$id;
        $db->setQuery($query);
        $photoList = $db->loadObjectList();
        
        for($i=0; $i<count($photoList); $i++)
		{
		    $photo =& $photoList[$i];
		    
		    $query = 'SELECT filename FROM #__igallery_img WHERE filename = '.$db->Quote($photo->filename);
    		$db->setQuery($query);
    		$db->query();
    		$numRows = $db->getNumRows();
    		$deleteOrig = $numRows > 1 ? false: true;
    		
    	    igFileHelper::deleteImage($photo->filename, $deleteOrig);
            
    		$query = 'DELETE FROM #__igallery_img WHERE id = '.(int)$photo->id;
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
                JCommentsModel::deleteComments( (int)$photo->id, 'com_igallery');
            }

            $alPath = JPATH_SITE.'/components/com_alratings/classes/alratings.php';
            if( file_exists($alPath) )
            {
                require_once($alPath);
                ALRatings::deleteRating( 'com_igallery', (int)$photo->id );
            }
        }
	    
		$query = 'DELETE FROM #__igallery WHERE id = '.(int)$id;
		$db->setQuery($query);
		
		if(!$db->query())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}

		$query = 'UPDATE #__igallery SET parent = 0 WHERE parent = '.(int)$id;
		$db->setQuery($query);
		if(!$db->query())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		$row =& $this->getTable('igallery');
    	$row->reorder('parent = '.(int)$category->parent);
		
		return true;
	}
	
	function copy()
	{
        $db	= $this->getDbo();
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$id = (int)$cid[0];
		
	    $row =& $this->getTable('igallery');
        $row->load($id);
        
        $row->id = null;
        $row->ordering = $row->getNextOrder('parent = '.(int)$row->parent);
        $row->name = JText::_('COPY_OF').' '.$row->name;
        $row->alias = JFilterOutput::stringURLSafe($row->name);
        
        if (!$row->store())
    	{
    		JError::raise(2, 500, $row->getError());
    		return false;
    	}
    	
    	$catId = $db->insertid();
    	
    	$query = 'SELECT * FROM #__igallery_img WHERE gallery_id = '.(int)$id;
		$photos = $this->_getList($query);
		
		$row =& $this->getTable('igallery_img');
		
		for($i=0; $i<count($photos); $i++)
		{
		    $photo =& $photos[$i];
		    
            $row->load($photo->id);
            $row->id = null;
            $row->gallery_id = $catId;
		   
            if(!$row->store())
            {
               JError::raise(2, 500, $row->getError());
               return false;
            }
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

			$query = 'UPDATE #__igallery SET moderate = '.(int)$moderate
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