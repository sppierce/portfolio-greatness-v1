<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb K2 Category Field
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
jimport('joomla.form.formfield');

class JFormFieldK2Category extends JFormField {

    var $type = 'k2category';

    function getInput() {
        $db = JFactory::getDBO();
        $fieldName = $this->name . '[]';

        if (file_exists(JPATH_BASE . '/components/com_k2')) {
            $query = $db->getQuery(true);
            
            $query->select('m.*');
            $query->from('#__k2_categories AS m');
            $query->where('trash = 0');
            $query->where('published = 1');
            $query->order('parent ASC');
            $query->order('ordering ASC');
            
            $db->setQuery($query);
            
            try {
                $mitems = $db->loadObjectList();
            } catch (Exception $exc) {
                $mitems = '';
                return;
            }
            
            
            if (count($mitems)) {
                $children = array();
                if ($mitems) {
                    foreach ($mitems as $v) {
                        $v->title = $v->name;
                        $v->parent_id = $v->parent;
                        $pt = $v->parent;
                        $list = @$children[$pt] ? $children[$pt] : array();
                        array_push($list, $v);
                        $children[$pt] = $list;
                    }
                }
                $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
                $mitems = array();

                foreach ($list as $item) {
                    $item->treename = JString::str_ireplace('&#160;', '- ', $item->treename);
                    $mitems[] = JHTML::_('select.option', $item->id, '   ' . $item->treename);
                }

                $output = JHTML::_('select.genericlist', $mitems, $fieldName, 'class="inputbox" multiple="multiple" size="10"', 'value', 'text', $this->value);
            } else {
                $mitems[] = JHTML::_('select.option', 0, 'There is no K2 category available.');
                $output = JHtml::_('select.genericlist', $mitems, $fieldName, 'class="inputbox" disabled="disabled" multiple="multiple" style="width:160px" size="5"', 'value', 'text', $this->value);
            }
        } else {
            $mitems = array();
            $mitems[] = JHTML::_('select.option', 0, 'K2 is not installed');
            $output = JHtml::_('select.genericlist', $mitems, $fieldName, 'class="inputbox" disabled="disabled" multiple="multiple" style="width:160px" size="5"', 'value', 'text', $this->value);
        }

        return $output;
    }

}