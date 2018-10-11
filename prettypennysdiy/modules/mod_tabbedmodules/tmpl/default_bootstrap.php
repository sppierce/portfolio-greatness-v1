<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$modid = $module->id;
$position = $params->get("position", "left");
$preloaded = $params->get("preload", 1) ? "true" : "false";
$user = JFactory::getUser();
$url = JURI::base(false) . "modules/mod_tabbedmodules/fetchmodule.php";

$tabtitles = array();
$tabcontents = array();

foreach (JModuleHelper::getModules($position) as $mod)
{
	if (!is_object($mod))
	{
		$mod = JModuleHelper::getModule($mod, $title);
	}

	if (is_object($mod))
	{

		// Latest Events Title fix!
		modTabbedmodulesHelper::fixLatestEventsModule($mod);

		$document = JFactory::getDocument();
		$renderer = $document->loadRenderer('module');
		$tempparams = array('style' => -2);
		// always preload the first tab
		if ($params->get("preload", 1) || count($tabtitles) == 0)
		{
			$mod->tabcontent = $renderer->render($mod, $tempparams);
		}
		else
		{
			$mod->tabcontent = "";
		}

		// wrap latest events module
		modTabbedmodulesHelper::wrapLatestEventsModule($mod);

		$tabtitles[] = $mod;
	}
}

// only output if there are any modules to display
if (count($tabtitles) == 0)
	return "";

modTabbedmodulesHelper::stylesheet("mod_tabbedmodules_bootstrap.css");

//JHtml::_('behavior.framework', true);
JHtml::_('bootstrap.framework');

$tabselection = $params->get("tabbedorselect", 0);
?>
<div class="adminform form-horizontal" id="tabbed_events" >
	<ul class="nav nav-tabs moduletabs_title" id="myEditTabs<?php echo $modid; ?>">
		<?php
		$active = true;
		$count = 0;
		foreach ($tabtitles as $tabtitle)
		{
			$title = isset($tabtitle->tabtitle) ? $tabtitle->tabtitle : $tabtitle->title;
			$activeclass = $active ? 'active' : 'inactive';
			$activeclass .= ($tabtitle->module == "mod_jevents_latest" ? ' events' : '');
			?>
			<li class="<?php echo $activeclass; ?>"><a data-toggle="tab" href="#<?php echo $tabtitle->module . "_". $tabtitle->id; ?>" id="title_<?php echo $tabtitle->module . "_".  $tabtitle->id; ?>"><?php echo $title; ?></a></li>
			<?php
			$count++;
			$active = false;
		}
		?>
	</ul>
	<?php
	echo JHtml::_('bootstrap.startPane', 'myEditTabs' . $modid, array('active' => $tabtitles[0]->module .  "_" . $tabtitles[0]->id));

	foreach ($tabtitles as $tabtitle)
	{
		echo JHtml::_('bootstrap.addPanel', 'myEditTabs' . $modid, $tabtitle->module . "_".$tabtitle->id);
		echo $tabtitle->tabcontent;
		echo JHtml::_('bootstrap.endPanel');
	}
	echo JHtml::_('bootstrap.endPane', 'myEditTabs' . $modid);
	?>
</div >
<script>
	(function($) {
		$('a[data-toggle="tab"]').on('show', function(e) {
			// activated tab
			var tabid = $(e.target).prop("id").replace('#', '');
			var json_tabid = tabid;
			var tabparts = tabid.split("_");
			if(tabparts.length>0){
				json_tabid = tabparts[tabparts.length-1]
			}
			tabid = tabid.replace('title_', '');
			var tabcontent = $("#"+tabid);
			// no need to reload the same data 
			if (tabcontent.html() != ""){
				return;
			}
			
			var requestObject = new Object();
			requestObject.json = new Object();
			requestObject.json.error = false;
			requestObject.json.modid = json_tabid;

			$.ajax({
				dataType: "json",
				url: "<?php echo $url;?>",
				data: requestObject,
				success: function(returnData){
					var tabcontent = $("#"+tabid);
					tabcontent.html(returnData.modvalue);
				}
			});
		})
	})(jQuery);
</script>

