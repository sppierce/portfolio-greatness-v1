<?php
/**
* @version    $Id: Coordinate.php 63 2010-09-29 23:06:01Z mthomsonnz $
* @package	  GDImage
* @copyright  2007-2010 Gasper Kozak, Matthew Thomson
* @license	  GNU Lesser General Public License version 2.1
*/

defined('JPATH_BASE') or die();

class GDImage_Coordinate
{
	static protected $coord_align = array("left", "center", "right", "top", "middle", "bottom");
	static protected $coord_numeric = array("[0-9]+", "[0-9]+\.[0-9]+", "[0-9]+%", "[0-9]+\.[0-9]+%");
	
	/**
	 * Parses a numeric or string representation of a corrdinate into a structure
	 * 
	 * @param string $coord Smart coordinate
	 * @return array Parsed smart coordinate
	 */
	static function parse($c)
	{
		$tokens = array();
		$operators = array('+', '-');
		
		$flush_operand = false;
		$flush_operator = false;
		$current_operand = '';
		$current_operator = '';
		$coordinate = strval($c);
		$expr_len = strlen($coordinate);
		
		for ($i = 0; $i < $expr_len; $i++)
		{
			$char = $coordinate[$i];
			
			if (in_array($char, $operators))
			{
				$flush_operand = true;
				$flush_operator = true;
				$current_operator = $char;
			}
			else
			{
				$current_operand .= $char;
				if ($i == $expr_len - 1)
					$flush_operand = true;
			}
			
			if ($flush_operand)
			{
				if (trim($current_operand) != '')
					$tokens[] = array('type' => 'operand', 'value' => trim($current_operand));
				
				$current_operand = '';
				$flush_operand = false;
			}
			
			if ($flush_operator)
			{
				$tokens[] = array('type' => 'operator', 'value' => $char);
				$flush_operator = false;
			}
		}
		return $tokens;
	}
	
	/**
	 * Evaluates the $coord relatively to $dim
	 * 
	 * @param string $coord A numeric value or percent string
	 * @param int $dim Dimension
	 * @param int $sec_dim Secondary dimension (for align)
	 * @return int Calculated value
	 */
	static function evaluate($coord, $dim, $sec_dim = null)
	{
		$comp_regex = implode('|', self::$coord_align) . '|' . implode('|', self::$coord_numeric);
		if (preg_match("/^([+-])?({$comp_regex})$/", $coord, $matches))
		{
			$sign = intval($matches[1] . "1");
			$val = $matches[2];
			if (in_array($val, self::$coord_align))
			{
				if ($sec_dim === null)
				{
					switch ($val)
					{
						case 'left':
						case 'top':
							return 0;
							break;
						case 'center':
						case 'middle':
							return $sign * intval($dim / 2);
							break;
						case 'right':
						case 'bottom':
							return $sign * $dim;
							break;
						default:
							return null;
					}
				}
				else
				{
					switch ($val)
					{
						case 'left':
						case 'top':
							return 0;
							break;
						case 'center':
						case 'middle':
							return $sign * intval($dim / 2 - $sec_dim / 2);
							break;
						case 'right':
						case 'bottom':
							return $sign * ($dim - $sec_dim);
							break;
						default:
							return null;
					}
				}
			}
			elseif (substr($val, -1) === '%')
				return intval(round($sign * $dim * floatval(str_replace('%', '', $val)) / 100));
			else
				return $sign * intval(round($val));
		}
	}
	
	/**
	 * Calculates and fixes a smart coordinate into a numeric value
	 * 
	 * @param mixed $value Smart coordinate, relative to $dim
	 * @param int $dim Coordinate to which $value is relative
	 * @param int $sec_dim Secondary dimension (for align)
	 * @return int Calculated value
	 */
	static function fix($value, $dim, $sec_dim = null)
	{
		$coord_tokens = self::parse($value);
		
		if (count($coord_tokens) == 0 || $coord_tokens[count($coord_tokens) - 1]['type'] != 'operand')
		{
			JError::raiseError(500, JText::sprintf('JLIB_GDIMAGE_ERROR_PARSE_COORDINATE', $value) );
			return false;
		}
		
		$value = 0;
		$operation = 1;
		foreach ($coord_tokens as $token)
		{
			if ($token['type'] == 'operand')
			{
				$operand_value = self::evaluate($token['value'], $dim, $sec_dim);
				if ($operation == 1)
					$value = $value + $operand_value;
				elseif ($operation == -1)
					$value = $value - $operand_value;
				else
				{
					JError::raiseError(500, JText::_('JLIB_GDIMAGE_ERROR_INVALID_COORDINATE_SYNTAX') );
					return false;
				}
				
				$operation = 0;
			}
			elseif ($token['type'] == 'operator')
			{
				if ($token['value'] == '-')
				{
					if ($operation == 0)
						$operation = -1;
					else
						$operation = $operation * -1;
				}
				elseif ($token['value'] == '+')
				{
					if ($operation == 0)
						$operation = '1';
				}
			}
		}
		return $value;
		
		if ($coord['type'] === 'abs')
		{
			$result = self::evaluate($coord['value'], $dim, $sec_dim);
		}
		elseif ($coord['type'] === 'cal')
		{
			$p = self::evaluate($coord['pivot'], $dim, $sec_dim);
			$v = self::evaluate($coord['value'], $dim, $sec_dim);
			$result = $p + $v;
		}
		
		if ($clip)
		{
			if ($result < 0)
				return 0;
			elseif ($result >= $dim)
				return $dim;
		}
		return $result;
	}
	
	/**
	 * Fix a coordinate for a resize (limits by image weight and height)
	 * 
	 * @param GDImage_Image $img
	 * @param int $width Width of the image
	 * @param int $height Height of the image
	 * @return array An array(width, height), fixed for resizing
	 */
	static function fixForResize($img, $width, $height)
	{
		if ($width === null && $height === null)
			return array($img->getWidth(), $img->getHeight());
		
		if ($width !== null)
			$width = self::fix($width, $img->getWidth());
		
		if ($height !== null)
			$height = self::fix($height, $img->getHeight());
		
		if ($width === null)
			$width = floor($img->getWidth() * $height / $img->getHeight());
		
		if ($height === null)
			$height = floor($img->getHeight() * $width / $img->getWidth());
		
		return array($width, $height);
	}
}
