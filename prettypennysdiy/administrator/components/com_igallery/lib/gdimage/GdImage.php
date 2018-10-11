<?php
/**
* @version    $Id: GdImage.php 63 2010-09-29 23:06:01Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();
	
	require_once GDImage::path() . 'Image.php';
	require_once GDImage::path() . 'TrueColorImage.php';
	require_once GDImage::path() . 'PaletteImage.php';
	
	require_once GDImage::path() . 'Coordinate.php';
	require_once GDImage::path() . 'Canvas.php';
	require_once GDImage::path() . 'MapperFactory.php';
	require_once GDImage::path() . 'OperationFactory.php';
	
	require_once GDImage::path() . 'Font/TTF.php';
	
	jimport('joomla.filesystem.file');
	
	$lang =& JFactory::getLanguage();
	$lang->load('lib_gdimage', JPATH_SITE);
	
	class GDImage
	{
		const SIDE_TOP_LEFT = 1;
		const SIDE_TOP = 2;
		const SIDE_TOP_RIGHT = 4;
		const SIDE_RIGHT = 8;
		const SIDE_BOTTOM_RIGHT = 16;
		const SIDE_BOTTOM = 32;
		const SIDE_BOTTOM_LEFT = 64;
		const SIDE_LEFT = 128;
		const SIDE_ALL = 255;
		
		protected static $path = null;
		
		/**
		 * Returns the library version
		 * 
		 * @return string The library version
		 */
		static function version()
		{
			return '1.0 Beta2';
		}
		
		/**
		 * Returns the path to the library
		 *
		 * @return string
		 */
		static function path()
		{
			if (self::$path === null)
				self::$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
			return self::$path;
		}
		
		/**
		 * Loads an image from a file, URL, upload field, binary string, or a valid image handle. This function
		 * analyzes the input and decides whether to use GDImage::loadFromHandle(),
		 * GDImage::loadFromFile(), GDImage::loadFromUpload() or GDImage::loadFromString().
		 * 
		 * @param mixed $source File name, url, upload field name, binary string, or a GD image resource
		 * @param string $format *DEPRECATED* Hint for image format
		 * @return GDImage_Image GDImage_PaletteImage or GDImage_TrueColorImage instance
		 */
		static function load($source, $format = null)
		{
			$predictedSourceType = '';
			
			// Creating image via a valid resource
			if (!$predictedSourceType && self::isValidImageHandle($source))
				$predictedSourceType = 'Handle';
			
			// Check for binary string
			if (!$predictedSourceType)
			{
				// search first $binLength bytes (at a maximum) for ord<32 characters (binary image data)
				$binLength = 64;
				$sourceLength = strlen($source);
				$maxlen = ($sourceLength > $binLength) ? $binLength : $sourceLength;
				for ($i = 0; $i < $maxlen; $i++)
					if (ord($source[$i]) < 32)
					{
						$predictedSourceType = 'String';
						break;
					}
			}
			
			// Uploaded image (array uploads not supported)
			if (isset($_FILES[$source]) && isset($_FILES[$source]['tmp_name']))
				$predictedSourceType = 'Upload';
			
			// Otherwise, must be a file or an URL
			if (!$predictedSourceType)
				$predictedSourceType = 'File';
			
			return call_user_func(array('GDImage', 'loadFrom' . $predictedSourceType), $source, $format);
		}			
		
		/**
		 * Create and load an image from a file or URL. You can override the file 
		 * format by specifying the second parameter.
		 * 
		 * @param string $uri File or url
		 * @param string $format *DEPRECATED* Format hint, usually not needed
		 * @return GDImage_Image GDImage_PaletteImage or GDImage_TrueColorImage instance
		 */
		static function loadFromFile($uri, $format = null)
		{
			$data = file_get_contents($uri);
			$handle = @imagecreatefromstring($data);
			if (!self::isValidImageHandle($handle))
			{
				$mapper = GDImage_MapperFactory::selectMapper($uri, $format);
				$handle = $mapper->load($uri);
			}
			if (!self::isValidImageHandle($handle))
			{
				JError::raiseError(500, JText::_('JLIB_GDIMAGE_ERROR_INVALID_SOURCE').' '.$uri);
                return false;
			}
			
			return self::loadFromHandle($handle);
		}
		
		/**
		 * Create and load an image from a string. Format is auto-detected.
		 * 
		 * @param string $string Binary data, i.e. from BLOB field in the database
		 * @return GDImage_Image GDImage_PaletteImage or GDImage_TrueColorImage instance
		 */
		static function loadFromString($string)
		{
			$handle = imagecreatefromstring($string);
			if (!self::isValidImageHandle($handle))
			{
				JError::raiseError(500, JText::_('JLIB_GDIMAGE_ERROR_STRING_INVALID') );
				return false;
			}
			
			return self::loadFromHandle($handle);
		}
		
		/**
		 * Create and load an image from an image handle.
		 * 
		 * @param resource $handle A valid GD image resource
		 * @return GDImage_Image GDImage_PaletteImage or GDImage_TrueColorImage instance
		 */
		static function loadFromHandle($handle)
		{
			if (!self::isValidImageHandle($handle))
			{
				JError::raiseError(500, JText::_('JLIB_GDIMAGE_ERROR_INVALID_GD_RESOURCE') );
				return false;
			}
			
			if (imageistruecolor($handle))
				return new GDImage_TrueColorImage($handle);
			else
				return new GDImage_PaletteImage($handle);
		}
		
		/**
		 * This method loads a file from the $_FILES array.
		 *
		 * @param $field_name Name of the key in $_FILES array
		 * @return GDImage_Image The loaded image
		 */
		static function loadFromUpload($field_name)
		{
			if (!array_key_exists($field_name, $_FILES) || !file_exists($_FILES[$field_name]['tmp_name']))
			{
				JError::raiseError(500, JText::_('JLIB_GDIMAGE_ERROR_UPLOAD_EXIST') );
				return false;
			}
			
			return self::loadFromFile($_FILES[$field_name]['tmp_name'], $_FILES[$field_name]['type']);
		}
		
		/**
		 * Factory method for creating a palette image
		 * 
		 * @param int $width
		 * @param int $height
		 */
		static function createPaletteImage($width, $height)
		{
			return GDImage_PaletteImage::create($width, $height);
		}
		
		/**
		 * Factory method for creating a true-color image
		 * 
		 * @param int $width
		 * @param int $height
		 */
		static function createTrueColorImage($width, $height)
		{
			return GDImage_TrueColorImage::create($width, $height);
		}
		
		/**
		 * Check whether the given handle is a valid GD resource
		 * 
		 * @param mixed $handle The variable to check
		 * @return bool
		 */
		static function isValidImageHandle($handle)
		{
			return (is_resource($handle) && get_resource_type($handle) == 'gd');
		}
		
		/**
		 * Throws exception if the handle isn't a valid GD resource
		 * 
		 * @param mixed $handle The variable to check
		 */
		static function assertValidImageHandle($handle)
		{
			if (!self::isValidImageHandle($handle))
			{
				JError::raiseError(500, JText::_('JLIB_GDIMAGE_ERROR_INVALID_HANDLE') );
				return false;
			}
		}
	}
