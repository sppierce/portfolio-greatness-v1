<?php
/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barr�re (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.colorpicker');
JHtml::_('formbehavior.chosen', '.droppics-chosen-select');

JHtml::_('behavior.modal');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$params = array(
    'imgp_margin_left' => '4',
    'imgp_margin_top' => '4',
    'imgp_margin_right' => '4',
    'imgp_margin_bottom' => '4',
    'imgp_border' => '0',
    'imgp_radius' => '3',
    'imgp_border_color' => '#cccccc',
    'imgp_shadow' => '0',
    'imgp_shadow_color' => '#cccccc',
    'imgp_click_target' => 'current',
    'imgp_click' => 'lightbox',
    'custom_link' => '',
    'content_custom_title' => '',
    'follow_custom_link' => 0,
    'show_caption' => 0,
    'imgp_source' => 'thumbnail',
    'customWidth' => '',
    'customHeight' => '',
    'customFilename' => '',
    'click_content_article_id_name' => '',
    'imagealign' => 'left',
    'acticle_id' => '',
    'acticle_link' => '',
    'click_content_menuitem' =>'',
    'is_video' => false
    );
if(is_array($this->item->params) && !empty($this->item->params)){
    $params = array_merge($params,$this->item->params);
}
$droppics_params = JComponentHelper::getParams('com_droppics');
if($droppics_params->get('single_image_settting',1)==0): ?>
<style type="text/css">
    .hide_single_image_params{display: none;}
</style>
<?php endif; 

?>
<form action="<?php echo JRoute::_('index.php?option=com_droppics&task=picture.save&id='.$this->item->id); ?>" method="post" id="picture-form-image" class="form-horizontal">
        
    <div id="imageblock" class="well">
        <h4><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE'); ?></h4>
        <fieldset>
            <div class="center">
                <img id="singleimage" src="<?php echo $this->imgpath; ?>" />
            </div>
            <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_REPLACE'); ?></h5>
            <div id="replete_wrap">
            </div>
            <div class="hide_single_image_params">
            <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_MARGIN'); ?></h5>
            <div class="floating">
                <label id="imgp_margin_left-lbl" for="imgp_margin_left"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_MARGIN_LEFT'); ?></label>
                <span class="paraminput">
                    <input type="text" name="jform[params][imgp_margin_left]" value="<?php echo $params['imgp_margin_left'] ?>" size="3" />
                </span>
            </div>
            <div class="floating">
                <label id="imgp_margin_top-lbl" for="imgp_margin_top"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_MARGIN_TOP'); ?></label>
                <span class="paraminput">
                    <input type="text" name="jform[params][imgp_margin_top]" value="<?php echo $params['imgp_margin_top'] ?>" size="3" />
                </span>
            </div>
            <div class="floating">
                <label id="imgp_margin_right-lbl" for="imgp_margin_right"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_MARGIN_RIGHT'); ?></label>
                <span class="paraminput">
                    <input type="text" name="jform[params][imgp_margin_right]" value="<?php echo $params['imgp_margin_right'] ?>" size="3" />
                </span>
            </div>
            <div class="floating">
                <label id="imgp_margin_bottom-lbl" for="imgp_margin_bottom"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_MARGIN_BOTTOM'); ?></label>
                <span class="paraminput">
                    <input type="text" name="jform[params][imgp_margin_bottom]" value="<?php echo $params['imgp_margin_bottom'] ?>" size="3" />
                </span>
            </div>
            <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_BORDER'); ?></h5>
            <div class="floating">
                <label id="imgp_border-lbl" for="imgp_border"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_BORDER_WIDTH'); ?></label>
                <span class="paraminput">
                    <input type="text" name="jform[params][imgp_border]" value="<?php echo $params['imgp_border'] ?>" size="3" />
                </span>
            </div>
            <div class="floating">
                <label id="imgp_radius-lbl" for="imgp_radius"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_RADIUS'); ?></label>
                <span class="paraminput">
                    <input type="text" name="jform[params][imgp_radius]" value="<?php echo $params['imgp_radius'] ?>" size="3" />
                </span>
            </div>
            <div class="floating">
                <label id="imgp_border_color-lbl" for="imgp_border_color"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_BORDER_COLOR'); ?></label>
                <span class="paraminput">
                    <input type="text" name="jform[params][imgp_border_color]" id="imgp_border_color" class="minicolors minicolors-input" value="<?php echo $params['imgp_border_color'] ?>" data-position="left" data-control="hue" />
                </span>
            </div>
            <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_SHADOW'); ?></h5>
            <div class="floating">
                <label id="imgp_shadow-lbl" for="imgp_shadow"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_SHADOW_WIDTH'); ?></label>
                <span class="paraminput">
                    <input type="text" name="jform[params][imgp_shadow]" value="<?php echo $params['imgp_shadow'] ?>" size="3" />
                </span>
            </div>
            <div class="floating">
                <label id="imgp_shadow_color-lbl" for="imgp_shadow_color"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_SHADOW_COLOR'); ?></label>
                <span class="paraminput">                                    
                    <input type="text" name="jform[params][imgp_shadow_color]" id="imgp_shadow_color" class="minicolors" value="<?php echo $params['imgp_shadow_color'] ?>" data-position="left" />
                </span>
            </div>
            </div>
            
            <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK'); ?></h5>
            <div>
                <div class="paraminput input-append w100">
                    <?php if( $params["is_video"]) {  ?>
                      <select type="text" name="jform[params][imgp_click]" id="imgp_click">
                        <option <?php if($params['imgp_click'] == 'lightbox') echo 'selected' ?> value="lightbox"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_VIDEO_PARAM_TARGET_LIGHTBOX'); ?></option>
                        <option <?php if($params['imgp_click'] == 'current') echo 'selected' ?> value="current"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_VIDEO_PARAM_TARGET_REPLACE'); ?></option>
                        <option <?php if($params['imgp_click'] == '_blank') echo 'selected' ?> value="_blank"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_VIDEO_PARAM_TARGET_BLANK'); ?></option>                        
                    </select>
                    <?php }else { ?>
                    <select type="text" name="jform[params][imgp_click]" id="imgp_click">
                        <option <?php if($params['imgp_click'] == 'lightbox') echo 'selected' ?> value="lightbox"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_LIGHTBOX'); ?></option>
                        <option <?php if($params['imgp_click'] == 'article') echo 'selected' ?> value="article"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_ARTICLE'); ?></option>
                        <option <?php if($params['imgp_click'] == 'menuitem') echo 'selected' ?> value="menuitem"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_MENUITEM'); ?></option>
                        <option <?php if($params['imgp_click'] == 'custom') echo 'selected' ?> value="custom"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_CUSTOM'); ?></option>
                        <option <?php if($params['imgp_click'] == 'nothing') echo 'selected' ?> value="nothing"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_NOTHING'); ?></option>
                    </select>
                    <span id="click_content_article" class="click_content_block" style="<?php echo ($params['imgp_click'] == 'article')?'display:inline-block;':'display:none;' ?>">
                        <input type="text" name="jform[params][click_content_article_id_name]" class="input-medium" id="click_content_article_id_name" value="<?php echo $params['click_content_article_id_name'] ?>" disabled="disabled" size="35" />
                        <a class="modal btn hasTooltip" title="" href="index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle&<?php echo JSession::getFormToken(); ?>=1" rel="{handler: 'iframe', size: {x: 800, y: 450}}" data-original-title="Select or Change article"><i class="icon-file"></i> Select</a>
                        <input type="hidden" id="click_content_article_id" class="required modal-value" name="jform[params][acticle_id]" value="<?php echo $params['acticle_id'] ?>" aria-required="true" required="required">    
                        <input type="hidden" id="click_content_article_link" name="jform[params][acticle_link]" value="<?php echo $params['acticle_link'] ?>">    
                        <script type="text/javascript">
                            function jSelectArticle(id, title, catid, object, link, lang) {
                                document.getElementById("click_content_article_id").value = id;
                                document.getElementById("click_content_article_id_name").value = title;
                                document.getElementById("click_content_article_link").value = link;
                                SqueezeBox.close();
                            }
                        </script>
                    </span>
                    <?php
                    $menu = JMenu::getInstance('site');
                    $menuElems = JHtml::_('menu.menuitems', array('published' => 1));
                    $generatedMenuElems = array();
                    foreach ($menuElems as $menuElem) {
                        $elemObj = new stdClass();
                        $elemObj->text = $menuElem->text;
                        if (isset($menuElem->disable)) {
                            $elemObj->disable = $menuElem->disable;
                        }
                        $parts = explode('.', $menuElem->value);
                        if (count($parts) > 1) {
                            $route = $menu->getItem($parts[count($parts) - 1]);
                            if ($route) {
                                $elemObj->value = $route->link . '&Itemid=' . $parts[count($parts) - 1];
                            } else {
                                $elemObj->value = $menuElem->value;
                            }
                        } else {
                            $elemObj->value = $menuElem->value;
                        }
                        $generatedMenuElems[] = $elemObj;
                    }
                    ?>
                    <span id="click_content_menuitem" class="click_content_block" style="<?php echo ($params['imgp_click'] == 'menuitem')?'display:inline-block;':'display:none;' ?>">
                        <select style="width:250px;" name="jform[params][click_content_menuitem]" class="droppics-chosen-select" id="click_content_menuitem_id">
                            <option value=""><?php echo JText::_('COM_DROPPICS_VIEW_DROPPICS_SELECT') ?></option>
                            <?php echo JHtml::_('select.options', $generatedMenuElems ,'value','text' , $params['click_content_menuitem']); ?>
                        </select>
                    </span>
                    <span id="click_content_custom" class="click_content_block" style="<?php echo ($params['imgp_click'] == 'custom')?'display:inline-block;':'display:none;' ?>">
                        <input name="jform[params][custom_link]" type="text" class="input-medium" id="click_content_custom_id" value="<?php echo $params['custom_link'] ?>" placeholder="http://" size="35" />
                    </span>
                    
                    <?php } ?>
                </div>

                <div class="paraminput input-append w100" id="imgp_click_target_wrap" style="<?php echo ($params['imgp_click'] == 'nothing'|| $params['imgp_click'] == 'lightbox')?'display:inline-block;':'display:none;' ?>">
                    <label id="imgp_click_target-lbl" for="imgp_click_target"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_TARGET'); ?></label>
                    <select type="text" name="jform[params][imgp_click_target]" id="imgp_click_target">
                        <option <?php if($params['imgp_click_target'] == 'current') echo 'selected' ?> value="current"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_TARGET_CURRENT'); ?></option>
                        <option <?php if($params['imgp_click_target'] == '_blank') echo 'selected' ?> value="_blank"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_TARGET_BLANK'); ?></option>
                        <option <?php if($params['imgp_click_target'] == 'lightbox') echo 'selected' ?> value="lightbox"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_TARGET_LIGHTBOX'); ?></option>
                    </select>
                </div>
            </div>
            <div class="wauto nomargin">
                <label id="click_content_custom_title-lbl" for="click_content_custom_title"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CAPTION'); ?> :</label>
                <input name="jform[params][content_custom_title]" type="text" class="input-large" id="click_content_custom_title" value="<?php echo $params['content_custom_title'] ?>" size="35" />
            </div>
             <?php if( !$params["is_video"]) {  ?>
             <div class="wauto" >                 
                <label id="follow_custom_link-lbl" for="follow_custom_link">
                    <input name="jform[params][follow_custom_link]" type="checkbox" class="input" style="margin-top:0" id="follow_custom_link" value="1" <?php if($params['follow_custom_link']) { echo 'checked';} ?>  />
                    <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_FOLLOW_CUSTOM_LINK'); ?></label>                
            </div>
             <?php } ?>
            
             <div class="wauto" >                 
                <label id="show_caption-lbl" for="show_caption">
                    <input name="jform[params][show_caption]" type="checkbox" class="input" style="margin-top:0" id="show_caption" value="1" <?php if($params['show_caption']) { echo 'checked';} ?>  />
                    <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_SHOW_CAPTION'); ?></label>                
            </div>
            
            <div class="wauto nomargin">
                <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_WIDTH'); ?></h5>
                <div id="imgp_source">
                    <label class="radio">
                        <input type="radio" class="imgp_source" name="imgp_source" value="thumbnail" <?php echo ($params['imgp_source'] == 'thumbnail')?'checked':'' ?> />
                        <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_WIDTH_THUMBNAIL') ?>
                        <?php if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                            <a class="editImage" title="<?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_EDIT') ?>"><i class="icon-pencil"></i></a>
                        <?php endif; ?>
                    </label>
                    <label class="radio">
                        <input type="radio" class="imgp_source" name="imgp_source" value="original" <?php echo ($params['imgp_source'] == 'original')?'checked':'' ?> />
                        <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_WIDTH_ORIGINAL') ?>
                        <?php if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                            <a class="editImage" title="<?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_EDIT') ?>"><i class="icon-pencil"></i></a>
                        <?php endif; ?>
                    </label>
                    <?php if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own') || $this->canDo->get('core.delete')): ?>
                        <label class="radio template" style="display: none">
                            <input type="radio" class="imgp_source" name="imgp_source" value=""  />
                            <span></span>
                            <?php if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                                <a class="editImage" title="<?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_EDIT') ?>"><i class="icon-pencil"></i></a>
                            <?php endif; ?>
                            <?php if ($this->canDo->get('core.delete')): ?>
                                <a id="imgp_delete" class="hidden deleteImage" title="<?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_DELETE') ?>"><i class="icon-trash"></i></a>
                            <?php endif; ?>
                        </label>
                        <label class="radio">
                            <input type="radio" class="imgp_source" name="imgp_source" value="custom" <?php echo ($params['imgp_source'] == 'custom')?'checked':'' ?> />
                            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_WIDTH_NEW') ?>
                        </label>
                    <?php endif; ?>
                    <input type="hidden" name="jform[params][imgp_source]" value="<?php echo $params['imgp_source'] ?>">
                </div>
                <?php if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                    <div class="form-inline" id="newCustomSize" style="<?php echo ($params['imgp_source'] == 'custom')?'display:block;':'display:none;' ?>">
                        <div class="control-group">
                            <div class="input-append"><input type="text" name="jform[params][customWidth]" class="input-mini" id="customWidth" value="<?php echo $params['customWidth'] ?>"><span class="add-on">px</span></div> x <div class="input-append"><input type="text" name="jform[params][customHeight]" class="input-mini" id="customHeight" value="<?php echo $params['customHeight'] ?>"><span class="add-on">px</span></div>
                        </div>
                        <div class="control-group">
                            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_IMAGE_SAVE_TO_FILENAME'); ?> : <input type="text" name="jform[params][customFilename]" id="customFilename" class="input-large" value="<?php echo $params['customFilename'] ?>" />
                        </div>
                        <button id="applyNewCustomSize" class="btn"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_IMAGE_RESIZE'); ?></button>
                    </div>
                
                    <div class="form-inline" id="changeCustomFile" style="<?php echo ($params['imgp_source'] =='' || $params['imgp_source'] == 'original' || $params['imgp_source'] == 'thumbnail' || $params['imgp_source'] == 'custom')?'display:none;':'display:block;' ?>">                      
                        <div class="control-group">
                            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_IMAGE_SAVE_TO_FILENAME'); ?> : <input type="text" name="jform[params][newCustomFilename]" id="newCustomFilename" class="input-large" value="<?php echo $params['customFilename'] ?>" />                            
                        </div>
                        <button id="applyCustomFile" class="btn"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_IMAGE_RESIZE'); ?></button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="floating wauto">
                <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_POSITION'); ?></h5>
                <div id="imagealign" class="btn-group" data-toggle="buttons-radio">
                    <button class="insertleft btn <?php echo ($params['imagealign'] == 'left')?'active':'' ?>" data-align="left" type="button" ><i class="icon-align-left"></i></button>
                    <button class="insert btn <?php echo ($params['imagealign'] == 'none')?'active':'' ?>" data-align="none" type="button" ><i class="icon-align-center"></i></button>
                    <button class="insertright btn <?php echo ($params['imagealign'] == 'right')?'active':'' ?>" data-align="right" type="button" ><i class="icon-align-right"></i></button>
                </div>
                <input type="hidden" name="jform[params][imagealign]" value="<?php echo $params['imagealign'] ?>">
            </div>
        </fieldset>
        <?php if( $params["is_video"]) {  ?>
        <!--        video params    -->
            <input type="hidden" name="jform[params][is_video]" value="1">
            <input type="hidden" name="jform[params][vid]" value="<?php echo $params["vid"];?>">
            <input type="hidden" name="jform[params][directLink]" value="<?php echo $params["directLink"];?>">
            <input type="hidden" name="jform[params][ratio]" value="<?php echo $params["ratio"];?>">
            <input type="hidden" name="jform[params][video_url]" value="<?php echo $params["video_url"];?>">
            <input type="hidden" name="jform[params][video_type]" value="<?php echo $params["video_type"];?>">
                        
        <?php } ?>
    </div>
    
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
            
            
            <div class="control-group" <?php if( $params["is_video"]) { echo 'style="display:none"'; }?> >
                <?php echo $this->form->getLabel('file'); ?> 
                <div class="controls">
                    <?php echo $this->form->getInput('file'); ?>
                </div>
            </div>
            <?php if( $params["is_video"]) { ?>
            <div class="control-group">
                <label><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_VIDEO_TYPE'); ?>:&nbsp; <?php echo ucfirst($params["video_type"]);?></label>
                    
            </div>    
            <div class="control-group">
                <label><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_VIDEO_URL'); ?>: <a href="<?php echo $params["video_url"];?>" target="_blank"><?php echo $params["video_url"];?></a></label>                    
            </div>    
            <?php }?>
            <input type="hidden" class="droppicsinput" name="<?php echo JSession::getFormToken() ?>" value="1" />
            <button class="btn" type="submit"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_SAVE'); ?></button>
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
            <button class="btn" type="submit"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_SAVE'); ?></button>
    </div>
	<div class="clr"></div>
</form>
<?php endif; ?>
<script type="text/javascript">
    var custom_size = '<?php echo $params['imgp_source']; ?>';
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