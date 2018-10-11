<?php
/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;


class DroppicsTablePicture extends JTable
{
	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__droppics_pictures', 'id', $db);
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param	array		Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @see		JTable:bind
	 * @since	1.5
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string)$registry;
		}
		return parent::bind($array, $ignore);
	}

        public function check() {
            $filename = droppicsBase::getFullPicturePath($this->id_gallery).$this->file;
                        
            $dbo = $this->getDbo();
            $dbo->setQuery('SELECT file FROM #__droppics_pictures WHERE id='.(int)$this->id);
            $current = $dbo->loadResult();
            
            if(!$this->file){
                $this->setError(JText::_('COM_DROPPICS_CTRL_FILES_EMPTY'));
                return false;
            }
            if($current !== $this->file && file_exists($filename)){
                $this->setError(JText::_('COM_DROPPICS_CTRL_FILES_EXISTS'));
                return false;
            }
            return parent::check();
        }
        
        public function store($updateNulls = false) {
            $dbo = $this->getDbo();
            $dbo->setQuery('SELECT file FROM #__droppics_pictures WHERE id='.(int)$this->id);
            $result = $dbo->loadResult();
            rename(droppicsBase::getFullPicturePath($this->id_gallery).$result, droppicsBase::getFullPicturePath($this->id_gallery).$this->file);
            rename(droppicsBase::getThumbnailPath($this->id_gallery).$result, droppicsBase::getThumbnailPath($this->id_gallery).$this->file);
            rename(droppicsBase::getPicturePath($this->id_gallery).$result, droppicsBase::getPicturePath($this->id_gallery).$this->file);
            
            return parent::store($updateNulls);
        }
}
