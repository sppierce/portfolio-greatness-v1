<?php

defined("_JEXEC") or die("Restricted access");
/**
 * @package             Joomla
 * @subpackage          CoalaWeb Facebook JS Plugin
 * @author              Steven Palmer
 * @author url          https://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Facebook JS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgSystemCwfacebookjs extends JPlugin
{

    var $checkOk;

    public function __construct($subject, $config)
    {

        parent::__construct($subject, $config);

        $this->joomla = JFactory::getApplication();
        $this->checkOk = $this->checkDependencies();

        // Load the language files
        $jlang = JFactory::getLanguage();

        // Plugin
        $jlang->load('plg_system_cwfacebookjs', JPATH_ADMINISTRATOR, 'en-GB', true);
        $jlang->load('plg_system_cwfacebookjs', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
        $jlang->load('plg_system_cwfacebookjs', JPATH_ADMINISTRATOR, null, true);

    }

    /**
     *
     * @return type
     */
    function onAfterRender()
    {
        $doc = JFactory::getDocument();
        $joomla = JFactory::getApplication();

        // Lets do a few checks first
        if (
            $joomla->getName() !== 'site' ||
            $doc->getType() !== 'html' ||
            $this->checkOk === false
        ) {

            return;
        }

        //Social Links
        $socialLoad = '';
        $appIdS = '';
        $social = 'com_coalawebsociallinks';
        $versionS = JPATH_ADMINISTRATOR . '/' . 'components/' . $social . '/version.php';

        if (file_exists($versionS)) {
            $checkS = JComponentHelper::isEnabled($social, true);
            $comParams = JComponentHelper::getParams('com_coalawebsociallinks');
            $appIdS = $comParams->get('fb_app_id');
            if ($checkS) {
                $socialLoad = $comParams->get('load_fb_js', '1');
            }
        }

        //Comments
        $commentLoad = '';
        $fbComments = '';
        $appIdC = '';
        $comments = 'com_coalawebcomments';
        $versionC = JPATH_ADMINISTRATOR . '/' . 'components/' . $comments . '/version.php';

        if (file_exists($versionC)) {
            $checkC = JComponentHelper::isEnabled($comments, true);
            $comParamsTwo = JComponentHelper::getParams('com_coalawebcomments');
            $appIdC = $comParamsTwo->get('fb_app_id');
            if ($checkC) {
                $commentLoad = $comParamsTwo->get('load_fb_js', '1');
            }
        }

        // Lets see if we should load the Facebook JS
        if ($socialLoad === '1' || $commentLoad === '1') {
            $loadJs = '1';
        } else {
            $loadJs = '';
        }

        //Do we want to load Facebook JS?
        if (!$loadJs) {
            return;
        }

        //Lets check for a Facebook App ID
        if ($appIdS) {
            $appId = $comParams->get('fb_app_id');
        } elseif ($appIdC) {
            $appId = $comParamsTwo->get('fb_app_id');
        } else {
            $appId = '';
        }

        //Type of SDK
        $sdkType = $this->params->get('sdk_type', 'all');

        //Comments
        $url = JURI::getInstance()->toString();
        $mailUrl = JRoute::_('index.php');

        //Helper class to check if we should load Facebook JS on the current page
        $helpFunc = new CwGearsHelperLoadcount();

        // Detect language
        $lang = JFactory::getLanguage();
        $locale = $lang->getTag();
        $locale = str_replace("-", "_", $locale);

        // Facebook and Google only seem to support es_ES and es_LA for all of LATAM
        $locale = (substr($locale, 0, 3) == 'es_' && $locale != 'es_ES') ? 'es_LA' : $locale;

        $body = $joomla->getBody();

        if (file_exists($versionC)) {
            if ($comParamsTwo->get('send_mail') && $comParamsTwo->get('recipient')) {
                $fbComments = "	
                FB.Event.subscribe('comment.create', function(response) {
                    new Request({
                            method: 'post',
                            url:'" . $mailUrl . "',
                            data: 'cwfbcomments=notify&href=' + response.href
                    }).send();
                });";
            }
        }

        $html = "\n" . '<!-- CoalaWeb Facebook JS -->'
            . "\n" . '<div id="fb-root"></div>
        <script>      
            window.fbAsyncInit = function() {
            FB.init({
              appId      : "' . $appId . '",
              xfbml      : true,
              status     : true,
              cookie     : true,
              version    : "v2.6"
            });
            ' . $fbComments . '
          };
          (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "//connect.facebook.net/' . $locale . '/' . $sdkType . '.js";
             fjs.parentNode.insertBefore(js, fjs);
           }(document, "script", "facebook-jssdk"));
        </script>';

        //Should we load the Facebook code?
        $facebookJs = $helpFunc::getCounts($url, 'facebook_js');

        if ($facebookJs > 0) {
            $matches = preg_split('/(<body.*?>)/i', $body, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

            /* assemble the HTML output back with the code in it */
            $injectedHTML = $matches[0] . $matches[1] . $html . $matches[2];

            $joomla->setBody($injectedHTML);
        }

        return;
    }

    /**
     * Check extension dependencies are available
     *
     * @return boolean
     */
    public function checkDependencies()
    {
        $checkOk = false;
        $minVersion = '0.1.5';

        // Load the version.php file for the CW Gears plugin
        $version_php = JPATH_SITE . '/plugins/system/cwgears/version.php';
        if (!defined('PLG_CWGEARS_VERSION') && JFile::exists($version_php)) {
            include_once $version_php;
        }

        // Check CW Gears plugin is installed and the right version otherwise tell the user that it's needed
        $loadcount_php = JPATH_SITE . '/plugins/system/cwgears/helpers/loadcount.php';
        if (
            JPluginHelper::isEnabled('system', 'cwgears', true) == true &&
            JFile::exists($version_php) &&
            version_compare(PLG_CWGEARS_VERSION, $minVersion, 'ge') &&
            JFile::exists($loadcount_php)
        ) {

            if (!class_exists('CwGearsHelperLoadcount')) {
                JLoader::register('CwGearsHelperLoadcount', $loadcount_php);
            }

            $checkOk = true;
        }

        return $checkOk;
    }

}
