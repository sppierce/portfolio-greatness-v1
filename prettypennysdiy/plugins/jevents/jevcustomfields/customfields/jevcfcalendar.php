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

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('calendar');

class JFormFieldJevcfcalendar extends JFormFieldCalendar
{

	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'jevcfcalendar';
	const name = 'jevcfcalendar';

	public static function loadScript($field=false)
	{
		JHtml::script('plugins/jevents/jevcustomfields/customfields/js/jevcfcalendar.js');

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
			CustomFieldsHelper::tooltip($id, $field);

			$format = "%Y-%m-%d";
			if ($field)
			{
				$format = $field->attribute('format');
			}
			?>

			<div class="jevcflabel">
				<?php echo JText::_("JEVCF_DEFAULT_VALUE"); ?>
				<?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_DEFAULT_VALUE_DESC'));?>
			</div>
			<div class="jevcfinputs">
				<input type="text" name="dv[<?php echo $id; ?>]" id="dv<?php echo $id; ?>" size="<?php echo $field ? $field->attribute('size') : 10; ?>"   value="<?php echo $field ? $field->attribute('default') : "NOW"; ?>"  onchange="jevcfcalendar.setvalue('<?php echo $id; ?>');"  onkeyup="jevcfcalendar.setvalue('<?php echo $id; ?>');"/>
				<img  alt="calendar" src="<?php echo JURI::root(true); ?>/templates/system/images/calendar.png" class="calendar" />
			</div>
			<div class="jevcfclear"></div>

			<div class="jevcflabel">
				<?php echo JText::_("JEVCF_CALENDAR_FORMAT"); ?>
				<?php CustomFieldsHelper::fieldTooltip(JText::_('JEVCF_CALENDAR_FORMAT_DESC'));?>
			</div>
			<div class="jevcfinputs">
				<input type="text" name="fmt[<?php echo $id; ?>]" id="dv<?php echo $id; ?>format" size="15"   value="<?php echo $format; ?>" />
			</div>
			<div class="jevcfclear"></div>

			<?php
			CustomFieldsHelper::size($id, $field, self::name);
			CustomFieldsHelper::maxlength($id, $field, self::name);
			CustomFieldsHelper::required($id, $field);
			CustomFieldsHelper::requiredMessage($id, $field);
			CustomFieldsHelper::conditional($id, $field);
			CustomFieldsHelper::allowoverride($id, $field);
			CustomFieldsHelper::hiddenValue($id, $field);
			CustomFieldsHelper::searchable($id,  $field);
			//CustomFieldsHelper::filterOptions($id, $field);
			//CustomFieldsHelper::filtermenuOptions($id, $field);
                	//CustomFieldsHelper::filterDefault($id, $field);
			CustomFieldsHelper::applicableCategories($id, $field);
			CustomFieldsHelper::accessOptions($id, $field);
			CustomFieldsHelper::readaccessOptions($id,  $field);
                        CustomFieldsHelper::fieldclass($id, $field);
			CustomFieldsHelper::universal($id, $field);
			?>

			<div class="jevcfclear"></div>

		</div>
		<div class='jevcffieldpreview'  id='<?php echo $id; ?>preview'>
			<div class="previewlabel"><?php echo JText::_("JEVCF_PREVIEW"); ?></div>
			<div class="jevcflabel jevcfpll" id='pl<?php echo $id; ?>' ><?php echo $field ? $field->attribute('label') : JText::_("JEVCF_FIELD_LABEL"); ?></div>
			<input type="text"  id="pdv<?php echo $id; ?>" value="<?php echo $field ? $field->attribute('default') : ""; ?>"  size="<?php echo $field ? $field->attribute('size') : 10; ?>"   />
			<img  alt="calendar" src="<?php echo JURI::root(true); ?>/templates/system/images/calendar.png" class="calendar" />
		</div>
		<div class="jevcfclear"></div>
		<?php
		$html = ob_get_clean();

		return CustomFieldsHelper::setField($id, $field, $html, self::name);

	}

	function OLDgetInput()
	{
		JHTML::_('behavior.calendar'); //load the calendar behavior

		$format = ( $this->attribute('format') ? $this->attribute('format') : '%Y-%m-%d' );
		$class = $this->element['class'] ? $this->element['class'] : 'inputbox';

		// Joomla can only take date values in the format "%Y-%m-%d" despite taking the $format argument here so we do a workaround
		$this->newvalue = $this->convertTime("%Y-%m-%d", $format, $this->value);
		$HTML =  JHTML::_('calendar', $this->newvalue, $this->name, $this->id, $format, array('class' => $class));
		// This replaces the value in the input box and thankfully Joomla has told the calendar script to use the format provided even though Joomla itself can't cope with it.
		return str_replace($this->newvalue,$this->value,$HTML);
	}

