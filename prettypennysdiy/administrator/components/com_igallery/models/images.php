<?php
defined( '_JEXEC' ) or die();

jimport('joomla.application.component.modellist');

class igalleryModelImages extends JModelList
{
    public function __construct( $config = array() )
    {
        if( empty($config['filter_fields']) )
        {
            $config['filter_fields'] = array('filename', 'filesize','description', 'tags','link',
                'published','hits', 'access','ordering','moderate','id');
        }

        parent::__construct($config);
    }

    function getListQuery($resolveFKs = true)
	{
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);

		$query->select('i.*');
		$query->from('#__igallery_img AS i');

		$query->select('c.name, c.user as category_owner');
		$query->join('INNER', '`#__igallery` AS c ON c.id = i.gallery_id');
		
		$query->select('p.id as id_of_profile, p.max_width, p.max_height, p.thumb_width, '.
		'p.thumb_height, p.crop_thumbs, p.img_quality, '.
		'p.watermark, p.watermark_filename, p.watermark_position, '.
		'p.watermark_transparency, p.crop_main, p.crop_lbox, p.round_thumb,
		p.round_fill, p.round_large, p.watermark_text, p.watermark_text_color, p.watermark_text_size');
		$query->join('INNER', '`#__igallery_profiles` AS p ON p.id = c.profile');
		
		$query->select('v.title as access_group_name');
		$query->join('INNER', '`#__viewlevels` AS v ON v.id = i.access');

		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('i.published = ' . (int)$published);
		}
		else if ($published === '')
		{
			$query->where('(i.published IN (0, 1))');
		}

		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('i.id = '.(int)substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(i.filename LIKE '.$search.' OR i.description LIKE '.$search.' OR i.tags LIKE '.$search.' OR i.alt_text LIKE '.$search.')');
			}
		}
		
		$catid = $this->getState('filter.catid');
		if (!empty($catid))
		{
			$query->where('i.gallery_id = ' . (int)$catid);
		}

		$query->order($db->escape($this->getState('list.ordering', 'i.ordering')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
    
	protected function populateState($ordering = null, $direction = null)
	{
		$app		= JFactory::getApplication();

		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);
		
		$catid = $app->getUserStateFromRequest($this->context.'.catid', 'catid', '');
		$this->setState('filter.catid', (int)$catid);
		
		if($app->isSite() == true)
		{
			$this->setState('list.limit', 0);
		}
		else
		{
			parent::populateState('i.ordering', 'asc');
		} 
	}
	
	function getCategory($id)
	{
        $db	= $this->getDbo();
		$query = 'SELECT * FROM #__igallery WHERE id = '.(int)$id;
		$db->setQuery($query);
		$category = $db->loadObject();
		
		if($category == null)
		{
			$category = new stdClass();
			$category->id = 0;
			$category->name = '';
		}
		
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
}