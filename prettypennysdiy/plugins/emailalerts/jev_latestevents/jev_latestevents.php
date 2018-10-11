<?php
/*
 * @package Latest News - JomSocial  Plugin for J!MailAlerts Component
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://www.techjoomla.com
 */

// Do not allow direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

//include plugin helper file
$jma_helper=JPATH_SITE.DS.'components'.DS.'com_jmailalerts'.DS.'helpers'.DS.'plugins.php';
if(JFile::exists($jma_helper)){
	include_once($jma_helper);
}
else//this is needed when JMA integration plugin is used on sites where JMA is not installed
{
	if(JVERSION>'1.6.0'){
		$jma_integration_helper=JPATH_SITE.DS.'plugins'.DS.'system'.DS.'plg_sys_jma_integration'.DS.'plg_sys_jma_integration'.DS.'plugins.php';
	}else{
		$jma_integration_helper=JPATH_SITE.DS.'plugins'.DS.'system'.DS.'plg_sys_jma_integration'.DS.'plugins.php';
	}
	if(JFile::exists($jma_integration_helper)){
		include_once($jma_integration_helper);
	}
}

//class plgPluginTypePluginName extends JPlugin
class plgEmailalertsJev_latestevents extends JPlugin
{
	function plgEmailalertsJev_latestevents(&$subject,$config)
	{
		parent::__construct($subject, $config);
		if($this->params===false)
		{	
			$jPlugin=JPluginHelper::getPlugin('emailalerts','jev_latestevents');
			$this->params=new JParameter( $jPlugin->params);
		}
	}

	function onEmail_jev_latestevents($id,$date,$userparam,$fetch_only_latest)
	{
                        require_once (JPATH_SITE."/modules/mod_jevents_latest/helper.php");
                        
                        $areturn =  array();
                        if($id==NULL)//if no userid/or no guest user return blank array for html and css
                        {
                                $areturn[0] =$this->_name;
                                $areturn[1]	= '';
                                $areturn[2]	= '';
                                return $areturn;
                        }    
                        //We get JEvents latest events data
                        $catids = $this->params->get("catidnew", "0");
                        $list=$this->getLatestEvents($id,$catids);
                        
                        if(strstr($list, JText::_('JEV_NO_EVENTS')))
                        {
                            $list="";
                        }
	   
                        $areturn[0] =$this->_name;
                        if(empty($list))
                        {
                                //if no output is found, return array with 2 indexes with NO values
                                $areturn[1]='';
                                $areturn[2]='';
                        }
                        else
                        {
                                $jevhelper = new modJeventsLatestHelper();
                                $modtheme = $this->params->get("com_calViewName", "");
                                if ($modtheme=="" || $modtheme=="global"){
                                        $modtheme=JEV_CommonFunctions::getJEventsViewName();;
                                }
                                $theme = $modtheme;                                
                                
                                $plugin_params=$this->params;                                
                                $helper = new pluginHelper();
                                $ht=$helper->getLayout($this->_name,$list,$plugin_params);
                                $areturn[1]=$ht;

                                //Get JEvents theme CSS
                                $cssfile = JPATH_SITE."/components/".JEV_COM_COMPONENT."/views/".$theme."/assets/css/modstyle.css";                                                      
                                $cssdata=JFile::read($cssfile);
                                $areturn[2] = $cssdata;
                        }
                        return $areturn;
	}

                private function getLatestEvents($userid,$catids)
                {
                        $return = "";
                        //JRequest::setVar("jevu_fv",$userId);
                        require_once (JPATH_SITE."/modules/mod_jevents_latest/helper.php");

                        $jevhelper = new modJeventsLatestHelper();
                        $modtheme = $this->params->get("com_calViewName", "");
                        if ($modtheme=="" || $modtheme=="global"){
                                $modtheme=JEV_CommonFunctions::getJEventsViewName();;
                        }
                        $theme = $modtheme; 
                        

                        JPluginHelper::importPlugin("jevents");

                        $viewclass = $jevhelper->getViewClass($theme, 'mod_jevents_latest',$theme . "/latest", $this->params);

                        // create temporary clone of the params for tinkering with
                        $this->tempparams = clone $this->params;

                        // Set categories in params
                        if ($catids && count($catids)>0){
                                for ($c=0; $c < 999; $c++) {
                                        $nextCID = "catid$c";
                                        //  stop looking for more catids when you reach the last one!
                                        if (!$nextCatId = $this->tempparams->get( $nextCID, null)) {
                                                break;
                                        }
                                }
                                // now add in the constraints
                                foreach($catids as $catid){
                                        $nextCID = "catid$c";
                                        $this->tempparams->set( $nextCID, $catid);
                                        $c++;
                                }

                        }
                        
                        //We set the userid to provide proper events
                        JRequest::setVar('jev_userid',$userid);

                        // record what is running - used by the filters
                        $registry	= JRegistry::getInstance("jevents");
                        $registry->set("jevents.activeprocess","mod_jevents_latest");
                        $registry->set("jevents.moduleid", "cb");
                        $registry->set("jevents.moduleparams", $this->tempparams);

                        $modview = new $viewclass($this->tempparams, 0);
                        $return .= $modview->displayLatestEvents();

						$linkRoot = "href=\"".str_replace(JURI::root(true),"",JURI::root());

						$linkRoot = substr($linkRoot, 0, strlen($linkRoot)-1);

						$return = str_replace ( "href=\"" , $linkRoot , $return);

                        $return .="<br style='clear:both'/>";

                        // replace seffed URLs generated from the backend of the site
                        $return = str_replace('/administrator/', '/', $return);

                        return $return;
	}

}
