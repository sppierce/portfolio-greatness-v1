<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Dependent Element
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

class CWElementDependent extends CWElement {

    public function fetchElement($name, $value, &$node, $control_name) {
        return NULL;
    }

    public function fetchTooltip($label, $description, &$node, $control_name, $name) {
        
                // Load version.php
        jimport('joomla.filesystem.file');
        $arr = explode("_", $description);
        
        switch ($arr[0]) {
            case "com":
                $url = JPATH_ADMINISTRATOR . '/' . 'components/' . $description . '/version.php';
                $check = JComponentHelper::isEnabled($description, true);
                break;
            case "mod":
                $url = JPATH_SITE . '/' . 'modules/' . $description . '/version.php';
                break;
            case "plg":
                $url = JPATH_SITE . '/' . 'plugins/' . $arr[1] . '/' . $arr[2] . '/version.php';
                $check = JPluginHelper::isEnabled($arr[1], $arr[2], true);
                break;
        }   
        
        if (!file_exists($url) || !$check){
            return '<div class="cw-message-block">'
                    . '<div class="cw-message">'
                    . '<p class="alert">' . JText::_($label . '_MSG_DEPENDENT') . '</p>'
                    . '</div></div>';
        }
    }

}

class JFormFieldDependent extends CWElementDependent {
    var $type = 'dependent';
}
