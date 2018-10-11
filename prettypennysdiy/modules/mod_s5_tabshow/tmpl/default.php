<?php

if ($s5_javascript == "mootools") {
$s5_effectmouse = "";
}

if ($s5_javascript == "s5effects") {
$s5_mootoolsmouse = "";
}

$s5_ifvisible = 0;
if ($text1line != "" && $text2line == "" && $text3line == "" && $text4line == "" && $text5line == "" && $text6line == "" && $text7line == "" && $text8line == "" && $text9line == "" && $text10line == "")  {
$s5_ifvisible = 1;
}
if ($text1line != "" && $text2line != "" && $text3line == "" && $text4line == "" && $text5line == "" && $text6line == "" && $text7line == "" && $text8line == "" && $text9line == "" && $text10line == "")  {
$s5_ifvisible = 2;
} 
if ($text1line != "" && $text2line != "" && $text3line != "" && $text4line == "" && $text5line == "" && $text6line == "" && $text7line == "" && $text8line == "" && $text9line == "" && $text10line == "")  {
$s5_ifvisible = 3;
} 
if ($text1line != "" && $text2line != "" && $text3line != "" && $text4line != "" && $text5line == "" && $text6line == "" && $text7line == "" && $text8line == "" && $text9line == "" && $text10line == "")  {
$s5_ifvisible = 4;
} 
if ($text1line != "" && $text2line != "" && $text3line != "" && $text4line != "" && $text5line != "" && $text6line == "" && $text7line == "" && $text8line == "" && $text9line == "" && $text10line == "")  {
$s5_ifvisible = 5;
} 
if ($text1line != "" && $text2line != "" && $text3line != "" && $text4line != "" && $text5line != "" && $text6line != "" && $text7line == "" && $text8line == "" && $text9line == "" && $text10line == "")  {
$s5_ifvisible = 6;
} 
if ($text1line != "" && $text2line != "" && $text3line != "" && $text4line != "" && $text5line != "" && $text6line != "" && $text7line != "" && $text8line == "" && $text9line == "" && $text10line == "")  {
$s5_ifvisible = 7;
} 
if ($text1line != "" && $text2line != "" && $text3line != "" && $text4line != "" && $text5line != "" && $text6line != "" && $text7line != "" && $text8line != "" && $text9line == "" && $text10line == "")  {
$s5_ifvisible = 8;
} 
if ($text1line != "" && $text2line != "" && $text3line != "" && $text4line != "" && $text5line != "" && $text6line != "" && $text7line != "" && $text8line != "" && $text9line != "" && $text10line == "")  {
$s5_ifvisible = 9;
} 
if ($text1line != "" && $text2line != "" && $text3line != "" && $text4line != "" && $text5line != "" && $text6line != "" && $text7line != "" && $text8line != "" && $text9line != "" && $text10line != "")  {
$s5_ifvisible = 10;
} 

echo "<script language=\"javascript\" type=\"text/javascript\" >var s5_ifvisible = ".$s5_ifvisible.";</script>";

?>



<?php
$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser.
$iss_ie6 = "";
$iss_ie7 = "";
if(strrpos($br,"msie 6") > 1) {
$iss_ie6 = "yes";
} 
if(strrpos($br,"msie 7") > 1) {
$iss_ie7 = "yes";
$iss_ie6 = "no";
}
else {
$iss_ie6 = "no";
}

?>

