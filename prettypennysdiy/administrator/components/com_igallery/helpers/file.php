<?php
defined('_JEXEC') or die('Restricted access');

class igFileHelper
{
    function processUploadedImage($fileName, $tmpPath, $uploadError, $tableName, $refresh)
    {
	if(!$uploadedFile = igUploadHelper::upload_file($fileName, $tmpPath, $uploadError, IG_TEMP_PATH, $refresh) )
	{
		return false;
	}

	$params =& JComponentHelper::getParams('com_igallery');

	//get the folder path
	$increment = igFileHelper::getFileIncrement($tableName);
	$folderName = igFileHelper::getFolderName($increment);
	igFileHelper::makeFolder(IG_ORIG_PATH.'/'.$folderName);
	$destFolderPath = IG_ORIG_PATH.'/'.$folderName;

	//make the filename
	$destFile = igFileHelper::addIncrement($uploadedFile, $increment);
	$destFile = igFileHelper::checkUniqueName($destFolderPath, $destFile);

	//make the folder paths
	$sourcePath = IG_TEMP_PATH.'/'.$uploadedFile;
	$destPath = $destFolderPath.'/'.$destFile;

	$imageinfo = getimagesize($sourcePath);
	$maxServerWidth = $params->get('server_max_width', 2100);
	$maxServerHeight = $params->get('server_max_height', 1600);

	if( $imageinfo[0] > $maxServerWidth || $imageinfo[1] > $maxServerHeight)
	{
		if(!igFileHelper::makeImage($sourcePath, $destPath, $maxServerWidth, $maxServerHeight, 95, $refresh) )
		{
			return false;
		}
	}
	else
	{
		if(!JFile::copy($sourcePath, $destPath))
		{
			igFileHelper::raiseError($sourcePath.' -> '.$destPath .' '. JText::_( 'Error Moving File To Directory' ), $refresh);
			return false;
		}
	}

	$fileData = array();
	$fileData['filename'] = $destFile;

	if($params->get('import_exif_data', 0) == 1)
	{
		$fileData['exif_des'] = igFileHelper::getExifData($sourcePath);
	}
		
	if($params->get('import_iptc_data', 0) == 1)
	{
		$fileData['iptc_des'] = igFileHelper::getIptcData($sourcePath);
	}

	if(JFile::exists($sourcePath))
	{
		JFile::delete($sourcePath);
	}

	return $fileData;

    }
    
    function processImportedImage($filePath)
    {
    	$fullPath = $filePath;
    	
    	if( !igUploadHelper::checkExtension($fullPath, false) )
		{
		    return false;
		}
		
    	if( !igUploadHelper::checkIsImage($fullPath, false) )
		{
		    return false;
		}
		
		$slash = strrpos($fullPath, DIRECTORY_SEPARATOR) + 1;
		$filename = substr($fullPath, $slash);
		$filename = igUploadHelper::replaceSpecial($filename);
		
		//get the folder path
		$increment = igFileHelper::getFileIncrement('igallery_img');
		$folderName = igFileHelper::getFolderName($increment);
		igFileHelper::makeFolder(IG_ORIG_PATH.'/'.$folderName);
        $destFolderPath = IG_ORIG_PATH.'/'.$folderName;

        //make the filename
        $destFile = igFileHelper::addIncrement($filename, $increment);
        $destFile = igFileHelper::checkUniqueName($destFolderPath, $destFile);
		$destPath = $destFolderPath.'/'.$destFile;
		
    	$imageinfo = getimagesize($fullPath);
    	$params =& JComponentHelper::getParams('com_igallery');
        $maxServerWidth = $params->get('server_max_width', 2100);
        $maxServerHeight = $params->get('server_max_height', 1600);
        
        if( $imageinfo[0] > $maxServerWidth || $imageinfo[1] > $maxServerHeight)
        {
	        if(!igFileHelper::makeImage($fullPath, $destPath, $maxServerWidth, $maxServerHeight, 95, false) )
			{
			    return false;
			}
        }
        else
        {
	        if(!JFile::copy($fullPath, $destPath))
			{
				igFileHelper::raiseError($fullPath.' -> '.$destPath .' '. JText::_( 'Error Moving File To Directory' ), false);
				return false;
			}
        }

		$fileData = array();
		$fileData['filename'] = $destFile;

		if($params->get('import_exif_data', 0) == 1)
		{
		    $fileData['exif_des'] = igFileHelper::getExifData($fullPath);
		}
		
    	if($params->get('import_iptc_data', 0) == 1)
		{
		    $fileData['iptc_des'] = igFileHelper::getIptcData($fullPath);
		}

		return $fileData;

    }

