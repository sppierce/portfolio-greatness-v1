<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
JLoader::register('JevJoomlaVersion', JPATH_ADMINISTRATOR . '/components/com_jevents/libraries/version.php');

class plgJEventsjevfiles extends JPlugin {

    private
            $_dbvalid = 0;
    private
            $form = false;
    private
            $anonupload = false;

    public function onContentPrepareForm($form, $data)
    {
            if (!($form instanceof JForm))
            {
                    $this->_subject->setError('JERROR_NOT_A_FORM');
                    return false;
            }
            
            $input = JFactory::getApplication()->input;
            if (!($input->getCmd("option")=="com_jevents" && ($input->getString("task")=="icalevent.edit") || $input->getString("task")=="icalrepeat.edit")){
                return true;
            }
            
            // Check we are manipulating a valid form.
            $name = $form->getName();
            if (in_array($name, array('jevents.edit.icalevent')))
            {
                JForm::addFormPath(JPATH_SITE."/plugins/jevents/jevfiles/");
                $form->loadFile('media', false);
            }

            // Store a reference to the form to use on the event edit page
            $this->form = $form;

    }    
       
    public function onJeventsGetter( &$row, $name, &$available) {
        $value = $this->substitutefield($row, $name);
        if ($value !== ""){
            $available = true;
            echo $value;
        }        
    }
    
    function __construct(& $subject, $config) {
        parent::__construct($subject, $config);

        JFactory::getLanguage()->load('plg_jevents_jevfiles', JPATH_ADMINISTRATOR);

        jimport('joomla.application.component.view');

        if (version_compare(JVERSION, '1.6.0', 'ge')) {
            $this->_basepath = JPATH_SITE . '/plugins/jevents/jevfiles/';
        } else {
            $this->_basepath = JPATH_SITE . '/plugins/jevents/';
        }
        //$this->view = new JView(array('base_path' => $this->_basepath, "template_path" => $this->_basepath . "tmpl", "name" => 'jevfiles'));
        //$this->view->addTemplatePath(JPATH_SITE . / . 'templates' . / . JFactory::getApplication ()->getTemplate() . / . 'html' . / . "plg_jevfiles" );
    }

