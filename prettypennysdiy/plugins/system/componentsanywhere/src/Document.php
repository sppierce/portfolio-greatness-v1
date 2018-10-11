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
use Joomla\Utilities\ArrayHelper;
use RegularLabs\Library\RegEx as RL_RegEx;

class Document
{
	public static function placeScriptsAndStyles(&$head, &$body)
	{
		if (
			strpos($head, '</head>') === false
			&& strpos($body, '<!-- CA HEAD START') === false
		)
		{
			return;
		}

		RL_RegEx::matchAll('<!-- CA HEAD START STYLES -->(.*?)<!-- CA HEAD END STYLES -->', $body, $matches);

		if ( ! empty($matches))
		{
			$styles = '';
			foreach ($matches as $match)
			{
				$styles .= $match['1'];

				$body = str_replace($match['0'], '', $body);
			}

			$add_before = '</head>';
			if (RL_RegEx::match('<link [^>]+templates/', $body, $add_before_match))
			{
				$add_before = $add_before_match['0'];
			}

			$head = str_replace($add_before, $styles . $add_before, $head);
		}

		RL_RegEx::matchAll('<!-- CA HEAD START SCRIPTS -->(.*?)<!-- CA HEAD END SCRIPTS -->', $body, $matches, null, PREG_SET_ORDER);

		if ( ! empty($matches))
		{
			$scripts = '';
			foreach ($matches as $match)
			{
				$scripts .= $match['1'];

				$body = str_replace($match['0'], '', $body);
			}

			$add_before = '</head>';
			if (RL_RegEx::match('<script [^>]+templates/', $body, $add_before_match))
			{
				$add_before = $add_before_match['0'];
			}

			$head = str_replace($add_before, $scripts . $add_before, $head);
		}

		self::removeDuplicatesFromHead($head, '<link[^>]*>');
		self::removeDuplicatesFromHead($head, '<style.*?</style>');
		self::removeDuplicatesFromHead($head, '<script.*?</script>');
	}

	public static function addScriptsAndStyles(&$data, $area = '')
	{
		$doc = JFactory::getDocument();

		$data->styles  = ! empty($data->styles) ? $data->styles : [];
		$data->style   = ! empty($data->style) ? $data->style : [];
		$data->scripts = ! empty($data->scripts) ? $data->scripts : [];
		$data->script  = ! empty($data->script) ? $data->script : [];

		self::removeDuplicatesFromObject($data->styles, $doc->_styleSheets);
		self::removeDuplicatesFromObject($data->style, $doc->_style, 1);
		self::removeDuplicatesFromObject($data->scripts, $doc->_scripts);
		self::removeDuplicatesFromObject($data->script, $doc->_script, 1);

		if ($area == 'article')
		{
			self::addScriptsAndStylesToDocument($data);

			return;
		}

		self::addScriptsAndStylesInline($data);
	}

	private static function addScriptsAndStylesToDocument(&$data)
	{
		$doc = JFactory::getDocument();

		foreach ($data->styles as $style => $options)
		{
			if (JVERSION < '3.7')
			{
				$doc->addStyleSheet($style, $options->mime, $options->media, $options->attribs);
				continue;
			}

			$doc->addStyleSheet($style, (array) $options->options);
		}

		foreach ($data->style as $type => $content)
		{
			$doc->addStyleDeclaration($content, $type);
		}

		foreach ($data->scripts as $script => $options)
		{
			if (JVERSION < '3.7')
			{
				$doc->addScript($script, $options->mime, $options->defer, $options->async);
				continue;
			}

			$doc->addScript($script, (array) $options->options);
		}

		foreach ($data->script as $type => $content)
		{
			$doc->addScriptDeclaration($content, str_replace('javascript2', 'javascript', $type));
		}

		foreach ($data->custom as $content)
		{
			$doc->addCustomTag($content);
		}
	}

