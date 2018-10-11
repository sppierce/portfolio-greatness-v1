<?php
defined('_JEXEC') or die('Restricted access');
if ($this->jevparams->get("mapplacement", 1) == 1)
{
	echo $this->jevoutput;
}
$this->gmap($this);
if ($this->jevparams->get("mapplacement", 1) == 0)
{
	echo $this->jevoutput;
}

echo $this->adminpanel;

echo $this->copyright;
