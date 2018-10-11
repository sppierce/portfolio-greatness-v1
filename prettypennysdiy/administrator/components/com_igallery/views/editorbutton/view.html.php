<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class igalleryViewEditorbutton extends JViewLegacy
{
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

        $name = preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', JRequest::getVar('e_name') );
		$rand = rand(1,9999);

	    $js = '
	    function insertToken()
	    {
	    	var type = document.id(\'jform_ig_type\').get(\'value\');
    		var catid = document.id(\'jformig_category\').value;
    		var children = document.id(\'jform_ig_include_child\').value;
    		var addlinks = document.id(\'jform_ig_add_links\').value;
    		var profile = document.id(\'jformig_profile\').value;
    		var tags = document.id(\'jform_ig_tags\').value;
    		var limit = document.id(\'jform_ig_max_images\').value;
    		
    		var token = \'{igallery id=\' + '.$rand.' + \'|cid=\' + catid + \'|pid=\' + profile + \'|type=\' + type + \'|children=\' + children + \'|addlinks=\' + addlinks + \'|tags=\' + tags + \'|limit=\' + limit + \'}\';
    		window.parent.jInsertEditorText(token, \''.$name.'\');
    		return false;
		}';

        JHTML::_('behavior.framework');
		
	    $document  =& JFactory::getDocument();
	    $document->addScriptDeclaration($js);

		parent::display($tpl);
    }
}