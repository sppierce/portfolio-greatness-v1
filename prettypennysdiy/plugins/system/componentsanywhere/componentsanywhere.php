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

defined('_JEXEC') or die;

if ( ! is_file(__DIR__ . '/vendor/autoload.php'))
{
	return;
}

require_once __DIR__ . '/vendor/autoload.php';

use RegularLabs\Plugin\System\ComponentsAnywhere\Plugin;

/**
 * Plugin that loads components
 */
class PlgSystemComponentsAnywhere extends Plugin
{
	public $_alias       = 'componentsanywhere';
	public $_title       = 'COMPONENTS_ANYWHERE';
	public $_lang_prefix = 'CA';

	public $_has_tags = true;
}
