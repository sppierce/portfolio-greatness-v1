<?php
/**
* @version    $Id: PaletteImage.php 20 2010-09-23 07:16:05Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();

class GDImage_PaletteImage extends GDImage_Image
{
	/**
	 * Create a palette image
	 *
	 * @param int $width
	 * @param int $height
	 * @return GDImage_PaletteImage
	 */
	static function create($width, $height)
	{
		if ($width * $height <= 0 || $width < 0)
		{
			JError::raiseError(500, JText::sprintf('JLIB_GDIMAGE_ERROR_CREATE_IMAGE_DIMENSIONS', $width, $height) );
			return false;
		}
		
		return new GDImage_PaletteImage(imagecreate($width, $height));
	}
	
	function doCreate($width, $height)
	{
		return self::create($width, $height);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GDImage_Image#isTrueColor()
	 */
	function isTrueColor()
	{
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GDImage_Image#asPalette($nColors, $dither, $matchPalette)
	 */
	function asPalette($nColors = 255, $dither = null, $matchPalette = true)
	{
		return $this->copy();
	}
	
	/**
	 * Returns a copy of the image
	 * 
	 * @param $trueColor True if the new image should be truecolor
	 * @return GDImage_Image
	 */
	protected function copyAsNew($trueColor = false)
	{
		$width = $this->getWidth();
		$height = $this->getHeight();
		
		if ($trueColor)
			$new = GDImage_TrueColorImage::create($width, $height);
		else
			$new = GDImage_PaletteImage::create($width, $height);
		
		// copy transparency of source to target
		if ($this->isTransparent())
		{
			$rgb = $this->getTransparentColorRGB();
			if (is_array($rgb))
			{
				$tci = $new->allocateColor($rgb['red'], $rgb['green'], $rgb['blue']);
				$new->fill(0, 0, $tci);
				$new->setTransparentColor($tci);
			}
		}
		
		imageCopy($new->getHandle(), $this->handle, 0, 0, 0, 0, $width, $height);
		return $new;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GDImage_Image#asTrueColor()
	 */
	function asTrueColor()
	{
		$width = $this->getWidth();
		$height = $this->getHeight();
		$new = GDImage::createTrueColorImage($width, $height);
		if ($this->isTransparent())
			$new->copyTransparencyFrom($this);
		imageCopy($new->getHandle(), $this->handle, 0, 0, 0, 0, $width, $height);
		return $new;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GDImage_Image#getChannels()
	 */
	function getChannels()
	{
		$args = func_get_args();
		if (count($args) == 1 && is_array($args[0]))
			$args = $args[0];
		return GDImage_OperationFactory::get('CopyChannelsPalette')->execute($this, $args);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GDImage_Image#copyNoAlpha()
	 */
	function copyNoAlpha()
	{
		return GDImage_Image::loadFromString($this->asString('png'));
	}
}
