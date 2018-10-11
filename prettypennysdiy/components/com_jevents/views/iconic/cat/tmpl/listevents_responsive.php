<?php
defined('_JEXEC') or die('Restricted access');

$cfg = JEVConfig::getInstance();

$data = $this->datamodel->getCatData($this->catids, $cfg->get('com_showrepeats', 0), $this->limit, $this->limitstart);
$this->data = $data;

$Itemid = JEVHelper::getItemid();
?>
<div id='jev_maincal' class='jev_listview jev_<?php echo $this->colourscheme; ?> jev_catlist'>
	<div class="jev_toprow" style="height:34px;">
		<div class="jev_header">
			<h2><span><?php echo JText::_('CATEGORY_VIEW'); ?></span></h2>
			<div class="today" > <?php $this->viewNavCatText($this->catids, JEV_COM_COMPONENT, 'cat.listevents', $this->Itemid); ?></div>
		</div>
	</div>
	<div class="jev_clear" ></div>
	<div class="jev_daysnames jev_daysnames_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>">&nbsp;</div>
	<?php
	if (strlen($data['catdesc']) > 0)
	{
		?>
		<div class='jev_catdesc'><?php echo $data['catdesc']; ?></div>
		<div class="jev_clear" ></div>
		<?php
	}


	$num_events = count($data['rows']);
	$chdate = "";
	if ($num_events > 0)
	{
		for ($r = 0; $r < $num_events; $r++) {
			$row = $data['rows'][$r];

			$Itemid = JEVHelper::getItemid();
			$cat = "";
			if ($this->datamodel->catidsOut != 0){
				$cat = '&catids='.$this->datamodel->catidsOut;
			}

			$datestp	= JevDate::mktime( 0, 0, 0,$row->mup(),$row->dup(),$row->yup());

			$lnk = JRoute::_( 'index.php?option='.JEV_COM_COMPONENT.'&task=day.listevents&year='.$row->yup().'&month='.$row->mup().'&day='.$row->dup().'&Itemid='.$Itemid . $cat);
			//$day_link = $this->dateicon(array($days,$month),JText::_('JEV_CLICK_TOSWITCH_DAY'),$link, "", $row);
			$day_link = $this->dateicon(explode(":",JEV_CommonFunctions::jev_strftime("%d:%b",$datestp)),JText::_('JEV_CLICK_TOSWITCH_DAY'),$lnk, "",$row);
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("colourbar",0)) $listyle = 'style="border-color:'.$row->bgcolor().';"';
			else $listyle = 'style="border:none"';
			?>
			<div class="jev_listrow">
			<div class="jevleft jevleft_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>">
				<?php echo $day_link;?>
			</div>
			<?php
			echo "<div  class='jevright' $listyle>";
			if (!$this->loadedFromTemplate('icalevent.list_row', $row, 0)){
				$this->viewEventRowNEW ($row);
				echo "&nbsp;::&nbsp;";
				$this->viewEventCatRowNEW ($row);
			}
			echo "</div>";
			echo '</div>';
		}

	}
	else
	{
		?>
		<div class="jev_listrow  jev_noresults">
			<div class="jevleft jevleft_<?php echo $this->colourscheme;?> jev_<?php echo $this->colourscheme;?>">
			</div>
			<div  class='jevright' style="border:none">
				<?php
				if (count($this->catids) == 0 || $data['catname'] == "")
				{
					echo JText::_('JEV_EVENT_CHOOSE_CATEG') . '';
				}
				else
				{
					echo JText::_('JEV_NO_EVENTFOR') . '&nbsp;<b>' . $data['catname'] . '</b>';
				}
				?>
			</div>
		</div>
		<?php
	}

	?>	
</div>
<?php
$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
