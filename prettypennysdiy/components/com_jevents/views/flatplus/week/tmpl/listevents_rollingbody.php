<?php
defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();
$howManyWeeksToShow = intval($cfg->get('rollingweeks', 1));

//$this->data = $data = $this->datamodel->getWeekData($this->year, $this->month, $this->day);
$extradata = array();

// generate the extra data for each week to display
if ($howManyWeeksToShow)
{
	$extradata = array();
	$today = mktime(0, 0, 0, $this->month, $this->day, $this->year);
	for ($w = 0; $w < $howManyWeeksToShow; $w++)
	{
		list($y, $m, $d) = explode("-", strftime("%Y-%m-%d", $today));
		$extradata[$w] = $this->datamodel->getWeekData($y, $m, $d);
		if ($w == 0)
		{
			$this->data = $data = $extradata[$w];
		}
		$today += 604800;
	}
	// set the end date output correctly
	$extradata[0]["enddate"] = $data["enddate"] = $extradata[$howManyWeeksToShow - 1]["enddate"];
}
// make sure the weeks are in the correct sequence
ksort($extradata);

// previous and following month names and links
$followingWeek = $this->datamodel->getFollowingWeek($this->year, $this->month, $this->day + ($howManyWeeksToShow - 1) * 7);
$precedingWeek = $this->datamodel->getPrecedingWeek($this->year, $this->month, $this->day);

$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();

echo '<fieldset><legend class="ev_fieldset">' . JText::_('JEV_EVENTSFOR') . '&nbsp;' . JText::_('JEV_WEEK')
 . ' : </legend><br />' . "\n";
echo '<table align="center" cellspacing="0" cellpadding="5" class="ev_table">' . "\n";
?>
<tr valign="top">
	<td colspan="2"  align="center" class="cal_td_daysnames">
		<div class="cal_daysnames"> 
		<?php echo $data['startdate'] . ' - ' . $data['enddate']; ?>
		</div>
	</td>
</tr>
<?php
$week = 0;
foreach ($extradata as & $data)
{
	for ($d = 0; $d < 7; $d++)
	{

		$day_link = '<a class="ev_link_weekday" href="' . $data['days'][$d]['link'] . '" title="' . JText::_('JEV_CLICK_TOSWITCH_DAY') . '">'
				. JEventsHTML::getDateFormat($data['days'][$d]['week_year'], $data['days'][$d]['week_month'], $data['days'][$d]['week_day'], 2) . '</a>' . "\n";

		if ($data['days'][$d]['today'])
			$bg = 'class="ev_td_today"';
		else
			$bg = 'class="ev_td_left"';

		echo '<tr><td ' . $bg . '>' . $day_link . '</td>' . "\n";
		echo '<td class="ev_td_right">' . "\n";

		$num_events = count($data['days'][$d]['rows']);
		if ($num_events > 0)
		{

			echo "<ul class='ev_ul'>\n";

			for ($r = 0; $r < $num_events; $r++)
			{
				$row = $data['days'][$d]['rows'][$r];

				$listyle = 'style="border-color:' . $row->bgcolor() . ';"';
				echo "<li class='ev_td_li' $listyle>\n";

				if (!$this->loadedFromTemplate('icalevent.list_row', $row, 0))
				{
					$this->viewEventRowNew($row);
					echo "&nbsp;::&nbsp;";
					$this->viewEventCatRowNew($row);
				}
				echo "</li>\n";
			}
			echo "</ul>\n";
		}
		echo '</td></tr>' . "\n";
	} // end for days
	unset($data);
	$week++;
}


echo '</table><br />' . "\n";
echo '</fieldset><br /><br />' . "\n";
