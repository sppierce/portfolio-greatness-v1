<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport("joomla.form.form");
include_once("JevcfField.php");

class JevCfForm extends JForm
{

	private $event;
        private $filterElements;
	// for attendees only fields
	private $hasrsvp = false;

	public function __construct($name, array $options = array()) {
		parent::__construct($name, $options);

		// Include mootools framework
		// JHtml::_('behavior.framework', true);
	}

	/**
	 * Method to bind data to the form.
	 *
	 * @param   mixed  $data  An array or object of data to bind to the form.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function bind($data)
	{
		// Make sure there is a valid JForm XML document.
		if (!($this->xml instanceof SimpleXMLElement))
		{
			return false;
		}

		// The data must be an object or array.
		if (!is_object($data) && !is_array($data))
		{
			return false;
		}

		// Convert the input to an array.
		if (is_object($data))
		{
			if ($data instanceof JRegistry)
			{
				// Handle a JRegistry.
				$data = $data->toArray();
			}
			elseif ($data instanceof JObject)
			{
				// Handle a JObject.
				$data = $data->getProperties();
			}
			else
			{
				// Handle other types of objects.
				$data = (array) $data;
			}
		}
		// Process the input data.
		foreach ($data as $k => $v)
		{
			if (strpos($k, "fieldid_")===0){
				continue;
			}
			if ($this->findFieldSlim($k))
			{
				// If the field exists set the value.
				$this->data->set($k, $v);
			}
			elseif (is_object($v) || JArrayHelper::isAssociative($v))
			{
				// If the value is an object or an associative array hand it off to the recursive bind level method.
				$this->bindLevel($k, $v);
			}

		}
		return true;
	}

	/**
	 * Method to get a form field represented as an XML element object.
	 *
	 * @param   string  $name   The name of the form field.
	 * @param   string  $group  The optional dot-separated form group path on which to find the field.
	 *
	 * @return  mixed  The XML element object for the field or boolean false on error.
	 *
	 * @since   11.1
	 */
	protected function findFieldSlim($name)
	{

		// Make sure there is a valid JForm XML document.
		if (!($this->xml instanceof SimpleXMLElement))
		{
			return false;
		}

		$fields = array();

		static $allfields;
		if (!isset($allfields))
		{
			$allfields = array();
			$tempfields = $this->xml->xpath('//field');
			foreach ($tempfields as $field) {
				$allfields[(string)$field["name"]] = $field;
			}
		}
		if (count($allfields)==0) {
			return false;
		}
		
		// Make sure something was found.
		if (!isset($allfields[$name]))
		{
			// May be a new xml file
			$tempfields = $this->xml->xpath('//field');
			foreach ($tempfields as $field) {
				$allfields[(string)$field["name"]] = $field;
			}
			if (!isset($allfields[$name]))
			{
				$allfields[$name] = false;
			}

			return $allfields[$name];
		}

		return $allfields[$name];

	}

	public function setEvent($event)
	{
		$this->event = $event;

	}

