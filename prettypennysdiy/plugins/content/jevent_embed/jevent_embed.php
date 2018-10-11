<?php

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('joomla.plugin.plugin');
JLoader::register('JevJoomlaVersion',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/version.php");

class plgContentJevent_embed extends JPlugin
{

	function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);
		if (version_compare(JVERSION, "1.6.0", 'ge'))
		{
			$this->pluginpath = "/plugins/jevents/jevcck/";
		}
		else
		{
			$this->pluginpath = "/plugins/jevents/";
		}

	}

// Joomla 1.6!!
	public
			function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		return $this->onPrepareContent($article, $params);

	}
/*
	static function onExtensionBeforeSave($context, $table, $isNew)
	{
		$dispatcher = JDispatcher::getInstance();
		if ($context == "com_plugins.plugin") {
			// just incase we don't have jevents plugins registered yet
			JPluginHelper::importPlugin("jevents");
			return $dispatcher->trigger('onPluginBeforeSave', array($context, &$table, $isNew));
		}
	}
*/
	public
			function onContentAfterSave($optname, & $table, $isNew)
	{
//if ($isNew) {
		if ($optname == "com_categories.category" && $table->extension == "com_jevents")
		{
			$x = 1;
		}
//}

	}

	public
			function onContentBeforeDelete($optname, $table)
	{
//if ($isNew) {
		if ($optname == "com_categories.category" && $table->extension == "com_jevents")
		{
			$x = 1;
		}
//}

	}

	public
			function onContentAfterDelete($optname, $table)
	{
//if ($isNew) {
		if ($optname == "com_categories.category" && $table->extension == "com_jevents")
		{
			$x = 1;
		}
//}

	}

	function onPrepareContent(&$row, &$registry)
	{

		// do not process the same article more than once (i.e. avoid recursion!)
		if (isset($row->jev_processed))
		{
			return true;
		}
		$row->jev_processed = true;

		JFactory::getLanguage()->load('plg_content_jevent_embed', JPATH_ADMINISTRATOR);

// expression to search for
		$regex = "#{jevent[\=|\s]?(.+)}#s";
		$regex1 = '/{(jevent=)\s*(.*?)}/i';

// find all instances of mambot and put in $matches
		preg_match_all($regex1, $row->text, $matches);
// Number of mambots
		$replace = array();
		if ($matches && count($matches) > 0)
		{

			$count = count($matches[0]);

			if ($count > 0)
			{
				for ($i = 0; $i < $count; $i++)
				{
					$r = str_replace('{jevent=', '', $matches[0][$i]);
					$r = str_replace('}', '', $r);
					$ex = explode('|', $r);

					$ploc = $ex[0];
					$jtarget = $ex[1];
					$redirect = $ex[2];
					$nextrepeat = array_key_exists(3, $ex) ? $ex[3] : false;
					$usetitle = array_key_exists(4, $ex) ? $ex[4] : false;

					$replace[] = $this->plg_jeventembed_replacer($ploc, $jtarget, $redirect, $nextrepeat, $usetitle);
				}
				$row->text = str_replace($matches[0], $replace, $row->text);
			}
			if ($count == 0)
			{
				if ($this->params->get("autoagenda", 0))
				{
					$link = $this->autolink($row);
					if ($link != "")
					{
						$row->text .= "<div class='autoagenda'>" . $link . "</div>";
					}
				}
			}
		}
		else if ($this->params->get("autoagenda", 0))
		{
			$link = $this->autolink($row);
			if ($link != "")
			{
				$row->text .= "<div class='autoagenda'>" . $link . "</div>";
			}
		}


		$regexmatch = '/{(jeventmatch=)\s*(.*?)}/i';
		preg_match_all($regexmatch, $row->text, $matches);
		$replace = array();
		if ($matches && count($matches) > 0)
		{

			$count = count($matches[0]);

			for ($i = 0; $i < $count; $i++)
			{
				$r = str_replace('{jeventmatch=', '', $matches[0][$i]);
				$r = str_replace('}', '', $r);
				$ex = explode('|', $r);

				$id = $ex[0];
				$jtarget = $ex[1];

				$replace[] = $this->plg_jeventembed_replacer2($id, $jtarget);
			}
			$row->text = str_replace($matches[0], $replace, $row->text);
		}

		// Sobi Pro
		/*
		 * You must include {spjevents=<xsl:value-of select="entry/@id" />} in your entry/details.xsl
		 * You must enable Section Config->General Config=>Template Data=>Parse Template Output = Yes
		 */
		$regex2 = '/{spjevents=\s*(.*?)}/i';
		preg_match_all($regex2, $row->text, $matches);
		if ($matches && count($matches) == 2 && count($matches[1]) > 0)
		{

			$count = count($matches[1]);
			for ($i = 0; $i < $count; $i++)
			{
				$row->jevcckid = $matches[1][$i];
				if ($row->jevcckid > 0)
				{
					$html = $this->sobiEvents($row);
					$row->text = str_replace($matches[0][$i], $html, $row->text);
				}
			}
		}
        $jinput = JFactory::getApplication()->input;
		// CCK links if required
		if ($jinput->get('option', '', 'cmd') == "com_zoo")
		{
			JPluginHelper::importPlugin('jevents');
			$dispatcher = JDispatcher::getInstance();
			$plugin = JPluginHelper::getPlugin("jevents", "jevcck");
			if ($plugin)
			{
				$params = new JRegistry($plugin->params);
				if ($params->get("ccktype") == "zoo" && is_object($registry))
				{
                    // Caused fatal error on J3.4.1 should use JInput
					//$row->jevcckid = $registry->get("item_id", 0);
					$row->jevcckid = $jinput->get('item_id', 0, 'int');
					//$row->jevcckid = JRequest::getInt("item_id", 0);
					if ($row->jevcckid > 0)
					{
						$this->zooEvents($row);
					}
				}
			}
		}

		return true;

	}

	private
			function plg_jeventembed_replacer($evid, $jtarget, $redirect, $nextrepeat, $usetitle)
	{

// getting the event by event detail id
// setup the Joomla autoloader
		include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");
// get the data and query models
		$dataModel = new JEventsDataModel("JEventsAdminDBModel");
		$queryModel = new JEventsDBModel($dataModel);
//method viewDetailLink is in the following class
		$jEventModel = new jEventCal($dataModel);

// get the event by repeat id
		$jevent = $queryModel->listEventsById(intval($evid), 1, "icaldb");
		if (!$jevent)
			return "";
		if ($nextrepeat)
		{
			$jevent = $jevent->getNextRepeat();
		}
		// get the event detail link (aleady SEFed)
		$Itemid = JEVHelper::getItemid($jevent);
		$Itemid = intval($this->params->get("target_itemid",$Itemid));
		$detailSefLink = $jevent->viewDetailLink($jevent->yup(), $jevent->mup(), $jevent->dup(), true, $Itemid);
		$link = JRoute::_($detailSefLink);
//$link = JRoute::_('index.php?option=com_jevents&task=icalrepeat.detail&evid='.$evid);

		if ($redirect == 1)
		{
			JFactory::getApplication()->redirect($link);
		}
		else
		{
			$title = $usetitle ? $jevent->title() : JText::_("DISPLAY_EVENT");
			return '<a class="jev_embed_link" href="' . $link . '" target="' . $jtarget . '">' . $title . '</a>';
		}

	}

	private
			function plg_jeventembed_replacer2($evid, $jtarget)
	{

		// getting the event by event detail id
		// setup the Joomla autoloader
		include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");
		// get the data and query models
		$dataModel = new JEventsDataModel("JEventsAdminDBModel");
		$queryModel = new JEventsDBModel($dataModel);
		//method viewDetailLink is in the following class
		$jEventModel = new jEventCal($dataModel);

		// get the event by repeat id
		$jevent = $queryModel->listEventsById(intval($evid), 1, "icaldb");
		if (!$jevent)
			return "";

		// attach matching event summary
		JPluginHelper::importPlugin('jevents');
		$dispatcher = JDispatcher::getInstance();
		$plugin = JPluginHelper::getPlugin("jevents", "jevmatchingevents");
		if ($plugin)
		{
			JLoader::register('plgJEventsJevmatchingevents', JPATH_SITE . "/plugins/jevents/jevmatchingevents/jevmatchingevents.php");
			$plugin = new plgJEventsJevmatchingevents($dispatcher, get_object_vars($plugin));

			$plugin->onDisplayCustomFields($jevent);
		}

		// this calls too many plugins that are not needed!
		//$dispatcher->trigger( 'onDisplayCustomFields', array( &$jevent) );

		if (isset($jevent->_jevmatches))
			return $jevent->_jevmatches;

		return "none";

	}

	private
			function autolink($row)
	{
		$mainframe = JFactory::getApplication();
		if ($mainframe->isAdmin())
		{
			return;
		}

		if (!$row)
			return;
		if (isset($row->id) && isset($row->introtext) && isset($row->fulltext) && $row->id > 0)
		{
			$db = JFactory::getDBO();
			static $instances = array();

			// Deal with agenda or minutes
			if (!isset($instances[$row->id]))
			{
				$sql = "SELECT am.*, rp_id FROM #__jev_agendaminutes as am 
					LEFT JOIN #__jevents_repetition as rpt on rpt.eventdetail_id=am.evdet_id
					WHERE am.agenda_id=" . $row->id . " AND rpt.rp_id IS NOT NULL" .
						" GROUP BY rpt.eventid ";
				$db->setQuery($sql);
				$am = $db->loadObject();
				$instances[$row->id] = $am;
				if ($am)
				{
					$jtarget = "_blank";
					$redirect = 0;
					$nextrepeat = true;
					$usetitle = true;

					$instances[$row->id] = $this->plg_jeventembed_replacer($instances[$row->id]->rp_id, $jtarget, $redirect, $nextrepeat, $usetitle);
				}
			}

			if ($instances[$row->id])
			{
				return $instances[$row->id];
			}
		}

	}

	private
			function zooEvents($item)
	{
		if (!isset($this->cckhelper))
		{
			$plugin = JPluginHelper::getPlugin("jevents", "jevcck");
			$pluginparams = new JRegistry($plugin->params);

			jimport("joomla.utilities.file");
			$l = $cck = $pluginparams->get("ccktype", 'sobi2');
			if (JFile::exists(JPATH_SITE . $this->pluginpath . "/cck/$cck.php"))
			{
				include_once(JPATH_SITE . $this->pluginpath . "/cck/$cck.php");
				$cck = "Jev$cck";
				$this->cckhelper = new $cck($pluginparams);
			}
			else
			{
				$this->cckhelper = false;
				JError::raiseError(403, "Invalid CCK Type");
			}
			if ($this->cckhelper)
			{
				$this->cckhelper->loadLanguages();
			}
		}

		require_once (JPATH_SITE . "/modules/mod_jevents_latest/helper.php");
		$jevhelper = new modJeventsLatestHelper();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		$viewclass = $jevhelper->getViewClass($theme, 'mod_jevents_latest', $theme ."/latest", $this->params);

		$modview = new $viewclass($this->params, 0);

		$oldfv = JRequest::getVar("cck_fv", 0);
		JRequest::setVar("cck_fv", $item->jevcckid);
		$return = $modview->displayLatestEvents();
		if ($return != "")
		{
			$item->text .= "<div class='jevzoo'>".JText::_("JEV_EVENT_EMBED_RELATED_EVENTS")."<br/>" . $return . "</div>";
		}
		JRequest::setVar("cck_fv", $oldfv);

	}

	private
			function sobiEvents($item)
	{
		if (!isset($this->cckhelper))
		{
			$plugin = JPluginHelper::getPlugin("jevents", "jevcck");
			$pluginparams = new JRegistry($plugin->params);

			jimport("joomla.utilities.file");
			$l = $cck = $pluginparams->get("ccktype", 'sobi2');
			if (JFile::exists(JPATH_SITE . $this->pluginpath . "/cck/$cck.php"))
			{
				include_once(JPATH_SITE . $this->pluginpath . "/cck/$cck.php");
				$cck = "Jev$cck";
				$this->cckhelper = new $cck($pluginparams);
			}
			else
			{
				$this->cckhelper = false;
				JError::raiseError(403, "Invalid CCK Type");
			}
			if ($this->cckhelper)
			{
				$this->cckhelper->loadLanguages();
			}
		}

		require_once (JPATH_SITE . "/modules/mod_jevents_latest/helper.php");
		$jevhelper = new modJeventsLatestHelper();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		$viewclass = $jevhelper->getViewClass($theme, 'mod_jevents_latest', $theme .  "/latest", $this->params);

		$modview = new $viewclass($this->params, 0);

		$oldfv = JRequest::getVar("cck_fv", 0);
		JRequest::setVar("cck_fv", $item->jevcckid);
		$return = $modview->displayLatestEvents();
		if ($return != "")
		{
			JRequest::setVar("cck_fv", $oldfv);
			return "<div class='jevsp'>".JText::_("JEV_EVENT_EMBED_RELATED_EVENTS")."<br/>" . $return . "</div>";
		}
		return "";

	}


	public function DISABLED_METHOD_onContentPrepareForm($form, $data)
	{
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}

		/*
		// Check we are manipulating a valid form.
		$name = $form->getName();
		if (in_array($name, array('com_modules.module')))
		{
			// Add the layout specific fields to the form.
			if (isset($data->module)  && $data->module=="mod_modulecacher"){
				if (isset($data->params["umod"]) && strpos($data->params["umod"], ":")>0){
					$umod = $data->params["umod"];
					list($umodid, $umod) = explode(":",$umod);
					JForm::addFormPath(JPATH_SITE."/modules/$umod/");
					//$form->loadFile($umod, false, "//config/fields");
					$form->loadFile($umod, false, "//config");
					//$form->loadFile($umod, false, "fieldset");
				}
			}
			else {
				$jform = JRequest::getVar("jform", array());
				if (isset($jform["params"]["umod"]) && strpos($jform["params"]["umod"],":")>0){
					$umod = $jform["params"]["umod"];
					list($umodid, $umod) = explode(":",$umod);
					JForm::addFormPath(JPATH_SITE."/modules/$umod/");
					//$form->loadFile($umod, false, "//config/fields");
					$form->loadFile($umod, false, "//config");
					//$form->loadFile($umod, false, "fieldset");
				}
			}

		}

		return true;
		*/
		// Check we are manipulating a valid form.
		$name = $form->getName();
		if (in_array($name, array('com_modules.module')))
		{
			// Add the layout specific fields to the form.
			if (isset($data->module)  && $data->module=="mod_jevents_latest"){
				JForm::addFormPath(JPATH_SITE."/modules/mod_jevents_latest/tmpl/iconic/");
				$form->loadFile('custom', false);
			}
		}

		 if (in_array($name, array('com_menus.item'))) {
			// Add the layout specific fields to the form.
			if (isset($data["type"])  && $data["type"]=="component" && isset($data["link"]) && strpos($data["link"] , 'com_jevents')!==false ){
				$params = JComponentHelper::getParams("com_jevents");
				$viewname = (isset($data["params"]["com_calViewName"]) && $data["params"]["com_calViewName"]!="global")?$data["params"]["com_calViewName"] : $params->get("com_calViewName","geraint");
				JForm::addFormPath(JPATH_SITE."/components/com_jevents/views/$viewname/");
				$form->loadFile('custom', false);
			}
		 }

		return true;
	}

}