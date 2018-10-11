<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Datetime Element
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

// Add CSS and JS some are local to stop potential mix content errors
$gearsMedia = JURI::root() . 'media/coalaweb/plugins/system/gears/';
JHtml::stylesheet('https://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css');
JHtml::stylesheet($gearsMedia . 'css/bootstrap-datetimepicker.min.css');
JHtml::script($gearsMedia . 'js/bootstrap-datetimepicker.min.js');

jimport('joomla.form.formfield');

class JFormFieldDateTime extends JFormField {

    protected $type = 'DateTime';

    public function getInput() {

         // Detect language
        $lang = JFactory::getLanguage();
        $locale = $lang->getTag();
        
        //Date time format
        $format = 'dd/MM/yyyy HH:mm:ss PP';
        
        // Input field
        $output[] = '<div id="datetimepicker' . $this->id . '" class="input-append">'; 
        $output[] = '<input '
                . 'data-format="' . $format . '" '
                . 'name="' . $this->name . '" '
                . 'value="' . $this->value . '" '
                . 'type="text" '
                . 'placeholder="' . $this->hint . '">'
                . '</input>';
        $output[] = '<span class="add-on">';
        $output[] = '<i data-time-icon="icon-time"></i>';
        $output[] = '</span>';
        $output[] = '</div>';

        //jQuery code
        $output[] = '<script type="text/javascript">';
        $output[] = 'jQuery(function() {';
        $output[] = '   jQuery("#datetimepicker' . $this->id . '").datetimepicker({';
        $output[] = '       language: "' . $locale . '",';
        $output[] = '       pick12HourFormat: false,';
        $output[] = '       format: "' . $format . '"';
        $output[] = '   });';
        $output[] = '});';
        $output[] = '</script>';

        return implode("\n", $output);
    }
}