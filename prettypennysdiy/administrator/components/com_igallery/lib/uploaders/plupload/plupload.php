<?php
defined('_JEXEC') or die('Restricted access');

class igUploadPlupload
{
	function pluploadHeadJs()
	{
		$catid = JRequest::getInt('catid',0);
		$Itemid = JRequest::getInt('Itemid', null);
		$ItemIdString = empty($Itemid) ? '' : '&Itemid='.$Itemid;
		
		$params =& JComponentHelper::getParams('com_igallery');
		$resize = $params->get('client_resize', 1);
		$resizeWidth = $params->get('client_resize_width', 2000);
		$resizeHeight = $params->get('client_resize_height', 1500);
		$resizeQuality = $params->get('client_resize_quality', 90);

        if( $params->get('pl_runtime_first', 'html5') == 'flash')
        {
            $preferencesString = 'flash,html5';
        }
        else
        {
            $preferencesString = 'html5,flash';
        }
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet(IG_HOST.'media/com_igallery/plupload/css/plupload.queue.css');
		
		if(IG_J30)
        {
            if(JFactory::getApplication()->isSite() && $params->get('pl_include_jquery', 1) == 1)
            {
                $document->addScript(IG_HOST.'media/com_igallery/plupload/js/jquery.js');
            }
        }
        else
        {
            if(!JFactory::getApplication()->isSite() || $params->get('pl_include_jquery', 1) == 1)
            {
                $document->addScript(IG_HOST.'media/com_igallery/plupload/js/jquery.js');
            }
        }

        ?>
		<script type="text/javascript" src="<?php echo IG_HOST.'media/com_igallery/plupload/js/plupload.min.js';?>"></script>
		<script type="text/javascript" src="<?php echo IG_HOST.'media/com_igallery/plupload/js/jquery.plupload.queue.min.js';?>"></script>
		<script type="text/javascript" src="<?php echo IG_HOST.'media/com_igallery/plupload/js/plupload.html5.min.js';?>"></script>
		<script type="text/javascript" src="<?php echo IG_HOST.'media/com_igallery/plupload/js/plupload.flash.min.js';?>"></script>
		<?php
		
		$headJs = '
		jQuery.noConflict();
		
		jQuery(function()
		{
			jQuery("#plupload_div").pluploadQueue(
			{
				runtimes : \''.$preferencesString.'\',
				url : \'index.php?option=com_igallery&task=image.plUpload&catid='.$catid.'&format=raw\',
				max_file_size : \'20mb\',
				unique_names : false,
				preinit: attachCallbacks,
				multipart: true,
				filters : [{title : "Image files", extensions : "jpg,jpeg,gif,png"}],
				flash_swf_url : \''.IG_HOST.'media/com_igallery/plupload/js/plupload.flash.swf\'';
				
				if($resize == 1)
				{
				$headJs .= ',
		 		resize : {width : '.$resizeWidth.', height : '.$resizeHeight.', quality : '.$resizeQuality.'}';
				}
				$headJs .= '
			});
		});
		
		function attachCallbacks(Uploader)
		{
			Uploader.bind(\'FileUploaded\', function(Up, File, Response)
			{
				if(Response.response.length > 2)
				{
		        	alert(\'Upload Error, Adjusting the uploader settings in the component paramaters -> upload tab may help, response from server: \' + Response.response);
		        	Up.trigger("Error", {message : Response.response, code : 9999, details : Response.response, file: File});
		        	Uploader.stop();
		        	return false;
				}
					
				if( (Uploader.total.uploaded + 1) >= Uploader.files.length)
				{
					window.location = \'index.php?option=com_igallery&view=images&catid='.$catid.$ItemIdString.'\';
				}
		
	    	});
		};
		';
		?><script type="text/javascript"><?php echo $headJs; ?></script>
		<?php		
	}
	
	function pluploadHTML()
	{
		?>
		<div id="plupload_div" style="width: 450px; height: 330px;">Plupload Initialising...</div>
		<?php
	}
}