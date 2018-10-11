<?php
/*------------------------------------------------------------------------
# mod_sw_facebookdisplay - SW FACEBOOK DISPLAY
# ------------------------------------------------------------------------
# @author - Social Widgets
# copyright - All rights reserved by Social Widgets
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://socialwidgets.net/
# Technical Support:  admin@socialwidgets.net
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die;

//all parameters
$pageURL = $params->get('pageURL');
//$connections = $params->get('connections');
$width = $params->get('width');
$height = $params->get('height');
$streams = $params->get('streams');
//$color_scheme = $params->get('color_scheme');
$show_faces = $params->get('show_faces');
$header = $params->get('header');
//$border = $params->get('border');
$print_facebook = '';
$print_facebook .= '<div class="fb-page" data-href="'. $pageURL .'" data-width="'. $width .'" data-height="'. $height .'" data-hide-cover="'. $header .'" data-show-facepile="' . $show_faces .'" data-show-posts="'. $streams .'"><div class="fb-xfbml-parse-ignore"><blockquote cite="'. $pageURL .'"><a href="'. $pageURL .'">Facebook</a></blockquote></div></div>';
$print_facebook .= '<div style="width: '. $width .'px;font-size: 9px; color: #808080; font-weight: normal; font-family: tahoma,verdana,arial,sans-serif; line-height: 1.28; text-align: right; direction: ltr;"><a href="http://corporatecostcontrol.com/" target="_blank" style="color: #808080;" title="visit us">unemployment cost management</a></div>';
?>
<div id="sw_facebook_display" class="<?php echo $params->get('moduleclass_sfx');?>">
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
	<?php echo $print_facebook; ?>
</div>
