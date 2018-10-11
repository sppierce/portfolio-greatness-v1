<?php
defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();
// for adding events in day cell
$this->popup = false;
if ($cfg->get("editpopup", 0))
{
	JEVHelper::script('components/'.JEV_COM_COMPONENT.'/assets/js/editpopup.js');
	$this->popup = true;
	$this->popupw = $cfg->get("popupw", 800);
	$this->popuph = $cfg->get("popuph", 600);
}

$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();

$compname = JEV_COM_COMPONENT;
$viewname = $this->getViewName();
$viewpath = JURI::root() . "components/$compname/views/" . $viewname . "/assets";
$viewimages = $viewpath . "/images";

echo $this->loadTemplate('cell');
$eventCellClass = "EventCalendarCell_" . $viewname;

$data1 = $this->datamodel->getWeekData($this->year, $this->month, $this->day);
$data2 = $this->datamodel->getWeekData($this->year, $this->month, $this->day + 7);
$data3 = $this->datamodel->getWeekData($this->year, $this->month, $this->day + 14);
$data4 = $this->datamodel->getWeekData($this->year, $this->month, $this->day + 21);
$data1['enddate'] = $data4['enddate'];

// previous and following month names and links
$precedingWeek = $this->datamodel->getPrecedingWeek($this->year, $this->month, $this->day);
$followingWeek = $this->datamodel->getFollowingWeek($this->year, $this->month, $this->day+21);

