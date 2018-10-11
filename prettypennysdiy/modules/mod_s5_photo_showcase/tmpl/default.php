<?php 
$version=new JVersion;
$document = &JFactory::getDocument();
$document->addCustomTag('<link rel="stylesheet" href="'.$LiveSite.'modules/mod_s5_photo_showcase/s5_photo_showcase/s5_photo_showcase.css" type="text/css" />');
if($version->RELEASE <= '2.5') { ?>
<script type="text/javascript">//<![CDATA[
if (!window.jQuery) {
document.write('<script src="<?php echo $LiveSite; ?>modules/mod_s5_photo_showcase/s5_photo_showcase/jquery.min.js"><\/script>');
document.write('<script src="<?php echo $LiveSite; ?>modules/mod_s5_photo_showcase/s5_photo_showcase/jquery.no.conflict.js"><\/script>');
}
if(jQuery.easing.easeOutExpo==undefined){
document.write('<script src="<?php echo $LiveSite; ?>modules/mod_s5_photo_showcase/s5_photo_showcase/jquery-ui.min.js"><\/script>');
}
//]]></script>
<?php }
if($version->RELEASE>='3.0'){
JHtml::_('jquery.framework');
$template_vertex = "no";
$app = JFactory::getApplication();
$template = $app->getTemplate();
$template_json_location = $_SERVER['DOCUMENT_ROOT'].JURI::root(true).'/templates/'.$template.'/vertex.json';
if(file_exists($template_json_location)) { 
$template_vertex = "yes";
} ?>
	<?php if($template_vertex == "no"){ ?>
		<script type="text/javascript">//<![CDATA[
		if(jQuery.easing.easeOutExpo==undefined){
		document.write('<script src="<?php echo $LiveSite?>modules/mod_s5_photo_showcase/s5_photo_showcase/jquery-ui.min.js"><\/script>');
		}
		//]]></script>
	<?php } 
}
?>	
<script type="text/javascript" src="<?php echo $LiveSite?>/modules/mod_s5_photo_showcase/s5_photo_showcase/s5_photo_showcase.js"></script>	
<script type="text/javascript">jQuery.noConflict();</script>
<script language="javascript" type="text/javascript" >

