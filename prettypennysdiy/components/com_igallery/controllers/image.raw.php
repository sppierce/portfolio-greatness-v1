<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class igControllerImage extends JControllerLegacy
{
	function __construct($config = array())
	{
		$config['base_path'] = JPATH_SITE.'/components/com_igallery';
		parent::__construct($config);
	}
	
	public function &getModel($name = 'Image', $prefix = 'IgModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function addHit()
	{
		$id = JRequest::getInt('id',0);
		$model = $this->getModel();
	
		if( !$model->addHit($id) ) 
		{
			echo $model->getError();
		}
		else
		{
			echo 1;
		}
	}
	
	function download()
	{
	    $linkSource = JRequest::getCmd('type', 'main');
	    
	    $model = $this->getModel();
	    $photo = $model->getPhoto( JRequest::getInt('id', 0));
	    $category = $model->getCategory($photo->gallery_id);
	    $profile = $model->getProfile($category->profile);
	    
	    if($linkSource == 'lbox')
	    {
	        $imageType = $profile->lbox_download_image;
	    }
	    else
	    {
	        $imageType = $profile->download_image;
	    }
	    
	    if($imageType == 'none')
	    {
	        echo JText::_('JERROR_ALERTNOAUTHOR');
	        return;
	    }
	    
	    switch($imageType)
	    {
            case 'large':
                $fileDetails = igFileHelper::originalToResized($photo->filename, $profile->max_width, 
		        $profile->max_height, $profile->img_quality, $profile->crop_main, $photo->rotation, $profile->round_large, $profile->round_fill, 
    		    $profile->watermark, $profile->watermark_text, $profile->watermark_text_color, $profile->watermark_text_size, $profile->watermark_filename,
    		    $profile->watermark_position, $profile->watermark_transparency, 0);
		        
		        $path = IG_RESIZE_PATH.'/'.$fileDetails['folderName'].'/'.$fileDetails['fullFileName'];
		        break;
		        
            case 'lightbox':
                $fileDetails = igFileHelper::originalToResized($photo->filename, $profile->lbox_max_width,
    		    $profile->lbox_max_height, $profile->img_quality, $profile->crop_lbox, $photo->rotation, $profile->round_large, $profile->round_fill,
    		    $profile->watermark, $profile->watermark_text, $profile->watermark_text_color, $profile->watermark_text_size,  $profile->watermark_filename,
    		    $profile->watermark_position, $profile->watermark_transparency, 0);
    		    
    		    $path = IG_RESIZE_PATH.'/'.$fileDetails['folderName'].'/'.$fileDetails['fullFileName'];
    		    break;
    		    
            default:
                $increment = igFileHelper::getIncrementFromFilename($photo->filename);
	            $folderName = igFileHelper::getFolderName($increment);
	            
	            $path = IG_ORIG_PATH.'/'.$folderName.'/'.$photo->filename;
        }

        
        if( file_exists($path) ) 
        {
            preg_match_all('/-[0-9]+/', $photo->filename, $matches);
            $filename =  str_replace( $matches[0][0], '', $photo->filename );
            
	        header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$filename );
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            ob_clean();
            flush();
            readfile($path);
            exit;
        }
        else
        {
            echo JText::_('FILESYSTEM_CANNOT_FIND_SOURCE_FILE');
        }
	}

	function addRating()
	{
        $model = $this->getModel();

        if( !$model->addRating() )
        {
			?>
            {
                success: 0,
                message: "<?php echo $model->getError(); ?>"
            }
            <?php
		}
		else
		{
			?>
            {
                success: 1,
                message: "<?php echo JText::_('SUCCESS_VOTE_MESSAGE'); ?>",
                average: <?php echo $model->getRatingAverage(); ?>
            }
            <?php
		}
    }
}
