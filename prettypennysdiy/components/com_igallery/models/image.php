<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');

class igModelimage extends JModelAdmin
{
	
	public function getTable($type = 'igallery_img', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		return;
	}
	
	function getPhoto($id)
	{
        $db	= $this->getDbo();
		$query = 'SELECT * FROM #__igallery_img WHERE id = '. (int)$id;
		$db->setQuery($query);
		$photo = $db->loadObject();
		return $photo;
	}
	
	function getCategory($id)
	{
        $db	= $this->getDbo();
		$query = 'SELECT * FROM #__igallery WHERE id = '.(int)$id;
		$db->setQuery($query);
		$category = $db->loadObject();
	    return $category;
    }
    
	function getProfile($id)
	{
        $db	= $this->getDbo();
		$query = 'SELECT * FROM #__igallery_profiles WHERE id = '.(int)$id;
		$db->setQuery($query);
		$profile = $db->loadObject();
	    return $profile;
    }
	
	function addHit($id)
	{
        $db	= $this->getDbo();
		$row =& $this->getTable('igallery_img');
		$row->load( (int)$id );
		$row->hits = $row->hits + 1;
		if(!$row->store())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		return true;
	}
	
	function reportImage()
	{
        $mail = JFactory::getMailer();
		$id = JRequest::getInt('id', 0);
		$text = JRequest::getVar('report_textarea', '');
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();
		
		$siteConfig =& JFactory::getConfig();
		$from = $siteConfig->get( 'config.mailfrom' );
		$fromname = $siteConfig->get( 'config.fromname' );
		
		$igalleryConfig =& JComponentHelper::getParams('com_igallery');
		$recipient = explode(',', $igalleryConfig->get('notify_emails_report', '') );
		
		$query = 'SELECT '.
		'#__igallery_img.filename, #__igallery_img.ordering, '.
		'#__igallery.name, #__igallery.id '.
		'FROM #__igallery_img '.
		'INNER JOIN #__igallery ON #__igallery.id = #__igallery_img.gallery_id '.
		'WHERE #__igallery_img.id = '. (int)$id;
		$db->setQuery($query);
		$photo = $db->loadObject();
		
		$subject = $siteConfig->get('config.sitename').' : '.JText::_('IMAGE_REPORTED');
		
		if( $user->get('guest') )
		{
			$user->name = JText::_('PUBLIC');
		}
		
		$body =
		JText::_('JGLOBAL_USERNAME').': '.$user->name." \n\n ".
		JText::_('JCATEGORY').': '.$photo->name." \n\n ".
		IG_HOST.'index.php?option=com_igallery&view=category&igid='.$photo->id.'&image='.$photo->ordering.'&Itemid='.JRequest::getInt('Itemid')." \n\n ".
		$text;
		
		
		for($i=0; $i<count($recipient); $i++)
		{
            $mail->sendMail($from, $fromname, $recipient[$i], $subject, $body);
			
			if($i > 5)
			{
				break;
			}
		}
		return true;
	}

	function addRating()
	{
	    $params = JComponentHelper::getParams('com_igallery');

        if($params->get('rating_allow_guest', 1) == 0)
        {
            if(JFactory::getUser()->get('guest') == true)
            {
                $this->setError( JText::_('PLEASE_LOGIN_TO_RATE_IMAGES') );
                return false;
            }
        }

	    $imageId = JRequest::getInt('imageid', 0);
	    $rating = JRequest::getInt('rating', 0);

	    if( $rating < 1 || $rating > 5 )
	    {
            $this->setError('Rating: '.$rating.' is out of the range 1-5' );
            return false;
        }

        $db	=& JFactory::getDBO();
	    $query = $db->getQuery(true);

	    $query->select('i.id');
		$query->from('#__igallery_img AS i');
		$query->where('i.id = '.(int)$imageId);
		$db->setQuery($query);
		$image = $db->loadObject();

		if(!isset($image->id))
        {
            $this->setError($db->getErrorMsg().' | Error, image with id: '.$imageId. ' Does not exist' );
            return false;
        }

        $query = $db->getQuery(true);
		$query->select('r.*');
		$query->from('#__igallery_ratings AS r');

		$query->where('r.image_id = '.(int)$imageId);
        $db->setQuery($query);
		$ratings = $db->loadObjectList();

        foreach($ratings as $rating)
        {
            if( JFactory::getUser()->get('guest') == true )
			{
				if( $rating->ip == $_SERVER['REMOTE_ADDR'] )
				{
					$interval = $params->get('rating_interval', 24) * 3600;

					if( time() < $rating->date + $interval )
					{
						$this->setError( JText::_('ALREADY_VOTED') );
						return false;
					}
				}
			}
			else
			{
				if( $rating->user == JFactory::getUser()->id )
				{
					$interval = $params->get('rating_interval', 24) * 3600;

					if( time() < $rating->date + $interval )
					{
						$this->setError( JText::_('ALREADY_VOTED') );
						return false;
					}
				}
			}
        }

		$row =& $this->getTable('igallery_ratings');

		$row->image_id = (int)$imageId;
		$row->rating = (int)JRequest::getInt('rating', 0);
		$row->ip = $_SERVER['REMOTE_ADDR'];
		$row->user = (int)JFactory::getUser()->id;
		$row->published = 1;
		$row->date = time();

		if(!$row->store())
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}

        return true;
    }

    function getRatingAverage()
    {
        $imageId = JRequest::getInt('imageid', 0);

        $db	=& JFactory::getDBO();
	    $query = $db->getQuery(true);
		$query->select('r.*');
		$query->from('#__igallery_ratings AS r');

		$query->where('r.image_id = '.(int)$imageId);
        $db->setQuery($query);
		$ratings = $db->loadObjectList();

		$sum = 0;
		$count = 0;
		foreach($ratings as $rating)
        {
            $sum = $sum + $rating->rating;
            $count++;
        }
        $average = round( ($sum/$count), 2);

        return $average;
    }
		
}