<?php
/**
* @version    $Id: TrueColorImage.php 20 2010-09-23 07:16:05Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();
	
class GDImage_TrueColorImage extends GDImage_Image
{
	/**
	 * Creates the object
	 *
	 * @param resource $handle
	 */
	function __construct($handle)
	{
		parent::__construct($handle);
		$this->alphaBlending(false);
		$this->saveAlpha(true);
	}
	
	/**
	 * Factory method that creates a true-color image object
	 *
	 * @param int $width
	 * @param int $height
	 * @return GDImage_TrueColorImage
	 */
	static function create($width, $height)
	{
		if ($width * $height <= 0 || $width < 0)
		{
			JError::raiseError(500, JText::sprintf('JLIB_GDIMAGE_ERROR_CREATE_IMAGE_DIMENSIONS', $width, $height) );
			return false;
		}
		
		return new GDImage_TrueColorImage(imagecreatetruecolor($width, $height));
	}
	
	function doCreate($width, $height)
	{
		return self::create($width, $height);
	}
	
	function isTrueColor()
	{
		return true;
	}
	
	/**
	 * Sets alpha blending mode via imagealphablending()
	 *
	 * @param bool $mode
	 * @return bool
	 */
	function alphaBlending($mode)
	{
		return imagealphablending($this->handle, $mode);
	}
	
	/**
	 * Toggle if alpha channel should be saved with the image via imagesavealpha()
	 *
	 * @param bool $on
	 * @return bool
	 */
	function saveAlpha($on)
	{
		return imagesavealpha($this->handle, $on);
	}
	
	/**
	 * Allocates a color and returns its index
	 * 
	 * This method accepts either each component as an integer value,
	 * or an associative array that holds the color's components in keys
	 * 'red', 'green', 'blue', 'alpha'.
	 *
	 * @param mixed $R
	 * @param int $G
	 * @param int $B
	 * @param int $A
	 * @return int
	 */
	function allocateColorAlpha($R, $G = null, $B = null, $A = null)
	{
		if (is_array($R))
			return imageColorAllocateAlpha($this->handle, $R['red'], $R['green'], $R['blue'], $R['alpha']);
		else
			return imageColorAllocateAlpha($this->handle, $R, $G, $B, $A);
	}
	
	/**
	 * @see GDImage_Image#asPalette($nColors, $dither, $matchPalette)
	 */
	function asPalette($nColors = 255, $dither = null, $matchPalette = true)
	{
		$nColors = intval($nColors);
		if ($nColors < 1)
			$nColors = 1;
		elseif ($nColors > 255)
			$nColors = 255;
		
		if ($dither === null)
			$dither = $this->isTransparent();
		
		$temp = $this->copy();
		imagetruecolortopalette($temp->handle, $dither, $nColors);
		if ($matchPalette == true && function_exists('imagecolormatch'))
			imagecolormatch($this->handle, $temp->handle);
		
		$temp->releaseHandle();
		return new GDImage_PaletteImage($temp->handle);
	}
	
	/**
	 * Returns the index of the color that best match the given color components
	 *
	 * This method accepts either each component as an integer value,
	 * or an associative array that holds the color's components in keys
	 * 'red', 'green', 'blue', 'alpha'.
	 *
	 * @param mixed $R Red component value or an associative array
	 * @param int $G Green component
	 * @param int $B Blue component
	 * @param int $A Alpha component
	 * @return int The color index
	 */
	function getClosestColorAlpha($R, $G = null, $B = null, $A = null)
	{
		if (is_array($R))
			return imagecolorclosestalpha($this->handle, $R['red'], $R['green'], $R['blue'], $R['alpha']);
		else
			return imagecolorclosestalpha($this->handle, $R, $G, $B, $A);
	}
	
	/**
	 * Returns the index of the color that exactly match the given color components
	 *
	 * This method accepts either each component as an integer value,
	 * or an associative array that holds the color's components in keys
	 * 'red', 'green', 'blue', 'alpha'.
	 *
	 * @param mixed $R Red component value or an associative array
	 * @param int $G Green component
	 * @param int $B Blue component
	 * @param int $A Alpha component
	 * @return int The color index
	 */
	function getExactColorAlpha($R, $G = null, $B = null, $A = null)
	{
		if (is_array($R))
			return imagecolorexactalpha($this->handle, $R['red'], $R['green'], $R['blue'], $R['alpha']);
		else
			return imagecolorexactalpha($this->handle, $R, $G, $B, $A);
	}
	
	/**
	 * @see GDImage_Image#getChannels()
	 */
	function getChannels()
	{
		$args = func_get_args();
		if (count($args) == 1 && is_array($args[0]))
			$args = $args[0];
		return GDImage_OperationFactory::get('CopyChannelsTrueColor')->execute($this, $args);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GDImage_Image#copyNoAlpha()
	 */
	function copyNoAlpha()
	{
		$prev = $this->saveAlpha(false);
		$result = GDImage_Image::loadFromString($this->asString('png'));
		$this->saveAlpha($prev);
		//$result->releaseHandle();
		return $result;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GDImage_Image#asTrueColor()
	 */
	function asTrueColor()
	{
		return $this->copy();
	}
}