function s5_photo_showcase_function() { 
	var s5_photoshowcase_Carousel  = new s5_photoshowcase_iCarousel("#s5_photo_showcase_button_content", {
		item: {
			klass: "s5_photo_showcase_item"},
		animation: {
			type: "scroll",
			duration: 800,
			amount: 1,
			rotate: {
                type: '<?php echo $display_time > 0 ? "auto" : "manual"?>',
                interval: <?php echo $display_time ? $display_time * 1000  : 5000;?>,
                onMouseOver: "stop"
            }
			 },
			 display_time: <?php echo $display_time; ?>,
	});
	<?php if ($picture1 != "") { ?>
	jQuery("#s5_photo_showcase_button_1").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(0)});
	<?php } ?>
	<?php if ($picture2 != "") { ?>
	jQuery("#s5_photo_showcase_button_2").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(1)});
	<?php } ?>
	<?php if ($picture3 != "") { ?>
	jQuery("#s5_photo_showcase_button_3").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(2)});
	<?php } ?>
	<?php if ($picture4 != "") { ?>
	jQuery("#s5_photo_showcase_button_4").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(3)});
	<?php } ?>
	<?php if ($picture5 != "") { ?>
	jQuery("#s5_photo_showcase_button_5").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(4)});
	<?php } ?>
	<?php if ($picture6 != "") { ?>
	jQuery("#s5_photo_showcase_button_6").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(5)});
	<?php } ?>
	<?php if ($picture7 != "") { ?>
	jQuery("#s5_photo_showcase_button_7").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(6)});
	<?php } ?>
	<?php if ($picture8 != "") { ?>
	jQuery("#s5_photo_showcase_button_8").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(7)});
	<?php } ?>
	<?php if ($picture9 != "") { ?>
	jQuery("#s5_photo_showcase_button_9").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(8)});
	<?php } ?>
	<?php if ($picture10 != "") { ?>
	jQuery("#s5_photo_showcase_button_10").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(9)});
	<?php } ?>
	<?php if ($picture11 != "") { ?>
	jQuery("#s5_photo_showcase_button_11").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(10)});
	<?php } ?>
	<?php if ($picture12 != "") { ?>
	jQuery("#s5_photo_showcase_button_12").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(11)});
	<?php } ?>
	<?php if ($picture13 != "") { ?>
	jQuery("#s5_photo_showcase_button_13").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(12)});
	<?php } ?>
	<?php if ($picture14 != "") { ?>
	jQuery("#s5_photo_showcase_button_14").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(13)});
	<?php } ?>
	<?php if ($picture15 != "") { ?>
	jQuery("#s5_photo_showcase_button_15").bind("click", function(event){event.preventDefault();s5_photoshowcase_Carousel.goTo(14)});
	<?php } ?>
	jQuery('#s5_photo_showcase_prev').click(function(e){
		s5_photoshowcase_Carousel._previous();
		s5_photoshowcase_Carousel.timer = $clear(s5_photoshowcase_Carousel.timer);
		s5_photoshowcase_Carousel.timer = s5_photoshowcase_Carousel._autoRotate.periodical(s5_photoshowcase_Carousel.options.animation.rotate.interval, s5_photoshowcase_Carousel) ;
	});
	jQuery('#s5_photo_showcase_next').click(function(e){
		s5_photoshowcase_Carousel._next();
		s5_photoshowcase_Carousel.timer = $clear(s5_photoshowcase_Carousel.timer);
		s5_photoshowcase_Carousel.timer = s5_photoshowcase_Carousel._autoRotate.periodical(s5_photoshowcase_Carousel.options.animation.rotate.interval, s5_photoshowcase_Carousel) ;
	});
	jQuery('#s5_photo_showcase_outer').bind("mouseenter", function () {
		s5_photoshowcase_Carousel.isMouseOver = true;
		if (s5_photoshowcase_Carousel.options.animation.rotate.type == "auto") {
			s5_photoshowcase_Carousel.timer = $clear(s5_photoshowcase_Carousel.timer);
		}
	});
	jQuery('#s5_photo_showcase_outer').bind(  "mouseleave",function () {
		s5_photoshowcase_Carousel.isMouseOver = false;
		if (s5_photoshowcase_Carousel.options.animation.rotate.type == "auto") {
			s5_photoshowcase_Carousel.timer = $clear(s5_photoshowcase_Carousel.timer);
			s5_photoshowcase_Carousel.timer = s5_photoshowcase_Carousel._autoRotate.periodical(s5_photoshowcase_Carousel.options.animation.rotate.interval, s5_photoshowcase_Carousel)
		}
	});
	
	jQuery('#s5_photo_showcase_prev, #s5_photo_showcase_next').each(function(i,d){
		jQuery(d).bind('mouseover',function () {
			s5_photoshowcase_Carousel.isMouseOver = true;
			if (s5_photoshowcase_Carousel.options.animation.rotate.type == "auto") {
				s5_photoshowcase_Carousel.timer = $clear(s5_photoshowcase_Carousel.timer);
			}
		}).bind("mouseleave",function () {
		s5_photoshowcase_Carousel.isMouseOver = false;
		if (s5_photoshowcase_Carousel.options.animation.rotate.type == "auto") {
			s5_photoshowcase_Carousel.timer = $clear(s5_photoshowcase_Carousel.timer);
			s5_photoshowcase_Carousel.timer = s5_photoshowcase_Carousel._autoRotate.periodical(s5_photoshowcase_Carousel.options.animation.rotate.interval, s5_photoshowcase_Carousel)
		}
	});
	});
	
	var s5_photo_showcase_body_width = document.body.offsetWidth;
	
	function s5_photo_showcase_resize_advance() {
		if (s5_photo_showcase_body_width != document.body.offsetWidth) {
			s5_photoshowcase_Carousel._next();
			s5_photo_showcase_body_width = document.body.offsetWidth;
		}
	}
	
	jQuery(window).resize(s5_photo_showcase_resize_advance);
	
	}
	
	function s5_photo_showcase_load() {
	s5_photo_showcase_function();
	}
	jQuery(document).ready(function(){s5_photo_showcase_load();});
</script>

<div style="display:none">
<img id="s5_photo_showcase_next_hover" alt="" src="<?php echo $LiveSite; ?>modules/mod_s5_photo_showcase/s5_photo_showcase/s5_ps_next_hover.png" />
<img id="s5_photo_showcase_prev_hover" alt="" src="<?php echo $LiveSite; ?>modules/mod_s5_photo_showcase/s5_photo_showcase/s5_ps_prev_hover.png" />
</div>

<div id="s5_photo_showcase_height_width" style="width:<?php echo $width; ?>%;max-width:<?php echo $max_width; ?>px;">
<img id="s5_photo_showcase_height_width_img" alt="" src="<?php echo $picture1; ?>" />
</div>

