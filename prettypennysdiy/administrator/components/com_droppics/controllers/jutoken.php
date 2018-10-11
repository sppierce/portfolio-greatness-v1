<?php

// no direct access
defined('_JEXEC') or die;
jimport( 'joomla.filesystem.folder' );

JLoader::register('JuupdaterHelper', JPATH_SITE.'/plugins/installer/juupdater/helper.php');
class DroppicsControllerJutoken extends JControllerForm{
 
    public function ju_add_token() {
        JuupdaterHelper::ju_add_token();
    }
    
    public function ju_remove_token() {
        JuupdaterHelper::ju_remove_token();
    }
  
    private function exit_status($status,$datas=array()){
        JuupdaterHelper::exit_status($status,$datas=array());
    }
    
    function check_config_token(){
        return JuupdaterHelper::check_config_token();
    }
    
    function ju_update_config_token($token){
        JuupdaterHelper::ju_update_config_token($token);
    }
    
    function ju_update_site_token($token){
        JuupdaterHelper::ju_update_site_token($token);
    }
}