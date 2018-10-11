<?php
defined('_JEXEC') or die('Restricted access');

class igTreeHelper
{
    function makeCategoryTree($categories)
    {
        $categoriesByParent = array();

        foreach($categories as $category)
        {
            if( !isset( $categoriesByParent[$category->parent] ) )
            {
                $categoriesByParent[$category->parent] = array();
            }

            $categoriesByParent[$category->parent][] = $category;
        }

        $categoriesbyTree = igTreeHelper::arrangeTree($categoriesByParent);

        return $categoriesbyTree;
    }

    function arrangeTree($categoriesByParent, $parentId=0, $categoriesbyTree = array(), $level=0)
    {
        if( isset($categoriesByParent[$parentId]) )
        {
            foreach($categoriesByParent[$parentId] as $category)
            {
                $category->level = $level;
                $categoriesbyTree[] = $category;

                if (!empty($categoriesByParent[$category->id]))
                {
                    $categoriesbyTree = igTreeHelper::arrangeTree($categoriesByParent, $category->id, $categoriesbyTree, $level + 1);
                }
            }
        }

        return $categoriesbyTree;
    }

    function removeFromTree($categoriesbyTree, $field, $searchTerm, $precision)
    {
        foreach($categoriesbyTree as $key => $category)
        {
            if($precision == 'string-not-exist')
            {
                if( strpos(JString::strtolower($category->{$field}), JString::strtolower($searchTerm) ) === false)
                {
                    unset($categoriesbyTree[$key]);
                }
            }

            if($precision == 'numeric-not-exist')
            {
                if( $category->{$field} != $searchTerm )
                {
                    unset($categoriesbyTree[$key]);
                }
            }

            if($precision == 'numeric-exist')
            {
                if( $category->{$field} == $searchTerm )
                {
                    unset($categoriesbyTree[$key]);
                }
            }
        }

        return $categoriesbyTree;
    }

    function getChildIds($categories, $parentId)
    {
        $categoriesByParent = array();
        $childIds = array();

        foreach($categories as $category)
        {
            if( !isset( $categoriesByParent[$category->parent] ) )
            {
                $categoriesByParent[$category->parent] = array();
            }

            $categoriesByParent[$category->parent][] = $category;
        }

        if( isset($categoriesByParent[$parentId]) )
        {
            $childIds = igTreeHelper::buildChildIds($categoriesByParent, $parentId, $childIds);
        }

        JArrayHelper::toInteger($childIds);
        return $childIds;
    }

    function buildChildIds($categoriesByParent, $parentId, $childIds)
    {
        foreach($categoriesByParent[$parentId] as $category)
        {
            if($category->published != 0 && $category->moderate != 0)
            {
                $childIds[] = $category->id;
            }
            if( isset($categoriesByParent[$category->id]) )
            {
                if( is_array($categoriesByParent[$category->id]) )
                {
                    $childIds = igTreeHelper::buildChildIds($categoriesByParent, $category->id, $childIds);
                }
            }
        }

        return $childIds;
    }

    function getParentPath($categories, $childId, $includeChild)
    {
        $categoriesById = array();
        $parents = array();

        foreach($categories as $category)
        {
            $categoriesById[$category->id] = $category;
        }

        if($includeChild)
        {
            $parents[] = $categoriesById[$childId];
        }

        $parents = igTreeHelper::buildParentPath($categoriesById, $childId, $parents);

        return $parents;
    }

    function buildParentPath($categoriesById, $childId, $parents)
    {
        foreach($categoriesById as $category)
        {
            if($category->id == $childId)
            {
                if($category->parent != 0)
                {
                    $parents[] = $categoriesById[$category->parent];
                    $parents = igTreeHelper::buildParentPath($categoriesById, $category->parent, $parents);
                }
            }
        }

        return $parents;
    }

}