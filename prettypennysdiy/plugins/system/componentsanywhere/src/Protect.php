<?php
/**
 * @package         Components Anywhere
 * @version         4.1.10
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2017 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Plugin\System\ComponentsAnywhere;

defined('_JEXEC') or die;

use RegularLabs\Library\Protect as RL_Protect;

class Protect
{
	static $name = 'Components Anywhere';

	public static function _(&$string)
	{
		RL_Protect::protectFields($string, Params::getTags(true));
		RL_Protect::protectSourcerer($string);
	}

	public static function protectTags(&$string)
	{
		RL_Protect::protectTags($string, Params::getTags(true));
	}

	public static function unprotectTags(&$string)
	{
		RL_Protect::unprotectTags($string, Params::getTags(true));
	}

	/**
	 * Wrap the comment in comment tags
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function wrapInCommentTags($string)
	{
		return RL_Protect::wrapInCommentTags(self::$name, $string);
	}

	/**
	 * Get the html comment tag containing a message
	 *
	 * @param string $message
	 *
	 * @return string
	 */
	public static function getMessageCommentTag($message)
	{
		return RL_Protect::getMessageCommentTag(self::$name, $message);
	}
}
