<?php
/**
* @version    $Id: OperationFactory.php 20 2010-09-23 07:16:05Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();

class GDImage_OperationFactory
{
	static protected $cache = array();
	
	static function get($operationName)
	{
		$lcname = strtolower($operationName);
		if (!isset(self::$cache[$lcname]))
		{
			$opClassName = "GDImage_Operation_" . $operationName;
			if (!class_exists($opClassName, false))
			{
				$fileName = GDImage::path() . 'Operation/' . ucfirst($operationName) . '.php';
				if (file_exists($fileName))
					require_once $fileName;
				else
				{
					JError::raiseError(500, JText::sprintf('JLIB_GDIMAGE_ERROR_LOAD_OPERATION', $operationName) );
					return false;
				}
			}
			self::$cache[$lcname] = new $opClassName();
		}
		return self::$cache[$lcname];
	}
}
