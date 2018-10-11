<?php
defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();

// previous and following month names and links
$followingMonth = $this->datamodel->getFollowingMonth($this->data);
$precedingMonth = $this->datamodel->getPrecedingMonth($this->data);

$compname = JEV_COM_COMPONENT;
$viewname = $this->getViewName();
$viewpath = JURI::root() . "components/$compname/views/" . $viewname . "/assets";
$viewimages = $viewpath . "/images";

// previous and following month names and links
$followingMonth = $this->datamodel->getFollowingMonth($this->data);
$precedingMonth = $this->datamodel->getPrecedingMonth($this->data);
?>
<div class="jev_toprow jev_monthv">
    <div class="jev_header2">
        <div class="previousmonth" >
            <?php echo "<a href='" . $precedingMonth["link"] . "' title='" . $precedingMonth['name'] . "' style='text-decoration:none;'>" . $precedingMonth['name'] . "</a>"; ?>
        </div>
        <div class="currentmonth">
            <?php echo $this->data['fieldsetText']; ?>
        </div>
        <div class="nextmonth">
            <?php echo "<a href='" . $followingMonth["link"] . "' title='" . $followingMonth['name'] . "' style='text-decoration:none;'>" . $followingMonth['name'] . "</a>"; ?>
        </div>

    </div>
    <div class="jev_clear"></div>
</div>

<div class="jev_clear" ></div>
<div id='jev_maincal' class='jev_listview'>

    <?php
    $hasevents = false;
    $datacount = count($this->data["dates"]);
    for ($d = 0; $d < $datacount; $d++) {
        if ($this->data["dates"][$d]["monthType"] != "current") {
            continue;
        }
        $num_events = count($this->data['dates'][$d]['events']);
        if ($num_events > 0) {
            $hasevents = true;
            ?>
            <div class="jev_daysnames">
                <?php echo JEventsHTML::getDateFormat($this->data['dates'][$d]['year'], $this->data['dates'][$d]['month'], $this->data['dates'][$d]['d'], 1); ?>
            </div>
            <div class="jev_listrow">
                <?php
                for ($r = 0; $r < $num_events; $r++) {
                    echo "<ul class='ev_ul'>\n";

                    for ($r = 0; $r < $num_events; $r++) {
                        $row = $this->data['dates'][$d]['events'][$r];

                        $listyle = 'style="border-color:' . $row->bgcolor() . ';"';
                        echo "<li class='ev_td_li' $listyle>\n";

                        if (!$this->loadedFromTemplate('icalevent.list_row', $row, 0)) {
                            $this->viewEventRowNew($row);
                            echo "&nbsp;::&nbsp;";
                            $this->viewEventCatRowNew($row);
                        }
                        echo "</li>\n";
                    }
                    echo "</ul>\n";
                }
                echo '</div>';
            }
        }
        if (!$hasevents) {
            echo '<div class="list_no_e">' . "\n";
            echo JText::_('JEV_NO_EVENTS_FOUND');
            echo "</div>\n";
        }
        ?>
        <div class="jev_clear" ></div>
    </div>
    <?php
    $this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
    