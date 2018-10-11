<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldIfolderList extends JFormFieldList
{
	
	public $type = 'IfolderList';

	protected function getOptions()
	{
		$options = array();

		$filter			= (string) $this->element['filter'];
		$exclude		= (string) $this->element['exclude'];
		
		$options[] = JHtml::_('select.option', '-1', JText::_('SELECT_FOLDER'));
		
		$params =& JComponentHelper::getParams('com_igallery');
		$path = (string)$params->get('import_server_base', 'images/');
		
		
		if (!is_dir($path)) {
			$path = JPATH_ROOT.'/'.$path;
		}

		$folders = JFolder::listFolderTree($path, $filter, 9);
		
		if (is_array($folders)) 
		{
			foreach($folders as $folder) 
			{
				if ($exclude) 
				{
					if (preg_match(chr(1).$exclude.chr(1), $folder)) 
					{
						continue;
					}
				}

				$options[] = JHtml::_('select.option', $folder['relname'], $folder['relname']);
			}
		}
		
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}

