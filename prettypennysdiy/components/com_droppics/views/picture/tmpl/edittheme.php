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


// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
?>

<form action="<?php echo JRoute::_('index.php?option=com_droppics&task=picture.save&id='.$this->item->id); ?>" method="post" id="picture-form" class="form-horizontal">
    <div class="well">
            <?php
            $fieldSets = $this->form->getFieldsets('params');
            if(!empty($fieldSets)){
                echo '<h4>'.JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_THEME').'</h4>';
                foreach ($fieldSets as $name => $fieldSet) : ?>

                                <?php foreach ($this->form->getFieldset($name) as $field) : ?>
                                    <div class="control-group">
                                        <?php echo $field->label; ?>
                                        <div class="controls">
                                            <!--<span class="help-block"><?php echo $field->description; ?></span>-->
                                            <?php echo $field->input; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                <?php endforeach; 
            }?>

            <input type="hidden" class="droppicsinput" name="<?php echo JSession::getFormToken() ?>" value="1" />
            <button class="btn" type="submit">Save</button>
            <?php //echo JHtml::_('form.token'); ?>
    </div>
        
	<div class="clr"></div>
</form>
