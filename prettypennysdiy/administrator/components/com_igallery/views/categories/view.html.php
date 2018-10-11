<?php
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');

class igalleryViewcategories extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->user         =& JFactory::getUser();
		$this->isSite       =  JFactory::getApplication()->isSite();
		$this->canConfigure =  igGeneralHelper::authorise('core.admin');
		$this->params =& JComponentHelper::getParams('com_igallery');
		$this->moderate = $this->params->get('moderate_cat', 0);

		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		igHtmlHelper::addSubmenu();

		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'IGNITE_GALLERY' ).' <small>v3.5</small>', 'generic.png' );
		
		if(igGeneralHelper::authorise('core.create')) 
		{
			JToolBarHelper::custom('categories.add_category_redirect', 'new', '', JText::_( 'NEW_CATEGORY' ), false);
			if($this->isSite == false)
			{
				JToolBarHelper::custom('categories.copy', 'copy', '', JText::_( 'IG_COPY' ) );
			}
		}
		
		if(igGeneralHelper::authorise('core.edit.state')) 
		{
			JToolBarHelper::custom('categories.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('categories.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}
		
		if($this->moderate == 1 && $this->isSite == false)
		{
			JToolBarHelper::custom('categories.moderate', 'checkin.png', 'checkin_f2.png', JText::_( 'APPROVE' ) );
			JToolBarHelper::custom('categories.unmoderate', 'remove.png', 'remove_f2.png', JText::_( 'UNAPPROVE' ) );
		}
		
		if(igGeneralHelper::authorise('core.delete')) 
		{
			JToolBarHelper::deleteList(JText::_( 'CONFIRM_DELETE_CATEGORY' ), 'categories.delete','JTOOLBAR_DELETE');
		}
		
		if(igGeneralHelper::authorise('core.admin') && $this->isSite == false) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_igallery');
		}
	}
}