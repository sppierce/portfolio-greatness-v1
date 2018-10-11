<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

include_once("JevcfField.php");

class JFormFieldJevcfcbfield extends JevcfField {

    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    var $_name = 'Jevcfcbfield';

    const name = 'jevcfcbfield';

    static function isEnabled() {
        if (JFile::exists(JPATH_SITE . '/components/com_comprofiler/comprofiler.php')) {
            if (JComponentHelper::isEnabled("com_comprofiler")) {
                return true;
            }
        }
        return false;
    }

    public static function loadScript($field = false) {
        JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcfcbfield.js');

        if ($field) {
            $id = 'field' . $field->fieldname;
            $selectedField = $field->attribute('fieldname');
        } else {
            $id = '###';
            $selectedField = "";
        }
        ob_start();
        ?>
        <div class='jevcffieldinput'>

        <?php
        CustomFieldsHelper::fieldtype($id, $field, self::name);
        //CustomFieldsHelper::fieldId($id); 
        ?>

        <div class="jevcflabel"><?php echo JText::_("CUSTOM_FIELD_JEVCFCBTEXT_FIELD_SELECTION"); ?></div>
        <div class="jevcfinputs" style="font-weight:bold;">
            <select name="fieldname[<?php echo $id; ?>]" id="fieldname<?php echo $id; ?>"  onchange="jevcfcbfield.setvalue('<?php echo $id; ?>');">
                <?php
                // get the community builder language file - in the vain hope they have moved to Joomla 1.5 system
                $lang = JFactory::getLanguage();
                $lang->load("com_comprofiler", JPATH_SITE);

                $cblanguagePath = JPATH_SITE . '/components/com_comprofiler/plugin/language';
                if (!defined('CBLIB')) {
                    if (!defined('CBLIB'))
                        include_once(JPATH_SITE . '/libraries/CBLib/CB/Application/CBApplication.php');
                }
                $languages = include( $cblanguagePath . "/default_language/language.php" );

                $db = JFactory::getDBO();
                $db->setQuery("SELECT title, name FROM #__comprofiler_fields WHERE ( name != 'NA' ) ");
                $rawrows = $db->loadObjectList();

                // strip out exlucded fields
                $rows = array();
                $exfields = array();
                if (JFile::exists(JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/cbexclusions.txt")) {
                    $exfields = JFile::read(JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/cbexclusions.txt");
                    $exfields = explode("\n", $exfields);
                } else if (JFile::exists(JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/cbexclusions.starter.txt")) {
                    $exfields = JFile::read(JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/cbexclusions.starter.txt");
                    $exfields = explode("\n", $exfields);
                }
                foreach ($rawrows as $row) {
                    if (in_array($row->title, $exfields) || in_array($row->name, $exfields))
                        continue;
                    $rows[] = $row;
                }

                $activeField = "";

                if (!$db->getErrorNum()) {
                    //foreach ($rows as $row) {
                    for ($i = 0; $i < count($rows); $i++) {
                        $row = $rows[$i];
                        if (array_key_exists($row->title, $languages)) {
                            $cbFieldName = $languages[$row->title];
                        } else {
                            $cbFieldName = $row->title;
                        }

                        $selected = "";
                        if ($field && $selectedField == $row->name) {
                            $selected = "selected='selected'";
                            $activeField = $cbFieldName;
                        }
                        ?>

                        <option value="<?php echo $row->name; ?>" <?php echo $selected; ?> ><?php echo$cbFieldName ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="jevcfclear"></div>

        <?php
        CustomFieldsHelper::hidden($id, $field, self::name);
        CustomFieldsHelper::label($id, $field, self::name);
        CustomFieldsHelper::name($id, $field, self::name);
        CustomFieldsHelper::conditional($id, $field);
        CustomFieldsHelper::allowoverride($id, $field);
        CustomFieldsHelper::accessOptions($id, $field);
        CustomFieldsHelper::readaccessOptions($id,  $field);
        CustomFieldsHelper::applicableCategories($id, $field);
        CustomFieldsHelper::fieldclass($id, $field);
        CustomFieldsHelper::universal($id, $field);
        ?>
        <div class="jevcfclear"></div>
        </div>
        <div class='jevcffieldpreview'  id='<?php echo $id; ?>preview'>
            <div class="previewlabel"><?php echo JText::_("JEVCF_PREVIEW"); ?></div>
            <div class="jevcflabel jevcfpl" id='pl<?php echo $id; ?>'><?php echo $field ? $field->attribute('label') : JText::_("JEVCF_FIELD_LABEL"); ?></div>
            <div id="pdv<?php echo $id; ?>">
                <?php
                echo $activeField;
                ?>
            </div>
        </div>
        <div class="jevcfclear"></div>
        <?php
        $html = ob_get_clean();

        return CustomFieldsHelper::setField($id, $field, $html, self::name);
    }

    public function getInput() {
        jimport("joomla.filesystem.folder");
        if (Jfolder::exists(JPATH_ADMINISTRATOR . "/components/com_comprofiler")) {
            return "";
        }
        $value = $this->value;
        if ($this->event && method_exists($this->event, "created_by") && $this->event->created_by()) {
            $creator = $this->event->created_by();
            $user = JEVHelper::getUser($creator);
        } else if ($this->event && isset($this->event->pers_id) && isset($this->event->linktouser)) {
            $creator = (int) $this->event->linktouser;
            $user = JEVHelper::getUser($creator);
        } else {
            $user = JFactory::getUser();
            $creator = $user->id;
        }

        $html = "";
        $lang = JFactory::getLanguage();
        $baseurl = JURI::root();

        if ($creator == 0) {
            return "'";
        } else {
            if (!isset($user->cbProfile)) {
                $db = JFactory::getDBO();
                $user->cbProfile = new stdClass();
                $db->setQuery('SELECT cbprofile.*, user.name, user.username, user.lastvisitDate, user.registerDate ' .
                        'FROM #__comprofiler AS cbprofile ' .
                        'LEFT JOIN #__users AS user ON ( user.id = cbprofile.user_id ) ' .
                        ' WHERE ( cbprofile.user_id = \'' . $user->id . '\' ) ');
                $user->cbProfile = $db->loadObject();
                if (is_null($user->cbProfile))
                    $user->cbProfile = false;
            }
            // this chops off the leading cb_ part 
            //$field = $this->fieldname;
            // so go to the raw data
            $field = $this->element->attributes()->fieldname;
            if ($user->cbProfile && isset($user->cbProfile->$field)) {
                return $user->cbProfile->$field;
            } else if (isset($user->$field)) {
                return $user->$field;
            } else {
                return "";
            }
        }
    }

    public function convertValue($value, $node) {

        if ($this->event && method_exists($this->event, "created_by") && $this->event->created_by()) {
            $creator = $this->event->created_by();
            $user = JEVHelper::getUser($creator);
        } else if ($this->event && isset($this->event->pers_id) && isset($this->event->linktouser)) {
            $creator = (int) $this->event->linktouser;
            $user = JEVHelper::getUser($creator);
        } else {
            $user = JFactory::getUser();
            $creator = $user->id;
        }

        $html = "";
        $lang = JFactory::getLanguage();
        $baseurl = JURI::root();

        if ($creator == 0) {
            return "'";
        } else {
            if (!isset($user->cbProfile)) {
                $db = JFactory::getDBO();
                $user->cbProfile = new stdClass();
                $db->setQuery('SELECT cbprofile.*, user.name, user.username, user.lastvisitDate, user.registerDate ' .
                        'FROM #__comprofiler AS cbprofile ' .
                        'LEFT JOIN #__users AS user ON ( user.id = cbprofile.user_id ) ' .
                        ' WHERE ( cbprofile.user_id = \'' . $user->id . '\' ) ');
                $user->cbProfile = $db->loadObject();
                if (is_null($user->cbProfile))
                    $user->cbProfile = false;
            }
            $field = $node->attribute("fieldname");
            if ($user->cbProfile && isset($user->cbProfile->$field)) {
                return $user->cbProfile->$field;
            } else if (isset($user->$field)) {
                return $user->$field;
            } else {
                return "";
            }
        }
    }

    public function constructFilter($node) {
        $this->node = $node;
        $this->filterType = "cbf";
        $this->filterLabel = JevcfField::varempty($this->attribute("filterlabel")) ? $this->attribute("label") : $this->attribute("filterlabel");
        $this->filterNullValue = "";
        $this->filter_value = $this->filterNullValue;
        $this->map = "csf" . $this->filterType;

        $registry = JRegistry::getInstance("jevents");
        $this->indexedvisiblefilters = $registry->get("indexedvisiblefilters", false);
        if ($this->indexedvisiblefilters === false)
            return;

        // This is our best guess as to whether this filter is visible on this page.
        $this->visible = in_array("customfield", $this->indexedvisiblefilters);

        // If using caching should disable session filtering if not logged in
        $cfg = JEVConfig::getInstance();
        $useCache = (int) $cfg->get('com_cache', 0);
        $user = JFactory::getUser();
        $mainframe = JFactory::getApplication();
        if ((int) JRequest::getVar('filter_reset', 0)) {
            JFactory::getApplication()->setUserState($this->filterType . '_fv_ses', $this->filterNullValue);
            $this->filter_value = $this->filterNullValue;
        }
        // ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
        else if (!$this->visible) {
            $this->filter_value = JRequest::getVar($this->filterType . '_fv', $this->filterNullValue, "request", "string");
        } else {
            $this->filter_value = JFactory::getApplication()->getUserStateFromRequest($this->filterType . '_fv_ses', $this->filterType . '_fv', $this->filterNullValue, "string");
        }

        //$this->filter_value = JRequest::getString($this->filterType.'_fv', $this->filterNullValue );
    }

    public function createJoinFilter() {
        if (is_string($this->filter_value) && trim($this->filter_value) == $this->filterNullValue)
            return "";
        $join = " #__comprofiler AS cbf ON ev.created_by=cbf.user_id";
        $db = JFactory::getDBO();
        $filter = " $this->map.value LIKE (" . $db->Quote($this->filter_value . "%") . ")";
        return $join . " AND " . $filter;
    }

    public function createFilter() {
        if (is_string($this->filter_value) && trim($this->filter_value) == $this->filterNullValue)
            return "";
        return "$this->map.id IS NOT NULL";
    }

    public function setSearchKeywords(&$extrajoin) {
        if ($this->attribute('searchable')) {
            $db = JFactory::getDBO();
            if (strpos($extrajoin, " #__comprofiler AS $this->map ON ev.created_by=$this->map.user_id") === false) {
                $extrajoin .= "\nLEFT JOIN #__comprofiler AS $this->map ON ev.created_by=$this->map.user_id";
            }

            $field = $this->element->attributes()->fieldname;
            return " $this->map.$field LIKE (" . $db->Quote('###' . "%") . ")";
        }
    }

    /**
     * Magic setter; allows us to set protected values
     * @param string $name
     * @return nothing
     */
    public function setValue($value) {
        $this->value = $value;
    }

    public function bindField($fieldid) {
        include_once("JevcfField.php");
        return JevcfField::bindFieldWithVarkeys($fieldid, get_object_vars($this));
    }

}
