<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Sorting Field
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
class JFormFieldSorting extends JFormField {

    protected $type = 'Sorting';

    protected function getInput() {
        $document = JFactory::getDocument();

        //Include base jQuery
        if (version_compare(JVERSION, '3.0', '>')) {
            JHtml::_('jquery.framework');
        } else {
            $jquery = "//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js";
            $document->addScript($jquery);
        }

        $baseurl = JURI::base(true);
        $baseurl = str_replace('administrator', '', $baseurl);
        $scriptname = $this->element['scriptpath'] ? (string) $this->element['scriptpath'] : $baseurl . 'media/coalaweb/modules/generic/js/jquery-sortable-min.js';

        //try to find the script
        if ($scriptname == 'self') {
            $filedir = str_replace(JPATH_SITE . '/', '', dirname(__FILE__));
            $filedir = str_replace('\\', '/', $filedir);
            $scriptname = $baseurl . $filedir . '/jquery-sortable-min.js';
        }

        $document->addScript($scriptname);

        // Now initialize the plugin
        $document->addScriptDeclaration('
            jQuery(document).ready(function($) {
                var group = $("#sortable").sortable({
                    pullPlaceholder: false,
                    onDrop: function(item, container, _super) {
                        $("#' . $this->id . '").val(group.sortable("serialize").get().join("\n"))
                        _super(item, container)
                    },
                    serialize: function(parent, children, isContainer) {
                        return isContainer ? children.join() : parent.attr("id")
                    },
                })
            });
        ');

        // Get possible new options.
        $possibleNew = '';
        if ($this->name === 'jform[params][sorting_tabs]') {
            $default = 'facebook_s,twitter_s,google_s,linkedin_s,pinterest_s,digg_s,stumbleupon_s,reddit_s,email_s,whatsapp_s,facebook_f,twitter_f,google_f,linkedin_f,pinterest_f,rss_f,contact_f,customone_c,customtwo_c';
            $possibleNew = explode(',', $default);
        }
        // Explode currently stored values.
        $currentItems = explode(',', $this->value);

        //Are there any new options?
        $newItems = array_diff($possibleNew, $currentItems);

        //Append new options to list.
        $items = array_merge((array) $currentItems, (array) $newItems);
        
        //Create a string from our new appended array
        $cvs = join(',', $items);

        $input = '<ul id="sortable">';

        foreach ($items as $item) {
            $parts = explode('_', $item);
            $name = $parts[0];
            $type = $parts[1];
            $input .= '<li class="sort" id="' . $item . '"><span></span>' . ucfirst($name) . ' [' . strtoupper($type) . ']</li>';
        }

        $input .= '</ul>
		<input type="hidden" name="' . $this->name . '" value="' . $cvs . '" id="' . $this->id . '" />';
        return $input;
    }

}
