<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldJevTextarea extends JFormField
{

        public function getInput()
        {
                $rows = $this->element['rows'] ? $this->element['rows'] : 0;
                $cols = $this->element['cols'] ? $this->element['cols'] : 0;
                $class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : 'class="text_area"';
                // convert <br /> tags so they are not visible when editing
                $this->value = str_replace('<br />', "\n", JText::_($this->value));
                if (strpos($this->value, "JEV ") === 0 || strpos($this->value, "JEV_") === 0)
                {
                        $this->value = JText::_($this->element->getAttribute('default'));
                }

                return '<textarea name="' . $this->name . '" cols="' . $cols . '" rows="' . $rows . '" ' . $class . ' id="' . $this->name . '" >' . $this->value . '</textarea>';
        }
}

