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

/**
 * Form Field class for the Joomla Framework.
 */
class JFormFieldImgsizes extends JFormField
{
	
	protected $type = 'imgsizes';

	/**
	 */
	protected function getInput()
	{
		
                $baseurl = JURI::base();
		$baseurl = str_replace('administrator/','',$baseurl);	
		$doc = JFactory::getDocument();
		$html = '';
               
                $href = "index.php?option=com_droppics&amp;view=imagesizes&amp;tmpl=component&amp;asset=com_config&amp;fieldid=jform_".$this->element['name'];
		$html .= '<div class="input-prepend input-append"><div class="media-preview add-on"><span class="hasTipPreview" title=""><i class="icon-image"></i></span></div>	
                <input name="jform['.$this->element['name'].']" id="jform_'.$this->element['name'].'" value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" readonly="readonly" title="" class="" type="text">
            <a class="modal btn" title="Select" href="'. $href . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'. JText::_('COM_DROPPICS_CONFIG_CHOOSE') .'</a>
            <a data-original-title="Clear" class="btn hasTooltip" title="" href="#" onclick="jInsertFieldValue(\'\', \'jform_'.$this->element['name'].'\'); return false;">
                <i class="icon-remove"></i></a>
            </div>' ;

            //$doc->addScriptDeclaration($js);
		
            return $html;

	}
}
