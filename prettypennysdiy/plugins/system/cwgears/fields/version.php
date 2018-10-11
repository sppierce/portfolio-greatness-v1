<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Version Element
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
require_once (JPATH_SITE . '/plugins/system/cwgears/fields/base.php');

$latestVersion = JPATH_SITE . '/plugins/system/cwgears/helpers/latestversion.php';
if (JFile::exists($latestVersion) && !class_exists('CwGearsLatestversion')) {
    JLoader::register('CwGearsLatestversion', $latestVersion);
}

class CWElementVersion extends CWElement {

    public function fetchElement($name, $value, &$node, $control_name) {
        return NULL;
    }

    public function fetchTooltip($label, $description, &$node, $control_name, $name) {

        // Load version.php
        jimport('joomla.filesystem.file');
        $arr = explode("_", $label);

        //initiate variables
        $version_php = '';
        $current = '';
        $version = '';
        $ispro = '';
        $date = '';

        switch ($arr[0]) {
            case "com":
                $version_php = JPATH_ADMINISTRATOR . '/' . 'components/' . $label . '/version.php';
                break;
            case "mod":
                $version_php = JPATH_SITE . '/' . 'modules/' . $label . '/version.php';
                break;
            case "plg":
                $version_php = JPATH_SITE . '/' . 'plugins/' . $arr[1] . '/' . $arr[2] . '/version.php';
                break;
        }
                
        if (JFile::exists($version_php)) {
            require_once $version_php;
        }

        //Which extension is being displayed?
        switch ($label) {
            case "com_coalawebcontact":
                $version = (COM_CWCONTACT_VERSION);
                $date = (COM_CWCONTACT_DATE);
                $ispro = (COM_CWCONTACT_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-contact-'. $type, $version );
                }
                break;
            case "com_coalawebsociallinks":
                $version = (COM_CWSOCIALLINKS_VERSION);
                $date = (COM_CWSOCIALLINKS_DATE);
                $ispro = (COM_CWSOCIALLINKS_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-sociallinks-'. $type, $version );
                }
                break;
            case "com_coalawebcomments":
                $version = (COM_CWCOMMENTS_VERSION);
                $date = (COM_CWCOMMENTS_DATE);
                $ispro = (COM_CWCOMMENTS_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-comments-'. $type, $version );
                }
                break;
            case "com_coalawebtraffic":
                $version = (COM_CWTRAFFIC_VERSION);
                $date = (COM_CWTRAFFIC_DATE);
                $ispro = (COM_CWTRAFFIC_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-traffic-'. $type, $version );
                }
                break;
            case "com_coalawebmembers":
                $version = (COM_CWMEMBERS_VERSION);
                $date = (COM_CWMEMBERS_DATE);
                $ispro = (COM_CWMEMBERS_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-members-'. $type, $version );
                }
                break;
            case "com_coalawebmarket":
                $version = (COM_CWMARKET_VERSION);
                $date = (COM_CWMARKET_DATE);
                $ispro = (COM_CWMARKET_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-market-'. $type, $version );
                }
                break;
            case "mod_coalawebpanel":
                $version = (MOD_CWPANEL_VERSION);
                $date = (MOD_CWPANEL_DATE);
                $ispro = (MOD_CWPANEL_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-panel-'. $type, $version );
                }
                break;
            case "mod_coalawebnews":
                $version = (MOD_CWNEWS_VERSION);
                $date = (MOD_CWNEWS_DATE);
                $ispro = (MOD_CWNEWS_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-news-'. $type, $version );
                }
                break;
            case "mod_coalawebflair":
                $version = (MOD_CWFLAIR_VERSION);
                $date = (MOD_CWFLAIR_DATE);
                $ispro = (MOD_CWFLAIR_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-flair-'. $type, $version );
                }
                break;
            case "mod_coalawebhours":
                $version = (MOD_CWHOURS_VERSION);
                $date = (MOD_CWHOURS_DATE);
                $ispro = (MOD_CWHOURS_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-hours-'. $type, $version );
                }
                break;
            case "plg_system_cwgears":
                $version = (PLG_CWGEARS_VERSION);
                $date = (PLG_CWGEARS_DATE);
                $ispro = (PLG_CWGEARS_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-gears-'. $type, $version );
                }
                break;
            case "plg_system_cwfacebookjs":
                $version = (PLG_CWFACEBOOKJS_VERSION);
                $date = (PLG_CWFACEBOOKJS_DATE);
                $ispro = (PLG_CWFACEBOOKJS_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-facebookjs-'. $type, $version );
                }
                break;
            case "plg_content_cwgithub":
                $version = (PLG_CWGITHUB_VERSION);
                $date = (PLG_CWGITHUB_DATE);
                $ispro = (PLG_CWGITHUB_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-github-'. $type, $version );
                }
                break;
            case "plg_content_cwversions":
                $version = (PLG_CWVERSIONS_VERSION);
                $date = (PLG_CWVERSIONS_DATE);
                $ispro = (PLG_CWVERSIONS_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-versions-'. $type, $version );
                }
                break;
            case "plg_content_cwmarkdown":
                $version = (PLG_CWMARKDOWN_VERSION);
                $date = (PLG_CWMARKDOWN_DATE);
                $ispro = (PLG_CWMARKDOWN_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-markdown-'. $type, $version );
                }
                break;
            case "plg_system_cwprint":
                $version = (PLG_CWPRINT_VERSION);
                $date = (PLG_CWPRINT_DATE);
                $ispro = (PLG_CWPRINT_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-print-'. $type, $version );
                }
                break;
            case "plg_user_cwusers":
                $version = (PLG_CWUSERS_VERSION);
                $date = (PLG_CWUSERS_DATE);
                $ispro = (PLG_CWUSERS_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-users-'. $type, $version );
                }
                break;
            case "plg_content_coalawebdate":
                $version = (PLG_CWDATE_VERSION);
                $date = (PLG_CWDATE_DATE);
                $ispro = (PLG_CWDATE_PRO);
                $type = $ispro ? 'pro' : 'core';
                if (class_exists('CwGearsLatestversion')) {
                    $current = CwGearsLatestversion::getCurrent('cw-date-'. $type, $version );
                }
                break;
        }

        if ($ispro == 1) {
            $ispro = JText::_('PLG_CWGEARS_RELEASE_TYPE_PRO');
        } else {
            $ispro = JText::_('PLG_CWGEARS_RELEASE_TYPE_CORE');
        }
        
        //No current use default
        if (!$current) {
            $current = [
                'remote' => JText::_('PLG_CWGEARS_NO_FILE'),
                'update' => ''
            ];
        }

        return '<div class="cw-message-block">'
            . '<div class="cw-module">'
            . '<h3>' . JText::_('PLG_CWGEARS_RELEASE_TITLE') . '</h3>'
            . '<ul class="cw_module">'
            . '<li>' . JText::_('PLG_CWGEARS_FIELD_RELEASE_TYPE_LABEL') . ' <strong>' . $ispro . '</strong></li>'
            . '<li>' . JText::_('PLG_CWGEARS_FIELD_RELEASE_VERSION_LABEL') . ' <strong>' . $version . '</strong></li>'
            . '<li>' . JText::_('PLG_CWGEARS_FIELD_RELEASE_DATE_LABEL') . ' <strong>' . $date . '</strong></li>'
            . '</ul>'
            . '<h3>' . JText::_('PLG_CWGEARS_LATEST_RELEASE_TITLE') . '</h3>'
            . '<ul class="cw_module">'
            . '<li>' . JText::_('PLG_CWGEARS_FIELD_RELEASE_VERSION_LABEL') . ' <strong>' . $current['remote'] . '</strong> ' . $current['update'] . '</li>'
            . '</ul>'
            . '</div></div>';
    }

}

class JFormFieldVersion extends CWElementVersion {

    var $type = 'version';

}
