<?php
defined('_JEXEC') or die('Restricted access');

function FlatPlusEventIcalButton($view, $row)
{
	JEVHelper::script('view_detail.js', 'components/' . JEV_COM_COMPONENT . "/assets/js/");
	?>
	<a href="#myical-modal" data-target="#ical_dialogJQ<?php echo $row->rp_id(); ?>" data-toggle="modal" title="<?php echo JText::_('JEV_SAVEICAL'); ?>">
		<img src="<?php echo JURI::root() . 'components/' . JEV_COM_COMPONENT . '/views/flatplus/assets/images/ical_download.png' ?>" name="image"  alt="<?php echo JText::_('JEV_SAVEICAL'); ?>" class="jev_ev_sml nothumb"/>
	</a>
	<?php

}
