<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Upgrade Element
 * @author              Steven Palmer
 * @author url          https://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Gears is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once (JPATH_SITE . '/plugins/system/cwgears/fields/base.php');

class CWElementUpgrade extends CWElement {

    public function fetchElement($name, $value, &$node, $control_name) {
        return NULL;

    }

    public function fetchTooltip($label, $description, &$node, $control_name, $name) {

                return '<div class="cw-message-block">'
                . '<div class="cw-message">'
                . '<p class="upgrade">' . JText::_($label) . '</p>'
                . '</div></div>';
    }

}

class JFormFieldUpgrade extends CWElementUpgrade {

    var $type = 'upgrade';

}
