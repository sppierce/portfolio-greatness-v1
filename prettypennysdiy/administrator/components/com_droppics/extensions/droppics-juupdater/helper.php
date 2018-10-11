<?php

/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barr?re (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die;

class JuupdaterHelper{
    public static function ju_add_token() {
        $token = JRequest::getVar('token');
        if(!empty($token)){
            JuupdaterHelper::ju_update_config_token('token='.$token);
            JuupdaterHelper::ju_update_site_token('token='.$token);
            JuupdaterHelper::exit_status(true,array('token'=>$token));
        }else{
            JuupdaterHelper::exit_status(false,array('token'=>''));
        }
    }
    
    public static function ju_remove_token() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('name') . ' = ' . $db->quote('ju_user_token')
        );

        $query->delete($db->quoteName('#__joomunited_config'));
        $query->where($conditions);
        $db->setQuery($query);
        $result = $db->execute();    
        
        
        // update site token
        $ju_base = 'https://www.joomunited.com';
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('extra_query') . ' = ' . $db->quote(''),
        );

        $query->update($db->quoteName('#__update_sites'))->set($fields)->where($db->quoteName('location') . ' LIKE '.$db->quote('%'.$ju_base.'%'));
        $db->setQuery($query);
        $result = $db->execute();
        
        // remove in #__updaters table
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('extra_query') . ' = ' . $db->quote(''),
        );
        $query->update($db->quoteName('#__updates'))->set($fields)->where($db->quoteName('detailsurl') . ' LIKE '.$db->quote('%'.$ju_base.'%'));
        $db->setQuery($query);
        $result = $db->execute();
        
        JuupdaterHelper::exit_status(true,array());
    }
  
    public static function exit_status($status,$datas=array()){
            $response = array('response'=>$status,'datas'=>$datas);            
            echo json_encode($response);
            JFactory::getApplication()->close();
    }
    
    public static function check_config_token(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__joomunited_config');
        $query->where('name = "ju_user_token"');
        $db->setQuery($query);
        $res = $db->loadObjectList();
        return count($res);
    }
    
    public static function ju_update_config_token($token){
        $count = JuupdaterHelper::check_config_token();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        if(empty($count)){
            $columns = array('name', 'value');
            $values = array($db->quote('ju_user_token'), $db->quote($token));
            $query
                ->insert($db->quoteName('#__joomunited_config'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));
        }else{
            $fields = array(
                $db->quoteName('value') . ' = ' . $db->quote($token),
            );
            $query->update($db->quoteName('#__joomunited_config'))->set($fields)->where("name = 'ju_user_token'");
        }
        
        $db->setQuery($query);
        $db->execute();
    }
    
    public static function ju_update_site_token($token){
        $ju_base = 'https://www.joomunited.com';
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('extra_query') . ' = '.$db->quote($token.'&siteurl='.JUri::root()),
        );

        $query->update($db->quoteName('#__update_sites'))->set($fields)->where($db->quoteName('location') . ' LIKE '.$db->quote('%'.$ju_base.'%'));
        $db->setQuery($query);
        $result = $db->execute();
        
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('extra_query') . ' = '.$db->quote($token.'&siteurl='.JUri::root()),
        );

        $query->update($db->quoteName('#__updates'))->set($fields)->where($db->quoteName('detailsurl') . ' LIKE '.$db->quote('%'.$ju_base.'%'));
        $db->setQuery($query);
        $result = $db->execute();
    }
}
