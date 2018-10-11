<?php
/**
* @version    $Id: PNG.php 63 2010-09-29 23:06:01Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();

class GDImage_Mapper_PNG
{
	function load($uri)
	{
		return imagecreatefrompng($uri);
	}
	
	/**
	 * Saves the image to a file
	 *
	 * @param GDImage_Image $img
	 * @param string $uri
	 * @param integer $compression
	 * @param constant $filters
	 */
	function save($handle, $uri = null, $compression = 9, $filters = PNG_ALL_FILTERS)
	{
		if($uri)
		{
			ob_start();
		    imagepng($handle, null, $compression, $filters);
			$imageBuffer = ob_get_contents();
	        ob_end_clean();
	
	    	JFile::write($uri, $imageBuffer);
		}
		else
		{
			imagepng($handle, null, $compression, $filters);
		}
	}
}
