<?php
/**
 * copyright (C) 2009-2014 GWE Systems Ltd - All rights reserved
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
JLoader::register('jevFilterProcessing',JPATH_SITE."/components/com_jevents/libraries/filters.php");

jimport( 'joomla.plugin.plugin' );

class plgJEventsJevfeatured extends JPlugin
{

	private $event = null;
	private $showingemailaddress = false;
	private $jomsocial = false;

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		JFactory::getLanguage()->load( 'plg_jevents_jevfeatured',JPATH_ADMINISTRATOR );

	}

	/**
	 * When editing a JEvents menu item can add additional menu constraints dynamically
	 *
	 */
	public function onEditMenuItem(&$menudata, $value,$control_name,$name, $id, $param)
	{
		// already done this param
		if (isset($menudata[$id])) return;

		static $matchingextra = null;
		// find the parameter that matches jevf: (if any)
		if (!isset($matchingextra)){
			$params = $param->getGroup('params');
				foreach ($params as $key => $element){
					$val = $element->value;
					if (strpos($key,"jform_params_extras")===0 ){
						if (strpos($val,"jevf:")===0){
							$matchingextra = $key;
							break;
						}
					}
				}			
			if (!isset($matchingextra)){
				$matchingextra = false;
			}
		}

		// either we found matching extra and this is the correct id or we didn't find matching extra and the value is blank
		if (strpos($value,"jevf:")===0 || (($value==""||$value=="0") && $matchingextra===false)){
			$matchingextra = true;
			$invalue = str_replace(" ","",$value);
			if ($invalue =="") $invalue =  'jevf:-1';

			$options = array();
			$options[] = JHTML::_('select.option', 'jevf:-1', JText::_('JEVF_ANY_LEVEL'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevf:0', JText::_('JEVF_LEVEL_0'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevf:1', JText::_('JEVF_LEVEL_1'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevf:2', JText::_('JEVF_LEVEL_2'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevf:3', JText::_('JEVF_LEVEL_3'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevf:4', JText::_('JEVF_LEVEL_4'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevf:5', JText::_('JEVF_LEVEL_5'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevf:6', JText::_('JEVF_LEVEL_6'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevf:7', JText::_('JEVF_LEVEL_7'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevf:8', JText::_('JEVF_LEVEL_8'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevf:9', JText::_('JEVF_LEVEL_9'), 'id', 'title');

			$invalue = explode(",",$invalue);
/*
			$input = "<select multiple='multiple' id='jevfeatselect'  size='5' onchange='jupdateJevFeat();'>";
			foreach ($options as $option) {
				$title=$option->title;
				$val=$option->id;
				$selected = in_array($option->id,$invalue)?"selected='selected'":"";
				$input .= "<option value='$val' $selected>$title</option>";
			}
			$input .= "</select>";
*/
			$input = JHTML::_('select.genericlist',  $options, 'jevfeatselect', 'multiple="multiple" size="4" onchange="jupdateJevFeat()"', 'id', 'title', $invalue, 'jevfeatselect' );
			if (version_compare(JVERSION, '1.6.0', '>=') ){
				$input .= '<input type="hidden"  name="'.$name.'"  id="jevfeat" value="'.$value.'" />';
				// for CB plugins !!!
				$input .= '<input type="hidden"  name="'.$control_name.'['.$name.']"   id="compat_jevfeat"  value="'.$value.'" />';
				$input .= '<div style="clear:left"></div>';
			}
			else {
				$input .= '<input type="hidden"  name="'.$control_name.'['.$name.']"  id="jevfeat" value="'.$value.'" />';
			}

			$script = '
			function jupdateJevFeat(){
				jupdateJevFeatByField("jevfeat");
				jupdateJevFeatByField("compat_jevfeat");
			}
			function jupdateJevFeatByField(fieldid){
				var select = document.getElement("select#jevfeatselect");
				var input = document.getElementById(fieldid);
				if (!input) return;
				input.value="";
				select.getChildren().each(
					function(item,index){
						if (item.selected) {
							// if select none - reset everything else
							if (item.value=="jevf:-1") {
								select.selectedIndex=0;
								return;
							}
							if (input.value!="") input.value+=",";
							input.value+=item.value;
						}
					}
				);
				// trigger Bootstrap Chosen replacement
				try {
					jQuery("select#jevfeatselect").trigger("liszt:updated");
				}
				catch (e){
				}
			}
			';
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($script);

			$data = new stdClass();
			$data->name = "jevuser";
			$data->html = $input;
			$data->label = "JEVF_FEATURED_LEVEL";
			$data->description = "JEVF_FEATURED_LEVEL_DESC";
			$data->options = array();
			$menudata[$id] = $data;
		}
	}

	public function onListIcalEvents( & $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroupdby=false)
	{

		if(JFactory::getApplication()->isAdmin() && JRequest::getCmd("option")=="com_jevents") {
			return;
		}

		// Have we specified specific people for the menu item
		$compparams = JComponentHelper::getParams(JRequest::getCmd("option","com_jevents"));

		// If loading from a module then get the modules params from the registry
		$reg = JFactory::getConfig();
		$modparams = $reg->get("jev.modparams",false);
		if ($modparams){
			$compparams = $modparams;
		}

		for ($extra = 0;$extra<20;$extra++){
			$extraval = $compparams->get("extras".$extra, false);
			if (strpos($extraval,"jevf:")===0){
				break;
			}
		}
		if (!$extraval) return true;

		$invalue = str_replace("jevf:","",$extraval);
		$invalue = str_replace(" ","",$invalue);
		if (strlen($invalue)>0){
			$invalue = explode(",",$invalue);
			JArrayHelper::toInteger($invalue);
		}
		else {
			return true;
		}
		//  any events
		if (in_array(-1,$invalue)) return;

		$extrawhere[]  = "det.priority IN (".implode(",",$invalue).")";
		return true;
	}

        function onEditCustom(&$row, &$customfields)
	{
            /// do this via javascript 
            if ($row->_ev_id ==0){
                $default = $this->params->get("defaultpriority", 0);
                $script = <<<SCRIPT
jQuery(document).ready(function ($){
    jQuery('#jevents #priority').val($default);
    jQuery('#jevents #priority').trigger("chosen:updated");
    // old style version - still needed!
    jQuery('#jevents #priority').trigger("liszt:updated");
                        
    });
SCRIPT;
                JFactory::getDocument()->addScriptDeclaration($script);
            }
            
        }
        
        public function onBeforeSaveEvent(&$array, &$rrule, $dryrun = false)
	{
            if (!isset($array["priority"])){
                $array["priority"] = $this->params->get("defaultpriority", 0);
            }
        }


}