<?php if ($iss_ie6 == "yes") { ?>
<script type="text/javascript">//<![CDATA[
    document.write('<link href="<?php echo $LiveSite;?>modules/mod_s5_tabshow/s5_tabshow/stylesie6.css" rel="stylesheet" type="text/css" media="screen" />');
//]]></script><?php } ?>
<?php if ($iss_ie6 == "no") { ?>
<script type="text/javascript">//<![CDATA[
    document.write('<link href="<?php echo $LiveSite;?>modules/mod_s5_tabshow/s5_tabshow/styles.css" rel="stylesheet" type="text/css" media="screen" />');
//]]></script><?php } ?>


<?php if ($iss_ie6 == "yes") { ?>	
<script language="javascript" type="text/javascript">

startList = function() {
	var sfElss = document.getElementById("s5navfs").getElementsByTagName("LI");
	for (var ii=0; ii<sfElss.length; ii++) {
		sfElss[ii].onmouseover=function() {
			this.className+=" over";
			this.className+=" sfhover";
		}
		sfElss[ii].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" over\\b"), "");
			this.className=this.className.replace(new RegExp(" sfhover\\b"), "");

		}
	}
}
if (window.attachEvent) window.attachEvent("onload", startList);

</script>
<?php } ?>



<?php 
if ($s5_javascript == "mootools") { 
$version=new JVersion;
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
    document.write('<script src="<?php echo $LiveSite?>/modules/mod_s5_tabshow/s5_tabshow/jquery-ui.min.js"><\/script>');
    }
	//]]></script>
<?php } ?>	
<script type="text/javascript" src="<?php echo $LiveSite?>/modules/mod_s5_tabshow/s5_tabshow/iCarousel_jquery.js"></script>	
<script type="text/javascript">jQuery.noConflict();</script>
<script language="javascript" type="text/javascript" >

