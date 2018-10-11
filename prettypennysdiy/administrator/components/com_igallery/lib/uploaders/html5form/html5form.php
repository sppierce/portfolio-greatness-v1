<?php
defined('_JEXEC') or die('Restricted access');

class igUploadHtml5form
{
	function html5formHeadJs()
	{
		return;
	}
	
	function html5formHTML()
	{
		$params =& JComponentHelper::getParams('com_igallery');
		$moderate = $params->get('moderate_img', 0) == 1 && JFactory::getApplication()->isAdmin() == false ? 1 : 0;
		
		?>
		<form action="index.php?option=com_igallery" method="post" name="browserUploaderForm" enctype="multipart/form-data">
		
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_TITLE' ); ?></legend>
		
		<input name='uploads[]' type="file" multiple="true" onchange="listFiles()"/>
		<input type="hidden" name="task" value="images.browserUpload" />

		<input type="hidden" name="catid" value="<?php echo JRequest::getInt('catid',0); ?>" />
		<input type="hidden" name="moderateMsg" value="<?php echo $moderate; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
		
		<div class="clr"></div>
		<input type="submit" name="submit" value="<?php echo JText::_( 'JTOOLBAR_UPLOAD' ); ?>" />
		<div class="clr"></div>
		<p><?php echo JText::_( 'HOLD_CONTROL_SHIFT' ); ?></p>
		</fieldset>
		</form>
		<?php
	}
}