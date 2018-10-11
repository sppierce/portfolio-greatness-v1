<?php
/**
* @version    $Id: Canvas.php 63 2010-09-29 23:06:01Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();
	
	class GDImage_Canvas
	{
		protected $handle = 0;
		protected $image = null;
		protected $font = null;
		
		function __construct($img)
		{
			$this->handle = $img->getHandle();
			$this->image = $img;
		}
		
		function setFont($font)
		{
			$this->font = $font;
		}
		
		/**
		 * Creates and sets the current font
		 * 
		 * @param string $file Font file name (string)
		 * @param int $size Font size
		 * @param int $color Text color
		 * @param int $bgcolorBackground color
		 * @return object One of the GDImage_Font_* objects
		 */
		function useFont($file, $size = 12, $color = 0, $bgcolor = null)
		{
			$p = strrpos($file, '.');
			if ($p === false || $p < strlen($file) - 4)
				$ext = 'ttf';
			else
				$ext = strtolower(substr($file, $p + 1));
			
			if ($ext == 'ttf')
				$font = new GDImage_Font_TTF($file, $size, $color);
			elseif ($ext == 'ps')
				$font = new GDImage_Font_PS($file, $size, $color, $bgcolor);
			elseif ($ext == 'gdf')
				$font = new GDImage_Font_GDF($file, $color);
			else
			{
				JError::raiseError(500, JText::_('JLIB_GDIMAGE_ERROR_INVALID_FONT_FILE') );
				return false;
			}
			
			$this->setFont($font);
			return $font;
		}
		
		/**
		 * Write text on the image at specified position
		 * 
		 * You must set a font with a call to GDImage_Canvas::setFont() prior to writing text to the image.
		 * 
		 * @param int $x Left
		 * @param int $y Top
		 * @param string $text Text to write
		 * @param int $angle The angle, defaults to 0
		 */
		function writeText($x, $y, $text, $angle = 0)
		{
			if ($this->font === null)
			{
				JError::raiseError(500,  JText::_('JLIB_GDIMAGE_ERROR_CANNOT_WRITE_TEXT') );
				return false;
			}
			
			$angle = - floatval($angle);
			if ($angle < 0)
				$angle = 360 + $angle;
			$angle = $angle % 360;
			
			$this->font->writeText($this->image, $x, $y, $text, $angle);
		}
		
		/**
		 * A magic method that allows you to call any PHP function that starts with "image".
		 */
		function __call($method, $params)
		{
			if (function_exists('image' . $method))
			{
				array_unshift($params, $this->handle);
				call_user_func_array('image' . $method, $params);
			}
			else
			{
				JError::raiseError(500, JText::sprintf('JLIB_GDIMAGE_ERROR_FUNCTION_NOT_EXIST', $method) );
				return false;
			}
		}
	}
