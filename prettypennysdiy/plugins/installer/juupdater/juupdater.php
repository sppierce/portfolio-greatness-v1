<?php
/**
 * @package     Juupdater.Plugin
 * @subpackage  Installer.juupdater
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
//use Joomla\Registry\Registry;
jimport('joomla.plugin.plugin');
/**
 * Juupdater Installer plugin
 *
 * @since  1.5
 */

class PlgInstallerJuupdater extends JPlugin{
    function onInstallerBeforePackageDownload($url, $headers){
        if(strpos($url, 'infosite=joomunited')){
            $url_checktoken = str_replace('task=download.download', 'task=download.checktoken', $url);
            $app = JFactory::getApplication();

            $http = JHttpFactory::getHttp();
            $response = $http->get($url_checktoken);
            $res_body = json_decode($response->body);
            if($res_body->status == false){
                if($res_body->linkdownload != ''){
                    JError::raiseError('', $res_body->linkdownload);
                }else{
                    JError::raiseError('', $res_body->datas);
                }
                $app->redirect(JUri::base().'index.php?option=com_installer&view=update');
            }
        }
    }
}
