<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class igalleryViewImages extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->isSite = JFactory::getApplication()->isSite();
		$this->user         =& JFactory::getUser();
		
		$model              = $this->getModel();
		$this->category		= $model->getCategory( JRequest::getInt('catid', 0) );
		

		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->params =& JComponentHelper::getParams('com_igallery');
		$this->moderate = $this->params->get('moderate_img', 0);
		
		$this->catDropDown = igHtmlHelper::getCategorySelect('catid', 'id', 'name', null, true, 1, true, $this->category->id);
		
		if( !empty($this->category->id) )
		{
			$selectItems     = array();
		    $selectItems[]   = JHTML::_('select.option', 'copy', JText::_('IG_COPY') );
		    $selectItems[]   = JHTML::_('select.option', 'move', JText::_('IG_MOVE') );
		    $this->copyMove = JHTML::_('select.genericlist', $selectItems, 'copy_move', 'class="inputbox" size="1"' );
		    
		    $this->catCopyMove = igHtmlHelper::getCategorySelect('cat_id_copy_move', 'id', 'name', $this->category->id, true, 1, false, 0);
		}
	    
	    $this->thumbFiles = array();
	    $this->mainFiles = array();
		
	    for ($i=0; $i<count($this->items); $i++)
		{
		    $row =& $this->items[$i];
		    
		    if(! $this->thumbFiles[$i] = igFileHelper::originalToResized($row->filename, $row->thumb_width, 
		    $row->thumb_height, $row->img_quality, $row->crop_thumbs, $row->rotation,
		    $row->round_thumb, $row->round_fill) )
		    {
		        return false;
		    }
		    
		    if(! $this->mainFiles[$i] = igFileHelper::originalToResized($row->filename, $row->max_width, 
		    $row->max_height, $row->img_quality, $row->crop_main, $row->rotation, $row->round_large, $row->round_fill, 
		    $row->watermark, $row->watermark_text, $row->watermark_text_color, $row->watermark_text_size, 
		    $row->watermark_filename, $row->watermark_position, $row->watermark_transparency, 1) )
		    {
		        return false;
		    }	
		}

        $this->showImportServer = false;
        if( $this->params->get('show_import_server', 0) && $this->category->id > 0 && $this->isSite == false && igGeneralHelper::authorise('core.create', $this->category->id) )
        {
            $this->showImportServer = true;
        }

        if( $this->showImportServer )
        {
            $data = '
            <?xml version="1.0" encoding="utf-8"?>
            <form><fieldset>
            <field name="server_import" type="ifolderlist" directory="images" label="SERVER_IMPORT"  addfieldpath="administrator/components/com_igallery/models/fields"/>
            </fieldset></form>';
            $this->imagesForm = JForm::getInstance('images',$data);
        }
		
        if(igGeneralHelper::authorise('core.create', $this->category->id))
        {
			if( !empty($this->category->id) )
			{
				$uploader = $this->params->get('file_uploader', 'plupload');
				$uploaderFileName = IG_UPLOAD_PATH.'/'.$uploader.'/'.$uploader.'.php';
				$uploaderClassName = 'igUpload'.ucfirst($uploader);
				$headJsFunctionName = $uploader.'HeadJs';
				$htmlFunctionName = $uploader.'HTML';
			
				require_once($uploaderFileName);
				call_user_func( array($uploaderClassName, $headJsFunctionName) );
				call_user_func( array($uploaderClassName, $htmlFunctionName) );
			}
			else
			{
				echo '<p style="padding: 5px 15px;">'.JText::_('PLEASE_SELECT_CATEGORY_TO_UPLOAD').'<p>';
			}
        }
		
		$this->addToolbar($this->category);
		igHtmlHelper::addSubmenu();
		parent::display($tpl);
		
	}
	
	protected function addToolbar($category)
	{
		JToolBarHelper::title( JText::_( 'MANAGE_IMAGES').' - '.$category->name ,'generic.png' );
		
		if($this->isSite == true)
		{
			JToolBarHelper::back('JTOOLBAR_BACK','index.php?option=com_igallery&view=categories&Itemid='.JRequest::getInt('Itemid', '') );
		}
		
		if(igGeneralHelper::authorise('core.edit.state')) 
		{
			JToolBarHelper::custom('images.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('images.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}
		
		if($this->moderate == 1  && $this->isSite == false)
		{
			JToolBarHelper::custom('images.moderate', 'checkin.png', 'checkin_f2.png', JText::_( 'APPROVE' ) );
			JToolBarHelper::custom('images.unmoderate', 'remove.png', 'remove_f2.png', JText::_( 'UNAPPROVE' ) );
		}
		
		if(igGeneralHelper::authorise('core.delete')) 
		{
			JToolBarHelper::deleteList('', 'images.delete','JTOOLBAR_DELETE');
		}
		
	}
}