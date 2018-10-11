<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldIcategory extends JFormField
{
	protected $type = 'Icategory';

	protected function getInput()
	{
        require_once(JPATH_ADMINISTRATOR.'/components/com_igallery/helpers/tree.php');
        require_once(JPATH_ADMINISTRATOR.'/components/com_igallery/helpers/static.php');
		$option = JRequest::getVar('option');
        $hideCurrent = false;

		if($option == 'com_config')
		{
			$params =& JComponentHelper::getParams('com_igallery'); 
			$selected = $params->get('default_parent', 0);
		}
		else if($option == 'com_menus')
		{
			$selected = $this->value;
		}
		else if(strpos($option, 'module') )
		{
			$igparams = $this->form->getValue('params');
			$selected = isset($igparams->category_id) ? $igparams->category_id : 0;
		}
		else
		{
			$selected = $this->form->getValue('parent');
			$hideCurrent = true;
		} 
		
		$categories = igStaticHelper::getCategories();
        $categoriesbyTree = igTreeHelper::makeCategoryTree($categories);

        if($hideCurrent)
        {
            $current = JRequest::getInt('id',0);
            $categoriesbyTree = igTreeHelper::removeFromTree($categoriesbyTree, 'id', $current, 'numeric-exist');
        }

        if($option == 'com_igallery' && JRequest::getVar('view') == 'icategory' && JRequest::getInt('id', 0) > 0 )
        {
            $children = igTreeHelper::getChildIds($categories, JRequest::getInt('id', 0) );

            if( count($children) > 0)
            {
                foreach($children as $child)
                {
                    $categoriesbyTree = igTreeHelper::removeFromTree($categoriesbyTree, 'id', $child, 'numeric-exist');
                }
            }
        }

	    $selectItems = array();
	    
	    if($option != 'com_menus' && !strpos($option, 'module') && JRequest::getVar('view') != 'editorbutton')
	    {
	    	$selectItems[]   = JHTML::_('select.option', '0', JText::_('JGLOBAL_TOP') );
	    }

	    foreach ($categoriesbyTree as $category)
	    {
            $displayName = $category->name;
            for($k=0; $k<$category->level; $k++)
            {
                $displayName  = ' - '.$displayName;
            }
	        $selectItems[] = JHTML::_('select.option', $category->id, $displayName );
	    }
		
	    $selectHTML = JHTML::_("select.genericlist", $selectItems, $this->name, 'class="inputbox" size="10"', 
	    'value', 'text', $selected);
	   
	    return $selectHTML;
	}
}