    function onEditCustom(&$row, &$customfields) {
        if (!extension_loaded('gd') || !function_exists('gd_info')) {
            echo '<strong>' . JText::_("JEV_GD_LIBRARY_MISSING") . '</strong>';
            return;
        }

        if (!$this->authorised(false)) {
            return;
        }

        $files = JFactory::getApplication()->input->files;
        $filecount = $files->count();

        // intercept this to save the file
        if ($filecount > 0) {
            $filedata = $files->getArray();
            $hasfile = false;
            $uploadfile = '';
            foreach ($filedata as $key => $val) {
                // Joomla 3.6 is too strict on file uploads and considered zip files to be unsafe!
                $val = $files->get($key, $val, 'raw');

                if ((strpos($key, 'upload_image') === 0 || strpos($key, 'upload_file') === 0) && $val['size'] > 0) {
                    $hasfile = true;
                    $uploadfile = $key;
                    break;
                }
            }

            if ($hasfile)
                return $this->processUpload($uploadfile);
        }

        // Only setup when editing an event (not a repeat)  - unless editing a repeat and it is allowed!
        if (!$this->params->get('allowoverrides', 0)) {
            if (JRequest::getString('jevtask', '') != 'icalevent.edit' && JRequest::getString('jevtask', '') != 'icalevent.editcopy') {
                return;
            }
        }

        // Useful query form which I considered but was slower than the single combined row
        /*
         * Using #__jev_files instead and 
         * CASE WHEN jfc.file_num=1 then jfc.filename else null end as filename1,
         * CASE WHEN jfc.file_num=2 then jfc.filename else ull end as filename2,
         * CASE WHEN jfc.file_num=3 then jfc.filename else null end as filename3
         */
        // Do we offer migration?  Remove this code after October 2017
        if ($this->params->get('offermigration', 1) && JFactory::getUser()->authorise('core.admin')) {
            $sql = "SELECT count(jf.file_num) FROM #__jev_files as jf
LEFT JOIN #__jev_files_combined as jfc on jf.ev_id = jfc.ev_id AND jf.evdet_id = jfc.evdet_id
where jfc.file_id is null";
            $db = JFactory::getDbo();
            $db->setQuery($sql);
            $migrationcount = (int) $db->loadResult();
            if ($migrationcount>0 && $this->params->get('offermigration', 1)==1) {
                JFactory::getApplication()->enqueueMessage(JText::_("JEV_IMAGES_MIGRATION"), "warning");
            }
            else if ($migrationcount>0 && $this->params->get('offermigration', 1)==2){
                
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
        }
        $jevuser = JEVHelper::getAuthorisedUser();

        // Get the media component configuration settings
        $params = JComponentHelper::getParams('com_media');
        // Set the path definitions
        if (!defined('JEVP_MEDIA_BASE')) {
            define('JEVP_MEDIA_BASE', JPATH_ROOT . '/' . $params->get('image_path', 'images/stories'));
            define('JEVP_MEDIA_BASEURL', JURI::root(true) . '/' . $params->get('image_path', 'images/stories'));
        }
        // folder relative to media folder
        $folder = $this->params->get("folder", "");
        if ($folder == "") {
            echo JText::_("JEV_SAVE_PLUGIN_PARAMETERS");
            return;
        }
        // ensure folder exists
        jimport('joomla.filesystem.folder');
        if (!JFolder::exists(JEVP_MEDIA_BASE . "/" . $folder)) {
            JFolder::create(JEVP_MEDIA_BASE . "/" . $folder);
        }

        // get the data from database and attach to row                
        $db = JFactory::getDbo();
        $filemappings = new stdClass();
        if ($this->params->get("allowoverrides", 0) && $row->evdet_id()>0 ) {
            $detid = intval($row->evdet_id());
            $sql = "SELECT * FROM #__jev_files_combined WHERE evdet_id=" . $detid;
            $db->setQuery($sql);
            $filemappings = $db->loadObject();
        } else if ($row->ev_id()>0) {
            $evid = intval($row->ev_id());
            $sql = "SELECT * FROM #__jev_files_combined WHERE ev_id=" . $evid;
            $db->setQuery($sql);
            $filemappings = $db->loadObject();
        }
        
        // need session id to ensure login is maintained
        $session = JFactory::getSession();
        $mainframe = JFactory::getApplication();
        if ($mainframe->isAdmin()) {
            //$targetURL = JURI::root(true) . '/administrator/index.php?tmpl=component&folder=' . $folder . '&' . $session->getName() . '=' . $session->getId() . '&' . JSession::getFormToken() . '=1';
            $targetURL = JURI::root(true) . '/administrator/index.php?tmpl=component&folder=' . $folder;
        } else {
            //$targetURL = JURI::root(true) . '/index.php?tmpl=component&folder=' . $folder . '&' . $session->getName() . '=' . $session->getId() . '&' . JSession::getFormToken() . '=1';
            $targetURL = JURI::root(true) . '/index.php?tmpl=component&folder=' . $folder;
        }

        $uploaderInit = "
		var oldAction = '';
		var oldTarget = '';
		var oldTask = '';
		var oldOption = '';
		function uploadFileType(field){
			form = document.adminForm;
			oldAction = form.action;
			oldTarget = form.target;
			oldTask = form.task.value;
			oldOption = form.option.value;
			form.action = '" . $targetURL . "&jEV=default&field='+field;

			form.target = 'uploadtarget';";
        if ($mainframe->isAdmin()) {
            // if we allow overrides then no need to change the task!
            if (!$this->params->get("allowoverrides", 0)) {
                $uploaderInit .= "form.task.value = 'icalevent.edit';";
            }
        } else {
            $uploaderInit .= "form.task.value = 'day.listevents';";
        }
        $uploaderInit .= "form.option.value = 'com_jevents';
			form.submit();
			form.action = oldAction ;
			form.target = oldTarget ;
			form.task.value = oldTask ;
			form.option.value = oldOption;

			var loading = document.getElementById(field+'_loading');
			loading.style.display='block';
			var loaded = document.getElementById(field+'_loaded');
			loaded.style.display='none';
		}
		function setImageFileName(fname, filename,oname){
			if(!fname) return;

			var elemname =  fname.substr(0,fname.length-5);
			var titleelem = document.getElementById('custom_' + elemname + '_title');
			var settitle = false;
			if (titleelem && titleelem.value=='')  settitle = true;

			// msie fix - it doens't clear the upload fieldfile  after upload
			var elem = document.getElementById(fname);
			if (jQuery.browser.msie){
				elem.parentNode.innerHTML = elem.parentNode.innerHTML;
			}

			// contorted because of Msie fix!
			if (settitle) {
				titleelem = document.getElementById('custom_' + elemname + '_title');
				titleelem.value = oname;
				//titleelem.value = document.getElementById('title').value;
			}

			elem = document.getElementById('custom_' + elemname);
			if (elem) elem.value = filename;
			elem = document.getElementById(fname);
			if (elem) elem.value = '';
			img = document.getElementById(elemname+'_img');
			img.src = '" . JEVP_MEDIA_BASEURL . "/$folder/thumbnails/thumb_'+filename;
			img.style.display='block';
			img.style.marginRight='10px';

			var loading = document.getElementById(elemname+'_loading');
			loading.style.display='none';
			var loaded = document.getElementById(elemname+'_loaded');
			loaded.style.display='block';

		}
		function clearImageFile(elemname){

			img = document.getElementById(elemname+'_img');
			img.src = ''
			img.style.display='none';
			img.style.marginRight='0px';
			elem = document.getElementById('custom_' + elemname);
			if (elem) elem.value = '';
			elem = document.getElementById('custom_' + elemname+'_title');
			if (elem) elem.value = '';
		}

		function clearFile(elemname){
			img = document.getElementById(elemname+'_link');
			img.href = ''
			img.innerHTML='';
			img.style.display='none';
			elem = document.getElementById('custom_'+elemname);
			if (elem) elem.value = '';
		}

		function setLinkFileHref(fname, filename, oname){
			if(!fname) return;

			// msie fix - it doens't clear the upload fieldfile  after upload
			elem = document.getElementById(fname);
			if (jQuery.browser.msie){
				elem.parentNode.innerHTML = elem.parentNode.innerHTML;
			}

			elemname = fname.substr(0,fname.length-5);
			elem = document.getElementById('custom_' + elemname);
			if (elem) elem.value = filename;

			var titleelem = document.getElementById('custom_' + elemname + '_title');
			var settitle = false;
			if (titleelem && titleelem.value=='')  settitle = true;

			if (settitle) {
				titleelem.value = oname;
			}

			elem = document.getElementById(fname);
			if (elem) elem.value = '';
			mylink = document.getElementById(elemname+'_link');
			mylink.href = '" . JEVP_MEDIA_BASEURL . "/$folder/'+filename;
			mylink.innerHTML = oname;

			var loading = document.getElementById(elemname+'_loading');
			loading.style.display='none';
			var loaded = document.getElementById(elemname+'_loaded');
			loaded.style.display='block';
		}

		";
        $document = JFactory::getDocument();
        $document->addScriptDeclaration($uploaderInit);

        $result = '<iframe src="about:blank" style="display:none" name="uploadtarget" id="uploadtarget"></iframe>';
        $result .= '<input type="hidden" name="' . $session->getName() . '" id="sessions" value="' . $session->getId() . '" />';
        $result .= '<input type="hidden" name="' . JSession::getFormToken() . '" id="token" value="1" />';

        $imagenos = intval($this->params->get("imnum", 1));
        $filenos = intval($this->params->get("filnum", 1));

        // index from 1 !!!
        for ($i = 1; $i <= $imagenos; $i++) {

            $namefield = "imagename" . $i;
            $titlefield = "imagetitle" . $i;
            $filename = isset($filemappings->$namefield) && $filemappings->$namefield != "" ? $filemappings->$namefield : "";
            $filetitle = isset($filemappings->$namefield) && $filemappings->$titlefield != "" ? $filemappings->$titlefield : "";

            if ($filename) {
                // non-joomla images
                if ($this->params->get("allowmedia", 0)  && !JFile::exists(JPATH_ROOT."/$filename")) {
                    $filename = $params->get('image_path', 'images/stories')."/$folder/$filename";
                }
                if(strpos($filename, "/")!==false){
                    $src =   JURI::root(true) . '/'. $filename;
                }
                else {
                    $src = JEVP_MEDIA_BASEURL . "/$folder/thumbnails/thumb_$filename";
                }
                $visibility = "visibility:visible;";
                $visibility = "margin-right:10px;display:block;";
            } else {
                $src = "about:blank";
                $visibility = "margin-right:0px;";
                $visibility = "display:none;";
            }

            $fieldname = "upload_image" . $i;
            $id_to_check = "custom_" . $fieldname;
            $result .= '<img id="' . $fieldname . '_img" alt="' . htmlentities($filetitle) . '" src="' . $src . '" style="' . $visibility . 'max-width:400px!important;"/>';
            $result .= JHTML::_('form.token');
            if ($this->params->get("imagetitle", 1)) {
                $result .= '<label for="custom_' . $fieldname . '_file">' . JText::_("JEV_UPLOAD_IMAGE_TITLE") . '</label>';
                $result .= '<input type="text" name="custom_' . $fieldname . '_title" id="custom_' . $fieldname . '_title" value="' . $filetitle . '" size="50"/>';
            } else {
                $result .= '<input type="hidden" name="custom_' . $fieldname . '_title" id="custom_' . $fieldname . '_title" value="' . $filetitle . '" size="50"/>';
            }            
            if ($this->params->get("allowmedia", 0) && $this->form ) {
                $result .= '<label for="' . $fieldname . '_file">' . JText::_("JEV_SELECT_IMAGE") . '</label>';
                $this->form->setValue("custom_upload_image$i",null,  $filename);
                
                // workaround for CMS routing problem with frontend media manager
                $imageinput = $this->form->getInput("custom_upload_image$i");
                //$result .= str_replace("&amp;asset=com_jevents&", "&amp;asset=com_jevents&amp;task=display&", $imageinput );
		$imageinput = str_replace("index.php", JUri::base()."index.php", $imageinput );
                $result .= str_replace( "/".JUri::base(), JUri::base(),  $imageinput );
                JFactory::getDocument()->addStyleDeclaration("#jevents #imageModal_custom_upload_image$i {margin-left: -40%;}");                                           
            }
            else {
                $result .= '<input type="hidden" name="custom_' . $fieldname . '" id="custom_' . $fieldname . '" value="' . $filename . '" size="50"/>';                
                $result .= '<label for="' . $fieldname . '_file">' . JText::sprintf("JEV_UPLOAD_IMAGE", number_format($this->params->get("maxupload", 1000000) / 1000000, 2)) . '</label><br/>';
                $result .= '<span><input type="file" name="' . $fieldname . '_file" id="' . $fieldname . '_file" size="50"/ class="btn"></span>';
                $result .= ' <input type="button" onclick="uploadFileType(\'' . $fieldname . '\')" value="' . JText::_("jev_upload") . '"  class="btn"/> ';
                $result .= '<input type="button" onclick="clearImageFile(\'' . $fieldname . '\')" value="' . JText::_("jev_Delete") . '"  class="btn"/>';
                $result .= '<div id="' . $fieldname . '_loading" class="loading" style="display:none">' . JText::_("JEV_UPLOADING_WAIT") . '</div>';
                $result .= '<div id="' . $fieldname . '_loaded" class="loaded" style="display:none">' . JText::_("JEV_UPLOAD_COMPLETE") . '</div>';
            }
            
            $label = JText::_("JEV_STANDARD_IMAGE_" . $i);
            if ($label == "JEV_STANDARD_IMAGE_" . $i)
                $label = JText::_("JEV_STANDARD_IMAGE");

            $user = JFactory::getUser();
            $jevparams = JComponentHelper::getParams('com_jevents');
            if (($jevparams->get("authorisedonly", 0) && $jevuser && $jevuser->canuploadimages) || $this->anonupload) {
                $customfield = array("label" => $label, "input" => $result, "default_value" => "", "id_to_check" => $id_to_check);
                $customfields["image$i"] = $customfield;
            } else if (!$jevparams->get("authorisedonly", 0)) {
                $userGroups = JFactory::getUser()->getAuthorisedGroups();
                if (!array_intersect($this->params->get('upimageslevel', array(8)), array_values($userGroups))) {
                    return;
                }
                $customfield = array("label" => $label, "input" => $result, "default_value" => "", "id_to_check" => $id_to_check);
                $customfields["image$i"] = $customfield;
            }

            $result = "";
        }

        // index from 1 !!!
        for ($i = 1; $i <= $filenos; $i++) {

            $namefield = "filename" . $i;
            $titlefield = "filetitle" . $i;
            $filename = isset($filemappings->$namefield) && $filemappings->$namefield != "" ? $filemappings->$namefield : "";
            $filetitle = isset($filemappings->$namefield) && $filemappings->$titlefield != "" ? $filemappings->$titlefield : "";

            if ($filename) {
                $href = JEVP_MEDIA_BASEURL . "/$folder/$filename";
            } else {
                $href = "about:blank";
            }
            $fieldname = "upload_file" . $i;
            $id_to_check = "custom_" . $fieldname;
            $result .= '<a id="' . $fieldname . '_link" href="' . $href . '" style="float:left;margin-right:10px;" target="_blank">' . ($filename ? $filetitle : "") . "</a>";
            $result .= JHTML::_('form.token');
            $result .= '<input type="hidden" name="' . $id_to_check . '" id="custom_' . $fieldname . '" value="' . $filename . '" size="50"/>';
            if ($this->params->get("filetitle", 1)) {
                $result .= '<label for="custom_' . $fieldname . '_file">' . JText::_("JEV_UPLOAD_FILE_TITLE") . '</label><br/>';
                $result .= '<input type="text" name="custom_' . $fieldname . '_title" id="custom_' . $fieldname . '_title" value="' . $filetitle . '" size="50"/><br/>';
            } else {
                $result .= '<input type="hidden" name="custom_' . $fieldname . '_title" id="custom_' . $fieldname . '_title" value="' . $filetitle . '" size="50"/>';
            }

            $result .= '<label for="' . $fieldname . '_file">' . JText::sprintf("JEV_UPLOAD_FILE", number_format($this->params->get("maxupload", 1000000) / 1000000, 2)) . '</label><br/>';
            $result .= '<input type="file" name="' . $fieldname . '_file" id="' . $fieldname . '_file" size="50"/ class="btn">';
            $result .= ' <input type="button" onclick="uploadFileType(\'' . $fieldname . '\')" value="' . JText::_("jev_upload") . '" class="btn" /> ';
            $result .= '<input type="button" onclick="clearFile(\'' . $fieldname . '\')" value="' . JText::_("jev_Delete") . '" class="btn"/>';
            $result .= '<div id="' . $fieldname . '_loading" class="loading" style="display:none">' . JText::_("JEV_UPLOADING_WAIT") . '</div>';
            $result .= '<div id="' . $fieldname . '_loaded" class="loaded" style="display:none">' . JText::_("JEV_UPLOAD_COMPLETE") . '</div>';
            $result .= '<br style="clear:both"/>';

            $label = JText::_("JEV_STANDARD_FILE_" . $i);
            if ($label == "JEV_STANDARD_FILE_" . $i)
                $label = JText::_("JEV_STANDARD_FILE");

            $user = JFactory::getUser();
            $jevparams = JComponentHelper::getParams('com_jevents');
            if (($jevparams->get("authorisedonly", 0) && $jevuser && $jevuser->canuploadmovies) || $this->anonupload) {
                $customfield = array("label" => $label, "input" => $result, "default_value" => "", "id_to_check" => $id_to_check);
                $customfields["file$i"] = $customfield;
            } else if (!$jevparams->get("authorisedonly", 0)) {
                if (!(JEVHelper::isAdminUser($user))) {
                    // restrict usage to certain user types
                    $userGroups = JFactory::getUser()->getAuthorisedGroups();
                    if (!array_intersect($this->params->get('upfileslevel', array(8)), array_values($userGroups))) {
                        return;
                    }
                }
                $customfield = array("label" => $label, "input" => $result, "default_value" => "", "id_to_check" => $id_to_check);
                $customfields["file$i"] = $customfield;
            }


            $result = "";
        }

        return true;
    }

    /**
     * Clean out custom fields for event details not matching global event detail
     *
     * @param unknown_type $idlist
     */
    function onDeleteEventDetails($idlist) {
        return true;
    }

    /**
     * Store custom fields
     *
     * @param iCalEventDetail $evdetail
     */
    function onStoreCustomDetails($evdetail) {
        // are we importing events with image details contained within them
        if (isset($evdetail->_customFields) && strpos(JRequest::getCmd("task"), "icals") !== false) {
            foreach ($evdetail->_customFields as $key => $val) {
                if (strpos($key, "IMPORT_IMAGE_") === 0) {
                    $x = 1;
                }
            }
        }
    }

    /**
     * Store custom fields
     *
     * @param iCalEventDetail $evdetail
     */
    // TODO update reminder timestamps when event times have changed
    function onStoreCustomEvent($event) {
        if (!$this->authorised(false))
            return;

        $success = true;
        $evdetail = $event->_detail;

        $eventid = $event->ev_id;
        $detailid = $event->detail_id;

        $db = JFactory::getDbo();
        $sql = "SELECT * FROM #__jev_files_combined WHERE evdet_id=" . $detailid;
        $db->setQuery($sql);
        $filedata = $db->loadObject();

        $imagenos = intval($this->params->get("imnum", 1));
        $rowcreated = false;
        // index from 1 !!!
        for ($i = 1; $i <= $imagenos; $i++) {
            $filename = "upload_image" . $i;

            if (!isset($evdetail->_customFields) || !array_key_exists($filename, $evdetail->_customFields) || $evdetail->_customFields[$filename]=="")
                continue;

            $noHtmlFilter = JFilterInput::getInstance(/* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
            $image = $noHtmlFilter->clean($evdetail->_customFields[$filename]);
            $imagetitle = $noHtmlFilter->clean($evdetail->_customFields[$filename . "_title"]);

            if (($filedata && $filedata->evdet_id > 0) || $rowcreated) {

                $sql = "UPDATE #__jev_files_combined "
                        . " SET imagename$i=" . $db->Quote($image)
                        . ", imagetitle$i=" . $db->Quote($imagetitle)
                        . " WHERE evdet_id=" . intval($detailid);
            } else {
                $sql = "INSERT INTO #__jev_files_combined "
                        . " SET imagename$i=" . $db->Quote($image)
                        . ", imagetitle$i=" . $db->Quote($imagetitle)
                        . ", ev_id=" . intval($eventid)
                        . ", evdet_id=" . intval($detailid);
                $rowcreated = true;
            }
            $db->setQuery($sql);
            $success = $db->execute();
        }

        $filenos = intval($this->params->get("filnum", 1));
        // index from 1 !!!
        for ($i = 1; $i <= $filenos; $i++) {
            $filename = "upload_file" . $i;

            if (!isset($evdetail->_customFields) || !array_key_exists($filename, $evdetail->_customFields))
                continue;

            $noHtmlFilter = JFilterInput::getInstance(/* $tags, $attr, $tag_method, $attr_method, $xss_auto */);
            $image = $noHtmlFilter->clean($evdetail->_customFields[$filename]);
            $imagetitle = $noHtmlFilter->clean($evdetail->_customFields[$filename . "_title"]);

            if (($filedata && $filedata->evdet_id > 0) || $rowcreated) {

                $sql = "UPDATE #__jev_files_combined "
                        . " SET filename$i=" . $db->Quote($image)
                        . ", filetitle$i=" . $db->Quote($imagetitle)
                        .  " WHERE evdet_id=" . intval($detailid);
            } else {
                $sql = "INSERT INTO #__jev_files_combined "
                        . " SET filename$i=" . $db->Quote($image)
                        . ", filetitle$i=" . $db->Quote($imagetitle)
                        . ", ev_id=" . intval($eventid)
                        . ", evdet_id=" . intval($detailid);
                $rowcreated = true;
            }
            $db->setQuery($sql);
            $success = $db->execute();
        }

        return $success;
    }

    function onStoreCustomRepeat($event) {
        $event->_detail = new stdClass();
        $data = JFactory::getApplication()->input->getArray();
        foreach ($data as $key => $value) {
            if (strpos($key, "custom_") === 0) {
                $field = JString::substr($key, 7);
                $event->_detail->_customFields[$field] = $value;
            }
        }

        $event->ev_id = $event->eventid;
        $event->detail_id = $event->eventdetail_id;

        return $this->onStoreCustomEvent($event);
    }

    /**
     * Clean out custom details for deleted event details
     *
     * @param comma separated list of event detail ids $idlist
     */
    function onDeleteCustomEvent($idlist) {

        $ids = explode(",", $idlist);
        JArrayHelper::toInteger($ids);
        $idlist = implode(",", $ids);

        // Find the referenced files
        $db = JFactory::getDbo();
        $sql = "SELECT * FROM #__jev_files_combined WHERE ev_id IN (" . $idlist . ")";
        $db->setQuery($sql);
        $eventfilesrows = $db->loadAssocList();
        $eventfiles = array();
        foreach ($eventfilesrows as $row) {
            foreach ($row as $fieldname => $fieldvalue) {
                if ((strpos($fieldname, "imagename") === 0 || strpos($fieldname, "filename") === 0) && $fieldvalue != "") {
                    $eventfiles[$fieldname] = $fieldvalue;
                }
            }
        }

        // Get the media component configuration settings
        $params = JComponentHelper::getParams('com_media');
        $path = JPATH_ROOT . "/" . $params->get('image_path', 'images/stories');
        $folder = $this->params->get("folder", "jevents");

        // Set the path definitions
        if (!defined('JEVP_MEDIA_BASE')) {
            define('JEVP_MEDIA_BASE', JPATH_ROOT . "/" . $params->get('image_path', 'images/stories'));
            define('JEVP_MEDIA_BASEURL', JURI::root(true) . '/' . $params->get('image_path', 'images/stories'));
        }

        jimport('joomla.filesystem.file');
        foreach ($eventfiles as $fieldname => $file) {
            if (trim($file) == "")
                continue;

            // make sure not used in a copied event
            $sql = "SELECT count(ev_id) FROM #__jev_files_combined WHERE $fieldname = " . $db->quote($file);
            $db->setQuery($sql);
            $count = $db->loadResult();
            if ($count > 1)
                continue;

            $files = JFolder::files(JEVP_MEDIA_BASE . "/" . $folder, $file, true, true);
            foreach ($files as $file2) {
                JFile::delete($file2);
            }
            if (JFile::exists($path . "/" . $folder . "/" . 'thumbnails' . "/" . 'thumb_' . $file)) {
                JFile::delete($path . "/" . $folder . "/" . 'thumbnails' . "/" . 'thumb_' . $file);
            }
        }

        // delete the metatags too
        $sql = "DELETE FROM #__jev_files_combined WHERE ev_id IN (" . $idlist . ")";
        $db->setQuery($sql);
        $db->execute();

        return;
    }

    function onDisplayCustomFields(&$row) {

        $db = JFactory::getDBO();
        if ($this->params->get("allowoverrides", 0)) {
            $detid = intval($row->evdet_id());
            $sql = "SELECT * FROM #__jev_files_combined WHERE evdet_id=" . $detid;
        } else {
            $evid = intval($row->ev_id());
            $sql = "SELECT * FROM #__jev_files_combined WHERE ev_id=" . $evid;
        }
        
        $db->setQuery($sql);
        $row->filedata = $db->loadObject();        
        
        $row->_image = "";

        // Get the media component configuration settings
        $params = JComponentHelper::getParams('com_media');
        // Set the path definitions
        if (!defined('JEVP_MEDIA_BASE')) {
            define('JEVP_MEDIA_BASE', JPATH_ROOT . "/" . $params->get('image_path', 'images/stories'));
            define('JEVP_MEDIA_BASEURL', JURI::root(true) . '/' . $params->get('image_path', 'images/stories'));
        }

        $result = "";
        // folder relative to media folder
        $folder = $this->params->get("folder", "jevents");

        $imagenos = intval($this->params->get("imnum", 1));
        // index from 1 !!!
        for ($i = 1; $i <= $imagenos; $i++) {
            $imageurlfield = "_imageurl$i";
            $imagetitlefield = "_imagetitle$i";
            $imagenamesql = "imagename$i";
            $imagetitlesql = "imagetitle$i";
            
            if (!isset($row->filedata->$imagenamesql) || $row->filedata->$imagenamesql==""){
                $row->$imageurlfield = "";
                $row->$imagetitlefield = "";
                continue;
            }
            $row->$imageurlfield = $row->filedata->$imagenamesql;            
            // put original full sized image in the _imageurl'n' field since its used by other plguins e.g. autotweet and facebook social
            $row->$imageurlfield = plgJEventsjevfiles::getSizedImageUrl($row, "_imageurl$i", "0x0", $this->params);

			$filetitle = $row->filedata->$imagetitlesql;
            $row->$imagetitlefield = $filetitle;

            $result .= ($result != "") ? "<br/>" : "";
            $name = "_image$i";
            
            // this is all that matters any more!  
            // TODO remove the redundant field setting
            $img = plgJEventsjevfiles::getSizedImageUrl($row, "_imageurl1", $this->params->get("imagew", 300)."x".$this->params->get("imageh", 225) , $this->params);
            $row->$name = '<img  src="' . $img . '" alt="' . htmlentities($filetitle) . '"  class="jev_image jev_image1"/>';
            $result .= $row->$name;
        }

        $filnos = intval($this->params->get("filnum", 1));
        // index from 1 !!!
        for ($i = 1; $i <= $filnos; $i++) {

            $filtitle = "filetitle" . $i;
            $filname = "filename" . $i;
            if (!isset($row->filedata->$filtitle) || $row->filedata->$filtitle == "") {
                $name = "_file$i";
                $row->$name = "";
                $name = "_filetitle$i";
                $row->$name = "";
                continue;
            }
            $filename = $row->filedata->$filname;
            $filetitle = $row->filedata->$filtitle;

            if ($filename == "") {
                $name = "_file$i";
                $row->$name = "";
                $name = "_filetitle$i";
                $row->$name = "";
                continue;
            }

            $href = JEVP_MEDIA_BASEURL . "/$folder/$filename";
            $name = "_filetitle$i";
            $row->$name = $filetitle;
            $name = "_file$i";
            $row->$name = $filename;

            if (strpos($filename, '.pdf')) {
                $ispdf = "target='_blank'";
            } else {
                $ispdf = "";
            }

            $link = "<a href='$href' title='" . htmlentities($filetitle) . "' " . $ispdf . " class='jev_file jev_file1'>" . $filetitle . "</a>";
            $name = "_filelink$i";
            $row->$name = $link;
            $name = "_filehref$i";
            $row->$name = $href;

            $result .= ($result != "") ? "<br/>" : "";
            $result .= $link;
        }

        return $result;
    }

    function onListIcalEvents(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroupdby = false) {
        $files = JFactory::getApplication()->input->files;
        $filecount = $files->count();

        // intercept this to save the file
        if ($filecount > 0) {
            $filedata = $files->getArray();
            $hasfile = false;
            $uploadfile = "";
            foreach ($filedata as $key => $val) {
                // Joomla 3.6 is too strict on file uploads and considered zip files to be unsafe!
                $val = $files->get($key, $val, "raw");

                if ((strpos($key, "upload_image") === 0 || strpos($key, "upload_file") === 0) && $val["size"] > 0) {
                    $hasfile = true;
                    $uploadfile = $key;
                    break;
                }
            }

            if ($hasfile)
                return $this->processUpload($uploadfile);
        }

        if (!$this->params->get("inlist", 1))
            return "";


        $params = JComponentHelper::getParams('com_media');
        $mediabase = JURI::root(true) . '/' . $params->get('image_path', 'images/stories');
        // folder relative to media folder
        $folder = $this->params->get("folder", "jevents");

        $imagenos = intval($this->params->get("imnum", 1));

        // if loading multiple images then needs group by!!
        if ($imagenos > 1) {
            $needsgroupdby = true;
        }

        if ($this->params->get("allowoverrides", 0)) {        
            $extrajoin[] = " #__jev_files_combined as jfc ON jfc.evdet_id = det.evdet_id";
        }
        else {
            $extrajoin[] = " #__jev_files_combined as jfc ON jfc.ev_id = ev.ev_id";
        }

        // index from 1 !!!
        for ($i = 1; $i <= $imagenos; $i++) {
            // are we using Joomla media manager - if so mustn't repeat the path
            if ($this->params->get("allowmedia", 0)) {
                $extrafields .= <<<SQL
                , jfc.imagename$i as imageurl$i
SQL;
            }
            else {
                $extrafields .= <<<SQL
                , jfc.imagename$i as imageurl$i
SQL;
            }
        }

        $filenos = intval($this->params->get("filnum", 1));
        for ($i = 1; $i <= $filenos; $i++) {
            $extrafields .= <<<SQL
                , CASE WHEN (jfc.filename$i='') THEN '' ELSE CONCAT('$mediabase/$folder/',jfc.filename$i) END as filehref$i
                , jfc.filetitle$i as filetitle$i
                , CASE WHEN (jfc.filename$i='') THEN '' ELSE CONCAT('<a href="$mediabase/$folder/',jfc.filename$i,'" />',jfc.filetitle$i,'</a>') END as filelink$i
SQL;
        }


        // default is to only show all events
        // If loading from a module then get the modules params from the registry
        $reg = JFactory::getConfig();
        $compparams = clone JComponentHelper::getParams(JRequest::getCmd("option", "com_jevents"));
        $modparams = $reg->get("jev.modparams", false);

        if ($modparams) {
            $compparams = $modparams;
        }

        $extraval = "";
        for ($extra = 0; $extra < 20; $extra++) {
            $extraval = $compparams->get("extras" . $extra, false);
            if (strpos($extraval, "jevhi:") === 0) {
                break;
            }
        }

        if ($extraval && strpos($extraval, "jevhi:") === 0) {

            $invalue = str_replace("jevhi:", "", $extraval);
            $invalue = intval(str_replace(" ", "", $invalue));

            if ($invalue == 1) {
                $extrawhere[] = " jfc.imagename1 is not null";
            } else if ($invalue == 0) {
                $extrawhere[] = " jfc.imagename1 is  null";
            }
        }
    }

    private
            function processUpload($uploadfile) {

        // Check for request forgeries
        JRequest::checkToken('request') or jexit('Invalid Token');

        $this->authorised(true);

        // Get the media component configuration settings
        $params = JComponentHelper::getParams('com_media');
        // Set the path definitions
        if (!defined('JEVP_MEDIA_BASE')) {
            define('JEVP_MEDIA_BASE', JPATH_ROOT . "/" . $params->get('image_path', 'images/stories'));
            define('JEVP_MEDIA_BASEURL', JURI::root(true) . '/' . $params->get('image_path', 'images/stories'));
        }

        include_once(dirname(__FILE__) . "/files/uploadhelper.php");
        $uploadhelper = new JevUploadHelper($this->params);
        $filename = $uploadfile;

        $files = JFactory::getApplication()->input->files;
        $filedata = $files->getArray();

        foreach ($filedata as $fname => $file) {
            if (strpos($fname, $filename) === 0) {
                // Joomla 3.6 is too strict on file uploads and considered zip files to be unsafe!
                $file = $files->get($fname, $file, "raw");

                if (strpos($uploadfile, "upload_image") === 0) {
                    $filename = $uploadhelper->processImageUpload($fname);
                    $filetype = "image";
                } else {
                    $filename = $uploadhelper->processFileUpload($fname, ".xml", explode(",", $this->params->get("allowedfiles", "csv,xml,pdf,doc,xls")));
                    $filetype = "file";
                }
                $oname = $file['name'];
                ?>
                <script  type="text/javascript">
                    var oname = "<?php echo $oname; ?>";
                    var fname = "<?php echo $fname; ?>";
                    var filename = "<?php echo $filename; ?>";
                    var filetype = "<?php echo $filetype; ?>";
                <?php if ($filetype == "image") {
                    ?>
                        window.parent.setImageFileName(fname, filename, oname);
                <?php
                } else {
                    ?>
                        window.parent.setLinkFileHref(fname, filename, oname);
                <?php } ?>
                </script>
                <?php
            }
        }
        exit();
        return;
    }

    private
            function erroralert($msg) {
        ?>
        <html>
            <head>
                <script  type="text/javascript">
                    alert("<?php echo $msg; ?>");
                </script>
            </head>
            <body>
            </body>
        </html>
        <?php
        exit();
    }

    private
            function authorised($kill = false) {
        $jevuser = JEVHelper::getAuthorisedUser();
        $params = JComponentHelper::getParams(JEV_COM_COMPONENT);
        $authorisedonly = $params->get("authorisedonly", 0);
        $user = JFactory::getUser();

        if ($user->id == 0 && $this->params->get("anonupload", 0)) {
            $plugin = JPluginHelper::getPlugin('jevents', 'jevanonuser');
            $pluginparams = new JRegistry($plugin->params);
            if ($pluginparams->get("recaptchapublic", false)) {
                $this->anonupload = true;
                return true;
            }
        }

        if ($authorisedonly) {
            if (is_null($jevuser)) {
                // Set the layout
                if ($kill)
                    $this->erroralert(JText::_("JEV_Not_authorised", true));
                return false;
            }
            else if (!$jevuser->canuploadimages && !$jevuser->canuploadmovies) {
                // Set the layout
                if ($kill)
                    $this->erroralert(JText::_("JEV_Not_authorised", true));
                return false;
            }
        }
        else {
            $userGroups = JFactory::getUser()->getAuthorisedGroups();
            if (!is_array($userGroups) || !is_array($this->params->get('upimageslevel', array(8))) || !is_array($this->params->get('upfileslevel', array(8)))) {
                $this->erroralert(JText::_("JEV_YOU_MUST_SET_PERMISSIONS_IN_JEVFILES_PLUGIN", true));
            }
            if (!array_intersect($this->params->get('upimageslevel', array(8)), array_values($userGroups)) && !array_intersect($this->params->get('upfileslevel', array(8)), array_values($userGroups))) {
                if ($kill)
                    $this->erroralert(JText::_("JEV_Not_authorised", true));
                return false;
            }
        }
        return true;
    }

    static
            function fieldNameArray($layout = 'detail') {
        $return = array();
        $return['group'] = JText::_("JEV_STANDARD_IMAGES_FILES", true);

        $labels = array();
        $values = array();

        JPluginHelper::importPlugin('jevents');
        $plugin = JPluginHelper::getPlugin("jevents", "jevfiles");
        $params = new JRegistry($plugin->params);
        $imagenos = intval($params->get("imnum", 1));
        $filenos = intval($params->get("filnum", 1));

        if ($layout == "edit") {
            if (count($imagenos) > 0) {
                // index from 1 !!!
                for ($i = 1; $i <= $imagenos; $i++) {
                    $label = JText::_("JEV_STANDARD_IMAGE_EDIT_" . $i);
                    if ($label == "JEV_STANDARD_IMAGE_EDIT_" . $i)
                        $label = JText::_("JEV_STANDARD_IMAGE_" . $i);
                    if ($label == "JEV_STANDARD_IMAGE_" . $i)
                        $label = JText::_("JEV_STANDARD_IMAGE_");
                    $labels[] = $label;
                    $values[] = "image$i";
                }

                for ($i = 1; $i <= $filenos; $i++) {
                    $label = JText::_("JEV_STANDARD_FILE_EDIT_" . $i);
                    if ($label == "JEV_STANDARD_FILE_EDIT_" . $i)
                        $label = JText::_("JEV_STANDARD_FILE_" . $i);
                    if ($label == "JEV_STANDARD_FILE_" . $i)
                        $label = JText::_("JEV_STANDARD_FILE");
                    $labels[] = $label;
                    $values[] = "file$i";
                }
            }

            $return['values'] = $values;
            $return['labels'] = $labels;
            return $return;
        }

        if (count($imagenos) > 0) {
            if ($layout == "detail") {
                // index from 1 !!!
                for ($i = 1; $i <= $imagenos; $i++) {
                    $labels[] = JText::_("JEV_STANDARD_IMAGE", true) . " " . $i;
                    $labels[] = JText::_("JEV_IMAGE_LINK", true) . " " . $i;
                    $labels[] = JText::_("JEV_STANDARD_THUMBNAIL", true) . " " . $i;
                    $labels[] = JText::_("JEV_STANDARD_POPUP", true) . " " . $i;
                    $labels[] = JText::_("JEV_IMAGE_TITLE", true) . " " . $i;
                    $values[] = "JEV_STANDARD_IMAGE_$i";
                    $values[] = "JEV_IMAGE_LINK_$i";
                    $values[] = "JEV_STANDARD_THUMBNAIL_$i";
                    $values[] = "JEV_STANDARD_POPUP_$i";
                    $values[] = "JEV_IMAGE_TITLE_$i";
                }
                for ($i = 1; $i <= $filenos; $i++) {
                    $labels[] = JText::_("JEV_STANDARD_FILE_TITLE", true) . " " . $i;
                    $labels[] = JText::_("JEV_STANDARD_FILE_LINK", true) . " " . $i;
                    $labels[] = JText::_("JEV_STANDARD_FILE_HREF", true) . " " . $i;

                    $values[] = "JEV_FILE_TITLE_$i";
                    $values[] = "JEV_FILE_LINK_$i";
                    $values[] = "JEV_FILE_HREF_$i";
                }
                $labels[] = JText::_("JEV_PINTEREST", true);
                $values[] = "PINTEREST";
            } else if ($layout == "list") {
                // index from 1 !!!
                for ($i = 1; $i <= $imagenos; $i++) {
                    $labels[] = JText::_("JEV_STANDARD_IMAGE", true) . " " . $i;
                    $labels[] = JText::_("JEV_IMAGE_LINK", true) . " " . $i;
                    $labels[] = JText::_("JEV_STANDARD_THUMBNAIL", true) . " " . $i;
                    $labels[] = JText::_("JEV_STANDARD_POPUP", true) . " " . $i;
                    $labels[] = JText::_("JEV_THUMBNAIL_LINK", true) . " " . $i;
                    $labels[] = JText::_("JEV_IMAGE_TITLE", true) . " " . $i;

                    $values[] = "JEV_LIST_IMAGE_$i";
                    $values[] = "JEV_IMAGE_LINK_$i";
                    $values[] = "JEV_LIST_THUMBNAIL_$i";
                    $values[] = "JEV_LIST_POPUP_$i";
                    $values[] = "JEV_THUMBLINK_$i";
                    $values[] = "JEV_IMAGE_TITLE_$i";
                }

                for ($i = 1; $i <= $filenos; $i++) {
                    $labels[] = JText::_("JEV_STANDARD_FILE_TITLE", true) . " " . $i;
                    $labels[] = JText::_("JEV_STANDARD_FILE_LINK", true) . " " . $i;
                    $labels[] = JText::_("JEV_STANDARD_FILE_HREF", true) . " " . $i;

                    $values[] = "JEV_FILE_TITLE_$i";
                    $values[] = "JEV_FILE_LINK_$i";
                    $values[] = "JEV_FILE_HREF_$i";
                }
            }
            // index from 1 !!!
            for ($i = 1; $i <= $imagenos; $i++) {
                $labels[] = JText::_("JEV_IMAGE_URL", true) . " " . $i;
                $labels[] = JText::_("JEV_THUMBNAIL_URL", true) . " " . $i;

                $values[] = "JEV_IMAGEURL_$i";
                $values[] = "JEV_THUMBURL_$i";
            }
            for ($i = 1; $i <= $imagenos; $i++) {
                $labels[] = JText::_("JEV_SIZED_IMAGE", true) . " " . $i;

                $values[] = "JEV_SIZEDIMAGE_$i;400x300";
            }
            for ($i = 1; $i <= $imagenos; $i++) {
                $labels[] = JText::sprintf("JEV_SIZED_IMAGE_URL", $i, array('jsSafe' => true));

                $values[] = "JEV_SIZEDIMAGE_URL_$i;400x300";
            }
        }

        // if called from a module then we must support old style codes     
        $callers = array(debug_backtrace()[1]['function'], debug_backtrace()[2]['function'], debug_backtrace()[3]['function']);
        
        if (in_array("processMatch", $callers)){
            for ($i = 1; $i <= $imagenos; $i++) {
                $labels[] = "";
                $values[] = "imageimg$i";
                $labels[] = "";
                $values[] = "thumbimg$i";
                $labels[] = "";
                $values[] = "imagethumb$i";
            }
            $labels[] = "";
            $values[] = "imageimg";
            $labels[] = "";
            $values[] = "thumbimg";
            $labels[] = "";
            $values[] = "imagethumb";
        }
        
        $return['values'] = $values;
        $return['labels'] = $labels;

        return $return;
    }

    // Special method called when plugins are saved
    // Have the image sizes changed ??
    /*
      static function onPluginBeforeSave($context, $article, $isNew)
      {
      if (!$isNew){
      $plugin = JPluginHelper::getPlugin("jevents", "jevfiles");
      if ($plugin)
      {
      $params = new JRegistry($plugin->params);
      }
      }
      $x = 1;
      }
     */
    
    // TODO move ALL of this over to scaled image code - also simplify queries and make sure I handle latest events module !
    static
            function substitutefield($row, $code) {

        if (isset($row->hidedetail) && $row->hidedetail){
            return "";
        }
        
        // Old style codes
        $code = str_replace(array("imageimg","thumbimg","imagethumb"), array("JEV_STANDARD_IMAGE_","JEV_LIST_THUMBNAIL_", "JEV_THUMBURL_"), $code);

        if (substr($code, strlen($code)-1)=="_"){
            $code .= "1";
        }
        $plugin = JPluginHelper::getPlugin("jevents", "jevfiles");
        $params = new JRegistry($plugin->params);

        if (strpos($code, "JEV_STANDARD_IMAGE_") === 0 || strpos($code, "JEV_IMAGEURL_") === 0 
                || strpos($code, "JEV_LIST_IMAGE_") === 0) {        

            $i = str_replace(array("JEV_STANDARD_IMAGE_","JEV_LIST_IMAGE_","JEV_IMAGEURL_"), "", $code);
            
            $name = "_imageurl" . $i;
            
            $img = plgJEventsjevfiles::getSizedImageUrl($row, $name, $params->get("imagew", 300)."x".$params->get("imageh", 225), $params);
            
            if (!$img){
                return "";
            }            
            if (strpos($code, "JEV_STANDARD_IMAGE_") === 0 || strpos($code, "JEV_LIST_IMAGE_") === 0) {
                $fname = "_imagetitle$i";
                if (!empty($row->$fname))
                    $filetitle = $row->$fname;
                else
                    $filetitle = "";
                
                return "<img src='" . $img . "' class='jev_image" . $i . "'  alt='" . htmlentities($filetitle) . "' />";
            } else {
                return $img;
            }
            
        }
        if (strpos($code, "JEV_SIZEDIMAGE_") === 0 || strpos($code, "JEV_SIZEDIMAGE_URL_") === 0) {
            if (strpos($code, ";") === false || strpos($code, "x") === false) {
                return "";
            }

            list($code, $dimensions) = explode(";", $code);
            $i = str_replace(array("JEV_SIZEDIMAGE_URL_", "JEV_SIZEDIMAGE_"), "", $code);

            $name = "_imageurl" . $i;

            $img = plgJEventsjevfiles::getSizedImageUrl($row, $name, $dimensions, $params);
            
            if (!$img){
                return "";
            }            

            $fname = "_imagetitle$i";
            if (!empty($row->$fname))
                $filetitle = $row->$fname;
            else
                $filetitle = $row->title();
            if (strpos($code, "JEV_SIZEDIMAGE_URL_") === 0) {
                return $img;
            } else {
                return "<img alt='" . htmlentities($filetitle) . "' src='" . $img . "' class='jev_image" . $i . "' />";
            }
        }
        if (strpos($code, "JEV_IMAGE_LINK_") === 0 || strpos($code, "JEV_THUMBLINK_") === 0) {
            $i = str_replace(array("JEV_IMAGE_LINK_","JEV_THUMBLINK_"), "", $code);
            $name = "_imageurl" . $i;
            if (strpos($code, "JEV_IMAGE_LINK_") === 0 ){
                $img = plgJEventsjevfiles::getSizedImageUrl($row, $name, $params->get("imagew", 300)."x".$params->get("imageh", 225), $params);     
            }
            else {
                $img = plgJEventsjevfiles::getSizedImageUrl($row, $name, $params->get("thumbw", 120)."x".$params->get("thumbh", 90), $params);     
            }            
            
            if (!$img){
                return "";
            }         
            
            $fname = "_imagetitle$i";
            if (!empty($row->$fname))
                $filetitle = $row->$fname;
            else
                $filetitle = "";

            if (strpos($code, "JEV_IMAGE_LINK_") === 0 ){
                $img = "<img src='" . $img . "' class='jev_image" . $i . "'  alt='" . htmlentities($filetitle) . "' />";
            }
            else {
                $img = "<img src='" . $img . "' class='jev_imagethumb" . $i . "'  alt='" . htmlentities($filetitle) . "' />";
            }

            // Title link
            $reg = JevRegistry::getInstance("jevents");
            static $datamodel;
            if (!isset($datamodel)) {
                $datamodel = $reg->getReference("jevents.datamodel", false);
                if (!$datamodel) {
                    $datamodel = new JEventsDataModel();
                }
            }

            $rowlink = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), false);
            $rowlink = JRoute::_($rowlink . $datamodel->getCatidsOutLink());
            ob_start();
            ?>
            <a class="ev_link_row" href="<?php echo $rowlink; ?>" style="font-weight:bold;" title="<?php echo JEventsHTML::special($row->title()); ?>">
            <?php echo $img; ?>
            </a>
            <?php
            $link = ob_get_clean();

            return $link;

        }
        if (strpos($code, "JEV_STANDARD_THUMBNAIL_") === 0 || strpos($code, "JEV_THUMBURL_") === 0
              || strpos($code, "JEV_LIST_THUMBNAIL_") === 0   ) {
            $i = str_replace(array("JEV_STANDARD_THUMBNAIL_","JEV_THUMBURL_", "JEV_LIST_THUMBNAIL_"), "", $code);
            $name = "_imageurl" . $i;
            
            $img = plgJEventsjevfiles::getSizedImageUrl($row, $name, $params->get("thumbw", 120)."x".$params->get("thumbh", 90), $params);
            
            if (!$img){
                return "";
            }            
            if (strpos($code, "JEV_STANDARD_THUMBNAIL_") === 0 || strpos($code, "JEV_LIST_THUMBNAIL_") === 0) {                
                $fname = "_imagetitle$i";
                if (!empty($row->$fname))
                    $filetitle = $row->$fname;
                else
                    $filetitle = "";

                return "<img src='" . $img . "' class='jev_imagethumb" . $i . "'  alt='" . htmlentities($filetitle) . "' />";
            } else {
                return $img;
            }
        }

