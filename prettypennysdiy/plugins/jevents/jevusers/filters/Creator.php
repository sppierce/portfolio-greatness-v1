<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: Search.php 1410 2009-04-09 08:13:54Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die( 'No Direct Access' );

// searches author of event
class jevCreatorFilter extends jevFilter
{
	private $hasrsvp = false;
	private $hasaje = false;
	private $rsvpparams = null;
	private $rsvpversion = 1.2;
	private $ajeparams = null;
	private $params = null;

	public function __construct($tablename, $filterfield, $isstring=true){
		$jinput = JFactory::getApplication()->input;

		$this->filterType="jevcr";
		$this->filterNullValue="-1";
		parent::__construct($tablename,$filterfield, true);

		// Should these be ignored?
		$reg = JFactory::getConfig();
		$modparams = $reg->get("jev.modparams",false);
		if ($modparams && $modparams->get("ignorefiltermodule",false)){
			$this->filter_value = $this->filterNullValue;
			return ;
		}

		if ((int) $jinput->getInt('filter_reset', 0)){
			$jinput->set($this->filterType.'_fv', $this->filterNullValue );
		}

		// Only have memory on page with the module visible or
		$this->filter_value =  $jinput->get($this->filterType.'_fv', $this->filterNullValue );

		// get plugin params
		$plugin = JPluginHelper::getPlugin('jevents', 'jevusers');
		if (!$plugin) return ;
		
		$this->params = new JRegistry($plugin->params);

	}

	public function _createFilter($prefix=""){
		if (!$this->filterField ) return "";

		if (trim($this->filter_value)==$this->filterNullValue || trim($this->filter_value)=="") return "";
		
		$this->filter_value = (int) $this->filter_value;

                $filter = "ev.created_by = ".$this->filter_value;

		return $filter;
	}

	public function _createJoinFilter($prefix=""){
		return "";
	}

	public function _createfilterHTML(){

		if (!$this->filterField) return "";

		$db = JFactory::getDBO();

		$filterList=array();
		$filterList["title"]=JText::_("JEV_EVENT_CREATOR");
                
                $sql = "SELECT distinct (u.id), u.name,  u.username FROM #__users as u "
                        . " INNER JOIN #__jevents_vevent as ev on ev.created_by = u.id"
                        . " ORDER BY name asc LIMIT 200";
                $db->setQuery($sql);
                $users = $db->loadObjectList();

                // get list of creators - if fewer than 200
                if (count($users)>200) {
                        return null;
                }

                $userOptions[] = JHTML::_('select.option', '-1', JText::_('SELECT_USER'));
                foreach ($users as $user)
                {
                       // $userOptions[] = JHTML::_('select.option', $user->id, $user->name . " ( " . $user->username . " )");
                     $userOptions[] = JHTML::_('select.option', $user->id, $user->name);
                }

                $userlist = JHTML::_('select.genericlist', $userOptions, $this->filterType."_fv", 'class="inputbox" size="1" ', 'value', 'text', $this->filter_value);
                // auto submit 
                //$userlist = JHTML::_('select.genericlist', $userOptions, $this->filterType."_fv", 'class="inputbox" size="1" onchange="submit(this.form)" ', 'value', 'text', $this->filter_value);
                
		$filterList["html"] = $userlist;

		return $filterList;

	}
}
