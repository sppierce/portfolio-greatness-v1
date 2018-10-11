<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	 = JEVConfig::getInstance();

$this->data = $data = $this->datamodel->getDayData( $this->year, $this->month, $this->day );
$this->Redirectdetail();

$cfg = JEVConfig::getInstance();
$Itemid = JEVHelper::getItemid();

echo '<table class="ev_table">' . "\n";
?>
    <tr >
        <td colspan="2"  class="cal_td_daysnames cal_td_daysnames_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>">
           <!-- <div class="cal_daysnames"> -->
            <?php echo JEventsHTML::getDateFormat( $this->year, $this->month, $this->day, 0) ;?>
            <!-- </div> -->
        </td>
    </tr>
<?php
// Timeless Events First
if (count($data['hours']['timeless']['events'])>0){
	$start_time = JText::_( 'TIMELESS' );

	echo '<tr><td class="ev_td_left">' . $start_time . '</td>' . "\n";
	echo '<td class="ev_td_right"><ul class="ev_ul">' . "\n";
	foreach ($data['hours']['timeless']['events'] as $row) {
		$listyle = 'style="border-color:'.$row->bgcolor().';"';
		echo "<li class='ev_td_li' $listyle>\n";

		$this->loadedFromTemplate('icalevent.list_row', $row, 0);
		echo "</li>\n";
	}
	echo "</ul></td></tr>\n";
}

for ($h=0;$h<24;$h++){
	if (count($data['hours'][$h]['events'])>0){
		$start_time = JEVHelper::getTime($data['hours'][$h]['hour_start']);

		echo '<tr><td class="ev_td_left">' . $start_time . '</td>' . "\n";
		echo '<td class="ev_td_right"><ul class="ev_ul">' . "\n";
		foreach ($data['hours'][$h]['events'] as $row) {
			$listyle = 'style="border-color:'.$row->bgcolor().';"';
			echo "<li class='ev_td_li' $listyle>\n";
			$this->loadedFromTemplate('icalevent.list_row', $row, 0);
			echo "</li>\n";
		}
		echo "</ul></td></tr>\n";
	}
}
echo '</table><br />' . "\n";

//  $this->showNavTableText(10, 10, $num_events, $offset, '');

