<?php
/** 
 * Dropfiles
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Dropfiles
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');
jimport( 'joomla.filesystem.file' );

class DroppicsControllerConnector extends JControllerLegacy
{

    public function listdir(){
        $user = JFactory::getUser();
        if(!$user->authorise('core.admin')){
            return json_encode(array());
        }
        $params = JComponentHelper::getParams('com_droppics');
        $allowed_ext = explode(',', 'gif,jpeg,jpg,png');
        foreach ($allowed_ext as $key => $value) {
            $allowed_ext[$key] = strtolower(trim($allowed_ext[$key]));
            if($allowed_ext[$key]==''){
                unset($allowed_ext[$key]);
            }
        }
    
        $path = JPATH_ROOT.DIRECTORY_SEPARATOR. 'images' .DIRECTORY_SEPARATOR;
        $dir = JFolder::makeSafe(JRequest::getString('dir'));
        
        $return = $dirs = $fi = array();

        if( file_exists($path.$dir) ) {            
                $files = scandir($path.$dir);

                natcasesort($files);
                if( count($files) > 2 ) { // The 2 counts for . and ..
                    // All dirs
                    foreach( $files as $file ) {
                        if($file=='com_droppics')    continue ;
                            if( file_exists($path . $dir . DIRECTORY_SEPARATOR . $file) && $file != '.' && $file != '..' && is_dir($path . $dir. DIRECTORY_SEPARATOR . $file) ) {
                                    $dirs[] = array('type'=>'dir','dir'=>$dir,'file'=>$file);
                            }elseif( file_exists($path . $dir . DIRECTORY_SEPARATOR . $file) && $file != '.' && $file != '..' && !is_dir($path . $dir . DIRECTORY_SEPARATOR . $file) && in_array(JFile::getExt($file), $allowed_ext) ) {
                                    $fi[] = array('type'=>'file','dir'=>$dir,'file'=>$file,'ext'=>strtolower(JFile::getExt($file)));
                            }
                    }
                    $return = array_merge($dirs,$fi);
                }
        }
        echo json_encode( $return );
        jexit();
    }
}