	function originalToResized($fileName, $width, $height, $quality, $crop, $rotation, $round, $roundFill,
    $watermark=0, $wmText='', $wmTextColor='', $wmTextSize='', $wmFilename='', $wmPosition='', $wmTrans=100, $filesize=0)
    {
        $increment = igFileHelper::getIncrementFromFilename($fileName);
	    $folderName = igFileHelper::getFolderName($increment);
	    $fullFileName = igFileHelper::makeFileName($fileName, $width, $height, $quality, $crop, $rotation,
	    $round, $roundFill, $watermark, $wmPosition, $wmTrans, $wmText, $wmTextColor, $wmTextSize, $wmFilename);

		$sourceFile = IG_ORIG_PATH.'/'.$folderName.'/'.$fileName;
		$destFile = IG_RESIZE_PATH.'/'.$folderName.'/'.$fullFileName;
		
		if(! JFile::exists($destFile) )
        {
            if(! JFile::exists($sourceFile) )
            {
                igFileHelper::raiseError('The source file: '.$sourceFile. ' does not exist, please delete/reupload this image.', true);
                return true;
            }

            igFileHelper::makeFolder(IG_RESIZE_PATH.'/'.$folderName);

            if(!igFileHelper::makeImage($sourceFile, $destFile, $width, $height, $quality, true,
            $crop, $rotation, $round, $roundFill, $watermark, $wmText, $wmTextColor, $wmTextSize, $wmFilename, $wmPosition, $wmTrans) )
            {
                return false;
            }
        }

		$imgSize = getimagesize($destFile);

		$fileArray = array();
		$fileArray['fullFileName'] = $fullFileName;
		$fileArray['folderName'] = $folderName;
		$fileArray['width'] = $imgSize[0];
		$fileArray['height'] = $imgSize[1];

		if($filesize == 1)
		{
		   $size = filesize($destFile);
		   $fileArray['filesize'] = $size;
		}

		return $fileArray;
    }

    function deleteImage($fileName, $deleteOrig)
    {
        $increment = igFileHelper::getIncrementFromFilename($fileName);
    	$folderName = igFileHelper::getFolderName($increment);
    	
        if($deleteOrig)
        {
            $path = IG_ORIG_PATH.'/'.$folderName.'/'.$fileName;
    	    JFile::delete($path);
        }
        
        $OrigPattern = JFile::stripExt($fileName);
        $OrigPatternLength = strlen($OrigPattern);
        
        $resizedFolder = IG_RESIZE_PATH.'/'.$folderName;
        $filesArray = JFolder::files($resizedFolder);
        
        for($i=0; $i<count($filesArray); $i++)
        {
            if( substr($filesArray[$i], 0, $OrigPatternLength) == $OrigPattern)
            {
                JFile::delete($resizedFolder.'/'.$filesArray[$i]);
            }
        }
        
    }
    
    function getFileIncrement($tableName)
	{
	    $db =& JFactory::getDBO();
	    $prefix = $db->getPrefix();
	    $query =  "SHOW TABLE STATUS LIKE '".$prefix.$tableName."'";
	    $db->setQuery($query);
        $row = $db->loadObject();
        return $row->Auto_increment;
	}

    function getIncrementFromFilename($fileName)
	{
	    preg_match_all('/-[0-9]+/',$fileName, $matches);
	    $last = array_pop($matches[0]);
	    $increment =  str_replace( '-', '', $last );
	    return $increment;
	}

	function getFolderName($folderRef)
	{
	    $start = floor( ($folderRef/100) - 0.001) * 100;
        $end = ceil($folderRef /100 ) * 100;
        $folderName = ( ($start ) + 1).'-'.$end;

	    return $folderName;
	}

	function makeFolder($folderPath)
    {
    	jimport('joomla.filesystem.folder');

        if ( !JFolder::exists($folderPath) )
    	{
    		if( !JFolder::create($folderPath, 0755) )
    		{
    			JError::raise(2, 500, $folderPath .' '. JText::_( 'FOLDER_CREATE_ERROR' ) );
    			return false;
    		}
    	}
    }

