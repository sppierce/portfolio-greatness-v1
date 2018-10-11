<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JLoader::register('jevFilterProcessing',JPATH_SITE."/components/com_jevents/libraries/filters.php");
JLoader::register('JevJoomlaVersion',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/version.php");

class plgJEventsJevusers extends JPlugin
{
	var $_dbvalid = 0;
	public $params = null;
	private $jomsocial = false;
	private $cb = false;
	private $groupjive = false;

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$lang 		= JFactory::getLanguage();
		$lang->load("plg_jevents_jevusers", JPATH_ADMINISTRATOR);
		$lang->load("plg_jevents_jevusers", JPATH_SITE);

		// get plugin params
		$plugin = JPluginHelper::getPlugin('jevents', 'jevusers');
		if (!$plugin) return;
		$this->params = new JRegistry($plugin->params);

		jimport('joomla.filesystem.folder');
		if (JFolder::exists(JPATH_SITE.'/components/com_community')){
			if (JComponentHelper::isEnabled("com_community")) {
				$this->jomsocial = true;
			}
		}

		if (JFolder::exists(JPATH_SITE.'/components/com_comprofiler') && JComponentHelper::isEnabled("com_comprofiler")){
			$this->cb = true;
			jimport('joomla.filesystem.file');
			if (JFile::exists(JPATH_SITE."/components/com_comprofiler/plugin/user/plug_cbgroupjive/cbgroupjive.php") ){
                                $gjplugin = JPluginHelper::getPlugin('user', 'cbgroupjive');
				$this->groupjive = true;
			}
		}
	}


	// This enables the plugin to control the type of event published
	public function isEventPublisher($type, &$isEventPublisher){

		$jinput = JFactory::getApplication()->input;

		// type is strict then do not change status
		if ( $type=="strict") return true;

		if (!(int) $this->params->get("enableprivate",0) && !(int)$this->params->get("enablehidden",0)) return true;

		//$jevtask = JRequest::getString("jevtask","");
		//if ($jevtask!="icalevent.save") return true;

		$jevuser = $jinput->getInt("custom_jevuser",0);
		// if  jevuser mode is "public" event then user may not be authorised to publish
		// otherwise pass through

		if ($jevuser>0) return true;

		if (!(int)$this->params->get("override_publish_permission",0)) return true;

		$user = JFactory::getUser();
		// leave anon users to its own plugin
		if ($user->id==0) return true;

		$globalPublisher = JEVHelper::isEventPublisher(true);
		// This is a public event that this user cannot publish!
		$isEventPublisher = $globalPublisher?true:false;
		return true;

	}

	/**
	 * When editing a JEvents menu item can add additional menu constraints dynamically
	 *
	 */
	public function onEditMenuItem(&$menudata, $value, $control_name,$name, $id, $param)
	{
		if (!(int)$this->params->get("enableprivate",0) && !(int)$this->params->get("enablehidden",0)){
	//		return;
		}

		// already done this param
		if (isset($menudata[$id])) return;

		static $matchingextra = null;
		// find the parameter that matches jevu: (if any)
		if (!isset($matchingextra)){
                        $params = $param->getGroup('params');
                        foreach ($params as $key => $element){
                                $val = $element->value;
                                if (strpos($key,"jform_params_extras")===0 ){
                                        if (strpos($val,"jevu:")===0){
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
		if (strpos($value,"jevu:")===0 || (($value==""||$value=="0") && $matchingextra===false)){
			$matchingextra = true;
			$invalue = str_replace(" ","",$value);
			if ($invalue =="") $invalue =  'jevu:0';

			$options = array();
			$options[] = JHTML::_('select.option', 'jevu:0', JText::_('JEVU_All_Events'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevu:1', JText::_('JEVU_Public_Events'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevu:2', JText::_('JEVU_Private_Events'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevu:3', JText::_('JEV_DETAILS_PRIVATE'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevu:6', JText::_('JEVU_LOGGED_IN_USER_EVENTS'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevu:4', JText::_('JOMSOCIAL_GROUP_EVENT'), 'id', 'title');
			//			$options[] = JHTML::_('select.option', 'jevu:5', JText::_('GROUPJIVE_GROUP_EVENT'), 'id', 'title');
			$options[] = JHTML::_('select.option', 'jevu:7', JText::_('JEVU_JOMSOCIAL_PROFILE_USER_EVENTS'), 'id', 'title');

                        if ($control_name=="params"){
                                // for CB
                                $input = JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', '', 'id', 'title', $invalue, $control_name.$name );
                        }
                        else {
                                $input = JHTML::_('select.genericlist',  $options, $name, '', 'id', 'title', $invalue, $control_name.$name );
                        }
                        $input .= '<div style="clear:left"></div>';

			$data = new stdClass();
			$data->name = "jevuser";
			$data->html = $input;
			$data->label = "JEVU_USER";
			$data->description = "JEVU_USER_DESC";
			$data->options = array();
			$menudata[$id] = $data;
			return;
		}

		static $matchingextra2 = null;
		// find the parameter that matches jevsu: (if any)
		if (!isset($matchingextra2)){
			$params = $param->getGroup('params');
			foreach ($params as $key => $element){
				$val = $element->value;
				if (strpos($key,"jform_params_extras")===0 ){
					if (strpos($val,"jevsu:")===0){
						$matchingextra2 = $key;
						break;
					}
				}
			}
			if (!isset($matchingextra2)){
				$matchingextra2 = false;
			}
		}

		// either we found matching extra and this is the correct id or we didn't find matching extra and the value is blank
		if (strpos($value,"jevsu:")===0 || (($value==""||$value=="0") && $matchingextra2===false)){
			$matchingextra2 = true;
			$invalue = trim($value);
			if ($invalue =="") $invalue =  'jevsu:';
			$value = str_replace("jevsu:","",$invalue);

			$input = '<input type="text"  name="jevsu"  value="'.$value.'" onchange="$(\'jevsu\').value=\'jevsu:\'+this.value;" />';
			$input .= '<input type="hidden"  name="'.$name.'"  id="jevsu" value="'.$invalue.'" />';

			$data = new stdClass();
			$data->name = "jevspecificuser";
			$data->html = $input;
			$data->label = "JEV_SPECIFIC_CREATORS";
			$data->description = "JEV_SPECIFIC_CREATORS_DESC";
			$data->options = array();
			$menudata[$id] = $data;
		}

	}

	public function onListIcalEvents( & $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroupdby=false)
	{
		$mainframe = JFactory::getApplication();

		$jinput = JFactory::getApplication()->input;

		if($mainframe->isAdmin()) {
			return;
		}
		// find what is running - used by the filters
		$registry	= JRegistry::getInstance("jevents");
		$activeprocess = $registry->get("jevents.activeprocess","");
		$moduleid = $registry->get("jevents.moduleid", 0);
		$moduleparams = $registry->get("jevents.moduleparams", false);

		// default is to only show all events
		$extraval = "";
		if ($activeprocess=="component" || $activeprocess=="admin"){
			// What type of events have we specified for the menu item
			$compparams = JComponentHelper::getParams("com_jevents");
			for ($extra = 0;$extra<20;$extra++){
				$extraval = $compparams->get("extras".$extra, false);
				if (strpos($extraval,"jevu:")===0){
					break;
				}
			}
		}
		else if ($activeprocess=="mod_jevents_cal" || $activeprocess=="mod_jevents_latest"){
			for ($extra = 0;$extra<20;$extra++){
				$extraval = $moduleparams->get("extras".$extra, false);
				if (strpos($extraval,"jevu:")===0){
					break;
				}
			}
		}

		if ((int)$this->params->get("enableprivate",0) || (int)$this->params->get("enablehidden",0)){
			if ($extraval=="jevu:1"){
				// show public so strip all private events
				$filters = jevFilterProcessing::getInstance(array("activeuser","userssearch","creator","privateevents","noprivateevents"),JPATH_SITE."/plugins/jevents/jevusers/filters",false,$moduleid);
			}
			else if ($extraval=="jevu:2"){
				// show private so strip all private events
				$filters = jevFilterProcessing::getInstance(array("activeuser","userssearch","creator","privateevents","nopublicevents"),JPATH_SITE."/plugins/jevents/jevusers/filters",false,$moduleid);
			}
			else if ($extraval=="jevu:4"){
				// show only group events
				// show private so strip all private events
				$filters = jevFilterProcessing::getInstance(array("activeuser","privateevents","jsgroupevents"),JPATH_SITE."/plugins/jevents/jevusers/filters",false,$moduleid);
			}
			else if ($extraval=="jevu:6"){
				// show only events of the logged in user
				$user = JFactory::getUser();
				$jinput->set("jevu_fv",$user->id);
				$filters = jevFilterProcessing::getInstance(array("userssearch"), JPATH_SITE."/plugins/jevents/jevusers/filters", false, $moduleid);
			}
			else if ($extraval=="jevu:7"){
				// show only events of the user being profiled
				if ($jinput->getCmd("option")=="com_community" && $jinput->getCmd("view")=="profile" && $jinput->getInt("userid")>0) {
					$user = JFactory::getUser($jinput->getInt("userid"));
					$jinput->set("jevu_fv",$user->id);
					$filters = jevFilterProcessing::getInstance(array("userssearch"), JPATH_SITE."/plugins/jevents/jevusers/filters", false, $moduleid);
				}
				// show only events of the users frontpage
				else  if ($jinput->getCmd("option")=="com_community" && $jinput->getCmd("view")=="frontpage" ) {
					$user = JFactory::getUser();
					if ($user->id>0){
						$jinput->set("jevu_fv",$user->id);
					}
					else {
						$jinput->set("jevu_fv",-999);
					}
					$filters = jevFilterProcessing::getInstance(array("userssearch"), JPATH_SITE."/plugins/jevents/jevusers/filters", false, $moduleid);
				}
				else {
					$jinput->set("jevu_fv",-999);
					$filters = jevFilterProcessing::getInstance(array("userssearch"), JPATH_SITE."/plugins/jevents/jevusers/filters", false, $moduleid);
				}
			}
			else {
				// show all so strip all private events	the user is not authorised to see
				$filters = jevFilterProcessing::getInstance(array("activeuser","creator","userssearch","privateevents"), JPATH_SITE."/plugins/jevents/jevusers/filters");
			}
		}
		else {
			if ($extraval=="jevu:6"){
				// show only events of the logged in user
				$user = JFactory::getUser();
				$jinput->set("jevu_fv", $user->id);
				$filters = jevFilterProcessing::getInstance(array("userssearch"), JPATH_SITE."/plugins/jevents/jevusers/filters", false, $moduleid);
			}
			else if ($extraval=="jevu:7"){
				// show only events of the user being profiled
				if ($jinput->getCmd("option")=="com_community" && $jinput->getCmd("view")=="profile" && $jinput->getInt("userid") > 0) {
					$user = JFactory::getUser($jinput->getInt("userid"));
					$jinput->set("jevu_fv",$user->id);
					$filters = jevFilterProcessing::getInstance(array("userssearch"), JPATH_SITE."/plugins/jevents/jevusers/filters", false, $moduleid);
				}
				// show only events of the users frontpage
				else  if ($jinput->getCmd("option")=="com_community" && $jinput->getCmd("view")=="frontpage" ) {
					$user = JFactory::getUser();
					if ($user->id>0){
						$jinput->set("jevu_fv", $user->id);
					}
					else {
						$jinput->set("jevu_fv", -999);
					}
					$filters = jevFilterProcessing::getInstance(array("userssearch"), JPATH_SITE."/plugins/jevents/jevusers/filters", false, $moduleid);
				}
				else {
					$jinput->set("jevu_fv", -999);
					$filters = jevFilterProcessing::getInstance(array("userssearch"), JPATH_SITE."/plugins/jevents/jevusers/filters", false, $moduleid);
				}
			}
			else {
				$filters = jevFilterProcessing::getInstance(array("activeuser","creator","userssearch"), JPATH_SITE."/plugins/jevents/jevusers/filters");
			}
		}

		// list of specific users
		$extraval = false;
		if ($activeprocess=="component" || $activeprocess=="admin"){
			// What type of events have we specified for the menu item
			$compparams = JComponentHelper::getParams("com_jevents");
			for ($extra = 0;$extra<20;$extra++){
				$extraval = $compparams->get("extras".$extra, false);
				if (strpos($extraval,"jevsu:")===0){
					break;
				}
			}
		}
		else if ($activeprocess=="mod_jevents_cal" || $activeprocess=="mod_jevents_latest"){
			for ($extra = 0;$extra<20;$extra++){
				$extraval = $moduleparams->get("extras".$extra, false);
				if (strpos($extraval,"jevsu:")===0){
					break;
				}
			}
		}
		
		if ($extraval && strpos($extraval,"jevsu:")===0)
		{

			$invalue = str_replace("jevsu:", "", $extraval);
			$invalue = str_replace(" ", "", $invalue);
			if (substr($invalue, strlen($invalue) - 1) == ",")
			{
				$invalue = substr($invalue, 0, strlen($invalue) - 1);
			}
			if ($invalue!=""){
				$invalue = explode(",", $invalue);
				JArrayHelper::toInteger($invalue);

				$extrawhere[] = "ev.created_by IN (" . implode(",", $invalue) . ")";
			}
		}

		$filters->setWhereJoin($extrawhere,$extrajoin);
		if (!$needsgroupdby) $needsgroupdby=$filters->needsGroupBy();

		//reset the title and description!!
		if ((int)$this->params->get("enablehidden",0)){

			$joinedUsers = false;
			foreach ($extrajoin as $join){
				if (strpos($join, "jev_usereventsmap as jum")>0){
					$joinedUsers = true;
					break;;
				}
			}
			if (!$joinedUsers){
				$extrajoin[] = "#__jev_usereventsmap as jum ON jum.evdet_id=det.evdet_id";
			}
			$extrafields .= ", jum.privateevent";

			// event editors can see the details of course!
			if (JEVHelper::isEventEditor()) return true;

			$user = JFactory::getUser();
			$db = JFactory::getDBO();
			if (!(int)$this->params->get("hidedetailonly",0)){
				$extrafields .=" , CASE WHEN (jum.privateevent=3 AND jum.user_id!=".(int)$user->id.") THEN ".$db->Quote($this->params->get("hiddentitle",JText::_("JEV_HIDDEN_EVENT_TITLE_DEFAULT")))." ELSE summary END AS summary ";
			}
			$extrafields .=" , CASE WHEN (jum.privateevent=3 AND jum.user_id!=".(int)$user->id.") THEN ".$db->Quote($this->params->get("hiddendesc",JText::_("JEV_HIDDEN_EVENT_DESCRIPTION_DEFAULT")))." ELSE det.description END AS description ";
		}
		else if ((int)$this->params->get("enableprivate",0) ){
			$joinedUsers = false;
			foreach ($extrajoin as $join){
				if (strpos($join, "jev_usereventsmap as jum")>0){
					$joinedUsers = true;
					break;;
				}
			}
			if (!$joinedUsers){
				$extrajoin[] = "#__jev_usereventsmap as jum ON jum.evdet_id=det.evdet_id";
			}
			$extrafields .= ", jum.privateevent";
		}

		if ($jinput->getInt("jevge_fv")>0 && $extraval!="jevu:4" && $activeprocess=="component"){
			$joinedGroups = false;
			foreach ($extrajoin as $join){
				if (strpos($join, "community_groups_members as jcgm")>0){
					$joinedGroups = true;
					break;;
				}
			}
			if (!$joinedGroups){
				$filters = jevFilterProcessing::getInstance(array("activeuser","privateevents","jsgroupevents"),JPATH_SITE."/plugins/jevents/jevusers/filters",false,$moduleid);
			}
			else {
				$filters = jevFilterProcessing::getInstance(array("jsgroupevents"),JPATH_SITE."/plugins/jevents/jevusers/filters",false,$moduleid);
			}
			$filters->setWhereJoin($extrawhere,$extrajoin);
		}


		return true;
	}

	public function onListEventsById( & $extrafields, & $extratables, & $extrawhere, & $extrajoin)
	{
		$mainframe = JFactory::getApplication();

		if($mainframe->isAdmin()) {
			return;
		}

		//$filters = jevFilterProcessing::getInstance(array("userssearch"),$pluginsDir.DS."filters".DS);
		// use computed path so that joomla doesn't choke on soft links!
		if ((int)$this->params->get("enableprivate",0) || (int)$this->params->get("enablehidden",0)){
			$filters = jevFilterProcessing::getInstance(array("userssearch","creator","privateevents"),JPATH_SITE."/plugins/jevents/jevusers/filters");

		}
		else {
			$filters = jevFilterProcessing::getInstance(array("userssearch","creator"),JPATH_SITE."/plugins/jevents/jevusers/filters");
		}

		$filters->setWhereJoin($extrawhere,$extrajoin);                
                
		//reset the title and description!!
		if ((int)$this->params->get("enablehidden",0)){
			// event editors can see the details of course!
			if (JEVHelper::isEventEditor()) return true;
			$user = JFactory::getUser();
			$db = JFactory::getDBO();
			if (!(int)$this->params->get("hidedetailonly",0)){
				$extrafields .=" , CASE WHEN (jum.privateevent=3 AND jum.user_id!=".(int)$user->id.") THEN ".$db->Quote($this->params->get("hiddentitle",JText::_("JEV_HIDDEN_EVENT_TITLE_DEFAULT")))." ELSE summary END AS summary ";
			}
			$extrafields .=" , CASE WHEN (jum.privateevent=3 AND jum.user_id!=".(int)$user->id.") THEN ".$db->Quote($this->params->get("hiddendesc",JText::_("JEV_HIDDEN_EVENT_DESCRIPTION_DEFAULT")))." ELSE det.description END AS description ";
			$extrafields .=" , jum.privateevent";
                        
			$joinedUsers = false;
			foreach ($extrajoin as $join){
				if (strpos($join, "jev_usereventsmap as jum")>0){
					$joinedUsers = true;
					break;;
				}
			}
			if (!$joinedUsers){
				$extrajoin[] = "#__jev_usereventsmap as jum ON jum.evdet_id=det.evdet_id";
			}
                        
		}

		return true;
	}

	public function onEditCustom( &$row, &$customfields )
	{

		$mainframe = JFactory::getApplication();

		if (!(int)$this->params->get("enableprivate",0) && !(int)$this->params->get("enablehidden",0)) return "";

		$user = JFactory::getUser();
		// redundant in this situation
		if ((int)$this->params->get("forcestate",0) && !JEVHelper::isAdminUser($user)){
			return;
		}

		// get the data from database and attach to row
		$detailid = (int) $row->evdet_id();
		$user = JFactory::getUser();
		if ($user->id==0) return true;

		// if user can set the created_by field then they should still be allowed to edit access
		// If user is jevents can deleteall or has backend access then allow them to specify the creator
		$jevuser	= JEVHelper::getAuthorisedUser();
		$user = JFactory::getUser();

		$access = false;
		if ($user->get('id')>0){
			if (version_compare(JVERSION, "1.6.0", 'ge')) {
				$access = $user->authorise('core.deleteall', 'com_jevents');
			}
			else {
				// does this logged in have backend access
				// Get an ACL object
				$acl = JFactory::getACL();
				$grp = $acl->getAroGroup($user->get('id'));
				// if no valid group (e.g. anon user) then skip this.
				if (!$grp) return;

				$access = $acl->is_group_child_of($grp->name, 'Public Backend');
			}
		}

		if (($jevuser && $jevuser->candeleteall) || $access){
			$access = true;
		}

		if ($user->id !== $row->created_by()  && !$access) return true;

		$script ="function showextraoptions(elem){
			if ($('jsgroups')){
				if (elem.value==4) {
					$('jsgroups').style.display='block'; 
				}
				else { 
					$('jsgroups').style.display='none';
					$('custom_jevuser_jsgroupselection').selectedIndex=-1;
				}
			}
			if ($('cbgroups')){
				if (elem.value==5) {
					$('cbgroups').style.display='block'; 
				}
				else { 
					$('cbgroups').style.display='none';
					$('custom_jevuser_cbgroupselection').selectedIndex=-1;
				}
			}
		}";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

		$db = JFactory::getDbo();
		$sql = "SELECT jum.privateevent FROM #__jev_usereventsmap as jum WHERE evdet_id=".$detailid;
		$db->setQuery($sql);
		$privateevent =$db->loadResult();
		if (is_null($privateevent) || $detailid==0){
			if ((int) $this->params->get("forcestate",0) > 0){
				$privateevent = (int) $this->params->get("forcestate",0);
			}
			else {
				$privateevent = (int) $this->params->get("defaultstate",0);
			}
		}
		$privateevent = (int) $privateevent;
		$html = '<div class="radio btn-group">';
		$html .= '<label for="custom_jevuser_public" class="radio btn btn-success"><input name="custom_jevuser" id="custom_jevuser_public" value="0" '.($privateevent==0?'checked="checked"':"").' type="radio" onclick="showextraoptions(this)"/>'.ucfirst(JText::_("public")).'</label>';

		if ((int) $this->params->get("enableprivate",0)){
			$html .= '<label for="custom_jevuser_private" class="radio btn"><input name="custom_jevuser" id="custom_jevuser_private" value="1" '.($privateevent==1?'checked="checked"':"").' type="radio"  onclick="showextraoptions(this)"/>'.ucfirst(JText::_("private")).'</label>';
		}
		if ((int) $this->params->get("enablehidden",0)){
			$html .= '<label for="custom_jevuser_hidden" class="radio btn"><input name="custom_jevuser" id="custom_jevuser_hidden" value="3" '.($privateevent==3?'checked="checked"':"").' type="radio"  onclick="showextraoptions(this)"/>'.ucfirst(JText::_("JEV_DETAILS_PRIVATE")).'</label>';		}
		//if ($this->cb || $this->jomsocial) $html .= "<br/>";
		if ($this->jomsocial){
			$html .= '<label for="custom_jevuser_jomsocial" class="radio btn"><input name="custom_jevuser" id="custom_jevuser_jomsocial" value="2" '.($privateevent==2?'checked="checked"':"").' type="radio" onclick="showextraoptions(this)" />'.ucfirst(JText::_("jomsocial_friends")).'</label>';

                        if ((int) $this->params->get("jsgrouprestrict",0)){
                            $db->setQuery("select * from #__community_groups as a LEFT JOIN #__community_groups_members as b ON a.id=b.groupid where a.published=1 AND (a.approvals=0  OR (b.approved=1 AND a.ownerid=".$user->id.")) group by a.id ORDER BY a.name");
                        }
                        else {
                            $db->setQuery("select * from #__community_groups as a LEFT JOIN #__community_groups_members as b ON a.id=b.groupid where a.published=1 AND (a.approvals=0  OR (b.approved=1 AND b.memberid=".$user->id.")) group by a.id ORDER BY a.name");
                        }

			$jsgroups = $db->loadObjectList();
			if ($jsgroups && count($jsgroups)>0){
				$this->jomsocialgroups = true;
				$html .= '<label for="custom_jevuser_group" class="radio btn"><input name="custom_jevuser" id="custom_jevuser_group" value="4" '.($privateevent==4?'checked="checked"':"").' type="radio"  onclick="showextraoptions(this)" />'.ucfirst(JText::_("JOMSOCIAL_GROUP_EVENT")).'</label>';

			}
			else $this->jomsocialgroups = false;

			//if ($this->cb) $html .= "<br/>";
		}

		// These must be last!!
		if ($this->jomsocial && $this->jomsocialgroups){
			$html .= "<div id='jsgroups' style='display:".($privateevent==4?'block':'none')."'>";

			// Find the current selection
			if ($privateevent==4){
				$sql = "SELECT jum.groupid FROM #__jev_usereventsmap as jum WHERE evdet_id=".$detailid;
				$db->setQuery($sql);
				$privategroups = $db->loadColumn();
			}
			else {
				$privategroups = array();
			}
			$html .= "<select name='custom_jevuser[]' id='custom_jevuser_jsgroupselection' multiple='multiple' size='4'>";
			foreach ($jsgroups as $group) {
				$html .= "<option value='4g$group->id' ".(in_array($group->id,$privategroups)?" selected='true'":"").">$group->name</option>";
			}
			$html .= "</select>";
			$html .= "</div>";

		}

		if ($this->cb){
			$html .= '<label for="custom_jevuser_cb" class="radio btn"><input name="custom_jevuser" id="custom_jevuser_cb" value="6" '.($privateevent==6?'checked="checked"':"").' type="radio" onclick="showextraoptions(this)" />'.ucfirst(JText::_("JEV_CB_CONNECTIONS")).'</label>';
			if ($this->groupjive){
				$user = JFactory::getUser();
				//$db->setQuery("select * from #__groupjive_groups WHERE access ". (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' . JEVHelper::getAid($user)) ." AND (type=1 OR (user_id=".$user->id.")) and published=1 ORDER BY name" );
                                // Group Jive no longer seems to check access levels in a group
                                $db->setQuery("select * from #__groupjive_groups WHERE (type=1 OR (user_id=".$user->id.")) and published=1 ORDER BY name" );
				$cbgroups = $db->loadObjectList();
				if ($cbgroups && count($cbgroups)>0){
					$this->cbgroups = true;
					$html .= '<label for="custom_jevuser_groupjive" class="radio btn" ><input name="custom_jevuser" id="custom_jevuser_groupjive" value="5" '.($privateevent==5?'checked="checked"':"").' type="radio"  onclick="showextraoptions(this)" />'.ucfirst(JText::_("GROUPJIVE_GROUP_EVENT")).'</label>';

				}
				else $this->cbgroups = false;
			}
			else $this->cbgroups = false;

		}
		if ($this->cb && $this->cbgroups){
			$html .= "<div id='cbgroups' style='display:".($privateevent==5?'block':'none')."'>";

			// Find the current selection
			if ($privateevent==5){
				$sql = "SELECT jum.groupid FROM #__jev_usereventsmap as jum WHERE evdet_id=".$detailid;
				$db->setQuery($sql);
				$privategroups = $db->loadColumn();
			}
			else {
				$privategroups = array();
			}
			$html .= "<select name='custom_jevuser[]' id='custom_jevuser_cbgroupselection' multiple='multiple' size='4'>";
			foreach ($cbgroups as $group) {
				$html .= "<option value='5g$group->id' ".(in_array($group->id,$privategroups)?" selected='true'":"").">$group->name</option>";
			}
			$html .= "</select>";
			$html .= "</div>";
		}

		$html .= "</div>";
		
		$label = JText::_("Personal_Event_Status");

		$customfield = array("label"=>$label,"input"=>$html);
		$customfields["jevusers"]=$customfield;

		return true;
	}

	/**
	 * Clean out custom fields for event details not matching global event detail
	 *
	 * @param unknown_type $idlist
	 */
	public function onCleanCustomDetails($idlist){
		// TODO
		return true;
	}


	/**
	 * Store custom fields
	 *
	 * @param iCalEventDetail $evdetail
	 */
	public function onStoreCustomDetails($evdetail){

		$jinput = JFactory::getApplication()->input;


		if (!(int) $this->params->get("enableprivate",0) && !(int)$this->params->get("enablehidden",0) && !(int)$this->params->get("forcestate",0)) return true;

		$detailid = (int)$evdetail->evdet_id;
		$privateevent = array_key_exists("jevuser", $evdetail->_customFields) ? $evdetail->_customFields["jevuser"] : (int)$this->params->get("forcestate", 0);

		// Forced State overrules above options
		$user = JFactory::getUser();
		if ((int) $this->params->get("forcestate", 0) > 0 && !JEVHelper::isAdminUser($user)){
			$privateevent = (int) $this->params->get("forcestate",$privateevent);
		}

		$db = JFactory::getDBO();

		$user = JFactory::getUser();

		// Who is marked as the creator - no need for double security here since the event would unviewable if it is abused
		$created_by = $jinput->getInt("jev_creatorid", isset($evdetail->_created_by) ? $evdetail->_created_by : $user->id);
		
		// first of all remove all the old mappings
		$sql = "DELETE FROM #__jev_usereventsmap WHERE evdet_id=".$detailid;
		$db->setQuery($sql);
		$success = $db->execute();

		//TODO review code, we are coniditionally setting int into $privateevent array? a foreach loop cannot go through an int.
		//Although we don't get to a foreach loop because we check it's array first it's not clean good.

		// These are groups
		if (is_array($privateevent)){
			foreach ($privateevent as $group) {
				// Jomsocial groups have a leading value of 4g and a group type = 1
				if (strpos($group,"4g")===0){
					$value = (int) str_replace("4g","",$group);
					$sql = "INSERT INTO #__jev_usereventsmap SET user_id=".(int) $created_by.",  evdet_id=".$detailid.",privateevent=4,groupid=".(int) $value;
					$db->setQuery($sql);
					$success =  $db->execute();
				}
				// GroupJive groups have a leading value of 5g and a group type = 1
				if (strpos($group,"5g")===0){
					$value = (int) str_replace("5g","",$group);
					$sql = "INSERT INTO #__jev_usereventsmap SET user_id=". (int) $created_by.",  evdet_id=".$detailid.",privateevent=5,groupid=".(int) $value;
					$db->setQuery($sql);
					$success =  $db->execute();
				}

			}
		}
		else {
			$sql = "INSERT INTO #__jev_usereventsmap SET user_id=".(int) $created_by.",  evdet_id=".$detailid.",privateevent=".(int) $privateevent;
			$db->setQuery($sql);
			$success =  $db->execute();
		}
		return $success;

	}

	public function onDisplayCustomFields(&$row){
		if (!(int)$this->params->get("enableprivate",0) && !(int) $this->params->get("enablehidden",0)) return "";

		// get the data from database and attach to row
		$detailid = (int) $row->evdet_id();
		$user = JFactory::getUser();
		if ($user->id==0) return "";

		if ($user->id !== $row->created_by()) return "";

		$sql = "SELECT jum.privateevent FROM #__jev_usereventsmap as jum WHERE evdet_id=".$detailid;
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		$privateevent = $db->loadResult();

		switch ($privateevent) {
			case 0:
				$return =  "<span class='jevpublicevent'>".JText::_("Public_Event")."</span>";
				break;

			case 1:
				$return = "<span class='jevprivateevent'>".JText::_("Private_Event")."</span>";
				break;

			case 2:
				$return = "<span class='jevprivateevent jevprivateevent_js'>".JText::_("JOMSOCIAL_EVENT")."</span>";
				break;

			case 3:
				$return = "<span class='jevhiddenevent jevhiddenevent_hd'>".JText::_("JEV_DETAILS_PRIVATE")."</span>";
				break;

			case 4:
				$return = "<span class='jevhiddenevent jevhiddenevent_ge'>".JText::_("JOMSOCIAL_GROUP_EVENT")."</span>";
				break;

			case 5:
				$return = "<span class='jevhiddenevent jevhiddenevent_gje'>".JText::_("GROUPJIVE_EVENT")."</span>";
				break;

			case 6:
				$return = "<span class='jevprivateevent jevprivateevent_cbce'>".JText::_("JEV_CB_CONNECTION_EVENT")."</span>";
				break;

			default:
				break;
		}
		$row->_privateevent = $privateevent;
		$row->privateeventsummary = $return;
		return $return;
	}

	public function onDisplayCustomFieldsMultiRow(&$rows)
	{
		if (!(int) $this->params->get("enableprivate",0) && !(int) $this->params->get("enablehidden",0)) return "";

		$user = JFactory::getUser();
		if ($user->id==0) return "";

		foreach ($rows as &$row) {
			if (!isset($row->_privateevent))
				continue;
			$return = "";
			switch ((int) $row->_privateevent) {
				case 0:
					$return =  "<span class='jevpublicevent'>".JText::_("Public_Event")."</span>";
					break;

				case 1:
					$return = "<span class='jevprivateevent'>".JText::_("Private_Event")."</span>";
					break;

				case 2:
					$return = "<span class='jevprivateevent jevprivateevent_js'>".JText::_("JOMSOCIAL_EVENT")."</span>";
					break;

				case 3:
					$return = "<span class='jevhiddenevent jevhiddenevent_hd'>".JText::_("JEV_DETAILS_PRIVATE")."</span>";
					break;

				case 4:
					$return = "<span class='jevhiddenevent jevhiddenevent_ge'>".JText::_("JOMSOCIAL_GROUP_EVENT")."</span>";
					break;

				case 5:
					$return = "<span class='jevhiddenevent jevhiddenevent_gje'>".JText::_("GROUPJIVE_EVENT")."</span>";
					break;

				case 6:
					$return = "<span class='jevprivateevent jevprivateevent_cbce'>".JText::_("JEV_CB_CONNECTION_EVENT")."</span>";
					break;

				default:
					break;
			}
			$row->privateeventsummary = $return;
		}
		return "";
	}

	public static function fieldNameArray($layout='detail'){

		if ($layout == "edit")
		{
			$return = array();
			$labels = array();
			$values = array();

			$return['group'] = JText::_("PERSONAL_EVENT_STATUS", true);

			$labels[] = JText::_('Personal_Event_Status');
			$values[] = "jevusers";

			$return['values'] = $values;
			$return['labels'] = $labels;

			return $return;
		}

		// only offer in detail view ??
		// if ($layout != "detail") return array();

		$return  = array();
		$return['group'] = JText::_("PERSONAL_EVENT_STATUS",true);

		$labels = array();
		$labels[] = JText::_("PERSONAL_EVENT_SUMMARY",true);
		$values = array();
		$values[] = "JEV_PERSONALEVENT_SUMMARY";

		$return['values'] = $values;
		$return['labels'] = $labels;

		return $return;
	}

	public static function substitutefield($row, $code){
		if ($code == "JEV_PERSONALEVENT_SUMMARY"){
			if (isset($row->privateeventsummary))return $row->privateeventsummary;
		}
		return "";
	}

}