function s5_tabshowfunctionld() { 
	var s5Carousel = new iCarousel("#s5_button_content", {
		idPrevious: "#s5_button_previous",
		idNext: "",
		idToggle: "",
		item: {
			klass: "s5_button_item",
			size: <?php echo ($width) ?>},
		animation: {
			type: "scroll",
			duration: 1000,
			amount: 1,
			rotate: {
                type: '<?php echo $s5_javascript == 'mootools' && $display_time > 0 ? "auto" : "manual"?>',
                interval: <?php echo $display_time ? $display_time * 1000  : 5000;?>,
                onMouseOver: "stop"
            }
			 },
			 display_time: <?php echo $params->get('s5_display_time',0);?>,
			 s5_javascript: '<?php echo $params->get('s5_javascript',"mootools");?>'
	});
		<?php if ($text1line != "") { ?>
	jQuery("#thumb0").bind("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){event.preventDefault();s5Carousel.goTo(0)});<?php } ?>	 
	<?php if ($text2line != "") { ?>
	jQuery("#thumb1").bind("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){event.preventDefault();s5Carousel.goTo(1)});<?php } ?>	 
	<?php if ($text3line != "") { ?>
	jQuery("#thumb2").bind("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){event.preventDefault();s5Carousel.goTo(2)});<?php } ?>	 
	<?php if ($text4line != "") { ?>
	jQuery("#thumb3").bind("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){event.preventDefault();s5Carousel.goTo(3)});<?php } ?>	 
	<?php if ($text5line != "") { ?>
	jQuery("#thumb4").bind("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){event.preventDefault();s5Carousel.goTo(4)});<?php } ?>	 
	<?php if ($text6line != "") { ?>
	jQuery("#thumb5").bind("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){event.preventDefault();s5Carousel.goTo(5)});<?php } ?>	 
	<?php if ($text7line != "") { ?>
	jQuery("#thumb6").bind("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){event.preventDefault();s5Carousel.goTo(6)});<?php } ?>	 
	<?php if ($text8line != "") { ?>
	jQuery("#thumb7").bind("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){event.preventDefault();s5Carousel.goTo(7)});<?php } ?>	 
	<?php if ($text9line != "") { ?>
	jQuery("#thumb8").bind("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){event.preventDefault();s5Carousel.goTo(8)});<?php } ?>	 
	<?php if ($text10line != "") { ?>
	jQuery("#thumb9").bind("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){event.preventDefault();s5Carousel.goTo(9)});<?php } ?>

		 
	}
function s5_tabshowloadit() {
s5_tabshowfunctionld();}
jQuery(document).ready(function(){s5_tabshowloadit();});
</script>
<?php }else{
JHTML::_('behavior.mootools');
?>



<script type="text/javascript" src="<?php echo $LiveSite?>/modules/mod_s5_tabshow/s5_tabshow/iCarousel.js"></script>
<script language="javascript" type="text/javascript" >
function s5_tabshowfunctionld() { 
	var s5Carousel = new iCarousel("s5_button_content", {
		idPrevious: "s5_button_previous",
		idNext: "",
		idToggle: "",
		item: {
			klass: "s5_button_item",
			size: <?php echo ($width) ?>},
		animation: {
			type: "scroll",
			duration: 700,
			amount: 1,
		
			rotate: {
                type: '<?php echo $s5_javascript == 'mootools' && $display_time > 0 ? "auto" : "manual"?>',
                interval: <?php echo $display_time ? $display_time * 1000  : 5000;?>,
                onMouseOver: "stop"
            } }
	});
	<?php if ($text1line != "") { ?>
	$("thumb0").addEvent("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){new Event(event).stop();s5Carousel.goTo(0)});<?php } ?>	 
	<?php if ($text2line != "") { ?>
	$("thumb1").addEvent("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){new Event(event).stop();s5Carousel.goTo(1)});<?php } ?>	 
	<?php if ($text3line != "") { ?>
	$("thumb2").addEvent("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){new Event(event).stop();s5Carousel.goTo(2)});<?php } ?>	 
	<?php if ($text4line != "") { ?>
	$("thumb3").addEvent("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){new Event(event).stop();s5Carousel.goTo(3)});<?php } ?>	 
	<?php if ($text5line != "") { ?>
	$("thumb4").addEvent("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){new Event(event).stop();s5Carousel.goTo(4)});<?php } ?>	 
	<?php if ($text6line != "") { ?>
	$("thumb5").addEvent("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){new Event(event).stop();s5Carousel.goTo(5)});<?php } ?>	 
	<?php if ($text7line != "") { ?>
	$("thumb6").addEvent("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){new Event(event).stop();s5Carousel.goTo(6)});<?php } ?>	 
	<?php if ($text8line != "") { ?>
	$("thumb7").addEvent("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){new Event(event).stop();s5Carousel.goTo(7)});<?php } ?>	 
	<?php if ($text9line != "") { ?>
	$("thumb8").addEvent("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){new Event(event).stop();s5Carousel.goTo(8)});<?php } ?>	 
	<?php if ($text10line != "") { ?>
	$("thumb9").addEvent("<?php if ($s5_mootoolsmouse == "click") { ?>click<?php } else {?>mouseover<?php }?>", function(event){new Event(event).stop();s5Carousel.goTo(9)});<?php } ?>
}
function s5_tabshowloadit() {
s5_tabshowfunctionld();}
window.addEvent('domready',function(){s5_tabshowfunctionld();});
</script>
<?php }

} ?>	 

<?php if ($s5_javascript == "s5effects") { ?>
<?php if ($s5_effectsani == "snap") { ?>
<script type="text/javascript" src="<?php echo $LiveSite?>/modules/mod_s5_tabshow/s5_tabshow/s5_effects_snap.js"></script>
<?php } ?>	

<?php if ($s5_effectsani == "fade") { ?>
<script type="text/javascript" src="<?php echo $LiveSite?>/modules/mod_s5_tabshow/s5_tabshow/s5_effects_fade.js"></script>
<?php } ?>	
<?php } ?>	 

<div id="s5_button_wrap0" style="width:<?php echo $width ?>px">
<div id="s5_button_wrap1">
<div id="s5_button_wrap2">
	<div id="s5_button_frame">  
       <ul id="s5navfs">  	 
		<?php if ($text1line != "") { ?>
             <li id="thumb0" class="s5_ts_not_active" <?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectmouse == "click") { ?>onclick<?php } else {?>onmouseover<?php }?>="<?php if ($s5_effectsani == "fade") { ?>s5thumb0op();shiftOpacity_ts('s5_button_item1');<?php } ?>s5thumb0();"<?php } ?>><a <?php if ($s5_effectmouse == "click" || $s5_mootoolsmouse == "click") { ?>onclick="s5_active1()"<?php } ?><?php if ($s5_effectmouse == "mouse" || $s5_mootoolsmouse == "mouse") { ?>onmouseover="s5_active1()"<?php } ?>><?php echo $text1line ?></a></li>  
		<?php } ?>  
		<?php if ($text2line != "") { ?>
			<li id="thumb1" class="s5_ts_not_active" <?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectmouse == "click") { ?>onclick<?php } else {?>onmouseover<?php }?>="<?php if ($s5_effectsani == "fade") { ?>s5thumb1op();shiftOpacity_ts('s5_button_item2');<?php } ?>s5thumb1();"<?php } ?>><a <?php if ($s5_effectmouse == "click" || $s5_mootoolsmouse == "click") { ?>onclick="s5_active2()"<?php } ?><?php if ($s5_effectmouse == "mouse" || $s5_mootoolsmouse == "mouse") { ?>onmouseover="s5_active2()"<?php } ?>><?php echo $text2line ?></a></li>  
        <?php } ?>
		<?php if ($text3line != "") { ?>
			<li id="thumb2" class="s5_ts_not_active" <?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectmouse == "click") { ?>onclick<?php } else {?>onmouseover<?php }?>="<?php if ($s5_effectsani == "fade") { ?>s5thumb2op();shiftOpacity_ts('s5_button_item3');<?php } ?>s5thumb2();"<?php } ?>><a <?php if ($s5_effectmouse == "click" || $s5_mootoolsmouse == "click") { ?>onclick="s5_active3()"<?php } ?><?php if ($s5_effectmouse == "mouse" || $s5_mootoolsmouse == "mouse") { ?>onmouseover="s5_active3()"<?php } ?>><?php echo $text3line ?></a></li>  
        <?php } ?>
		<?php if ($text4line != "") { ?> 
			<li id="thumb3" class="s5_ts_not_active" <?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectmouse == "click") { ?>onclick<?php } else {?>onmouseover<?php }?>="<?php if ($s5_effectsani == "fade") { ?>s5thumb3op();shiftOpacity_ts('s5_button_item4');<?php } ?>s5thumb3();"<?php } ?>><a <?php if ($s5_effectmouse == "click" || $s5_mootoolsmouse == "click") { ?>onclick="s5_active4()"<?php } ?><?php if ($s5_effectmouse == "mouse" || $s5_mootoolsmouse == "mouse") { ?>onmouseover="s5_active4()"<?php } ?>><?php echo $text4line ?></a></li>  
        <?php } ?>
		<?php if ($text5line != "") { ?>
			<li id="thumb4" class="s5_ts_not_active" <?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectmouse == "click") { ?>onclick<?php } else {?>onmouseover<?php }?>="<?php if ($s5_effectsani == "fade") { ?>s5thumb4op();shiftOpacity_ts('s5_button_item5');<?php } ?>s5thumb4();"<?php } ?>><a <?php if ($s5_effectmouse == "click" || $s5_mootoolsmouse == "click") { ?>onclick="s5_active5()"<?php } ?><?php if ($s5_effectmouse == "mouse" || $s5_mootoolsmouse == "mouse") { ?>onmouseover="s5_active5()"<?php } ?>><?php echo $text5line ?></a></li>  
        <?php } ?>
		<?php if ($text6line != "") { ?>
			<li id="thumb5" class="s5_ts_not_active" <?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectmouse == "click") { ?>onclick<?php } else {?>onmouseover<?php }?>="<?php if ($s5_effectsani == "fade") { ?>s5thumb5op();shiftOpacity_ts('s5_button_item6');<?php } ?>s5thumb5();"<?php } ?>><a <?php if ($s5_effectmouse == "click" || $s5_mootoolsmouse == "click") { ?>onclick="s5_active6()"<?php } ?><?php if ($s5_effectmouse == "mouse" || $s5_mootoolsmouse == "mouse") { ?>onmouseover="s5_active6()"<?php } ?>><?php echo $text6line ?></a></li>  
        <?php } ?>
		<?php if ($text7line != "") { ?> 
			<li id="thumb6" class="s5_ts_not_active" <?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectmouse == "click") { ?>onclick<?php } else {?>onmouseover<?php }?>="<?php if ($s5_effectsani == "fade") { ?>s5thumb6op();shiftOpacity_ts('s5_button_item7');<?php } ?>s5thumb6();"<?php } ?>><a <?php if ($s5_effectmouse == "click" || $s5_mootoolsmouse == "click") { ?>onclick="s5_active7()"<?php } ?><?php if ($s5_effectmouse == "mouse" || $s5_mootoolsmouse == "mouse") { ?>onmouseover="s5_active7()"<?php } ?>><?php echo $text7line ?></a></li>  
		<?php } ?>
		<?php if ($text8line != "") { ?>
			<li id="thumb7" class="s5_ts_not_active" <?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectmouse == "click") { ?>onclick<?php } else {?>onmouseover<?php }?>="<?php if ($s5_effectsani == "fade") { ?>s5thumb7op();shiftOpacity_ts('s5_button_item8');<?php } ?>s5thumb7();"<?php } ?>><a <?php if ($s5_effectmouse == "click" || $s5_mootoolsmouse == "click") { ?>onclick="s5_active8()"<?php } ?><?php if ($s5_effectmouse == "mouse" || $s5_mootoolsmouse == "mouse") { ?>onmouseover="s5_active8()"<?php } ?>><?php echo $text8line ?></a></li>  
		<?php } ?>
		<?php if ($text9line != "") { ?>
			<li id="thumb8" class="s5_ts_not_active" <?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectmouse == "click") { ?>onclick<?php } else {?>onmouseover<?php }?>="<?php if ($s5_effectsani == "fade") { ?>s5thumb8op();shiftOpacity_ts('s5_button_item9');<?php } ?>s5thumb8();"<?php } ?>><a <?php if ($s5_effectmouse == "click" || $s5_mootoolsmouse == "click") { ?>onclick="s5_active9()"<?php } ?><?php if ($s5_effectmouse == "mouse" || $s5_mootoolsmouse == "mouse") { ?>onmouseover="s5_active9()"<?php } ?>><?php echo $text9line ?></a></li>  
		<?php } ?>
		<?php if ($text10line != "") { ?>
			<li id="thumb9" class="s5_ts_not_active" <?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectmouse == "click") { ?>onclick<?php } else {?>onmouseover<?php }?>="<?php if ($s5_effectsani == "fade") { ?>s5thumb9op();shiftOpacity_ts('s5_button_item10');<?php } ?>s5thumb9();"<?php } ?>><a <?php if ($s5_effectmouse == "click" || $s5_mootoolsmouse == "click") { ?>onclick="s5_active10()"<?php } ?><?php if ($s5_effectmouse == "mouse" || $s5_mootoolsmouse == "mouse") { ?>onmouseover="s5_active10()"<?php } ?>><?php echo $text10line ?></a></li>  
        <?php } ?>
		</ul>  
     </div>  
</div>
</div>
</div>

<div id="s5_tabshow_left" style="width:<?php echo $width ?>px">
<div id="s5_tabshow_right" style="width:<?php echo $width ?>px;">
	 <div id="s5_button" style="width:<?php echo $width-2 ?>px">  
	     <ul id="s5_button_content">  
			<?php if ($text1line != "") { ?>
				<li class="s5_button_item" id="s5_button_item1" style="width:<?php echo $width; ?>px;<?php if ($s5_javascript == "s5effects") { ?>left:0px;display:block;<?php } ?>"><div style="margin-left:0px;padding:0px" class="s5_button_item_inner"><?php
$myblurb_modules = &JModuleHelper::getModules( 's5_tab1' );
foreach ($myblurb_modules as $myblurb) {
$_options = array( 'style' => 'xhtml' );
echo JModuleHelper::renderModule( $myblurb, $_options );
}
?>
<div style="clear:both;height:0px"></div>
</div></li> 
			<?php } ?>
			<?php if ($text2line != "") { ?>		 
				<li class="s5_button_item" id="s5_button_item2" style="width:<?php echo $width; ?>px;<?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectsani == "fade") { ?>opacity:.0; <?php if ($iss_ie6 == "yes" || $iss_ie7 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?><?php } ?>left:0px;display:none;<?php } ?>"><div style="margin-left:0px;padding:0px" class="s5_button_item_inner"><?php
$myblurb_modules = &JModuleHelper::getModules( 's5_tab2' );
foreach ($myblurb_modules as $myblurb) {
$_options = array( 'style' => 'xhtml' );
echo JModuleHelper::renderModule( $myblurb, $_options );
}
?>
<div style="clear:both;height:0px"></div>
</div></li>  
			<?php } ?>
			<?php if ($text3line != "") { ?>        
				<li class="s5_button_item" id="s5_button_item3" style="width:<?php echo $width; ?>px;<?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectsani == "fade") { ?>opacity:.0; <?php if ($iss_ie6 == "yes" || $iss_ie7 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?><?php } ?>left:0px;display:none;<?php } ?>"><div style="margin-left:0px;padding:0px" class="s5_button_item_inner"><?php
$myblurb_modules = &JModuleHelper::getModules( 's5_tab3' );
foreach ($myblurb_modules as $myblurb) {
$_options = array( 'style' => 'xhtml' );
echo JModuleHelper::renderModule( $myblurb, $_options );
}
?>
<div style="clear:both;height:0px"></div>
</div></li>  
			<?php } ?>
			<?php if ($text4line != "") { ?>        
				<li class="s5_button_item" id="s5_button_item4" style="width:<?php echo $width; ?>px;<?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectsani == "fade") { ?>opacity:.0; <?php if ($iss_ie6 == "yes" || $iss_ie7 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?><?php } ?>left:0px;display:none;<?php } ?>"><div style="margin-left:0px;padding:0px" class="s5_button_item_inner"><?php
$myblurb_modules = &JModuleHelper::getModules( 's5_tab4' );
foreach ($myblurb_modules as $myblurb) {
$_options = array( 'style' => 'xhtml' );
echo JModuleHelper::renderModule( $myblurb, $_options );
}
?>
<div style="clear:both;height:0px"></div>
</div></li>  
			<?php } ?>
			<?php if ($text5line != "") { ?>        
				<li class="s5_button_item" id="s5_button_item5" style="width:<?php echo $width; ?>px;<?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectsani == "fade") { ?>opacity:.0; <?php if ($iss_ie6 == "yes" || $iss_ie7 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?><?php } ?>left:0px;display:none;<?php } ?>"><div style="margin-left:0px;padding:0px" class="s5_button_item_inner">
				<?php
$myblurb_modules = &JModuleHelper::getModules( 's5_tab5' );
foreach ($myblurb_modules as $myblurb) {
$_options = array( 'style' => 'xhtml' );
echo JModuleHelper::renderModule( $myblurb, $_options );
}
?>
<div style="clear:both;height:0px"></div>
</div></li>  
			<?php } ?>
			<?php if ($text6line != "") { ?>        
				<li class="s5_button_item" id="s5_button_item6" style="width:<?php echo $width; ?>px;<?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectsani == "fade") { ?>opacity:.0; <?php if ($iss_ie6 == "yes" || $iss_ie7 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?><?php } ?>left:0px;display:none;<?php } ?>"><div style="margin-left:0px;padding:0px" class="s5_button_item_inner"><?php
$myblurb_modules = &JModuleHelper::getModules( 's5_tab6' );
foreach ($myblurb_modules as $myblurb) {
$_options = array( 'style' => 'xhtml' );
echo JModuleHelper::renderModule( $myblurb, $_options );
}
?>
<div style="clear:both;height:0px"></div>
</div></li>  
			<?php } ?>
			<?php if ($text7line != "") { ?>        
				<li class="s5_button_item" id="s5_button_item7" style="width:<?php echo $width; ?>px;<?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectsani == "fade") { ?>opacity:.0; <?php if ($iss_ie6 == "yes" || $iss_ie7 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?><?php } ?>left:0px;display:none;<?php } ?>"><div style="margin-left:0px;padding:0px" class="s5_button_item_inner"><?php
$myblurb_modules = &JModuleHelper::getModules( 's5_tab7' );
foreach ($myblurb_modules as $myblurb) {
$_options = array( 'style' => 'xhtml' );
echo JModuleHelper::renderModule( $myblurb, $_options );
}
?>
<div style="clear:both;height:0px"></div>
</div></li>  
			<?php } ?>
			<?php if ($text8line != "") { ?>		
				<li class="s5_button_item" id="s5_button_item8" style="width:<?php echo $width; ?>px;<?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectsani == "fade") { ?>opacity:.0; <?php if ($iss_ie6 == "yes" || $iss_ie7 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?><?php } ?>left:0px;display:none;<?php } ?>"><div style="margin-left:0px;padding:0px" class="s5_button_item_inner"><?php
$myblurb_modules = &JModuleHelper::getModules( 's5_tab8' );
foreach ($myblurb_modules as $myblurb) {
$_options = array( 'style' => 'xhtml' );
echo JModuleHelper::renderModule( $myblurb, $_options );
}
?>
<div style="clear:both;height:0px"></div>
</div></li>  
			<?php } ?>
			<?php if ($text9line != "") { ?>		
				<li class="s5_button_item" id="s5_button_item9" style="width:<?php echo $width; ?>px;<?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectsani == "fade") { ?>opacity:.0; <?php if ($iss_ie6 == "yes" || $iss_ie7 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?><?php } ?>left:0px;display:none;<?php } ?>"><div style="margin-left:0px;padding:0px" class="s5_button_item_inner"><?php
$myblurb_modules = &JModuleHelper::getModules( 's5_tab9' );
foreach ($myblurb_modules as $myblurb) {
$_options = array( 'style' => 'xhtml' );
echo JModuleHelper::renderModule( $myblurb, $_options );
}
?>
<div style="clear:both;height:0px"></div>
</div></li>  
			<?php } ?>
			<?php if ($text10line != "") { ?>		
				<li class="s5_button_item" id="s5_button_item10" style="width:<?php echo $width; ?>px;<?php if ($s5_javascript == "s5effects") { ?><?php if ($s5_effectsani == "fade") { ?>opacity:.0; <?php if ($iss_ie6 == "yes" || $iss_ie7 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?><?php } ?>left:0px;display:none;<?php } ?>"><div style="margin-left:0px;padding:0px" class="s5_button_item_inner"><?php
$myblurb_modules = &JModuleHelper::getModules( 's5_tab10' );
foreach ($myblurb_modules as $myblurb) {
$_options = array( 'style' => 'xhtml' );
echo JModuleHelper::renderModule( $myblurb, $_options );
}
?>
<div style="clear:both;height:0px"></div>
</div></li>  
			<?php } ?>
	     </ul>  
	 </div>
</div>
</div>

<script type="text/javascript" src="<?php echo $LiveSite?>/modules/mod_s5_tabshow/s5_tabshow/s5_activejs.js"></script>

