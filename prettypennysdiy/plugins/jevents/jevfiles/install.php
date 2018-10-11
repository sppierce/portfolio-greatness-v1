<?php

/**
 * @copyright (C) 2012 GWE Systems Ltd - All rights reserved
 * @license GNU/GPLv3 www.gnu.org/licenses/gpl-3.0.html
 * */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class plgjeventsjevfilesInstallerScript {

    //
    // Joomla installer functions
    //
	public function preflight($type, $parent) {

		//PHP check first:

		if(!$this->phpCheck())
		{
			return false;
		}


        // Joomla! broke the update call, so we have to create a workaround check.
        $db = JFactory::getDbo();
        $db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevents' AND type='component' ");
        $is_enabled = $db->loadResult();

        if ($is_enabled == 1) {
            $manifest = JPATH_SITE . "/administrator/components/com_jevents/manifest.xml";
            if (!JFile::exists($manifest) || !$manifestdata = $this->getValidManifestFile($manifest)) {
                $manifest = JPATH_SITE . "/administrator/manifests/packages/pkg_jevents.xml";
                if (!JFile::exists($manifest) || !$manifestdata = $this->getValidManifestFile($manifest)) {
                    Jerror::raiseWarning(null, JText::_('JEV_REQUIRED'));
                    return false;
                }
            }

            $app = new stdClass ();
            $app->name = $manifestdata ["name"];
            $app->version = $manifestdata ["version"];

            if (version_compare($app->version, '3.4.0', "lt")) {
                Jerror::raiseWarning(null, JText::_('JEV_MIN_VERSION'));
                return false;
            } else {
                $this->hasJEventsInst = 1;
                return true;
            }
        } else {
            $this->hasJEventsInst = 0;
            if ($is_enabled == 0) {
                Jerror::raiseWarning(null, JText::_('JEV_DISABLED'));
                return false;
            } elseif (!$is_enabled) {
                Jerror::raiseWarning(null, JText::_('JEV_REQUIRED'));
                return false;
            }
        }
    }

    function install($parent) {
        $success = $this->createTables();
        if (!$success) {
            return false;
        }

        // Whoops! must disable auto enable for now. We need to update the database default params at the same time, or add more fallbacks in code.
        // New install, lets enable the plugin!
        $db = JFactory::getDbo();
        $db->setDebug(0);
        $sql = "UPDATE #__extensions SET enabled=1 WHERE element='jevfiles'";
        $db->setQuery($sql);
        $db->execute();
        echo $db->getErrorMsg();

        return true;
    }

    function uninstall($parent) {
        // No nothing for now, we want to keep the tables just incase they remove the plugin by accident.
    }

    function update($parent) {
        $success = $this->createTables();
        if (!$success) {
            return false;
        }

        // Nothing to do for now, tables should be created on install.
    }

    function createTables() {
        $db = JFactory::getDbo();
        if (version_compare(JVERSION, '3.3', 'ge')) {
            $charset = ($db->hasUTFSupport()) ? ' DEFAULT CHARACTER SET `utf8`' : '';
            $rowcharset = ($db->hasUTFSupport()) ? 'CHARACTER SET utf8' : '';
        } else {
            $charset = ($db->hasUTF()) ? ' DEFAULT CHARACTER SET `utf8`' : '';
            $rowcharset = ($db->hasUTF()) ? 'CHARACTER SET utf8' : '';
        }
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_files(
	file_num int(2) NOT NULL  default 0,
	ev_id int(12) NOT NULL  default 0,
	evdet_id int(12) NOT NULL  default 0,
	filetype varchar(10) NOT NULL  default "",
	filetitle varchar(120) NOT NULL  default "",
	filename varchar(120) NOT NULL  default "",
	filecomment varchar(250) NOT NULL  default "",

	PRIMARY KEY (file_num,evdet_id,filetype),
	INDEX (ev_id),
        INDEX (evdet_id)
)  $charset;
SQL;
        $db->setQuery($sql);
        if (!$db->execute()) {
            echo $db->getErrorMsg();
        }

        $sql = 'SHOW COLUMNS FROM `#__jev_files`';
        $db->setQuery($sql);
        $cols = @$db->loadObjectList('Field');

        if (!array_key_exists('evdet_id', $cols))
        {
                $sql = 'ALTER TABLE #__jev_files ADD COLUMN evdet_id int(12) NOT NULL  default 0';
                $db->setQuery($sql);
                @$db->execute();

                // Now insert the missing data
                $sql = 'UPDATE #__jev_files AS jf, #__jevents_vevent AS evt  SET jf.evdet_id = evt.detail_id WHERE evt.ev_id=jf.ev_id and jf.evdet_id=0';
                $db->setQuery($sql);
                @$db->execute();

                // Drop orphan rows
                $sql = "SELECT jf.ev_id FROM #__jev_files AS jf
LEFT JOIN #__jevents_vevent AS evt  
ON evt.ev_id=jf.ev_id 
where evt.ev_id is null";
                $db->setQuery($sql);
                $orphans = $db->loadColumn();
                
                if (count($orphans)>0){
                   // $sql = 'DELETE FROM #__jev_files WHERE ev_id in ('.implode(",", $orphans).')';
                    //$db->setQuery($sql);
                    //@$db->execute();                    
                }
                
                // finally fix the index
                $sql = 'alter table #__jev_files drop primary key, add PRIMARY KEY (file_num,ev_id, filetype)';
                $db->setQuery($sql);
                @$db->execute();
        }

        // New combined table - should be faster with fewer queries
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_files_combined(
        file_id int(12) NOT NULL auto_increment,
	ev_id int(12) NOT NULL  default 0,
	evdet_id int(12) NOT NULL  default 0,
	
        PRIMARY KEY (file_id),
        UNIQUE KEY (ev_id, evdet_id),
	INDEX (ev_id),
        INDEX (evdet_id)
)  $charset;
SQL;
      
        $db->setQuery($sql);
        if (!$db->execute()) {
            echo $db->getErrorMsg();
        }
        $sql = 'SHOW COLUMNS FROM `#__jev_files_combined`';
        $db->setQuery($sql);
        $cols = @$db->loadObjectList('Field');

        if (!array_key_exists('filetitle1', $cols)) {

            for ($col = 1; $col <= 30; $col++) {
                $sql = "ALTER TABLE #__jev_files_combined "
                        . "ADD COLUMN imagetitle$col   varchar(120) NOT NULL  default '', "
                        . "ADD COLUMN imagename$col    varchar(120) NOT NULL  default '', "
                        . "ADD COLUMN imagecomment$col TEXT , "
                        . "ADD COLUMN filetitle$col   varchar(120) NOT NULL  default '', "
                        . "ADD COLUMN filename$col    varchar(120) NOT NULL  default '', "
                        . "ADD COLUMN filecomment$col TEXT ";
                $db->setQuery($sql);
                @$db->execute();
            }
        }

        $sql = 'SELECT * FROM `#__jev_files_combined` LIMIT 1';
        $db->setQuery($sql);
        $hasnewdata = @$db->loadObject();
        $sql = "SELECT * FROM `#__jev_files` LIMIT 1";
        $db->setQuery($sql);
        $hasolddata = @$db->loadObject();
        if ($hasolddata && !$hasnewdata) {
            /*
            // Do we have permission to create the procedure
            $sql = "SHOW GRANTS";
            $db->setQuery($sql);
            $grants = $db->loadColumn();

            $allowed = false;
            foreach ($grants as $grant){
                if (strpos($grant, "ALL PRIVILEGES")>0 || strpos($grant, "CREATE ROUTINE")>0){
                    $allowed = true;
                }
            }
            if (!$allowed) {
                Jerror::raiseWarning(null, JText::_('JEV_UPGRADE_REQUIRED_CREATE_ROUTINE_PERMISSIONS'));
                return false;
            }
            
            $sql = <<< SQL
DROP PROCEDURE IF EXISTS convert_jevfiles;
SQL;
            $db->setQuery($sql);
            $db->execute();
            $sql = <<< SQL
CREATE PROCEDURE convert_jevfiles()
BEGIN
    DECLARE cnt INT DEFAULT 1;

    DELETE  FROM #__jev_files where filename="";

    INSERT IGNORE INTO #__jev_files_combined ( ev_id, evdet_id) 
        SELECT  ev_id, evdet_id FROM #__jev_files 
        WHERE filetype="image" OR filetype="file" AND ev_id IS NOT NULL GROUP BY ev_id, evdet_id;

countloop: WHILE cnt <=30 DO
        SET @qry = CONCAT('UPDATE #__jev_files_combined AS jfc
LEFT JOIN #__jev_files AS jf ON jfc.evdet_id=jf.evdet_id AND jfc.ev_id=jf.ev_id 
SET jfc.imagetitle',cnt,'=jf.filetitle ,
jfc.imagename',cnt,'= jf.filename,
jfc.imagecomment',cnt,'=jf.filecomment
WHERE jf.filetype="image" AND jf.file_num=',cnt
            );

        PREPARE stmt FROM @qry;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        SET @qry = CONCAT('UPDATE #__jev_files_combined AS jfc
LEFT JOIN #__jev_files AS jf ON jfc.evdet_id=jf.evdet_id AND jfc.ev_id=jf.ev_id 
SET jfc.filetitle',cnt,'=jf.filetitle ,
jfc.filename',cnt,'= jf.filename,
jfc.filecomment',cnt,'=jf.filecomment
WHERE jf.filetype="file" AND jf.file_num=',cnt
            );

        PREPARE stmt FROM @qry;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        SET cnt = cnt + 1;
        
    END WHILE countloop;
END;
SQL;
            // normal DB execute method can't deal with table prefix within the stored procedure so we do a trivial replacement here
            $sql = str_replace('#__', $db->getPrefix(), $sql);
            $db->setQuery($sql);
            $db->execute();
            $sql = <<< SQL
CALL convert_jevfiles();
SQL;
            $db->setQuery($sql);
            $db->execute();
            $sql = <<< SQL
DROP PROCEDURE IF EXISTS convert_jevfiles;
SQL;
            $db->setQuery($sql);
            $db->execute();
             * 
             */
            
            // do migration using PHP - too many sites have issues with stored procedures :(

            $sql = "DELETE  FROM #__jev_files where filename=''";
            $db->setQuery($sql);
            $db->execute();

            // Initial import of reference data - no image or files yet
            $sql = "INSERT IGNORE INTO #__jev_files_combined ( ev_id, evdet_id) 
            SELECT  ev_id, evdet_id FROM #__jev_files 
            WHERE filetype='image' OR filetype='file' AND ev_id IS NOT NULL 
            GROUP BY ev_id, evdet_id;";
            $db->setQuery($sql);
            $db->execute();

            // Now loop over the images/files
            for ($cnt = 1; $cnt<=30; $cnt++){
                $sql = "UPDATE #__jev_files_combined AS jfc
LEFT JOIN #__jev_files AS jf ON jfc.evdet_id=jf.evdet_id AND jfc.ev_id=jf.ev_id 
SET jfc.imagetitle$cnt = jf.filetitle ,
jfc.imagename$cnt = jf.filename,
jfc.imagecomment$cnt = jf.filecomment
WHERE jf.filetype='image' AND jf.file_num=$cnt ";
                $db->setQuery($sql);
                $db->execute();

                $sql = "UPDATE #__jev_files_combined AS jfc
LEFT JOIN #__jev_files AS jf ON jfc.evdet_id=jf.evdet_id AND jfc.ev_id=jf.ev_id 
SET jfc.filetitle$cnt = jf.filetitle ,
jfc.filename$cnt = jf.filename,
jfc.filecomment$cnt = jf.filecomment
WHERE jf.filetype='file' AND jf.file_num=$cnt ";
                $db->setQuery($sql);
                $db->execute();

                JFactory::getApplication()->enqueueMessage(JText::_("JEV_IMAGES_MIGRATED"), "warning");                    
            }
            
        }
                
        return true;
    }

    function postflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        echo '<h2>' . JText::_('PLG_INST_JEVENTS_JEVFILES') . ' ' . $parent->get('manifest')->version . ' </h2>';
        echo '<strong>';

        if ($type == 'update') {
            echo JText::_('PLG_INST_JEVENTS_JEVFILES_UPDATE') . '<br/>';
        } else {
            echo JText::_('PLG_INST_JEVENTS_JEVFILES_INSTALL') . '<br/>';
        }
        echo '</strong><br/><br/>';
    }

    // Manifest validation
    function getValidManifestFile($manifest) {
        $filecontent = JFile::read($manifest);
        if (stripos($filecontent, "jevents.net") === false && stripos($filecontent, 'gwesystems.com') === false && stripos($filecontent, 'joomlacontenteditor') === false && stripos($filecontent, 'virtuemart') === false && stripos($filecontent, 'sh404sef') === false) {
            return false;
        }
        // for JCE and Virtuemart only check component version number
        if (stripos($filecontent, 'joomlacontenteditor') !== false || stripos($filecontent, 'virtuemart') !== false || stripos($filecontent, 'sh404sef') !== false || strpos($filecontent, 'JCE') !== false) {
            if (strpos($filecontent, "type='component'") === false && strpos($filecontent, 'type="component"') === false) {
                return false;
            }
        }

        $manifestdata = JApplicationHelper::parseXMLInstallFile($manifest);
        if (!$manifestdata)
            return false;
        if (strpos($manifestdata ['authorUrl'], 'jevents') === false && strpos($manifestdata ['authorUrl'], 'gwesystems') === false && strpos($manifestdata ['authorUrl'], 'joomlacontenteditor') === false && strpos($manifestdata ['authorUrl'], 'virtuemart') === false && strpos($manifestdata ['name'], 'sh404SEF') === false) {
            return false;
        }
        return $manifestdata;
    }

	function phpCheck()
	{
		// Only allow to install on PHP 5.4.1 or later
		if (defined('PHP_VERSION'))
		{
			$version = PHP_VERSION;
		}
		elseif (function_exists('phpversion'))
		{
			$version = phpversion();
		}
		else
		{
			$version = '5.0.0'; // We set this version as reference
		}

		if (!version_compare($version, '5.4.1', 'ge'))
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf("COM_JEVENTS_LOW_PHP_WARNING", JText::_("JEV_STANDARD_IMAGE_AND_FILES_NAME")), 'error');

			return false;
		}
		else
		{
			return true;
		}
	}
}
