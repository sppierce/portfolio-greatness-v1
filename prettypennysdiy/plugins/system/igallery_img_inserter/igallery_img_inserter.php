<?php
defined( '_JEXEC' ) or die('Restricted Access');

jimport( 'joomla.plugin.plugin' );

class plgSystemIgallery_img_inserter extends JPlugin
{

	function __construct (&$subject, $params)
	{
		parent::__construct($subject, $params);
	}

    function onAfterDispatch()
    {
        $option =  JRequest::getCmd('option', '');
    
        if($option == 'com_search')
        {
            require_once(JPATH_ADMINISTRATOR.'/components/com_igallery/defines.php');
            
            $db		  = JFactory::getDBO();
    		$document = JFactory::getDocument();
            $buffer   = $document->getBuffer('component');
            $imageIdsArray = array();
            preg_match_all('/igalleryimg ([0-9]+?)/U', $buffer, $matches);

            foreach($matches[1] as $imageId)
            {
                $imageIdsArray[] = (int)$imageId;
            }

            if( count($imageIdsArray) > 0 )
            {
                $query = $db->getQuery(true);

                $query->select('i.id, i.filename, i.alt_text, i.gallery_id, i.ordering, i.rotation');
                $query->from('#__igallery_img AS i');

                $query->select('c.profile');
                $query->join('INNER', '`#__igallery` AS c ON c.id = i.gallery_id');

                $query->select('p.thumb_width, p.thumb_height, p.img_quality, p.crop_thumbs, p.round_thumb, p.round_fill, p.thumb_pagination, p.thumb_pagination_amount');
                $query->join('INNER', '`#__igallery_profiles` AS p ON p.id = c.profile');

                $query->where('i.id IN ('.implode(',', $imageIdsArray).')');

                $db->setQuery($query);
                $imageRows = $db->loadObjectList();

                if(is_array($imageRows))
                {
                    foreach($imageRows as $row)
                    {
                        if(!$thumbFile = igFileHelper::originalToResized($row->filename, $row->thumb_width,
                        $row->thumb_height, $row->img_quality, $row->crop_thumbs, $row->rotation, $row->round_thumb, $row->round_fill) )
                        {
                            JError::raise(2, 500, 'Imgage Inserter Plugin Make Image Error: '.$row->filename);
                            return false;
                        }

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

                        $link = JRoute::_('index.php?option=com_igallery&view=category&igid='.$row->gallery_id.'&Itemid='.igUtilityHelper::getItemid($row->gallery_id).$limitStart.'#!'.$fileHashNoRef);

                        $imageTag = '<a href="'.$link.'"><img src="'.IG_IMAGE_HTML_RESIZE.$thumbFile['folderName'].'/'.$thumbFile['fullFileName'].'"
                        width="'.$thumbFile['width'].'" height="'.$thumbFile['height'].'" alt="'.$row->alt_text.'"/></a>';

                        $buffer = str_replace('igalleryimg '.$row->id, $imageTag, $buffer);
                    }

                    $document->setBuffer($buffer, 'component');
                }
            }
        }
    }
}