<div id="s5_photo_showcase_button_frame" style="display:none">  
	<?php if ($picture1 != "") { ?>
	<span id="s5_photo_showcase_button_1"></span>  
	<?php } ?>
	<?php if ($picture2 != "") { ?>
	<span id="s5_photo_showcase_button_2"></span>  
	<?php } ?>
	<?php if ($picture3 != "") { ?>
	<span id="s5_photo_showcase_button_3"></span>  
	<?php } ?>
	<?php if ($picture4 != "") { ?>
	<span id="s5_photo_showcase_button_4"></span>  
	<?php } ?>
	<?php if ($picture5 != "") { ?>
	<span id="s5_photo_showcase_button_5"></span>  
	<?php } ?>
	<?php if ($picture6 != "") { ?>
	<span id="s5_photo_showcase_button_6"></span>  
	<?php } ?>
	<?php if ($picture7 != "") { ?>
	<span id="s5_photo_showcase_button_7"></span>  
	<?php } ?>
	<?php if ($picture8 != "") { ?>
	<span id="s5_photo_showcase_button_8"></span>  
	<?php } ?>
	<?php if ($picture9 != "") { ?>
	<span id="s5_photo_showcase_button_9"></span>  
	<?php } ?>
	<?php if ($picture10 != "") { ?>
	<span id="s5_photo_showcase_button_10"></span>  
	<?php } ?>
	<?php if ($picture11 != "") { ?>
	<span id="s5_photo_showcase_button_11"></span>  
	<?php } ?>
	<?php if ($picture12 != "") { ?>
	<span id="s5_photo_showcase_button_12"></span>  
	<?php } ?>
	<?php if ($picture13 != "") { ?>
	<span id="s5_photo_showcase_button_13"></span>  
	<?php } ?>
	<?php if ($picture14 != "") { ?>
	<span id="s5_photo_showcase_button_14"></span>  
	<?php } ?>
	<?php if ($picture15 != "") { ?>
	<span id="s5_photo_showcase_button_15"></span>  
	<?php } ?>
	<div style="clear:both;height:0px"></div>
</div>  
<div style="clear:both;height:0px"></div>

