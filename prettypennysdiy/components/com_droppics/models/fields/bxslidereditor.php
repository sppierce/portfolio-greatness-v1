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
class JFormFieldBxslidereditor extends JFormField
{
	/**
	 * Editor for bxslider
	 */
	protected $type = 'bxslidereditor';

        /**
	 */
	protected function getInput()
	{
		
                $baseurl = JURI::root();
		
                if($this->value===''){
                    $this->value = JText::_('PLG_DROPPICS_BXSLIDER_IMAGE_ADD_CONTENT');
                }
                
                $content = '';
		// Initialize some field attributes.
                $content .= '<div style="clear: both;"></div><div id="bxsliderimagewrapper">'
                        . '     <div id="bxsliderimagehtmlwrapper" style="width:100%; height:30%;">'
                        . '         <div id="bxsliderimagehtmldad"><i class="icon-move"></i></div>'
                        . '         <div id="bxsliderimagehtml" contenteditable="true">'.$this->value.'</div>'
                        . '     </div>'
                        . '     <img id="bxsliderimage" style="width:100%;" />'
                        . '</div>';
		
                
		$script  = 'CKEDITOR_BASEPATH = "'.$baseurl.'components/com_droppics/assets/ckeditor/";'
                        . 'jQuery.getScript("'.$baseurl.'components/com_droppics/assets/ckeditor/ckeditor.js",function(){'
                        . '         CKEDITOR.disableAutoInline = true;
                                    editor = CKEDITOR.inline( "bxsliderimagehtml" );
                                    jQuery("#bxsliderimage").attr("src",jQuery("#preview .selimg.selected img.img").attr("src"));
                                    jQuery( "#bxsliderimagehtmlwrapper" ).resizable({
                                        containment: "#bxsliderimage",
                                        handles: "n, e, s, w, se, sw, ne",
                                        stop: function() {
                                            wh = parseInt(jQuery("#bxsliderimagewrapper").height());
                                            ww = parseInt(jQuery("#bxsliderimagewrapper").width());

                                            hh = parseInt(jQuery("#bxsliderimagehtmlwrapper").height());
                                            hw = parseInt(jQuery("#bxsliderimagehtmlwrapper").width());

                                            pos = jQuery("#bxsliderimagehtmlwrapper").position();

                                            jQuery("#jform_params_bxslider_image_height").val(100-(wh-hh)*100/wh);
                                            jQuery("#jform_params_bxslider_image_width").val(100-(ww-hw)*100/ww);
                                            
                                            jQuery("#jform_params_bxslider_image_top").val(100-(wh-pos.top)*100/wh);
                                            jQuery("#jform_params_bxslider_image_left").val(100-(ww-pos.left)*100/ww);
                                        }
                                    });
                                    jQuery( "#bxsliderimagehtmlwrapper" ).draggable({
                                        containment: "#bxsliderimage", 
                                        handle: "#bxsliderimagehtmldad",
                                        scroll: false,
                                        stop: function() {
                                            wh = parseInt(jQuery("#bxsliderimagewrapper").height());
                                            ww = parseInt(jQuery("#bxsliderimagewrapper").width());

                                            pos = jQuery("#bxsliderimagehtmlwrapper").position();


                                            jQuery("#jform_params_bxslider_image_top").val(100-(wh-pos.top)*100/wh);
                                            jQuery("#jform_params_bxslider_image_left").val(100-(ww-pos.left)*100/ww);
                                        }
                                    });

                                    wh = parseInt(jQuery("#bxsliderimagewrapper").height());
                                    ww = parseInt(jQuery("#bxsliderimagewrapper").width());
                                    
                                    jQuery("#bxsliderimagehtmlwrapper").css("height",(jQuery("#jform_params_bxslider_image_height").val()*wh/100)); 
                                    jQuery("#bxsliderimagehtmlwrapper").css("width",(jQuery("#jform_params_bxslider_image_width").val()*ww/100)); 
                                    
                                    jQuery("#bxsliderimagehtmlwrapper").css("top",(jQuery("#jform_params_bxslider_image_top").val()*wh/100)); 
                                    jQuery("#bxsliderimagehtmlwrapper").css("left",(jQuery("#jform_params_bxslider_image_left").val()*ww/100)); 
                                ;';
                                $script .= 'jQuery("#jform_params_bxslider_image_bgcolor").each(function() {
                                                jQuery(this).minicolors({
                                                        control: jQuery(this).attr("data-control") || "hue",
                                                        position: jQuery(this).attr("data-position") || "right",
                                                        theme: "bootstrap",
                                                        change: function(hex) {
                                                            jQuery("#bxsliderimagehtml").css("background-color",hex2rgba(hex,jQuery( "#jform_params_bxslider_image_transparency" ).val()/100));
                                                        }
                                                });
                                        });
                                        jQuery( "#jform_params_bxslider_image_transparency" ).on("slide",function(){
                                            jQuery("#bxsliderimagehtml").css("background-color",hex2rgba(jQuery("#jform_params_bxslider_image_bgcolor").val(),jQuery( "#jform_params_bxslider_image_transparency" ).val()/100));
                                        });
                                        jQuery("#bxsliderimagehtml").css("background-color",hex2rgba(jQuery("#jform_params_bxslider_image_bgcolor").val(),jQuery( "#jform_params_bxslider_image_transparency" ).val()/100));';
                            $script .= '});';
                
                $script = '<script type="text/javascript">jQuery( document ).ready( function(){'.$script.';} );</script>';

                $content .= '<input name="'.$this->name.'" type="hidden" id="'.$this->id.'"' .
				' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'" />';
                $content = $content.$script;

		return $content ;
	}
}