	/**
	 * Render
	 *
	 * @access	public
	 * @param	string	The name of the control, or the default text area if a setup file is not found
	 * @return	string	HTML
	 * @since	1.5
	 */
	public function render($name = 'custom_', $group = 'default', &$customfields)
	{
                static $requiredScripts = array();
		$fieldsetData = $this->getFieldsetInfo($group);
		if (!$fieldsetData)
		{
			return false;
		}

		// The description from the fieldset node!
		if ($fieldsetData && $fieldsetData->attributes()->description != "")
		{
			// add the params description to the display
			$desc = JText::_($fieldsetData->attributes()->description);
			//$customfield = array("label" => "", "input" => $desc);
			//$customfields["customfield_" . $group] = $customfield;
		}
                else if ($fieldsetData) {
                        $desc = JText::_((string)$fieldsetData->description);
                }
                else {
                    $desc = "";
                }

		$params = $this->getFieldset($group);

		if (!$params)
		{
			return false;
		}

		$doc = JFactory::getDocument();

		foreach ($params as $p => $node)
		{
			// attach the event to the $node
			$node->event = $this->event;

			$task = JRequest::getCmd('task', 'cpanel.show');
			// default state of allow override is TRUE
			$allowoverride = $node->attribute('allowoverride');
			if (!is_null($allowoverride) && $allowoverride != 0)
				$allowoverride = 1;
			if ($task == "icalrepeat.edit" && !$allowoverride)
				continue;

			// check access
			$user = JFactory::getUser();
			if ($node->attribute('access') && $node->attribute('access')!=0 && !in_array($node->attribute('access'), JEVHelper::getAid($user, 'array')))
				continue;

			// Disabled for now
			$required = $node->required ? JText::_("JEV_REQUIRED") : "";
			//$required = "";
			$customfield = array();

			// magic method to get the input
			// set id if not already set
			if (!$node->id) {
				$node->id = $node->name;
			}
			$customfield["input"] = $node->input;
			$customfield["group"] = $group;
                        $customfield["node"] = $node;
                        
                        // Is this field conditional - if so setup showon
                        $datashowon = '';
                        $showonstring = $node->getAttribute('showon');
                        if (!$showonstring && $node->getAttribute('cf') && $node->getAttribute('cfvfv')!="")
                        {
                            // Drop the 'field' prefix from our field id
                            $showonstring = "jform[".substr($node->getAttribute('cf'), 5)."]:".$node->getAttribute('cfvfv');
                        }
                        if ($showonstring)
                        {

                                JHtml::_('jquery.framework');
                                JHtml::_('script', 'jui/cms.js', false, true);
                                $showonarr = array();

                                foreach (preg_split('%\[AND\]|\[OR\]%', $showonstring) as $showonfield)
                                {
                                        $showon   = explode(':', $showonfield, 2);
                                        $showonarr[] = array(
                                                'field'  => $showon[0],
                                                'values' => explode(',', $showon[1]),
                                                'op'     => (preg_match('%\[(AND|OR)\]' . $showonfield . '%', $showonstring, $matches)) ? $matches[1] : ''
                                        );
                                }
                                $datashowon = ' data-showon=\'' . json_encode($showonarr) . '\'';
                        }
                         $customfield["showon"] = $datashowon;

			// Jform takes care of the label and the tooltip
                        if ($node->fieldclass) {
                            $node->labelclass = $node->fieldclass;
                        }
                        
			$customfield["label"] = JText::_($node->label);

			// should we reset the label?
			$customfield["label"] = strip_tags($customfield["label"]) != "" ? $customfield["label"] : "";

			if (strpos($node->name, ".") === false && strpos($node->name, "#") === false)
			{
				// if its the fall back class then the type is not valid
				if (get_class($node)=="JFormFieldText"){
					JError::raise(E_ERROR, 500, JText::sprintf("JEV_INVALID_FIELD_TYPE",$node->fieldname));
				}
				$key = $node->attribute('name');
				$customfields["customfield_" . $key] = $customfield;
			}

			if ($required)
			{

				if (method_exists($node, "fetchRequiredScript"))
				{
					$script = $node->fetchRequiredScript($node->name, $node, $name);
                                        if (!in_array($script, $requiredScripts)){
                                            $doc->addScriptDeclaration($script);
                                            $requiredScripts[] = $script;
                                        }
				}
				else
				{
					if (strpos($node->name, ".") !== false || strpos($node->name, "#") !== false)
					{
						// use $node->attribute("name") since $node->name is the complex form element name
						$script = "JevStdRequiredFields.fields.push({'name':'" . $node->attribute("name") . "', 'default' :'" . $node->attribute("default") . "' ,'reqmsg':'" . trim(JText::_($node->attribute("requiredmessage"), true)) . "'}); ";
					}
					else
					{
						$script = "JevStdRequiredFields.fields.push({'name':'" . $node->id . "', 'default' :'" . $node->attribute("default") . "' ,'reqmsg':'" . trim(JText::_($node->attribute("requiredmessage"), true)) . "'}); ";
					}
					$doc->addScriptDeclaration($script);
				}
			}

			$catrestrictions = $node->attribute('categoryrestrictions');
			if ($catrestrictions)
			{
				static $done;
				if (!isset($done))
				{
					$done = array();
				}
				if (!in_array($node->name . $name, $done))
				{
					$done[] = $node->name . $name;

					$cats = explode(",", $node->attribute('categoryrestrictions'));

					if (method_exists($node, "fetchCategoryRestrictionScript"))
					{
						$script = $node->fetchCategoryRestrictionScript($node->attribute("name"), $node, $name, $cats);
						$doc->addScriptDeclaration($script);
					}
					else
					{
						$script = "jQuery(document).ready( function(){if (typeof(JevrCategoryFields)!='undefined') {JevrCategoryFields.fields.push({'name':'" . $node->attribute("name") . "', 'default' :'" . $node->attribute("default") . "' ,'catids':" . json_encode($cats) . "});} });";
						$doc->addScriptDeclaration($script);
					}
				}
			}
		}


		return true;

	}

