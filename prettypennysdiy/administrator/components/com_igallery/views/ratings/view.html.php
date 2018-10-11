<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class igalleryViewratings extends JViewLegacy
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

		for ($i=0; $i<count($this->items); $i++)
		{
		    $row =& $this->items[$i];

		    if(! $this->thumbFiles[$i] = igFileHelper::originalToResized($row->filename, $row->thumb_width,
		    $row->thumb_height, $row->img_quality, $row->crop_thumbs, $row->rotation,
		    $row->round_thumb, $row->round_fill) )
		    {
		        return false;
		    }
		}
		
		$this->addToolbar();
		igHtmlHelper::addSubmenu();
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'RATINGS' ), 'generic.png' );

        if(igGeneralHelper::authorise('core.edit.state'))
        {
            JToolBarHelper::custom('ratings.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('ratings.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
        }
		
		if(igGeneralHelper::authorise('core.delete'))
		{
			JToolBarHelper::deleteList('', 'ratings.delete','JTOOLBAR_DELETE');
		}
	}
}