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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldButton extends JFormField
{
	protected $type = 'Button';

	/**
	 */
	protected function getInput()
	{
		
		return '<button type="button" class="'.$this->element['class'].'" id="'.$this->element['id'].'" name="'.$this->element['name'].'">'.JText::_($this->element['label']).'</button><br/><br/>';
	}
}
