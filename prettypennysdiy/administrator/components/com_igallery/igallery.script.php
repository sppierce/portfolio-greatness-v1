<?php
defined('_JEXEC') or die('Restricted access');

class com_igalleryInstallerScript
{
    function update($parent)
    {
        define('IG_ADMIN_ROOT', JPATH_ADMINISTRATOR.'/components/com_igallery');
        define('IG_FRONTEND_ROOT', JPATH_SITE.'/components/com_igallery');

        $filesToDelete = array();
        $filesToDelete[] = IG_FRONTEND_ROOT.'/views/category/tmpl/default.php';
        $filesToDelete[] = IG_ADMIN_ROOT.'/lib/uploaders/plupload/js/jquery-1.3.2.js';
        $filesToDelete[] = IG_ADMIN_ROOT.'/install.igallery.php';
        $filesToDelete[] = IG_ADMIN_ROOT.'/uninstall.igallery.php';

        for($i=0; $i<count($filesToDelete); $i++)
        {
            if(JFile::exists($filesToDelete[$i]))
            {
                if( !JFile::delete($filesToDelete[$i]) )
                {
                    echo 'The Unused File: '.$filesToDelete[$i]. ' could not be removed,
                    please remove it manually <br />';
                }
            }
        }

        $foldersToDelete = array();
        $foldersToDelete[] = IG_ADMIN_ROOT.'/lib/uploaders/plupload/css';
        $foldersToDelete[] = IG_ADMIN_ROOT.'/lib/uploaders/plupload/img';
        $foldersToDelete[] = IG_ADMIN_ROOT.'/lib/uploaders/plupload/js';

        for($i=0; $i<count($foldersToDelete); $i++)
        {
            if(JFolder::exists($foldersToDelete[$i]))
            {
                if( !JFolder::delete($foldersToDelete[$i]) )
                {
                    echo 'The Unused Folder: '.$foldersToDelete[$i]. ' could not be removed,
                    please remove it manually <br />';
                }
            }
        }

        $iDb =& JFactory::getDBO();
        $queries = array();

        $query = 'SELECT * FROM #__igallery LIMIT 1';
        $iDb->setQuery($query);
        $category = $iDb->loadAssoc();

        if( is_array($category) )
        {
            if( !array_key_exists('moderate', $category) )
            {
                $queries[] = "ALTER TABLE `#__igallery` ADD `moderate` INT(1) NOT NULL DEFAULT '1'";
            }

            if( !array_key_exists('publish_up', $category) )
            {
                $queries[] = "ALTER TABLE `#__igallery` ADD `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
            }

            if( !array_key_exists('publish_down', $category) )
            {
                $queries[] = "ALTER TABLE `#__igallery` ADD `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
            }
        }

        $query = 'SELECT * FROM #__igallery_img LIMIT 1';
        $iDb->setQuery($query);
        $image = $iDb->loadAssoc();

        if( is_array($image) )
        {
            if( !array_key_exists('moderate', $image) )
            {
                $queries[] = "ALTER TABLE `#__igallery_img` ADD `moderate` INT(1) NOT NULL DEFAULT '1'";
            }

            if( !array_key_exists('publish_up', $image) )
            {
                $queries[] = "ALTER TABLE `#__igallery_img` ADD `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
            }

            if( !array_key_exists('publish_down', $image) )
            {
                $queries[] = "ALTER TABLE `#__igallery_img` ADD `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
            }
        }

        $query = 'SELECT * FROM #__igallery_profiles LIMIT 1';
        $iDb->setQuery($query);
        $profile = $iDb->loadAssoc();

        if( is_array($profile) )
        {
            if( !array_key_exists('asset_id', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `asset_id` int(10) NOT NULL";
            }

            if( !array_key_exists('slideshow_position', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `slideshow_position` VARCHAR(24) NOT NULL DEFAULT 'below'";
            }

            if( !array_key_exists('lbox_slideshow_position', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `lbox_slideshow_position` VARCHAR(24) NOT NULL DEFAULT 'below'";
            }

            if( !array_key_exists('show_filename', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `show_filename` VARCHAR(24) NOT NULL DEFAULT 'none'";
            }

            if( !array_key_exists('lbox_show_filename', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `lbox_show_filename` VARCHAR(24) NOT NULL DEFAULT 'none'";
            }

            if( !array_key_exists('show_numbering', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `show_numbering` INT(1) NOT NULL DEFAULT '0'";
            }

            if( !array_key_exists('lbox_show_numbering', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `lbox_show_numbering` INT(1) NOT NULL DEFAULT '0'";
            }

            if( !array_key_exists('show_thumb_info', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `show_thumb_info` VARCHAR(24) NOT NULL DEFAULT 'none'";
            }

            if( !array_key_exists('lbox_show_thumb_info', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `lbox_show_thumb_info` VARCHAR(24) NOT NULL DEFAULT 'none'";
            }

            if( !array_key_exists('plus_one', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `plus_one` INT(1) NOT NULL DEFAULT '0'";
            }

            if( !array_key_exists('lbox_plus_one', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `lbox_plus_one` INT(1) NOT NULL DEFAULT '0'";
            }

            if( !array_key_exists('main_image_align_horiz', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `main_image_align_horiz` VARCHAR(24) NOT NULL DEFAULT 'center'";
            }

            if( !array_key_exists('main_image_align_vert', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `main_image_align_vert` VARCHAR(24) NOT NULL DEFAULT 'center'";
            }

            if( !array_key_exists('lbox_image_align_horiz', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `lbox_image_align_horiz` VARCHAR(24) NOT NULL DEFAULT 'center'";
            }

            if( !array_key_exists('lbox_image_align_vert', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `lbox_image_align_vert` VARCHAR(24) NOT NULL DEFAULT 'center'";
            }

            if( !array_key_exists('show_image_count', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `show_image_count` INT( 1 ) NOT NULL DEFAULT '0'";
            }

            if( !array_key_exists('image_ordering', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `image_ordering` VARCHAR( 24 ) NOT NULL DEFAULT 'ordering'";
            }

            if( !array_key_exists('twitter_button', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `twitter_button` INT( 1 ) NOT NULL DEFAULT '0'";
            }

            if( !array_key_exists('lbox_twitter_button', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `lbox_twitter_button` INT( 1 ) NOT NULL DEFAULT '0'";
            }

            if( !array_key_exists('access', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `access` int(2) NOT NULL DEFAULT '1'";
            }

            if( !array_key_exists('menu_access', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `menu_access` int(2) NOT NULL DEFAULT '1'";
            }

            if( !array_key_exists('show_category_hits', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `show_category_hits` int(1) NOT NULL DEFAULT '0'";
            }

            if( !array_key_exists('search_results', $profile) )
            {
                $queries[] = "ALTER TABLE `#__igallery_profiles` ADD `search_results` varchar(24) NOT NULL DEFAULT 'joomla'";
            }

        }

		$queries[] = 'CREATE TABLE IF NOT EXISTS `#__igallery_ratings`(
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `image_id` int(11) NOT NULL,
		  `rating` int(1) NOT NULL,
		  `ip` varchar(24) NOT NULL,
		  `user` int(11) NOT NULL,
		  `published` int(1) NOT NULL DEFAULT "1",
		  `date` int(20) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `image_id` (`image_id`),
		  KEY `published` (`published`)
		)  DEFAULT CHARSET=utf8;';

        for($i=0;$i<count($queries); $i++)
        {
            $iDb->setQuery($queries[$i]);
            $iDb->query();
        }

        //set social button legacy bit:
        if( is_array($profile) )
        {
            if( !array_key_exists('twitter_button', $profile) )
            {
                $socialUsed = false;

                $query = 'SELECT * FROM #__igallery_profiles';
                $iDb->setQuery($query);
                $profiles = $iDb->loadObjectList();

                for($i=0; $i<count($profiles); $i++)
                {
                    if($profiles[$i]->share_facebook == 1 || $profiles[$i]->lbox_share_facebook == 1 || $profiles[$i]->allow_comments == 4 || $profiles[$i]->lbox_allow_comments == 4)
                    {
                        $socialUsed = true;
                    }
                }

                if($socialUsed == true)
                {
                    $iDb->setQuery('SELECT params FROM #__extensions WHERE name = '.$iDb->quote('com_igallery'));
                    $params = json_decode( $iDb->loadResult(), true );
                    $params['fb_legacy_urls'] = '1';
                    $paramsString = json_encode($params);
                    $iDb->setQuery('UPDATE #__extensions SET params = '.$iDb->quote($paramsString).' WHERE name = '.$iDb->quote('com_igallery'));
                    $iDb->query();
                }
            }
        }

        //migrate alrating bit
        if(JFile::exists(JPATH_ADMINISTRATOR.'/components/com_alratings/alratings.php') )
		{
			$db =& JFactory::getDBO();
			$query = 'SELECT id FROM #__igallery_ratings';
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if(empty($rows))
			{
				$db = JFactory::getDBO();
				$query = "SELECT * FROM #__alratings WHERE object_group = 'com_igallery'";
				$db->setQuery($query);
				$rows = $db->loadObjectList();

				if(count($rows) > 0)
				{
					JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_igallery/tables/');

					foreach($rows as $oldRating)
					{
						for( $i=0; $i<(int)$oldRating->rating_count;$i++)
						{
							$row = JTable::getInstance('igallery_ratings', 'Table');
							$row->image_id = (int)$oldRating->object_id;
							$row->rating = (int)$oldRating->rating_sum/$oldRating->rating_count;
							$row->ip = 0;
							$row->user = 0;
							$row->published = 1;
							$row->store();
						}
					}
				}

			}
    	}
    }

    function uninstall($parent)
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        require_once(JPATH_ADMINISTRATOR.'/components/com_igallery/defines.php');

        if ( JFolder::exists(IG_IMAGE_PATH) )
        {
            JFolder::delete(IG_IMAGE_PATH);
        }
    }

    function preflight($type, $parent)
    {
        $jversion = new JVersion();
        if(! $jversion->isCompatible('2.5.5') )
        {
            JError::raiseWarning(404, 'Ignite Gallery requires Joomla 2.5.5 or greater to function, you current Joomla version is '.$jversion->getShortVersion().', Please upgrade Joomla, and then install the gallery.');
            return false;
        }

        return true;
    }
}