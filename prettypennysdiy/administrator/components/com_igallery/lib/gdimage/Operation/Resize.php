<?php
/**
* @version    $Id: Resize.php 20 2010-09-23 07:16:05Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();

class GDImage_Operation_Resize
{
	/**
	 * Prepares and corrects smart coordinates
	 *
	 * @param GDImage_Image $img
	 * @param smart_coordinate $width
	 * @param smart_coordinate $height
	 * @param string $fit
	 * @return array
	 */
	protected function prepareDimensions($img, $width, $height, $fit)
	{
		list($width, $height) = GDImage_Coordinate::fixForResize($img, $width, $height);
		if ($width === 0 || $height === 0)
			return array('width' => 0, 'height' => 0);
		
		if ($fit == null)
			$fit = 'inside';
		
		$dim = array();
		if ($fit == 'fill')
		{
			$dim['width'] = $width;
			$dim['height'] = $height;
		}
		elseif ($fit == 'inside' || $fit == 'outside')
		{
			$rx = $img->getWidth() / $width;
			$ry = $img->getHeight() / $height;
			
			if ($fit == 'inside')
				$ratio = ($rx > $ry) ? $rx : $ry;
			else
				$ratio = ($rx < $ry) ? $rx : $ry;
			
			$dim['width'] = round($img->getWidth() / $ratio);
			$dim['height'] = round($img->getHeight() / $ratio);
		}
		else
		{
			JError::raiseError(500, JText::sprintf('JLIB_GDIMAGE_ERROR_INVALID_FIT', $fit));
			return false;
		}
		
		return $dim;
	}
	
	/**
	 * Returns a resized image
	 *
	 * @param GDImage_Image $img
	 * @param smart_coordinate $width
	 * @param smart_coordinate $height
	 * @param string $fit
	 * @param string $scale
	 * @return GDImage_Image
	 */
	function execute($img, $width, $height, $fit, $scale = 'any')
	{
		$dim = $this->prepareDimensions($img, $width, $height, $fit);
		if (($scale === 'down' && ($dim['width'] >= $img->getWidth() && $dim['height'] >= $img->getHeight())) ||
			($scale === 'up' && ($dim['width'] <= $img->getWidth() && $dim['height'] <= $img->getHeight())))
			$dim = array('width' => $img->getWidth(), 'height' => $img->getHeight());
		
		if ($dim['width'] <= 0 || $dim['height'] <= 0)
		{
			JError::raiseError(500, JText::_('JLIB_GDIMAGE_ERROR_DIMENSIONS_ZERO') );
			return false;
		}
		
		if ($img->isTransparent())
		{
			$new = GDImage_PaletteImage::create($dim['width'], $dim['height']);
			$new->copyTransparencyFrom($img);
			imagecopyresized(
					$new->getHandle(), 
					$img->getHandle(), 
					0, 0, 0, 0, 
					$new->getWidth(), 
					$new->getHeight(), 
					$img->getWidth(), 
					$img->getHeight()
				);
		}
		else
		{
			$new = GDImage_TrueColorImage::create($dim['width'], $dim['height']);
			$new->alphaBlending(false);
			$new->saveAlpha(true);
			imagecopyresampled(
					$new->getHandle(), 
					$img->getHandle(), 
					0, 0, 0, 0, 
					$new->getWidth(), 
					$new->getHeight(), 
					$img->getWidth(), 
					$img->getHeight()
				);
			$new->alphaBlending(true);
		}
		return $new;
	}
}
