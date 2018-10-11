<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class igalleryViewprofiles extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

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
		JToolBarHelper::title( JText::_( 'PROFILES' ), 'generic.png' );
		
		if(igGeneralHelper::authorise('core.create')) 
		{
			JToolBarHelper::custom('profiles.add_profile_redirect', 'new', '', JText::_( 'NEW_PROFILE' ), false);
			JToolBarHelper::custom('profiles.copy', 'copy', '', JText::_( 'IG_COPY' ) );
		}
		
		if(igGeneralHelper::authorise('core.delete'))
		{
			JToolBarHelper::deleteList('', 'profiles.delete','JTOOLBAR_DELETE');
		}
	}
}