        if (strpos($code, "JEV_STANDARD_POPUP") === 0 || strpos($code, "JEV_LIST_POPUP") === 0) {
            $i = str_replace(array("JEV_LIST_POPUP_","JEV_STANDARD_POPUP_"), "", $code);
            $name = "_imageurl" . $i;
            
            $thumb = plgJEventsjevfiles::getSizedImageUrl($row, $name, $params->get("thumbw", 120)."x".$params->get("thumbh", 90), $params);
            $img = plgJEventsjevfiles::getSizedImageUrl($row, $name, $params->get("imagew", 300)."x".$params->get("imageh", 225), $params);
            if (!$img || !$thumb){
                return "";
            }            
            
            $fname = "_imagetitle$i";
            if (!empty($row->$fname))
                $filetitle = $row->$fname;
            else
                $filetitle = "";
            
            JHTML::_('behavior.modal');
            static $cssdone = false;
            if (!$cssdone) {
                JFactory::getDocument()->addStyleDeclaration("#jevents_body .modal {  display: inherit;  position: relative;}");
                $cssdone = true;
            }
            $src = "<a class='modal' rel='{handler: \"image\",}' href='" . $img . "' ><img src='" . $thumb . "' class='jev_imagethumb" . $i . "' alt='" . htmlentities($filetitle) . "' /></a>";
            return $src;
        }

