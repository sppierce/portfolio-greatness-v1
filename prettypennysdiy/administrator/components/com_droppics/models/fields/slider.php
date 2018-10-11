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

defined('_JEXEC') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 */
class JFormFieldSlider extends JFormField
{
	
	protected $type = 'Slider';

	/**
	 */
	protected function getInput()
	{
            
                $document = JFactory::getDocument();
//                $document->addStyleSheet(JURI::root().'components/com_droppics/assets/css/slider.css');
//                $document->addScript(JURI::root().'components/com_droppics/assets/js/bootstrap-slider.js');
                $document->addScript(JURI::root().'components/com_droppics/assets/js/sliderfieldinit.js');
		
		// Initialize some field attributes.
		$class		= $this->element['class'] ? ' '.(string) $this->element['class'].'' : '';
		$stype		= $this->element['stype'] ? ' data-slider-stype="'.(string) $this->element['stype'].'"' : '';

		

		// Initialize JavaScript field attributes.
                return '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.(int)$this->value.'" class="slider hide '.$class.'" '.$stype.' data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="'.(int)$this->value.'" data-slider-orientation="horizontal" data-slider-selection="after" data-slider-tooltip="always">'
                        . '<script type="text/javascript" src="'.JURI::root().'components/com_droppics/assets/js/sliderfieldinit.js"></script>';
	}
        
//        public function getLabel() {
//            return JText::_($this->element->attributes()->label);
//        }
}
