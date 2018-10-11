<?php
/**
* @version    $Id: MapperFactory.php 20 2010-09-23 07:16:05Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();
	
	abstract class GDImage_MapperFactory
	{
		static protected $mappers = array();
		
		static protected $mimeTable = array(
			'image/jpg' => 'JPEG', 
			'image/jpeg' => 'JPEG', 
			'image/pjpeg' => 'JPEG', 
			'image/gif' => 'GIF', 
			'image/png' => 'PNG',
			);
		
		/**
		 * Returns a mapper, based on the $uri and $format
		 * 
		 * @param string $uri File URI
		 * @param string $format File format (extension or mime-type) or null
		 * @return GDImage_Mapper
		 **/
		static function selectMapper($uri, $format = null)
		{
			$format = self::determineFormat($uri, $format);
			
			if (array_key_exists($format, self::$mappers))
				return self::$mappers[$format];
			
			$mapperClassName = 'GDImage_Mapper_' . $format;
			if (!class_exists($mapperClassName, false))
			{
				$mapperFileName = GDImage::path() . 'Mapper/' . $format . '.php';
				if (file_exists($mapperFileName))
					require_once $mapperFileName;
			}
			
			if (class_exists($mapperClassName, false))
			{
				self::$mappers[$format] = new $mapperClassName();
				return self::$mappers[$format];
			}
			
			JError::raiseError(500, JText::sprintf('JLIB_GDIMAGE_ERROR_FORMAT_NOT_SUPPORTED', $format) );
		}
		
		static function determineFormat($uri, $format = null)
		{
			if ($format == null)
				$format = self::extractExtension($uri);
			
			// mime-type match
			if (preg_match('~[a-z]*/[a-z-]*~i', $format))
				if (isset(self::$mimeTable[strtolower($format)]))
				{
					return self::$mimeTable[strtolower($format)];
				}
			
			// clean the string
			$format = strtoupper(preg_replace('/[^a-z0-9_-]/i', '', $format));
			if ($format == 'JPG')
				$format = 'JPEG';
			
			return $format;
		}
		
		static function mimeType($format)
		{
			return array_search(strtoupper($format), self::$mimeTable);
		}
		
		static function extractExtension($uri)
		{
			$p = strrpos($uri, '.');
			if ($p === false)
				return '';
			else
				return substr($uri, $p + 1);
		}
	}
