<?php
/**
* @version    $Id: TTF.php 63 2010-09-29 23:06:01Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();
	
class GDImage_Font_TTF
{
	public $face;
	public $size;
	public $color;
	
	function __construct($face, $size, $color)
	{
		$this->face = $face;
		$this->size = $size;
		$this->color = $color;
	}
	
	/**
	 * Writes text to the image's canvas
	 *
	 * @param GDImage_Image $img
	 * @param smart_coordinate $left
	 * @param smart_coordinate $top
	 * @param string $text
	 * @param integer $angle 
	 * @return GDImage_Image
	 */
	function writeText($image, $x, $y, $text, $angle = 0)
	{
		if ($image->isTrueColor())
			$image->alphaBlending(true);
		
		$box = imageftbbox($this->size, $angle, $this->face, $text);
		$obox = array(
			'left' => min($box[0], $box[2], $box[4], $box[6]),
			'top' => min($box[1], $box[3], $box[5], $box[7]),
			'right' => max($box[0], $box[2], $box[4], $box[6]) - 1,
			'bottom' => max($box[1], $box[3], $box[5], $box[7]) - 1
		);
		$obox['width'] = abs($obox['left']) + abs($obox['right']);
		$obox['height'] = abs($obox['top']) + abs($obox['bottom']);
		
		$x = GDImage_Coordinate::fix($x, $image->getWidth(), $obox['width']);
		$y = GDImage_Coordinate::fix($y, $image->getHeight(), $obox['height']);
		
		$fixed_x = $x - $obox['left'];
		$fixed_y = $y - $obox['top'];
		
		imagettftext($image->getHandle(), $this->size, $angle, $fixed_x, $fixed_y, $this->color, $this->face, $text);
	}
}
