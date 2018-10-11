<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(JPATH_ADMINISTRATOR.'/components/com_igallery/helpers/static.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_igallery/helpers/tree.php');

function igalleryBuildRoute(&$query)
{
    $segments = array();
    $catIdOfActive = 0;

    if( isset($query['view']) && isset($query['igid']) )
    {
        if($query['view'] == 'category')
        {
            if( isset($query['Itemid']) )
            {
                $app	= JFactory::getApplication();
				$menu	= $app->getMenu();
                $activeMenu = $menu->getItem($query['Itemid']);
                if( isset($activeMenu->query['igid']) )
                {
                	$catIdOfActive = $activeMenu->query['igid'];
                }
            }

            $categories = igStaticHelper::getCategories();
            $parents = igTreeHelper::getParentPath($categories, $query['igid'], true);

            foreach($parents as $parent)
            {
                if($parent->id != $catIdOfActive)
                {
                    array_unshift($segments, $parent->alias);
                }
                else
                {
                    break;
                }
            }

            unset($query['view']);
            unset($query['igid']);
        }
    }

    return $segments;
}

function igalleryParseRoute($segments)
{
    $vars = array();
    $count = count($segments);
    $lastSegment = $segments[$count - 1];
    
    $lastSegment = str_replace(':', '-', $lastSegment);
    
    $db =& JFactory::getDBO();
    $dbQuery = 'SELECT id FROM #__igallery WHERE alias = "'.$db->escape($lastSegment).'"';
    $db->setQuery($dbQuery);
    $row = $db->loadObject();
    $vars['igid'] = $row->id;
    $vars['view'] = 'category';
    return $vars;
}
?>