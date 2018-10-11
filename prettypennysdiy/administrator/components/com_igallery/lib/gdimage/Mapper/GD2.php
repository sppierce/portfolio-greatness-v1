<?php
/**
* @version    $Id: GD2.php 63 2010-09-29 23:06:01Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();

class GDImage_Mapper_GD2
{
	function load($uri)
	{
		return imagecreatefromgd2($uri);
	}
	
	/**
	 * Saves the image to a file
	 *
	 * @param GDImage_Image $img
	 * @param string $uri
	 * @param integer $chunk_size
	 * @param integer $type
	 */
	function save($handle, $uri = null, $chunk_size = null, $type = null)
	{
		ob_start();
		imagegd2($handle, null, $chunk_size, $type);
		$imageBuffer = ob_get_contents();
		ob_end_clean();

		JFile::write($uri, $imageBuffer);
	}
}