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
class jevJsgroupeventsFilter extends jevFilter
{

	public function __construct($tablename, $filterfield, $isstring=true){
		$this->filterType="jevjsg";
		$this->filterNullValue="";
		parent::__construct($tablename,$filterfield, true);

	}
	
	public function _createFilter($prefix=""){

		$jinput = JFactory::getApplication()->input;

		JPluginHelper::importPlugin('jevents');
		$plugin = JPluginHelper::getPlugin("jevents","jevusers");
		$this->params = new JRegistry($plugin->params);
		if ($this->params->get("allowadmin",0)){
			return "1";
		}

		if ($jinput->getCmd("option") == "com_community" && $jinput->getCmd("task") == "viewgroup" && $jinput->getCmd("view") == "groups" &&  $jinput->getString("groupid") != "" && strpos( $jinput->getString("groupid"),":") > 0){
			list($groupid,$groupname)=explode(":",JRequest::getString("groupid"));
			if ((int) $groupid > 0){
				return "jum.privateevent=4 AND jcgm.groupid= ".(int)$groupid;
			}
		}
		else if ($jinput->getCmd("option")=="com_jevents" && $jinput->getInt("jevge_fv")>0){
			return "jum.privateevent=4 AND jcgm.groupid= ".$jinput->getInt("jevge_fv");
		}
		return "0";
	}


	public function _createfilterHTML(){

		$filterList=array();
		return $filterList;

	}
}