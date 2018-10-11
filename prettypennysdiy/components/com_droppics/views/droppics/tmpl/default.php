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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.colorpicker');
JHtml::_('formbehavior.chosen', '.droppics-chosen-select');
JHtml::_('behavior.modal');

//$function	= JRequest::getCmd('function', 'jInsertGallery');

JText::script('COM_DROPPICS_JS_DROP_FILES_HERE');
JText::script('COM_DROPPICS_JS_DROP_FILES_HERE');
JText::script('COM_DROPPICS_JS_USE_UPLOAD_BUTTON');
JText::script('COM_DROPPICS_JS_ADD_REMOTE_VIDEO');
JText::script('COM_DROPPICS_JS_ARE_YOU_SURE');
JText::script('COM_DROPPICS_JS_ARE_YOU_SURE_ALL');
JText::script('COM_DROPPICS_JS_DELETE');
JText::script('COM_DROPPICS_JS_INSERT_PICTURE');
JText::script('COM_DROPPICS_JS_INSERT_PICTURE_NO_ALIGN');
JText::script('COM_DROPPICS_JS_INSERT_PICTURE_ALIGN_LEFT');
JText::script('COM_DROPPICS_JS_INSERT_PICTURE_ALIGN_RIGHT');
JText::script('COM_DROPPICS_JS_EDIT');
JText::script('COM_DROPPICS_JS_BROWSER_NOT_SUPPORT_HTML5');
JText::script('COM_DROPPICS_JS_TOO_ANY_FILES');
JText::script('COM_DROPPICS_JS_FILE_TOO_LARGE');
JText::script('COM_DROPPICS_JS_ONLY_IMAGE_ALLOWED');
JText::script('COM_DROPPICS_JS_DBLCLICK_TO_EDIT_TITLE');
JText::script('COM_DROPPICS_JS_WANT_DELETE_GALLERY');
JText::script('COM_DROPPICS_JS_SELECT_FILES');
JText::script('COM_DROPPICS_JS_IMAGE_PARAMETERS');
JText::script('COM_DROPPICS_JS_CANCEL');
JText::script('COM_DROPPICS_JS_OK');
JText::script('COM_DROPPICS_JS_CONFIRM');
JText::script('COM_DROPPICS_JS_SAVE');
JText::script('COM_DROPPICS_JS_SAVED');
$doc = JFactory::getDocument();
$doc->addScriptDeclaration('gcaninsert='.(JRequest::getBool('caninsert',false)?'true':'false').';');
$doc->addScriptDeclaration('e_name="'.JRequest::getString('e_name').'";');

$params = JComponentHelper::getParams('com_droppics');
$collapse = (int)$params->get('catcollapsed',0) ;

$paramsmedia = JComponentHelper::getParams('com_media');
$declaration = 
            "   if(typeof(Droppics)=='undefined'){"
          . "     Droppics={};"
          . "}"
          . "Droppics.can = {};"
          . "Droppics.can.create=".(int)$this->canDo->get('core.create').";"
          . "Droppics.can.edit=".(int)($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')).";"
          . "Droppics.can.delete=".(int)$this->canDo->get('core.delete').";"
          . "Droppics.addRemoteVideo = ".(int)$params->get('remote_video',0).";"
          . "Droppics.baseurl='".COM_MEDIA_BASEURL."';"
          . "Droppics.relativeUrl='".$paramsmedia->get("file_path", 'images')."';"
            . "Droppics.collapse=".($collapse?'true':'false').";"
          . "Droppics.version='".droppicsComponentHelper::getVersion()."';"
        . "";
$doc->addScriptDeclaration($declaration);

$ownbootstrap = '';
if(JFactory::getApplication()->isSite()){
    $ownbootstrap = 'ownbootstrap';
}
?>
<div id="mybootstrap" class="<?php echo $ownbootstrap; ?>">
    <div id="main_wrapper">
        <?php echo $this->loadTemplate('cats'); ?>

        <div id="rightcol" class="">
            <?php if(JRequest::getBool('caninsert')): ?>
                <a id="insertgallery" class="btn btn-success btn-block" href=""><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_INSERT_GALLERY'); ?></a>
                <a id="insertimage" class="btn btn-success btn-block" style="display: none;" href="" ><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_INSERT_PICTURE'); ?></a>
            <?php endif; ?>

            <div>
                <?php if($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                <div class="themesblock  ui-accordion ui-widget ui-helper-reset">
                    <div class="well">
                        <h4 class="ui-accordion-header ui-state-default ui-accordion-icons"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_THEME'); ?></h4>
                        <div id="themeSelection" class="ui-accordion-content">
                        <?php 
                        JPluginHelper::importPlugin('droppics');
                        $dispatcher = JDispatcher::getInstance();
                        $themes = $dispatcher->trigger('getThemeName');
                        foreach ($themes as $theme): ?>
                        <a class="themebtn <?php echo strtolower($theme['name']); ?>" href="" data-theme="<?php echo $theme['id']; ?>"><?php echo $theme['name']; ?></a>
                        <?php endforeach; ?>
                        <div class="clrr"></div>
                        </div>
                    </div>

                    <div class="well">
                        <h4 class="ui-accordion-header ui-state-default ui-accordion-icons"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_PARAMETERS'); ?></h4>
                        <div id="galeryparams" class="ui-accordion-content">

                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="imageblock" style="display: none;">
                    <?php if($this->canDo->get('core.delete')): ?>
                        <a id="deleteImage" href="" class="deleteImage btn btn-block btn-large"><i class="icon-trash icon-white"></i> <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_DELETE'); ?></a>
                    <?php endif; ?>                   
                    <div id="imageparameters" class="">

                    </div>
                </div>
            </div>
        </div>
        <div id="pwrapper">
            <div id="wpreview">
                <div id="preview"></div>        
            </div>
            <input type="hidden" name="id_gallery" value="" />
        </div>
    </div>
    
    <div id="picture_wrapper" style="display: none;">
        <?php echo $this->loadTemplate('picture'); ?>
    </div>
    
    <div id="droppics-remote-form" style="display: none">
        <div class="form-horizontal"> 
            <div class="control-group">
                <label class="control-label" for="droppics-remote-url"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_VIDEO_URL'); ?></label>
                <div class="controls">
                    <input id="droppics-remote-url" name="droppics-remote-url" type="text" placeholder="URL" class="input-xlarge">
                    <p><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_VIDEO_SUPPORTED'); ?></p>
                    <p class="description more">
                        <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_VIDEO_SUPPORTED_EXAMPLE'); ?>
                    </p>
                </div> 
            </div>                          
        </div>        
    </div>
    
</div>
<script type="text/javascript">
    jQuery(document).ready(function (){
            jQuery('#galleryparams .minicolors, #galleryparams .input-colorpicker').each(function() {
                    jQuery(this).minicolors({
                            control: jQuery(this).attr('data-control') || 'hue',
                            position: jQuery(this).attr('data-position') || 'right',
                            theme: 'bootstrap'
                    });
            });
            jQuery('#imageblock .minicolors, #imageblock .input-colorpicker').minicolors('destroy');
            jQuery('#imageblock .minicolors, #imageblock .input-colorpicker').each(function() {
                    $this = this;
                    jQuery(this).minicolors({
                            control: jQuery(this).attr('data-control') || 'hue',
                            position: jQuery(this).attr('data-position') || 'right',
                            theme: 'bootstrap',
                            change : function(){jQuery($this).trigger('change')}
                    });
                    jQuery(this).attr('maxlength',7);
            });
    });
</script>