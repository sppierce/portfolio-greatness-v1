<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class igalleryViewprofile extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	
	function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->params =& JComponentHelper::getParams('com_igallery');

		$this->addToolbar($this->item);
		parent::display($tpl);
	}
	
	protected function addToolbar($item)
	{
		if(empty($item->id))
		{
			JToolBarHelper::title( JText::_('NEW_PROFILE') );
		}
		else
		{
			JToolBarHelper::title( JText::_('EDIT_PROFILE').' - '.$item->name );
			JToolBarHelper::apply('profile.apply');
		}

		JToolBarHelper::save('profile.save');
		JToolBarHelper::cancel('profile.cancel');
	}
}