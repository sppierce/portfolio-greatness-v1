<?php
defined('_JEXEC') or die( 'Restricted access' );

JHTML::_('behavior.framework', true);
jimport('joomla.application.component.view');

class igalleryViewServerimport extends JViewLegacy
{
	function display($tpl = null)
	{
		$pathVar = JRequest::getVar('path');

        $regex = array('#[^ A-Za-z0-9:_\\\/-]#');
        $pathVar = preg_replace($regex, '', $pathVar);

		$fullPath = JPATH_SITE.$pathVar;
		
		$filesArray = JFolder::files($fullPath);
		$count = count($filesArray);
		
		for($i=0; $i<$count; $i++)
		{
			if(!igUploadHelper::checkExtension($filesArray[$i], false, false))
			{
				unset($filesArray[$i]);
			}
			else
			{
				$filesArray[$i] = $fullPath.'/'.$filesArray[$i];
				$filesArray[$i] = str_replace('\\','*',$filesArray[$i]);
			}
		}
		
		$filesArray = array_values($filesArray);
		
		$headJs = '
		window.addEvent(\'load\', function()
		{
		
		var fileNames = [';
		for($i=0;$i<count($filesArray);$i++)
		{
			$headJs .= '\''.$filesArray[$i].'\', ';
		}
		$headJs = substr($headJs,0,-2);
		$headJs .= ']
		
		';
		
		$headJs .= '
		
		var importClass = new Class
		({
			Implements: Options,
			options: {},
		
		    initialize: function(options)
		    {
		    	this.setOptions(options);
		    	this.index = 0;
		    	this.doRequest();
		    },
		    
		    doRequest: function()
    		{
				this.serverUrl = \'index.php?option=com_igallery&task=image.serverImport&format=raw&catid='.JRequest::getInt('catid').'&path=\' + this.options.fileNames[this.index];
				
				this.serverAjax = new Request({url:this.serverUrl, method: \'get\', 
				onComplete: function(response)
				{
					if(response != 1)
					{
						$(\'error_msg\').innerHTML = \'Error, response from server:<br /> \' + response;
						return;
					}
					
					$(\'fileProgress\').set(\'html\', this.index + 1);
					if(this.index < this.options.fileNames.length - 1)
					{
						this.index ++;
						this.doRequest();
					}
					else
					{
						window.parent.location = \'index.php?option=com_igallery&view=images&catid='.JRequest::getInt('catid').'\'
					}
				}.bind(this)
				});
				
				this.serverAjax.send();
			}
		    
		 })
		 
		 var importClass1 = new importClass({fileNames: fileNames});
		
		});';
		
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration($headJs);
		?>
		<p><?php echo JText::_('IMPORTING_IMAGES');?>...</p>
		<p><span id="fileProgress">1</span> &#47; <?php echo count($filesArray);?></p>
		<div id="error_msg"></div>
		
		<?php
		
			
	}
}
