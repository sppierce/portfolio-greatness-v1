<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');

class igalleryModelprofile extends JModelAdmin
{
	
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		$item->remove_wm_image = 0;
		return $item;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_igallery.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));
		
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
	
	public function getTable($type = 'igallery_profiles', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	function save($data)
	{
        $db	= $this->getDbo();
		$row =& $this->getTable('igallery_profiles');
		
		if (!$row->bind($data)) 
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		if($data['remove_wm_image'] == 1)
		{
		    $query = 'SELECT watermark_filename FROM #__igallery_profiles WHERE id = '.(int)$row->id;
            $db->setQuery($query);
            $profile = $db->loadObject();
		    
		    $query = 'SELECT watermark_filename FROM #__igallery_profiles WHERE watermark_filename = '.$db->Quote($profile->watermark_filename);
    		$db->setQuery($query);
    		$db->query();
    		$numRows = $db->getNumRows();
    		$deleteImg = $numRows > 1 ? false: true;
    		
			if($deleteImg)
			{
				JFile::delete(IG_WATERMARK_PATH.'/'.$profile->watermark_filename);
			}
    	    
    	    $row->watermark_filename =  '';
		}

		if(empty($row->id)) 
		{
			$row->ordering = $row->getNextOrder();
		}

        if (!$row->store()) 
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		return true;
	}
	
	function delete()
	{
        $db	= $this->getDbo();
	    $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		$id = (int)$cid[0];
		
		$query = 'SELECT watermark_filename FROM #__igallery_profiles WHERE id = '.$id;
        $db->setQuery($query);
        $profile = $db->loadObject();
		
        if( !empty($profile->watermark_filename) )
        {
	    	$query = 'SELECT watermark_filename FROM #__igallery_profiles WHERE watermark_filename = '.$db->Quote($profile->watermark_filename);
	    	$db->setQuery($query);
	    	$db->query();
	    	$numRows = $db->getNumRows();
	    	if($numRows < 2)
	    	{
	    	    JFile::delete(IG_WATERMARK_PATH.'/'.$profile->watermark_filename);
	    	}
        }
		
		$query = 'DELETE FROM #__igallery_profiles WHERE id = '.(int)$id;
		$db->setQuery($query);
		if(!$db->query()) 
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		$query = 'DELETE FROM #__assets WHERE name = '.$db->Quote( 'com_igallery.profile.'.(int)$id );
		$db->setQuery($query);
		if(!$db->query()) 
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		return true;
	}
	
	function copy()
	{
		$cid = JRequest::getVar('cid', array(), '', 'array');
		$id = (int)$cid[0];
		
	    $row =& $this->getTable('igallery_profiles');
	    $row->load($id);
        $row->id = null;
        $row->ordering = $row->getNextOrder();
        $row->name = JText::_('COPY_OF').' '.$row->name;
        if (!$row->store())
		{
		    $this->setError( $row->getError() );
			return false;
		}
		return true;
	}
	
	function checkAssigned()
	{
        $db	= $this->getDbo();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		$id = (int)$cid[0];
		
		$query = 'SELECT id, name from #__igallery where profile = '.(int)$id;
		$db->setQuery($query);
		$categories = $db->loadObjectlist();

		if( !empty($categories) )
		{
			$msg = '';
			for($i=0; $i<count($categories); $i++)
			{
				$msg .= ' <a href="index.php?option=com_igallery&amp;view=icategory&amp;id='.$categories[$i]->id.'">'.$categories[$i]->name.'</a> ,';
			}

			JError::raise(2, 500, JText::_('Please Assign These Categories A New Profile First').$msg);
			return false;
		}
		
		return true;
	}
	
}
