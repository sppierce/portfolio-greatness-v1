<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class igalleryViewicategory extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	
	function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->isSite = JFactory::getApplication()->isSite();
		$this->params = JComponentHelper::getParams('com_igallery');
		
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$model =& $this->getModel();
		
		if($this->item->parent != 0)
		{
			$parentCat = $model->getCategory($this->item->parent);
			$profile = $model->getProfile($parentCat->profile);
		}
		else
		{
			$profile = $model->getProfile($this->item->profile);
		}
		
		if( !empty($this->item->menu_image_filename) )
		{
		    if(! $this->fileArray = igFileHelper::originalToResized($this->item->menu_image_filename, $profile->menu_max_width, 
		    $profile->menu_max_height, $profile->img_quality, $profile->crop_menu, 0, $profile->round_menu, $profile->round_fill) )
		    {
		        return false;
		    }
		    
		}
		
		$this->addToolbar($this->item);
		parent::display($tpl);
			
	}
	
	protected function addToolbar($category)
	{
		if(empty($category->id))
		{
			JToolBarHelper::title(JText::_('NEW_CATEGORY'),'generic.png');
		}
		else
		{
			JToolBarHelper::title( JText::_('EDIT_CATEGORY').' - '.$category->name );
			JToolBarHelper::apply('icategory.apply');
		}
		JToolBarHelper::save('icategory.save');
		JToolBarHelper::cancel('icategory.cancel');
	}
}