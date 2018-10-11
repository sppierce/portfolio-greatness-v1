<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.TheGrue.Social.Bar
 *
 * @copyright   Copyright (C) 2014 TheGrue.org. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
/**
 * Contact Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.TheGrue.Social.Bar
 * @since       3.3
 */
class plgContentGrueSocialBar extends JPlugin
{	
    public function onContentPrepare($context, &$article, &$params, $limitstart)
    {
        if ($context == 'mod_custom.content') {
            return true;
        }
        if ((JRequest :: getVar('view')) != 'article') {
            return true;
        }
        $app = JFactory::getApplication();
        //ignore admin
        if ($app->isAdmin()) {
            return true;
        }
		
		$doc = JFactory::getDocument();
		
		$jquery_lib = $this->params->get('jquery_lib', '0');
		if ( version_compare( JVERSION, '3.0.0', '<' ) == 1) { 
		if ($jquery_lib == "1"){
			$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
		}} else { JHtml::_('bootstrap.framework');}
		
		$doc->addStyleSheet(JURI::root(true) . '/plugins/content/gruesocialbar/css/style.css');

        $doc->addScript("http://platform.twitter.com/widgets.js");
        $doc->addScript("http://platform.linkedin.com/in.js");
        $doc->addScript("https://apis.google.com/js/platform.js");		
        $facebookJs = '(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, "script", "facebook-jssdk"));';
		$floatingJs = 'jQuery("document").ready(function($){
		var nav = $("#grue-social-bar");
		var offsetPixels = $("#grue-social-bar").offset().top;
		var max_width = $("#grue-social-bar").width();;
			$(window).scroll(function() {
				if ($(window).scrollTop() > offsetPixels) {
					$("#grue-social-bar").css({
						"position": "fixed",
						"top": "15px",
						"width": max_width
					});
				} else {
					$("#grue-social-bar").css({
						"position": "relative",
						"top": "0"
					});
				}
			});
		});';

        $doc->addScriptDeclaration($facebookJs);
		$doc->addScriptDeclaration($floatingJs);

		$show_data = $this->params->get('show_data', '0');
		
        $html = '<div id="grue-social-bar"><ul>';

        if ($show_data == "0"){
            
		$html .= '<li><div class="g-plusone" data-size="medium" data-annotation="none"></div></li>';
        $html .= '<li><a class="twitter-share-button" href="https://twitter.com/share" data-related="twitterdev" data-size="small" data-count="none">Tweet</a></li>'; 
        $html .= '<li><div class="fb-like" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div></li>';
		$html .= '<li><script type="IN/Share" data-counter="right" data-showzero=false></script></li>';
		} else {
		$html .= '<li><div class="g-plusone" data-size="medium" data-annotation="bubble"></div></li>';
        $html .= '<li><a class="twitter-share-button" href="https://twitter.com/share" data-related="twitterdev" data-size="small" data-count="horizontal">Tweet</a></li>'; 
        $html .= '<li><div class="fb-like" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div></li>';
		$html .= '<li><script type="IN/Share" data-counter="right" data-showzero=true></script></li>';
		}
        $html .= '</ul></div>';
		
        $article->text =$html.$article->text ;

        return true;
    }

    function onContentAfterDisplay($context, &$article, &$params, $limitstart)
    {
        if ($context == 'mod_custom.content') {
            return '';
        }
    }
}
