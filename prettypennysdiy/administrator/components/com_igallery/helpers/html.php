<?php
defined('_JEXEC') or die('Restricted access');

class igHtmlHelper
{
    function getCategorySelect($name, $key, $value, $exclude, $selectText, $size, $submit, $active)
	{
	    $categories = igStaticHelper::getCategories();

        $categoriesbyTree = igTreeHelper::makeCategoryTree($categories);

        if( !empty($exclude) )
        {
            $categoriesbyTree = igTreeHelper::removeFromTree($categoriesbyTree, 'id', $exclude, 'numeric-exist');
        }

		$selectOptions     = array();

		if($selectText)
		{
		    $selectOptions[]   = JHTML::_('select.option', '0', JText::_('JOPTION_SELECT_CATEGORY'), $key, $value);
		}

		foreach($categoriesbyTree as $category)
		{
            $displayName = $category->name;
            for($k=0; $k<$category->level; $k++)
            {
                $displayName  = ' - '.$displayName;
            }

			$selectOptions[] = JHTML::_('select.option', $category->id, $displayName, $key, $value);
		}

		$html = 'class="inputbox" size="'.$size.'" ';
        $javascript = $submit == true ? 'onchange="document.adminForm.submit();"' : '';

		$parentSelectList = JHTML::_("select.genericlist", $selectOptions, $name, $html.$javascript, $key, $value, $active );
		
		return $parentSelectList;
	}
	
	function moderateImage($row, $i, $prefix, $frontend)
	{
		$img 	= $row->moderate ? 'apply.png' : 'revert.png';
		$task 	= $row->moderate  ? $prefix.'.unmoderate' : $prefix.'.moderate';
		$alt 	= $row->published ? JText::_( 'APPROVE' ) : JText::_( 'UNAPPROVE' );

		$href = '';
		if(!$frontend)
		{
			$href .= '<a class="jgrid" href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $alt .'">';
		}

		$href .= '<img src="'.IG_IMAGE_ASSET_PATH.'menu'.'/'.$img.'" border="0" alt="'. $alt .'" style="float: none;" />';

		if(!$frontend)
		{
			$href .= '</a>';
		}

		return $href;
	}

	function addSubmenu()
	{
		$vName = JRequest::getCmd('view', 'categories');
		
		JSubMenuHelper::addEntry(JText::_('JCATEGORIES'),
		'index.php?option=com_igallery&view=categories', $vName == 'categories');
		
		JSubMenuHelper::addEntry(JText::_('IMAGES'),
		'index.php?option=com_igallery&view=images', $vName == 'images');
		
		if(igGeneralHelper::authorise('core.admin'))
		{
			JSubMenuHelper::addEntry(JText::_('PROFILES'),
			'index.php?option=com_igallery&view=profiles', $vName == 'profiles');
		}

		JSubMenuHelper::addEntry(JText::_('RATINGS'),
		'index.php?option=com_igallery&view=ratings', $vName == 'ratings');
	}
	
}