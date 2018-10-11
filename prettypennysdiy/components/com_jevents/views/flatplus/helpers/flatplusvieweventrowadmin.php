<?php
defined('_JEXEC') or die('Restricted access');

function FlatplusViewEventRowAdmin($view,$row, $manage=false){

    $popup=false;
    $params = JComponentHelper::getParams(JEV_COM_COMPONENT);
    $layout_path = JURI::base() . "/components/com_jevents/views/flatplus/assets/images/";
    if ($params->get("editpopup",0)){
        JEVHelper::script('editpopup.js','components/'.JEV_COM_COMPONENT.'/assets/js/');
        $popup=true;
        $popupw = $params->get("popupw",800);
        $popuph = $params->get("popuph",600);
    }

    $editLink = $row->editLink(true);
    $editLink = $popup?"javascript:jevEditPopup('".$editLink."');":$editLink;

    $modifylink = "";
    if (!$manage && JEVHelper::canEditEvent($row)){
        $modifylink = '<a href="' . $editLink . '" title="' . JText::_('JEV_MODIFY') . '"><img src="' . $layout_path . 'edit.png" alt="'. JText::_('JEV_MODIFY') . '"/></a>';
        $copy_modifylink = '<a href="' . $row->editCopyLink(false) . "&rettask=admin.listevents". '" title="' . JText::_('COPY_AND_EDIT_EVENT') . '"><img src="' . $layout_path . 'copy_edit.png" alt="'. JText::_('JEV_MODIFY') . '"/></a>';
    }

    $deletelink = "";
    if (!$manage && JEVHelper::canDeleteEvent($row)){
        $deletelink = '<a href="' . $row->deletelink(false)."&rettask=admin.listevents" . '" title="'. JText::_('JEV_DELETE') . '"><img src="' . $layout_path . 'delete.png" alt="'. JText::_('JEV_DELETE') . '"/></a>';
    }

    if (!$manage && JEVHelper::canPublishEvent($row)){
        if ($row->published()){
            $rstate = 'jev_ad_unpub';
            $publishlink = '<a href="' . $row->unpublishlink(false)."&rettask=admin.listevents" . '" title="' . JText::_( 'UNPUBLISH' ) . '"><img src="' . $layout_path . 'unpub.png" alt="'. JText::_('UNPUBLISH') . '"/></a>';
        }
        elseif ($row->published() == '-1') {

        } else {
            $rstate = 'jev_ad_pub';
            $publishlink = '<a href="' . $row->publishlink(false)."&rettask=admin.listevents" . '" title="' . JText::_( 'PUBLISH' ) . '"><img src="' . $layout_path . 'pub.png" alt="'. JText::_('PUBLISH') . '"/></a>';
        }
    }
    else {
        $publishlink = "";
    }

    $eventlink = $row->viewDetailLink($row->yup(),$row->mup(),$row->dup(),false);
    $eventlink = JRoute::_($eventlink.$view->datamodel->getCatidsOutLink());
    if ($row->bgcolor()) { $border="border-color:".$row->bgcolor(); } else {$border = "";}

    // Ok lets make sure she doesn't show trashed items some how with this condition
    if ($row->published() != '-1') {
    ?>

    <li class="ev_td_li <?PHP echo $rstate; ?>" style="<?php echo $border;?> ">
        <a class=" <?php echo $row->state() ? 'ev_link_row' : 'ev_link_unpublished'; ?>" href="<?php echo $eventlink; ?>" title="<?php echo JEventsHTML::special($row->title()) . ( $row->state() ? '' : JText::_('JEV_UNPUBLISHED') );?>"><?php echo $row->title() . ( $row->state() ? '' : JText::_('JEV_UNPUBLISHED') );?></a>
        &nbsp;<?php echo JText::_('JEV_BY');?>
        &nbsp;<i><?php echo $row->contactlink('',true);?></i>
        <span class="jev_admin_buttons">
        &nbsp;&nbsp;<?php echo $deletelink;?>
        &nbsp;&nbsp;<?php echo $modifylink;?>
        &nbsp;&nbsp;<?php echo $copy_modifylink;?>
        &nbsp;&nbsp;<?php echo $publishlink;?>
        </span>
    </li>
    <?php
    }
}
