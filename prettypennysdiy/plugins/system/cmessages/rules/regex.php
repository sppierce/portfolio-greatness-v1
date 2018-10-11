<?php
/**
 * @version		$Id: cmessages.php conflate $
 * @package		Handle system messages
 * @copyright	(C) 2013 Conflate. All rights reserved.
 * @license		GNU/GPL 2.0
 * @author		Conflate <info@conflate.nl>
 */

defined('_JEXEC') or die('Restricted access');

//Needed for J1.7
jimport('joomla.form.formrule');

class JFormRuleRegex extends JFormRule{
	
	public function test(&$element, $value, $group = null, &$input = null, &$form = null){
		$this->regex = $element['regex'];
		$this->modifiers = $element['modifiers'];
		
		return $value ? parent::test($element, $value, $group, $input, $form) : true;
		
	}
}
