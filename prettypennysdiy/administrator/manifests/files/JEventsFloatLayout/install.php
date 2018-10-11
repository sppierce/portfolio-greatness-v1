<?php

/**
 * copyright (C) 2015 GWE Systems Ltd - All rights reserved
 * @license GNU/GPLv3 www.gnu.org/licenses/gpl-3.0.html
 * */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// This will only be triggered in Joomla 3.4.0 or later after a change in the name convension of file packages
class JEventsFloatLayoutInstallerScript
{

    private $oldelement = "float";
    private $newelement = "JEventsFloatLayout";


    //
    // Joomla installer functions
    //
    public function preflight($type, $parent)
    {
	    //Basic JEvents exists check
	    if(!JFolder::exists(JPATH_SITE . '/components/com_jevents/views/')) {
		    Jerror::raiseWarning(null, JText::_('JEVENTS_REQUIRED'));

		    return false;
	    }
    }

    public function postflight($type, $parent)
    {

        // Joomla! broke the update call, so we have to create a workaround check.
        $db = JFactory::getDbo();

	    $db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_jevents" AND type = "component"');
	    $manifestcache = $db->loadResult();
	    if (!$manifestcache) {
		    Jerror::raiseWarning(null, 'This version of Float Theme is desgined for JEvents.<br/>Please install JEvents 3.4.0 or later before installing Float Theme' );
		    return false;
	    }
	    else {
		    $manifestcache = json_decode( $manifestcache, true );
		    if (version_compare($manifestcache["version"], "3.4.11", "<")){
			    Jerror::raiseWarning(null, 'This version of Float Theme is designed for JEvents 3.4.11 or later.<br/>Please upgrade JEvents to version 3.4.11 or later before installing this version of Float Theme' );
			    return false;
		    }
	    }

        $db->setQuery("SELECT * FROM #__extensions WHERE element =".$db->quote($this->oldelement). " OR element=".$db->quote($this->newelement));
        $extensions = $db->loadObjectList();

        if (count($extensions)>1){
            $hasold = false;
            $hasnew = false;
            foreach ($extensions as $extension){
                if ($extension->element == $this->oldelement) {
                    $hasold = $extension;
                }
                else if ($extension->element == $this->newelement) {
                    $hasnew = $extension ;
                }
            }
            if ($hasold && $hasnew){
                $db->setQuery("DELETE FROM #__extensions WHERE element =".$db->quote($this->oldelement));
                $db->query();
            }
        }
	    //Lets just check we have inserted the default rows if not insert them!!
	    $db->setQuery("SELECT * FROM #__jev_defaults");
	    $defaults =$db->loadObjectList("name");
	    if (!isset($defaults['icalevent.list_block1'])){
		    $db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.list_block1',
						title=".$db->Quote("JEV_FLOAT_LAYOUT_BLOCK1").",
						subject='',
						value='',
						state=0");
		    $db->execute();

		    $db->setQuery("INSERT INTO  #__jev_defaults set name='icalevent.list_block2',
						title=".$db->Quote("JEV_FLOAT_LAYOUT_BLOCK2").",
						subject='',
						value='',
						state=0");
		    $db->execute();
	    }

	    if (JFile::exists(JPATH_ADMINISTRATOR."/manifests/files/float.xml")){
		    JFile::delete(JPATH_ADMINISTRATOR."/manifests/files/float.xml");
	    }
	    
		return;
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        echo '<h2>' . JText::_('PLG_INST_JEVENTS_JEVFLOAT') . ' ' . $parent->get('manifest')->version . ' </h2>';
        echo '<strong>';

        if ($type == "update")
        {
            echo JText::_('PLG_INST_JEVENTS_JEVFLOAT_SUCC1') . '<br/>';
            echo JText::_('PLG_INST_JEVENTS_JEVFLOAT_DESC');
        }
        else
        {
            echo JText::_('PLG_INST_JEVENTS_JEVFLOAT_SUCC2') . '<br/>';
            echo JText::_('PLG_INST_JEVENTS_JEVFLOAT_DESC');
        }
        echo '</strong><br/><br/>';

    }

	public function uninstall($parent)
	{

		// We are a file installer, so we need to manually delete the files.
		// $parent is the class calling this method
		if (JFolder::exists(JPATH_SITE."/components/com_jevents/views/float")){
			JFolder::delete(JPATH_SITE."/components/com_jevents/views/float");
		}
		if (JFolder::exists(JPATH_SITE."/modules/mod_jevents_cal/tmpl/float")){
			JFolder::delete(JPATH_SITE."/modules/mod_jevents_cal/tmpl/float");
		}
		if (JFolder::exists(JPATH_SITE."/modules/mod_jevents_latest/tmpl/float")){
			JFolder::delete(JPATH_SITE."/modules/mod_jevents_latest/tmpl/float");
		}
		if (JFolder::exists(JPATH_SITE."/modules/mod_jevents_legend/tmpl/float")){
			JFolder::delete(JPATH_SITE."/modules/mod_jevents_legend/tmpl/float");
		}
	}

}
