<?php
defined( '_JEXEC' ) or die();

jimport('joomla.application.component.modellist');

class igalleryModelcategories extends JModelList
{
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);
		
		parent::populateState('ordering', 'asc');
	}
	
	function getListQuery()
	{
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);

        $query->select('c.id, c.ordering, c.name, c.profile, c.parent, c.user, c.published, c.moderate, c.date, c.publish_up, c.publish_down');
        $query->from('#__igallery AS c');

        $query->select('u.name AS name_of_user');
        $query->join('LEFT', '`#__users` AS u ON u.id = c.user');

        $query->select('p.name AS profile_name, p.id as profile_id');
        $query->join('INNER', '`#__igallery_profiles` AS p ON p.id = c.profile');

        $query->select('COUNT(i.id) AS numimages');
        $query->join('LEFT', '#__igallery_img AS i ON i.gallery_id = c.id' );

        $query->group('c.id');
        $query->order('c.parent, c.ordering');

        return $query;
	}
    
    function getItems()
	{
        $db		= $this->getDbo();
		$query = $this->getListQuery();
		$user =& JFactory::getUser();

        $db->setQuery($query);
		$categories = $db->loadObjectList();
        $categoriesbyTree = igTreeHelper::makeCategoryTree($categories);

        $search = JString::strtolower( $this->getState('filter.search') );
        if($search)
        {
            $categoriesbyTree = igTreeHelper::removeFromTree($categoriesbyTree, 'name', $search, 'string-not-exist');
        }
		
		$filter_state = $this->getState('filter.published');
		if( is_numeric($filter_state) )
		{
            $categoriesbyTree = igTreeHelper::removeFromTree($categoriesbyTree, 'published', $filter_state, 'numeric-not-exist');
        }
		
		if( JFactory::getApplication()->isSite() )
		{
			foreach ($categoriesbyTree as $key => $category)
			{
				$editOk = igGeneralHelper::authorise('core.edit', $category->id, null, $category->profile, $category->user);
				$editOwn = igGeneralHelper::authorise('core.edit.own', $category->id, null, $category->profile, $category->user) && $category->user == $user->id;
				if(!$editOk && !$editOwn)
				{
					unset($categoriesbyTree[$key]);
				}
			}
		}
			
        $orderedCategories = array_values($categoriesbyTree);
        $limit = $this->getState('list.limit') == 0 ? 1000 : $this->getState('list.limit');
        
        if( JFactory::getApplication()->isSite() )
		{
			$slicedCategories = array_slice( $orderedCategories, 0, 1000 );
		}
		else
		{
            $slicedCategories = array_slice( $orderedCategories, $this->getState('list.start'), $limit );
		} 
		
        return $slicedCategories;
	}
}	