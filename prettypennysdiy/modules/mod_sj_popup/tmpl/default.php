<?php
/**
 * @package Sj Popup
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;
JHtml::stylesheet('modules/' . $module->module . '/assets/css/style.css');
if (!defined('SMART_JQUERY') && $params->get('include_jquery', 0) == "1") {
    JHtml::script('modules/' . $module->module . '/assets/js/jquery-1.8.2.min.js');
    JHtml::script('modules/' . $module->module . '/assets/js/jquery-noconflict.js');
    define('SMART_JQUERY', 1);
}
	$tag_id = 'sj-popup-'.$module->id;
	if($params->get('show_btn_close_popup') == 1){
		$close_popup = '<div class="sj-close-popup"></div>';
	}else{
		$close_popup = '';
	}
	echo '<div id="'.$tag_id.'" class="sj_popup_wrap">';
		if($params->get('position_popup') == 'center'){
			echo '<div class="popup-center sj-popup">'.
					'<div class="relative">'.$close_popup.
						$params->get('content_popup').
					'</div>'
				.'</div>';
		}
		if($params->get('position_popup') == 'left'){
			echo '<div class="popup-bottom-left sj-popup">'.
					'<div class="relative">'.$close_popup.
						$params->get('content_popup').
					'</div>'
				.'</div>';
		}
		if($params->get('position_popup') == 'right'){
			echo '<div class="popup-bottom-right sj-popup">'.
					'<div class="relative">'.$close_popup.
						$params->get('content_popup').
					'</div>'
				.'</div>';
		}
		if($params->get('position_popup') == 'fullsize'){
			echo '<div class="popup-bottom-fullsize sj-popup">'.
					'<div class="relative">'.$close_popup.
						$params->get('content_popup').
					'</div>'
				.'</div>';
		}
	echo '</div>';
?>
<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function ($){
		
		<?php if($params->get('position_popup') == 'center'){	?>
			var height = parseInt(parseInt($('#<?php echo $tag_id;?> .sj-popup').css('height'))/2);
			var marginTop = parseInt($('#<?php echo $tag_id;?> .sj-popup').css('marginTop'));
			var top = height - marginTop;
			$('#<?php echo $tag_id;?> .sj-popup').css('marginTop','-'+top+'px');
			var width = parseInt(parseInt($('#<?php echo $tag_id;?> .sj-popup').css('width'))/2);
			var marginLeft = parseInt($('#<?php echo $tag_id;?> .sj-popup').css('marginLeft'));
			var left = width - marginLeft;
			$('#<?php echo $tag_id;?> .sj-popup').css('marginLeft','-'+left+'px');
			$('#<?php echo $tag_id;?>').css('opacity','1');
		<?php } ?>
			$('#<?php echo $tag_id;?> .sj-close-popup').click(function(){
				$('#<?php echo $tag_id;?>').remove();
			});
		<?php if($params->get('time_show_popup') != 0){ ?>
			$('#<?php echo $tag_id;?>').css('display','none');
			var time = <?php echo $params->get('time_show_popup');?>;
			var first = 1;
			var q = setInterval(function(){
				if(first == 1){
					first++;
				}else{
					$('#<?php echo $tag_id;?>').css('display','block');
					clearInterval(q);
				}
			},time);
		<?php }else{ ?>
			$('#<?php echo $tag_id;?>').css('display','block');
		<?php } ?>
		<?php if($params->get('show_btn_close_popup') == 0){ ?>
			var check = 0;
			$('#<?php echo $tag_id;?> .sj-popup').click(function(){
				check = 1;;
			});
			$('#<?php echo $tag_id;?>').not('.sj-popup').click(function(){
				if(check == 1){check = 0;return;}
				$('#<?php echo $tag_id;?>').remove();
			});
		<?php } ?>
		$('#<?php echo $tag_id;?>').css('opacity','1');
	});
//]]>
</script>
<?php 
		if(!isset($_COOKIE['check_popup'])) { 
		   $cookietime = (int)($params->get('cookietime',1))*3600;
		   $time = ($cookietime > 0)?time()+ $cookietime:time();
		   setcookie('check_popup', 1, $time ,'/'); 
		}
		 
?>
