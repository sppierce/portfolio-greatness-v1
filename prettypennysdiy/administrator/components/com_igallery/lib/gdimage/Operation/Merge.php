<?php
/**
* @version    $Id: Merge.php 20 2010-09-23 07:16:05Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();

class GDImage_Operation_Merge
{
	/**
	 * Returns a merged image
	 *
	 * @param GDImage_Image $base
	 * @param GDImage_Image $overlay
	 * @param smart_coordinate $left
	 * @param smart_coordinate $top
	 * @param numeric $pct
	 * @return GDImage_Image
	 */
	function execute($base, $overlay, $left, $top, $pct)
	{
		$x = GDImage_Coordinate::fix($left, $base->getWidth(), $overlay->getWidth());
		$y = GDImage_Coordinate::fix($top, $base->getHeight(), $overlay->getHeight());
		
		$result = $base->asTrueColor();
		$result->alphaBlending(true);
		$result->saveAlpha(true);
		
		if ($pct <= 0)
			return $result;
		
		if ($pct < 100)
			imagecopymerge(
				$result->getHandle(), 
				$overlay->getHandle(), 
				$x, $y, 0, 0, 
				$overlay->getWidth(), 
				$overlay->getHeight(), 
				$pct
			);
		else
			imagecopy(
				$result->getHandle(), 
				$overlay->getHandle(), 
				$x, $y, 0, 0, 
				$overlay->getWidth(), 
				$overlay->getHeight() 
			);
		
		return $result;
	}
}
