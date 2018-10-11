<?php
/**    
 * SEO Glossary Colorpicker field
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	SEO Glossary
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 */
class JFormFieldColorpicker extends JFormField
{
	/**
	 * Color picker form field type compatible with Joomla 1.6. Displays an Adobe type color picker panel, and returns a six-digit hex value, eg #cc99ff
	 */
	protected $type = 'Colorpicker';

	/**
	 */
	protected function getInput()
	{
		
        $baseurl = JURI::base();
		$baseurl = str_replace('administrator/','',$baseurl);	
		
		
		// Initialize some field attributes.
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$scriptname	 = $this->element['scriptpath'] ?(string) $this->element['scriptpath'] : $baseurl.'components/com_droppics/assets/js/color-picker.js';

		
		//try to find the script
		if($scriptname == 'self')
		{
           $filedir = str_replace(JPATH_SITE . '/','',dirname(__FILE__));
    	   $filedir = str_replace('\\','/',$filedir);
           $scriptname = $baseurl . $filedir . '/color-picker.js';
		}
				
		
		$doc = JFactory::getDocument();
		$doc->addScript($scriptname);
		
		$options = array();
		if( $this->element['cellwidth']){ $options[] = "cellWidth:". (int) $this->element['cellwidth'];}
		if( $this->element['cellheight']){ $options[] = "cellHeight:".(int) $this->element['cellheight'];}
		if( $this->element['top']){ $options[] = "top:". (int) $this->element['top'];}
		if( $this->element['left']){ $options[] = "left:". (int) $this->element['left'];}
																			  
        $optionString = implode(',',$options);

		$js = 'window.addEvent(\'domready\', function(){
		var colorInput = $(\''.$this->id.'\');
		var cpicker = new ColorPicker(colorInput,{'.$optionString.'});
});
';

        $doc->addScriptDeclaration($js);
		
		

		// Initialize JavaScript field attributes.
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		return '<input type="text" name="'.$this->name.'" id="'.$this->id.'"' .
				' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"' .
				$class.$size.$disabled.$readonly.$onchange.$maxLength.'/>';
	}
}
