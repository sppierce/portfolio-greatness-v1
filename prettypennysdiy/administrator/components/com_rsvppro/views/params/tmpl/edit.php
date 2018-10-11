<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: edit16.php 2983 2011-11-10 14:02:23Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.bootstrap');

?>

<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm">

	<fieldset class='jevconfig'>
		<legend>
			<?php echo JText::_('RSVP_CONFIG'); ?>
		</legend>

		<ul class="nav nav-tabs" id="myParamsTabs">
			<?php
			$fieldSets = $this->form->getFieldsets();
			$first = true;
			foreach ($fieldSets as $name => $fieldSet)
			{
				if ($name == "permissions" || $name == "noop")
				{
					continue;
				}
				$label = empty($fieldSet->label) ? $name : $fieldSet->label;
				if ($first)
				{
					$first = false;
					$class = ' class="active"';
				}
				else
				{
					$class = '';
				}
				?>
				<li <?php echo $class; ?>><a data-toggle="tab" href="#<?php echo $name; ?>"><?php echo JText::_($label); ?></a></li>
				<?php
			}
			  ?>
		</ul>

		<?php
		echo JHtml::_('bootstrap.startPane', 'myParamsTabs', array('active' => 'RSVP_ATTENDANCE_OPTIONS'));
		$fieldSets = $this->form->getFieldsets();

		foreach ($fieldSets as $name => $fieldSet)
		{
			$label = empty($fieldSet->label) ? $name : $fieldSet->label;
			echo JHtml::_('bootstrap.addPanel', "myParamsTabs", $name);

			$html = array();

			$html[] = '<fieldset class="form-horizontal">';
                        
                         // $html[] = "<legend>".JText::_($label)."</legend>";
                        if (isset($fieldSet->description) && !empty($fieldSet->description))
			{
				$desc = JText::_($fieldSet->description);
				$html[] = '<p class="paramlist_description" >' . $desc . '</p>';
			}
                        
			foreach ($this->form->getFieldset($name) as $field)
			{
				if ($field->hidden)
				{
					continue;
				}
				$class = isset($field->class) ? $field->class : "";

				if (strlen($class) > 0)
				{
					$class = " class='$class control-group'";
				}
                                else {
                                    $class = " class='control-group '";
                                }
                                $showonstring = $field->getAttribute('showon');
                                $datashowon = "";
                                if ($showonstring)
                                {

                                        JHtml::_('jquery.framework');
                                        JHtml::_('script', 'jui/cms.js', false, true);
                                        $showonarr = array();

                                        foreach (preg_split('%\[AND\]|\[OR\]%', $showonstring) as $showonfield)
                                        {
                                                $showon   = explode(':', $showonfield, 2);
                                                $showonarr[] = array(
                                                        'field'  => "jform[".$showon[0]."]",
                                                        'values' => explode(',', $showon[1]),
                                                        'op'     => (preg_match('%\[(AND|OR)\]' . $showonfield . '%', $showonstring, $matches)) ? $matches[1] : ''
                                                );
                                        }
                                        $datashowon = ' data-showon=\'' . json_encode($showonarr) . '\'';
                                }

                                $html[] = "<div $class $datashowon >";
                                $l = $field->label;
                                $i = $field->input;
				if (trim($l)!=""  && $name != "JCONFIG_PERMISSIONS_LABEL")
				{
                                    $html[] = "<div class='control-label paramlist_key'>$field->label</div>";
                                    if (strpos($field->input, 'class="controls"')==false) {
                                        $html[] = '<div class="controls">' . $field->input . '</div>';                                    
                                    }
                                    else {
                                        $html[] = $field->input;
                                    }
				}                                
                                else {
                                    if (strpos($field->input, 'class="controls"')==false) {
                                        $html[] = '<div class="controls nolabel">' . $field->input . '</div>';                                    
                                    }
                                    else {
                                        $html[] = $field->input;
                                    }
                                }

                                $html[] = "</div>";
    			}

			$html[] = '</fieldset>';

			echo implode("\n", $html);
			?>

			<?php
			echo JHtml::_('bootstrap.endPanel');
		}
		echo JHtml::_('bootstrap.endPane', 'myParamsTabs');
		?>


	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
	<input type="hidden" name="component" value="<?php echo $this->component->option; ?>" />

	<input type="hidden" name="controller" value="component" />
	<input type="hidden" name="option" value="<?php echo RSVP_COM_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>



