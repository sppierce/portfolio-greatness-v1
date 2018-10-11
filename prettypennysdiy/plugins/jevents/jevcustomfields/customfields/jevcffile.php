<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

include_once(JPATH_SITE."/plugins/jevents/jevcustomfields/customfields/JevcfFieldText.php");

class JFormFieldJevcffile extends JevcfFieldText
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'jevcffile';
	const name = 'jevcffile';

	public static function loadScript($field=false){
		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcftext.js');

		if ($field){
			$id = 'field'.$field->fieldname;
		}
		else {
			$id = '###';
		}
		ob_start();
?>
<div class='jevcffieldinput'>

	<?php
        CustomFieldsHelper::fieldtype($id, $field, self::name );
	CustomFieldsHelper::hidden($id,  $field, self::name);
	CustomFieldsHelper::label($id,  $field, self::name);
        CustomFieldsHelper::name($id, $field, self::name);
	CustomFieldsHelper::tooltip($id,  $field);
	?>

	<div class="jevcflabel">
		<?php echo JText::_("JEVCF_DEFAULT_VALUE");?>
		<?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_DEFAULT_VALUE_DESC'));?>
	</div>
	<div class="jevcfinputs">
		<input type="text" name="dv[<?php echo $id;?>]" id="dv<?php echo $id;?>" size="<?php echo $field?$field->attribute('size'):5;?>"  value="<?php echo $field?$field->attribute('default'):"";?>"  onchange="jevcftext.setvalue('<?php echo $id;?>');"  onkeyup="jevcftext.setvalue('<?php echo $id;?>');"/>
	</div>
	<div class="jevcfclear"></div>

	<?php
	CustomFieldsHelper::required($id,  $field);
	CustomFieldsHelper::requiredMessage($id,  $field);
	CustomFieldsHelper::conditional($id,  $field);
	CustomFieldsHelper::allowoverride($id,  $field);
	CustomFieldsHelper::applicableCategories($id, $field) ;
	CustomFieldsHelper::accessOptions($id,  $field);
	CustomFieldsHelper::readaccessOptions($id,  $field);
        CustomFieldsHelper::fieldclass($id, $field);
	CustomFieldsHelper::universal($id, $field);
	?>

	<div class="jevcfclear"></div>

</div>
<div class='jevcffieldpreview'  id='<?php echo $id;?>preview'>
	<div class="previewlabel"><?php echo JText::_("JEVCF_PREVIEW");?></div>
	<div class="jevcflabel jevcfpl" id='pl<?php echo $id;?>' ><?php echo $field?$field->attribute('label'):JText::_("JEVCF_FIELD_LABEL");?></div>
	<input type="text"  id="pdv<?php echo $id;?>" value="<?php echo $field?$field->attribute('default'):"";?>" size="<?php echo $field?$field->attribute('size'):5;?>"  />
</div>
<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id,  $field, $html, self::name	);

	}


	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		
		$plugin = JPluginHelper::getPlugin('jevents', 'jevfiles');
		if (!$plugin)
			return "<strong>" . JText::_("Please install the JEV Files Addon") . "</strong>";

		// intercept this to save the file
		if (isset($_FILES) && count($_FILES) > 0)
		{

			$hasfile = false;
			$uploadfile = "";
			foreach ($_FILES as $key => $val)
			{
				if ((strpos($key, "upload_cfile") === 0) && $val["size"] > 0)
				{
					$hasfile = true;
					$uploadfile = $key;
					break;
				}
			}

			if ($hasfile)
			{
				return $this->processUpload($uploadfile);
			}
		}

		JFactory::getLanguage()->load('plg_jevents_jevfiles', JPATH_ADMINISTRATOR);

		$this->params = new JRegistry($plugin->params);

		// Get the media component configuration settings
		$params =  JComponentHelper::getParams('com_media');
		// Set the path definitions
		if (!defined('JEVP_MEDIA_BASE'))
		{
			define('JEVP_MEDIA_BASE', JPATH_ROOT . "/" . $params->get('image_path', 'images' . "/" . 'stories'));
			define('JEVP_MEDIA_BASEURL', JURI::root() . $params->get('image_path', 'images/stories'));
		}

		// folder relative to media folder
		$folder = $this->params->get("folder", "");
		if ($folder == "")
		{
			echo JText::_("JEV_SAVE_PLUGIN_PARAMETERS");
			return;
		}
		// ensure folder exists
		if (!JFolder::exists(JEVP_MEDIA_BASE . "/" . $folder))
		{
			JFolder::create(JEVP_MEDIA_BASE . "/" . $folder);
		}

		static $setscripts;
		static $result;
		if (!isset($setscripts))
		{
			$setscripts = 1;
			// need session id to ensure login is maintained
			$session = JFactory::getSession();
			$mainframe = JFactory::getApplication();
			$option = JRequest::getCmd("option", "com_jevents");

			if ($option == "com_jevents")
			{
				if (JFactory::getApplication()->isAdmin())
				{
					$targetURL = JURI::root() . 'administrator/index.php?&option=com_jevents&folder=' . $folder . '&' . $session->getName() . '=' . $session->getId() . '&' . JSession::getFormToken() . '=1';
				}
				else
				{
					$targetURL = JURI::root() . 'index.php?task=day.listevents&option=com_jevents&tmpl=component&folder=' . $folder . '&' . $session->getName() . '=' . $session->getId() . '&' . JSession::getFormToken() . '=1';
				}
			}
			else if ($option == "com_jevlocations")
			{
				$targetURL = JURI::root() . (JFactory::getApplication()->isAdmin() ? "administrator/" : "") . 'index.php?task=locations.edit&option=com_jevlocations&tmpl=component&folder=' . $folder . '&' . JSession::getFormToken() . '=1';
			}
			else if ($option == "com_jevpeople")
			{
				$targetURL = JURI::root() . (JFactory::getApplication()->isAdmin() ? "administrator/" : "") . 'index.php?task=people.edit&option=com_jevpeople&tmpl=component&folder=' . $folder . '&' . JSession::getFormToken() . '=1';
			}
			$uploaderInit = "
			var oldAction = '';
			var oldTarget = '';
			var oldTask = '';
			var oldOption = '';
                        var oldEnctype = '';
			function uploadFileTypeCustomField(field){
				if ('" . $option . "'=='com_jevents') form = document.updateattendance || document.adminForm;
				else form = document.adminForm;
				oldAction = form.action;
				oldTarget = form.target;
                                oldTask = form.task.value;
                                oldEnctype = form.enctype;
                                if (oldTask == 'icalevent.savetranslation'){ 
                                    form.task.value = 'icalevent.translate';
                                    form.enctype = 'multipart/form-data';
                                }

				form.action = '" . $targetURL . "&field='+field;
				
				form.target = 'uplaodtargetcf';
				form.submit();
				form.action = oldAction ;
				form.target = oldTarget ;
                                form.task.value = oldTask ;
                                form.enctype = oldEnctype;
				
				var loading = document.getElementById(field+'_loading');
				loading.style.display='block';
				var loaded = document.getElementById(field+'_loaded');
				loaded.style.display='none';
			}
			
			function clearFileCustom(elemname){
				img = document.getElementById(elemname+'_link');
				img.href = ''
				img.innerHTML='';
				img.style.display='none';
				elem = document.getElementById('custom_'+elemname);
				if (elem) elem.value = '';
				elem = document.getElementById('jform_'+elemname);
				if (elem) elem.value = '';
			}
			
			function setLinkFileHrefCustom(){			
				var myiframe = frames.uplaodtargetcf;
				if(!myiframe.fname) return;
				
				elemname = myiframe.fname.substr(0,myiframe.fname.length-5);
				elem = document.getElementById('custom_'+elemname);
				if (elem) elem.value = myiframe.filename+'|'+myiframe.oname;
				elem = document.getElementById('jform_'+elemname);
				if (elem) elem.value = myiframe.filename+'|'+myiframe.oname;
				elem = document.getElementById(myiframe.fname);
				if (elem) elem.value = '';
				mylink = document.getElementById(elemname+'_link');
				mylink.href = '" . JEVP_MEDIA_BASEURL . "/$folder/'+myiframe.filename;
				mylink.innerHTML = myiframe.oname;
				
				var loading = document.getElementById(elemname+'_loading');
				loading.style.display='none';
				var loaded = document.getElementById(elemname+'_loaded');
				loaded.style.display='block';			
			}		
			
			";
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($uploaderInit);

			$result = '<iframe src="about:blank" style="display:none" name="uplaodtargetcf" id="uplaodtargetcf"></iframe>';
		}
		else
		{
			$result = "";
		}

		if (is_string($value) && strpos($value, "|") > 0)
		{
			list($filename, $filetitle) = explode("|", $value);
		}
		else if (is_array($value) && count($value) == 2)
		{
			list($filename, $filetitle) = $value;
			$value = implode("|", $value);
		}
		else
		{
			$filename = $value;
			$filetitle = $value;
		}

		if ($filename)
		{
			$href = JURI::root() . JEVP_MEDIA_BASEURL . "/$folder/$filename";
		}
		else
		{
			$href = "about:blank";
		}

		$name = (string) $this->attribute("name");
		$fieldname = "upload_cfile_" . $name;
		$result .= '<a id="' . $fieldname . '_link" href="' . $href . '" style="float:left;margin-right:10px;" target="_blank">' . ($filename ? $filetitle : "") . "</a>";
		$result .= JHTML::_('form.token');
                  $result .= '<input type="hidden" name="'.$this->name.'" id="jform_'.$fieldname.'" value="'.$value.'" size="50"/>';
		$result .= '<input type="hidden" name="custom_' . $name . '" id="custom_' . $fieldname . '" value="' . $value . '" size="50"/>';
		$result .= '<label for="' . $fieldname . '_file">' . JText::sprintf("JEV_UPLOAD_FILE", number_format($this->params->get("maxupload", 1000000) / 1000000, 2)) . '</label><br/>';
		$result .= '<span><input type="file" name="' . $fieldname . '_file" id="' . $fieldname . '_file" size="50"/></span>';
		$result .= ' <input type="button" onclick="uploadFileTypeCustomField(\'' . $fieldname . '\')" value="' . JText::_("jev_upload") . '"/> ';
		$result .= '<input type="button" onclick="clearFileCustom(\'' . $fieldname . '\')" value="' . JText::_("jev_Delete") . '"/>';
		$result .= '<div id="' . $fieldname . '_loading" class="loading" style="display:none">' . JText::_("JEV_UPLOADING_FILE_WAIT") . '</div>';
		$result .= '<div id="' . $fieldname . '_loaded" class="loaded" style="display:none">' . JText::_("JEV_UPLOAD_COMPLETE") . '</div>';
		$result .= '<br style="clear:both"/>';


		return $result;
	}

	public function convertValue($value, $node)
	{

		static $folder;
		if (!isset($folder))
		{
			// Get the media component configuration settings
			$mediaparams =  JComponentHelper::getParams('com_media');
			// Set the path definitions
			if (!defined('JEVP_MEDIA_BASE'))
			{
				define('JEVP_MEDIA_BASE', JPATH_ROOT . "/" . $mediaparams->get('image_path', 'images' . "/" . 'stories'));
				define('JEVP_MEDIA_BASEURL', JURI::root() . $mediaparams->get('image_path', 'images/stories'));
			}

			$plugin = JPluginHelper::getPlugin('jevents', 'jevfiles');
			if (!$plugin)
				return "";
			$params = new JRegistry($plugin->params);
			// folder relative to media folder
			$folder = $params->get("folder", "");
			if ($folder == "")
			{
				echo JText::_("JEV_SAVE_PLUGIN_PARAMETERS");
				return;
			}
			// ensure folder exists
			if (!JFolder::exists(JEVP_MEDIA_BASE . "/" . $folder))
			{
				JFolder::create(JEVP_MEDIA_BASE . "/" . $folder);
			}
		}
		if (strpos($value, "|") > 0)
		{
			list($filename, $filetitle) = explode("|", $value);
			return "<a href='" . JEVP_MEDIA_BASEURL . "/" . $folder . "/" . $filename . "' target='_blank'>" . $filetitle . "</a>";
		}
		else if (is_array($value) && count($value)==2){
			list($filename, $filetitle) = $value;
			return "<a href='" . JEVP_MEDIA_BASEURL . "/" . $folder . "/" . $filename . "' target='_blank'>" . $filetitle . "</a>";			
		}
		return $value;

	}

	private function processUpload($uploadfile)
	{

		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Get the media component configuration settings
		$mediaparams =  JComponentHelper::getParams('com_media');
		// Set the path definitions
		if (!defined('JEVP_MEDIA_BASE'))
		{
			define('JEVP_MEDIA_BASE', JPATH_ROOT . "/" . $mediaparams->get('image_path', 'images' . "/" . 'stories'));
			define('JEVP_MEDIA_BASEURL', JURI::root() . $mediaparams->get('image_path', 'images/stories'));
		}

		if (version_compare(JVERSION, "1.6.0", 'ge'))
		{
			$pluginhelper = JPATH_SITE . "/plugins/jevents/jevfiles/files/uploadhelper.php";
		}
		else
		{
			$pluginhelper = JPATH_SITE . "/plugins/jevents/files/uploadhelper.php";
		}
		include_once($pluginhelper);
		$plugin = JPluginHelper::getPlugin("jevents", "jevfiles");
		$jevfilesparams = new JRegistry($plugin->params);
		$uploadhelper = new JevUploadHelper($jevfilesparams);
		$filename = $uploadfile;
		foreach ($_FILES as $fname => $file)
		{
			if ((strpos($fname, "upload_cfile") === 0) && $file["size"] > 0)
			{
				$filename = $uploadhelper->processFileUpload($fname, ".xml", explode(",", $jevfilesparams->get("allowedfiles", "csv,xml,pdf,doc,xls")));
				$filetype = "file";
				$oname = $_FILES[$fname]['name'];
				?>
				<script  type="text/javascript">
					var oname = "<?php echo $oname; ?>";
					var fname = "<?php echo $fname; ?>";
					var filename = "<?php echo $filename; ?>";
					var filetype = "<?php echo $filetype; ?>";
				<?php if ($filetype == "image")
				{ ?>
						window.parent.setImageFileNameCustom();
					<?php
				}
				else
				{
					?>
							window.parent.setLinkFileHrefCustom();
				<?php } ?>
				</script>
				<?php
			}
		}
		return;

	}

	/**
	 * Magic setter; allows us to set protected values
	 * @param string $name
	 * @return nothing
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	public function bindField($fieldid)	{
		include_once("JevcfField.php");
		return JevcfField::bindFieldWithVarkeys($fieldid, get_object_vars($this));
	}

}