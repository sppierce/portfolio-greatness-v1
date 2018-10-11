<?php

defined('_JEXEC') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 */
class JFormFieldDroppicsCss extends JFormField
{

    protected $type = 'DroppicsCss';

    protected function getLabel()
    {

        return '';
    }

    /**
     */
    protected function getInput()
    {
        $html = '
		<style>
		.droppics_wtm .checkbox{
			 display: block;
             float: left;
             padding-right: 10px;
             padding-left: 22px;
             text-indent: 4px;
		}
		.droppics_wtm input {
          vertical-align: middle;
        }
        .droppics_wtm label span {
          vertical-align: middle;
        }
		</style>
		';
        return $html;
    }

}
