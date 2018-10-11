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
use JText;
use RegularLabs\Library\Cache as RL_Cache;
use RegularLabs\Library\RegEx as RL_RegEx;

class Component
{
	public static function getObject($body = '')
	{
		$doc = JFactory::getDocument();

		return (object) [
			'script'  => self::getScriptDeclaration(),
			'scripts' => $doc->_scripts,
			'style'   => $doc->_style,
			'styles'  => $doc->_styleSheets,
			'custom'  => $doc->_custom,
			'html'    => $body,
			'token'   => JFactory::getSession()->getFormToken(),
		];
	}

	private static function getScriptDeclaration()
	{
		$script = JFactory::getDocument()->_script;

		$strings = JVERSION < '3.7' ? JText::script() : JText::getScriptStrings();

		if (empty($strings))
		{
			return $script;
		}

		// Adding it to javascript2 to force it into its own <script> block
		// This prevents some conflicts
		// The text/javascript2 will get replaced with text/javascript when adding to the doc

		$javascript = '\n'
			. '\t\t' . '(function() {' . '\n'
			. '\t\t\t' . 'Joomla.JText.load(' . json_encode($strings) . ');' . '\n'
			. '\t\t' . '})();' . '\n';

		if ( ! isset($script['text/javascript2']))
		{
			$script['text/javascript2'] = '';
		}

		$script['text/javascript2'] .= self::formatJavascriptString($javascript);

		return $script;
	}

	private static function formatJavascriptString($string)
	{
		return str_replace(
			array('\t', '\n'),
			array(JFactory::getDocument()->_getTab(), JFactory::getDocument()->_getLineEnd()),
			$string
		);
	}

	public static function render($object)
	{
		header('Content-Type: application/json');
		echo json_encode($object);
		die();
	}

	public static function createJsonString($html)
	{
		return json_encode((object) ['html' => $html]);
	}

	public static function get($url, $cache)
	{
		if ($cache)
		{
			$cacheid = 'componentsanywhere'
				. '_' . $url
				. '_' . JFactory::getLanguage()->getTag()
				. '_' . implode('.', JFactory::getUser()->getAuthorisedGroups());

			$data = RL_Cache::read($cacheid);

			if ( ! empty($data))
			{
				RL_Cache::write($cacheid, $data);

				return $data;
			}
		}

		$data = Data::get($url);

		if ( ! empty($data))
		{
			$data = self::convertHtmlToObject($data);
		}

		if ($cache)
		{
			RL_Cache::write($cacheid, $data);
		}

		return $data;
	}

	private static function convertHtmlToObject($data)
	{
		if (empty($data) || $data == '{}')
		{
			return false;
		}

		// remove possible leading encoding characters
		$data = RL_RegEx::replace('^.*?\{', '{', $data);

		$data = json_decode($data);
		if (is_null($data) || empty($data))
		{
			return false;
		}

		return $data;
	}
}
