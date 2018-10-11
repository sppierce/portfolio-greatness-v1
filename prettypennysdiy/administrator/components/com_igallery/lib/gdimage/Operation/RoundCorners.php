<?php
/**
* @version    $Id: RoundCorners.php 20 2010-09-23 07:16:05Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();

class GDImage_Operation_RoundCorners
{
	/**
	 * @param GDImage_Image $image
	 * @param int $radius
	 * @param int $color
	 * @param int $smoothness
	 * @return GDImage_Image
	 */
	function execute($image, $radius, $color, $smoothness, $corners)
	{
		if ($smoothness < 1)
			$sample_ratio = 1;
		elseif ($smoothness > 16)
			$sample_ratio = 16;
		else
			$sample_ratio = $smoothness;
		
		$corner = GDImage::createTrueColorImage($radius * $sample_ratio, $radius * $sample_ratio);
		if ($color === null)
		{
			imagepalettecopy($corner->getHandle(), $image->getHandle());
			$bg_color = $corner->allocateColor(0, 0, 0);
			
			$corner->fill(0, 0, $bg_color);
			$fg_color = $corner->allocateColor(255, 255, 255);
			$corner->getCanvas()->filledEllipse($radius * $sample_ratio, $radius * $sample_ratio, $radius * 2 * $sample_ratio, $radius * 2 * $sample_ratio, $fg_color);
			$corner = $corner->resize($radius, $radius);
			
			$result = $image->asTrueColor();
			
			$tc = $result->getTransparentColor();
			if ($tc == -1)
			{
				$tc = $result->allocateColorAlpha(255, 255, 255, 127);
				imagecolortransparent($result->getHandle(), $tc);
				$result->setTransparentColor($tc);
			}
			
			if ($corners & GDImage::SIDE_TOP_LEFT || $corners & GDImage::SIDE_LEFT || $corners & GDImage::SIDE_TOP)
				$result = $result->applyMask($corner, -1, -1);
			
			$corner = $corner->rotate(90);
			if ($corners & GDImage::SIDE_TOP_RIGHT || $corners & GDImage::SIDE_TOP || $corners & GDImage::SIDE_RIGHT)
				$result = $result->applyMask($corner, $result->getWidth() - $corner->getWidth() + 1, -1, 100);
			
			$corner = $corner->rotate(90);
			if ($corners & GDImage::SIDE_BOTTOM_RIGHT || $corners & GDImage::SIDE_RIGHT || $corners & GDImage::SIDE_BOTTOM)
				$result = $result->applyMask($corner, $result->getWidth() - $corner->getWidth() + 1, $result->getHeight() - $corner->getHeight() + 1, 100);
			
			$corner = $corner->rotate(90);
			if ($corners & GDImage::SIDE_BOTTOM_LEFT || $corners & GDImage::SIDE_LEFT || $corners & GDImage::SIDE_BOTTOM)
				$result = $result->applyMask($corner, -1, $result->getHeight() - $corner->getHeight() + 1, 100);
			
			return $result;
		}
		else
		{
			$bg_color = $color;
			
			$corner->fill(0, 0, $bg_color);
			$fg_color = $corner->allocateColorAlpha(127, 127, 127, 127);
			$corner->getCanvas()->filledEllipse($radius * $sample_ratio, $radius * $sample_ratio, $radius * 2 * $sample_ratio, $radius * 2 * $sample_ratio, $fg_color);
			$corner = $corner->resize($radius, $radius);
			
			$result = $image->copy();
			if ($corners & GDImage::SIDE_TOP_LEFT || $corners & GDImage::SIDE_LEFT || $corners & GDImage::SIDE_TOP)
				$result = $image->merge($corner, -1, -1, 100);
			
			$corner = $corner->rotate(90);
			if ($corners & GDImage::SIDE_TOP_RIGHT || $corners & GDImage::SIDE_TOP || $corners & GDImage::SIDE_RIGHT)
				$result = $result->merge($corner, $result->getWidth() - $corner->getWidth() + 1, -1, 100);
			
			$corner = $corner->rotate(90);
			if ($corners & GDImage::SIDE_BOTTOM_RIGHT || $corners & GDImage::SIDE_RIGHT || $corners & GDImage::SIDE_BOTTOM)
				$result = $result->merge($corner, $result->getWidth() - $corner->getWidth() + 1, $result->getHeight() - $corner->getHeight() + 1, 100);
			
			$corner = $corner->rotate(90);
			if ($corners & GDImage::SIDE_BOTTOM_LEFT || $corners & GDImage::SIDE_LEFT || $corners & GDImage::SIDE_BOTTOM)
				$result = $result->merge($corner, -1, $result->getHeight() - $corner->getHeight() + 1, 100);
			
			return $result;
		}
	}
}
