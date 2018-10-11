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
use RegularLabs\Library\Article as RL_Article;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Html as RL_Html;
use RegularLabs\Library\RegEx as RL_RegEx;

/**
 * Plugin that replaces stuff
 */
class Helper
{
	var $rendercomponent = null;

	public function onContentPrepare($article, $context, $params)
	{
		$area    = isset($article->created_by) ? 'article' : 'other';
		$context = (($params instanceof \JRegistry) && $params->get('rl_search')) ? 'com_search.' . $params->get('readmore_limit') : $context;

		RL_Article::process($article, $context, $this, 'processComponents', [$area, $context, $article]);

		return $article;
	}

	public function onAfterDispatch()
	{
		if ( ! $buffer = RL_Document::getBuffer())
		{
			return;
		}

		if (JFactory::getApplication()->input->get('rendercomponent'))
		{
			$this->rendercomponent = Component::getObject($buffer);
		}

		if ( ! Replace::replaceTags($buffer, 'component'))
		{
			return;
		}

		RL_Document::setBuffer($buffer);
	}

	public function onAfterRender()
	{
		$html = JFactory::getApplication()->getBody();

		if ($html == '')
		{
			return;
		}

		if (RL_Document::isFeed())
		{
			Replace::replaceTags($html);

			Clean::cleanLeftoverJunk($html);

			JFactory::getApplication()->setBody($html);

			return;
		}

		list($pre, $body, $post) = RL_Html::getBody($html);

		if (JFactory::getApplication()->input->get('rendercomponent'))
		{
			$body = RL_RegEx::replace('^\s*<body.*?>\s*', '', $body);
			$body = RL_RegEx::replace('\s*</body>\s*$', '', $body);

			$this->rendercomponent->html = $body;

			Component::render($this->rendercomponent);

			return;
		}

		// only do stuff in body
		Replace::replaceTags($body, 'body');

		Document::placeScriptsAndStyles($pre, $body);
		$html = $pre . $body . $post;

		Clean::cleanLeftoverJunk($html);

		JFactory::getApplication()->setBody($html);
	}

	public function processComponents(&$string, $area = 'article', $context = '', $article = null)
	{
		Replace::processComponents($string, $area, $context, $article);
	}
}
