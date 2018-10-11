<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevboolean.php 1569 2009-09-16 06:22:03Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('radio');

include_once("jevcfradio.php");
class JFormFieldJevcfboolean extends JFormFieldJevcfradio //JFormFieldRadio
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'Jevcfboolean';
	const name = 'jevcfboolean';

	public function getInput() {
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("bootstrapchosen", 1) && strpos($this->element['class'],"btn-group")===false){
			$this->element['class'] .= " btn-group";
		}
		if (!$params->get("bootstrapchosen", 1) && strpos($this->element['class'],"btn-group")!==false){
			$this->element['class'] = str_replace("btn-group", "",$this->element['class']);
		}
		return parent::getInput();
	}
		
	public function getOptions()
	{		
		// Must load admin language files
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);
		
		$options = array ();
		$options[] = JHTML::_('select.option', 0, JText::_("Jev_No"));
		$options[] = JHTML::_('select.option', 1, JText::_("jev_Yes"));

		for ($i=0;$i<count($options);$i++){
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("bootstrapchosen", 1)){
				//$options[$i]->class="btn radio ".($options[$i]->value?"btn-success":"btn-danger");
				$options[$i]->class="btn radio";
			}
		}
		return $options;

		
	}	
	function fetchRequiredScript($name, &$node, $control_name)
	{
		return "JevStdRequiredFields.fields.push({'name':'" . $control_name . $name . "', 'default' :'" . $this->attribute('default') . "' ,'reqmsg':'" . trim(JText::_($this->attribute('requiredmessage'), true)) . "'}); ";

	}

	public function convertValue($value, $node)
	{
                $empty = "";
                $hiddenvalue = $this->attribute('hiddenvalue');
		if (!empty($hiddenvalue) && $value==$hiddenvalue) return $empty;
		static $values;
		if (!isset($values))
		{
			$values = array();
		}
		if (!isset($values[$this->attribute('name')]))
		{
			$values[$this->attribute('name')] = array();
			$values[$this->attribute('name')][0] = JText::_("JEV_NO");
			$values[$this->attribute('name')][1] = JText::_("JEV_YES");
		}
		return $values[$this->attribute('name')][(int) $value > 0 ? 1 : 0];

	}

	public function constructFilter($node)
	{
		$this->node = $node;
		$specialCharacters = array(" ", "-" , ",", ".");
		$this->filterType = str_replace($specialCharacters,"",$this->attribute("name"));
                $filterlabel = $this->attribute('filterlabel');
		$this->filterLabel = empty($filterlabel)?$this->attribute("label"):$filterlabel;
                $filterdefault = $this->attribute('filterdefault');
                $default = $this->attribute('default');
		$this->filterNullValue = empty($filterdefault)?(empty($default)?"":$default):$filterdefault;
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
		if ((int) JRequest::getVar('filter_reset', 0))
		{
			JFactory::getApplication()->setUserState( $this->filterType.'_fv_ses', $this->filterNullValue ); 			
			$this->filter_value = $this->filterNullValue;
		}
		// ALSO if this filter is not visible on the page then should not use filter value - does this supersede the previous condition ???
		else if (!$this->visible)
		{
			$this->filter_value = JRequest::getVar($this->filterType . '_fv', $this->filterNullValue, "request", "int");
		}
		else
		{
			$this->filter_value = JFactory::getApplication()->getUserStateFromRequest($this->filterType . '_fv_ses', $this->filterType . '_fv', $this->filterNullValue);
		}
		$this->filter_value = (int) $this->filter_value;

		//$this->filter_value = JRequest::getInt($this->filterType.'_fv', $this->filterNullValue );

	}

	public function createJoinFilter()
	{
		if (trim($this->filter_value) == $this->filterNullValue)
			return "";
		$join =  " #__jev_customfields AS $this->map ON det.evdet_id=$this->map.evdet_id";
		$db = JFactory::getDBO();
		$filter =  "$this->map.name=" . $db->Quote($this->attribute('name')) . " AND $this->map.value=" . $db->Quote($this->filter_value);
		return $join . " AND ". $filter;
	}

	public function createFilter()
	{
		if (trim($this->filter_value) == $this->filterNullValue)
			return "";
		return "$this->map.id IS NOT NULL";		
	}

	public function createFilterHTML()
	{
		return parent::createFilterHTML();
		
		$filterList = array();
		$filterList["title"] = "<label class='evdate_label' for='" . $this->filterType . "_fv'>" . $this->filterLabel . "</label>";
		$name = $this->filterType ;
		
		$name = $this->node->name;
		$id = $this->node->id;
		$value = $this->node->value;
		$this->node->name = $this->filterType . "_fv";
		$this->node->id = $this->filterType . "_fv";
		$this->node->value = $this->filter_value;
		$filterList["html"] = $this->node->getInput();
		$this->node->name = $name;
		$this->node->id = $id;
		$this->node->value = $value;

		$name .= $this->filterNullValue;
		$script = "function reset" . $this->filterType . "_fv(){\$('jform_$name').checked=true;};\n";
		$script .= "try {JeventsFilters.filters.push({action:'reset" . $this->filterType . "_fv()',id:'" . $this->filterType . "_fv',value:" . $this->filterNullValue . "});} catch (e) {}";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		return $filterList;

	}

	/**
	 * Magic setter; allows us to set protected values
	 * @param string $name
	 * @return nothing
	 */
	public function setValue($value) {
		$this->value = $value;
	}


	public static function loadScript($field=false)
	{
		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcfboolean.js');

		if ($field)
		{
			$id = 'field' . $field->fieldname;
		}
		else
		{
			$id = '###';
		}
		ob_start();
		?>
		<div class='jevcffieldinput jevcfbooleanfield'>
			<?php
                        CustomFieldsHelper::fieldtype($id, $field, self::name );
			CustomFieldsHelper::hidden($id, $field, self::name);
			CustomFieldsHelper::label($id, $field, self::name);
			CustomFieldsHelper::name($id, $field, self::name);
			CustomFieldsHelper::tooltip($id, $field);
			?>

			<div class="jevcflabel">
				<?php echo JText::_("JEVCF_DEFAULT_VALUE"); ?>
				<?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_DEFAULT_VALUE_DESC'));?>
			</div>
			<div class="jevcfinputs">
				<label for="dv1<?php echo $id; ?>"><?php echo JText::_("JEVCF_YES"); ?></label>
				<input type="radio" name="dv[<?php echo $id; ?>]"  id="dv1<?php echo $id; ?>" value="1" <?php if ($field && $field->attribute('default') == 1)
				echo 'checked="checked"'; if (!$field)
				echo 'checked="checked"'; ?> onclick="jevcfboolean.settrue('<?php echo $id; ?>');" />
				<label for="dv0<?php echo $id; ?>"><?php echo JText::_("JEVCF_NO"); ?></label>
				<input type="radio" name="dv[<?php echo $id; ?>]" id="dv0<?php echo $id; ?>" value="0" <?php if ($field && $field->attribute('default') == 0)
			echo 'checked="checked"'; ?> onclick="jevcfboolean.setfalse('<?php echo $id; ?>');"/>
			</div>
			<div class="jevcfclear"></div>

			<?php
			CustomFieldsHelper::required($id, $field);
			CustomFieldsHelper::requiredMessage($id, $field);
			CustomFieldsHelper::conditional($id, $field);
			CustomFieldsHelper::allowoverride($id, $field);
			CustomFieldsHelper::hiddenValue($id, $field);
			CustomFieldsHelper::filterOptions($id, $field);
			CustomFieldsHelper::filtermenuOptions($id, $field);
          
                        // use special filter default
                        ?>
                        <div class="jevcfnotranslate filterdefault filterdefault<?php echo $id; ?>" style="display:none;">
                            <div class="jevcflabel">
                                <label for="filterdefault<?php echo $id; ?>">
                        <?php echo JText::_("JEVCF_FILTER_DEFAULT"); ?>
                        <?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_FILTER_DEFAULT_DESC')); ?>
                                </label>
                            </div>
                        
                            <?php 
                            $fieldattribute = "filterdefault";
                            $fieldid0="filterdefault". $id."_0";
                            $fieldid1="filterdefault". $id."_1";
                            $fieldid2="filterdefault". $id."_2";
                            $fieldname = "filterdefault[".$id."]";
                            ?>
                            <div class="jevcfinputs radio btn-group">
                                <label for="<?php echo $fieldid1; ?>" class="btn radio"><?php echo JText::_("JEVCF_YES"); ?>
                                    <input type="radio" name="<?php echo $fieldname; ?>"  id="<?php echo $fieldid1; ?>" value="1" <?php
                                if ($field && $field->attribute($fieldattribute) == 1) {
                                    echo 'checked="checked"';
                                } else if (!$field && $defaultvalue) {
                                    echo 'checked="checked"';
                                }
                                ?> />
                                </label>
                                <label for="<?php echo $fieldid0; ?>"  class="btn radio"><?php echo JText::_("JEVCF_NO"); ?>
                                    <input type="radio" name="<?php echo $fieldname; ?>"  id="<?php echo $fieldid0; ?>" value="0" <?php
                                    if ($field && $field->attribute($fieldattribute) == 0) {
                                        echo 'checked="checked"';
                                    } else if (!$field && !$defaultvalue) {
                                        echo 'checked="checked"';
                                    }
                                    ?> />
                                </label>
                                <label for="<?php echo $fieldid2; ?>"  class="btn radio"><?php echo JText::_("JEVCF_ANY"); ?>
                                    <input type="radio" name="<?php echo $fieldname; ?>"  id="<?php echo $fieldid2; ?>" value="2" <?php
                                    if ($field && $field->attribute($fieldattribute) == 2) {
                                        echo 'checked="checked"';
                                    } else if (!$field && !$defaultvalue) {
                                        echo 'checked="checked"';
                                    }
                                    ?> />
                                </label>    
                            </div>
                        </div>
                        <div class="jevcfclear"></div>
                        <?php
                         
                        if ($field) {
                            ?>
                            <script type="text/javascript">
                                jevcfforms.revealConditionalDisplayField(['fmo<?php echo $id; ?>', 'fo<?php echo $id; ?>'], 'filterdefault<?php echo $id; ?>');
                            </script>
                            <?php
                        }
                        
			CustomFieldsHelper::searchable($id, $field);
			CustomFieldsHelper::applicableCategories($id, $field) ;
			CustomFieldsHelper::accessOptions($id, $field);
			CustomFieldsHelper::readaccessOptions($id, $field);
                        CustomFieldsHelper::fieldclass($id, $field);
			CustomFieldsHelper::universal($id, $field);
			?>

			<div class="jevcfclear"></div>

		</div>
		<div class='jevcffieldpreview' id='<?php echo $id; ?>preview'>
			<div class="previewlabel"><?php echo JText::_("JEVCF_PREVIEW"); ?></div>
			<div class="jevcflabel jevcfpl" id='pl<?php echo $id; ?>' ><?php echo $field ? $field->attribute('label') : JText::_("JEVCF_FIELD_LABEL"); ?></div>
			<label for="pdv0<?php echo $id; ?>"><?php echo JText::_("JEVCF_YES"); ?></label>
			<input type="radio" name="pdv[<?php echo $id; ?>]"  id="pdv0<?php echo $id; ?>" value="1"  <?php if ($field && $field->attribute('default') == 1)
			echo 'checked="checked"'; if (!$field)
			echo 'checked="checked"'; ?> />
			<label for="pdv1<?php echo $id; ?>"><?php echo JText::_("JEVCF_NO"); ?></label>
			<input type="radio" name="pdv[<?php echo $id; ?>]" id="pdv1<?php echo $id; ?>" value="0" <?php if ($field && $field->attribute('default') == 0)
			echo 'checked="checked"'; ?> />
		</div>
		<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		//return $html;
		return CustomFieldsHelper::setField($id, $field, $html, self::name);

	}

	public function bindField($fieldid)
	{
		return parent::bindField($fieldid);
	}

	
}
