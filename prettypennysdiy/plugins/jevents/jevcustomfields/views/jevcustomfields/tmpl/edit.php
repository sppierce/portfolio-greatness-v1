<?php
// No direct access
defined('_JEXEC') or die;

jimport("joomla.filesystem.file");
JHtml::_('bootstrap.framework');
JHtml::_('bootstrap.loadCss');
JHtml::_('formbehavior.chosen', 'select:not(.notchosen)');

$editor = JFactory::getEditor('none');

$input = JFactory::getApplication()->input;

if (!empty($this->sidebar)) {
    $version = JEventsVersion::getInstance();
    ?>
    <div id="j-sidebar-container" class="span2">

        <?php echo $this->sidebar; ?>

        <?php
        //Version Checking etc
        ?>
        <div class="jev_version">
            <?php echo JText::sprintf('JEV_CURRENT_VERSION', JString::substr($version->getShortVersion(), 1)); ?>
        </div>
    </div>
    <?php
}

$mainspan = 10;
$fullspan = 12;
?>
<div id="jevents" class="span12">
    <form action="index.php" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
            <div class="row-fluid">
                <input type="hidden" name="option" value="com_jevents"/>
                <input type="hidden" name="task" value="plugin.jev_customfields.edit"/>
                <?php echo JHtml::_('form.token'); ?>

                <div class="form-horizontal ">
                    <div class="control-group">
                        <label class="control-label"><?php echo JText::_('JEV_CUSTOMFIELDS_FILENAME'); ?> </label>
                        <div class="controls" >
                            <input class="inputbox" type="text" name="cfname"  size="50" maxlength="100" value="<?php echo $this->file; ?>"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label"><?php echo JText::_('JEV_CUSTOMFIELDS_DESCRIPTION'); ?></label>

                        <div class="controls" >
                            <?php
                            // parameters : areaname, content, hidden field, width, height, rows, cols
                            echo $editor->display('description', htmlspecialchars(trim($this->xml->description), ENT_QUOTES, 'UTF-8'), "80%", 100, '70', '10', false);
                            ?>
                        </div>
                    </div>
                    <div class="addfield span6">                       
                        <h3><?php echo JText::_('JEVCF_Add_field'); ?></h3>
                        <div id="jevtemplate_fields">
                            <?php
                            jimport("joomla.filesystem.folder");
                            $fieldfiles = JFolder::files(JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/", ".php");

                            $options = array();
                            $value = false;
                            foreach ($fieldfiles as $fieldfile) {
                                $type = str_replace(".php", "", $fieldfile);
                                if (strpos($type, "jevcf") !== 0 || strpos($type, ".zip") !== false || strpos($type, ".gz") !== false || strpos($type, "jevcfparameter") !== false) {
                                    continue;
                                }
                                if (!$value)
                                    $value = $type;
                                try {
                                    include_once( JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/" . $fieldfile);

                                    if (method_exists("JFormField" . ucfirst($type), "isEnabled")) {
                                        if (!call_user_func(array("JFormField" . ucfirst($type), "isEnabled"))) {
                                            continue;
                                        }
                                    }

                                    $label = JText::_('CUSTOM_FIELD_TYPE_' . $type);
                                    if ($label == 'CUSTOM_FIELD_TYPE_' . $type && method_exists("JFormField" . ucfirst($type), "fieldName")) {
                                        $label = call_user_func(array("JFormField" . ucfirst($type), "fieldName"));
                                    }
                                    if (method_exists("JFormField" . ucfirst($type), "loadScript")) {
                                        call_user_func(array("JFormField" . ucfirst($type), "loadScript"));
                                        $options[] = JHtml::_('select.option', $type, $label);
                                    }
                                }
                                catch (Exception $e){
                                    echo $e->getMessage()."<br/>";
                                    continue;
                                }
                            }
                            CustomFieldsHelper::getFieldScript();
                            ?>
<div class="dropdown btn-group" id="fieldsdropdown">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdowncustomfields" data-toggle="dropdown" aria-expanded="false">
    <?php echo JText::_("JEVCF_SELECT_NEW_FIELD_TYPE");?>
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdowncustomfields" id="dropdownUL_customfields" role="menu">
      <?php
      foreach ($options as $option){
          ?>
      <li role="presentation"><a role="menuitem" class="dropdownpopover" href="#" data-title="<?php echo addslashes($option->text);?>" data-content="<?php echo JText::_("JEVCF_".strtoupper($option->value)."_DESCRIPTION", true);?>" data-value="<?php echo $option->value;?>" ><?php echo $option->text;?></a></li>
          <?php
      }
      ?>
    </ul>
</div>
<?php
                            //echo JHtml::_('select.genericlist', $options, 'templatetype', 'class="jevcftemplatetype" size="1" ', 'value', 'text', $value, "templatetype");
                            ?>

                        </div>
                        <!-- <input id="newFieldButton" type="button" value="<?php echo JText::_("JEVCF_CREATE_FIELD"); ?>"/> //-->
                        <input id="deleteFieldButton" type="button" value="<?php echo JText::_("JEVCF_DELETE_FIELD"); ?>" style='display:none' class="deleteFieldButton"/>
                        <input id="closeFieldButton" type="button" value="<?php echo JText::_("JEVCF_CLOSE_FIELD"); ?>" style='display:none' class="closeFieldButton"/>
                    </div>
                    <div class="addfieldgroup span6">
                        <h3><?php echo JText::_('JEVCF_Add_field_GROUP'); ?></h3>
                        <input id="newFieldGroupButton" type="button" value="<?php echo JText::_("JEVCF_CREATE_FIELD_GROUP"); ?>"/>
                    </div>
                    <div id="jevcffields" class="jevcffieldscontainer" >
                        <?php
                        $cfparams = JevCfForm::getInstance("com_jevent.customfields", $this->xml->asXML(), array('control' => 'jform', 'load_data' => true), true, "/form");
                        $cfparams->setEvent(null);
                        $fieldsets = $cfparams->getFieldsets();
                        // Put the default fieldset first
                        $groups = array();
                        if (isset($fieldsets["default"])) {
                            $groups["default"] = $fieldsets["default"];
                        }
                        foreach ($fieldsets as $group => $element) {
                            if ($group != "default") {
                                $groups[$group] = $element;
                            }
                        }

                        foreach ($groups as $group => $element) {
                            $description = (isset($element->description) ? $element->description : "");
                            $descriptionElement= $this->xml->xpath('//fieldset[@name="' . (string) $group . '"]/description');
                            if (count($descriptionElement)>0){
                                $description = trim( (string) $descriptionElement[0]) ;
                            }
                            ?>
                            <fieldset class="jevcffieldset <?php echo ($group == "default")?"jevcfdefaultfieldset":""?>" >
                            <?php
                            if ($group == "default") {
                                ?>
                                    <legend class="fieldsetlabel">
                                        <!-- Default HAS TO COME FIRST!!
                                        <span class="sortable-handler" >
                                            <i class="icon-menu"></i>
                                        </span>                         
                                        //-->
                                <?php
                                echo JText::sprintf("JEVCF_FIELD_GROUP", ucwords($group));
                                echo "<input type='text' name='jevcffieldset[]' size='40' value='" . htmlspecialchars($group) . "' />";
                                echo "<div class='fieldsetdescription' ><textarea name='jevcffieldsetdescription[]' cols='60' rows='3'></textarea></div>";
                                ?>
                                    </legend>
                                    <?php
                                } else {
                                    ?>
                                    <legend class="fieldsetlabel">
                                        <span class="sortable-handler" >
                                            <i class="icon-menu"></i>
                                        </span>                                        
                                        <?php
                                        echo JText::sprintf("JEVCF_FIELD_GROUP", "<span class='fieldsetname' >" . ucwords($group) . "</span>");
                                        echo JHtml::image('com_jevents/icons-32/edit.png', JText::_("JEV_E_EDIT"), array("class"=>"editimg"), true);
                                        echo JHtml::image('com_jevents/icons-32/discard.png', JText::_("JEV_E_DELETE"), array("class"=>"deleteimg"), true);
                                        echo "<input type='text' name='jevcffieldset[]' size='40' value='" . ucwords(htmlspecialchars($group)) . "' />";
                                        echo "<div class='fieldsetdescription' >" . JText::sprintf("JEVCF_FIELD_GROUP_DESCRIPTION", "<textarea name='jevcffieldsetdescription[]' cols='60' rows='3'>" . $description . "</textarea>") . "</div>";
                                        ?>
                                    </legend>
                                    <?php
                                }
                                $count = $cfparams->getFieldCountByFieldSet($group);
                                $groupparams = $cfparams->getFieldset($group);
                                foreach ($groupparams as $p => $field) {
                                    if ($field->fieldname == "Field Name" || $field->fieldname == JText::_("CUSTOM_FIELD_NAME")){
                                        $field->fieldname =  uniqid('cf_', false);
                                        $field->addAttribute("name", $field->fieldname) ;
                                    }
                                    $fieldhtml = "<div class='jevcfclear'></div>";
                                    if (method_exists($field, "loadScript")) {
                                        $fieldhtml = $field->html = $field->loadScript($field);
                                    }
                                    ?>
                                    <div class='jevcffield' id='field<?php echo $field->fieldname; ?>'>
                                        <span class="sortable-handler" >
                                            <i class="icon-menu"></i>
                                        </span>
                                        <input id="deleteFieldButtonfield<?php echo $field->fieldname; ?>" type="button" value="<?php echo JText::_("JEVCF_DELETE_FIELD"); ?>" class="deleteFieldButton"/>
                                        <input id="closeFieldButtonfield<?php echo $field->fieldname; ?>" type="button" value="<?php echo JText::_("JEVCF_CLOSE_FIELD"); ?>" class="closeFieldButton"/>
                                    <?php
                                    echo $fieldhtml;
                                    ?>
                                        <input class="fieldsetmap" type="text" name="fieldsetmap[field<?php echo $field->fieldname; ?>]" value="<?php echo ucwords(htmlspecialchars($group)); ?>">
                                    </div>
                                    <?php
                                }
                                ?>
                            <div class='jevcffield jevcflastfield'></div>
                            </fieldset>
    <?php
}
                        ?>
                        <fieldset class="jevcffieldset jevcflastfieldset" >
                            <legend class="fieldsetlabel">
                                <span class="sortable-handler" >
                                    <i class="icon-menu"></i>
                                </span>

                                <?php
                                echo JText::sprintf("JEVCF_FIELD_GROUP", "<span class='fieldsetname' ></span>");
                                echo JHtml::image('com_jevents/icons-32/edit.png', JText::_("JEV_E_EDIT"), array("class"=>"editimg"), true);
                                echo JHtml::image('com_jevents/icons-32/discard.png', JText::_("JEV_E_DELETE"), array("class"=>"deleteimg"), true);
                                echo "<input type='text' name='jevcffieldset[]' size='40' value='' />";
                                echo "<div class='fieldsetdescription' ><textarea name='jevcffieldsetdescription[]' cols='60' rows='3'></textarea></div>";
                                ?>
                            </legend>
                            <div class='jevcffield jevcflastfield'></div>
                        </fieldset>
                    </div>
                </div>
            </div>
    </form>
</div>
                            <?php
// make sure the form isn't too big!'
                            $max_input_vars = (int) @ini_get("max_input_vars");
                            if ($max_input_vars == 0) {
                                $max_input_vars = 999999;
                            }
                            ?>
<script type="text/javascript">
    var inputvars = $('adminForm').getElements('input');
    var selectvars = $('adminForm').getElements('select');
    var textareavars = $('adminForm').getElements('textarea');
    if (inputvars.length + selectvars.length + textareavars.length > <?php echo $max_input_vars; ?>) {
        alert("<?php echo JText::_("JEVCF_FORM_LARGER_THAN_MAXIMUM_SIZE_CHECK_HTACCESS_SETTINGS", true) ?>\n" + (inputvars.length + selectvars.length + textareavars.length) + "  vs " + <?php echo $max_input_vars; ?>);
    } else if (inputvars.length + selectvars.length + textareavars.length > <?php echo $max_input_vars; ?> * 0.90) {
        alert("<?php echo JText::_("JEVCF_FORM_GETTING_CLOSE_TO_MAXIMUM_SIZE_CHECK_HTACCESS_SETTINGS", true) ?>\n" + (inputvars.length + selectvars.length + textareavars.length) + "  vs " + <?php echo $max_input_vars; ?>);
    }
</script>

<script type="text/javascript" >
    window.setTimeout("setupJEVCFTemplateBootstrap()", 500);

    function setupJEVCFTemplateBootstrap() {
        (function ($) {
            // Turn radios into btn-group
            $('.radio.btn-group label').addClass('btn');
            var el = $(".radio.btn-group label:not(.active)");

            // Isis template and others may already have done this so remove these!
            $(".radio.btn-group label:not(.active)").unbind('click');

            $(".radio.btn-group label:not(.active)").click(function () {
                var label = $(this);
                var input = $('#' + label.attr('for'));
                if (!input.prop('checked') && !input.prop('disabled')) {
                    label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
                    if (input.prop('value') != 0) {
                        label.addClass('active btn-success');
                    } else {
                        label.addClass('active btn-danger');
                    }
                    input.prop('checked', true);
                    input.trigger('change');
                }
            });

            // Turn checkboxes into btn-group
            $('.checkbox.btn-group label').addClass('btn');

            // Isis template and others may already have done this so remove these!
            $(".checkbox.btn-group label").unbind('click');

            $(".checkbox.btn-group label").click(function (event) {
                event || (event = window.event);
                var label = $(this);
                var input = $('#' + label.attr('for'));
                //alert(label.val()+ " checked? "+input.prop('checked')+ " disabled? "+input.prop('disabled')+ " label disabled? "+label.hasClass('disabled'));
                if (input.prop('disabled')) {
                    label.removeClass('active btn-success btn-danger btn-primary');
                    input.prop('checked', false);
                    event.stopImmediatePropagation();
                    input.trigger('change');
                    return false;
                }
                if (!input.prop('checked')) {
                    if (input.prop('value') != 0) {
                        label.addClass('active btn-success');
                    } else {
                        label.addClass('active btn-danger');
                    }
                } else {
                    label.removeClass('active btn-success btn-danger btn-primary');
                }
                input.trigger('change');
                // bootstrap takes care of the checkboxes themselves!
            });

            $(".btn-group input[type=checkbox]").each(function () {
                var input = $(this);
                input.css('display', 'none');
            });
        })(jQuery);

        initialiseJEVCFTemplateBootstrapButtons();
    }

    function initialiseJEVCFTemplateBootstrapButtons() {
        (function ($) {
            // this doesn't seem to find just the checked ones!'
            //$(".btn-group input[checked=checked]").each(function() {
            $(".btn-group input").each(function () {
                var label = $("label[for=" + $(this).attr('id') + "]");
                var elem = $(this);
                if (elem.prop('disabled')) {
                    label.addClass('disabled');
                    label.removeClass('active btn-success btn-danger btn-primary');
                    return;
                }
                label.removeClass('disabled');
                if (!elem.prop('checked')) {
                    label.removeClass('active btn-success btn-danger btn-primary');
                    return;
                }
                if (elem.prop('value') != 0) {
                    label.addClass('active btn-success');
                } else {
                    label.addClass('active btn-danger');
                }
            });

        })(jQuery);
    }

</script>