	private static function addScriptsAndStylesInline(&$data)
	{
		$inline_head_styles  = [];
		$inline_head_scripts = [];

		// Generate stylesheet links
		foreach ($data->styles as $style => $options)
		{
			$inline_head_styles[] = self::styleToString($style, $options) . "\n";
		}

		// Generate stylesheet declarations
		foreach ($data->style as $type => $content)
		{
			$inline_head_styles[] = '<style type="' . $type . '">' . "\n"
				. $content . "\n"
				. $inline_head[] = '</style>' . "\n";
		}

		// Generate script file links
		foreach ($data->scripts as $script => $options)
		{
			$inline_head_scripts[] = self::scriptToString($script, $options) . "\n";
		}

		// Generate script declarations
		foreach ($data->script as $type => $content)
		{
			$inline_head_scripts[] = '<script type="' . str_replace('javascript2', 'javascript', $type) . '">' . "\n"
				. $content . "\n"
				. '</script>' . "\n";
		}

		if ( ! empty($data->custom))
		{
			$inline_head_scripts[] = is_array($data->custom)
				? implode("\n", $data->custom)
				: (string) $data->custom;
		}

		if ( ! empty($inline_head_styles))
		{
			$data->html = '<!-- CA HEAD START STYLES -->' . implode('', $inline_head_styles) . '<!-- CA HEAD END STYLES -->' . $data->html;
		}

		if ( ! empty($inline_head_scripts))
		{
			$data->html = '<!-- CA HEAD START SCRIPTS -->' . implode('', $inline_head_scripts) . '<!-- CA HEAD END SCRIPTS -->' . $data->html;
		}
	}

	private static function styleToString($style, $options)
	{
		$attributes = '';
		$attributes .= ! empty($options->media) ? ' media="' . $options->media . '"' : '';
		$attributes .= ! empty($options->type) ? ' type="' . $options->type . '"' : '';
		$attributes .= ! empty($options->mime) ? ' type="' . $options->mime . '"' : '';
		$attributes .= ! empty($options->attribs) ? ' ' . ArrayHelper::toString((array) $options->attribs) : '';

		return '<link rel="stylesheet" href="' . $style . '"' . $attributes . '>';
	}

	private static function scriptToString($script, $options)
	{
		$attributes = '';
		$attributes .= ! empty($options->type) ? ' type="' . $options->type . '"' : '';
		$attributes .= ! empty($options->mime) ? ' type="' . $options->mime . '"' : '';
		$attributes .= ! empty($options->defer) ? ' defer="defer"' : '';
		$attributes .= ! empty($options->async) ? ' async="async"' : '';

		return '<script src="' . $script . '"' . $attributes . '></script>';
	}

	private static function removeDuplicatesFromObject(&$obj, $doc, $match_value = 0)
	{
		if (empty($obj))
		{
			return;
		}

		foreach ($obj as $key => $val)
		{
			if (isset($doc[$key]) && ( ! $match_value || $doc[$key] == $val))
			{
				unset($obj->{$key});
			}
		}
	}

	private static function removeDuplicatesFromHead(&$head, $regex = '')
	{
		RL_RegEx::matchAll($regex, $head, $matches, null, PREG_PATTERN_ORDER);

		if (empty($matches))
		{
			return;
		}

		$tags = [];

		foreach ($matches['0'] as $tag)
		{
			if ( ! in_array($tag, $tags))
			{
				$tags[] = $tag;
				continue;
			}

			$tag  = RL_RegEx::quote($tag);
			$head = RL_RegEx::replace('(' . $tag . '.*?)\s*' . $tag, '\1', $head);
		}
	}
//
//	private static function removeDuplicatesScriptBlocksFromHead(&$head)
//	{
//		$regex = '<script>\s*(.+?)\s*</script>';
//
//		RL_RegEx::matchAll($regex, $head, $matches);
//
//		if (empty($matches))
//		{
//			return;
//		}
//
//		foreach ($matches as $script)
//		{
//			$new_script = self::removeDuplicatesScriptBlocksFromScript($script);
//
//			$head = str_replace($script[0], $new_script, $head);
//		}
//	}
//
//	private static function removeDuplicatesScriptBlocksFromScript($script)
//	{
//
//		$new_script = $script[1];
//
//		$regex = '^(.{30,})\1';
//		while (RL_RegEx::matchAll($regex, $new_script, $matches, null, PREG_PATTERN_ORDER))
//		{
//			$new_script = str_replace($matches[0], $matches[1], $script[1]);
//		}
//
//		$regex = '(function .{30,}\})\s*\1';
//		while (RL_RegEx::matchAll($regex, $new_script, $matches, null, PREG_PATTERN_ORDER))
//		{
//			$new_script = str_replace($matches[0], $matches[1], $script[1]);
//		}
//
//		return str_replace($script[1], $new_script, $script[0]);
//	}
}
