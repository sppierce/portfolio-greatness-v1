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

<form action="<?php echo JRoute::_('index.php?option=com_droppics&task=picture.save&id='.$this->item->id); ?>" method="post" id="picture-form-image" class="form-horizontal">
    <div class="well">
            <h4><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAMETERS'); ?></h4>
            <div class="control-group">
                <?php echo $this->form->getLabel('title'); ?>
                <div class="controls">
                    <?php echo $this->form->getInput('title'); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo $this->form->getLabel('alt'); ?> 
                <div class="controls">
                    <?php echo $this->form->getInput('alt'); ?>
                </div>
            </div>
            <div class="control-group">
                <?php echo $this->form->getLabel('file'); ?> 
                <div class="controls">
                    <?php echo $this->form->getInput('file'); ?>
                </div>
            </div>
            <input type="hidden" class="droppicsinput" name="<?php echo JSession::getFormToken() ?>" value="1" />
            <button class="btn" type="submit">Save</button>
    </div>
</form>
<?php
$fieldSets = $this->form->getFieldsets('params');
if(!empty($fieldSets)):
?>
<form action="<?php echo JRoute::_('index.php?option=com_droppics&task=picture.save&id='.$this->item->id.'&id_gallery='.$this->item->id_gallery); ?>" method="post" id="picture-form-theme" class="form-horizontal">
    <div class="well">
            <?php
                echo '<h4>'.JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_THEME').'</h4>';
                foreach ($fieldSets as $name => $fieldSet) : ?>
                        <?php foreach ($this->form->getFieldset($name) as $field): ?>
                            <?php if(DroppicsBase::getAttribute($field,'type')!=='hidden' && DroppicsBase::getAttribute($field,'type')!='button'): ?>
                            <?php if(DroppicsBase::getAttribute($field,'type')=='slider'){$style='style="float:right;"';}else{$style='';} ?>
                            <div class="control-group <?php echo DroppicsBase::getAttribute($field,'wrapperClass'); ?>" <?php echo $style; ?>>
                                <?php echo $field->label; ?>
                                <div class="controls">
                                    <?php echo $field->description; ?></span>
                            <?php endif; ?>
                                    
                                    <?php echo $field->input; ?>
                                    
                            <?php if(DroppicsBase::getAttribute($field,'type')!=='hidden' && DroppicsBase::getAttribute($field,'type')!='button'): ?>        
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                <?php endforeach; ?>
            <input type="hidden" class="droppicsinput" name="<?php echo JSession::getFormToken() ?>" value="1" />
            <button class="btn" type="submit">Save</button>
    </div>
	<div class="clr"></div>
</form>
<?php endif; ?>
