<?php 
defined('_JEXEC') or die('Restricted access');

$data = $this->data;

$Itemid = JEVHelper::getItemid();

echo "<div id='cal_title'>". JText::_('JEV_EVENTSFOR') ."</div>\n";
?>
<table width="90%" class="ev_table jev_<?php echo $this->colourscheme;?>">
    <tr >
        <td colspan="2"   class="cal_td_daysnames  cal_td_daysnames_<?php echo $this->colourscheme;?>  jev_<?php echo $this->colourscheme;?>">
           <!-- <div class="cal_daysnames"> -->
		<?php echo $this->dateFormattedDateRange();?>
            <!-- </div> -->
        </td>
    </tr>
    <?php
    $num_events = count($data['rows']);
    $chdate ="";
    if( $num_events > 0 ){

    	for( $r = 0; $r < $num_events; $r++ ){
    		$row = $data['rows'][$r];

    		$event_day_month_year 	= $row->dup() . $row->mup() . $row->yup();
    		// Ensure we reflect multiday setting
    		if (!$row->eventOnDate(JevDate::mktime(0,0,0,$row->mup(),$row->dup(),$row->yup()))) continue;

    		if(( $event_day_month_year <> $chdate ) && $chdate <> '' ){
    			echo '</ul></td></tr>' . "\n";
    		}

    		if( $event_day_month_year <> $chdate ){
    			$date =JEventsHTML::getDateFormat( $row->yup(), $row->mup(), $row->dup(), 1 );
    			echo '<tr><td class="ev_td_left">'.$date.'</td>' . "\n";
    			echo '<td class="ev_td_right"><ul class="ev_ul">' . "\n";
    		}

    		$listyle = 'style="border-color:'.$row->bgcolor().';"';
    		echo "<li class='ev_td_li' $listyle>\n";
    		$this->loadedFromTemplate('icalevent.list_row', $row, 0);
    		echo "</li>\n";

    		$chdate = $event_day_month_year;
    	}
    	echo "</ul></td></tr>\n";
    } 
    echo '</table><br />' . "\n";
    echo '<br /><br />' . "\n";

    // Create the pagination object
    if ($data["total"]>$data["limit"]){
    	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
    }
