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

use JHttpFactory;
use RuntimeException;

class Http
{
	public static function get($url, $cookies = '')
	{
		$params = Params::get();

		// Site uses Kerberos authentication: use curl instead
		// Site is behind a login: use curl instead
		if (
			$params->use_negotiate_authentication
			|| Data::getAuthenticationCredentials()
		)
		{
			return false;
		}

		try
		{
			$html = JHttpFactory::getHttp()->get($url, null, $params->timeout)->body;
		}
		catch (RuntimeException $e)
		{
			return false;
		}

		return $html;
	}
}
