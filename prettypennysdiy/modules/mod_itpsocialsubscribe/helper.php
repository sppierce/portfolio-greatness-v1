<?php
/**
 * @package      ITPSocialSubscribe
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class ItpSocialSubscribeHelper
{
    static protected $googleScriptLoaded = false;

    /**
     * Generate a code for the extra buttons.
     *
     * @param object $params
     *
     * @return string
     */
    public static function getExtraButtons($params)
    {
        $html = "";

        // Extra buttons
        for ($i = 1; $i < 6; $i++) {
            $btnName     = "ebuttons" . $i;
            $extraButton = $params->get($btnName, "");
            if (!empty($extraButton)) {
                $html .= $extraButton;
            }
        }

        return $html;
    }

    /**
     * @param Joomla\Registry\Registry $params
     *
     * @return string
     */
    public static function getTwitter($params)
    {
        $html = "";
        if ($params->get("twitterButton")) {

            $locale = self::getLocale("twitter", $params);

            $counter = (!$params->get("twitterCounter")) ? "false" : "true";

            $html = '
            <div class="itp_social_sidebar itp_twitter">
            	<a href="https://twitter.com/' . $params->get("twitterName") . '" class="twitter-follow-button" data-show-count="' . $counter . '" data-lang="' . $locale . '" data-size="' . $params->get("twitterSize") . '" >' . JText::sprintf("MOD_ITPSOCIALSUBSCRIBE_FOLLOW", $params->get("twitterName")) . '</a>
            </div>
            ';

            if ($params->get("load_twitter_library", 1)) {
                $html .= '<script type="text/javascript">
window.twttr = (function (d, s, id) {
  var t, js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src= "https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);
  return window.twttr || (t = { _e: [], ready: function (f) { t._e.push(f) } });
}(document, "script", "twitter-wjs"));
</script>';
            }
        }

        return $html;
    }

    /**
     * @param Joomla\Registry\Registry $params
     *
     * @return string
     */
    public static function getGoogleBadge($params)
    {
        $html = "";

        if ($params->get("badgeWidget")) {

            // Get locale code
            $locale = self::getLocale("google", $params);

            // Get address
            $url = $params->get("badgeAddress");

            // Generate code
            $html .= '<div class="itp_google_badge">';
            $html .= '<link href="' . $url . '" rel="publisher" />';

            // Load the JavaScript asynchroning
            if ($params->get("loadGoogleJsLib", 1)) {

                $googleScript = self::getGoogleLoadingScript($locale);

                if (!is_null($googleScript)) {
                    $html .= $googleScript;
                }
            }

            switch ($params->get("badgeRenderer")) {
                case 1:
                    $html .= self::genGoogleBadge($params, $url);
                    break;

                default:
                    $html .= self::genGoogleBadgeHTML5($params, $url);
                    break;
            }


            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param string $service
     * @param Joomla\Registry\Registry $params
     *
     * @return string
     */
    protected static function getLocale($service, $params)
    {
        $locale = "";

        // Get locale code
        if (!$params->get("dynamicLocale")) {

            switch ($service) {

                case "google":
                    $locale = $params->get("badgeLocale", "en");
                    break;

                case "twitter":
                    $locale = $params->get("twitterLanguage", "en");
                    break;

                case "facebook":
                    $locale = $params->get("fbLocale", "en_US");
                    break;

            }

        } else {

            $tag     = JFactory::getLanguage()->getTag();
            $locale  = str_replace("-", "_", $tag);
            $locales = self::getButtonsLocales($locale);

            switch ($service) {

                case "google":
                case "twitter":
                    $locale = JArrayHelper::getValue($locales, $service, "en");
                    break;

                case "facebook":
                    $locale = JArrayHelper::getValue($locales, $service, "en_US");
                    break;

            }

        }

        return $locale;

    }

    public static function getGoogleLoadingScript($locale)
    {
        if (self::$googleScriptLoaded) {
            return null;
        }

        self::$googleScriptLoaded = true;

        return '
<script>
window.___gcfg = {
    lang: "' . $locale . '",
    parsetags: "onload"
};
        
(function() {
    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
    po.src = "https://apis.google.com/js/platform.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
})();
</script>';

    }

    /**
     * Render the Google badge in standard syntax
     *
     * @param Joomla\Registry\Registry  $params
     * @param string $url
     *
     * @return string
     */
    public static function genGoogleBadge($params, $url)
    {
        return '<g:page href="' . $url . '" width="' . $params->get("badgeWidth") . '" theme="' . $params->get("badgeTheme") . '" layout="landscape"></g:plus>';
    }

    /**
     * Render the Google badge in HTML5 syntax
     *
     * @param Joomla\Registry\Registry  $params
     * @param string $url
     *
     * @return string
     */
    public static function genGoogleBadgeHTML5($params, $url)
    {
        return '<div class="g-page" data-href="' . $url . '" data-width="' . $params->get("badgeWidth") . '" data-theme="' . $params->get("badgeTheme") . '" data-layout="landscape" ></div>';
    }

    /**
     * @param Joomla\Registry\Registry $params
     *
     * @return string
     */
    public static function getFacebookLike($params)
    {
        $html = "";
        if ($params->get("facebookLikeButton")) {

            $url = $params->get("facebookLikePageAddress");

            $locale = self::getLocale("facebook", $params);

            $faces = (!$params->get("facebookLikeFaces")) ? "false" : "true";
//            $share = (!$params->get("facebookLikeShare")) ? "false" : "true";

            $html = '<div class="itp_socialsubscribe_fbl">';

            switch ($params->get("facebookLikeRenderer")) {

                case 0: // iframe
                    $html .= self::genFacebookLikeIframe($params, $url, $faces, $locale);
                    break;

                case 1: // XFBML
                    $html .= self::genFacebookLikeXfbml($params, $url, $faces, $locale);
                    break;

                default: // HTML5
                    $html .= self::genFacebookLikeHtml5($params, $url, $faces, $locale);
                    break;
            }

            $html .= "</div>";
        }

        return $html;
    }

    /**
     * @param Joomla\Registry\Registry $params
     * @param string $url
     * @param string $faces
     * @param string $locale
     *
     * @return string
     */
    public static function genFacebookLikeIframe($params, $url, $faces, $locale)
    {
        $html = '<iframe src="//www.facebook.com/plugins/like.php?';

        $html .= ' href=' . rawurlencode($url) . '&amp;send=false&amp;locale=' . $locale . '&amp;layout=standard&amp;show_faces=' . $faces . '&amp;width=' . $params->get("facebookLikeWidth", "450") . '&amp;action=' . $params->get("facebookLikeAction", 'like') . '&amp;colorscheme=' . $params->get("facebookLikeColor", 'light') . '&amp;height=80';
        if ($params->get("facebookLikeFont")) {
            $html .= "&amp;font=" . $params->get("facebookLikeFont");
        }
        if ($params->get("facebookLikeAppId")) {
            $html .= "&amp;appId=" . $params->get("facebookLikeAppId");
        }
        if ($params->get("facebookLikeShare")) {
            $html .= "&amp;share=" . $params->get("facebookLikeShare");
        }
        $html .= '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' . $params->get("facebookLikeWidth", "450") . 'px; height:80px;" allowTransparency="true"></iframe>';

        return $html;
    }

    /**
     * @param Joomla\Registry\Registry $params
     * @param string $url
     * @param string $faces
     * @param string $locale
     *
     * @return string
     */
    public static function genFacebookLikeXfbml($params, $url, $faces, $locale)
    {
        $html = "";

        if ($params->get("facebookRootDiv", 1)) {
            $html .= '<div id="fb-root"></div>';
        }

        if ($params->get("facebookLoadJsLib", 1)) {
            $appId = "";
            if ($params->get("facebookLikeAppId")) {
                $appId = '&amp;appId=' . $params->get("facebookLikeAppId");
            }

            $html .= '
<script>
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/' . $locale . '/sdk.js#xfbml=1&version=v2.0' . $appId . '";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));
</script>';

        }

        $html .= '
            <fb:like 
            href="' . $url . '" 
            layout="standard" 
            show_faces="' . $faces . '" 
            width="' . $params->get("facebookLikeWidth", "450") . '"
            colorscheme="' . $params->get("facebookLikeColor", "light") . '"
            share="' . $params->get("facebookLikeShare", 0) . '"
            action="' . $params->get("facebookLikeAction", 'like') . '" ';

        if ($params->get("facebookLikeFont")) {
            $html .= 'font="' . $params->get("facebookLikeFont") . '"';
        }
        $html .= '></fb:like>';

        return $html;
    }

    /**
     * @param Joomla\Registry\Registry $params
     * @param string $url
     * @param string $faces
     * @param string $locale
     *
     * @return string
     */
    public static function genFacebookLikeHtml5($params, $url, $faces, $locale)
    {
        $html = '';

        if ($params->get("facebookRootDiv", 1)) {
            $html .= '<div id="fb-root"></div>';
        }

        if ($params->get("facebookLoadJsLib", 1)) {
            $appId = "";
            if ($params->get("facebookLikeAppId")) {
                $appId = '&appId=' . $params->get("facebookLikeAppId");
            }

            $html .= '
<script>
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/' . $locale . '/sdk.js#xfbml=1&version=v2.0' . $appId . '";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));
</script>';

        }

        $html .= '
            <div 
            class="fb-like" 
            data-href="' . $url . '" 
            data-share="' . $params->get("facebookLikeShare", 0) . '"
            data-layout="standard" 
            data-width="' . $params->get("facebookLikeWidth", "450") . '"
            data-show-faces="' . $faces . '" 
            data-colorscheme="' . $params->get("facebookLikeColor", "light") . '"
            data-action="' . $params->get("facebookLikeAction", 'like') . '"';


        if ($params->get("facebookLikeFont")) {
            $html .= ' data-font="' . $params->get("facebookLikeFont") . '" ';
        }

        $html .= '></div>';

        return $html;

    }

    /**
     * @param Joomla\Registry\Registry $params
     *
     * @return string
     */
    public static function getLinkedInAndPinterest($params)
    {
        $html = "";
        if ($params->get("linkedInButton") or $params->get("pinterestButton")) {

            $text = $params->get("pinlinkText");
            if (false !== strpos($text, "MOD_ITPSOCIALSUBSCRIBE")) {
                $text = JText::_($text);
            }

            $targetPinterest = 'target="' . $params->get("pinterest_small_open_link", "_blank") . '"';
            $targetLinkedIn  = 'target="' . $params->get("linkedin_small_open_link", "_blank") . '"';

            $html = '
            <div class="itp_social_sidebar itp_linkedin_pinterest">
            	<span>' . htmlentities($text, ENT_QUOTES, "UTF-8") . '</span>';

            if ($params->get("linkedInButton")) {
                $html .= '
            	<a class="itp_external" href="' . $params->get("linkedInAddress") . '" ' . $targetLinkedIn . ' style="margin: 0 10px;"><img width="70" height="25" alt="' . JText::_("MOD_ITPSOCIALSUBSCRIBE_LINKEDIN_TODAY") . '" src="http://c759930.r30.cf2.rackcdn.com/wp-content/themes/b2c/images/linkedin-today.png"></a>';
            }

            if ($params->get("pinterestButton")) {
                $html .= '
            	<a class="itp_external" href="' . $params->get("pinterestAddress") . '" ' . $targetPinterest . '><img width="78" height="26" alt="' . JText::_("MOD_ITPSOCIALSUBSCRIBE_FOLLOW_ME_PINTEREST") . '" src="http://passets-cdn.pinterest.com/images/pinterest-button.png"></a>
            	';
            }

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param Joomla\Registry\Registry $params
     *
     * @return string
     */
    public static function getYoutube($params)
    {
        $html = array();
        if ($params->get("youtubeWidget")) {

            $channelName = JString::trim($params->get("youtubeChannelName"));
            if (!$channelName) {
                return "";
            }

            $html[] = '<div class="itp_social_sidebar itp-youtube">';

            if (0 !== strpos($channelName, "UC")) { // Check for channel ID
                $channel = 'data-channel="' . htmlspecialchars($channelName, ENT_QUOTES, "UTF-8") . '"';
            } else {
                $channel = 'data-channelid="' . htmlspecialchars($channelName, ENT_QUOTES, "UTF-8") . '"';
            }

            $layout = 'data-layout="' . $params->get("youtubeLayout") . '"';
            $theme  = 'data-theme="' . $params->get("youtubeTheme") . '"';

            $html[] = '<div class="g-ytsubscribe" ' . $channel . ' ' . $layout . ' ' . $theme . '></div>';

            $html[] = '</div>';


            // Load the JavaScript asynchroning
            if ($params->get("loadGoogleJsLib", 1)) {

                $locale = self::getLocale("twitter", $params);

                $googleScript = self::getGoogleLoadingScript($locale);

                if (!is_null($googleScript)) {
                    $html[] = $googleScript;
                }
            }
        }

        return implode("\n", $html);
    }

    /**
     * @param Joomla\Registry\Registry $params
     *
     * @return string
     */
    public static function getPinterest($params)
    {
        $html = "";
        if ($params->get("pinterestFollowButton")) {

            $target = 'target="' . $params->get("pinterest_large_open_link", "_blank") . '"';

            $html = '<div class="itp_social_sidebar itp_pinterest">';
            $html .= '
            	<a href="' . $params->get("pinterestFollowAddress") . '" ' . $target . '>
            	<img src="http://passets-cdn.pinterest.com/images/follow-on-pinterest-button.png" width="156" height="26" alt="' . JText::_("MOD_ITPSOCIALSUBSCRIBE_FOLLOW_ME_PINTEREST") . '" />
            	</a>
            	';

            $html .= '</div>';

        }

        return $html;
    }

    /**
     * @param Joomla\Registry\Registry $params
     *
     * @return string
     */
    public static function getSubscriptionIcons($params)
    {
        $html = "";
        if ($params->get("subscription_icons")) {

            $target = 'target="' . $params->get("sicons_open_link", "_blank") . '"';

            $html = '<div class="itp_social_sidebar itp_subscription_links">';

            // Social links
            $html .= '<ul class="itp_subscription_social">';

            // LinkedIn
            if ($params->get("sicons_linkedin")) {
                $html .= '
            	<li class="linkedin">
                  <a rel="nofollow external" title="LinkedIn" href="' . $params->get("sicons_linkedin_address") . '" ' . $target . '>LinkedIn</a>
                </li>
            	';
            }

            // YouTube
            if ($params->get("sicons_youtube")) {
                $html .= '
            	<li class="youtube">
                  <a rel="nofollow external" title="YouTube" href="' . $params->get("sicons_youtube_address") . '" ' . $target . '>YouTube</a>
                </li>
            	';
            }

            // RSS
            if ($params->get("sicons_rss")) {
                $html .= '
            	<li class="rss">
                  <a rel="nofollow external" title="RSS Feed" href="' . $params->get("sicons_rss_address") . '" ' . $target . '>RSS</a>
                </li>
            	';
            }

            // SubmleUpon
            if ($params->get("sicons_stumbpleupon")) {
                $html .= '
            	<li class="stumbleupon">
                  <a rel="nofollow external" title="StumbleUpon" href="' . $params->get("sicons_stumbleupon_address") . '" ' . $target . '>Stumble</a>
                </li>
            	';
            }

            $html .= '</ul>';

            // Separator
            if ($params->get("sicons_separator")) {
                $html .= '<div class="itp_sseparator"></div>';
            }

            // Applications
            if ($params->get("sicons_android")) {

                $html .= '<ul class="itp_subscription_apps">';

                // Android
                if ($params->get("sicons_android")) {
                    $html .= '
                	<li class="android">
                      <a rel="nofollow external" title="' . JText::_("MOD_ITPSOCIALSUBSCRIBE_ANDROID_APP") . '" href="' . $params->get("sicons_android_address") . '" ' . $target . '>Android</a>
                    </li>
                	';
                }

                // iPhone
                if ($params->get("sicons_iphone")) {
                    $html .= '
                	<li class="iphone">
                      <a rel="nofollow external" title="' . JText::_("MOD_ITPSOCIALSUBSCRIBE_IPHONE_APP") . '" href="' . $params->get("sicons_iphone_address") . '" ' . $target . '>iPhone</a>
                    </li>
                	';
                }

                // iPad
                if ($params->get("sicons_ipad")) {
                    $html .= '
                	<li class="ipad">
                      <a rel="nofollow external" title="' . JText::_("MOD_ITPSOCIALSUBSCRIBE_IPAD_APP") . '" href="' . $params->get("sicons_ipad_address") . '" ' . $target . '>iPad</a>
                    </li>
                	';
                }

                $html .= '</ul>';

            }

            $html .= '</div>';

        }

        return $html;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    public static function getButtonsLocales($locale)
    {
        // Default locales
        $result = array(
            "twitter"  => "en",
            "facebook" => "en_US",
            "google"   => "en"
        );

        // The locales map
        $locales = array(
            "en_US" => array(
                "twitter"  => "en",
                "facebook" => "en_US",
                "google"   => "en"
            ),
            "en_GB" => array(
                "twitter"  => "en",
                "facebook" => "en_GB",
                "google"   => "en_GB"
            ),
            "th_TH" => array(
                "twitter"  => "th",
                "facebook" => "th_TH",
                "google"   => "th"
            ),
            "ms_MY" => array(
                "twitter"  => "msa",
                "facebook" => "ms_MY",
                "google"   => "ms"
            ),
            "tr_TR" => array(
                "twitter"  => "tr",
                "facebook" => "tr_TR",
                "google"   => "tr"
            ),
            "hi_IN" => array(
                "twitter"  => "hi",
                "facebook" => "hi_IN",
                "google"   => "hi"
            ),
            "tl_PH" => array(
                "twitter"  => "fil",
                "facebook" => "tl_PH",
                "google"   => "fil"
            ),
            "zh_CN" => array(
                "twitter"  => "zh-cn",
                "facebook" => "zh_CN",
                "google"   => "zh"
            ),
            "ko_KR" => array(
                "twitter"  => "ko",
                "facebook" => "ko_KR",
                "google"   => "ko"
            ),
            "it_IT" => array(
                "twitter"  => "it",
                "facebook" => "it_IT",
                "google"   => "it"
            ),
            "da_DK" => array(
                "twitter"  => "da",
                "facebook" => "da_DK",
                "google"   => "da"
            ),
            "fr_FR" => array(
                "twitter"  => "fr",
                "facebook" => "fr_FR",
                "google"   => "fr"
            ),
            "pl_PL" => array(
                "twitter"  => "pl",
                "facebook" => "pl_PL",
                "google"   => "pl"
            ),
            "nl_NL" => array(
                "twitter"  => "nl",
                "facebook" => "nl_NL",
                "google"   => "nl"
            ),
            "id_ID" => array(
                "twitter"  => "in",
                "facebook" => "nl_NL",
                "google"   => "in"
            ),
            "hu_HU" => array(
                "twitter"  => "hu",
                "facebook" => "hu_HU",
                "google"   => "hu"
            ),
            "fi_FI" => array(
                "twitter"  => "fi",
                "facebook" => "fi_FI",
                "google"   => "fi"
            ),
            "es_ES" => array(
                "twitter"  => "es",
                "facebook" => "es_ES",
                "google"   => "es"
            ),
            "ja_JP" => array(
                "twitter"  => "ja",
                "facebook" => "ja_JP",
                "google"   => "ja"
            ),
            "nn_NO" => array(
                "twitter"  => "no",
                "facebook" => "nn_NO",
                "google"   => "no"
            ),
            "ru_RU" => array(
                "twitter"  => "ru",
                "facebook" => "ru_RU",
                "google"   => "ru"
            ),
            "pt_PT" => array(
                "twitter"  => "pt",
                "facebook" => "pt_PT",
                "google"   => "pt"
            ),
            "pt_BR" => array(
                "twitter"  => "pt",
                "facebook" => "pt_BR",
                "google"   => "pt"
            ),
            "sv_SE" => array(
                "twitter"  => "sv",
                "facebook" => "sv_SE",
                "google"   => "sv"
            ),
            "zh_HK" => array(
                "twitter"  => "zh-tw",
                "facebook" => "zh_HK",
                "google"   => "zh_HK"
            ),
            "zh_TW" => array(
                "twitter"  => "zh-tw",
                "facebook" => "zh_TW",
                "google"   => "zh_TW"
            ),
            "de_DE" => array(
                "twitter"  => "de",
                "facebook" => "de_DE",
                "google"   => "de"
            ),
            "bg_BG" => array(
                "twitter"  => "en",
                "facebook" => "bg_BG",
                "google"   => "bg"
            ),

        );

        if (isset($locales[$locale])) {
            $result = $locales[$locale];
        }

        return $result;
    }
}
