<?php
/**
 * @version		1.0
 * @package		Floating Social Bar
 * @subpackage	Content
 * @copyright	Copyright (C) 2014 - 2015 JoomlaKave. All rights reserved.
 * @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class plgContentfloatingsocialbar extends JPlugin {
function onContentAfterDisplay($context, &$article, &$params, $limitstart)
	{

		$this->fb_url   			=  $this->params->get('fb_url','');
		$this->tw_url   			=  $this->params->get('tw_url','');
		$this->pin_url   			=  $this->params->get('pin_url','');
		$this->inst_url				=  $this->params->get('inst_url','');
		$this->googleplus_url   	=  $this->params->get('googleplus_url','');
		$this->mail_url   			=  $this->params->get('mail_url','');
		$this->show_to 				=  $this->params->get('show_to','right');
//add your plugin codes here
		$document = JFactory::getDocument();
		

		$socialcssadd = '.socialcss{
			        position: fixed;
    				top: 16em;
    				z-index: 9999;
    				'.$this->show_to.': -0.3em;
		}';
		$document->addStyleDeclaration($socialcssadd);		

		$output = '';

		$imgroot = JURI::base().'plugins/content/floatingsocialbar/image/social-icons-fixed.png';

		$output .= '<div class="socialcss">
		<img src="'.$imgroot.'" alt="" usemap="#map">
		<map name="map">
    		<area shape="poly" coords="50, 11, 16, 33, 52, 61" title="Facebook" href="'.$this->fb_url.'" target="_blank">
    		<area shape="poly" coords="13, 36, 14, 89, 54, 64" title="Twitter" href="'.$this->tw_url.'" target="_blank">
    		<area shape="poly" coords="51, 65, 16, 92, 51, 115" title="Pinterest" href="'.$this->pin_url.'" target="_blank">
    		<area shape="poly" coords="14, 94, 14, 147, 52, 120" title="Instagram" href="'.$this->inst_url.'" target="_blank">
    		<area shape="poly" coords="52, 120, 13, 150, 49, 175" title="Google Plue" href="'.$this->googleplus_url.'" target="_blank">
    		<area shape="poly" coords="14, 152, 14, 200, 51, 178" title="Mail" href="'.$this->mail_url.'" target="_blank">
  		</map></div>';


		return $output;
		
		}

}
?>