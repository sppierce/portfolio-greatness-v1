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

// No direct access.
defined('_JEXEC') or die;

if($this->form){ 
$fieldSets = $this->form->getFieldsets('params');
if(!empty($fieldSets)){
?>
<form id="droppicsparams">
    <?php
    echo $this->form->getInput('id');
    
    foreach ($fieldSets as $name => $fieldSet) :
            ?>
            <fieldset>                    
                    <?php foreach ($this->form->getFieldset($name) as $field) : ?>
                    <?php echo $field->label; ?>
                    <span class="paraminput"><?php echo $field->input; ?></span>
                    <!--<span class="help-block"><?php echo $field->description; ?></span>-->
                    <?php endforeach; ?>
            </fieldset>
    <?php endforeach; ?>
    <span class="paraminput"><?php echo JHtml::_('form.token'); ?></span>
    <button class="btn" type="submit">Save</button>
</form>
<script type="text/javascript">
    jQuery(document).ready(function (){
            jQuery('#galeryparams .minicolors, #galeryparams .input-colorpicker').each(function() {
                    jQuery(this).minicolors({
                            control: jQuery(this).attr('data-control') || 'hue',
                            position: jQuery(this).attr('data-position') || 'left',
                            theme: 'bootstrap'
                    });
            });
    });
</script>
<?php } 
}
?>