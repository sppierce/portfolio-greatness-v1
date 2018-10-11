<?php
defined('_JEXEC') or die('Restricted access');

class igStaticHelper
{
    static private $categories = array();

    static public function getCategories()
    {
        if( empty(self::$categories) )
        {
            $db	= JFactory::getDBO();
            $query = 'SELECT * FROM #__igallery ORDER BY parent, ordering';
            $db->setQuery($query);
            self::$categories = $db->loadObjectList();
        }
        return self::$categories;
    }
}