        if (strpos($code, "JEV_IMAGE_TITLE_") === 0) {
            $i = str_replace("JEV_IMAGE_TITLE_", "", $code);
            $name = "_imagetitle" . $i;
            if (isset($row->$name))
                return $row->$name;
        }

        if (strpos($code, "JEV_FILE_TITLE_") === 0) {
            $i = str_replace("JEV_FILE_TITLE_", "", $code);
            $name = "_filetitle" . $i;
            if (isset($row->$name))
                return $row->$name;
        }
        if (strpos($code, "JEV_FILE_LINK_") === 0) {
            $i = str_replace("JEV_FILE_LINK_", "", $code);
            $name = "_filelink" . $i;
            if (isset($row->$name))
                return $row->$name;
        }
        if (strpos($code, "JEV_FILE_HREF_") === 0) {
            $i = str_replace("JEV_FILE_HREF_", "", $code);
            $name = "_filehref" . $i;
            if (isset($row->$name))
                return $row->$name;
        }
        if ($code == "PINTEREST") {
            $name = "_imageurl1";
            if (!isset($row->$name) || $row->$name == "") {
                return "";
            }
            $reg = JevRegistry::getInstance("jevents");
            static $datamodel2;
            if (!isset($datamodel2)) {
                $datamodel2 = $reg->getReference("jevents.datamodel", false);
                if (!$datamodel2) {
                    $datamodel2 = new JEventsDataModel();
                }
            }

            $rowlink = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), false);
            $rowlink = JRoute::_($rowlink . $datamodel2->getCatidsOutLink());

            $url = urlencode(substr(JUri::base(), 0, -1) . $rowlink);
            $image = "&media=" . urlencode(substr(JUri::base(), 0, -1) . $row->$name);
            $title = urlencode($row->title());
            return '<div style="margin:5px 0px" class="jevpinterest"><a href="http://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $title . $image . '" class="pin-it-button" count-layout="horizontal">Pin It</a>