$startday = $cfg->get('com_starday');
if (!$startday)
{
	$startday = 0;
}
$data1['startday'] = $startday;
$data1["daynames"] = array();
for ($i = 0; $i < 7; $i++)
{
	$data1["daynames"][$i] = JEventsHTML::getDayName(($i + $startday) % 7, true);
}
?>
<div id='jev_maincal' class='jev_<?php echo $this->colourscheme; ?>'>
	<div class="jev_toprow">
		<div class="jev_header">
			<h2><span><?php echo JText::_('WEEKLY_VIEW'); ?></span></h2>
			<div class="today" ><span><?php echo $data1['startdate'] . ' - ' . $data1['enddate']; ?></span></div>
		</div>
		<div class="jev_header2">
			<div class="previousmonth" >
				<span>
					<?php
					if ($precedingWeek)
						echo "<a href='" . $precedingWeek . "' title='" . JText::_("PRECEEDING_Week") . "' >" . JText::_("PRECEEDING_Week") . "</a>";
					?>
				</span>
			</div>
			<div class="currentmonth">
				<span>
					<?php echo $data1['startdate'] . ' - ' . $data1['enddate']; ?>
				</span>
			</div>
			<div class="nextmonth">
				<span>
					<?php
					if ($followingWeek)
						echo "<a href='" . $followingWeek . "' title='" . JText::_("FOLLOWING_Week") . "' >" . JText::_("FOLLOWING_Week");
					?></a>
				</span>
			</div>

			<?php
			$dc = 0;
			foreach ($data1["daynames"] as $dayname)
			{
				?>
				<div class="jev_daysnames jev_daysnames_<?php echo $this->colourscheme; ?> jev_<?php echo $this->colourscheme; ?>  jev_daysnames<?php echo $dc; ?> ">
					<span>
						<?php echo $dayname; ?>
					</span>
				</div>
				<?php
				$dc++;
			}
			?>

		</div>
	</div>
	<div class="jev_clear" ></div>

	<?php
	for ($w = 1; $w <= 4; $w++)
	{
		unset($data);
		$data = "data" . $w;
		$data = & $$data;

// Sort out the events so we only show the max display number
		$maxdisplay = $cfg->get('com_calMaxDisplay', 5);
		$datacount = 7;
		for ($dn = 0; $dn < $datacount; $dn++)
		{
			if (count($data["days"][$dn]["rows"]) > $maxdisplay)
			{
				$data["days"][$dn]["rows"] = array_slice($data["days"][$dn]["rows"], 0, $maxdisplay);
				$data["days"][$dn]["capped"] = true;
			}
			else
			{
				$data["days"][$dn]["capped"] = false;
			}
		}

// setup and allocate slots if necessary NB slots hold the events that occur on that date
		if (!isset($data['days'][0]["slots"]))
		{
			for ($slot = 0; $slot < 7; $slot++)
			{
				$data['days'][$slot]["slots"] = array();
			}
		}
		for ($dn = 0; $dn < 7; $dn++)
		{

			unset($currentDay);
			$currentDay = & $data['days'][$dn];
			$currentDay['countDisplay'] = 0;

			$currentDay ["cellDate"] = JevDate::mktime(0, 0, 0, $data['days'][$dn]['week_month'], $data['days'][$dn]['week_day'], $data['days'][$dn]['week_year']);
			$dayOfWeek = JevDate::strftime("%w", $currentDay ["cellDate"]);

			$weekstartday = $cfg->get('com_starday');
			if ((!$weekstartday ) || ( $weekstartday > 1 ))
			{
				$weekstartday = 0;
			}
			// adjust day of week to reflect start day in config
			$dayOfWeek -= $weekstartday;
			if ($dayOfWeek < 0)
			{
				$dayOfWeek+=7;
			}

			// I need to sort the events by start date order (not start time on the day)
			usort($data['days'][$dn]["rows"], array($this, "sortjevents"));

			for ($i = 0; $i < count($data['days'][$dn]["rows"]); $i++)
			{
				unset($event);
				$event = & $currentDay["rows"][$i];

				// find first empty slot for this event
				// If second/third week for event the slot needs to be reset first
				// This clearly doens't apply to multiday evenyts only diusplaying on first day
				if (isset($event->slot_to_use) && $event->slot_to_use > 0 && !isset($event->slotreset) && !$event->multiday())
				{
					$old_slot_to_use = $event->slot_to_use;
					for ($spc = 0; $spc < $event->_length && $dn + $spc < 7; $spc++)
					{
						if (isset($data['days'][$dn + $spc]["slots"][$old_slot_to_use]))
						{
							$data['days'][$dn + $spc]["slots"][$old_slot_to_use] = array(0, 0, 0);
						}
					}
					$event->slotreset = 1;
				}

				$slot_to_use = nextEmptySlot($data['days'][$dn]);
				$event->slot_to_use = $slot_to_use;

				// simplest case first - single day events
				// or multiday events set to only show once and it is the first day
				if ($event->endDate() == $event->startDate() || (!$event->multiday() && $currentDay["cellDate"] == $event->_startday))
				{
					// put the event in its slot
					$currentDay["slots"][$slot_to_use] = array($event, 1, $i);
				}

				if ($event->endDate() != $event->startDate() && $event->multiday() && !isset($event->_length))
				{
					// started last week?
					if ($dn == 0 && $currentDay["cellDate"] != $event->_startday)
					{
						$event->_length = JevDate::strtotime($event->endDate()) - $currentDay["cellDate"];
					}
					else
					{
						$event->_length = JevDate::strtotime($event->endDate()) - JevDate::strtotime($event->startDate());
					}
					$event->_length = intval(round($event->_length / 86400, 0)) + 1;

					// Must allow for events that started BEFORE the month or week in hand
					if ($currentDay["cellDate"] == $event->_startday || ($dn == 0 && $currentDay["cellDate"] > $event->_startday))
					//if ($currentDay["cellDate"] == $event->_startday  )
					{
						if ($dayOfWeek + $event->_length > 6)
						{
							$blocks = 7 - $dayOfWeek;
						}
						else
						{
							$blocks = $event->_length;
						}
						$data['days'][$dn]["slots"][$slot_to_use] = array($event, $blocks, $i);
						for ($block = 1; $block < $blocks; $block++)
						{
							$data['days'][$dn + $block]["slots"][$slot_to_use] = array($event, 0, $i);
						}
					}
				}
				else
				{
					$event->_length = 1;
				}
				// mark event as shown
				$event->_shown = true;

			}


		}

// determine rowspan in advance
		$weekslots = 0;
		$dn = 0;
		for ($d = 0; $d < 7 && $dn < $datacount; $d++)
		{
			unset($currentDay);
			$currentDay = $data["days"][$dn];
			if (count($currentDay["slots"]) > 0)
			{
				$weekslots = $weekslots < max(array_keys($currentDay["slots"])) + 1 ? max(array_keys($currentDay["slots"])) + 1 : $weekslots;
			}
			$dn++;
		}
		$dn = 0;
		$dn2 = 0;
		$dn3 = 0;
		?>
		<div class="jev_row">
			<div class="interim">			
				<div class="jevdaydata">
					<?php
					$dn2 = $dn;
					for ($d = 0; $d < 7 && $dn < 7; $d++)
					{
						unset($currentDay);
						$currentDay = $data["days"][$dn];
						$cellclass = $currentDay["today"] ? 'jev_daynum_today jev_daynum' : ' jev_daynum';

						$cellclass.= $d == 6 ? ' jev_lastdaynum' : '';
						?>
						<div <?php echo 'class="' . $cellclass . '"'; ?>  >
							<?php $this->_datecellAddEvent($currentDay["week_year"], $currentDay["week_month"], $currentDay["week_day"]); ?>
							<a class="cal_daylink" href="<?php echo $currentDay["link"]; ?>" title="<?php echo JText::_('JEV_CLICK_TOSWITCH_DAY'); ?>"><?php echo $currentDay['week_day']; ?></a>
						</div>
						<?php
						$dn++;
					}
					?>
				</div>
				<?php
				for ($slot = 0; $slot < $weekslots; $slot++)
				{
					?>

					<div class="jeveventrow slots<?php echo $weekslots; ?>">
						<?php
						$dn3 = $dn2;
						for ($d = 0; $d < 7 && $dn3 < 7; $d++)
						{
							unset($currentDay);
							$currentDay = $data["days"][$dn3];
							$cellclass = $currentDay["today"] ? 'jev_today jevblocks1' : ' jevblocks1';

							if (array_key_exists($slot, $currentDay["slots"]))
							{
								$event = $currentDay["slots"][$slot][0];
								$blocks = $currentDay["slots"][$slot][1];
								$key = $currentDay["slots"][$slot][2];
								// reset class to include block count
								// reset class to include block count
								$withorwithout = $blocks > 0 ? "jev_daywithevents" : "jev_daynoevents";
								$cellclass = $currentDay["today"] ? 'jev_today jevblocks' . $blocks : $withorwithout . ' jevblocks' . $blocks;

								if ($blocks > 0)
								{
									echo '<div class="' . $cellclass . '" >';
									$datestp = JevDate::mktime(0, 0, 0, $currentDay['week_month'], $currentDay['week_day'], $currentDay['week_year']);
									$day_link = $this->dateicon(explode(":", JEV_CommonFunctions::jev_strftime("%d:%b", $datestp)), JText::_('JEV_CLICK_TOSWITCH_DAY'), $currentDay['link'], "", $event);
									echo "<span class='hiddenicon'>$day_link</span>";
									$ecc = new $eventCellClass($event, $this->datamodel, $this);
									echo $ecc->calendarCell($currentDay, $this->year, $this->month, $key, $slot);
									//echo $event->_summary. " ".$currentDay["slots"][$slot][1]." ".JevDate::strftime("%d",$event->_startday);
									echo '</div>';
									$currentDay['countDisplay']++;
								}
								else if (!$event)
								{
									echo "<div class='$cellclass' >&nbsp;</div>";
								}
							}
							else
							{
								echo "<div class='$cellclass' >&nbsp;</div>";
							}
							$dn3++;
						}
						?>
					</div>
					<?php
				}

				// Are any of these days capped
				$dn3 = $dn2;
				$capped = false;
				for ($d = 0; $d < 7 && $dn3 < 7; $d++)
				{
					unset($currentDay);
					$currentDay = $data["days"][$dn3];
					if ($currentDay["capped"])
						$capped = true;
					$dn3++;
				}
				// if capped then offer the link to more events
				if ($capped)
				{
					?>
					<div class="jeveventrow slots<?php echo $weekslots + 1; ?>">
						<?php
						$dn3 = $dn2;
						for ($d = 0; $d < 7 && $dn3 < 7; $d++)
						{
							unset($currentDay);
							$currentDay = $data["days"][$dn3];
							$withorwithout = $currentDay["capped"] ? "jev_daywithevents" : "jev_daynoevents";
							$cellclass = $currentDay["today"] ? 'jev_today jevblocks1' : $withorwithout . ' jevblocks1';
							if ($currentDay["capped"])
							{
								echo "<div class='$cellclass' style='text-align:right'><span style='margin-right:5px'>";
								echo '<a class="cal_daylink" href="' . $currentDay["link"] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">' . JText::_('JEV_MORE') . ' ...</a></span>';
								echo '</div>';
							}
							else
							{
								echo "<div class='$cellclass' >&nbsp;</div>";
							}
							$dn3++;
						}
						?>
					</div>
					<?php
				}
				?>

				<div class="jev_underlay">
					<?php
					// Are any of these days capped
					$dn4 = $dn2;
					$capped = false;
					for ($d = 0; $d < 7 && $dn4 < $datacount; $d++)
					{
						unset($currentDay);
						$currentDay = $data["days"][$dn4];
						$class = "jev_underlay_daynum";
						if ($d == 0)
						{
							$class .= " jev_underlay_firstdaynum";
						}
						else if ($d == 6)
						{
							$class .= " jev_underlay_lastdaynum";
						}
						if ($currentDay["today"])
						{
							$class .= " jev_underlay_daynum_today";
						}
						$monthType = "current";
						$testym = $currentDay["week_year"] . $currentDay["week_month"];
						$startym = $data["days"][0]["week_year"] . $data["days"][0]["week_month"];
						$endym = $data["days"][6]["week_year"] . $data["days"][6]["week_month"];
						$testd = $currentDay["week_day"];
						$startd = $data["days"][0]["week_day"];
						$endd = $data["days"][6]["week_day"];
						// start or end of month
						if ($startym != $endym)
						{
							// end of month
							if ($endd > 20)
							{
								if ($testym == $endym)
								{
									$monthType = "following";
								}
							}
							// start of month
							else
							{
								if ($testym != $endym)
								{
									$monthType = "prior";
								}
							}
						}

						switch ($monthType) {
							case "prior":
								$class .= " jev_underlay_outofmonth_start";
								break;
							case "following":
								$class .= " jev_underlay_outofmonth_end";
								break;
							case "current":
								break;
						}
						?>
						<div class="<?php echo $class; ?>">
							<div>&nbsp;</div>
						</div>
						<?php
						$dn4++;
					}
					?>

				</div>			

				<div class="jev_clear"></div>

			</div>
		</div>
	<?php
}
?>
	</div>

	<div class="jev_clear"></div>
<?php
$this->eventsLegend();

function nextEmptySlot($currentDay)
{
	if (!array_key_exists("slots", $currentDay) || count($currentDay["slots"]) == 0)
		return 0;
	$maxpossible = max(array_keys($currentDay["slots"])) + 1;
	for ($key = 0; $key <= $maxpossible; $key++)
	{

		if (!array_key_exists($key, $currentDay["slots"]) || !$currentDay["slots"][$key])
		{
			return $key;
		}
	}
	return $maxpossible;

}