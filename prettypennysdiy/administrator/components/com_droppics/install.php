<?php
/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;


class com_droppicsInstallerScript{
            
        public function __construct() {
            $this->oldRelease = $this->getVersion('com_droppics');
        }
    
        /**
         * method to install the component
         *
         * @return void
         */
        static function install($parent) 
        {
            $lang = JFactory::getLanguage();
            $lang->load('com_droppics.sys',JPATH_BASE.'/components/com_droppics',null,true);
            $dbo = JFactory::getDbo();

            $query = "CREATE TABLE IF NOT EXISTS `#__droppics` (
                        `id` int(11) NOT NULL,
                        `old_id` int(11) NOT NULL,
                        `theme` varchar(30) NOT NULL,
                        `params` text NOT NULL,
                        PRIMARY KEY (`id`)
                        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
            $dbo->setQuery($query);
            if(!$dbo->query()){}

            $query = "CREATE TABLE IF NOT EXISTS `#__droppics_pictures` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `id_gallery` int(11) NOT NULL,
                    `file` varchar(100) NOT NULL,
                    `position` int(11) NOT NULL,
                    `title` varchar(200) NOT NULL,
                    `alt` varchar(200) NOT NULL,
                    `params` text NOT NULL,
					`upload_date` datetime NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `id_gallery` (`id_gallery`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ";
            $dbo->setQuery($query);
            if(!$dbo->query()){}
            
            $query = 'CREATE TABLE IF NOT EXISTS `#__droppics_custom` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `id_picture` int(11) NOT NULL,
                        `file` varchar(255) NOT NULL,
						`title` varchar(255) NOT NULL,
                        `width` int(11) NOT NULL,
                        `height` int(11) NOT NULL,
                         PRIMARY KEY (`id`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;';
            $dbo->setQuery($query);
            if(!$dbo->query()){}
            
            $query = 'SELECT * FROM #__droppics';
            $dbo->setQuery($query);
            if(!$dbo->query() ){}
            
            $basePath = JPATH_ADMINISTRATOR . '/components/com_categories';
            require_once $basePath . '/models/category.php';
            $config = array( 'table_path' => $basePath . '/tables','name'=>'droppicscats');
            $catmodel = new CategoriesModelCategory( $config);
            $catData = array( 'id' => 0, 'parent_id' => 1, 'level' => 1, 'extension' => 'com_droppics'
            , 'title' => JText::_('COM_DROPPICS_INSTALLER_NEW_GALLERY'), 'alias' => 'new-gallery',  'published' => 1, 'language' => '*');
            $status = $catmodel->save( $catData);

            $newcat = (int)$catmodel->getState('droppicscats.id');
            $query = 'INSERT INTO `#__droppics` (id,theme) VALUES ('.$newcat.',"default")';
            $dbo->setQuery($query);
            if(!$dbo->query()){}
            
            if(!$status) 
            {
              JError::raiseWarning(500, JText::_('Unable to create default gallery!'));
            }
            return true;
            
        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        function uninstall($parent) 
        {
             

        }
 
        /**
         * method to update the component
         *
         * @return void
         */
        function update($parent) 
        {
            $dbo = JFactory::getDbo();            
            // $parent is the class calling this method
            if(version_compare($this->oldRelease, '1.0.0','lt')){
                //Nothing to do
            }
            if(version_compare($this->oldRelease, '1.0.1','lt')){
                $query = 'ALTER IGNORE TABLE `#__droppics` ADD `params` TEXT NOT NULL';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
            }
            if(version_compare($this->oldRelease, '2.0.0','lt')){
                jimport('joomla.filesystem.folder');
                $paramsmedia = JComponentHelper::getParams('com_media');
                $COM_MEDIA_BASE = JPATH_ROOT.'/'.$paramsmedia->get('file_path', 'images');
                    
                $query = 'ALTER TABLE `#__droppics` CHANGE `id` `id` INT(11) NOT NULL';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $query = 'ALTER TABLE `#__droppics` ADD `newid` INT(11) NOT NULL AFTER `id`';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $query = 'ALTER TABLE  `#__droppics` ADD  `old_id` INT NOT NULL AFTER  `newid` ;';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $query = 'ALTER TABLE `#__droppics_pictures` ADD `newid_gallery` INT(11) NOT NULL AFTER `id_gallery`';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $basePath = JPATH_ADMINISTRATOR . '/components/com_categories';
                require_once $basePath . '/models/category.php';
                $config = array( 'table_path' => $basePath . '/tables','name'=>'droppicscats');
                $catmodel = new CategoriesModelCategory( $config);
                
                $query = 'SELECT * FROM #__droppics';
                $dbo->setQuery($query);
                if(!$dbo->query()){}

                $datas = array();
                foreach ($dbo->loadObjectList() as $category) {
                    $datas['id'] = 0;
                    $datas['extension'] = 'com_droppics';
                    $datas['title'] = $category->name;
                    $datas['alias'] =  uniqid().'-droppics';
                    $datas['parent_id'] = 1;
                    $datas['level'] = 2;
                    $datas['published'] = 1;
                    $datas['language'] = '*';
                    $catmodel->save($datas);
                    
                    $newcat = (int)$catmodel->getState('droppicscats.id');
                    $query = 'UPDATE `#__droppics` SET newid='.$newcat.', old_id='.(int)$category->id.' WHERE id='.(int)$category->id;
                    $dbo->setQuery($query);
                    if(!$dbo->query()){}
                    
                    $query = 'UPDATE `#__droppics_pictures` SET newid_gallery='.$newcat.' WHERE id_gallery='.(int)$category->id;
                    $dbo->setQuery($query);
                    if(!$dbo->query()){}
                        
                    JFolder::move($category->id, $category->id.'_'.$newcat,$COM_MEDIA_BASE.'/com_droppics/');
                }
                
                $query = 'ALTER TABLE `#__droppics` DROP `id`, DROP `name`';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $query = 'ALTER TABLE `#__droppics_pictures` DROP `id_gallery`';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $query = 'ALTER TABLE `#__droppics` CHANGE `newid` `id` INT(11) NOT NULL';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                $query = 'ALTER TABLE `#__droppics_pictures` CHANGE `newid_gallery` `id_gallery` INT(11) NOT NULL';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
                
                foreach(JFolder::folders($COM_MEDIA_BASE.'/com_droppics/','^[0-9]+_[0-9]+$') as $folder){
                    $f = explode('_',$folder);
                    JFolder::move($folder, $f[1], $COM_MEDIA_BASE.'/com_droppics/');
                }
            }
            if(version_compare($this->oldRelease, '2.0.1','lt')){                    
                $query = 'CREATE TABLE IF NOT EXISTS `#__droppics_custom` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `id_picture` int(11) NOT NULL,
                            `file` varchar(255) NOT NULL,
							`title` varchar(255) NOT NULL,
                            `width` int(11) NOT NULL,
                            `height` int(11) NOT NULL,
                             PRIMARY KEY (`id`)
                  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
            }
            if(version_compare($this->oldRelease, '2.0.1','eq')){                    
                $query = 'ALTER TABLE  `#__droppics` ADD  `old_id` INT NOT NULL AFTER  `id` ;';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
            }
            
			if(version_compare($this->oldRelease, '2.2.0','lt')){                    
                $query = 'ALTER TABLE  `#__droppics_custom` ADD  `title` varchar(255) NOT NULL AFTER  `file` ;';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
            }
            if(version_compare($this->oldRelease, '2.3.0','lt')){
                $query = 'ALTER TABLE `#__droppics_pictures` ADD `upload_date` DATETIME NULL DEFAULT NULL ';
                $dbo->setQuery($query);
                if(!$dbo->query()){}
            }
            return true;
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        function preflight($type, $parent) 
        {
            if($type=='uninstall'){

            }elseif($type=='update'){
                if(version_compare($this->oldRelease, $parent->get( 'manifest' )->version,'gt')){
                    Jerror::raiseWarning(null, 'You already have a newer version of Droppics');
                    $controller = new JController();
                    $controller->setRedirect('index.php?option=com_installer&view=install');
                    $controller->redirect();
                    return false;
                }
            }
            else{
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
                $this->release = $parent->get( 'manifest' )->version;
                $jversion = new JVersion();
                // abort if the current Joomla release is older
                if( version_compare( $jversion->getShortVersion(), '2.5.6', 'lt' ) ) {
                        Jerror::raiseWarning(null, 'Cannot install Droppics component in a Joomla release prior to 2.5.6');
                        $controller = new JController();
                        $controller->setRedirect('index.php?option=com_installer&view=install');
                        $controller->redirect();
                        return false;
                }
            }
            
            return true;
        }
 
        /**
         * method to run after an install/update/uninstall method
         *
         * @return void
         */
        function postflight($type, $parent) 
        {
            if($type=='install' || $type=='update'){
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
                $lang = JFactory::getLanguage();
                $lang->load('com_droppics.sys',JPATH_BASE.'/components/com_droppics',null,true);

                $manifest = $parent->get('manifest');
                JLoader::register('DroppicsInstallerHelper', JPATH_ADMINISTRATOR . '/components/com_droppics/helpers/installer.php');
                echo '<h2>'.JText::_('COM_DROPPICS_INSTALLER_TITLE').'</h2>';
                echo JText::_('COM_DROPPICS_INSTALLER_MSG');


                $extensions = $manifest->extensions;

                foreach($extensions->children() as $extension){
                        $folder = $extension->attributes()->folder;
                        $enable = $extension->attributes()->enable;
                        if(DroppicsInstallerHelper::install(JPATH_ADMINISTRATOR.'/components/com_droppics/extensions/'.$folder,$enable)){
    //                        JFile::delete(JPATH_ADMINISTRATOR.'/components/com_sponsorshipreward/extensions/'.$folder);
                            echo '<img style="padding: 5px 10px;" src="'. JURI::root().'/components/com_droppics/assets/images/tick.png" />'.$folder.' : '.JText::sprintf('COM_DROPPICS_INSTALLER_EXT_OK','').'<br/>';
                        }else{
                            echo '<img style="padding: 5px 10px; src="'. JURI::root().'/components/com_droppics/assets/images/exclamation.png" />'.$folder.' : '.JText::sprintf('COM_DROPPICS_INSTALLER_EXT_NOK','').'<br/>';
                        }
                }                

                //Set the default parameters
                if($type=='install'){
                    $component = JComponentHelper::getComponent('com_droppics');
                    $data['params']['thumbnail_width'] =  '180';
                    $data['params']['thumbnail_height'] =  '180';
                    $data['params']['picture_width'] =  '800';
                    $data['params']['picture_height'] =  '600';
                    $data['params']['full_width'] =  '1200';
                    $data['params']['full_height'] =  '1000';
                    $data['params']['jpg_quality'] =  '80';
                    $data['params']['showlightboxtitle'] =  '1';
                    

                    $table	= JTable::getInstance('extension');
                    // Load the previous Data
                    if (!$table->load($component->id)) {

                            return false;
                    }
                    // Bind the data.
                    if (!$table->bind($data)) {

                            return false;
                    }

                    // Check the data.
                    if (!$table->check()) {

                            return false;
                    }

                    // Store the data.
                    if (!$table->store()) {

                            return false;
                    }
                }
                echo '<p><img src="http://www.joomunited.com/images/droppics/droppics-summary.gif" alt="Droppics explanation" /></p>';
            }						
			
			 // install template
            $sourcePath = JPATH_ADMINISTRATOR . '/components/com_droppics/templates';
            if (!JFolder::exists($sourcePath. '/droppicsfrontend') )
            {
                echo 'Unable to install droppics template, missing from source ZIP file!<br />';
                $templateExtensionId = false;
            }
            else
            {
                $installer = new JInstaller;
                $result = $installer->install($sourcePath . '/droppicsfrontend');

                if (empty($result))
                {
                    echo 'Error installing droppics template<br />';
                }
            }
            
			// add a menu type
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__menu_types');
            $query->where($db->quoteName('menutype') . '=' . $db->quote('droppics'));
            $db->setQuery($query);
            $menuTypeId = $db->loadResult();
            if (empty($menuTypeId))
            {
                include_once JPATH_ADMINISTRATOR . '/components/com_menus/models/menu.php';
                $menuType = array('menutype' => 'droppics', 'title' => 'Droppics menu', 'description' => 'Droppics menu');
                $model = new MenusModelMenu();
                $saved = $model->save($menuType);
                // need to check errors and display message
                if (!$saved)
                {
                    echo 'Error creating Droppics menu type: ' . $model->getError() . '<br />';
                }
            }
			
			$query = $db->getQuery(true);
            $query->select('id')->from('#__menu')->where($db->quoteName('menutype') . '=' . $db->quote('droppics'));           
            $db->setQuery($query);
            $menuItemId = $db->loadResult();
            if (empty($menuItemId))
            {                
                $query = $db->getQuery(true);
                $query->select('id')->from('#__template_styles')->where($db->quoteName('template') . '=' . $db->quote('droppicsfrontend'));
                $templateStyleId = $db->setQuery($query)->loadResult();
                $error = $db->getErrorMsg();
                if (empty($templateStyleId) || !empty($error))
                {
                    echo 'Error reading template style: ' . $error . '<br />';
                }
                else
                {
                    // now we can create the menu item. We can't use directly Joomla item model
                    // has it has an hardcoded JPATH_COMPONENT require_once that fails if used
                    // from another extension than com_menus
                    include_once JPATH_ADMINISTRATOR . '/components/com_droppics/helpers/item.php';

                    // fetch installed Josetta extension id, as menu item needs that
                    $query = $db->getQuery(true);
                    $query->select('extension_id')->from('#__extensions')->where($db->quoteName('type') . '=' . $db->Quote('component'))
                        ->where($db->quoteName('element') . '=' . $db->Quote('com_droppics'));
                    $componentId = $db->setQuery($query)->loadResult();
                    $error = $db->getErrorMsg();
                    if (!empty($error))
                    {
                        echo 'Error reading just installed com_droppics extension id, cannot create front end menu item: ' . $error . '<br />';
                    }
                    else
                    {
                        // prepare menu item record
                        $menuItem = array('id'        => 0, 'menutype' => 'droppics', 'title' => 'Manage Images',
                            'link'      => 'index.php?option=com_droppics&view=manage', 'type' => 'component', 'component_id' => $componentId,
                            'published' => 1, 'parent_id' => 1, 'level' => 1, 'language' => '*', 'template_style_id' => $templateStyleId);
                        $model = new MenusModelItem();
                        $saved = $model->save($menuItem);
                        $menuItemId = $model->getState('item.id');
                        if (!$saved)
                        {
                            echo 'Error creating Droppics menu item: ' . $model->getError() . '<br />';
                        }
                    }


                }
            }
			
            $dbo = JFactory::getDbo();
            $tables = $dbo->getTableList();
            $app = JFactory::getApplication(); 
            $prefix = $app->getCfg('dbprefix');
            if(in_array($prefix.'joomunited_config', $tables)){
                $query = $dbo->getQuery(true);
                $query->select('*');
                $query->from('#__joomunited_config');
                $dbo->setQuery($query);

                $results = $dbo->loadObject();
                if(!empty($results)){
                    $token = $results->value;
                    if(!empty($token)){
                        $token = str_replace('token=', '', $token);
                        $com_name = $parent->get('element');
                        $script = '<script type="text/javascript">';
                        $script .= 'jQuery(document).ready(function($){';
                        $script .= "jQuery.ajax({
                                                url     :   'index.php?option=$com_name&task=jutoken.ju_add_token',
                                                method    : 'GET',
                                                dataType : 'json',
                                                data    :   {
                                                    'token': '$token',
                                                }
                                            }).done(function(response){

                                            });";
                        $script.='});';
                        $script .='</script>';
                        echo $script;
                    }
                }
            }
            
            return true;
        }
        
        /** Own functions **/
        
        
        /**
        * Method to get the version of a component
        * @param string $option
        * @return null
        */
        private function getVersion($option){
                $manifest = self::getManifest($option);
                if(property_exists($manifest, 'version')){
                         return $manifest->version;
                }
                return null;
        }

        /**
        * Method to get an object containing the manifest values
        * @param string $option
        * @return object
        */
        private function getManifest($option){
//                $component = JComponentHelper::getComponent($option);
                $dbo = JFactory::getDbo();
                $query = 'SELECT extension_id FROM #__extensions WHERE element='.$dbo->quote($option).' AND type="component"';
                if(!$dbo->setQuery($query)){
                    return false;
                }
                if(!$dbo->query()){
                    return false;
                }
                $component = $dbo->loadResult();
                if(!$component){
                    return false;
                }
                $table	= JTable::getInstance('extension');
                // Load the previous Data
                if (!$table->load($component,false)) {
                         return false;
                }
                return json_decode($table->manifest_cache);
        }
}