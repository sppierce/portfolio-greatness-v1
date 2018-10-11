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

?>
<div id="mybootstrap">
<h3><?php echo JText::_('COM_DROPPICS_VIEW_IMAGESIZES_HEADING');?></h3>
<div id="view_imagesizes">
    
    <div class="control-group">
        <div class="input-append">
            <label><?php echo JText::_('COM_DROPPICS_VIEW_IMAGESIZES_TITLE');?></label>
            <input name="jform[params][customTitle]" class="input" id="customTitle" value="" type="text" />
        </div>
    </div>
    
    <div class="control-group">
        <div class="input-append">
             <label><?php echo JText::_('COM_DROPPICS_VIEW_IMAGESIZES_WIDTH');?>*</label>
            <input name="jform[params][customWidth]" class="input-mini" id="customWidth" value="" type="text" />
            <span class="add-on">px</span>
        </div>
        <div class="input-append">
             <label><?php echo JText::_('COM_DROPPICS_VIEW_IMAGESIZES_HEIGHT');?></label>
            <input name="jform[params][customHeight]" class="input-mini" id="customHeight" placeholder="auto" value="" type="text" />
            <span class="add-on">px</span>
        </div>
        <div class="input-append">
            <label>&nbsp;</label>
            <a class="btn btn-flat" id="addNewSize"><?php echo JText::_('COM_DROPPICS_ACTION_ADD');?> </a>
        </div>
    </div>
    
    <ul class="imgsizes_list">
         <?php 
        if(!empty($this->sizes)) {
            foreach ($this->sizes as $key => $value) {
                echo '<li data-size="'. trim(str_replace("px","", $value)).'">'. str_replace_first("x","px x ", $value). ' <a class="btn_removeSize"><i class="icon-remove"></i></a>' . '</li>';
            }
        }?>
    </ul>
   
    
    <div class="bottom right right-block">
        <a class="btn"  id="cancelCustomSize"><?php echo JText::_('COM_DROPPICS_JS_CANCEL');?></a>
        <a class="btn"  id="confirmCustomSize"><?php echo JText::_('COM_DROPPICS_JS_CONFIRM');?></a>
        
    </div>
</div>  
</div>
<?php
function str_replace_first($from, $to, $subject) { 
    $from = '/'.preg_quote($from, '/').'/'; return preg_replace($from, $to, $subject, 1);     
}
?>

<script>
 var fid = "<?php echo $this->fieldid;?>";
jQuery(document).ready(function($) {    
    $("#addNewSize").click(function(e){
        e.preventDefault();
        newWidth = $("#customWidth").val();
        newHeight = $("#customHeight").val();
        customTitle = $("#customTitle").val();
        if(newWidth=="" ) {
            $("#customWidth").focus();
            return false;
        }
        if(newHeight=="") {
            newSize = newWidth + 'px' + ' x ' + 'auto';    
            newHeight = '(auto)';
        }else {
            newSize = newWidth + 'px' + ' x ' + newHeight+'px';
        }
        
        if(customTitle != "") {
            newItem = "<li data-size='"+customTitle+"-"+newWidth+"x"+newHeight+"'>"+customTitle+"-"+ newSize + ' <a class="btn_removeSize"><i class="icon-remove"></i></a>' + '</li>';
        }else {
            newItem = "<li data-size='"+newWidth+"x"+newHeight+"'>"+ newSize + ' <a class="btn_removeSize"><i class="icon-remove"></i></a>' + '</li>';
        }
       $(".imgsizes_list").append(newItem);
       //clear old val
       $("#customWidth").val("");
       $("#customHeight").val("");
       $("#customTitle").val("");
       
       removeSizeInit();
    })
      
    $("#confirmCustomSize").click(function(e){
        fSizes = [];
        $(".imgsizes_list li").each(function(){
            fSizes.push($(this).data('size')+"px");
        })
        fVal = fSizes.join("; ");        
        parent.top.jQuery("#"+fid).val(fVal);          
        window.parent.SqueezeBox.close();
    })
    
    $("#cancelCustomSize").click(function(e){
       window.parent.SqueezeBox.close();
    });
    
    
    removeSizeInit = function() {
        $(".btn_removeSize").click(function(e){ 
             e.preventDefault();            
             $(this).parent().remove();
        })
    }    
    removeSizeInit();
})
</script>    