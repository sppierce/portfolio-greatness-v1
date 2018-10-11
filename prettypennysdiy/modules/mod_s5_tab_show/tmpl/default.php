<?php 
/**
@licenseGNU General Public License version 2 or later; see LICENSE.txt
Author: Shape 5 - Professional Template Community
Available for download at www.shape5.com
Copyright Shape5 LLC
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$s5_tab_show_slides_number = 1;
$s5_tab_show_buttons_number = 1;
$s5_tab_show_random_id = mt_rand (1000000000, mt_getrandmax());
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base().'modules/mod_s5_tab_show/s5_tab_show/s5_tab_show.css');
$doc->addScript(JUri::base().'modules/mod_s5_tab_show/s5_tab_show/s5_tab_show.js','text/javascript');
?>
<?php if ($pretext_outside != "") { ?>
	<div class="s5_tab_show_pretext_outside"><?php echo $pretext_outside; ?></div>
<?php } ?>
<div class="s5_tab_show_outer_wrap" id="s5_tab_show_outer_wrap_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($main_background == "yes") { ?> style="<?php if ($main_background_image == "") {?>background:<?php echo $main_background_color; ?> !important;<?php } ?><?php if ($main_background_image != "") {if ($main_background_image_size == "custom") {$main_background_image_size = $main_background_image_size_custom; }?>background-color:<?php echo $main_background_color; ?> !important;background-image:url(<?php echo $main_background_image; ?>) !important;background-size: <?php echo $main_background_image_size; ?>;background-attachment: <?php echo $main_background_image_attachment; ?> !important;background-repeat:<?php echo $main_background_image_repeat; ?> !important;background-position:<?php echo $main_background_image_position; ?>;<?php } ?>"<?php } ?>>
	<div class="<?php if ($custom_class != "") { echo $custom_class; echo " "; } ?>s5_tab_show_outer1 s5_tab_show_padding_<?php echo $padding; ?> s5_tab_show_effect_<?php echo $slide_snap; ?><?php if ($pause_onhover == "enabled") { ?> s5_tab_show_pause_onhover_<?php echo $pause_onhover; } ?><?php if ($hide_tabs == "enabled") { ?> s5_tab_show_hide_tabs_<?php echo $hide_tabs; } ?><?php if ($hide_arrows == "enabled") { ?> s5_tab_show_hide_arrows_<?php echo $hide_arrows; } ?><?php if ($show_mobile_arrows == "enabled") { ?> s5_tab_show_mobile_arrows_<?php echo $show_mobile_arrows; } ?><?php if ($show_mobile_tabs == "enabled") { ?> s5_tab_show_mobile_tabs_<?php echo $show_mobile_tabs; } ?><?php if ($center_arrows == "enabled") { ?> s5_tab_show_center_arrows_<?php echo $center_arrows; } ?><?php if ($center_tabs == "enabled") { ?> s5_tab_show_center_tabs_<?php echo $center_tabs; } ?><?php if ($tab_text_hover == "enabled") { ?> s5_tab_show_tab_text_hover_<?php echo $tab_text_hover; } ?>">
		<?php if ($button_location == "top_outside") { ?>	
			<div class="s5_tab_show_slides_buttons s5_tab_show_slides_buttons_top" id="s5_tab_show_slides_buttons_id<?php echo $s5_tab_show_random_id; ?>">
				<?php if (in_array("s5_tab1", $tab_show_positions) && JModuleHelper::getModules('s5_tab1')) { ?>
					<div <?php if ($tab1_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab1_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab1_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab1_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab1_text; ?></div>
						<?php } ?>
						<?php if ($tab1_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab2", $tab_show_positions) && JModuleHelper::getModules('s5_tab2')) { ?>
					<div <?php if ($tab2_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab2_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab2_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab2_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab2_text; ?></div>
						<?php } ?>
						<?php if ($tab2_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab3", $tab_show_positions) && JModuleHelper::getModules('s5_tab3')) { ?>
					<div <?php if ($tab3_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab3_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab3_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab3_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab3_text; ?></div>
						<?php } ?>
						<?php if ($tab3_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab4", $tab_show_positions) && JModuleHelper::getModules('s5_tab4')) { ?>
					<div <?php if ($tab4_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab4_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab4_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab4_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab4_text; ?></div>
						<?php } ?>
						<?php if ($tab4_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab5", $tab_show_positions) && JModuleHelper::getModules('s5_tab5')) { ?>
					<div <?php if ($tab5_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab5_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab5_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab5_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab5_text; ?></div>
						<?php } ?>
						<?php if ($tab5_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab6", $tab_show_positions) && JModuleHelper::getModules('s5_tab6')) { ?>
					<div <?php if ($tab6_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab6_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab6_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab6_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab6_text; ?></div>
						<?php } ?>
						<?php if ($tab6_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab7", $tab_show_positions) && JModuleHelper::getModules('s5_tab7')) { ?>
					<div <?php if ($tab7_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab7_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab7_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab7_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab7_text; ?></div>
						<?php } ?>
						<?php if ($tab7_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab8", $tab_show_positions) && JModuleHelper::getModules('s5_tab8')) { ?>
					<div <?php if ($tab8_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab8_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab8_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab8_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab8_text; ?></div>
						<?php } ?>
						<?php if ($tab8_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab9", $tab_show_positions) && JModuleHelper::getModules('s5_tab9')) { ?>
					<div <?php if ($tab9_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab9_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab9_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab9_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab9_text; ?></div>
						<?php } ?>
						<?php if ($tab9_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab10", $tab_show_positions) && JModuleHelper::getModules('s5_tab10')) { ?>
					<div <?php if ($tab10_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab10_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab10_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab10_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab10_text; ?></div>
						<?php } ?>
						<?php if ($tab10_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab11", $tab_show_positions) && JModuleHelper::getModules('s5_tab11')) { ?>
					<div <?php if ($tab11_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab11_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab11_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab11_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab11_text; ?></div>
						<?php } ?>
						<?php if ($tab11_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab12", $tab_show_positions) && JModuleHelper::getModules('s5_tab12')) { ?>
					<div <?php if ($tab12_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab12_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab12_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab12_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab12_text; ?></div>
						<?php } ?>
						<?php if ($tab12_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab13", $tab_show_positions) && JModuleHelper::getModules('s5_tab13')) { ?>
					<div <?php if ($tab13_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab13_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab13_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab13_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab13_text; ?></div>
						<?php } ?>
						<?php if ($tab13_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab14", $tab_show_positions) && JModuleHelper::getModules('s5_tab14')) { ?>
					<div <?php if ($tab14_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab14_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab14_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab14_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab14_text; ?></div>
						<?php } ?>
						<?php if ($tab14_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab15", $tab_show_positions) && JModuleHelper::getModules('s5_tab15')) { ?>
					<div <?php if ($tab15_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab15_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab15_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab15_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab15_text; ?></div>
						<?php } ?>
						<?php if ($tab15_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab16", $tab_show_positions) && JModuleHelper::getModules('s5_tab16')) { ?>
					<div <?php if ($tab16_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab16_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab16_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab16_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab16_text; ?></div>
						<?php } ?>
						<?php if ($tab16_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab17", $tab_show_positions) && JModuleHelper::getModules('s5_tab17')) { ?>
					<div <?php if ($tab17_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab17_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab17_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab17_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab17_text; ?></div>
						<?php } ?>
						<?php if ($tab17_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab18", $tab_show_positions) && JModuleHelper::getModules('s5_tab18')) { ?>
					<div <?php if ($tab18_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab18_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab18_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab18_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab18_text; ?></div>
						<?php } ?>
						<?php if ($tab18_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab19", $tab_show_positions) && JModuleHelper::getModules('s5_tab19')) { ?>
					<div <?php if ($tab19_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab19_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab19_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab19_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab19_text; ?></div>
						<?php } ?>
						<?php if ($tab19_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab20", $tab_show_positions) && JModuleHelper::getModules('s5_tab20')) { ?>
					<div <?php if ($tab20_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab20_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab20_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab20_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab20_text; ?></div>
						<?php } ?>
						<?php if ($tab20_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<div class="s5_tab_show_clear"></div>
			</div>
		<?php } ?>
		<?php if ($arrow_location == "top_outside") { ?>	
			<div class="s5_tab_show_next_prev_wrapper  s5_tab_show_next_prev_top">
				<div <?php echo $mouse_effect_arrows; ?>="s5_tab_show_trigger_prev(this.id)" class="s5_tab_show_next_prev s5_tab_show_prev" id="s5_tab_show_prev_id<?php echo $s5_tab_show_random_id; ?>"></div>
				<div <?php echo $mouse_effect_arrows; ?>="s5_tab_show_trigger_next(this.id)" class="s5_tab_show_next_prev s5_tab_show_next" id="s5_tab_show_next_id<?php echo $s5_tab_show_random_id; ?>"></div>
				<div class="s5_tab_show_clear"></div>
			</div>
		<?php } ?>
		<div class="s5_tab_show_outer2">
		<?php if ($pretext_inside != "") { ?>
			<div class="s5_tab_show_pretext_inside"><?php echo $pretext_inside; ?></div>
		<?php } ?>
			<?php if ($button_location == "top" || $button_location == "disabled") { ?>	
				<div <?php if ($button_location == "disabled") { ?>style="display:none"<?php } ?> class="s5_tab_show_slides_buttons s5_tab_show_slides_buttons_top" id="s5_tab_show_slides_buttons_id<?php echo $s5_tab_show_random_id; ?>">
					<?php if (in_array("s5_tab1", $tab_show_positions) && JModuleHelper::getModules('s5_tab1')) { ?>
						<div <?php if ($tab1_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab1_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab1_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab1_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab1_text; ?></div>
							<?php } ?>
							<?php if ($tab1_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab2", $tab_show_positions) && JModuleHelper::getModules('s5_tab2')) { ?>
						<div <?php if ($tab2_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab2_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab2_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab2_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab2_text; ?></div>
							<?php } ?>
							<?php if ($tab2_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab3", $tab_show_positions) && JModuleHelper::getModules('s5_tab3')) { ?>
						<div <?php if ($tab3_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab3_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab3_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab3_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab3_text; ?></div>
							<?php } ?>
							<?php if ($tab3_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab4", $tab_show_positions) && JModuleHelper::getModules('s5_tab4')) { ?>
						<div <?php if ($tab4_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab4_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab4_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab4_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab4_text; ?></div>
							<?php } ?>
							<?php if ($tab4_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab5", $tab_show_positions) && JModuleHelper::getModules('s5_tab5')) { ?>
						<div <?php if ($tab5_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab5_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab5_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab5_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab5_text; ?></div>
							<?php } ?>
							<?php if ($tab5_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab6", $tab_show_positions) && JModuleHelper::getModules('s5_tab6')) { ?>
						<div <?php if ($tab6_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab6_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab6_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab6_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab6_text; ?></div>
							<?php } ?>
							<?php if ($tab6_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab7", $tab_show_positions) && JModuleHelper::getModules('s5_tab7')) { ?>
						<div <?php if ($tab7_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab7_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab7_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab7_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab7_text; ?></div>
							<?php } ?>
							<?php if ($tab7_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab8", $tab_show_positions) && JModuleHelper::getModules('s5_tab8')) { ?>
						<div <?php if ($tab8_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab8_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab8_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab8_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab8_text; ?></div>
							<?php } ?>
							<?php if ($tab8_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab9", $tab_show_positions) && JModuleHelper::getModules('s5_tab9')) { ?>
						<div <?php if ($tab9_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab9_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab9_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab9_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab9_text; ?></div>
							<?php } ?>
							<?php if ($tab9_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab10", $tab_show_positions) && JModuleHelper::getModules('s5_tab10')) { ?>
						<div <?php if ($tab10_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab10_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab10_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab10_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab10_text; ?></div>
							<?php } ?>
							<?php if ($tab10_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab11", $tab_show_positions) && JModuleHelper::getModules('s5_tab11')) { ?>
						<div <?php if ($tab11_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab11_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab11_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab11_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab11_text; ?></div>
							<?php } ?>
							<?php if ($tab11_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab12", $tab_show_positions) && JModuleHelper::getModules('s5_tab12')) { ?>
						<div <?php if ($tab12_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab12_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab12_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab12_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab12_text; ?></div>
							<?php } ?>
							<?php if ($tab12_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab13", $tab_show_positions) && JModuleHelper::getModules('s5_tab13')) { ?>
						<div <?php if ($tab13_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab13_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab13_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab13_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab13_text; ?></div>
							<?php } ?>
							<?php if ($tab13_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab14", $tab_show_positions) && JModuleHelper::getModules('s5_tab14')) { ?>
						<div <?php if ($tab14_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab14_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab14_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab14_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab14_text; ?></div>
							<?php } ?>
							<?php if ($tab14_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab15", $tab_show_positions) && JModuleHelper::getModules('s5_tab15')) { ?>
						<div <?php if ($tab15_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab15_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab15_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab15_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab15_text; ?></div>
							<?php } ?>
							<?php if ($tab15_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab16", $tab_show_positions) && JModuleHelper::getModules('s5_tab16')) { ?>
						<div <?php if ($tab16_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab16_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab16_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab16_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab16_text; ?></div>
							<?php } ?>
							<?php if ($tab16_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab17", $tab_show_positions) && JModuleHelper::getModules('s5_tab17')) { ?>
						<div <?php if ($tab17_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab17_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab17_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab17_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab17_text; ?></div>
							<?php } ?>
							<?php if ($tab17_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab18", $tab_show_positions) && JModuleHelper::getModules('s5_tab18')) { ?>
						<div <?php if ($tab18_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab18_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab18_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab18_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab18_text; ?></div>
							<?php } ?>
							<?php if ($tab18_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab19", $tab_show_positions) && JModuleHelper::getModules('s5_tab19')) { ?>
						<div <?php if ($tab19_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab19_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab19_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab19_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab19_text; ?></div>
							<?php } ?>
							<?php if ($tab19_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab20", $tab_show_positions) && JModuleHelper::getModules('s5_tab20')) { ?>
						<div <?php if ($tab20_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab20_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab20_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab20_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab20_text; ?></div>
							<?php } ?>
							<?php if ($tab20_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<div class="s5_tab_show_clear"></div>
				</div>
			<?php } ?>
			<?php if ($arrow_location == "top") { ?>	
				<div class="s5_tab_show_next_prev_wrapper  s5_tab_show_next_prev_top">
					<div <?php echo $mouse_effect_arrows; ?>="s5_tab_show_trigger_prev(this.id)" class="s5_tab_show_next_prev s5_tab_show_prev" id="s5_tab_show_prev_id<?php echo $s5_tab_show_random_id; ?>"></div>
					<div <?php echo $mouse_effect_arrows; ?>="s5_tab_show_trigger_next(this.id)" class="s5_tab_show_next_prev s5_tab_show_next" id="s5_tab_show_next_id<?php echo $s5_tab_show_random_id; ?>"></div>
					<div class="s5_tab_show_clear"></div>
				</div>
			<?php } ?>
			<div class="s5_tab_show_slides_container_wrap_outer">
				<?php if ($arrow_location == "overlay") { ?>	
					<div class="s5_tab_show_next_prev_wrapper  s5_tab_show_next_prev_bottom">
						<div <?php echo $mouse_effect_arrows; ?>="s5_tab_show_trigger_prev(this.id)" class="s5_tab_show_next_prev s5_tab_show_prev" id="s5_tab_show_prev_id<?php echo $s5_tab_show_random_id; ?>"></div>
						<div <?php echo $mouse_effect_arrows; ?>="s5_tab_show_trigger_next(this.id)" class="s5_tab_show_next_prev s5_tab_show_next" id="s5_tab_show_next_id<?php echo $s5_tab_show_random_id; ?>"></div>
						<div class="s5_tab_show_clear"></div>
					</div>
				<?php } ?>	
				<div class="s5_tab_show_slides_wrap" id="s5_tab_show_slides_wrap_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($main_slider_background == "yes") { ?> style="<?php if ($main_slider_background_image == "") {?>background:<?php echo $main_slider_background_color; ?> !important;<?php } ?><?php if ($main_slider_background_image != "") {if ($main_slider_background_image_size == "custom") {$main_slider_background_image_size = $main_slider_background_image_size_custom; }?>background-color:<?php echo $main_slider_background_color; ?> !important;background-image:url(<?php echo $main_slider_background_image; ?>) !important;background-size: <?php echo $main_slider_background_image_size; ?>;background-attachment: <?php echo $main_slider_background_image_attachment; ?> !important;background-repeat:<?php echo $main_slider_background_image_repeat; ?> !important;background-position:<?php echo $main_slider_background_image_position; ?>;<?php } ?>"<?php } ?>>
				<div class="s5_tab_show_slides_inner_wrap">
					<?php if (in_array("s5_tab1", $tab_show_positions) && JModuleHelper::getModules('s5_tab1')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab1_background == "yes") { ?> style="<?php if ($tab1_background_image == "") {?>background:<?php echo $tab1_background_color; ?> !important;<?php } ?><?php if ($tab1_background_image != "") {if ($tab1_background_image_size == "custom") {$tab1_background_image_size = $tab1_background_image_size_custom; }?>background-color:<?php echo $tab1_background_color; ?> !important;background-image:url(<?php echo $tab1_background_image; ?>) !important;background-size: <?php echo $tab1_background_image_size; ?>;background-attachment: <?php echo $tab1_background_image_attachment; ?> !important;background-repeat:<?php echo $tab1_background_image_repeat; ?> !important;background-position:<?php echo $tab1_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab1' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab2", $tab_show_positions) && JModuleHelper::getModules('s5_tab2')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab2_background == "yes") { ?> style="<?php if ($tab2_background_image == "") {?>background:<?php echo $tab2_background_color; ?> !important;<?php } ?><?php if ($tab2_background_image != "") {if ($tab2_background_image_size == "custom") {$tab2_background_image_size = $tab2_background_image_size_custom; }?>background-color:<?php echo $tab2_background_color; ?> !important;background-image:url(<?php echo $tab2_background_image; ?>) !important;background-size: <?php echo $tab2_background_image_size; ?>;background-attachment: <?php echo $tab2_background_image_attachment; ?> !important;background-repeat:<?php echo $tab2_background_image_repeat; ?> !important;background-position:<?php echo $tab2_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab2' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab3", $tab_show_positions) && JModuleHelper::getModules('s5_tab3')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab3_background == "yes") { ?> style="<?php if ($tab3_background_image == "") {?>background:<?php echo $tab3_background_color; ?> !important;<?php } ?><?php if ($tab3_background_image != "") {if ($tab3_background_image_size == "custom") {$tab3_background_image_size = $tab3_background_image_size_custom; }?>background-color:<?php echo $tab3_background_color; ?> !important;background-image:url(<?php echo $tab3_background_image; ?>) !important;background-size: <?php echo $tab3_background_image_size; ?>;background-attachment: <?php echo $tab3_background_image_attachment; ?> !important;background-repeat:<?php echo $tab3_background_image_repeat; ?> !important;background-position:<?php echo $tab3_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab3' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab4", $tab_show_positions) && JModuleHelper::getModules('s5_tab4')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab4_background == "yes") { ?> style="<?php if ($tab4_background_image == "") {?>background:<?php echo $tab4_background_color; ?> !important;<?php } ?><?php if ($tab4_background_image != "") {if ($tab4_background_image_size == "custom") {$tab4_background_image_size = $tab4_background_image_size_custom; }?>background-color:<?php echo $tab4_background_color; ?> !important;background-image:url(<?php echo $tab4_background_image; ?>) !important;background-size: <?php echo $tab4_background_image_size; ?>;background-attachment: <?php echo $tab4_background_image_attachment; ?> !important;background-repeat:<?php echo $tab4_background_image_repeat; ?> !important;background-position:<?php echo $tab4_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab4' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab5", $tab_show_positions) && JModuleHelper::getModules('s5_tab5')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab5_background == "yes") { ?> style="<?php if ($tab5_background_image == "") {?>background:<?php echo $tab5_background_color; ?> !important;<?php } ?><?php if ($tab5_background_image != "") {if ($tab5_background_image_size == "custom") {$tab5_background_image_size = $tab5_background_image_size_custom; }?>background-color:<?php echo $tab5_background_color; ?> !important;background-image:url(<?php echo $tab5_background_image; ?>) !important;background-size: <?php echo $tab5_background_image_size; ?>;background-attachment: <?php echo $tab5_background_image_attachment; ?> !important;background-repeat:<?php echo $tab5_background_image_repeat; ?> !important;background-position:<?php echo $tab5_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab5' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab6", $tab_show_positions) && JModuleHelper::getModules('s5_tab6')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab6_background == "yes") { ?> style="<?php if ($tab6_background_image == "") {?>background:<?php echo $tab6_background_color; ?> !important;<?php } ?><?php if ($tab6_background_image != "") {if ($tab6_background_image_size == "custom") {$tab6_background_image_size = $tab6_background_image_size_custom; }?>background-color:<?php echo $tab6_background_color; ?> !important;background-image:url(<?php echo $tab6_background_image; ?>) !important;background-size: <?php echo $tab6_background_image_size; ?>;background-attachment: <?php echo $tab6_background_image_attachment; ?> !important;background-repeat:<?php echo $tab6_background_image_repeat; ?> !important;background-position:<?php echo $tab6_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab6' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab7", $tab_show_positions) && JModuleHelper::getModules('s5_tab7')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab7_background == "yes") { ?> style="<?php if ($tab7_background_image == "") {?>background:<?php echo $tab7_background_color; ?> !important;<?php } ?><?php if ($tab7_background_image != "") {if ($tab7_background_image_size == "custom") {$tab7_background_image_size = $tab7_background_image_size_custom; }?>background-color:<?php echo $tab7_background_color; ?> !important;background-image:url(<?php echo $tab7_background_image; ?>) !important;background-size: <?php echo $tab7_background_image_size; ?>;background-attachment: <?php echo $tab7_background_image_attachment; ?> !important;background-repeat:<?php echo $tab7_background_image_repeat; ?> !important;background-position:<?php echo $tab7_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab7' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab8", $tab_show_positions) && JModuleHelper::getModules('s5_tab8')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab8_background == "yes") { ?> style="<?php if ($tab8_background_image == "") {?>background:<?php echo $tab8_background_color; ?> !important;<?php } ?><?php if ($tab8_background_image != "") {if ($tab8_background_image_size == "custom") {$tab8_background_image_size = $tab8_background_image_size_custom; }?>background-color:<?php echo $tab8_background_color; ?> !important;background-image:url(<?php echo $tab8_background_image; ?>) !important;background-size: <?php echo $tab8_background_image_size; ?>;background-attachment: <?php echo $tab8_background_image_attachment; ?> !important;background-repeat:<?php echo $tab8_background_image_repeat; ?> !important;background-position:<?php echo $tab8_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab8' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab9", $tab_show_positions) && JModuleHelper::getModules('s5_tab9')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab9_background == "yes") { ?> style="<?php if ($tab9_background_image == "") {?>background:<?php echo $tab9_background_color; ?> !important;<?php } ?><?php if ($tab9_background_image != "") {if ($tab9_background_image_size == "custom") {$tab9_background_image_size = $tab9_background_image_size_custom; }?>background-color:<?php echo $tab9_background_color; ?> !important;background-image:url(<?php echo $tab9_background_image; ?>) !important;background-size: <?php echo $tab9_background_image_size; ?>;background-attachment: <?php echo $tab9_background_image_attachment; ?> !important;background-repeat:<?php echo $tab9_background_image_repeat; ?> !important;background-position:<?php echo $tab9_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab9' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab10", $tab_show_positions) && JModuleHelper::getModules('s5_tab10')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab10_background == "yes") { ?> style="<?php if ($tab10_background_image == "") {?>background:<?php echo $tab10_background_color; ?> !important;<?php } ?><?php if ($tab10_background_image != "") {if ($tab10_background_image_size == "custom") {$tab10_background_image_size = $tab10_background_image_size_custom; }?>background-color:<?php echo $tab10_background_color; ?> !important;background-image:url(<?php echo $tab10_background_image; ?>) !important;background-size: <?php echo $tab10_background_image_size; ?>;background-attachment: <?php echo $tab10_background_image_attachment; ?> !important;background-repeat:<?php echo $tab10_background_image_repeat; ?> !important;background-position:<?php echo $tab10_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab10' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab11", $tab_show_positions) && JModuleHelper::getModules('s5_tab11')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab11_background == "yes") { ?> style="<?php if ($tab11_background_image == "") {?>background:<?php echo $tab11_background_color; ?> !important;<?php } ?><?php if ($tab11_background_image != "") {if ($tab11_background_image_size == "custom") {$tab11_background_image_size = $tab11_background_image_size_custom; }?>background-color:<?php echo $tab11_background_color; ?> !important;background-image:url(<?php echo $tab11_background_image; ?>) !important;background-size: <?php echo $tab11_background_image_size; ?>;background-attachment: <?php echo $tab11_background_image_attachment; ?> !important;background-repeat:<?php echo $tab11_background_image_repeat; ?> !important;background-position:<?php echo $tab11_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab11' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab12", $tab_show_positions) && JModuleHelper::getModules('s5_tab12')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab12_background == "yes") { ?> style="<?php if ($tab12_background_image == "") {?>background:<?php echo $tab12_background_color; ?> !important;<?php } ?><?php if ($tab12_background_image != "") {if ($tab12_background_image_size == "custom") {$tab12_background_image_size = $tab12_background_image_size_custom; }?>background-color:<?php echo $tab12_background_color; ?> !important;background-image:url(<?php echo $tab12_background_image; ?>) !important;background-size: <?php echo $tab12_background_image_size; ?>;background-attachment: <?php echo $tab12_background_image_attachment; ?> !important;background-repeat:<?php echo $tab12_background_image_repeat; ?> !important;background-position:<?php echo $tab12_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab12' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab13", $tab_show_positions) && JModuleHelper::getModules('s5_tab13')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab13_background == "yes") { ?> style="<?php if ($tab13_background_image == "") {?>background:<?php echo $tab13_background_color; ?> !important;<?php } ?><?php if ($tab13_background_image != "") {if ($tab13_background_image_size == "custom") {$tab13_background_image_size = $tab13_background_image_size_custom; }?>background-color:<?php echo $tab13_background_color; ?> !important;background-image:url(<?php echo $tab13_background_image; ?>) !important;background-size: <?php echo $tab13_background_image_size; ?>;background-attachment: <?php echo $tab13_background_image_attachment; ?> !important;background-repeat:<?php echo $tab13_background_image_repeat; ?> !important;background-position:<?php echo $tab13_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab13' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab14", $tab_show_positions) && JModuleHelper::getModules('s5_tab14')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab14_background == "yes") { ?> style="<?php if ($tab14_background_image == "") {?>background:<?php echo $tab14_background_color; ?> !important;<?php } ?><?php if ($tab14_background_image != "") {if ($tab14_background_image_size == "custom") {$tab14_background_image_size = $tab14_background_image_size_custom; }?>background-color:<?php echo $tab14_background_color; ?> !important;background-image:url(<?php echo $tab14_background_image; ?>) !important;background-size: <?php echo $tab14_background_image_size; ?>;background-attachment: <?php echo $tab14_background_image_attachment; ?> !important;background-repeat:<?php echo $tab14_background_image_repeat; ?> !important;background-position:<?php echo $tab14_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab14' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab15", $tab_show_positions) && JModuleHelper::getModules('s5_tab15')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab15_background == "yes") { ?> style="<?php if ($tab15_background_image == "") {?>background:<?php echo $tab15_background_color; ?> !important;<?php } ?><?php if ($tab15_background_image != "") {if ($tab15_background_image_size == "custom") {$tab15_background_image_size = $tab15_background_image_size_custom; }?>background-color:<?php echo $tab15_background_color; ?> !important;background-image:url(<?php echo $tab15_background_image; ?>) !important;background-size: <?php echo $tab15_background_image_size; ?>;background-attachment: <?php echo $tab15_background_image_attachment; ?> !important;background-repeat:<?php echo $tab15_background_image_repeat; ?> !important;background-position:<?php echo $tab15_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab15' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab16", $tab_show_positions) && JModuleHelper::getModules('s5_tab16')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab16_background == "yes") { ?> style="<?php if ($tab16_background_image == "") {?>background:<?php echo $tab16_background_color; ?> !important;<?php } ?><?php if ($tab16_background_image != "") {if ($tab16_background_image_size == "custom") {$tab16_background_image_size = $tab16_background_image_size_custom; }?>background-color:<?php echo $tab16_background_color; ?> !important;background-image:url(<?php echo $tab16_background_image; ?>) !important;background-size: <?php echo $tab16_background_image_size; ?>;background-attachment: <?php echo $tab16_background_image_attachment; ?> !important;background-repeat:<?php echo $tab16_background_image_repeat; ?> !important;background-position:<?php echo $tab16_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab16' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab17", $tab_show_positions) && JModuleHelper::getModules('s5_tab17')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab17_background == "yes") { ?> style="<?php if ($tab17_background_image == "") {?>background:<?php echo $tab17_background_color; ?> !important;<?php } ?><?php if ($tab17_background_image != "") {if ($tab17_background_image_size == "custom") {$tab17_background_image_size = $tab17_background_image_size_custom; }?>background-color:<?php echo $tab17_background_color; ?> !important;background-image:url(<?php echo $tab17_background_image; ?>) !important;background-size: <?php echo $tab17_background_image_size; ?>;background-attachment: <?php echo $tab17_background_image_attachment; ?> !important;background-repeat:<?php echo $tab17_background_image_repeat; ?> !important;background-position:<?php echo $tab17_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab17' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab18", $tab_show_positions) && JModuleHelper::getModules('s5_tab18')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab18_background == "yes") { ?> style="<?php if ($tab18_background_image == "") {?>background:<?php echo $tab18_background_color; ?> !important;<?php } ?><?php if ($tab18_background_image != "") {if ($tab18_background_image_size == "custom") {$tab18_background_image_size = $tab18_background_image_size_custom; }?>background-color:<?php echo $tab18_background_color; ?> !important;background-image:url(<?php echo $tab18_background_image; ?>) !important;background-size: <?php echo $tab18_background_image_size; ?>;background-attachment: <?php echo $tab18_background_image_attachment; ?> !important;background-repeat:<?php echo $tab18_background_image_repeat; ?> !important;background-position:<?php echo $tab18_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab18' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab19", $tab_show_positions) && JModuleHelper::getModules('s5_tab19')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab19_background == "yes") { ?> style="<?php if ($tab19_background_image == "") {?>background:<?php echo $tab19_background_color; ?> !important;<?php } ?><?php if ($tab19_background_image != "") {if ($tab19_background_image_size == "custom") {$tab19_background_image_size = $tab19_background_image_size_custom; }?>background-color:<?php echo $tab19_background_color; ?> !important;background-image:url(<?php echo $tab19_background_image; ?>) !important;background-size: <?php echo $tab19_background_image_size; ?>;background-attachment: <?php echo $tab19_background_image_attachment; ?> !important;background-repeat:<?php echo $tab19_background_image_repeat; ?> !important;background-position:<?php echo $tab19_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab19' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab20", $tab_show_positions) && JModuleHelper::getModules('s5_tab20')) { ?>
						<div class="s5_tab_show_slide s5_tab_show_slide_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_slides_number; $s5_tab_show_slides_number = $s5_tab_show_slides_number +1; ?>_id<?php echo $s5_tab_show_random_id; ?>"<?php if ($tab20_background == "yes") { ?> style="<?php if ($tab20_background_image == "") {?>background:<?php echo $tab20_background_color; ?> !important;<?php } ?><?php if ($tab20_background_image != "") {if ($tab20_background_image_size == "custom") {$tab20_background_image_size = $tab20_background_image_size_custom; }?>background-color:<?php echo $tab20_background_color; ?> !important;background-image:url(<?php echo $tab20_background_image; ?>) !important;background-size: <?php echo $tab20_background_image_size; ?>;background-attachment: <?php echo $tab20_background_image_attachment; ?> !important;background-repeat:<?php echo $tab20_background_image_repeat; ?> !important;background-position:<?php echo $tab20_background_image_position; ?>;<?php } ?>"<?php } ?>>
							<div class="s5_tab_show_slide_inner">
								<?php $myblurb_modules = &JModuleHelper::getModules( 's5_tab20' ); foreach ($myblurb_modules as $myblurb) { $_options = array( 'style' => 'xhtml' ); echo JModuleHelper::renderModule( $myblurb, $_options ); } ?>
							</div>
						</div>
					<?php } ?>
					<div style="clear:both;height:0px"></div>
				</div>
				</div>
			</div>	
			<?php if ($arrow_location == "bottom") { ?>	
				<div class="s5_tab_show_next_prev_wrapper  s5_tab_show_next_prev_bottom">
					<div <?php echo $mouse_effect_arrows; ?>="s5_tab_show_trigger_prev(this.id)" class="s5_tab_show_next_prev s5_tab_show_prev" id="s5_tab_show_prev_id<?php echo $s5_tab_show_random_id; ?>"></div>
					<div <?php echo $mouse_effect_arrows; ?>="s5_tab_show_trigger_next(this.id)" class="s5_tab_show_next_prev s5_tab_show_next" id="s5_tab_show_next_id<?php echo $s5_tab_show_random_id; ?>"></div>
					<div class="s5_tab_show_clear"></div>
				</div>
			<?php } ?>			
			<?php if ($button_location == "bottom") { ?>	
				<div class="s5_tab_show_slides_buttons s5_tab_show_slides_buttons_bottom" id="s5_tab_show_slides_buttons_id<?php echo $s5_tab_show_random_id; ?>">
					<?php if (in_array("s5_tab1", $tab_show_positions) && JModuleHelper::getModules('s5_tab1')) { ?>
						<div <?php if ($tab1_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab1_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab1_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab1_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab1_text; ?></div>
							<?php } ?>
							<?php if ($tab1_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab2", $tab_show_positions) && JModuleHelper::getModules('s5_tab2')) { ?>
						<div <?php if ($tab2_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab2_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab2_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab2_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab2_text; ?></div>
							<?php } ?>
							<?php if ($tab2_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab3", $tab_show_positions) && JModuleHelper::getModules('s5_tab3')) { ?>
						<div <?php if ($tab3_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab3_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab3_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab3_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab3_text; ?></div>
							<?php } ?>
							<?php if ($tab3_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab4", $tab_show_positions) && JModuleHelper::getModules('s5_tab4')) { ?>
						<div <?php if ($tab4_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab4_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab4_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab4_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab4_text; ?></div>
							<?php } ?>
							<?php if ($tab4_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab5", $tab_show_positions) && JModuleHelper::getModules('s5_tab5')) { ?>
						<div <?php if ($tab5_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab5_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab5_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab5_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab5_text; ?></div>
							<?php } ?>
							<?php if ($tab5_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab6", $tab_show_positions) && JModuleHelper::getModules('s5_tab6')) { ?>
						<div <?php if ($tab6_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab6_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab6_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab6_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab6_text; ?></div>
							<?php } ?>
							<?php if ($tab6_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab7", $tab_show_positions) && JModuleHelper::getModules('s5_tab7')) { ?>
						<div <?php if ($tab7_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab7_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab7_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab7_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab7_text; ?></div>
							<?php } ?>
							<?php if ($tab7_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab8", $tab_show_positions) && JModuleHelper::getModules('s5_tab8')) { ?>
						<div <?php if ($tab8_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab8_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab8_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab8_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab8_text; ?></div>
							<?php } ?>
							<?php if ($tab8_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab9", $tab_show_positions) && JModuleHelper::getModules('s5_tab9')) { ?>
						<div <?php if ($tab9_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab9_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab9_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab9_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab9_text; ?></div>
							<?php } ?>
							<?php if ($tab9_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab10", $tab_show_positions) && JModuleHelper::getModules('s5_tab10')) { ?>
						<div <?php if ($tab10_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab10_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab10_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab10_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab10_text; ?></div>
							<?php } ?>
							<?php if ($tab10_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab11", $tab_show_positions) && JModuleHelper::getModules('s5_tab11')) { ?>
						<div <?php if ($tab11_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab11_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab11_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab11_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab11_text; ?></div>
							<?php } ?>
							<?php if ($tab11_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab12", $tab_show_positions) && JModuleHelper::getModules('s5_tab12')) { ?>
						<div <?php if ($tab12_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab12_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab12_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab12_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab12_text; ?></div>
							<?php } ?>
							<?php if ($tab12_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab13", $tab_show_positions) && JModuleHelper::getModules('s5_tab13')) { ?>
						<div <?php if ($tab13_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab13_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab13_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab13_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab13_text; ?></div>
							<?php } ?>
							<?php if ($tab13_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab14", $tab_show_positions) && JModuleHelper::getModules('s5_tab14')) { ?>
						<div <?php if ($tab14_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab14_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab14_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab14_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab14_text; ?></div>
							<?php } ?>
							<?php if ($tab14_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab15", $tab_show_positions) && JModuleHelper::getModules('s5_tab15')) { ?>
						<div <?php if ($tab15_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab15_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab15_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab15_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab15_text; ?></div>
							<?php } ?>
							<?php if ($tab15_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab16", $tab_show_positions) && JModuleHelper::getModules('s5_tab16')) { ?>
						<div <?php if ($tab16_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab16_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab16_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab16_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab16_text; ?></div>
							<?php } ?>
							<?php if ($tab16_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab17", $tab_show_positions) && JModuleHelper::getModules('s5_tab17')) { ?>
						<div <?php if ($tab17_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab17_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab17_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab17_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab17_text; ?></div>
							<?php } ?>
							<?php if ($tab17_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab18", $tab_show_positions) && JModuleHelper::getModules('s5_tab18')) { ?>
						<div <?php if ($tab18_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab18_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab18_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab18_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab18_text; ?></div>
							<?php } ?>
							<?php if ($tab18_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab19", $tab_show_positions) && JModuleHelper::getModules('s5_tab19')) { ?>
						<div <?php if ($tab19_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab19_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab19_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab19_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab19_text; ?></div>
							<?php } ?>
							<?php if ($tab19_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (in_array("s5_tab20", $tab_show_positions) && JModuleHelper::getModules('s5_tab20')) { ?>
						<div <?php if ($tab20_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
							<?php if ($tab20_img != "") { ?>
								<div class="s5_tab_show_button_img_present">
								<div class="s5_tab_show_button_img"><img src="<?php echo $tab20_img; ?>" alt="" /></div>
							<?php } ?>
							<?php if ($tab20_text != "") { ?>
								<div class="s5_tab_show_button_text"><?php echo $tab20_text; ?></div>
							<?php } ?>
							<?php if ($tab20_img != "") { ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<div class="s5_tab_show_clear"></div>
				</div>
			<?php } ?>
			<?php if ($posttext_inside != "") { ?>
				<div class="s5_tab_show_posttext_inside"><?php echo $posttext_inside; ?></div>
			<?php } ?>
			<div id="s5_tab_show_duration_id<?php echo $s5_tab_show_random_id; ?>" style="display:none"><?php echo $duration; ?></div>
		</div>
		<?php if ($arrow_location == "bottom_outside") { ?>	
			<div class="s5_tab_show_next_prev_wrapper  s5_tab_show_next_prev_bottom">
				<div <?php echo $mouse_effect_arrows; ?>="s5_tab_show_trigger_prev(this.id)" class="s5_tab_show_next_prev s5_tab_show_prev" id="s5_tab_show_prev_id<?php echo $s5_tab_show_random_id; ?>"></div>
				<div <?php echo $mouse_effect_arrows; ?>="s5_tab_show_trigger_next(this.id)" class="s5_tab_show_next_prev s5_tab_show_next" id="s5_tab_show_next_id<?php echo $s5_tab_show_random_id; ?>"></div>
				<div class="s5_tab_show_clear"></div>
			</div>
		<?php } ?>	
		<?php if ($button_location == "bottom_outside") { ?>	
			<div class="s5_tab_show_slides_buttons s5_tab_show_slides_buttons_bottom" id="s5_tab_show_slides_buttons_id<?php echo $s5_tab_show_random_id; ?>">
				<?php if (in_array("s5_tab1", $tab_show_positions) && JModuleHelper::getModules('s5_tab1')) { ?>
					<div <?php if ($tab1_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab1_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab1_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab1_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab1_text; ?></div>
						<?php } ?>
						<?php if ($tab1_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab2", $tab_show_positions) && JModuleHelper::getModules('s5_tab2')) { ?>
					<div <?php if ($tab2_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab2_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab2_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab2_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab2_text; ?></div>
						<?php } ?>
						<?php if ($tab2_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab3", $tab_show_positions) && JModuleHelper::getModules('s5_tab3')) { ?>
					<div <?php if ($tab3_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab3_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab3_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab3_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab3_text; ?></div>
						<?php } ?>
						<?php if ($tab3_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab4", $tab_show_positions) && JModuleHelper::getModules('s5_tab4')) { ?>
					<div <?php if ($tab4_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab4_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab4_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab4_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab4_text; ?></div>
						<?php } ?>
						<?php if ($tab4_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab5", $tab_show_positions) && JModuleHelper::getModules('s5_tab5')) { ?>
					<div <?php if ($tab5_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab5_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab5_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab5_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab5_text; ?></div>
						<?php } ?>
						<?php if ($tab5_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab6", $tab_show_positions) && JModuleHelper::getModules('s5_tab6')) { ?>
					<div <?php if ($tab6_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab6_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab6_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab6_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab6_text; ?></div>
						<?php } ?>
						<?php if ($tab6_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab7", $tab_show_positions) && JModuleHelper::getModules('s5_tab7')) { ?>
					<div <?php if ($tab7_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab7_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab7_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab7_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab7_text; ?></div>
						<?php } ?>
						<?php if ($tab7_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab8", $tab_show_positions) && JModuleHelper::getModules('s5_tab8')) { ?>
					<div <?php if ($tab8_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab8_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab8_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab8_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab8_text; ?></div>
						<?php } ?>
						<?php if ($tab8_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab9", $tab_show_positions) && JModuleHelper::getModules('s5_tab9')) { ?>
					<div <?php if ($tab9_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab9_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab9_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab9_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab9_text; ?></div>
						<?php } ?>
						<?php if ($tab9_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab10", $tab_show_positions) && JModuleHelper::getModules('s5_tab10')) { ?>
					<div <?php if ($tab10_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab10_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab10_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab10_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab10_text; ?></div>
						<?php } ?>
						<?php if ($tab10_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab11", $tab_show_positions) && JModuleHelper::getModules('s5_tab11')) { ?>
					<div <?php if ($tab11_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab11_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab11_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab11_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab11_text; ?></div>
						<?php } ?>
						<?php if ($tab11_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab12", $tab_show_positions) && JModuleHelper::getModules('s5_tab12')) { ?>
					<div <?php if ($tab12_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab12_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab12_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab12_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab12_text; ?></div>
						<?php } ?>
						<?php if ($tab12_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab13", $tab_show_positions) && JModuleHelper::getModules('s5_tab13')) { ?>
					<div <?php if ($tab13_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab13_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab13_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab13_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab13_text; ?></div>
						<?php } ?>
						<?php if ($tab13_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab14", $tab_show_positions) && JModuleHelper::getModules('s5_tab14')) { ?>
					<div <?php if ($tab14_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab14_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab14_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab14_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab14_text; ?></div>
						<?php } ?>
						<?php if ($tab14_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab15", $tab_show_positions) && JModuleHelper::getModules('s5_tab15')) { ?>
					<div <?php if ($tab15_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab15_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab15_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab15_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab15_text; ?></div>
						<?php } ?>
						<?php if ($tab15_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab16", $tab_show_positions) && JModuleHelper::getModules('s5_tab16')) { ?>
					<div <?php if ($tab16_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab16_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab16_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab16_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab16_text; ?></div>
						<?php } ?>
						<?php if ($tab16_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab17", $tab_show_positions) && JModuleHelper::getModules('s5_tab17')) { ?>
					<div <?php if ($tab17_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab17_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab17_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab17_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab17_text; ?></div>
						<?php } ?>
						<?php if ($tab17_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab18", $tab_show_positions) && JModuleHelper::getModules('s5_tab18')) { ?>
					<div <?php if ($tab18_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab18_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab18_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab18_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab18_text; ?></div>
						<?php } ?>
						<?php if ($tab18_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab19", $tab_show_positions) && JModuleHelper::getModules('s5_tab19')) { ?>
					<div <?php if ($tab19_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab19_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab19_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab19_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab19_text; ?></div>
						<?php } ?>
						<?php if ($tab19_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if (in_array("s5_tab20", $tab_show_positions) && JModuleHelper::getModules('s5_tab20')) { ?>
					<div <?php if ($tab20_img != "") { ?>style="padding:0px !important;" <?php } ?><?php echo $mouse_effect_tabs; ?>="s5_tab_show_trigger_slide_transition(this.id)" class="s5_tab_show_slide_button_inactive" id="s5_tab_show_slide<?php echo $s5_tab_show_buttons_number; $s5_tab_show_buttons_number = $s5_tab_show_buttons_number +1; ?>_button_id<?php echo $s5_tab_show_random_id; ?>">
						<?php if ($tab20_img != "") { ?>
							<div class="s5_tab_show_button_img_present">
							<div class="s5_tab_show_button_img"><img src="<?php echo $tab20_img; ?>" alt="" /></div>
						<?php } ?>
						<?php if ($tab20_text != "") { ?>
							<div class="s5_tab_show_button_text"><?php echo $tab20_text; ?></div>
						<?php } ?>
						<?php if ($tab20_img != "") { ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				<div class="s5_tab_show_clear"></div>
			</div>
		<?php } ?>
	</div>
</div>
<?php if ($posttext_outside != "") { ?>
	<div class="s5_tab_show_posttext_outside"><?php echo $posttext_outside; ?></div>
<?php } ?>
<div class="s5_tab_show_clear"></div>
<script type="application/javascript">
<?php
$version = new JVersion();
if($version->RELEASE >= '3.0') { ?>
jQuery(document).ready( function() {
s5_tab_show_start();
});
<?php } else { ?>
window.addEvent('domready', function() {
s5_tab_show_start();
});
<?php } ?>
</script>