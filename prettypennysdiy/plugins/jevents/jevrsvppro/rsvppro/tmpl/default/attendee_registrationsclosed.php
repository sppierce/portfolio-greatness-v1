<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if ($this->jomsocial){
	echo  '<div class="cModule jevattendform"><h3><span>'.JText::_( 'JEV_REGISTRATIONS_CLOSED' ).'</span></h3>'. "</div>";
}
else echo "<strong>".JText::_("JEV_REGISTRATIONS_CLOSED")."</strong><br/>";