	/**
	 * Render
	 *
	 * @access	public
	 * @param	string	The name of the control, or the default text area if a setup file is not found
	 * @return	string	HTML
	 * @since	1.5
	 */
	public function setupRender()
	{
		// make sure only called once!
		static $executed = false;
		if ($executed ) return;
		$executed = true;

		// Get all the categories and their parentage
		$db = JFactory::getDBO();
            	$db->setQuery("SELECT id, parent_id from #__categories where extension='com_jevents' and published=1");
		$catlist = $db->loadObjectList("id");

		$cats = array();
		foreach ($catlist as $cat)
		{
			// extract the complete ancestry
			if (!array_key_exists($cat->id, $cats))
			{
				$cats[$cat->id] = array();
				$cats[$cat->id][] = $cat->id;
				$parent = ($cat->parent_id > 0 && array_key_exists($cat->parent_id, $catlist)) ? $catlist[$cat->parent_id] : false;
				while ($parent)
				{
					$cats[$cat->id][] = $parent->id;
					$parent = ($parent->parent_id > 0 && array_key_exists($parent->parent_id, $catlist)) ? $catlist[$parent->parent_id] : false;
				}
			}
		}
		// Must set this up for empty category too
		$cats[0] = array();
		$cats[][] = 0;

		$cats = json_encode($cats);

		$missingRequiredMessage = JText::_("JEV_REQUIRED_CUSTOM_FIELD_NOTSET", true);

		// setup required fields script
		$doc = JFactory::getDocument();
		$script = <<<SCRIPT
var JevrRequiredFields = {
	fields: new Array(),
	verify:function (form){
		form = jQuery(form);
		var messages =  new Array();
		valid = true;
		JevrRequiredFields.fields.forEach(function (item,i) {
			name = item.name;

		    // should we skip this test because of category restrictions?
			if (JevrCategoryFields.skipVerify(name))  return;
			var matches = new Array();
                           jQuery(form.elements).each (function (testi, testitem) {
				// Checkbox test - must replace [0-9 ] with  []
				var chbxnametest = testitem.name.replace(/\[[0-9*]\]/g,"[]");
				if(testitem.name == name || "custom_"+testitem.name == name || testitem.id == name  || testitem.id.indexOf(name+"_")==0
					|| ("#"+testitem.id) == name  || $(testitem).hasClass(name.substr(1))
					|| (testitem.type=="checkbox" && "custom_"+chbxnametest == name && testitem.checked)
					){
					matches.push(testitem);
				}

			});
			var value = "";
			if(matches.length==1){
				value = matches[0].value;
			}
			// A set of radio checkboxes
			else if (matches.length>1){
				matches.each (function (match, index){
					if (match.checked) value = match.value;
				});
			}
			//if (elem) elem.value = item.value;
			if (value == item['default'] || value == ""){
				valid = false;
				// TODO add message together
				if(item.reqmsg!=""){
					messages.push(item.reqmsg);
				}
			}
		});
		if (!valid){
			message = "";
			messages.each (function (msg, index){message += msg+"\\n";});
			if (message==""){
				message = "$missingRequiredMessage";
			}
			alert(message);
		}
		return valid;
	}
}

jQuery(document).ready( function() {	
    var form =document.adminForm;
    if (form){	
            jQuery(form).on('submit',function(event){
                var task = jQuery(form.task).val();
                if ((task=="icalevent.apply" && task=="icalevent.save" && task=="icalevent.savenew") && !JevrRequiredFields.verify(form)) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                }
            });
    };
});


// category conditional fields
var JevrCategoryFields = {
	fields: [],
	cats: $cats,
	setup:function (){
		if (!jQuery('#catid').length) return;
		var catidel = jQuery('#catid');
		var catid = catidel.val();
                if (!catid){
                    catid = -1;
                }
		// These are the ancestors of this cat - javascript array
		this.fields.forEach(function (item,i) {
			var elem = jQuery(".jevplugin_customfield_"+item.name);
			if (item.name.indexOf(".")==0 || item.name.indexOf("#")==0) {
				elem = jQuery(item.name);
			}
			if (!elem.length) return;
			// This is the version that ignores parent category selections
			/*
			// only show it if the selected category is in the list
			if (item.catids.contains(catid)){
				if (window.ie) {
					elem.css("display", "");
				}
				else {
					elem.css("display","block");
				}
			}
			else {
				elem.css("display","none");
			}
			*/

			// hide the item by default
			elem.css('display',"none");

			if (catidel.attr("multiple")){
				jQuery('#catid option:selected').each(function(idx, opt) {
					if (opt.selected) {
						tempcatid = opt.value;
						var cats = this.cats[tempcatid];
						cats.each (function(cat,i){
							if (item.catids.contains(cat)){
								//alert("matched "+cat + " cf "+item.catids);
								if (window.ie) {
									elem.css("display","");
								}
								else {
									elem.css("display","block");
								}
                                                                jQuery(elem).trigger("chosen:updated");
                                                                // old style version - still needed!
                                                                jQuery(elem).trigger("liszt:updated");                        
							}
						});
					}
				}.bind(this));
			}
			else {
				var cats = this.cats[catid];
                                // javascript array
				cats.each (function(cat,i){
					if (item.catids.contains(cat)){
						//alert("matched "+cat + " cf "+item.catids);
                                                if (window.ie) {
                                                        elem.css("display","");
                                                }
                                                else {
                                                        elem.css("display","block");
                                                }
					}
				});
			}

		}.bind(this));
	},
	skipVerify:function (fieldname){
		if (!jQuery('#catid').length) return true;
		var catid = jQuery('#catid').val();
		var cats = JevrCategoryFields.cats[catid];
		var skip = false;
		this.fields.each(function (item,i) {
			if ('custom_'+item.name !== fieldname && ('custom_jform['+item.name+']') !== fieldname) return;
			skip = true;
			cats.each (function(cat,i){
				if (item.catids.contains(cat)){
					skip = false;
					return;
				}
			});
		});
		return skip;
	}
};
jQuery(window).on("load",function(){
	if (JevrCategoryFields){
		JevrCategoryFields.setup();
		if (!jQuery('#catid').length) return;
		/*
		// Chosen buggers up this event !
		jQuery('#catid').on('change',function(){
			JevrCategoryFields.setup();
		});
		*/
		jQuery('#catid').on('change', function(){JevrCategoryFields.setup();});
		if (!jQuery('#ics_id').length) return;
		jQuery('#ics_id').on('change',function(){
			setTimeout("JevrCategoryFields.setup()",500);
		});
	}
});
SCRIPT;
		$doc->addScriptDeclaration($script);

	}

	public function getFieldCountByFieldSet($fieldset)
	{
		$fields = $this->findFieldsByFieldset($fieldset);
		return $fields ? count($fields) : 0;

	}

	public function getFieldsetInfo($name)
	{
		// Initialise variables.
		$false = false;

		// Make sure there is a valid JForm XML document.
		if (!($this->xml instanceof SimpleXMLElement))
		{
			return $false;
		}

		/*
		 * Get an array of <field /> elements that are underneath a <fieldset /> element
		 * with the appropriate name attribute, and also any <field /> elements with
		 * the appropriate fieldset attribute.
		 */
		$fieldsets = $this->xml->xpath('//fieldset[@name="' . $name . '"]');
		if ($fieldsets)
		{
			return $fieldsets[0];
		}
		return false;

	}

	public function getFieldsetsBasic()
	{
		$sets = array();
		$fieldsets = array();

		// Make sure there is a valid JForm XML document.
		if (!($this->xml instanceof SimpleXMLElement))
		{
			return $sets;
		}

		// Get an array of <fieldset /> elements and fieldset attributes.
		$sets = $this->xml->xpath('//fieldset[@name] | //field[@fieldset]/@fieldset');
		// Process each found fieldset.
		foreach ($sets as $set)
		{
			// Are we dealing with a fieldset element?
			if ((string) $set['name'])
			{
				$fieldsets[(string) $set['name']] = $set;
			}
		}

		return $fieldsets;
	}

	public function renderToBasicArray($name = 'params', $group = 'default')
	{

		$fieldsetData = $this->getFieldsetInfo($group);
		if (!$fieldsetData)
		{
			return false;
		}

		$results = array();
		$params = $this->getFieldset($group);

		if (!$params)
		{
			return false;
		}

		$doc = JFactory::getDocument();

		foreach ($params as $p => $node)
		{
			// attach the event to the $node
			$node->event = $this->event;

			if ($node->attribute('categoryrestrictions'))
			{
				$cats = explode(",", $node->attribute('categoryrestrictions'));
				JArrayHelper::toInteger($cats);

				if (isset($this->event) && is_a($this->event, "jIcalEventRepeat" ) && !in_array($this->event->catid(), $cats))
					continue;
			}
			$result = array();
			$result['fieldtype'] = $node->attribute('type');
                        /*
			if (strpos($result['fieldtype'], "jevcf") === 0)
			{
				$result['fieldtype'] = "jevcf" . substr($result['fieldtype'], 4);
			}
                         */

			//$result['value'] = $this->get($node->name, $this->attribute('default'), $group);
			$result['value'] = $node->value;
			$result['defaultvalue'] = $node->attribute('default');

			// Raw value that can be used for styling
			$result['rawvalue'] = $result['value'];

			if (method_exists($node, "convertValue")) {
				$result['value'] = $node->convertValue($result['value'], $node);
			}

			$result["fieldnamearray"] = is_callable(array($node, "fieldNameArray")) ? $node->fieldNameArray() : array();

			// reset the type - just in case a special type has changed the node attributes
			$result['type'] = $node->type;
                        /*
			if (strpos($result['type'], "jevcf") === 0)
			{
				$result['type'] = "jevcf" . substr($result['type'], 4);
			}
                         */

			$result['name'] = $node->attribute('name');
			$result['label'] = $node->attribute('label');
			$translatedLabel = JText::_($result['label']);
			if( $result['label'] !== $translatedLabel )
			{
				$result['label'] = $translatedLabel;
			}
			$result['access'] = ($node->attribute('access') != null) ? $node->attribute('access') : 1;
			// Badly configured access in XML file
			if ($result['access'] == 0 ){
				$result['access'] = 1;
			}
			$result['readaccess'] = $node->attribute('readaccess');
			$result['hiddenvalue'] = $node->attribute('hiddenvalue');
			$result['userid'] = $node->attribute('userid');
			$result['allowhtml'] = $node->attribute('allowhtml');
			$result['allowraw'] = $node->attribute('allowraw');
			$result['group'] = $group;

			$result['separaterepeats'] = ($node->attribute('separaterepeats') != null) ? (int) $node->attribute('separaterepeats') : 0;

			// if field is for attendees only then hide if necessary
			if ($node->attribute('attendeesonly') == 1)
			{
				$this->hideFromNonAttendees($result, $node);
			}

			$results[$result['name']] = $result;
		}
		return $results;

	}

	private function hideFromNonAttendees(&$result, $element)
	{
		// TODO find a more efficient way to do this - it currently generates  LOT of queries for list views!
		$user = JFactory::getUser();
		if ($this->hasrsvp && isset($this->event) && $user->id > 0)
		{			
			$db = JFactory::getDbo();
			$eventid = $this->event->ev_id();
			// combined query
			$sql = "SELECT atdees.* FROM #__jev_attendees as atdees ";
			$sql .= " LEFT JOIN #__jev_attendance as atd ON atd.id = atdees.at_id AND atd.ev_id=" . $eventid;
			$sql .= " WHERE atdees.user_id=" . $user->id;
			$sql .= " AND ( (atd.allrepeats=0 AND atdees.rp_id=" . $this->event->rp_id(). ") OR atd.allrepeats=1)";
			$db->setQuery($sql);
			$attendee = $db->loadObject();

			if ($attendee && $attendee->attendstate == 1)
			{
				return true;
			}
			
		}
		// don't hide if editing layout in backend
		if ( !JFactory::getApplication()->isAdmin() || JRequest::getCmd("task")!="defaults.edit"){
			$result["value"] = $result['hiddenvalue'];
			$result["label"]="";
		}
	}
        
	public function constructFilters()
	{
                $registry = JRegistry::getInstance("jevents");
                $menuModuleFilters = $registry->get("jevents.menuModuleFilters",false);
                $mapPrefix = $registry->get("jevents.customFieldMapPrefix","");

		$this->filterElements = array();

		// Slimmer method since we don't need the elements
		$groups = $this->getFieldsetsBasic();

		foreach ($groups as $group => $elementNotNeeded)
		{
			//$groupparams = $this->getFieldset($group);
			// use slimmer RAW method
			$groupparams = $this->findFieldsByFieldset($group);
			if (count($groupparams) == 0)
			{
				continue;
			}
			foreach ($groupparams as $p => $node)
			{

				if (! (integer) $node["filter"] && ! (integer) $node["filtermenusandmodules"]) {
					continue;
				}
                                if (is_array($menuModuleFilters) && count($menuModuleFilters) &&  !in_array((string) $node["name"], $menuModuleFilters)){
                                    continue;
                                }
				// Convert to field object only if needed!
				$attrs = $node->xpath('ancestor::fields[@name]/@name');
				$groups = array_map('strval', $attrs ? $attrs : array());
				$group = implode('.', $groups);

				// If the field is successfully loaded add it to the result array.
				if ($field = $this->loadField($node, $group))
				{
					// Must be a new one
					if (method_exists($field, "constructFilter"))
						$field->constructFilter($field);
                                        if (isset($field->map)) {
                                            $field->map = $mapPrefix.$field->map;
                                        }
					$this->filterElements[] = $field;
				}
			}
		}

		return;

	}

	public function createFilters()
	{
		$results = array();
		$datamodel = new JEventsDataModel();
		$datamodel->setupComponentCatids();
		$accessibleCategoryList = $datamodel->accessibleCategoryList();

		foreach ($this->filterElements as $element)
		{
			if (method_exists($element, "createFilter"))
			{
				// Check if category is accessible first
				$catrestrictions = $element->attribute('categoryrestrictions');
				if ($catrestrictions && $element->attribute('hidefilterbasedoncategory'))
				{
					// if not selected a category then just continue - do not display this custom field filter
					if (count($datamodel->catids)==0 || JRequest::getInt("category_fv",-1)==0  || JRequest::getInt("catids",-1)==0 ){
						continue;
					}
					$catrestrictions = explode(",",$catrestrictions);
					// if not selected a category that is relevant then skip the filter
					if (count(array_intersect($catrestrictions, $datamodel->catids))==0){
						continue;
					}
					$result = $element->createFilter();
					if ($result)
						$results[] = $result;
				}
				else {
					$result = $element->createFilter();
					if ($result)
						$results[] = $result;
				}
			}
		}
		return implode(" AND ", $results);

	}

	public function createJoinFilters()
	{
		$results = array();
		$datamodel = new JEventsDataModel();
		$datamodel->setupComponentCatids();
		$accessibleCategoryList = $datamodel->accessibleCategoryList();

		foreach ($this->filterElements as $element)
		{
			if (method_exists($element, "createJoinFilter"))
			{
				// Check if category is accessible first
				$catrestrictions = $element->attribute('categoryrestrictions');
				if ($catrestrictions && $element->attribute('hidefilterbasedoncategory'))
				{
					// if not selected a category then just continue - do not display this custom field filter
					if (count($datamodel->catids)==0 || JRequest::getInt("category_fv",-1)==0  || JRequest::getInt("catids",-1)==0 ){
						continue;
					}
					$catrestrictions = explode(",",$catrestrictions);
					// if not selected a category that is relevant then skip the filter
					if (count(array_intersect($catrestrictions, $datamodel->catids))==0){
						continue;
					}
					$result = $element->createJoinFilter();
					if ($result)
						$results[] = $result;
				}
				else {
					$result = $element->createJoinFilter();
					if ($result)
						$results[] = $result;
				}
			}
		}
		return implode(" LEFT JOIN ", $results);

	}

	public function setSearchKeywords(& $extrajoin)
	{
		$results = array();
		foreach ($this->filterElements as $element)
		{
			if (method_exists($element, "setSearchKeywords"))
			{
				$result = $element->setSearchKeywords($extrajoin);
				if ($result)
					$results[] = $result;
			}
		}
		return $results;

	}

	public function createFiltersHTML()
	{
		$results = array();
		$results["merge"] = array();
		$datamodel = new JEventsDataModel();
		$datamodel->setupComponentCatids();
		$accessibleCategoryList = $datamodel->accessibleCategoryList();
		foreach ($this->filterElements as $element)
		{
			if (method_exists($element, "createFilterHTML")) {
				// only offer filter HTML for fields with filtering enabled
				if (!$element->attribute("filter")){
					continue;
				}

				// Check if category is accessible first
				$catrestrictions = $element->attribute('categoryrestrictions');
				if ($catrestrictions && $element->attribute('hidefilterbasedoncategory'))
				{
					// if not selected a category then just continue - do not display this custom field filter
					if (count($datamodel->catids)==0 || JRequest::getInt("category_fv",-1)==0  || JRequest::getInt("catids",-1)==0 ){
						continue;
					}
					$catrestrictions = explode(",",$catrestrictions);
					// if not selected a category that is relevant then skip the filter
					if (count(array_intersect($catrestrictions, $datamodel->catids))==0){
						continue;
					}
					$results["merge"][] = $element->createFilterHTML();
				}
				else {
					$results["merge"][] = $element->createFilterHTML();
				}
			}
		}
		return $results;

	}

	/* overloaded methods */

	/**
	 * Method to load the form description from an XML file.
	 *
	 * The reset option works on a group basis. If the XML file references
	 * groups that have already been created they will be replaced with the
	 * fields in the new XML file unless the $reset parameter has been set
	 * to false.
	 *
	 * @param   string  $file   The filesystem path of an XML file.
	 * @param   string  $reset  Flag to toggle whether form fields should be replaced if a field
	 *                          already exists with the same group/name.
	 * @param   string  $xpath  An optional xpath to search for the fields.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * CUSTOM VERSION - if we have an old xml file converts to new version
	 *
	 * @since   11.1
	 */
	public function loadFile($file, $reset = true, $xpath = false)
	{
		// Check to see if the path is an absolute path.
		if (!is_file($file))
		{

			// Not an absolute path so let's attempt to find one using JPath.
			$file = JPath::find(self::addFormPath(), strtolower($file) . '.xml');

			// If unable to find the file return false.
			if (!$file)
			{
				return false;
			}
		}
		// Attempt to load the XML file.
		$xml = JFactory::getXML($file, true);

                if (!$xml) {
                    $message = JText::sprintf("JEV_COULD_NOT_LOAD_XML_FILE", $file);
                    JFactory::getApplication()->enqueueMessage($message,"error");
                    $xml = JFactory::getXML('<?xml version="1.0" encoding="utf-8"?><form><fields><fieldset/></fields></form>', false);
                }
                
                if ($xml && $xml->getName() == "config")
		{
			$message = JText::sprintf("JEV_CUSTOM_FIELD_CONFIG_FILE_REGENERATED", basename($file), str_replace(".xml","_jform.xml",basename($file)));
			// This is an important message - must show it even if the language file is out of date
			if ($message == "JEV_CUSTOM_FIELD_CONFIG_FILE_REGENERATED"){
				$message = sprintf("Custom fields config file %1s updated to %2s - please update your plugin/component parameters",  basename($file), str_replace(".xml","_jform.xml",basename($file)));
			}
			JFactory::getApplication()->enqueueMessage($message,"warning");

			// rework the XML file
			//  **** include name for default group if not set ****
			if (!$xml->params [0]->attributes("group"))
			{
				$xml->params [0]->addAttribute("group", "default");
			}

			// Eliminate fields with . in their names => replace with underscore e.g. field2.5
			$namechanges = array();
			$paramcount = phpversion() >= '5.3.0' ? $xml->params->count() : count($xml->params->children() );
			for ($p = 0; $p < $paramcount; $p++)
			{
				$pcount = isset($xml->params[$p]) ? (phpversion() >= '5.3.0' ? $xml->params[$p]->count() : count($xml->params[$p]->children())) : 0;
				for ($i = 0; $i < $pcount; $i++)
				{
					if (strpos($xml->params[$p]->param[$i]->attributes()->name, ".") !== false || strpos($xml->params[$p]->param[$i]->attributes()->name, " ") !== false)
					{
						$oldname =(string) $xml->params[$p]->param[$i]->attributes()->name;
						$newname = str_replace(array(".", " "), "_", $xml->params[$p]->param[$i]->attributes()->name);
						JFactory::getApplication()->enqueueMessage(JText::sprintf("JEV_CUSTOM_FIELD_RENAMED_AND_DATA_COPIED", $oldname,$newname));
						$xml->params[$p]->param[$i]->attributes()->name = $newname;
						$namechanges[$oldname]=$newname;
					}
				}
			}

			$tempXml = $xml->asXML();
			// do something to convert old xml files to new JForm versions
			// config -> fields ,  wrap fields in form
			$tempXml = str_replace("<config>", "<form><fields>", $tempXml);
			$tempXml = str_replace("</config>", "</fields></form>", $tempXml);

			// params- > fieldset, group -> name , addpath -> addfieldpath and
			$tempXml = str_replace("<params", "<fieldset", $tempXml);
			$tempXml = str_replace("params>", "fieldset>", $tempXml);
			$tempXml = str_replace(" group=", " name=", $tempXml);
			$tempXml = str_replace("addpath=", "addfieldpath=", $tempXml);

			// param => field
			$tempXml = str_replace("<param ", "<field ", $tempXml);
			$tempXml = str_replace("param>", "field>", $tempXml);

			// jevcf -> jevcf throughout
			$tempXml = str_replace("type='jevcf", "type='jevcf", $tempXml);
			$tempXml = str_replace('type="jevcf', 'type="jevcf', $tempXml);

//			$xml = simplexml_load_string($tempXml, 'JXMLElement');

			$xmlfile = str_replace(".xml","_jform.xml",$file);
			jimport("joomla.filesystem.file");
			if (!JFile::exists($xmlfile)){
				JFile::write($xmlfile,$tempXml);
				// Also map the data
				$db = JFactory::getDbo();
				foreach ($namechanges as $oldname => $newname){
					$db->setQuery("UPDATE #__jev_customfields set name=".$db->quote($newname). " WHERE name=".$db->quote($oldname));
					$db->query();
				}
			}
			// reload the XML file
			$xml = JFactory::getXML($tempXml, false);
		}
		return $this->load($xml, $reset, $xpath);

	}

	/**
	 * Method to get an instance of a form.
	 *
	 * @param   string  $name     The name of the form.
	 * @param   string  $data     The name of an XML file or string to load as the form definition.
	 * @param   array   $options  An array of form options.
	 * @param   string  $replace  Flag to toggle whether form fields should be replaced if a field
	 * already exists with the same group/name.
	 * @param   string  $xpath    An optional xpath to search for the fields.
	 *
	 * @return  object  JForm instance.
	 *
	 * CUSTOM VERSION - gets instance of this class instead of JForm
	 *
	 * @since   11.1
	 * @throws  Exception if an error occurs.
	 */
	public static function getInstance($name, $data = null, $options = array(), $replace = true, $xpath = false)
	{
		// Reference to array with form instances
		$forms = &self::$forms;

		// Only instantiate the form if it does not already exist.
		if (!isset($forms[$name]))
		{

			$data = trim($data);

			if (empty($data))
			{
				throw new Exception(JText::_('JLIB_FORM_ERROR_NO_DATA'));
			}

			// Instantiate the form.
			$forms[$name] = new JevCfForm($name, $options);

			// Load the data.
			if (substr(trim($data), 0, 1) == '<')
			{
				if ($forms[$name]->load($data, $replace, $xpath) == false)
				{
					throw new Exception(JText::_('JLIB_FORM_ERROR_XML_FILE_DID_NOT_LOAD'));

					return false;
				}
			}
			else
			{
				if ($forms[$name]->loadFile($data, $replace, $xpath) == false)
				{
					throw new Exception(JText::_('JLIB_FORM_ERROR_XML_FILE_DID_NOT_LOAD'));

					return false;
				}
			}
		}

		// make sure jevents custom fields path is included!
		$path = JPATH_ROOT . '/plugins/jevents/jevcustomfields/customfields/';
		self::addFieldPath($path);
		$path = JPATH_ROOT . '/plugins/jevents/jevcustomfields/clientspecific/';
		self::addFieldPath($path);

		return $forms[$name];

	}

	/**
	 * Deep clone !!!
	 */
	public function __clone()
	{
		foreach ($this as $key => $val)
		{
			if (is_array($val))
			{
				try {
					$this->{$key} = unserialize(serialize($val));
				}
				catch (Exception $exc) {
					// we can't serialise some elements of the array e.g. JXMLElement
					//echo $exc->getTraceAsString();
					//$this->{$key} = clone $val;
				}
			}
			else if (is_object($val))
			{
				$this->$key= clone($val);
			}
		}

	}

	public function convertTemplateToXML($template)
	{
		$xml = array();
		$xml[] = '<?xml version="1.0" encoding="utf-8"?>';
		$xml[] = '<form>';
		$xml[] = '<description>';
		$xml[] = '<![CDATA[';
		$xml[] = $template->description;
		$xml[] = ']]>';
		$xml[] = '</description>';
		$xml[] = '<fields>';
		$xml[] = '<fieldset addfieldpath="/plugins/jevents/jevcustomfields/customfields/" />';
		$xml[] = '<fieldset addfieldpath="/plugins/jevents/jevcustomfields/clientspecific/" />';
                for ($i=0; $i<count($template->fieldsetnames);$i++){                    
                    if (isset($template->fieldsets[$template->fieldsetnames[$i]]) && 
                        count($template->fieldsets[$template->fieldsetnames[$i]]->fields) >0 )
                    {
                        if ($template->fieldsetnames[$i]=="Default"){
                            $template->fieldsetnames[$i]="default";
                        }
                        $xmlrow = '<fieldset name="'.$template->fieldsetnames[$i].'"  ';
                        $xmlrow .= ">\n<description>";
                        $xmlrow .= '<![CDATA[';
                        //$xmlrow .= ' description="';
                        $xmlrow .= isset($template->fieldsetdescriptions[$i])?$template->fieldsetdescriptions[$i]:"";
                        $xmlrow .= ']]>';
                        $xmlrow .= "</description>";
                        //$xmlrow .= '">';                    
                        $xml[] = $xmlrow;

                        foreach ($template->fieldsets[$template->fieldsetnames[$i]]->fields as $field)
                        {
                            $element =  $this->loadFieldType($field->type);
                            $xml[] = $element->toXml($field);
                        }
    
                        $xml[] = '</fieldset>';
                    }

                }
                /*
		$xml[] = '<fieldset name="default" >';
		foreach ($template->fields as $field)
		{
			$element =  $this->loadFieldType($field->type);
			$xml[] = $element->toXml($field);
		}
                */
		$xml[] = '</fields>';

		$xml[] = '</form>';

		return implode("\n", $xml);

	}

}
