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
class jevActiveuserFilter extends jevFilter
{

	public function __construct($tablename, $filterfield, $isstring=true){

		$jinput = JFactory::getApplication()->input;

		$this->filterType="jevu";
		$this->filterNullValue="";

		parent::__construct($tablename,$filterfield, true);
		//This filter has memory!

		if ((int) $jinput->getInt('filter_reset',0) && $jinput->getInt($this->filterType.'_fv')==0){
			$this->filter_value = "";
		}

		// Set the current user value for the main user filter to pick up
		if ($this->filter_value>0) $jinput->set($this->filterType.'_fv', $this->filter_value );
	}
	
	public function _createFilter($prefix=""){return "";}


	public function _createfilterHTML(){

		$filterList=array();
		
		if (!$this->filterField) return $filterList;

		if ($this->filter_value==$this->filterNullValue || (int) $this->filter_value < 0) return $filterList;
		
		$user = JFactory::getUser((int) $this->filter_value);
		if (!$user) return $filterList;
		
		$filterList["title"]=JText::_("Active_User");
		$filterList["html"] = $user->name." <input type='checkbox' name='".$this->filterType."_fv'  id='".$this->filterType."_fv'  value='".$this->filter_value."' checked='checked'/>";

		return $filterList;

	}
}