<?php
/**
 * @version		$Id: cmessages.php conflate $
 * @package		Handle system messages
 * @copyright	(C) 2013 Conflate. All rights reserved.
 * @license		GNU/GPL 2.0
 * @author		Conflate <info@conflate.nl>
 */

defined('_JEXEC') or die('Restricted access');

class plgSystemCMessages extends JPlugin{

	private $_messageOutput = null;
	
	private $_renderedMessages = null;

	public function onBeforeRender(){
		$app = JFactory::getApplication();
		if ( $app->isAdmin() ) {
			return;
		}
		
		$messageQueue = $app->getMessageQueue();
		
		if(is_array($messageQueue) && !empty($messageQueue)){
			$doc = JFactory::getDocument();
			$position = $this->params->get('message_position', 'center');
			
			//group the messages by type
			$messages = array();
			foreach($messageQueue as $message){
				if($message['message'] == '') continue;
				$messages[$message['type']][] = $message;
			}
			if(empty($messages)) return;
			
			if(!$this->params->get('use_renderer', false)){
				$output = '' . PHP_EOL;
			
				foreach($messages as $type => $msgs){
					$output .= '<div class="message ' . $type . '">' . PHP_EOL
						. '<div class="title"><h4>' . JText::_($type) . '</h4></div>' . PHP_EOL;
					foreach($msgs as $message){
						$output .= '<div class="body"><p>' . $message['message'] . '</p></div>' . PHP_EOL;
							
					}
					$output .= '</div>' . PHP_EOL;
				}
			}else{
				$output = $this->_getRenderedMessages();
			}
			
			//Should the message output be removed from the page
			if($this->params->get('clear_template', false)){
				if(version_compare(JVERSION,'3.0.0','<')){
					//In previous Joomla version, we're able to access the message queue to clear it. It's safer than removing the rendered output
					$app->set('_messageQueue', array());
				}
			}
			
			$this->_messageOutput = '<div id="cmessages-container">' . PHP_EOL 
										. '<div class="close"><a href="#"></a></div>'
										. '<div class="messages">' . PHP_EOL
											. $output . PHP_EOL 
										. '</div>' . PHP_EOL
									. '</div>' . PHP_EOL
									. '';
			$top = $this->params->get('specify_top', '');
			$right = $this->params->get('specify_right', '');
			$bottom = $this->params->get('specify_bottom', '');
			$left = $this->params->get('specify_left', '');
	
			$width = $this->params->get('specify_width', '');
			$height = $this->params->get('specify_height', '');
	
			$autohide = $this->params->get('autohide_seconds', 0);
			$fade = $this->params->get('fade_effects', true);
			$dock = $this->params->get('dock', false);
	
			$jquery = $this->params->get('use_jquery', false);
			if($jquery){
				if(version_compare(JVERSION,'3.0.0','ge')){
					JHtml::_('jquery.framework');
				}
				$doc->addScript('/media/cmessages/js/cmessages.jquery.js');
			}else{
				JHtml::_('behavior.framework', true);
				$doc->addScript('/media/cmessages/js/cmessages.js');
			}
	
			$script = "var cmessages_position = '" . $position . "';" . PHP_EOL
					. "var cmessages_autohide = " . ($autohide?$autohide:'false') . ";" . PHP_EOL
					. "var cmessages_fade = " . ($fade?'true':'false') . ";" . PHP_EOL
					. "var cmessages_dock = " . ($dock?'true':'false') . ";" . PHP_EOL
					. ($position == 'specify' ? ''
						. ($top != ''?"var cmessages_top = '" . $top . "';" . PHP_EOL : "")
						. ($right != ''?"var cmessages_right = '" . $right . "';" . PHP_EOL : "")
						. ($bottom != ''?"var cmessages_bottom = '" . $bottom . "';" . PHP_EOL : "")
						. ($left != ''?"var cmessages_left = '" . $left . "';" . PHP_EOL : "")
					: '')
					. ($position == 'center' || $position == 'specify'  ? ''
						. ($width != ''?"var cmessages_width = '" . $width . "';" . PHP_EOL : "")
						. ($height != ''?"var cmessages_height = '" . $height . "';" . PHP_EOL : "")
					: '')
					. '';
			$doc->addScriptDeclaration($script);
			
			//add styles
			$doc->addStyleSheet('/media/cmessages/css/cmessages.css');
			$style = $this->params->get('set_style', 'default');
			if($style && file_exists(JPATH_SITE . '/media/cmessages/css/styles/' . $style . '.css')){
				$doc->addStyleSheet('/media/cmessages/css/styles/' . $style . '.css');
			}
		}
	}
	
	public function onAfterRender(){
		if($this->_messageOutput){
			$app = JFactory::getApplication();
			$body = $app->getBody();
			
			//Should the message output be removed from the template
			if($this->params->get('clear_template', false)){
				if(version_compare(JVERSION,'3.0.0','ge')){
					//find and replace the output with the output in the template. It should be the same and unique
					//Unfortunately we cannot access any variables in the buffer since Joomla 3, and we have to do it the ugly way
					$output = $this->_getRenderedMessages();
					$body = str_replace($output, '', $body);
				}
			}
		
			$position = $this->params->get('message_position', 'specify');
			switch($position){
				case 'top':
				case 'center':
				case 'specify':
					$body = preg_replace('/(<body.*?>)/is', "$1" . $this->_messageOutput, $body);
					break;
				case 'bottom':
					$body = preg_replace('/(<\/body>)/i', $this->_messageOutput . "\n$1", $body);
					break;

			}
		
			$app->setBody($body);
		}
	}
	
	//This function will retreive the messages from the message rendered by Joomla
	private function _getRenderedMessages(){
		if(!$this->_renderedMessages){
			$doc = JFactory::getDocument();
			$renderer = $doc->loadRenderer('message');
			$this->_renderedMessages = $renderer->render('');
		}
		return $this->_renderedMessages;
	}
	
}
