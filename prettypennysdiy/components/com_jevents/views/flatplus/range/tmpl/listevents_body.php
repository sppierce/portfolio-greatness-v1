<?php
defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();
$Itemid = JEVHelper::getItemid();

$compname = JEV_COM_COMPONENT;
$viewname = $this->getViewName();
$viewpath = JURI::root() . "components/$compname/views/" . $viewname . "/assets";
$viewimages = $viewpath . "/images";

$view = $this->getViewName();
$data = $this->data;
?>
<div class="jev_toprow">
    <div class="jev_header2">
        <div class="previousmonth">
            <span>&ensp;</span>
        </div>
        <div class="currentmonth">
            <?php echo $this->dateFormattedDateRange();?>
        </div>
        <div class="nextmonth">
            <span>&ensp;</span>
        </div>

    </div>
    <div class="jev_clear"></div>
    </div>
<div id='jev_maincal' class='jev_listview range'>

    <?php
    $num_events = count($data['rows']);
    $chdate ="";
    ?>
    <div class="jev_listrow">
    <?php
    if( $num_events > 0 ){

        $hasevents = true;
        echo "<ul class='ev_ul'>\n";

    	for( $r = 0; $r < $num_events; $r++ ){
    		$row = $data['rows'][$r];

    		$event_day_month_year 	= $row->dup() . $row->mup() . $row->yup();
    		// Ensure we reflect multiday setting
    		if (!$row->eventOnDate(JevDate::mktime(0,0,0,$row->mup(),$row->dup(),$row->yup()))) continue;

    		//if(( $event_day_month_year <> $chdate ) && $chdate <> '' ){
    	    //		echo '</ul>' . "\n";
            //		}

   		if( $event_day_month_year <> $chdate ){
//    			$date =JEventsHTML::getDateFormat( $row->yup(), $row->mup(), $row->dup(), 1 );
//    			echo '<tr><td class="ev_td_left">'.$date.'</td>' . "\n";
//    			echo '<ul class="ev_ul">' . "\n";
            ?>
            <div class="jev_daysnames">
                <?php echo JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 1); ?>
            </div>
            <?php
  		}
            if ($row->bgcolor()) {
                $listyle = 'style="border-color:' . $row->bgcolor() . ';"';
            } else {
                $liststyle ="";
            }

    		echo "<li class='ev_td_li' $listyle>\n";
    		if (!$this->loadedFromTemplate('icalevent.list_row', $row, 0)){
    			$this->viewEventRowNew ( $row,'view_detail',JEV_COM_COMPONENT, $Itemid);
    		}
    		echo "</li>\n";

    		$chdate = $event_day_month_year;
    	}
    	echo "</ul>";
    }
    ?>
    </div>
    <?php

    if (!$hasevents) {
        echo '<div class="list_no_e">' . "\n";
        echo JText::_('JEV_NO_EVENTS_FOUND');
        echo "</div>\n";
    }
?>
<div class="jev_clear"></div>
</div>
<?
    // Create the pagination object
    if ($data["total"]>$data["limit"]){
    	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
    }
