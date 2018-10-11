<?php
/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;

class plgButtonDroppicsbtn extends JPlugin
{
    
        protected $do = true;
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{

            parent::__construct($subject, $config);
            JLoader::register('DroppicsBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/droppicsBase.php');
            if(!class_exists('DroppicsBase')){
                $this->do = false;
            }
            $lang = JFactory::getLanguage();
            $lang->load('plg_editors-xtd_droppicsbtn',JPATH_PLUGINS.'/editors-xtd/droppicsbtn',null,true);
            $lang->load('plg_editors-xtd_droppicsbtn.sys',JPATH_PLUGINS.'/editors-xtd/droppicsbtn',null,true);
            
            // Access check.
            if (!JFactory::getUser()->authorise('core.manage', 'com_droppics')) {
                $this->do = false;
            }
        }


	/**
	 * Display the button
	 *
	 * @return array A four element array of (code)
	 */
	function onDisplay($name)
	{
            
                if(!$this->do){
                    return '';
                }

		$doc = JFactory::getDocument();
                if(JFactory::getApplication()->isAdmin()){
                    $doc->addStyleDeclaration('.button2-left .droppics {
                        background: url('.JURI::root(true).'/components/com_droppics/assets/images/j_button2_droppics.png) 100% 0 no-repeat;
                    }');
                }
        $doc->addStyleDeclaration('.icon-droppics:before {content: "\2f";}');
        $doc->addStyleDeclaration('.mce-window-head {border-bottom: 1px !important ;}');
                
		JHtml::_('behavior.modal');
                
		/*
		 * Use the built-in element view to select the article.
		 * Currently uses blank class.
		 */
        $path = urlencode(JURI::root(true)) ;

		$link = 'index.php?option=com_droppics&amp;tmpl=component&amp;'.JSession::getFormToken().'=1&amp;e_name=' . $name . '&amp;caninsert=1&amp;template=system&amp;path='.$path;
                
                
		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('class', 'btn');
		$button->set('text', JText::_('PLG_DROPPICS_BUTTON'));
		$button->set('name', 'droppics');
		$button->set('options', "{handler: 'iframe', size: {x: (window.getSize().x*90/100), y: (window.getSize().y-50)}}");

		return $button;
	}
}
