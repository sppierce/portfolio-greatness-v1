<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Gears
 * @author              Steven Palmer
 * @author url          https://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Gears is free software: you can redistribute it and/or modify
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
jimport('joomla.environment.browser');
jimport('joomla.filesystem.file');
jimport('joomla.application.module.helper');
jimport('joomla.log.log');

class plgSystemCwgears extends JPlugin {

    var $pinterest;
    var $share;
    private $caching = 0;

    function __construct(&$subject, $config) {
        parent::__construct($subject, $config);

        // load the CoalaWeb Gears language file
        $lang = JFactory::getLanguage();
        if ($lang->getTag() != 'en-GB') {
            // Loads English language file as fallback (for undefined stuff in other language files)
            $lang->load('plg_system_cwgears', JPATH_ADMINISTRATOR, 'en-GB');
        }
        $lang->load('plg_system_cwgears', JPATH_ADMINISTRATOR, null, 1);
    }

    public function onAfterInitialise() {
        $app = JFactory::getApplication();

        if ($app->getName() !== 'site') {
            return;
        }
        
        //Lets keep our resource loading table nice and up to date
        $dbClean = $this->params->get('db_clean', '1');
        $db = JFactory::getDbo();

        $logsql = $this->params->get('log_sql', '');

        if ($logsql) {
            //Start our log file code
            JLog::addLogger(array('text_file' => 'coalaweb_gears_sql.log.php'), JLog::ERROR, 'coalaweb_gears_sql');
        }

        if ($dbClean) {
            //Current date time
            $siteOffset = $app->getCfg('offset');
            $dtnow = JFactory::getDate('now', $siteOffset);
            $now = $dtnow->toUnix(true);

            //How long should an URL be kept for.
            $locktime = $this->params->get('locktime', 60) * 60;

            //Check schedual table
            $query = $db->getQuery(true);
            $query->select('count(*)');
            $query->from($db->quoteName('#__cwgears_schedule'));
            $db->setQuery($query);
            
            try {
                $current = $db->loadResult();
            } catch (Exception $e) {
                $current = '';
                if ($logsql) {
                    //Log error
                    $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $e->getMessage());
                    JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
                }
            }

            //First time? then lets insert a time
            if (empty($current)) {
                $query = $db->getQuery(true);
                $columns = array('time');
                $values = array($db->quote($now));
                $query
                        ->insert($db->quoteName('#__cwgears_schedule'))
                        ->columns($db->quoteName($columns))
                        ->values(implode(',', $values));
                $db->setQuery($query);

                try {
                    $db->execute();
                    $items = '';
                } catch (Exception $e) {
                    $items = '';
                    if ($logsql) {
                        //Log error
                        $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $e->getMessage());
                        JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
                    }
                }
            
            } else {
                //Not our first time then lets check 
                //to see if we have any clean up work to do
                $query = $db->getQuery(true);
                $query->select('count(*)');
                $query->from($db->quoteName('#__cwgears_schedule'));
                $query->where('time + ' . $db->quote($locktime) . '<' . $db->quote($now));
                $db->setQuery($query);
                
                try {
                    $items = $db->loadResult();
                } catch (Exception $e) {
                    $items = '';
                    if ($logsql) {
                        //Log error
                        $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $e->getMessage());
                        JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
                    }
                }
            }

            //If we have some old entries we should remove them
            if ($items) {

                $query = $db->getQuery(true);
                $query->from($db->quoteName('#__cwgears'));
                $query->delete();
                $query->where('time + ' . $db->quote($locktime) . '<' . $db->quote($now));
                $db->setQuery($query);
                
                try {
                    $db->execute();
                } catch (Exception $e) {
                    if ($logsql) {
                        //Log error
                        $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $e->getMessage());
                        JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
                    }
                }

                //Reset our lock time
                $query = $db->getQuery(true);
                $query->update($db->quoteName('#__cwgears_schedule'));
                $query->set('time = ' . $db->quote($now));
                $db->setQuery($query);
                
                try {
                    $db->execute();
                } catch (Exception $e) {
                    if ($logsql) {
                        //Log error
                        $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $e->getMessage());
                        JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
                    }
                }
            }
        }
        return;
    }

    public function onAfterRoute() {

        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $option = $app->input->get('option');
        $ext = $app->input->get('extension');
        $baseUrl = '../media/coalaweb/';

        //Update the Download ID
        $this->updateDownloadKey();
        $this->updateDownloadKeySub();
        
        //Lets add some style for backend extension configurations.
        if ($app->isAdmin()) {

            if ($option == 'com_categories' && ($ext == 'com_coalawebcomments' || $ext == 'com_coalawebmarket' || $ext == 'com_coalawebtraffic' || $ext == 'com_coalaweblingual' || $ext == 'com_coalawebmembers')) {
                if (version_compare(JVERSION, '3.0', '>')) {
                    $doc->addStyleSheet($baseUrl . "components/generic/css/com-coalaweb-base-v2.css");
                    $doc->addStyleSheet($baseUrl . "components/generic/css/com-coalaweb-base-j3.css");
                    $doc->addStyleSheet($baseUrl . "components/generic/css/com-coalaweb-categories.css");
                } else {
                    $doc->addStyleSheet($baseUrl . "components/generic/css/com-coalaweb-base.css");
                    $doc->addStyleSheet($baseUrl . "components/generic/css/com-coalaweb-categories.css");
                }
            }

            if (in_array($option, array('com_coalawebcontact', 'com_coalawebsociallinks', 'com_coalawebtraffic', 'com_coalawebmarket', 'com_coalawebpaypal', 'com_coalaweblingual', 'com_coalawebcomments', 'com_coalawebmembers'))) {

                if (version_compare(JVERSION, '3.0', '>')) {
                    $doc->addStyleSheet($baseUrl . "components/generic/css/com-coalaweb-base-v2.css");
                    $doc->addStyleSheet($baseUrl . "components/generic/css/com-coalaweb-base-j3.css");
                } else {
                    $doc->addStyleSheet($baseUrl . "components/generic/css/com-coalaweb-base.css");
                }
            }
        }

        //----------------------------------------------------------------------
        // Gzip Help
        //----------------------------------------------------------------------
        //Lets stop Gzip affecting Facebook and Linkedin scrapper bots.
        $gziphelp = $this->params->get('gzip_help', 1);

        //Is Gzip turned on
        $gzip = $app->get('gzip');

        if ($gziphelp && $gzip && !$app->isAdmin()) {

            // Lets check if it exists before including
            $iptools_php = JPATH_SITE . '/plugins/system/cwgears/helpers/iptools.php';
            if (JFile::exists($iptools_php)) {
                include_once $iptools_php;
            } else {
                $app->enqueueMessage(JText::_('PLG_CWGEARS_ASSET_MISSING_MESSAGE'), 'notice');
                return;
            }

            //Now get an IP for the current visitor
            $ip = CwGearsIptools::getUserIp();

            //Intiate our agent variable
            $agent = false;

            //Grab our Facebook IP array to check against
            //Last checked from https://developers.facebook.com/docs/sharing/best-practices#crawl on March 16 2015
            $ipTable = array(
                '31.13.24.0/21',
                '31.13.64.0/18',
                '66.220.144.0/20',
                '69.63.176.0/20',
                '69.171.224.0/19',
                '74.119.76.0/22',
                '103.4.96.0/22',
                '173.252.64.0/18',
                '204.15.20.0/22');

            //Use some more of our tools to check if the visitor is in the list
            if (isset($ip)) {
                $agent = CwGearsIptools::ipinList($ip, $ipTable);
            }

            //lets do a quick check against the user agent for facebook or Linkedin bots
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                /* Facebook User Agent
                 * facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)
                 * LinkedIn User Agent
                 * LinkedInBot/1.0 (compatible; Mozilla/5.0; Jakarta Commons-HttpClient/3.1 +http://www.linkedin.com)
                 */
                $pattern = strtolower('/facebookexternalhit|LinkedInBot/x');

                if (preg_match($pattern, strtolower($_SERVER['HTTP_USER_AGENT']))) {
                    $agent = true;
                }
            }

            if ($gzip && $agent) {
                $app->set('gzip', 0);
            }
        }

        //----------------------------------------------------------------------
        // Cache Control
        //----------------------------------------------------------------------
        //Let stop Joomla cache from affecting specific parts of the website.
        //Inspired by Crosstec
        $loadCacheControl = $this->params->get('cache_off', 0);
        if ($loadCacheControl && $this->checkRules() && !$app->isAdmin()) {
            $this->caching = JFactory::getConfig()->get('caching');
            JFactory::getConfig()->set('caching', 0);
        }
    }

    public function onBeforeCompileHead() {

        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();

        //----------------------------------------------------------------------
        //Jquery Loading
        //----------------------------------------------------------------------
        $loadJquery = $this->params->get('jquery_on', 0);
        if ($loadJquery && !$app->isAdmin()) {

            // Let create a link to our local directory.
            $localURL = JURI::root(true) . "/media/coalaweb/plugins/system/gears/js";

            // Lets choose the location we want to use.
            switch ($this->params->get("jquery_server")) {

                case 1: // code.jquery.com
                    $url = "//code.jquery.com/jquery-" . $this->params->get("jquery_library") . ".min.js";
                    break;

                case 2: // ajax.googleapis.com
                    $url = "//ajax.googleapis.com/ajax/libs/jquery/" . $this->params->get("jquery_library") . "/jquery.min.js";
                    break;

                case 3: // ajax.aspnetcdn.com
                    $url = "//ajax.aspnetcdn.com/ajax/jQuery/jquery-" . $this->params->get("jquery_library") . ".min.js";
                    break;

                case 4: // cdnjs.cloudflare.com
                    $url = "//cdnjs.cloudflare.com/ajax/libs/jquery/" . $this->params->get("jquery_library") . "/jquery.min.js";
                    break;

                default: // Localhost
                    $url = $localURL . "/jquery-" . $this->params->get("jquery_library") . ".min.js";
                    break;
            }

            if ($this->params->get("jquery_noconflict")) {
                JHtml::_('behavior.framework');
            }

            $doc->addScript($url);

            if ($this->params->get("jquery_noconflict")) {
                $doc->addScript($localURL . "/jquery-noconflict.js");
            }

            if ($this->params->get("jquery_migrate")) {
                $doc->addScript($localURL . "/jquery-migrate-1.2.1.min.js");
            }

            // Order scripts
            $headData = $doc->getHeadData();

            $allowedJQuery = array("jquery.min.js", "jquery-" . $this->params->get("jquery_library") . ".min.js", "jquery-noconflict.js", "jquery-migrate-1.2.1.min.js");

            $first = array();
            $jquery = array();
            foreach ($headData["scripts"] as $key => $value) {

                if ((false !== strpos($key, "mootools-core-uncompressed.js")) OR ( false !== strpos($key, "mootools-core.js"))) {
                    $first[$key] = $value;
                    unset($headData["scripts"][$key]);
                }

                if ((false !== strpos($key, "mootools-more-uncompressed.js")) OR ( false !== strpos($key, "mootools-more.js"))) {
                    $first[$key] = $value;
                    unset($headData["scripts"][$key]);
                }

                if (false !== strpos($key, "jquery")) {
                    $baseName = basename($key);

                    // Order only jQuery library and no conflict script
                    if (in_array($baseName, $allowedJQuery)) {
                        $jquery[$key] = $value;
                    }
                }
            }

            $jquery = $this->orderLibrarires($jquery);
            $first = array_merge($first, $jquery);

            $second = $headData["scripts"];
            $headData["scripts"] = array_merge($first, $second);

            $doc->setHeadData($headData);

            unset($first);
            unset($second);
            unset($scripts);
            unset($headData);
        }

        //----------------------------------------------------------------------
        // Uikit
        //----------------------------------------------------------------------

        $uikitAdd = $this->params->get('uikit_add', 1);
        $uikitTheme = $this->params->get('uikit_theme', 'flat');
        $url = JURI::getInstance()->toString();
        
        if ($app->getName() === 'site' && $doc->getType() === 'html') {
            
            // Lets check if it exists before including
            $loadcount_php = JPATH_SITE . '/plugins/system/cwgears/helpers/loadcount.php';
            if (JFile::exists($loadcount_php)) {
                include_once $loadcount_php;
            } else {
                $app->enqueueMessage(JText::_('PLG_CWGEARS_ASSET_MISSING_MESSAGE'), 'notice');
                return;
            }
            
            $helpFunc = new CwGearsHelperLoadcount();
            $newCount = $helpFunc::getCounts($url, 'uikit');

            if ($newCount) {
                $uikitCount = $helpFunc::getCounts($url, 'uikit');
            } elseif ($app->get('CWUikitCount', 0) > 0) {
                $uikitCount = $app->get('CWUikitCount', 0);
            } else {
                $uikitCount = 0;
            }
            $uikitPlus = $helpFunc::getCounts($url, 'uikit_plus');

            $uikitLocal = JURI::root(true) . "/media/coalaweb/plugins/system/gears/uikit/";


            if ($uikitCount > 0 && $uikitAdd) {
                switch ($uikitTheme) {
                    case "default":
                        $uikitCss = 'css/coalaweb.uikit.min.css';
                        break;
                    case "flat":
                        $uikitCss = 'css/coalaweb.uikit.almost-flat.min.css';
                        break;
                    case "gradient":
                        $uikitCss = 'css/coalaweb.uikit.gradient.min.css';
                        break;
                    default:
                        $uikitCss = 'css/coalaweb.uikit.min.css';
                }

                //Define our custom uikit prefix for the JavaScript
                $uikitPre = "var myUIkit = UIkit.noConflict('cw');";

                //Add all the stuff we need
                $doc->addScriptDeclaration($uikitPre);
                $doc->addScript($uikitLocal . "js/coalaweb.uikit.min.js");
                $doc->addStyleSheet($uikitLocal . $uikitCss);
            }

            if ($uikitCount > 0 && $uikitPlus > 0 && $uikitAdd) {
                switch ($uikitTheme) {
                    case "default":
                        //adds slider naviagtion
                        $uikitSlidenav= 'css/components/coalaweb.slidenav.min.css';
                        //adds sticky
                        $uikitSticky = 'css/components/coalaweb.sticky.min.css';
                        //adds slider naviagtion
                        $uikitSlider= 'css/components/coalaweb.slider.min.css';
                        //adds datepicker
                        $uikitDate = 'css/components/coalaweb.datepicker.min.css';
                        //adds form select needed for other components
                        $uikitSelect = 'css/components/coalaweb.form-select.min.css';
                        //adds tooltips
                        $uikitTooltip = 'css/components/coalaweb.tooltip.min.css';
                        break;
                    case "flat":
                        //adds slider naviagtion
                        $uikitSlidenav= 'css/components/coalaweb.slidenav.almost-flat.min.css';
                        //adds sticky
                        $uikitSticky = 'css/components/coalaweb.sticky.almost-flat.min.css';
                        //adds slider naviagtion
                        $uikitSlider = 'css/components/coalaweb.slider.almost-flat.min.css';
                        //adds datepicker
                        $uikitDate = 'css/components/coalaweb.datepicker.almost-flat.min.css';
                        //adds form select needed for other components
                        $uikitSelect = 'css/components/coalaweb.form-select.almost-flat.min.css';
                        //adds tooltips
                        $uikitTooltip = 'css/components/coalaweb.tooltip.almost-flat.min.css';
                        break;
                    case "gradient":
                        //adds slider naviagtion
                        $uikitSlidenav= 'css/components/coalaweb.slidenav.gradient.min.css';
                        //adds sticky
                        $uikitSticky = 'css/components/coalaweb.sticky.gradient.min.css';
                        //adds slide
                        $uikitSlider = 'css/components/coalaweb.slider.gradient.min.css';
                        //adds datepicker
                        $uikitDate = 'css/components/coalaweb.datepicker.gradient.min.css';
                        //adds form select needed for other components
                        $uikitSelect = 'css/components/coalaweb.form-select.gradient.min.css';
                        //adds tooltips
                        $uikitTooltip = 'css/components/coalaweb.tooltip.gradient.min.css';
                        break;
                    default:
                        //adds slider naviagtion
                        $uikitSlidenav= 'css/components/coalaweb.slidenav.min.css';
                        //adds sticky
                        $uikitSticky = 'css/components/coalaweb.sticky.min.css';
                        //adds slider naviagtion
                        $uikitSlider= 'css/components/coalaweb.slider.min.css';
                        //adds datepicker
                        $uikitDate = 'css/components/coalaweb.datepicker.min.css';
                        //adds form select needed for other components
                        $uikitSelect = 'css/components/coalaweb.form-select.min.css';
                        //adds tooltips
                        $uikitTooltip = 'css/components/coalaweb.tooltip.min.css';
                }

                //lightbox support
                $doc->addScript($uikitLocal . "js/components/coalaweb.lightbox.min.js");
                //Sticky support
                $doc->addScript($uikitLocal . "js/components/coalaweb.sticky.min.js");
                //Grid support
                $doc->addScript($uikitLocal . "js/components/coalaweb.grid.min.js");
                //Slideset support
                $doc->addScript($uikitLocal . "js/components/coalaweb.slideset.min.js");
                //Slideset support
                $doc->addScript($uikitLocal . "js/components/coalaweb.slider.min.js");
                //Date picker support
                $doc->addScript($uikitLocal . 'js/components/coalaweb.datepicker.min.js');
                //Tooltip support
                $doc->addScript($uikitLocal . 'js/components/coalaweb.tooltip.min.js');
                //Form select support needed for other components
                $doc->addScript($uikitLocal . 'js/components/coalaweb.form-select.min.js');

                //Add CSS
                $doc->addStyleSheet($uikitLocal . $uikitSlider);
                $doc->addStyleSheet($uikitLocal . $uikitSlidenav);
                $doc->addStyleSheet($uikitLocal . $uikitSticky);
                $doc->addStyleSheet($uikitLocal . $uikitDate);
                $doc->addStyleSheet($uikitLocal . $uikitSelect);
                $doc->addStyleSheet($uikitLocal . $uikitTooltip);
            }
        }

        //----------------------------------------------------------------------
        // reCAPTCHA Size
        //----------------------------------------------------------------------

        $recapCompact = $this->params->get('recap_compact', 0);

        if ($recapCompact) {
            $headData = $doc->getHeadData();
            $recap = array();
            foreach ($headData["script"] as $key => $value) {
                if (strpos($value, 'theme: "light"') !== false) {
                    $value = str_replace('theme: "light"', 'theme: "light", size: "compact"', $value);
                    $recap["text/javascript"] = $value;
                } elseif (strpos($value, 'theme: "dark"') !== false) {
                    $value = str_replace('theme: "dark"', 'theme: "dark", size: "compact"', $value);
                    $recap["text/javascript"] = $value;
                }
            }

            $headData["script"] = $recap;
            $doc->setHeadData($headData);

            unset($recap);
            unset($headData);

            //Add a little CSS to fix size issues
            $doc->addStyleDeclaration('.recaptcha iframe{width:158px;height: 138px;border-radius:5px;}');
        }

        //----------------------------------------------------------------------
        //Custom CSS
        //----------------------------------------------------------------------

        $ccssAdd = $this->params->get('ccss_add');
        if ($ccssAdd && !$app->isAdmin() && $doc->getType() == 'html') {
            $ccssCode = $this->params->get('ccss_code');
            $ccssFile = $this->params->get('ccss_file');
            // Remove comments.
            if ($this->params->get('ccss_remove_comments')) {
                $ccssCode = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $ccssCode);
            }

            // Convert short absolute paths to full absolute paths.
            if ($this->params->get('ccss_full_paths')) {
                $ccssCode = str_replace('url(/', 'url(' . JURI::base(), $ccssCode);
                $ccssCode = str_replace("url('/", "url('" . JURI::base(), $ccssCode);
                $ccssCode = str_replace('url("/', 'url("' . JURI::base(), $ccssCode);
            }

            // Minimize.
            if ($this->params->get('ccss_minimize')) {
                $ccssCode = str_replace(array("\r\n", "\r", "\n", "\t"), '', $ccssCode);
                $ccssCode = preg_replace('/ +/', ' ', $ccssCode); // Replace multiple spaces with single space.
                $ccssCode = trim($ccssCode);  // Trim the string of leading and trailing space.
            }
            if ($ccssCode) {
                $doc->addCustomTag('<style type="text/css">' . $ccssCode . '</style>');
            }
            if ($ccssFile) {
                $doc->addStyleSheet(JURI::base(true) . $ccssFile);
            }
        }

        //----------------------------------------------------------------------
        //Custom Javascript
        //----------------------------------------------------------------------

        $cjsAdd = $this->params->get('cjs_add');
        if ($cjsAdd && !$app->isAdmin() && $doc->getType() == 'html') {
            $cjsCode = $this->params->get('cjs_code');

            // Remove comments.
            if ($this->params->get('cjs_remove_comments')) {
                $cjsCode = preg_replace('(// .+)', '', $cjsCode);
                $cjsCode = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $cjsCode);
            }

            // Minimize.
            if ($this->params->get('cjs_minimize')) {
                $cjsCode = str_replace(array("\r\n", "\r", "\n", "\t"), '', $cjsCode);
                $cjsCode = preg_replace('/ +/', ' ', $cjsCode); // Replace multiple spaces with single space.
                $cjsCode = trim($cjsCode);  // Trim the string of leading and trailing space.
            }

            $doc->addScriptDeclaration($cjsCode);
        }

        //----------------------------------------------------------------------
        //Zoo Editor Tweak
        //----------------------------------------------------------------------

        $yooEditorTweak = $this->params->get('zoo_editor_tweak');
        if ($yooEditorTweak && $app->isAdmin()) {
            $zooEditorTweak = '.creation-form textarea {width: 100%; height:400px;}';
            $doc->addCustomTag('<style type="text/css">' . $zooEditorTweak . '</style>');
        }

        //----------------------------------------------------------------------
        //Async
        //----------------------------------------------------------------------

        $defer = $this->params->get('defer');
        $async = $this->params->get('async');
        if (($defer || $async) && !$app->isAdmin() && $doc->getType() == 'html') {

            $scripts_to_handle = trim((string) $this->params->get('script_list', ''));

            // Detect language
            $lang = JFactory::getLanguage();
            $locale = $lang->getTag();
            $locale = str_replace("-", "_", $locale);

            // Facebook and Google only seem to support es_ES and es_LA for all of LATAM
            $locale = (substr($locale, 0, 3) == 'es_' && $locale != 'es_ES') ? 'es_LA' : $locale;

            if ($scripts_to_handle) {
                $paths = array_map('trim', (array) explode(",", $scripts_to_handle));
                foreach ($paths as $path) {
                    if (strpos($path, 'http') === 0) {
                        continue;
                    }

                    $withoutroot = str_replace(JURI::root(true), '', $path);
                    if ($withoutroot != $path) {
                        $paths[] = $withoutroot;
                    }
                    $withroot = JURI::root(true) . $path;
                    if ($withroot != $path) {
                        $paths[] = $withroot;
                    }
                    $withdomain = JURI::root(false) . $path;
                    if ($withdomain != $path) {
                        $paths[] = $withdomain;
                    }

                    $facebook = '//connect.facebook.net/all.js#xfbml=1';
                    if ($path === $facebook) {
                        $facebookLang = '//connect.facebook.net/' . $locale . '/all.js#xfbml=1';
                        $paths[] = $facebookLang;
                    }
                }

                foreach ($doc->_scripts as $url => $scriptparams) {
                    if (in_array($url, $paths)) {
                        if ($defer) {
                            $doc->_scripts[$url]['defer'] = true;
                        }
                        if ($async) {
                            $doc->_scripts[$url]['async'] = true;
                        }
                    }
                }
            }

            return true;
        }
    }

    /**
     * Order jQuery libraries in valid order
     * @param array $libs
     */
    private function orderLibrarires($libs) {

        $strings = array("code.jquery.com", "ajax.googleapis.com", "ajax.aspnetcdn.com", "cdnjs.cloudflare.com", "cwjquery");

        $first = array();
        foreach ($libs as $key => $value) {
            foreach ($strings as $string) {

                if (false !== strpos($key, $string)) {
                    $first[$key] = $value;
                    unset($libs[$key]);
                }
            }
        }

        $first = array_merge($first, $libs);
        return $first;
    }

    function onAfterRender() {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();

        // Only render for HTML output
        if ($doc->getType() == 'html' && $app->getName() == 'site') {

            //Lets add Pinterest JS if the Social Links module needs it.
            $module = JModuleHelper::getModule('coalawebsociallinks');
            $moduleTwo = JModuleHelper::getModule('coalawebsocialtabs');

            if ($module) {
                $modParams = new JRegistry;
                $modParams->loadString($module->params, 'JSON');
                $this->pinterest = $modParams->get('display_pinterest_bm');
                $this->share = $modParams->get('display_bm_sec');
            }

            if ($moduleTwo && ($this->pinterest == 0 || $this->share == 0)) {
                $modParamsTwo = new JRegistry;
                $modParamsTwo->loadString($moduleTwo->params, 'JSON');
                $this->pinterest = $this->share = $modParamsTwo->get('display_pinterest');
            }


            if ($this->pinterest && $this->share) {
                $body = $app->getBody();
                $pos = JString::strpos($body, "//assets.pinterest.com/js/pinit.js");
                if (!$pos) {
                    $body = JString::str_ireplace('</body>', '<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>' . "\n</body>", $body);
                    $app->setBody($body);
                } else {
                    return;
                }
            }
        }
    }

    function onBeforeRender() {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $loadMsg = $this->params->get('sys_msg_demo');

        // Only render for HTML output
        if ($doc->getType() == 'html' && $loadMsg) {
            $classes = JText::_('PLG_CWGEARS_SYSMSG_MSG');
            foreach (array('Message', 'Notice', 'Warning', 'Error') as $type) {
                $app->enqueueMessage($classes, $type);
            }
        }
    }

    //Lets check what shouldn't be cached.
    function checkRules() {
        $app = JFactory::getApplication();
        $plugin = JPluginHelper::getPlugin('system', 'cwgears');
        $params = new JRegistry($plugin->params);
        $loadCacheControl = $params->get('cache_off', 0);

        if ($loadCacheControl && !$app->isAdmin()) {
            $defs = str_replace("\r", "", $params->get('rules', ''));
            $defs = explode("\n", $defs);

            foreach ($defs As $def) {
                $result = $this->parseQueryString($def);
                if (is_array($result)) {
                    $found = 0;
                    $required = count($result);
                    foreach ($result As $key => $value) {
                        if ($app->input->get($key) == $value || ( $app->input->get($key, null) !== null && $value == '?' )) {
                            $found++;
                        }
                    }
                    if ($found == $required) {
                        return true;
                    }
                }
            }

            return false;
        }
    }

    function parseQueryString($str) {
        $op = array();
        $pairs = explode("&", $str);
        foreach ($pairs as $pair) {
            list($k, $v) = array_map("urldecode", explode("=", $pair));
            $op[$k] = $v;
        }
        return $op;
    }

    /**
     * Detect Google and Facebook crawlers
     * 
     * @param type $server
     * @return boolean
     */
    function crawlerDetect($server) {
        $crawlers = array(
            'Google' => 'Google',
            'Facebook' => 'facebookexternalhit',
        );

        $crawlers_agents = implode('|', $crawlers);

        if (strpos($server, $crawlers_agents) !== false) {
            return true; // Is a bot
        }


        return false; // Not a bot
    }
    
    /**
     * Add download ID to the update sites table when saving to a CoalaWeb 
     * component configuration.
     * 
     * @return null
     */
    function updateDownloadKey() {
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $component = $app->input->get('component');
        $task = $app->input->get('task');
        
        //Array of CoalaWeb extensions
        $components = array(
            'com_coalawebcontact',
            'com_coalawebsociallinks',
            'com_coalawebtraffic',
            'com_coalawebmarket',
            'com_coalawebpaypal',
            'com_coalaweblingual',
            'com_coalawebcomments'
        );
        
        // Array of tasks
        $tasks = array(
            'config.save.component.apply',
            'config.save.component.save'
        );
        
        if (
            $app->isSite() ||
            $option != 'com_config' ||
            !in_array($task, $tasks) ||
            !in_array($component, $components)
        ) {
            return;
        }

        $logsql = $this->params->get('log_sql', '');

        if ($logsql) {
            //Start our log file code
            JLog::addLogger(array('text_file' => 'coalaweb_gears_sql.log.php'), JLog::ERROR, 'coalaweb_gears_sql');
        }
        
        switch ($component) {
            case 'com_coalawebcontact':
                $updateurl = 'http://cdn.coalaweb.com/updates/cw-contact-pro.xml';
                break;
            case 'com_coalawebsociallinks':
                $updateurl = 'http://cdn.coalaweb.com/updates/cw-sociallinks-pro.xml';
                break;
            case 'com_coalawebtraffic':
                $updateurl = 'http://cdn.coalaweb.com/updates/cw-traffic-pro.xml';
                break;
            case 'com_coalawebmarket':
                $updateurl = 'http://cdn.coalaweb.com/updates/cw-market-pro.xml';
                break;
            case 'com_coalawebpaypal':
                $updateurl = 'http://cdn.coalaweb.com/updates/cw-paypal-pro.xml';
                break;
            case 'com_coalaweblingual':
                $updateurl = 'http://cdn.coalaweb.com/updates/cw-lingual-pro.xml';
                break;
            case 'com_coalawebcomments':
                $updateurl = 'http://cdn.coalaweb.com/updates/cw-comments-pro.xml';
                break;
            
            default:
                $updateurl = '';
        }

        $form = $app->input->post->get('jform', array(), 'array');
        if (!isset($form['downloadid'])) {
            return;
        }
        $dlid = $form['downloadid'];

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->update('#__update_sites')
                ->set($db->qn('extra_query') . ' = ' . $db->q(''))
                ->where($db->qn('location') . ' = ' . $db->q($updateurl));
        $db->setQuery($query);
        
        try {
            $db->execute();
        } catch (Exception $e) {
            if ($logsql) {
                //Log error
                $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
            }
        }

        $query->clear()
                ->update('#__update_sites')
                ->set($db->qn('extra_query') . ' = ' . $db->q('dlid=' . $dlid))
                ->where($db->qn('location') . ' =' . $db->q($updateurl));
        $db->setQuery($query);
        
        try {
            $db->execute();
        } catch (Exception $e) {
            if ($logsql) {
                //Log error
                $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
            }
        }
    }
    
     /**
     * Add download ID to the update sites table for CoalaWeb extensions 
     * that don't come packaged with a component.
     * 
     * @return null
     */
    function updateDownloadKeySub() {
        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $id = $app->input->get('extension_id');
        $task = $app->input->get('task');

        // Array of possible tasks
        $tasks = array(
            'plugin.apply', 
            'plugin.save'
        );

        $logsql = $this->params->get('log_sql');

        if ($logsql) {
            //Start our log file code
            JLog::addLogger(array('text_file' => 'coalaweb_gears_sql.log.php'), JLog::ERROR, 'coalaweb_gears_sql');
        }
        
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->qn('#__extensions'));
        $query->where($db->qn('element') . ' = ' . $db->q('cwgears'));
        $query->where($db->qn('extension_id') . ' = ' . $db->q($id));
        $db->setQuery($query);
        
        try {
            $iscwgears = $db->loadResult();
        } catch (Exception $e) {
            $iscwgears = '';
            if ($logsql) {
                //Log error
                $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
            }
        }
        
        if (
            $app->isSite() ||
            $option != 'com_plugins' ||
            !in_array($task, $tasks) ||
            !$iscwgears
        ) {
            return;
        }

        $news = 'http://cdn.coalaweb.com/updates/cw-news-pro.xml';
        $hours = 'http://cdn.coalaweb.com/updates/cw-hours-pro.xml';
        $users = 'http://cdn.coalaweb.com/updates/cw-users-pro.xml';
        $print = 'http://cdn.coalaweb.com/updates/cw-print-pro.xml';
        $panel = 'http://cdn.coalaweb.com/updates/cw-panel-pro.xml';
        $date = 'http://cdn.coalaweb.com/updates/cw-date-pro.xml';
        $dbtools = 'http://cdn.coalaweb.com/updates/cw-dbtools-pro.xml';

        
        $form = $app->input->post->get('jform', array(), 'array');
        if (!isset($form['params']['downloadid'])) {
            return;
        }
        $dlid = $form['params']['downloadid'];

        $query = $db->getQuery(true)
                ->update('#__update_sites')
                ->set($db->qn('extra_query') . ' = ' . $db->q(''))
                ->where($db->qn('location') . ' = ' . $db->q($news), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($hours), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($users), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($print), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($panel), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($date), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($dbtools));
        $db->setQuery($query);
        
        try {
            $db->execute();
        } catch (Exception $e) {
            if ($logsql) {
                //Log error
                $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
            }
        }

        $query->clear()
                ->update('#__update_sites')
                ->set($db->qn('extra_query') . ' = ' . $db->q('dlid=' . $dlid))
                ->where($db->qn('location') . ' = ' . $db->q($news), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($hours), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($users), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($print), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($panel), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($date), 'OR')
                ->where($db->qn('location') . ' = ' . $db->q($dbtools));
        $db->setQuery($query);
        
        try {
            $db->execute();
        } catch (Exception $e) {
            if ($logsql) {
                //Log error
                $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
            }
        }
    }
}
