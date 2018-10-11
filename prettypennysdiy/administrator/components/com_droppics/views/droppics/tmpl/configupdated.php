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
?>
<div id="mybootstrap">
    <div><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_CONFIGUPDATED_DESC'); ?></div>

    <div class="progress progress-striped active hide">
      <div class="bar" style="width: 0%;"></div>
    </div>
    <div class="pull-right">
        <button class="btn" id="proceed">
            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_CONFIGUPDATED_DO'); ?>
        </button>
        <button class="btn" id="close" onclick="window.parent.SqueezeBox.close();">
            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_CONFIGUPDATED_CANCEL'); ?>
        </button>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var nextstep = 0;
        $('#close').click(function(){
            $('#modal-alert',window.parent.document).modal('hide');
            $('body',window.parent.document).removeClass('modal-open');
            $('.modal-backdrop',window.parent.document).remove();
        });
        $('#proceed').click(function(){
            $('.progress').removeClass('hide');
            doit();    
        });
        function doit(){
            $.ajax({
                    url     :   "index.php?option=com_droppics&task=files.regeneratePictures&nb="+nextstep,
                    type    :   "POST"
                }).done(function(data){
                    result = jQuery.parseJSON(data);
                    if(result.response!==true){
                        $('.progress').addClass('hide');
                        $('#mybootstrap').prepend('<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong> An error occurs.</div>'); /*todo translate*/
                    }else if(result.datas.end===true){
                        $('#mybootstrap').prepend('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Done!</strong></div>'); /*todo translate*/
                        $('.progress').addClass('hide');
                        $('#proceed').addClass('hide');
                        setTimeout(function(){ window.parent.location.reload(); }, 3000);
                    }else if(result.datas.end===false){
                        $('.progress .bar').width(result.datas.progress+'%');
                        nextstep++;
                        doit(nextstep);
                    }
                });
        }
    });
</script>