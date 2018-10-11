<?php
defined('_JEXEC') or die('Restricted access');

class igGeneralHelper
{
    function authorise($action, $catid=null, $imgId=null, $profileId=0, $ownerId=0)
    {
    	$assetName = 'com_igallery';

    	if($profileId != 0 && $ownerId != 0)
        {
            $assetName = 'com_igallery.profile.'.$profileId;
        }
        else
        {
            if(!empty($catid) )
            {
                $db	=& JFactory::getDBO();
                $query = 'SELECT * FROM #__igallery WHERE id = '.(int)$catid;
                $db->setQuery($query);
                $category = $db->loadObject();
                $assetName = 'com_igallery.profile.'.$category->profile;
                $ownerId = $category->user;
            }

            else if(!empty($imgId))
            {
                $db	=& JFactory::getDBO();
                $query = $db->getQuery(true);

                $query->select('i.gallery_id, i.user');
                $query->from('#__igallery_img AS i');

                $query->select('c.profile');
                $query->join('INNER', '`#__igallery` AS c ON c.id = i.gallery_id');

                $query->where('i.id = '. (int)$imgId);

                $db->setQuery($query);
                $row = $db->loadObject();
                $assetName = 'com_igallery.profile.'.$row->profile;
                $ownerId = $row->user;
            }

        }

        if(!JFactory::getUser()->authorise($action, $assetName))
        {
	        if($action == 'core.edit' && !empty($catid) ) 
	        {
	        	if(JFactory::getUser()->authorise('core.edit.own', $assetName))
        		{
        			if($ownerId == JFactory::getUser()->id)
        			{
        				return true;
        			}
        		}	
	        }
	        
        	if($action == 'core.edit' && !empty($imgId) )
	        {
	        	if(JFactory::getUser()->authorise('core.edit.own', $assetName))
        		{
        			if($ownerId == JFactory::getUser()->id)
        			{
        				return true;
        			}
        		}	
	        }
        	
            return false;
        }
        
        return true;
    }
    
}