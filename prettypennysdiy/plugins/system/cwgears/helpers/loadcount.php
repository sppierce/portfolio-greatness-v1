<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Gears
 * @author              Steven Palmer
 * @author url          https://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /files/en-GB.license.txt
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

jimport('joomla.log.log');

/**
 *  component helper.
 */
class CwGearsHelperLoadcount {

    /**
     * Get counts
     */
    public static function getCounts($url, $check) {

        //Should we log SQL errors?
        $plugin = JPluginHelper::getPlugin('system', 'cwgears');
        $logsql = '';
        // Check if plugin is enabled
        if ($plugin) {
            $pluginParams = new JRegistry($plugin->params);
            $logsql = $pluginParams->get('log_sql', 1);
        }
        if ($logsql) {
            //Start our log file code
            JLog::addLogger(array('text_file' => 'coalaweb_gears_sql.log.php'), JLog::ERROR, 'coalaweb_gears_sql');
        }

        //Start our database queries
        $db = JFactory::getDbo();

        //Now lets check the count
        $query = $db->getQuery(true);
        $query->select($check);
        $query->from($db->quoteName('#__cwgears'));
        $query->where('url = ' . $db->quote($url));
        $db->setQuery($query);

        //Get the result
        try {
            $count = $db->loadResult();
        } catch (Exception $exc) {
            $count = '';
            if ($logsql) {
                //Log error
                $msg = JText::sprintf('PLG_CWGEARS_DATABASE_ERROR', $exc->getMessage());
                JLog::add($msg, JLog::ERROR, 'coalaweb_gears_sql');
            }
        }

        // Lastly send back the count
        return $count;
    }

    /**
     * Get counts
     */
    public static function setUikitCount($url) {

        //Should we log SQL errors?
        $plugin = JPluginHelper::getPlugin('system', 'cwgears');
        $logsql = '';
        // Check if plugin is enabled
        if ($plugin) {
            $pluginParams = new JRegistry($plugin->params);
            $logsql = $pluginParams->get('log_sql', 1);
        }
        if ($logsql) {
            //Start our log file code
            JLog::addLogger(array('text_file' => 'coalaweb_gears_sql.log.php'), JLog::ERROR, 'coalaweb_gears_sql');
        }

        //Start our database queries
        $db = JFactory::getDbo();
        //Current date time
        $siteOffset = JFactory::getApplication()->getCfg('offset');
        $dtnow = JFactory::getDate('now', $siteOffset);
        $now = $dtnow->toUnix(true);

        // Check if page already exists then update else insert
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->quoteName('#__cwgears'));
        $query->where('url = ' . $db->quote($url));
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

        if ($current) {// we are updating
            $query = $db->getQuery(true);

            $fields = array(
                $db->quoteName('uikit') . ' = ' . $db->quote('1'),
                $db->quoteName('time') . ' = ' . $db->quote($now)
            );

            $conditions = array(
                $db->quoteName('url') . ' = ' . $db->quote($url)
            );

            $query->update($db->quoteName('#__cwgears'))->set($fields)->where($conditions);

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
            
        } else {//we are inserting
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('#__cwgears'));
            $query->columns(
                    'url, uikit, time');
            $query->values(
                    $db->quote($url) . ','
                    . $db->quote('1') . ','
                    . $db->quote($now));

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

        return $query;
    }

    public static function setUikitPlusCount($url) {

        //Should we log SQL errors?
        $plugin = JPluginHelper::getPlugin('system', 'cwgears');
        $logsql = '';
        // Check if plugin is enabled
        if ($plugin) {
            $pluginParams = new JRegistry($plugin->params);
            $logsql = $pluginParams->get('log_sql', 1);
        }
        if ($logsql) {
            //Start our log file code
            JLog::addLogger(array('text_file' => 'coalaweb_gears_sql.log.php'), JLog::ERROR, 'coalaweb_gears_sql');
        }

        //Start our database queries
        $db = JFactory::getDbo();
        //Current date time
        $siteOffset = JFactory::getApplication()->getCfg('offset');
        $dtnow = JFactory::getDate('now', $siteOffset);
        $now = $dtnow->toUnix(true);

        // Check if page already exists then update else insert
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->quoteName('#__cwgears'));
        $query->where('url = ' . $db->quote($url));
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

        if ($current) {// we are updating
            $query = $db->getQuery(true);

            $fields = array(
                $db->quoteName('uikit_plus') . ' = ' . $db->quote('1'),
                $db->quoteName('time') . ' = ' . $db->quote($now)
            );

            $conditions = array(
                $db->quoteName('url') . ' = ' . $db->quote($url)
            );

            $query->update($db->quoteName('#__cwgears'))->set($fields)->where($conditions);

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
            
        } else {//we are inserting
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('#__cwgears'));
            $query->columns(
                    'url, uikit_plus, time');
            $query->values(
                    $db->quote($url) . ','
                    . $db->quote('1') . ','
                    . $db->quote($now));

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

        return $query;
    }

    /**
     * Get counts
     */
    public static function setFacebookJsCount($url) {

        //Should we log SQL errors?
        $plugin = JPluginHelper::getPlugin('system', 'cwgears');
        $logsql = '';
        // Check if plugin is enabled
        if ($plugin) {
            $pluginParams = new JRegistry($plugin->params);
            $logsql = $pluginParams->get('log_sql', '');
        }
        if ($logsql) {
            //Start our log file code
            JLog::addLogger(array('text_file' => 'coalaweb_gears_sql.log.php'), JLog::ERROR, 'coalaweb_gears_sql');
        }

        //Start our database queries
        $db = JFactory::getDbo();
        //Current date time
        $siteOffset = JFactory::getApplication()->getCfg('offset');
        $dtnow = JFactory::getDate('now', $siteOffset);
        $now = $dtnow->toUnix(true);

        // Check if page already exists then update else insert
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->quoteName('#__cwgears'));
        $query->where('url = ' . $db->quote($url));
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

        if ($current) {// we are updating
            $query = $db->getQuery(true);

            $fields = array(
                $db->quoteName('facebook_js') . ' = ' . $db->quote('1'),
                $db->quoteName('time') . ' = ' . $db->quote($now)
            );

            $conditions = array(
                $db->quoteName('url') . ' = ' . $db->quote($url)
            );

            $query->update($db->quoteName('#__cwgears'))->set($fields)->where($conditions);

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
            
        } else {//we are inserting
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('#__cwgears'));
            $query->columns(
                    'url, facebook_js, time ');
            $query->values(
                    $db->quote($url) . ','
                    . $db->quote('1') . ','
                    . $db->quote($now));

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

        return $query;
    }

}
