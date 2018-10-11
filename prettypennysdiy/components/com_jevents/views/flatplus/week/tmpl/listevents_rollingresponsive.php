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

$option = JEV_COM_COMPONENT;
$Itemid = JEVHelper::getItemid();

$followingWeek = $this->datamodel->getFollowingWeek($this->year, $this->month, $this->day);
$precedingWeek = $this->datamodel->getPrecedingWeek($this->year, $this->month, $this->day);

?>
<div class="jev_toprow jev_monthv">
    <div class="jev_header2">
        <div class="previousmonth" >
            <?php if ($precedingWeek) echo "<a href='" . $precedingWeek . "' title='" . JText::_("PRECEEDING_Week") . "' >" . JText::_("PRECEEDING_Week") . "</a>"; ?>
        </div>
        <div class="currentmonth">
            <?php
            $week_start = $data ['days'] ['0'];
            $week_end = $data ['days'] ['6'];

            $starttime = JevDate::mktime(0, 0, 0, $week_start ['week_month'], $week_start ['week_day'], $week_start ['week_year']);
            $endtime = JevDate::mktime(0, 0, 0, $week_end ['week_month'], $week_end ['week_day'], $week_end ['week_year']);

            if ($week_start ['week_month'] == $week_end ['week_month']) {
                $startformat = "%d";
                $endformat = "%d %B, %Y";
            } else if ($week_start ['week_year'] == $week_end ['week_year']) {
                $startformat = "%d %B";
                $endformat = "%d %B, %Y";
            } else {
                $startformat = "%d. %B  %Y";
                $endformat = "%d. %B %Y";
            }
            echo JEV_CommonFunctions::jev_strftime($startformat, $starttime) . ' - ' . JEV_CommonFunctions::jev_strftime($endformat, $endtime);
            ?>
        </div>
        <div class="nextmonth">
            <?php if ($followingWeek) echo "<a href='" . $followingWeek . "' title='" . JText::_("FOLLOWING_Week") . "' >" . JText::_("FOLLOWING_Week") . "</a>"; ?>
        </div>

    </div>
    <div class="jev_clear"></div>
</div>
<div class="jev_clear" ></div>
<div id="jev_maincal" class="jev_listview">
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
                
                echo '<div class="jev_daysnames"> ' . $day_link . '</div>';
                echo '<div class="jev_listrow">';

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
		echo '</div>' . "\n";
	} // end for days
	unset($data);
	$week++;
}
?>
</div>