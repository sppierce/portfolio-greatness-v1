<?php
defined('_JEXEC') or die('Restricted access');

class igUploadHelper
{
    function upload_file($fileName, $tmpPath, $uploadError, $destDir, $refresh)
	{
		if( !igUploadHelper::checkFileError($uploadError, $fileName, $refresh) )
		{
		    return false;
		}
		
		if( !igUploadHelper::checkMaxFilesize($tmpPath, $fileName, $refresh) )
		{
		    return false;
		}
		
		if( !igUploadHelper::checkExtension($fileName, $refresh) )
		{
		    return false;
		}
		
		if( !igUploadHelper::checkIsImage($tmpPath, $refresh) )
		{
		    return false;
		}
		
		$fileNameClean = igUploadHelper::replaceSpecial($fileName);
		
		$fileNameUnique = igUploadHelper::makeUniqueName($destDir, $fileNameClean);
		
		if( !igUploadHelper::moveFile($tmpPath, $destDir, $fileNameUnique, $refresh) )
		{
		    return false;
		}

		return $fileNameUnique;
	}
	
	function checkFileError($uploadError, $fileName, $refresh)
	{
		if ($uploadError > 0)
		{
			switch ($uploadError)
			{
				case 1:
					igFileHelper::raiseError($fileName . ' The uploaded file exceeds the upload_max_filesize directive in php.ini', $refresh);
					break;

				case 2:
					igFileHelper::raiseError($fileName . ' The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', $refresh);
					break;

				case 3:
					igFileHelper::raiseError($fileName . ' The uploaded file was only partially uploaded', $refresh);
					break;

				case 4:
					igFileHelper::raiseError($fileName . ' No file was uploaded', $refresh);
					break;
				
				case 6:
					igFileHelper::raiseError($fileName . ' Server is missing a temporary upload folder', $refresh);
					break;
					
				case 7:
					igFileHelper::raiseError($fileName . ' Failed to write file to disk', $refresh);
					break;
					
				case 8:
					igFileHelper::raiseError($fileName . ' A PHP extension stopped the file upload', $refresh);
					break;
					
				default:
					igFileHelper::raiseError($fileName . ' Upload Error Code: '.$uploadError, $refresh);
			}
			return false;
		}
		
		return true;
	}
	
	function checkMaxFilesize($tmpPath, $fileName, $refresh)
	{
	    $params =& JComponentHelper::getParams('com_igallery');
	    
		$maxUploadSize = $params->get('max_upload_img', 4000) * 1000;
		$fileSize = filesize($tmpPath);
		
		if ($maxUploadSize < $fileSize)
		{
			igFileHelper::raiseError($fileName .' - '.JText::_( 'Filesize' ).': '.$fileSize/1000 .'KB - '.
			JText::_( 'Maximum Image Upload Size - Kilobytes -' ).': '.$maxUploadSize/1000 .'KB - '. JText::_( 'File To Large - Reduce Filesize Or Change Component Paramaters' ), $refresh);
			return false;
		}
		
		return true;
	}
	
	function checkExtension($fileName, $refresh, $errorRaise = true)
	{
	    $extAccept = 'jpeg,jpg,png,gif';
		$validFileExts = explode(",", $extAccept);
		$uploadedFileExtension = JFile::getExt($fileName);
		$extOk = false;

		foreach($validFileExts as $key => $value)
		{
			if( preg_match("/$value/i", $uploadedFileExtension ) )
			{
				$extOk = true;
			}
		}

		if ($extOk == false)
		{
			if($errorRaise == true)
			{
                if( empty($uploadedFileExtension) )
                {
                    igFileHelper::raiseError('Error, No file extension detected, please try a different uploader in the component options, or try a different browser', $refresh);
                }
                else
                {
                    igFileHelper::raiseError(JText::_( 'Incorrect Filetype' ). ': '.$uploadedFileExtension.
                    ' '.JText::_( 'ALLOWED' ) .': '. $extAccept, $refresh);
                }
			}
			return false;
		}
		
		return true;
	}
	
	function checkIsImage($tmpPath, $refresh)
	{
		$imageinfo = getimagesize($tmpPath);

		if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) )
		{
			igFileHelper::raiseError(JText::_( 'No width or height detected in image file' ), $refresh);
			return false;
		}
		
		return true;
	}
	
	function replaceSpecial($fileName)
	{
	    $fileExt = JFile::getExt($fileName);
	    $fileNameNoExt = JFile::stripExt($fileName);
		$fileNameNoExt = preg_replace('/[^A-Za-z0-9.]/', '_', $fileNameNoExt);
		
		$fileExtLowerCase = strtolower($fileExt);
		
		$fileName = $fileNameNoExt.'.'.$fileExtLowerCase;
		
		return $fileName; 
	}
	
	function makeUniqueName($destDir, $fileName)
	{
	    $fileExt = JFile::getExt($fileName);
	    $fileNameNoExt = JFile::stripExt($fileName);
	    
		if(JFile::exists($destDir.$fileName) )
		{
			$i=1;
			while(JFile::exists($destDir.$fileNameNoExt.$i.'.'.$fileExt) )
			{
				$i++;
			}
			$fileName = $fileNameNoExt.$i.'.'.$fileExt;
		}
		
		return $fileName;
	}
	
	function moveFile($tmpPath, $destDir, $fileName, $refresh)
	{
		$destPath = $destDir.'/'.$fileName;
		
		if(!JFile::upload($tmpPath, $destPath))
		{
			igFileHelper::raiseError($tmpPath.' -> '.$destPath .' '. JText::_( 'Error Moving File To Directory' ), $refresh);
			return false;
		}
		
		return true;
	}
}
?>