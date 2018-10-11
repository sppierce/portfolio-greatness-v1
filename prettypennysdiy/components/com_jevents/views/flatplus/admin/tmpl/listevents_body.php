<?php
defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();

$this->data = $data = $this->datamodel->getDataForAdmin($this->creator_id, $this->limit, $this->limitstart);

$frontendPublish = intval($cfg->get('com_frontendPublish', 0)) > 0;

$num_events = count($data['rows']);
$chdate = '';

//Start Admin Page
?>
<div id="jev_ad">
<?PHP

//Admin Panel Title
echo '<div class="jev_ad_title"><h2>' . JText::_('JEV_ADMINPANEL') . '</h2></div>';

$myItemid = JEVHelper::getAdminItemid();
$form_link = JRoute::_(
    'index.php?option=' . JEV_COM_COMPONENT
    . '&task=admin.listevents'
    . "&Itemid=" . $myItemid
    , false);

// Start the date range filter
?>
    <div class="jev_ad_range_filter">
        <form action="<?php echo $form_link; ?>" method="post">

            <?php
            $filters = jevFilterProcessing::getInstance(array("startdate"));
            $filterHTML = $filters->getFilterHTML();
            foreach ($filterHTML as $filter) {
                echo "<div class='jev_adminfilter'>" . $filter["title"] . "<br/>" . $filter["html"] . "</div>";
            }
            ?>
        </form>
    </div>
    <div class="jev_ad_listrow">
        <?php

        if ($num_events > 0) {
            for ($r = 0; $r < $num_events; $r++) {
                $row = $data['rows'][$r];
                $event_month_year = $row->dup() . $row->mup() . $row->yup();

                if ($event_month_year <> $chdate && $chdate <> "") {
                    echo '</ul></td></tr>' . "\n";
                }
                if ($event_month_year <> $chdate) {
                    ?>
                    <div class="jev_daysnames_container">
                        <div class="jev_daysnames">
                            <?php echo JEventsHTML::getDateFormat($row->yup(), $row->mup(), $row->dup(), 1); ?>
                        </div>
                    </div>
                    <?PHP
                    echo '<ul class="ev_ul">' . "\n";
                }

                $this->viewEventRowAdmin($row);
                $chdate = $event_month_year;
            }
            echo '</ul>' . "\n";
        } else {
            echo '<div class="jev_ad_nevents">';
                echo "<p>" .JText::_('JEV_NO_EVENTS') . "</p>";
            echo '</div>';
        }
        echo '</tr>';
        ?>
    </div>
</div>
<?PHP
//End Admin PAge


// Create the pagination object
if ($data["total"] > $data["limit"]) {
    $this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
}
