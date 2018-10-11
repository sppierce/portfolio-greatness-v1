<?php
/**
* @version    $Id: Image.php 63 2010-09-29 23:06:01Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();
	
	abstract class GDImage_Image
	{
		/**
		 * Holds the image resource
		 * @var resource
		 */
		protected $handle = null;
		
		/**
		 * Flag that determines if GDImage should call imagedestroy() upon object destruction
		 * @var bool
		 */
		protected $handleReleased = false;
		
		/**
		 * Canvas object
		 * @var GDImage_Canvas
		 */
		protected $canvas = null;
		
		/**
		 * @var string
		 */
		protected $sdata = null;
		
		/**
		 * The base class constructor
		 *
		 * @param resource $handle Image handle (GD2 resource)
		 */
		function __construct($handle)
		{
			GDImage::assertValidImageHandle($handle);
			$this->handle = $handle;
		}
		
		/**
		 * Cleanup
		 * 
		 * Destroys the handle via GDImage_Image::destroy() when called by the GC.
		 */
		function __destruct()
		{
			$this->destroy();
		}
		
		/**
		 * This method destroy the image handle, and releases the image resource.
		 */
		function destroy()
		{
			if ($this->isValid() && !$this->handleReleased)
				imagedestroy($this->handle);
			
			$this->handle = null;
		}
		
		/**
		 * Returns the GD image resource
		 * 
		 * @return resource GD image resource
		 */
		function getHandle()
		{
			return $this->handle;
		}
		
		/**
		 * @return bool True, if the image object holds a valid GD image, false otherwise
		 */
		function isValid()
		{
			return GDImage::isValidImageHandle($this->handle);
		}
		
		/**
		 * Releases the handle
		 */
		function releaseHandle()
		{
			$this->handleReleased = true;
		}
		
		/**
		 * Saves an image to a file
		 * 
		 * The file type is recognized from the $uri.This method supports additional parameters: quality (for jpeg images) and 
		 * compression quality and filters (for png images)
		 * 
		 * @param string $uri The file locator (can be url)
		 * @return mixed Whatever the mapper returns
		 */
		function saveToFile($uri)
		{
			$mapper = GDImage_MapperFactory::selectMapper($uri, null);
			$args = func_get_args();
			array_unshift($args, $this->getHandle());
			return call_user_func_array(array($mapper, 'save'), $args);
		}
		
		/**
		 * Returns binary string with image data in format specified by $format
		 * 
		 * Additional parameters may be passed to the function. See GDImage_Image::saveToFile() for more details.
		 * 
		 * @param string $format The format of the image
		 * @return string The binary image data in specified format
		 */
		function asString($format)
		{
			ob_start();
			$args = func_get_args();
			$args[0] = null;
			array_unshift($args, $this->getHandle());
			
			$mapper = GDImage_MapperFactory::selectMapper(null, $format);
			call_user_func_array(array($mapper, 'save'), $args);
			
			return ob_get_clean();
		}
		
		/**
		 * Output a header to browser.
		 * 
		 * @param $name Name of the header
		 * @param $data Data
		 */
		protected function writeHeader($name, $data)
		{
			header($name . ": " . $data);
		}
		
		/**
		 * Outputs the image to browser
		 * 
		 * Sets headers Content-length and Content-type, and echoes the image in the specified format.
		 * All other headers (such as Content-disposition) must be added manually. 
		 * 
		 * @param string $format Image format
		 */
		function output($format)
		{
			$args = func_get_args();
			$data = call_user_func_array(array($this, 'asString'), $args);
			
			$this->writeHeader('Content-length', strlen($data));
			$this->writeHeader('Content-type', GDImage_MapperFactory::mimeType($format));
			echo $data;
		}
		
		/**
		 * @return int Image width
		 */
		function getWidth()
		{
			return imagesx($this->handle);
		}
		
		/**
		 * @return int Image height
		 */
		function getHeight()
		{
			return imagesy($this->handle);
		}
		
		/**
		 * Allocate a color by RGB values.
		 * 
		 * @param mixed $R Red-component value or an RGB array (with red, green, blue keys)
		 * @param int $G If $R is int, this is the green component
		 * @param int $B If $R is int, this is the blue component
		 * @return int Image color index
		 */
		function allocateColor($R, $G = null, $B = null)
		{
			if (is_array($R))
				return imageColorAllocate($this->handle, $R['red'], $R['green'], $R['blue']);
			else
				return imageColorAllocate($this->handle, $R, $G, $B);
		}
		
		/**
		 * @return bool True if the image is transparent, false otherwise
		 */
		function isTransparent()
		{
			return $this->getTransparentColor() >= 0;
		}
		
		/**
		 * @return int Transparent color index
		 */
		function getTransparentColor()
		{
			return imagecolortransparent($this->handle);
		}
		
		/**
		 * @param int $color Transparent color index
		 */
		function setTransparentColor($color)
		{
			return imagecolortransparent($this->handle, $color);
		}
		
		/**
		 * @return mixed Transparent color RGBA array
		 */
		function getTransparentColorRGB()
		{
			$total = imagecolorstotal($this->handle);
			$tc = $this->getTransparentColor();
			
			if ($tc >= $total && $total > 0)
			{
				return null;
			}
			else
				return $this->getColorRGB($tc);
		}
		
		/**
		 * Returns a RGBA array for pixel at $x, $y
		 * 
		 * @param int $x
		 * @param int $y
		 * @return array RGB array 
		 */
		function getRGBAt($x, $y)
		{
			return $this->getColorRGB($this->getColorAt($x, $y));
		}
		
		/**
		 * Writes a pixel at the designated coordinates
		 * 
		 * Takes an associative array of colours and uses getExactColor() to
		 * retrieve the exact index color to write to the image with.
		 *
		 * @param int $x
		 * @param int $y
		 * @param array $color
		 */
		function setRGBAt($x, $y, $color)
		{
			$this->setColorAt($x, $y, $this->getExactColor($color));
		}
		
		/**
		 * Returns a color's RGB
		 * 
		 * @param int $colorIndex Color index
		 * @return mixed RGBA array for a color with index $colorIndex
		 */
		function getColorRGB($colorIndex)
		{
			return imageColorsForIndex($this->handle, $colorIndex);
		}
		
		/**
		 * Returns an index of the color at $x, $y
		 * 
		 * @param int $x
		 * @param int $y
		 * @return int Color index for a pixel at $x, $y
		 */
		function getColorAt($x, $y)
		{
			return imagecolorat($this->handle, $x, $y);
		}
		
		/**
		 * Set the color index $color to a pixel at $x, $y
		 * 
		 * @param int $x
		 * @param int $y
		 * @param int $color Color index
		 */
		function setColorAt($x, $y, $color)
		{
			return imagesetpixel($this->handle, $x, $y, $color);
		}
		
		/**
		 * Returns closest color index that matches the given RGB value. Uses
		 * PHP's imagecolorclosest()
		 * 
		 * @param mixed $R Red or RGBA array
		 * @param int $G Green component (or null if $R is an RGB array)
		 * @param int $B Blue component (or null if $R is an RGB array)
		 * @return int Color index
		 */
		function getClosestColor($R, $G = null, $B = null)
		{
			if (is_array($R))
				return imagecolorclosest($this->handle, $R['red'], $R['green'], $R['blue']);
			else
				return imagecolorclosest($this->handle, $R, $G, $B);
		}
		
		/**
		 * Returns the color index that exactly matches the given RGB value. Uses
		 * PHP's imagecolorexact()
		 * 
		 * @param mixed $R Red or RGBA array
		 * @param int $G Green component (or null if $R is an RGB array)
		 * @param int $B Blue component (or null if $R is an RGB array)
		 * @return int Color index
		 */
		function getExactColor($R, $G = null, $B = null)
		{
			if (is_array($R))
				return imagecolorexact($this->handle, $R['red'], $R['green'], $R['blue']);
			else
				return imagecolorexact($this->handle, $R, $G, $B);
		}
		
		/**
		 * Copies transparency information from $sourceImage. Optionally fills
		 * the image with the transparent color at (0, 0).
		 * 
		 * @param object $sourceImage
		 * @param bool $fill True if you want to fill the image with transparent color
		 */
		function copyTransparencyFrom($sourceImage, $fill = true)
		{
			if ($sourceImage->isTransparent())
			{
				$rgba = $sourceImage->getTransparentColorRGB();
				if ($rgba === null)
					return;
				
				if ($this->isTrueColor())
				{
					$rgba['alpha'] = 127;
					$color = $this->allocateColorAlpha($rgba);
				}
				else
					$color = $this->allocateColor($rgba);
				
				$this->setTransparentColor($color);
				if ($fill)
					$this->fill(0, 0, $color);
			}
		}
		
		/**
		 * Fill the image at ($x, $y) with color index $color
		 * 
		 * @param int $x
		 * @param int $y
		 * @param int $color
		 */
		function fill($x, $y, $color)
		{
			return imagefill($this->handle, $x, $y, $color);
		}
		
		/**
		 * Used internally to create Operation objects
		 *
		 * @param string $name
		 * @return object
		 */
		protected function getOperation($name)
		{
			return GDImage_OperationFactory::get($name);
		}
		
		/**
		 * Resize the image to given dimensions.
		 * 
		 * @param mixed $width The new width (smart coordinate), or null.
		 * @param mixed $height The new height (smart coordinate), or null.
		 * @param string $fit 'inside', 'outside', 'fill'
		 * @param string $scale 'down', 'up', 'any'
		 * @return GDImage_Image resized image
		 */
		function resize($width = null, $height = null, $fit = 'inside', $scale = 'any')
		{
			return $this->getOperation('Resize')->execute($this, $width, $height, $fit, $scale);
		}
		
		/**
		 * Rotate the image for angle $angle clockwise.
		 * 
		 * @param int $angle Angle in degrees, clock-wise
		 * @param int $bgColor color of the new background
		 * @param bool $ignoreTransparent
		 * @return GDImage_Image The rotated image
		 */
		function rotate($angle, $bgColor = null, $ignoreTransparent = true)
		{
			return $this->getOperation('Rotate')->execute($this, $angle, $bgColor, $ignoreTransparent);
		}
		
		/**
		 * This method lays the overlay (watermark) on the image.
		 * 
		 * @param GDImage_Image $overlay The overlay image
		 * @param mixed $left Left position of the overlay, smart coordinate
		 * @param mixed $top Top position of the overlay, smart coordinate
		 * @param int $pct The opacity of the overlay
		 * @return GDImage_Image The merged image
		 */
		function merge($overlay, $left = 0, $top = 0, $pct = 100)
		{
			return $this->getOperation('Merge')->execute($this, $overlay, $left, $top, $pct);
		}
		
		/**
		 * Returns an image with round corners
		 * 
		 * @param int $radius Radius of the corners
		 * @param int $color The color of corners. If null, corners are rendered transparent (slower than using a solid color).
		 * @param int $smoothness Specify the level of smoothness. Suggested values from 1 to 4.
		 * @param int $corners Specify which corners to draw (defaults to all corners)
		 * @return GDImage_Image The resulting image with round corners
		 */
		function roundCorners($radius, $color = null, $smoothness = 2, $corners = 255)
		{
			return $this->getOperation('RoundCorners')->execute($this, $radius, $color, $smoothness, $corners);
		}
		
		/**
		 * Returns an image with applied mask
		 * 
		 * A mask is a grayscale image, where the shade determines the alpha channel. Black is fully transparent
		 * and white is fully opaque.
		 * 
		 * @param GDImage_Image $mask The mask image, greyscale
		 * @param mixed $left Left coordinate, smart coordinate
		 * @param mixed $top Top coordinate, smart coordinate
		 * @return GDImage_Image The resulting image
		 **/
		function applyMask($mask, $left = 0, $top = 0)
		{
			return $this->getOperation('ApplyMask')->execute($this, $mask, $left, $top);
		}
		
		/**
		 * Returns a cropped rectangular portion of the image
		 * 
		 * @param mixed $left Left-coordinate of the crop rect, smart coordinate
		 * @param mixed $top Top-coordinate of the crop rect, smart coordinate
		 * @param mixed $width Width of the crop rect, smart coordinate
		 * @param mixed $height Height of the crop rect, smart coordinate
		 * @return GDImage_Image The cropped image
		 **/
		function crop($left = 0, $top = 0, $width = '100%', $height = '100%')
		{
			return $this->getOperation('Crop')->execute($this, $left, $top, $width, $height);
		}
		
		
		/**
		 * Used internally to execute operations
		 *
		 * @param string $name
		 * @param array $args
		 * @return GDImage_Image
		 */
		function __call($name, $args)
		{
			$op = $this->getOperation($name);
			array_unshift($args, $this);
			return call_user_func_array(array($op, 'execute'), $args);
		}
		
		/**
		 * Returns an image in GIF or PNG format
		 *
		 * @return string
		 */
		function __toString()
		{
			if ($this->isTransparent())
				return $this->asString('gif');
			else
				return $this->asString('png');
		}
		
		/**
		 * Returns a copy of the image
		 * 
		 * @return GDImage_Image The copy
		 **/
		function copy()
		{
			$dest = $this->doCreate($this->getWidth(), $this->getHeight());
			$dest->copyTransparencyFrom($this, true);
			$this->copyTo($dest, 0, 0);
			return $dest;
		}
		
		/**
		 * Copies this image onto another image
		 * 
		 * @param GDImage_Image $dest
		 * @param int $left
		 * @param int $top
		 **/
		function copyTo($dest, $left = 0, $top = 0)
		{
			imageCopy($dest->getHandle(), $this->handle, $left, $top, 0, 0, $this->getWidth(), $this->getHeight());
		}
		
		/**
		 * Returns the canvas object
		 * 
		 * @return GDImage_Canvas The Canvas object
		 **/
		function getCanvas()
		{
			if ($this->canvas == null)
				$this->canvas = new GDImage_Canvas($this);
			return $this->canvas;
		}
		
		/**
		 * Returns true if the image is true-color, false otherwise
		 * 
		 * @return bool
		 **/
		abstract function isTrueColor();
		
		/**
		 * Returns a true-color copy of the image
		 * 
		 * @return GDImage_TrueColorImage
		 **/
		abstract function asTrueColor();
		
		/**
		 * Returns a palette copy (8bit) of the image
		 *
		 * @param int $nColors Number of colors in the resulting image, more than 0, less or equal to 255
		 * @param bool $dither Use dithering or not
		 * @param bool $matchPalette Set to true to use imagecolormatch() to match the resulting palette more closely to the original image 
		 * @return GDImage_Image
		 **/
		abstract function asPalette($nColors = 255, $dither = null, $matchPalette = true);
		
		/**
		 * Retrieve an image with selected channels
		 * 
		 * Examples:
		 * <code>
		 * $channels = $img->getChannels('red', 'blue');
		 * $channels = $img->getChannels('alpha', 'green');
		 * $channels = $img->getChannels(array('green', 'blue'));
		 * </code>
		 * 
		 * @return GDImage_Image
		 **/
		abstract function getChannels();
		
		/**
		 * Returns an image without an alpha channel
		 * 
		 * @return GDImage_Image
		 **/
		abstract function copyNoAlpha();
		
		/**
		 * Returns an array of serializable protected variables. Called automatically upon serialize().
		 * 
		 * @return array
		 */
		function __sleep()
		{
			$this->sdata = $this->asString('png');
			return array('sdata', 'handleReleased');
		}
		
		/**
		 * Restores an image from serialization. Called automatically upon unserialize().
		 */
		function __wakeup()
		{
			$this->handle = imagecreatefromstring($this->sdata);
			$this->sdata = null;
		}
	}
