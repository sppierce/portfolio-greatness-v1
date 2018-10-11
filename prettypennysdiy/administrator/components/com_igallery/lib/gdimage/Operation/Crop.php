<?php
/**
* @version    $Id: Crop.php 20 2010-09-23 07:16:05Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();

class GDImage_Operation_Crop
{
	/**
	 * Returns a cropped image
	 *
	 * @param GDImage_Image $img
	 * @param smart_coordinate $left
	 * @param smart_coordinate $top
	 * @param smart_coordinate $width
	 * @param smart_coordinate $height
	 * @return GDImage_Image
	 */
	function execute($img, $left, $top, $width, $height)
	{
		$width = GDImage_Coordinate::fix($width, $img->getWidth(), $width);
		$height = GDImage_Coordinate::fix($height, $img->getHeight(), $height);
		$left = GDImage_Coordinate::fix($left, $img->getWidth(), $width);
		$top = GDImage_Coordinate::fix($top, $img->getHeight(), $height);
		if ($left < 0)
		{
			$width = $left + $width;
			$left = 0;
		}
		
		if ($width > $img->getWidth() - $left)
			$width = $img->getWidth() - $left;
		
		if ($top < 0)
		{
			$height = $top + $height;
			$top = 0;
		}
		
		if ($height > $img->getHeight() - $top)
			$height = $img->getHeight() - $top;
		
		if ($width <= 0 || $height <= 0)
		{
			JError::raiseError(500, JText::_('JLIB_GDIMAGE_ERROR_CAN_NOT_CROP_OUTSIDE'));
			return false;
		}
		
		$new = $img->doCreate($width, $height);
		
		if ($img->isTransparent() || $img instanceof GDImage_PaletteImage)
		{
			$new->copyTransparencyFrom($img);
			imagecopyresized(
				$new->getHandle(), $img->getHandle(), 0, 0, $left, $top, $width, $height, $width, $height
				);
		}
		else
		{
			$new->alphaBlending(false);
			$new->saveAlpha(true);
			imagecopyresampled(
				$new->getHandle(), $img->getHandle(), 0, 0, $left, $top, $width, $height, $width, $height
				);
		}
		return $new;
	}
}