	protected
			function getInput()
	{
		if(method_exists("JEVHelper","getMinYear"))
		{
			$minyear = JEVHelper::getMinYear();
			$maxyear = JEVHelper::getMaxYear();
		}
		else
		{
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$minyear = $params->get("com_earliestyear", 1970);
			$maxyear = $params->get("com_latestyear", 2150);
		}
		$inputdateformat  = ( $this->attribute('format') ? $this->attribute('format') : '%Y-%m-%d' );

		if ($this->value=="NOW"){
			list($cyear, $cmonth, $cday) = JEVHelper::getYMD();
                        $now = new JevDate("$cyear-$cmonth-$cday 08:00:00");
			$this->value = $now->toFormat($inputdateformat);
		}
		else if ($this->value=="--"){
			$this->value = "";
		}

		$this->newvalue = $this->value ? $this->convertTime( "%Y-%m-%d", $inputdateformat, $this->value) : "";

		static $firsttime;
		if (!defined($firsttime)){
			$document = JFactory::getDocument();
			$js = "\n cfEventEditDateFormat='$inputdateformat';//Date.defineParser(cfEventEditDateFormat.replace('d','%d').replace('m','%m').replace('Y','%Y'));";
			$document->addScriptDeclaration($js);
		}
		$inputdateformat = str_replace("%","",$inputdateformat );
		ob_start();
		JEVHelper::loadElectricCalendar($this->name, $this->id, $this->newvalue, $minyear, $maxyear, 'var elem = $("'.$this->name.'");'.$this->element['onhidestart'], "elem = $('".$this->name."');".$this->element['onchange'], $inputdateformat);
		?>
		<input type="hidden"  name="<?php echo str_replace("]","2]",$this->name);?>" id="<?php echo $this->id;?>2" value="" />
		<?php
		$html = ob_get_clean();
		return $html;

	}

	function fetchRequiredScript($name, &$node, $control_name)
	{
		return "JevStdRequiredFields.fields.push({'name':'" . $control_name . $name . "', 'default' :'" . $this->attribute('default') . "' ,'reqmsg':'" . trim(JText::_($this->attribute('requiredmessage'), true)) . "'}); ";

	}

	public function attribute($attr)
	{
		$val = $this->element->attributes()->$attr;
		$val = !is_null($val) ? (string) $val : null;
		return $val;

	}

	/**
	 * Magic setter; allows us to set protected values
	 * @param string $name
	 * @return nothing
	 */
	public function setValue($value)
	{
		$this->value = $value;

	}

	// thanks to http://php.net/manual/en/function.strftime.php#90966
	private function convertTime($dformat, $sformat, $ts)
	{
		if (function_exists("strptime")){
			$datetime = strptime($ts, $sformat);
			$time = mktime(
				(int) $datetime["tm_hour"], 
				(int) $datetime["tm_min"],
				(int) $datetime["tm_sec"],
				(int) $datetime["tm_mon"] + 1,
				(int) $datetime["tm_mday"],
				(int) $datetime["tm_year"] + 1900
			);
			return strftime($dformat, $time);
		}
		else {
			$masks = array(
			  '%d' => '(?P<d>[0-9]{2})',
			  '%m' => '(?P<m>[0-9]{2})',
			  '%Y' => '(?P<Y>[0-9]{4})',
			  '%H' => '(?P<H>[0-9]{2})',
			  '%M' => '(?P<M>[0-9]{2})',
			  '%S' => '(?P<S>[0-9]{2})',
			);

			$rexep = "#".strtr(preg_quote($sformat), $masks)."#";
			if(!preg_match($rexep, $ts, $out))
			  return false;

			$datetime = array(
			  "tm_sec"  => isset($out['S']) ? (int) $out['S'] : 0,
			  "tm_min"  => isset($out['M']) ? (int) $out['M'] : 0,
			  "tm_hour" => isset($out['H']) ? (int) $out['H'] : 0,
			  "tm_mday" => isset($out['d']) ? (int) $out['d'] : 0,
			  "tm_mon"  => $out['m'] ? $out['m']-1:0,
			  "tm_year" => $out['Y'] > 1900 ? $out['Y'] - 1900 : 0,
			);
			$time = mktime(
				(int) $datetime["tm_hour"],
				(int) $datetime["tm_min"],
				(int) $datetime["tm_sec"],
				(int) $datetime["tm_mon"] + 1,
				(int) $datetime["tm_mday"],
				(int) $datetime["tm_year"] + 1900
			);
			return strftime($dformat, $time);
		}
	}

	public function convertValue($value, $node)
	{
		if ($value =="--"){
			$value ="";
		}
		return $value;
	}

	function toXML($field){
		$result = array();
		$result[] = "<field ";
		foreach (get_object_vars($field) as $k => $v){
			if ( $k=="options" || $k=="html"  || $k=="defaultvalue" || $k=="name") continue;
			if ($k=="field_id") {
				$k="name";
			}
			if ($k == "params")
			{
				if (is_string($field->params))
				{
					$field->params = @json_decode($field->params);
				}
				if (is_object($field->params))
				{
					foreach (get_object_vars($field->params) as $label => $value)
					{
						$result[] = $label . '="' . htmlspecialchars($value, ENT_QUOTES|ENT_XML1, "UTF-8")  . '" ';
					}
				}
				continue;
			}                        

			$result[] = $k . '="' . htmlspecialchars($v, ENT_QUOTES|ENT_XML1, "UTF-8") . '" ';
		}
		$result[] = " />";
		$xml =  implode(" " , $result);
		return $xml;
	}

	public function addAttribute($name, $value)
	{
		// Add the attribute to the element, override if it already exists
		$this->element->attributes()->$name = $value;
	}
	
	public function bindField($fieldid)	{
		include_once("JevcfField.php");
		$myXml = JevcfField::bindFieldWithVarkeys($fieldid, get_object_vars($this));
                unset($myXml->fmt);
		JevcfField::bindString($myXml, $fieldid, "fmt", "format");
		return $myXml;
	}

}