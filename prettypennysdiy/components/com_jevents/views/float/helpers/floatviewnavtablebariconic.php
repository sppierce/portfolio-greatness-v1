<?php
defined('_JEXEC') or die('Restricted access');

class FloatViewNavTableBarIconic {

    var $view = null;

    function __construct($view, $today_date, $view_date, $dates, $alts, $option, $task, $Itemid)
    {
        global $catidsOut;
        $jinput = JFactory::getApplication()->input;

        if ($jinput->getInt('pop', 0))
            return;
        $cfg = JEVConfig::getInstance();
        $compname = JEV_COM_COMPONENT;
        $jevcparams = JComponentHelper::getParams(JEV_COM_COMPONENT);

        //Lets check if we should show the nav on event details
        if ($task == "icalrepeat.detail" && $cfg->get('shownavbar_detail', 1) == 0) {
            return;
        }

        $this->iconstoshow = $cfg->get('iconstoshow', array('byyear', 'bymonth', 'byweek', 'byday', 'search'));
        $viewimages = JURI::root() . "components/" . JEV_COM_COMPONENT . "/views/" . $view->getViewName() . "/assets/images";

        $cat = "";
        $hiddencat = "";
        if ($catidsOut != 0) {
            $cat = '&catids=' . $catidsOut;
            $hiddencat = '<input type="hidden" name="catids" value="' . $catidsOut . '"/>';
        }

        $link = 'index.php?option=' . $option . '&task=' . $task . $cat . '&Itemid=' . $Itemid . '&';
        $month_date = (JevDate::mktime(0, 0, 0, $view_date->month, $view_date->day, $view_date->year));
    }

}