<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script></div>';
        }

        return "";
    }

    static public function getCategoryImageUrl($row) {
        $data = $row->getCategoryData();
        if (is_array($data)) {
            $data = $data[0];
        }
        if ($data) {
            $params = json_decode($data->params);
            if (isset($params->image) && $params->image != "") {
                return JURI::root() . $params->image;
            }
        }
    }

    /**
     * When editing a JEvents menu item/module can add additional menu constraints dynamically
     *
     */
    function onEditMenuItem(&$menudata, $value, $control_name, $name, $id, $param) {
        // already done this param
        if (isset($menudata[$id]))
            return;

        static $matchingextra = null;
        // find the parameter that matches jevtl: (if any)
        if (!isset($matchingextra)) {
            $params = $param->getGroup('params');
            foreach ($params as $key => $element) {
                $val = $element->value;
                if (strpos($key, "jform_params_extras") === 0) {
                    if (strpos($val, "jevhi:") === 0) {
                        $matchingextra = $key;
                        break;
                    }
                }
            }
            if (!isset($matchingextra)) {
                $matchingextra = false;
            }
        }

        // either we found matching extra and this is the correct id or we didn't find matching extra and the value is blank
        if (strpos($value, "jevhi:") === 0 || (($value == "" || $value == "0") && $matchingextra === false)) {
            $matchingextra = true;
            $invalue = str_replace(" ", "", $value);
            if ($invalue == "")
                $invalue = 'jevwd:-1';

            $options = array();
            $options[] = JHTML::_('select.option', 'jevhi:-1', JText::_('JEV_ANY_EVENTS'), 'id', 'title');
            $options[] = JHTML::_('select.option', 'jevhi:0', JText::_('JEV_WITH_NO_IMAGE'), 'id', 'title');
            $options[] = JHTML::_('select.option', 'jevhi:1', JText::_('JEV_WITH_IMAGE'), 'id', 'title');

            $input = JHTML::_('select.genericlist', $options, $name, '', 'id', 'title', $invalue, $name);

            $data = new stdClass();
            $data->name = "jevwithimage";
            $data->html = $input;
            $data->label = "JEVF_EVENTS_HAVE_IMAGE";
            $data->description = "JEVF_EVENTS_HAVE_IMAGE_DESC";
            $data->options = array();
            $menudata[$id] = $data;
        }
    }
    
    private static function getSizedImageUrl($row, $name, $dimensions, $params) {

        list($width, $height) = explode("x", $dimensions);

        $base = JUri::base();
        $basepath = JUri::base(true);
        $baseExAdmin = str_replace("/administrator", "", $base);
        $basepathExAdmin = str_replace("/administrator", "", $basepath);
        
        $img = false;
        if (!isset($row->$name) || $row->$name == "") {
            $defimage = $params->get("defaultimage", false);
            
            if ($params->get("defaultonlyforfirst", 0) && $name !== "_imageurl1") {
                $defimage = false;
            }
            
            if ($defimage) {
                $img = $defimage;
            } else {
                $catimage = $row->getCategoryImageUrl();
                if ($catimage) {
                    $img = $catimage;
                }
            }
            if (!$img)
                return false;
        }
        else {
            $img = $row->$name;
        }

        $imgfile = str_replace($base, "", $img);
        $imgfile = str_replace($basepath, "", $imgfile);
        $imgfile = str_replace($baseExAdmin, "", $imgfile);
        $imgfile = str_replace($basepathExAdmin, "", $imgfile);
        if (strpos($imgfile, "/") === 0) {
            $imgfile = substr($imgfile, 1);
        }

        // Get the media component configuration settings
        $mediaparams = JComponentHelper::getParams('com_media');
        // Set the path definitions
        if (!defined('JEVP_MEDIA_BASE')) {
            define('JEVP_MEDIA_BASE', JPATH_ROOT . '/' . $mediaparams->get('image_path', 'images/stories'));
            define('JEVP_MEDIA_BASEURL', JURI::root(true) . '/' . $mediaparams->get('image_path', 'images/stories'));
        }
        // folder relative to media folder
        $folder = $params->get("folder", "");
        if ($folder == "") {
            echo JText::_("JEV_SAVE_PLUGIN_PARAMETERS");
            return false;
        }

        // ensure folder exists
        jimport('joomla.filesystem.folder');
        $imgfile = str_replace($mediaparams->get('image_path', 'images/stories') . "/" . $folder . "/", "", $imgfile);
        if (!JFolder::exists(JEVP_MEDIA_BASE . "/" . $folder . "/" . $dimensions)) {
            JFolder::create(JEVP_MEDIA_BASE . "/" . $folder . "/" . $dimensions);
        }

        //Check if we are scaling?
        if ((int) $width === 0 && (int) $height === 0)
        {
            //We shouldn't be scalling 0 = no scaling.
            $src = JPATH_SITE . "/" . $imgfile;
                        
            if (!JFile::exists($src)) {
                jimport("joomla.image.image");
                if (JFile::exists(JEVP_MEDIA_BASE . "/" . $folder . "/originals/" .  basename($imgfile)))
                {                    
                    $src =  JEVP_MEDIA_BASE . "/" . $folder . "/originals/" . basename($imgfile);
                    $img = JEVP_MEDIA_BASEURL . "/" . $folder . "/originals/" . basename($imgfile);
                }
                else {
                    $src =  JEVP_MEDIA_BASE . "/" . $folder . "/" .  basename($imgfile);
                    $img = JEVP_MEDIA_BASEURL . "/" . $folder . "/" .  basename($imgfile);
                }

                if (!JFile::exists($src)) {
                    return false;
                }
                return $img;
            }
            else {
                $img = JURI::root(true) . "/".$imgfile;
                return $img;
            }
        }
        
	// use basename($imgfile) in case default image is not in the main path!
        $targetfile = JEVP_MEDIA_BASE . "/" . $folder . "/" . $dimensions . "/" . basename($imgfile);
        if (!JFile::exists($targetfile)) {
            jimport("joomla.image.image");
                        
            $src = JFile::exists(JEVP_MEDIA_BASE . "/" . $folder . "/originals/" .  basename($imgfile)) ? JEVP_MEDIA_BASE . "/" . $folder . "/originals/" .  basename($imgfile) : JEVP_MEDIA_BASE . "/" . $folder . "/" .  basename($imgfile);

            if (!JFile::exists($src) && JFile::exists(JPATH_SITE . "/" . $imgfile)) {
                $src = JPATH_SITE. "/" . $imgfile;
            } else if (!JFile::exists($src)) {
                return false;
            }

            // JImage uses isTransparent method which fails on alpha transparent PNG images :(
            try {
                $imageproperties = JImage::getImageFileProperties($src);
            }
            catch (Exception $e){
                return false;
            }
            $quality = $params->get("imagequality", 90);

            // Joomla image scaling doesn't work for alpha transparent PNG images
            if ($imageproperties->type == IMAGETYPE_PNG) {
                if ($quality >= 10) {
                    // PNG quality is up to 10
                    $quality = intval($quality / 10);
                    $quality = $quality > 9 ? 9 : $quality;
                }

                $img = @imagecreatefrompng($src);
                $old_x = imageSX($img); // width
                $old_y = imageSY($img); // height

                // preserve aspect ratio
                $thumbWidth = $width;
                $thumbHeight = intval($old_y * $thumbWidth / $old_x);
                if ($thumbHeight > $height)
                {
                        $thumbHeight = $height;
                        $thumbWidth = intval($old_x * $thumbHeight / $old_y);
                }
                $width = $thumbWidth;
                $height = $thumbHeight;
                
                $new_img = imageCreateTrueColor($width, $height);
                imagealphablending($new_img, false);
                $colorTransparent = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
                imagefill($new_img, 0, 0, $colorTransparent);
                imagesavealpha($new_img, true);
                imagecopyresampled($new_img, $img, 0, 0, 0, 0, $width, $height, $old_x, $old_y);
                imagepng($new_img, $targetfile, $quality);
                imagedestroy($new_img);
                imagedestroy($img);
            } else {
                $image = new JImage($src);
                $image = $image->resize($width, $height, true, JImage::SCALE_INSIDE);
                $image->toFile($targetfile, $imageproperties->type, array("quality" => $quality));
            }
        }
        $img = JEVP_MEDIA_BASEURL . "/" . $folder . "/" . $dimensions . "/" . basename($imgfile);

        return $img;
    }

}
