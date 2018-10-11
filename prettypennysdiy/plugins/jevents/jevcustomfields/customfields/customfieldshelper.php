<?php
/**
 * Copyright (C)2010-2015 GWE Systems Ltd
 *
 * All rights reserved.
 *
 */
defined('_JEXEC') or die('No Direct Access');

jimport("joomla.html.pagination");

class CustomFieldsHelper {

    static function booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip = false, $attribs = "", $style = "", $defaultvalue = 0) {
        $fieldid = str_replace(array("[", "]"), "", $fieldname);
        ?>
        <div class="jevcfnotranslate <?php echo $fieldid; ?>" id="<?php echo $fieldid; ?>" style="<?php echo $style; ?>">
            <div class="jevcflabel">
        <?php echo $fieldlabel; ?>
                <?php CustomFieldsHelper::fieldTooltip($fieldtooltip); ?>
            </div>
            <div class="jevcfinputs radio btn-group">
                <label for="<?php echo $fieldid1; ?>" class="btn radio"><?php echo JText::_("JEVCF_YES"); ?>
                    <input type="radio" name="<?php echo $fieldname; ?>"  id="<?php echo $fieldid1; ?>" value="1" <?php
                if ($field && $field->attribute($fieldattribute) == 1) {
                    echo 'checked="checked"';
                } else if (!$field && $defaultvalue) {
                    echo 'checked="checked"';
                }
                echo $attribs;
                ?> />
                </label>
                <label for="<?php echo $fieldid0; ?>"  class="btn radio"><?php echo JText::_("JEVCF_NO"); ?>
                    <input type="radio" name="<?php echo $fieldname; ?>"  id="<?php echo $fieldid0; ?>" value="0" <?php
                    if ($field && $field->attribute($fieldattribute) == 0) {
                        echo 'checked="checked"';
                    } else if (!$field && !$defaultvalue) {
                        echo 'checked="checked"';
                    }
                    echo $attribs;
                    ?> />
                </label>
            </div>
        </div>
        <div class="jevcfclear"></div>
                    <?php
                }

                static function textField($id, $field, $label, $tooltip, $fieldprefix, $fieldattribute, $fielddefault = "", $attributes = "") {
                    ?>
        <div class="jevcflabel">
            <label for="ft<?php echo $id; ?>">
        <?php echo $label; ?>
        <?php CustomFieldsHelper::fieldTooltip($tooltip); ?>
            </label>
        </div>
        <div class="jevcfinputs">
            <input type="text" name="<?php echo $fieldprefix . "[" . $id . "]"; ?>" id="<?php echo $fieldprefix . $id; ?>" <?php echo $attributes; ?>  value="<?php echo $field ? htmlspecialchars($field->attribute($fieldattribute)) : $fielddefault; ?>" size="40" maxlength="255" />
        </div>
        <div class="jevcfclear"></div>
                <?php
            }

            static function fieldTooltip($fieldtooltip = false) {
                JHTML::_('behavior.tooltip');
                if ($fieldtooltip && strpos($fieldtooltip, "JEVCF_") !== 0 && strpos(JText::_($fieldtooltip), "JEVCF_") !== 0) {
                    //echo " " . JHtml::_('tooltip', $fieldtooltip, null, null, '<span class="icon-comment fieldtooltip"></span>', null, 0);
                    echo " " . JHtml::_('tooltip', $fieldtooltip, null, null, '<span class="icon-help fieldtooltip"></span>', null, 0);
                }
            }

            static function setImage($id, $field, $label, $tooltip, $fieldprefix, $fieldattribute, $fielddefault = "") {
                ?>
        <div class="jevcflabel">
            <label for="ft<?php echo $id; ?>">
        <?php echo $label; ?>
        <?php CustomFieldsHelper::fieldTooltip($tooltip); ?>
            </label>
        </div>
        <div class="jevcfinputs">
            <input type="text" name="<?php echo $fieldprefix . "[" . $id . "]"; ?>" id="<?php echo $fieldprefix . $id; ?>" 
                   value="<?php echo $field ? $field->attribute($fieldattribute) : $fielddefault; ?>" size="80" maxlength="255"
                   onchange="jQuery('.jevcf_setimage<?php echo $fieldprefix . $id; ?>').prop('src', jQuery(this).val());"
                   />
            <img src="<?php echo $field ? $field->attribute($fieldattribute) : $fielddefault; ?>" class="jevcf_setimage jevcf_setimage<?php echo $fieldprefix . $id; ?>"/>
        </div>
        <div class="jevcfclear"></div>
                <?php
            }

            static function accessOptions($id, $field) {
                $fieldname = "fa[$id]";
                $value = $field ? $field->attribute('access') : 0;
                $style = '';
                ?>
        <div class="jevcfnotranslate">
            <div class="jevcflabel" <?php echo $style; ?> >
        <?php echo JText::_("JEVCF_ACCESS"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_EDIT_ACCESS_DESC')); ?>
            </div>
            <div class="jevcfinputs  radio btn-group"  <?php echo $style; ?> >
        <?php
        include_once(JPATH_SITE . "/components/com_jevents/libraries/jeventshtml.php");
        if (is_callable(array("JEventsHTML", "buildAccessSelect"))) {
            echo JEventsHTML::buildAccessSelect((int) $value, 'class="inputbox" size="1" ', "", $fieldname);
        } else {
            static $groups;
            if (!isset($groups)) {
                // get list of groups
                $db = JFactory::getDBO();
                $query = "SELECT id AS value, name AS text"
                        . "\n FROM #__groups"
                        . "\n ORDER BY id";
                $db->setQuery($query);
                $groups = $db->loadObjectList();
            }

            // build the html select list
            echo JHtml::_('select.genericlist', $groups, $fieldname, 'class="inputbox" size="1"', 'value', 'text', $value);
        }


        // field access flag - everyone apart from members of this group can access if this has value 1
        $fieldname = "faf[$id]";
        $value = $field ? $field->attribute('accessflag') : 1;
        ?>
            </div>
            <!--
            <div class="jevcflabel" <?php echo $style; ?> ></div>
            <div class="jevcfinputs  radio btn-group"   <?php echo $style; ?> >
                    <label for="accessflag1<?php echo $id; ?>" class="btn radio" ><?php echo JText::_("JEVCF_ALLOWED_ACCESS"); ?>
                    <input type="radio" name="accessflag[<?php echo $id; ?>]"    id="accessflag1<?php echo $id; ?>" value="1" <?php
                if ($field && $field->attribute('accessflag') == 1) {
                    echo 'checked="checked"';
                }
                if (!$field) {
                    echo 'checked="checked"';
                }
                ?> />
                    </label>
                    <label for="accessflag0<?php echo $id; ?>" class="btn radio" ><?php echo JText::_("JEVCF_ACCESS_BLOCKED"); ?>
                    <input type="radio" name="accessflag[<?php echo $id; ?>]"   id="accessflag0<?php echo $id; ?>" value="0" <?php
        if ($field && $field->attribute('accessflag') == 0) {
            echo 'checked="checked"';
        }
        ?> />
                    </label>
            </div>
        //-->
            <div class="jevcfclear"></div>
        </div>
            <?php
        }

        static function readaccessOptions($id, $field) {
            $fieldname = "fra[$id]";
            $value = $field ? $field->attribute('readaccess') : 0;
            $style = '';
            ?>
        <div class="jevcfnotranslate">
            <div class="jevcflabel" <?php echo $style; ?> >
            <?php echo JText::_("JEVCF_READ_ACCESS"); ?>
            <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_READ_ACCESS_DESC')); ?>
            </div>
            <div class="jevcfinputs  radio btn-group"  <?php echo $style; ?> >
        <?php
        include_once(JPATH_SITE . "/components/com_jevents/libraries/jeventshtml.php");
        echo JEventsHTML::buildAccessSelect((int) $value, 'class="inputbox" size="1" ', "", $fieldname);
        ?>
            </div>

            <div class="jevcfclear"></div>
        </div>
        <?php
    }

    static function searchable($id, $field) {
        $fieldname = "searchable[$id]";
        $fieldid0 = "searchable0$id";
        $fieldid1 = "searchable1$id";
        $fieldlabel = JText::_("JEVCF_SEARCHABLE");
        $fieldattribute = "searchable";
        $fieldtooltip = JText::_("JEVCF_SEARCHABLE_DESC");
        CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip);
    }

    static function scramble($id, $field) {
        $fieldname = "scramble[$id]";
        $fieldid0 = "scramble0$id";
        $fieldid1 = "scramble1$id";
        $fieldlabel = JText::_("JEVCF_SCRAMBLE");
        $fieldattribute = "scramble";
        $fieldtooltip = JText::_("JEVCF_SCRAMBLE_DESC");
        CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip);
    }

    static function fieldclass($id, $field) {
        $fieldname = "class";
        $fieldlabel = JText::_("JEVCF_CLASS");
        $fieldattribute = "class";
        $fieldtooltip = JText::_("JEVCF_CLASS_DESC");
        $fielddefault = "";
        CustomFieldsHelper::textField($id, $field, $fieldlabel, $fieldtooltip, $fieldname, $fieldattribute, $fielddefault);
    }

    static function filterOptions($id, $field) {
        $fieldname = "fo[$id]";
        $fieldid0 = "fo0$id";
        $fieldid1 = "fo1$id";
        $fieldlabel = JText::_("JEVCF_ENABLE_FILTERS");
        $fieldattribute = "filter";
        $fieldtooltip = JText::_("JEVCF_ENABLE_FILTERS_DESC");
        $attribs = " onclick=\"jevcfforms.revealConditionalDisplayField(['fmo" . $id . "','fo" . $id . "'], 'mf" . $id . "' );jevcfforms.revealConditionalDisplayField(['fmo" . $id . "','fo" . $id . "'], 'filterdefault" . $id . "' );jevcfforms.revealConditionalDisplayField(['fmo" . $id . "','fo" . $id . "'], 'filterlabel" . $id . "' ); \"";
        CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip, $attribs);
        
        ?>
        <div class="jevcfnotranslate filterlabel filterlabel<?php echo $id; ?>" style="display:none;">
            <div class="jevcflabel">
                <label for="filterlabel<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_FILTER_LABEL"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FILTER_LABEL_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input  type="text" name="filterlabel[<?php echo $id; ?>]" id="filterlabel<?php echo $id; ?>" value="<?php echo $field ? $field->attribute('filterlabel') : ''; ?>" size="40" maxlength="255" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
        if ($field) {
            ?>
            <script type="text/javascript">
                jevcfforms.revealConditionalDisplayField(['fmo<?php echo $id; ?>', 'fo<?php echo $id; ?>'], 'filterlabel<?php echo $id; ?>');
            </script>
            <?php
        }
        
    }

    static function filtermenuOptions($id, $field) {
        $fieldname = "fmo[$id]";
        $fieldid0 = "fmo0$id";
        $fieldid1 = "fmo1$id";
        $fieldlabel = JText::_("JEVCF_ENABLE_MENU_MODULE_FILTERS");
        $fieldattribute = "filtermenusandmodules";
        $fieldtooltip = JText::_("JEVCF_ENABLE_MENU_MODULE_FILTERS_DESC");
        $attribs = " onclick=\"jevcfforms.revealConditionalDisplayField(['fmo" . $id . "','fo" . $id . "'], 'mf" . $id . "' );jevcfforms.revealConditionalDisplayField(['fmo" . $id . "','fo" . $id . "'], 'filterdefault" . $id . "' ); \"";
        CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip, $attribs);
    }

    static function multiFilter($id, $field) {
        $fieldname = "mf[$id]";
        $fieldid0 = "mf0$id";
        $fieldid1 = "mf1$id";
        $fieldlabel = JText::_("JEVCF_ENABLE_MULTI_FILTERS");
        $fieldattribute = "multifilter";
        $fieldtooltip = JText::_("JEVCF_ENABLE_MULTI_FILTERS_DESC");
        $attribs = " onclick=\"jevcfforms.revealConditionalDisplayField('mf" . $id . "', 'fs" . $id . "' ); \" ";
        $style = "display:none;";
        CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip, $attribs, $style);
    }

    static function filterSize($id, $field) {
        ?>
        <div class="jevcfnotranslate filtersize fs<?php echo $id; ?>" style="display:none;">
            <div class="jevcflabel">
                <label for="fs<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_FILTER_SIZE"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FILTER_SIZE_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input  type="text" name="fs[<?php echo $id; ?>]" id="fs<?php echo $id; ?>" value="<?php echo $field ? $field->attribute('filtersize') : '3'; ?>" size="40" maxlength="255" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
        if ($field) {
            ?>
            <script type="text/javascript">
                jevcfforms.revealConditionalDisplayField('mf<?php echo $id; ?>', 'fs<?php echo $id; ?>');
            </script>
            <?php
        }
    }

    static function filterDefault($id, $field) {
        ?>
        <div class="jevcfnotranslate filterdefault filterdefault<?php echo $id; ?>" style="display:none;">
            <div class="jevcflabel">
                <label for="filterdefault<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_FILTER_DEFAULT"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FILTER_DEFAULT_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input  type="text" name="filterdefault[<?php echo $id; ?>]" id="filterdefault<?php echo $id; ?>" value="<?php echo $field ? $field->attribute('filterdefault') : ''; ?>" size="40" maxlength="255" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
        if ($field) {
            ?>
            <script type="text/javascript">
                jevcfforms.revealConditionalDisplayField(['fmo<?php echo $id; ?>', 'fo<?php echo $id; ?>'], 'filterdefault<?php echo $id; ?>');
            </script>
            <?php
        }
    }

    static function required($id, $field) {
        $fieldname = "rr[$id]";
        $fieldid0 = "rr0$id";
        $fieldid1 = "rr1$id";
        $fieldlabel = JText::_("JEVCF_IS_REQUIRED");
        $fieldattribute = "required";
        $fieldtooltip = JText::_("JEVCF_IS_REQUIRED_DESC");
        $attribs = " onclick=\"jevcfforms.revealConditionalDisplayField('rr" . $id . "', 'rm" . $id . "' ) \"";
        CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip, $attribs);
    }

    static function contact($id, $field) {
        $fieldname = "ct[$id]";
        $fieldid0 = "ct0$id";
        $fieldid1 = "ct1$id";
        $fieldlabel = JText::_("JEVCF_OUTPUT_AS_CONTACT");
        $fieldattribute = "contact";
        $fieldtooltip = JText::_("JEVCF_OUTPUT_AS_CONTACT_DESC");
        $attribs = " onclick=\"jevcfforms.revealConditionalDisplayField('ct" . $id . "', 'pf" . $id . "' , 0) \"";
        CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip, $attribs);
    }

    static function usergroups($id, $field, $label = false, $tooltip = false) {
        $options = array();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('a.id AS value')
                ->select('a.title AS text')
                ->select('COUNT(DISTINCT b.id) AS level')
                ->from('#__usergroups as a')
                ->join('LEFT', '#__usergroups  AS b ON a.lft > b.lft AND a.rgt < b.rgt')
                ->group('a.id, a.title, a.lft, a.rgt')
                ->order('a.lft ASC');
        $db->setQuery($query);

        if ($options = $db->loadObjectList()) {
            foreach ($options as &$option) {
                $option->text = str_repeat('- ', $option->level) . $option->text;
            }

            unset($option);
        }

        $fieldname = "ug[$id][]";
        $fieldid = "ug$id";
        $fieldlabel = $label ? $label : JText::_("JEVCF_APPLICABLE_USERGROUPS");
        $fieldattribute = "usergroups";
        $fieldtooltip = $tooltip ? $tooltip : JText::_("JEVCF_APPLICABLE_USERGROUPS_DESC");
        $attribs = "";

        $value = $field ? $field->attribute($fieldattribute) : "";
        if (is_string($value)) {
            $value = explode(",", $value);
        }
        ?>
        <div class="jevcflabel">
            <label for="<?php echo $fieldid; ?>">
        <?php echo $fieldlabel; ?>
        <?php CustomFieldsHelper::fieldTooltip($fieldtooltip); ?>
            </label>
        </div>
        <div class="jevcfinputs">
        <?php
        // build the html select list
        echo JHtml::_('select.genericlist', $options, $fieldname, 'id="' . $fieldid . '" class="inputbox" size="5" multiple="multiple"', 'value', 'text', $value);
        ?>
        </div>
        <div class="jevcfclear"></div>
        <?php
    }

    static function userids($id, $field, $label = false, $tooltip = false) {
        $options = array();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('a.id AS value')
                ->select('a.title AS text')
                ->select('COUNT(DISTINCT b.id) AS level')
                ->from('#__usergroups as a')
                ->join('LEFT', '#__usergroups  AS b ON a.lft > b.lft AND a.rgt < b.rgt')
                ->group('a.id, a.title, a.lft, a.rgt')
                ->order('a.lft ASC');
        $db->setQuery($query);

        if ($options = $db->loadObjectList()) {
            foreach ($options as &$option) {
                $option->text = str_repeat('- ', $option->level) . $option->text;
            }

            unset($option);
        }

        $fieldname = "us[$id][]";
        $fieldid = "us$id";
        $fieldlabel = $label ? $label : JText::_("JEVCF_APPLICABLE_USERGROUPS");
        $fieldattribute = "usergroups";
        $fieldtooltip = $tooltip ? $tooltip : JText::_("JEVCF_APPLICABLE_USERGROUPS_DESC");
        $attribs = "";

        $value = $field ? $field->attribute($fieldattribute) : "";
        if (is_string($value)) {
            $value = explode(",", $value);
        }
        ?>
        <div class="jevcflabel">
            <label for="<?php echo $fieldid; ?>">
        <?php echo $fieldlabel; ?>
        <?php CustomFieldsHelper::fieldTooltip($fieldtooltip); ?>
            </label>
        </div>
        <div class="jevcfinputs">
        <?php
        // build the html select list
        echo JHtml::_('select.genericlist', $options, $fieldname, 'id="' . $fieldid . '" class="inputbox" size="5" multiple="multiple"', 'value', 'text', $value);
        ?>
        </div>
        <div class="jevcfclear"></div>
        <?php
    }

    static function editorbuttons($id, $field, $label = false, $tooltip = false) {
        $options = array();

        $dispatcher = JEventDispatcher::getInstance();
        $plugins = JPluginHelper::getPlugin('editors-xtd');

        foreach ($plugins as $plugin) {
            $option = new stdClass();
            $option->text = ucwords($plugin->name);
            $option->value = $plugin->name;
            $options[] = $option;
        }

        $fieldname = "buttons[$id][]";
        $fieldid = "buttons$id";
        $fieldlabel = $label ? $label : JText::_("JEVCF_ENABLE_BUTTONS");
        $fieldattribute = "buttons";
        $fieldtooltip = $tooltip ? $tooltip : JText::_("JEVCF_ENABLE_BUTTONS_DESC");
        $attribs = "";

        $value = $field ? $field->attribute($fieldattribute) : "";
        if (is_string($value)) {
            $value = explode(",", $value);
        }
        ?>
        <div class="jevcflabel">
            <label for="<?php echo $fieldid; ?>">
                <?php echo $fieldlabel; ?>
                <?php CustomFieldsHelper::fieldTooltip($fieldtooltip); ?>
            </label>
        </div>
        <div class="jevcfinputs">
            <?php
            // build the html select list
            echo JHtml::_('select.genericlist', $options, $fieldname, 'id="' . $fieldid . '" class="inputbox" size="5" multiple="multiple"', 'value', 'text', $value);
            ?>
        </div>
        <div class="jevcfclear"></div>
        <?php
    }

    static function requiredMessage($id, $field) {
        ?>
        <div class="jevcfnotranslate requiredmessage rm<?php echo $id; ?>" style="display:none;">
            <div class="jevcflabel">
                <label for="rm<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_REQUIRED_MESSAGE"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_REQUIRED_MESSAGE_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input  type="text" name="rm[<?php echo $id; ?>]" id="rm<?php echo $id; ?>" value="<?php echo $field ? $field->attribute('requiredmessage') : ''; ?>" size="40" maxlength="255" />
            </div>
        </div>
        <div class="jevcfclear"></div>
        <?php
        if ($field) {
            ?>
            <script type="text/javascript">
                jevcfforms.revealConditionalDisplayField('rr<?php echo $id; ?>', 'rm<?php echo $id; ?>');
            </script>
            <?php
        }
    }

    static function allowoverride($id, $field) {
        $fieldname = "ao[$id]";
        $fieldid0 = "ao0$id";
        $fieldid1 = "ao1$id";
        $fieldlabel = JText::_("JEVCF_ALLOW_OVERRIDE");
        $fieldattribute = "allowoverride";
        $fieldtooltip = JText::_("JEVCF_ALLOW_OVERRIDE_DESC");
        CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip);
    }

    static function tooltip($id, $field) {
        ?>
        <div class="jevcflabel">
            <label for="ft<?php echo $id; ?>">
            <?php echo JText::_("JEVCF_FIELD_TOOLTIP"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FIELD_TOOLTIP_DESC')); ?>
            </label>
        </div>
        <div class="jevcfinputs">
            <input type="text" name="ft[<?php echo $id; ?>]" id="ft<?php echo $id; ?>" value="<?php echo $field ? htmlspecialchars($field->attribute('description')) : ''; ?>" size="40" maxlength="255" />
        </div>
        <div class="jevcfclear"></div>
        <?php
    }

    static function size($id, $field, $fieldtype) {
        ?>
        <div class="jevcfnotranslate">
            <div class="jevcflabel">
                <label for="size<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_FIELD_SIZE"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FIELD_SIZE_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input type="text" name="size[<?php echo $id; ?>]" id="size<?php echo $id; ?>" value="<?php echo $field && $field->attribute('size') > 0 ? $field->attribute('size') : 10; ?>" size="5" maxlength="5"
                       onchange="<?php echo $fieldtype; ?>.changeSize('<?php echo $id; ?>')"    onkeyup="<?php echo $fieldtype; ?>.changeSize('<?php echo $id; ?>')" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
    }

    static function hiddenvalue($id, $field) {
        ?>
        <div class="jevcfnotranslate">
            <div class="jevcflabel">
                <label for="hiddenvalue<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_HIDDEN_VALUE"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_HIDDEN_VALUE_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input type="text" name="hiddenvalue[<?php echo $id; ?>]" id="hiddenvalue<?php echo $id; ?>" value="<?php echo $field ? $field->attribute('hiddenvalue') : ""; ?>" size="30" maxlength="50" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
    }

    static function inputConditional($id, $field) {
	$plugin = JPluginHelper::getPlugin('jevents', 'jevcustomfields' );
	$pluginparams = new JRegistry($plugin->params);
        if (!$pluginparams->get("inputconditionals", 0)){ 
            return;
        }
        ?>
        <div class="jevcfnotranslate">
            <div class="jevcflabel">
                <label for="icvar<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_INPUT_CONDITIONAL_VARIABLE"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_INPUT_CONDITIONAL_VARIABLE_TIP')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input type="text" name="icvar[<?php echo $id; ?>]" id="icvar<?php echo $id; ?>" value="<?php echo $field ? $field->attribute('icvar') : ""; ?>" size="30" maxlength="50" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <div class="jevcfnotranslate">
            <div class="jevcflabel">
                <label for="icvar<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_INPUT_CONDITIONAL_VALUE"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_INPUT_CONDITIONAL_VALUE_TIP')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input type="text" name="icval[<?php echo $id; ?>]" id="icval<?php echo $id; ?>" value="<?php echo $field ? $field->attribute('icval') : ""; ?>" size="30" maxlength="50" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
    }
    
    static function fieldtype($id, $field, $fieldtype) {
        ?>
        <div class="jevcflabel">
                <?php echo JText::_("JEVCF_FIELD_TYPE"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FIELD_TYPE_DESC')); ?>
        </div>
        <div class="jevcfinputs" style="font-weight:bold;"><?php echo JText::_("CUSTOM_FIELD_TYPE_" . strtoupper($fieldtype)); ?><?php CustomFieldsHelper::fieldId($id); ?></div>
        <div class="jevcfclear"></div>
        <?php
    }

    static function maxlength($id, $field, $fieldtype) {
        ?>
        <div class="jevcfnotranslate">
            <div class="jevcflabel">
                <label for="maxlength<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_FIELD_MAXLENGTH"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FIELD_MAXLENGTH_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input type="text" name="maxlength[<?php echo $id; ?>]" id="maxlength<?php echo $id; ?>" value="<?php echo $field && $field->attribute('maxlength') > 0 ? $field->attribute('maxlength') : 20; ?>" size="5" maxlength="5"
                       onchange="<?php echo $fieldtype; ?>.changeMaxlength('<?php echo $id; ?>')" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
    }

    static function cols($id, $field, $fieldtype) {
        ?>
        <div class="jevcfnotranslate">
            <div class="jevcflabel">
                <label for="cols<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_FIELD_COLS"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FIELD_COLS_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input type="text" name="cols[<?php echo $id; ?>]" id="cols<?php echo $id; ?>" value="<?php echo $field && $field->attribute('cols') > 0 ? $field->attribute('cols') : 20; ?>" size="5" maxlength="5"
                       onchange="<?php echo $fieldtype; ?>.changeCols('<?php echo $id; ?>')" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
    }

    static function rows($id, $field, $fieldtype) {
        ?>
        <div class="jevcfnotranslate">
            <div class="jevcflabel">
                <label for="rows<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_FIELD_ROWS"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FIELD_ROWS_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input type="text" name="rows[<?php echo $id; ?>]" id="rows<?php echo $id; ?>" value="<?php echo $field && $field->attribute('rows') > 0 ? $field->attribute('rows') : 5; ?>" size="5" maxlength="5"
                       onchange="<?php echo $fieldtype; ?>.changeRows('<?php echo $id; ?>')" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
    }

    static function height($id, $field, $fieldtype) {
        ?>
        <div class="jevcfnotranslate">
            <div class="jevcflabel">
                <label for="rows<?php echo $id; ?>">
                    <?php echo JText::_("JEVCF_FIELD_HEIGHT"); ?>
                    <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FIELD_HEIGHT_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input type="text" name="height[<?php echo $id; ?>]" id="height<?php echo $id; ?>" value="<?php echo $field && $field->attribute('height') > 0 ? $field->attribute('height') : 5; ?>" size="5" maxlength="5" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
    }

    static function width($id, $field, $fieldtype) {
        ?>
        <div class="jevcfnotranslate">
            <div class="jevcflabel">
                <label for="rows<?php echo $id; ?>">
        <?php echo JText::_("JEVCF_FIELD_WIDTH"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FIELD_WIDTH_DESC')); ?>
                </label>
            </div>
            <div class="jevcfinputs">
                <input type="text" name="width[<?php echo $id; ?>]" id="width<?php echo $id; ?>" value="<?php echo $field && $field->attribute('width') > 0 ? $field->attribute('width') : 5; ?>" size="5" maxlength="5" />
            </div>
            <div class="jevcfclear"></div>
        </div>
        <?php
    }

    static function label($id, $field, $fieldtype = "") {
        ?>
        <div class="jevcflabel">
            <label for="fl<?php echo $id; ?>">
        <?php
        echo JText::_("JEVCF_FIELD_LABEL");
        CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FIELD_LABEL_DESC'));
        $value = $field ? $field->attribute('label') : JText::_("JEVCF_FIELD_LABEL");
        ?>
            </label>
        </div>
        <div class="jevcfinputs">
            <input type="text" name="fl[<?php echo $id; ?>]" id="fl<?php echo $id; ?>" 
                   value="<?php echo $value; ?>"
                   size="40" maxlength="255" class="jevcffl jevcffl_label"
                   onkeyup="jevcfpro.updateLabel('<?php echo $id; ?>');"  
                   onblur="jevcfpro.setName('<?php echo $id; ?>');"
                   rel="<?php echo $fieldtype; ?>"/>
        </div>
        <div class="jevcfclear"></div>
        <?php
    }

    static function name($id, $field, $fieldtype = "") {
        ?>
        <div class="jevcflabel">
            <label for="fn<?php echo $id; ?>">
        <?php echo JText::_("CUSTOM_FIELD_NAME"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('CUSTOM_FIELD_NAME_DESC')); ?>
            </label>
        </div>
        <div class="jevcfinputs">
            <input type="text" name="fn[<?php echo $id; ?>]" id="fn<?php echo $id; ?>" 
                    value="<?php echo $field ? $field->attribute('name') : ''; ?>"
                    placeholder ="<?php echo JText::_("CUSTOM_FIELD_NAME");?>"                                                      
                   size="40" maxlength="255" class="jevcffl jevcffl_name"
                   onkeyup="jevcfpro.cleanName('<?php echo $id; ?>');"
                   onblur="jevcfpro.cleanName('<?php echo $id; ?>');"
                   rel="<?php echo $fieldtype; ?>"/>
        </div>
        <div class="jevcfclear"></div>
        <?php
    }

    static function fieldId($id) {
        echo " {" . $id . "}";
        return "";
    }

    static function hidden($id, $field, $name) {
        ?>
        <input type="hidden" name="type[<?php echo $id; ?>]" id="type<?php echo $id; ?>" value="<?php echo $name; ?>" />
        <input type="hidden" name="fid[<?php echo $id; ?>]" id="fid<?php echo $id; ?>" value="<?php echo $field ? $id : 0; ?>" />
        <input type="hidden" name="ordering[<?php echo $id; ?>]" id="ordering<?php echo $id; ?>" value="<?php echo $field ? $field->attribute('ordering') : 0; ?>" />
        <input type="hidden" name="defaultvalue[<?php echo $id; ?>]" id="defaultvalue<?php echo $id; ?>" value="<?php echo ($field && is_string($field->attribute('defaultvalue'))) ? $field->attribute('defaultvalue') : 0 ?>" />
        <?php
    }

    static function universal($id, $field) {

        static $hasRsvpPro;
        if (!isset($hasRsvpPro)) {
            // RSVP Pro
            $db = JFactory::getDbo();
            $db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_rsvppro' AND type='component' ");
            $hasRsvpPro = $db->loadResult();
            if (is_null($hasRsvpPro)) $hasRsvpPro = false;
        }        
        
        if ($hasRsvpPro) {
            $fieldname = "attendeesonly[$id]";
            $fieldid0 = "attendeesonly0$id";
            $fieldid1 = "attendeesonly1$id";
            $fieldlabel = JText::_("JEVCF_attendeesonly");
            $fieldattribute = "attendeesonly";
            $fieldtooltip = JText::_("JEVCF_attendeesonly_DESC");
            $attribs = "";
            CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip, $attribs);
        }
        
        $dispatcher = JEventDispatcher::getInstance();
        $plugin = JPluginHelper::getPlugin('jevents',  "jevmatchingevents");
        if ($plugin) {
            $fieldname = "matchevents[$id]";
            $fieldid0 = "matchevents0$id";
            $fieldid1 = "matchevents1$id";
            $fieldlabel = JText::_("JEVCF_matchevents");
            $fieldattribute = "matchevents";
            $fieldtooltip = JText::_("JEVCF_matchevents_DESC");
            $attribs = "";
            CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip, $attribs);            
        }
    }

    private static $fieldscript;

    static function setField($id, $field, $html, $name) {
        if (!$field) {
            static $script;
            if (!isset(self::$fieldscript)) {
                self::$fieldscript = "";
            }
            self::$fieldscript .= "jevcfFieldTypes['" . strtolower($name) . "']=" . json_encode($html) . ";\n";
            return "";
        } else {
            return $html;
        }
    }

    static function getFieldScript() {
        $document = JFactory::getDocument();
        $document->addScriptDeclaration(self::$fieldscript);
    }

    static function applicableCategories($id, $field) {
        $fieldids = "facc[$id]";
        $fieldidc = "facs$id";
        $categories = $field ? $field->attribute('categoryrestrictions') : "all";
        ?>
        <div class="jevcfnotranslate" id="facc<?php echo $id; ?>">
            <div class="jevcflabel">
                <label>
        <?php echo JText::_("JEVCF_APPLICABLE_CATEGORIES"); ?>
        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_APPLICABLE_CATEGORIES_DESC')); ?>
                </label>
            </div>
        <?php
        static $donescript;
        if (!isset($donescript)) {
            $doc = JFactory::getDocument();
            $script = <<<SCRIPT
		   		function allselections(id) {
		   			var e = document.getElementById(id);
		   			e.disabled = true;
		   			var i = 0;
		   			var n = e.options.length;
		   			for (i = 0; i < n; i++) {
		   				e.options[i].disabled = true;
		   				e.options[i].selected = true;
		   			}
					jQuery("#"+id).parent().parent().find(".jevcfcategoryselection").css("display","none");
					jQuery('#'+id).trigger("chosen:updated");
					// old style version - still needed!
					jQuery('#'+id).trigger("liszt:updated");

		   		}
		   		function enableselections(id) {
		   			var e = document.getElementById(id);
		   			e.disabled = false;
		   			var i = 0;
		   			var n = e.options.length;
		   			for (i = 0; i < n; i++) {
		   				e.options[i].disabled = false;
		   			}
					jQuery("#"+id).parent().parent().find(".jevcfcategoryselection").css("display","block");
					jQuery('#'+id).trigger("chosen:updated");
					// old style version - still needed!
					jQuery('#'+id).trigger("liszt:updated");
		   		}
SCRIPT;
            $doc->addScriptDeclaration($script);
            $donescript = 1;
        }

        $fieldidcStripped = str_replace(array("[", "]"), "", $fieldidc);

        JLoader::register('JEventsCategory', JEV_ADMINPATH . "/libraries/categoryClass.php");

        $cattree = JEventsCategory::categoriesTree();
        $categorylist = JHtml::_('select.genericlist', $cattree, $fieldidc . '[]', 'multiple="multiple" size="15"', 'value', 'text', explode(",", $categories));
        $attribs = " jevcfforms.revealConditionalDisplayField('facc" . $id . "', 'hfboc" . $id . "', 'select' )";
        ?>
            <div class="jevcfinputs  radio btn-group" >
        <?php
        if ($categories == 'all' || $categories == '') {
            ?>
                    <label for="categories-all<?php echo $fieldidc; ?>" class="btn radio">
                        <input id="categories-all<?php echo $fieldidc; ?>" type="radio"  name="<?php echo $fieldids; ?>" value="all" onclick="allselections('<?php echo $fieldidcStripped; ?>');<?php echo $attribs; ?>" checked="checked" />
            <?php echo JText::_('JEVCF_ALL'); ?>
                    </label>
                    <label for="categories-select<?php echo $fieldidc; ?>" class="btn radio">
                        <input id="categories-select<?php echo $fieldidc; ?>" type="radio"  name="<?php echo $fieldids; ?>" value="select" onclick="enableselections('<?php echo $fieldidcStripped; ?>');<?php echo $attribs; ?>" />
            <?php echo JText::_('JEVCF_SELECT_FROM_LIST'); ?>
                    </label>
            <?php
        } else {
            ?>
                    <label for="categories-all<?php echo $fieldidc; ?>" class="btn radio">
                        <input id="categories-all<?php echo $fieldidc; ?>" type="radio"  name="<?php echo $fieldids; ?>" value="all" onclick="allselections('<?php echo $fieldidcStripped; ?>');" />
            <?php echo JText::_('JEVCF_ALL'); ?>
                    </label>
                    <label for="categories-select<?php echo $fieldidc; ?>" class="btn radio">
                        <input id="categories-select<?php echo $fieldidc; ?>" type="radio"  name="<?php echo $fieldids; ?>" value="select" onclick="enableselections('<?php echo $fieldidcStripped; ?>');" checked="checked" />
            <?php echo JText::_('JEVCF_SELECT_FROM_LIST'); ?>
                    </label>
        <?php } ?>
            </div>
            <div class="jevcfcategoryselection jevcfclear">
        <?php echo $categorylist; ?>

            </div>
        </div>
        <div class="jevcfclear"></div>
            <?php
                if ($categories == 'all' || $categories == '') {
            ?>
                <script type="text/javascript">setTimeout(allselections,500,'<?php echo $fieldidcStripped; ?>');</script>
            <?php } ?>
            <?php
            // hidefilterbasedoncategory=
            $fieldname = "hfboc[$id]";
            $fieldid0 = "hfboc0$id";
            $fieldid1 = "hfboc1$id";
            $fieldlabel = JText::_("JEVCF_HIDE_FILTER_BASED_ON_CATEGORY");
            $fieldattribute = "hidefilterbasedoncategory";
            $fieldtooltip = JText::_("JEVCF_HIDE_FILTER_BASED_ON_CATEGORY_DESC");
            CustomFieldsHelper::booleanField($field, $fieldname, $fieldid0, $fieldid1, $fieldlabel, $fieldattribute, $fieldtooltip, "", "display:none;");
            echo "<script type='text/javascript'> jevcfforms.revealConditionalDisplayField('facc" . $id . "', 'hfboc" . $id . "', 'select' )</script>";
            ?>
        <div class="jevcfclear"></div>
            <?php
        }

        public static function translate($string) {
            // Is there a translation - simple translation
            $translation = JText::_($string);
            if ($translation != $string) {
                return $translation;
            }
            // more name spaced!
            $totranslate = str_replace(" ", "_", trim($string));
            $totranslate = "JEVCF_FIELD_" . (string) preg_replace('/[^A-Z0-9_]/i', '', $totranslate);
            $translation = JText::_($totranslate);
            if ($translation != $totranslate) {
                return $translation;
            }
            return $string;
        }

        static function conditional($id, $field) {
            $cf = $field ? $field->attribute('cf') : '';
            $cfvfv = $field ? $field->attribute('cfvfv') : 1;

            $style = 'style="display:none"';
            ?>
        <div class="jevcfnotranslate jevcfconditional" <?php echo $style; ?>>
            <div class="jevcflabel"   ><?php echo JText::_("JEVCF_CONDITIONAL"); ?>
            <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_CONDITIONAL_DESC')); ?>
            </div>
            <div class="jevcfinputs"   >
                <div class="jevcflabel" ><label for="cf<?php echo $id; ?>"><?php echo JText::_("JEVCF_CONDITION_FIELD"); ?></label></div>
                <div class="jevcfinputs">
                    <select name="params[<?php echo $id; ?>][cf]" id="cf<?php echo $id; ?>" class="cf" onchange="conditionalEditorPlugin.updateSelection('cf<?php echo $id; ?>');"	>
                        <option value=""><?php echo JText::_("JEVCF_NOT_CONDITIONAL"); ?></option>
                    </select>
                </div>
                <div style="clear:left;" ></div>
                <div class="jevcfconditionalselector"  <?php echo $style; ?>>
                    <div class="jevcflabel" ><label for="cfvfv<?php echo $id; ?>"><?php echo JText::_("JEVCF_CONDITION_VISIBLE_FIELDVALUE"); ?></label></div>
                    <div class="jevcfinputs">
                        <input type="hidden" id="hiddencfvfv<?php echo $id; ?>" value="<?php echo $cfvfv;?>" />
                        <select name="params[<?php echo $id; ?>][cfvfv]" id="cfvfv<?php echo $id; ?>" class="cfvfv" >
                            <option value="1" <?php echo $cfvfv ? "selected='selected'" : ""; ?>><?php echo JText::_("JEVCF_YES"); ?></option>
                            <option value="0" <?php echo!$cfvfv ? "selected='selected'" : ""; ?>><?php echo JText::_("JEVCF_NO"); ?></option>
                        </select>
                    </div>
                </div>

            </div>
                <?php
                static $scripts = array();
                $script = 'jQuery(document).ready(function(){if (conditionalEditorPlugin) conditionalEditorPlugin.update("' . $id . '", "' . $cf . '");});';
                if (!in_array($script, $scripts)) {
                    $doc = JFactory::getDocument();
                    $doc->addScriptDeclaration($script);
                    $scripts[] = $script;
                }
                ?>
            <div class="jevcfclear"></div>
        </div>
        <?php
    }

    /**
     * Converts a double colon seperated string or 2 separate strings to a string ready for bootstrap tooltips
     *
     * @param   string  $title      The title of the tooltip (or combined '::' separated string).
     * @param   string  $content    The content to tooltip.
     * @param   int     $translate  If true will pass texts through JText.
     * @param   int     $escape     If true will pass texts through htmlspecialchars.
     *
     * @return  string  The tooltip string
     *
     * @since   3.1.17
     */
    public static function tooltipText($title = '', $content = '', $translate = 1, $escape = 1) {
        // Return empty in no title or content is given.
        if ($title == '' && $content == '') {
            return '';
        }

        // Split title into title and content if the title contains '::' (old Mootools format).
        if ($content == '' && !(strpos($title, '::') === false)) {
            list($title, $content) = explode('::', $title, 2);
        }

        // Pass texts through the JText.
        if ($translate) {
            $title = JText::_($title);
            $content = JText::_($content);
        }

        // Escape the texts.
        if ($escape) {
            $title = str_replace('"', '&quot;', $title);
            $content = str_replace('"', '&quot;', $content);
        }

        // Return only the content if no title is given.
        if ($title == '') {
            return $content;
        }

        if (version_compare(JVERSION, "3.2.0", 'ge')) {

            // Return only the title if title and text are the same.
            if ($title == $content) {
                return '<strong>' . $title . '</strong>';
            }

            // Return the formated sting combining the title and  content.
            if ($content != '') {
                return '<strong>' . $title . '</strong><br />' . $content;
            }
        } else {
            return $title . "::" . $content;
        }


        // Return only the title.
        return $title;
    }

}
