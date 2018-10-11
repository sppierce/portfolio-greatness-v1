<?php
// No direct access
defined('_JEXEC') or die;

jimport("joomla.filesystem.file");
$templates = JFolder::files(JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/templates/", ".xml");
// only offer extra fields templates if there is more than one available
$this->rows = array();
if (count($templates) > 1)
{
	// What uses this custom fields file
        $useoptions = array();
        $useoptions[] = JHTML::_('select.option', "0", JText::_("JEV_CUSTOM_FIELDS_SELECT_USAGE"), 'val', 'text');                
        
	$db = JFactory::getDBO();
	$db->setQuery('SELECT folder AS type, element AS name, params  FROM #__extensions  where folder="jevents" and element="jevcustomfields" ');
	$plugin = $db->loadObject();
	$jevplugin = false;
	if (!is_null($plugin))
	{
		$params = new JRegistry($plugin->params);
		$jevplugin = $params->get("template", false);
	}
        $useoptions[] = JHTML::_('select.option', "com_jevents", JText::_("COM_JEVENTS"), 'val', 'text');                

	// Managed Locations
        $locationtemplate = "MANAGED LOCATIONS ARE NOT INSTALLED";
	if (JComponentHelper::isInstalled("com_jevlocations")) {
		$locparams = JComponentHelper::getParams("com_jevlocations");
		$locationtemplate = $locparams->get("fieldtemplate",false);
                $useoptions[] = JHTML::_('select.option', "com_jevlocations", JText::_("COM_JEVLOCATIONS"), 'val', 'text');                
	}

	// Managed People
        $peoptemplate = "MANAGED PEOPLE ARE NOT INSTALLED";
        $ptypes = array();
	if (JComponentHelper::isInstalled("com_jevpeople")) {
		$peopparams = JComponentHelper::getParams("com_jevpeople");
		$peoptemplate = $peopparams->get("template",false);
                $useoptions[] = JHTML::_('select.option', "com_jevpeople",  JText::_("COM_JEVPEOPLE"), 'val', 'text');
		// Now the types
		$db->setQuery("SELECT * FROM #__jev_peopletypes where typetemplate<>'' ");
		$ptypes = $db->loadObjectList();
	}

        foreach ($ptypes as $ptype) {
            $useoptions[] = JHTML::_('select.option', "com_jevpeople_type.".$ptype->type_id, $ptype->title, 'val', 'text');
	}

        $rowcount = 0;
	foreach ($templates as $template)
	{
		if ($template == "fieldssample.xml" || $template == "fieldssample16.xml" || $template == "all_fields.xml")
			continue;
		$row = new stdClass();
		$row->name = ucfirst(str_replace(".xml", "", $template));
		$row->file = $template;
		$row->active = array();
		$file = JPATH_SITE . "/plugins/jevents/jevcustomfields/customfields/templates/".$template;
		$xml = simplexml_load_file($file);
		
		$row->description = $xml->description ? $xml->description : "";

		if ($row->file == $jevplugin)
		{
			$row->active[] = JText::_("JEV_INSTAL_MAIN");
		}

		if ($row->file == $locationtemplate )
		{
			$lang->load("com_jevlocations", JPATH_ADMINISTRATOR);
			$row->active[] = JText::_("COM_JEVLOCATIONS");
		}

		if ($row->file == $peoptemplate)
		{
			$lang->load("com_jevpeople", JPATH_ADMINISTRATOR);
			$row->active[] = JText::_("COM_JEVPEOPLE");
		}

		foreach ($ptypes as $ptype){
			if ($row->file == $ptype->typetemplate){
				$row->active[] = $ptype->title;
			}
		}

		$row->active = implode("<br/>",$row->active );
                
                $row->usein = JHTML::_('select.genericlist', $useoptions, 'tempusage', " onchange='jQuery(\"#adminForm input[type=checkbox]\").attr(\"checked\", false);jQuery(this).closest(\"tr\").find(\"input[type=checkbox]\").attr(\"checked\", true);jQuery(\"#usage\").val(jQuery(this).val());Joomla.submitbutton(\"plugin.jev_customfields.setusage\");' ", 'val', 'text', 0);
               
		$this->rows[] = $row;
                $rowcount ++;
	}
}

$input = JFactory::getApplication()->input;
$this->orderdir  = $input->getCmd("filter_order_Dir", "asc");
$this->order = $input->getCmd("filter_order", "name");

usort($this->rows, function ($a, $b) {
	$input = JFactory::getApplication()->input;
	$key = $input->getCmd("filter_order", "name");
	$orderdir  = $input->getCmd("filter_order_Dir","asc");
	if ($orderdir =="asc"){
		return strcasecmp($a->$key , $b->$key );
	}
	else {
		return -1 * strcasecmp($a->$key , $b->$key );
	}
});

if (!empty($this->sidebar))
{
	$version = JEventsVersion::getInstance();
	?>
	<div id="j-sidebar-container" class="span2">

		<?php echo $this->sidebar; ?>

		<?php
		//Version Checking etc
		?>
		<div class="jev_version">
	<?php echo JText::sprintf('JEV_CURRENT_VERSION', JString::substr($version->getShortVersion(), 1)); ?>
		</div>
	</div>
	<?php
}

$mainspan = 10;
$fullspan = 12;
?>
<div id="jevents" class="span12">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="span<?php echo (!empty($this->sidebar)) ? $mainspan : $fullspan; ?>  ">
			<h2><?php	echo $this->title;?></h2>

			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist   table table-striped">
				<tr>
					<th width="20" nowrap="nowrap">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>

					<th class="title" width="20%" nowrap="nowrap">
<?php echo JHtml::_('grid.sort', 'JEV_CUSTOM_FIELDS_NAME', 'name', $this->orderdir, $this->order, "plugin.jev_customfields.overview"); ?>
					</th>
					<th width="60%" nowrap="nowrap">
<?php echo JHtml::_('grid.sort', 'JEV_CUSTOM_FIELDS_DECRIPTION', 'description', $this->orderdir, $this->order, "plugin.jev_customfields.overview"); ?>
					</th>
					<th width="20%" nowrap="nowrap">
<?php echo JHtml::_('grid.sort', 'JEV_CUSTOM_FIELDS_ACTIVE', 'active', $this->orderdir, $this->order, "plugin.jev_customfields.overview"); ?>
					</th>
					<th width="20%" nowrap="nowrap">
                                            <?php echo JText::_('JEV_CUSTOM_FIELDS_USE_IN')?>
					</th>
				</tr>

				<?php
				$k = 0;

				for ($i = 0, $n = count($this->rows); $i < $n; $i++)
				{
					$row = &$this->rows[$i];
					$editlink = JRoute::_("index.php?option=com_jevents&task=plugin.jev_customfields.edit&cfname[]=". $row->file);
					?>
					<tr class="row<?php echo $k; ?>">
						<td width="20">
							<input type="checkbox" id="cb<?php echo $i; ?>" name="cfname[]" value="<?php echo $row->file; ?>"
								   onclick="Joomla.isChecked(this.checked);"/>
						</td>
						<td>
							<a href="<?php echo $editlink; ?>" title="<?php echo JText::_('JEV_CLICK_TO_EDIT'); ?>"><?php echo $row->name; ?></a>
						</td>
						<td style="text-align:left">
	<?php echo $row->description; ?>
						</td>
						<td style="text-align:left">
	<?php echo $row->active; ?>
						</td>
						<td style="text-align:left">
	<?php echo $row->usein; ?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
			</table>
			<input type="hidden" name="option" value="com_jevents"/>
			<input type="hidden" name="task" id="task" value="plugin.jev_customfields.overview"/>
			<input type="hidden" name="usage" id="usage" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->orderdir; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>