<div id="s5_photo_showcase_outer" class="s5_photo_showcase_outer_loading" style="visibility:hidden;<?php if ($background != "") { ?>background:#<?php echo $background; ?>;<?php } ?>">
	<div id="s5_photo_showcase_inner">

		<div id="s5_photo_showcase_prev_line" style="background:#<?php echo $bars_background; ?>;"></div>
		<div id="s5_photo_showcase_prev"></div>

		<div id="s5_photo_showcase_next_line" style="background:#<?php echo $bars_background; ?>;"></div>
		<div id="s5_photo_showcase_next"></div>

		<div style="clear:both;height:0px"></div>

		<ul id="s5_photo_showcase_button_content">  
			<?php if ($picture1 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_1" style="background:url('<?php echo $picture1; ?>');">
				<?php if ($picture1_text != "" || $picture1_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture1_text != "") { echo $picture1_text; } ?>
							<?php if ($content_type == "text" && ($picture1_text != "" || $picture1_title != "")) { ?>
								<?php if ($picture1_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture1_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture1_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture1_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture2 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_2" style="background:url('<?php echo $picture2; ?>');">
				<?php if ($picture2_text != "" || $picture2_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture2_text != "") { echo $picture2_text; } ?>
							<?php if ($content_type == "text" && ($picture2_text != "" || $picture2_title != "")) { ?>
								<?php if ($picture2_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture2_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture2_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture2_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture3 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_3" style="background:url('<?php echo $picture3; ?>');">
				<?php if ($picture3_text != "" || $picture3_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture3_text != "") { echo $picture3_text; } ?>
							<?php if ($content_type == "text" && ($picture3_text != "" || $picture3_title != "")) { ?>
								<?php if ($picture3_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture3_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture3_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture3_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture4 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_4" style="background:url('<?php echo $picture4; ?>');">
				<?php if ($picture4_text != "" || $picture4_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture4_text != "") { echo $picture4_text; } ?>
							<?php if ($content_type == "text" && ($picture4_text != "" || $picture4_title != "")) { ?>
								<?php if ($picture4_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture4_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture4_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture4_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture5 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_5" style="background:url('<?php echo $picture5; ?>');">
				<?php if ($picture5_text != "" || $picture5_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture5_text != "") { echo $picture5_text; } ?>
							<?php if ($content_type == "text" && ($picture5_text != "" || $picture5_title != "")) { ?>
								<?php if ($picture5_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture5_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture5_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture5_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture6 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_6" style="background:url('<?php echo $picture6; ?>');">
				<?php if ($picture6_text != "" || $picture6_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture6_text != "") { echo $picture6_text; } ?>
							<?php if ($content_type == "text" && ($picture6_text != "" || $picture6_title != "")) { ?>
								<?php if ($picture6_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture6_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture6_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture6_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture7 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_7" style="background:url('<?php echo $picture7; ?>');">
				<?php if ($picture7_text != "" || $picture7_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture7_text != "") { echo $picture7_text; } ?>
							<?php if ($content_type == "text" && ($picture7_text != "" || $picture7_title != "")) { ?>
								<?php if ($picture7_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture7_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture7_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture7_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture8 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_8" style="background:url('<?php echo $picture8; ?>');">
				<?php if ($picture8_text != "" || $picture8_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture8_text != "") { echo $picture8_text; } ?>
							<?php if ($content_type == "text" && ($picture8_text != "" || $picture8_title != "")) { ?>
								<?php if ($picture8_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture8_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture8_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture8_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture9 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_9" style="background:url('<?php echo $picture9; ?>');">
				<?php if ($picture9_text != "" || $picture9_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture9_text != "") { echo $picture9_text; } ?>
							<?php if ($content_type == "text" && ($picture9_text != "" || $picture9_title != "")) { ?>
								<?php if ($picture9_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture9_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture9_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture9_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture10 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_10" style="background:url('<?php echo $picture10; ?>');">
				<?php if ($picture10_text != "" || $picture10_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture10_text != "") { echo $picture10_text; } ?>
							<?php if ($content_type == "text" && ($picture10_text != "" || $picture10_title != "")) { ?>
								<?php if ($picture10_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture10_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture10_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture10_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture11 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_11" style="background:url('<?php echo $picture11; ?>');">
				<?php if ($picture11_text != "" || $picture11_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture11_text != "") { echo $picture11_text; } ?>
							<?php if ($content_type == "text" && ($picture11_text != "" || $picture11_title != "")) { ?>
								<?php if ($picture11_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture11_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture11_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture11_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture12 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_12" style="background:url('<?php echo $picture12; ?>');">
				<?php if ($picture12_text != "" || $picture12_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture12_text != "") { echo $picture12_text; } ?>
							<?php if ($content_type == "text" && ($picture12_text != "" || $picture12_title != "")) { ?>
								<?php if ($picture12_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture12_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture12_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture12_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture13 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_13" style="background:url('<?php echo $picture13; ?>');">
				<?php if ($picture13_text != "" || $picture13_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture13_text != "") { echo $picture13_text; } ?>
							<?php if ($content_type == "text" && ($picture13_text != "" || $picture13_title != "")) { ?>
								<?php if ($picture13_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture13_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture13_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture13_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture14 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_14" style="background:url('<?php echo $picture14; ?>');">
				<?php if ($picture14_text != "" || $picture14_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture14_text != "") { echo $picture14_text; } ?>
							<?php if ($content_type == "text" && ($picture14_text != "" || $picture14_title != "")) { ?>
								<?php if ($picture14_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture14_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture14_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture14_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
			<?php if ($picture15 != "") { ?>
			<li class="s5_photo_showcase_item" id="s5_photo_showcase_item_15" style="background:url('<?php echo $picture15; ?>');">
				<?php if ($picture15_text != "" || $picture15_title != "") { ?>
					<div class="s5_photo_showcase_conent_wrap1">
					<div class="s5_photo_showcase_conent_wrap2">
						<div class="s5_photo_showcase_conent_wrap_inner1">
						<div class="s5_photo_showcase_conent_wrap_inner2<?php if ($content_type == "text") {?> <?php echo $text_background; ?><?php } ?>"<?php if ($content_type == "text") {?>  style="padding:<?php echo $text_padding; ?>px;"<?php } ?>>
							<?php if ($content_type == "html" && $picture15_text != "") { echo $picture15_text; } ?>
							<?php if ($content_type == "text" && ($picture15_text != "" || $picture15_title != "")) { ?>
								<?php if ($picture15_title != "") { ?>
									<div class="s5_photo_showcase_title" style="color:#<?php echo $title_color; ?>;font-size:<?php echo $title_size; ?>pt;margin-bottom:<?php echo $title_margin; ?>px;font-weight:<?php echo $title_bold; ?>;">
										<?php echo $picture15_title; ?>
									</div>
								<?php } ?>
								<?php if ($picture15_text != "") { ?>
									<div class="s5_photo_showcase_text" style="color:#<?php echo $text_color; ?>;font-size:<?php echo $text_size; ?>pt;">
										<?php echo $picture15_text; ?>
									</div>
								<?php } ?>
							<?php } ?>
							<div style="clear:both;height:0px"></div>
						</div>
						</div>
					</div>
					</div>
				<?php } ?>
				<div style="clear:both;height:0px"></div>
			</li> 
			<?php } ?>
		</ul>  
		
		<div style="clear:both;height:0px"></div>
	</div>
</div>
