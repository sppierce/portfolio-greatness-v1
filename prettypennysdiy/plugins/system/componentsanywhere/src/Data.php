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

use JFactory;
use JRoute;
use JUri;
use RegularLabs\Library\RegEx as RL_RegEx;

class Data
{
	static $credentials = null;

	public static function get($url)
	{
		$params = Params::get();

		$url .= (strpos($url, '?') === false ? '?' : '&')
			. 'tmpl=component&rendercomponent=1&lang=' . JFactory::getLanguage()->getTag();

		$cookies = self::getCookies();

		if ($params->force_curl)
		{
			return Curl::get(self::getUrl($url), $cookies);
		}

		$data = Http::get(self::getUrl($url, $cookies));

		if ( ! empty($data))
		{
			return $data;
		}

		// Fall back on Curl if html is empty or not a json string
		return Curl::get(self::getUrl($url), $cookies);
	}

	private static function getCookies()
	{
		$params = Params::get();

		$cookies = [];

		if ( ! $params->pass_on_cookies)
		{
			$cookies = [];
		}

		foreach ($_COOKIE as $k => $v)
		{
			// Only include hexadecimal keys
			if ( ! RL_RegEx::match('^[a-f0-9]+$', $k))
			{
				continue;
			}

			$cookies[] = $k . '=' . $v;
		}

		return $cookies;
	}

	private static function getUrl($url, $cookies = [])
	{
		if ( ! empty($cookies))
		{
			$url .= '&' . implode('&', $cookies);
		}

		// Pass url through the JRoute if it is a non-SEF url
		if (strpos($url, 'index.php?') !== false)
		{
			$url = JRoute::_($url);
		}

		return JUri::getInstance()->toString(['scheme', 'user', 'pass', 'host', 'port']) . '/' . ltrim($url, '/');
	}

	public static function getAuthenticationCredentials()
	{
		if ( ! is_null(self::$credentials))
		{
			return self::$credentials;
		}

		if (isset($_SERVER['PHP_AUTH_USER']))
		{
			self::$credentials = [$_SERVER['PHP_AUTH_USER'], isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : ''];

			return self::$credentials;
		}

		if (isset($_SERVER['HTTP_AUTHENTICATION'])
			&& strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']), 'basic') === 0
		)
		{
			self::$credentials = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));

			return self::$credentials;
		}

		return false;
	}
}
