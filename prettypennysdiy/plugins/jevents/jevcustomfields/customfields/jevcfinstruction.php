<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

include_once("JevcfField.php");

class JFormFieldJevcfinstruction extends JevcfField
{
	
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevcfinstruction';
	const name = 'jevcfinstruction';

	public static function loadScript($field=false)
	{
		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcfinstruction.js');

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
		<div class='jevcffieldinput'>

			<?php
                        CustomFieldsHelper::fieldtype($id, $field, self::name );        
			CustomFieldsHelper::hidden($id, $field, self::name);
			CustomFieldsHelper::label($id, $field, self::name);
                        CustomFieldsHelper::name($id, $field, self::name);
			?>

			<div class="jevcflabel"><?php echo JText::_("JEVCF_DEFAULT_VALUE"); ?></div>
			<div class="jevcfinputs">
				<textarea name="dv[<?php echo $id; ?>]" id="dv<?php echo $id; ?>" onchange="jevcfinstruction.setvalue('<?php echo $id; ?>');"  onkeyup="jevcfinstruction.setvalue('<?php echo $id; ?>');"
                                          rows="6" cols="30" ><?php echo $field ? htmlspecialchars_decode($field->attribute('default')) : ""; ?></textarea>
			</div>
			<div class="jevcfclear"></div>

			<?php
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
			<div class="jevcflabel jevcfpl" id='pl<?php echo $id; ?>' ><?php echo $field ? $field->attribute('label') : JText::_("JEVCF_FIELD_LABEL"); ?></div>
			<div id="pdv<?php echo $id; ?>">
		<?php echo $field ? htmlspecialchars_decode($field->attribute('default')) : ""; ?>
			</div>
		</div>
		<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id, $field, $html, self::name);

	}

	public  function getInput()
	{				
		// This field has no data so we use the default atrribute value
		$value = $this->value;
		if (!$value) {
			$value = $this->attribute('default');
		}
                $value = htmlspecialchars_decode($value);
		$class = ( $this->element->attributes()->class ? 'class="'.$this->element->attributes()->class.'"' : 'class="text_area"' );
		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", JText::_($value));
		return '<div '.$class.' id="'.$this->id.'" >'.$value.'</div>';
	}
	
	/**
	 * Magic setter; allows us to set protected values
	 * @param string $name
	 * @return nothing
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	public function bindField($fieldid)	{
		include_once("JevcfField.php");
		return JevcfField::bindFieldWithVarkeys($fieldid, get_object_vars($this));
	}

}