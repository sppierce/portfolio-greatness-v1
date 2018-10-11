<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class igalleryViewImage extends JViewLegacy
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
		$this->params =& JComponentHelper::getParams('com_igallery');
		
		$model = $this->getModel();
		$this->category	= $model->getCategory($this->item->gallery_id);
		$profile	= $model->getProfile($this->category->profile);
		
		
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if(! $this->thumbFile = igFileHelper::originalToResized($this->item->filename, 
		$profile->thumb_width, $profile->thumb_height, $profile->img_quality, 
		$profile->crop_thumbs, $this->item->rotation, $profile->round_thumb, $profile->round_fill) )
	    {
	        return false;
	    }

	    $this->addToolbar();
		parent::display($tpl);
    }
    
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_('EDIT_IMAGE_DETAILS'),'generic.png' );
		
		JToolBarHelper::apply('image.apply');
		JToolBarHelper::save('image.save');
		JToolBarHelper::custom('image.save_and_next', 'forward.png', 'forward.png','SAVE_AND_NEXT', false);
		JToolBarHelper::cancel('image.cancel');
	}
}