	function makeFileName($fileName, $width, $height, $quality, $crop, $rotation, $round, $roundFill, 
    $watermark, $wmPosition, $wmTrans, $wmText, $wmTextColor, $wmTextSize, $wmFilename)
	{
	    jimport('joomla.filesystem.file');
	    $fileNameExt = JFile::getExt($fileName);
		$fileNameNoExt = JFile::stripExt($fileName);
        
		$fullFileName = $fileNameNoExt.'-'.$width.'-'.$height.'-'.$quality;

		if($crop == 1)
		{
		   $fullFileName.= '-c';
		}
		
		if($round == 1)
		{
		    $fullFileName.= '-rd';
		   
			if( preg_match("/jp/i", $fileNameExt) )
			{
				$fullFileName.= '-'.str_replace(',','-',$roundFill);
			}
		}
		
		if($rotation != 0)
		{
		   $fullFileName.= '-r'.$rotation;
		}
		
		if($watermark == 1 && ( strlen($wmFilename) > 1 || strlen($wmText) > 1) )
		{
			$fullFileName.= '-wm'.'-'.$wmPosition.'-'.$wmTrans;
			
			if(strlen($wmFilename) > 1)
			{
		    	$fullFileName.= '-'.preg_replace('/[^A-Za-z0-9]/', '', $wmFilename);
			}
			if(strlen($wmText) > 1)
			{
				$fullFileName.= '-'.preg_replace('/[^A-Za-z0-9]/', '', $wmText).'-'.str_replace(',','-',$wmTextColor).'-'.$wmTextSize;
			}
		}

		$fullFileName .= '.'.$fileNameExt;

		return $fullFileName;
	}

	function addIncrement($file,$increment)
	{
	    $fileNameExt = JFile::getExt($file);
		$fileNameNoExt = JFile::stripExt($file);

		return $fileNameNoExt.'-'.$increment.'.'.$fileNameExt;
	}

	function checkUniqueName($folderPath, $file)
	{
        if(JFile::exists($folderPath.'/'.$file) )
		{
		    $increment = igFileHelper::getIncrementFromFilename($file);
		    $fileNameExt = JFile::getExt($file);
		    $fileNameNoExt = JFile::stripExt($file);
		    $fileNameNoIncrement = substr($fileNameNoExt, 0, strrpos($fileNameNoExt, '-'));

		    $i=1;
			while(JFile::exists($folderPath.'/'.$fileNameNoIncrement.$i.'-'.$increment.'.'.$fileNameExt) )
			{
				$i++;
			}
			$file = $fileNameNoIncrement.$i.'-'.$increment.'.'.$fileNameExt;
		}

		return $file;
	}

	function getExifData($sourceFile)
	{
	    $exifDescription = '';
	    $ext = JFile::getExt($sourceFile);
	    
	    if( preg_match("/jp/i", $ext) )
    	{
    	    $exifData = exif_read_data($sourceFile);
    	    $match = false;
    
    		if( isset($exifData['ImageDescription']))
    	    {
    	        if(strlen($exifData['ImageDescription']) > 0)
    	        {
    	            $exifDescription = utf8_encode($exifData['ImageDescription']);
    	            $match = true;
    	        }
    	    }
    
    	    if( isset($exifData['COMPUTED']['UserComment']) && $match==false)
    	    {
    	        if(strlen($exifData['COMPUTED']['UserComment']) > 0)
    	        {
    	            $exifDescription = utf8_encode($exifData['COMPUTED']['UserComment']);
    	            $match = true;
    	        }
    	    }
    
    	    if( isset($exifData['COMMENT'][0]) && $match==false)
    	    {
    	        if(strlen($exifData['COMMENT'][0]) > 0)
    	        {
    	            $exifDescription = utf8_encode($exifData['COMMENT'][0]);
    	            $match = true;
    	        }
    	    }
    	}
		
    	$CleanExifDescription = JFilterInput::getInstance(null, null, 1, 1)->clean($exifDescription);
    	
	    return $CleanExifDescription;
	}
	
	function getIptcData($sourceFile)
	{
		$iptcDescription = '';
		$size = getimagesize($sourceFile, $info);
		if(isset($info['APP13']))
		{
		    $iptc = iptcparse($info['APP13']);
		    $match = false;
		    
			if( isset($iptc['2#120'][0]) )
    	    {
    	        if( strlen($iptc['2#120'][0]) > 0)
    	        {
    	            $iptcDescription = utf8_encode($iptc['2#120'][0]);
    	            $match = true;
    	        }
    	    }
		}
		
		$CleanIptcDescription = JFilterInput::getInstance(null, null, 1, 1)->clean($iptcDescription);
		return $CleanIptcDescription;
	}

	function makeImage($sourceFile, $destFile, $width, $height, $quality, $refresh,
	$crop=0, $rotation=0, $round=0, $roundFill='', $watermark=0, $wmText='', $wmTextColor='',
	$wmTextSize='', $wmFilename='', $wmPosition='', $wmTrans=100)
	{
		if( !extension_loaded('gd') && !function_exists('gd_info'))
		{
		    igFileHelper::raiseError('M1 0 :Please Check the PHP extension "GD Image Library" is installed on this server' , $refresh);
		    return false;
		}
		
	    if($width == 0 || $height == 0)
	    {
	        igFileHelper::raiseError('MI 1: '.JText::_('Image Width Or Height In Profile Settings Must Not Be Zero'), $refresh);
	        return false;
	    }
	    
		jimport('joomla.filesystem.file');
		require_once(JPATH_ADMINISTRATOR.'/components/com_igallery/lib/gdimage/GdImage.php');

		if(! JFile::exists($destFile) )
		{
            $ext = JFile::getExt($sourceFile);
            $imageinfo = getimagesize($sourceFile);
            $image = GDImage::load($sourceFile);
            $params =& JComponentHelper::getParams('com_igallery');
            $tryOriginal = $params->get('use_original', 0);
            
            if($tryOriginal == 1)
            {
	            if($rotation == 0 && $round == 0 && $watermark == 0 && $crop == 0)
	            {
			        if( $imageinfo[0] <= $width && $imageinfo[1] <= $height)
			        {
				        if(!JFile::copy($sourceFile, $destFile))
						{
							igFileHelper::raiseError($sourceFile.' -> '.$destFile .' '. JText::_( 'Error Moving File To Directory' ), $refresh);
							return false;
						}
						return true;
			        }
	            }
            }

    		if($crop == 1 && $imageinfo[0] > $width && $imageinfo[1] > $height)
    		{
    			$image = $image->resize( (int)$width ,(int)$height, 'outside' )->crop( 'center', 'middle', (int)$width, (int)$height );	
    		}
    		else
    		{
    			$image = $image->resize( (int)$width, (int)$height, 'inside', 'down' );
    		}
    		
			if($rotation != 0)
	            {
		            if(!function_exists('imagerotate'))
					{
					    igFileHelper::raiseError(JFile::getName($sourceFile).': nee'/' rotating '.$rotation.' degress, Please Check the php function "imagerotate" is available on this server' , $refresh);
					}
					else
					{
	            		$image = $image->rotate($rotation, null, false);
					}
	            }

    		if($watermark == 1)
    		{
    			$positionArray = explode('_', $wmPosition);
	    		
    			switch($positionArray[0])
	    		{
		        	case 'left': $leftIndent = '+5'; break;
		        	case 'right': $leftIndent = '-5'; break;
		        	default: $leftIndent = '';
		    	}
		    	
    			switch($positionArray[1])
	    		{
		        	case 'top': $topIndent = '+5'; break;
		        	case 'bottom': $topIndent = '-5'; break;
		        	default: $topIndent = '';
		    	}
    			
    			if( strlen($wmText) > 0)
    			{
					$canvas = $image->getCanvas();
					$fontPath = IG_ADMINISTRATOR_COMPONENT.'/fonts/font.ttf';
					if( !JFile::exists($fontPath) )
					{
						igFileHelper::raiseError('Watermarking Font File Missing, Please Upload a .ttf file to : '.$fontPath , $refresh);
						return false;
					}
					else
					{
						$colorArray = explode(',', $wmTextColor); 
						$font = new GDImage_Font_TTF($fontPath, (int)$wmTextSize, $image->allocateColorAlpha((int)$colorArray[0], (int)$colorArray[1], (int)$colorArray[2], (100 - $wmTrans) ) );
						$canvas->setFont($font);
						$canvas->writeText($positionArray[0].$leftIndent, $positionArray[1].$topIndent, $wmText);
					}
					
    			}
    			
    			if( strlen($wmFilename) > 0)
    			{
	                $extWatermark = JFile::getExt(IG_WATERMARK_PATH.'/'.$wmFilename);
					$overlay = GDImage::load(IG_WATERMARK_PATH.'/'.$wmFilename);
	                $image = $image->merge($overlay, $positionArray[0], $positionArray[1], $wmTrans);
    			}
    		}
    		
			if($round == 1)
    		{
				if(!function_exists('imagerotate'))
				{
				    igFileHelper::raiseError('Rounded Corners enabled: Please Check the php function "imagerotate" is available on this server' , $refresh);
				}
				else
				{
	    			if($width < 200)
	    			{
	    				$radius = 11;
	    			}
	    			else
	    			{
	    				$radius = 12;
	    			}
	    			
	    			$colorArray = explode(',', $roundFill);
	    			
	    			$image = $image->roundCorners($radius, $image->allocateColor( (int)$colorArray[0], (int)$colorArray[1], (int)$colorArray[2]), 255);
				}
			}

    		if( preg_match("/jp/i", $ext) )
    		{
    		    $image->saveToFile($destFile, $quality);
    		}
    		else
    		{
    		    $image->saveToFile($destFile);
    		}
    		
    		return true;
		}
		return true;
	}

	function raiseError($message, $refresh)
	{
	    if($refresh == true)
		{
		    JError::raise(2, 500,$message);
		}
		else
		{
			echo $message;
		}
	}

}