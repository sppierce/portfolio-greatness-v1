<?php
defined('_JEXEC') or die('Restricted access');

function RuthinViewHelperHeader16($view)
{

	$task = JRequest::getString("jevtask");
	$view->loadModules("jevprejevents");
	$view->loadModules("jevprejevents_" . $task);

	$dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger('onJEventsHeader', array($view));

	$cfg = JEVConfig::getInstance();
	$version = JEventsVersion::getInstance();
	$jevtype = JRequest::getVar('jevtype');
	$evid = JRequest::getInt('evid');
	$pop = JRequest::getInt('pop', 0);
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

	$view->copyrightComment();


	// stop crawler and set meta tag
	JEVHelper::checkRobotsMetaTag();

	// Call the MetaTag setter function.
	if (is_callable(array("JEVHelper", "SetMetaTags")))
	{
		JEVHelper::SetMetaTags();
	}

	$lang = JFactory::getLanguage();
	?>
	<div id="jevents">
	<div class="contentpaneopen jeventpage<?php
	echo $params->get('pageclass_sfx');
	echo $params->get("darktemplate", 0) ? " jeventsdark" : " ";
	echo $lang->isRTL() ? " jevrtl" : " ";
	?> jevbootstrap" id="jevents_header">
			<?php if ($params->get('show_page_heading', 0)) : ?>
			<h1>
			<?php echo $view->escape($params->get('page_heading')); ?>
			</h1>
		<?php endif; ?>
		<?php
		$t_headline = '';
		switch ($cfg->get('com_calHeadline', 'comp')) {
			case 'none':
				$t_headline = '';
				break;
			case 'menu':
				$menu2 =  JFactory::getApplication()->getMenu();
				$menu = $menu2->getActive();
				if (isset($menu) && isset($menu->title))
				{
					$t_headline = $menu->title;
				}
				break;
			default:
				$t_headline = JText::_('JEV_EVENT_CALENDAR');
				break;
		}
		if ($t_headline != "")
		{
			?>
			<h2 class="contentheading" ><?php echo $t_headline; ?></h2>
			<?php
		}
		$task = JRequest::getString("jevtask");
		$info = "";

		if ($cfg->get('com_print_icon_view', 1) || $cfg->get('com_email_icon_view', 1) || strpos($info, "<li>") !== false)
		{
			?>
			<ul class="actions">
				<?php
				if ($cfg->get('com_print_icon_view', 1))
				{
					$print_link = 'index.php?option=' . JEV_COM_COMPONENT
							. '&task=' . $task
							. ($evid ? '&evid=' . $evid : '')
							. ($jevtype ? '&jevtype=' . $jevtype : '')
							. ($view->year ? '&year=' . $view->year : '')
							. ($view->month ? '&month=' . $view->month : '')
							. ($view->day ? '&day=' . $view->day : '')
							. $view->datamodel->getItemidLink()
							. $view->datamodel->getCatidsOutLink()
							. '&pop=1'
							. '&tmpl=component';
					$print_link = JRoute::_($print_link);

					if ($pop)
					{
						?>
						<li class="print-icon">
							<a href="javascript:void(0);" rel="nofollow" onclick="javascript:window.print();
													return false;" title="<?php echo JText::_('JEV_CMN_PRINT'); ?>">
						<span class="icon-print"> </span>
							</a>
						</li> <?php
					}
					else
					{
						?>
						<li class="print-icon">
							<a href="javascript:void(0);" rel="nofollow" onclick="window.open('<?php echo $print_link; ?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=600,height=600,directories=no,location=no');" title="<?php echo JText::_('JEV_CMN_PRINT'); ?>">
						<span class="icon-print"> </span>
							</a>
						</li> <?php
					}
				}
				if ($cfg->get('com_email_icon_view', 1))
				{

					$task = JRequest::getString("jevtask");
					$link = 'index.php?option=' . JEV_COM_COMPONENT
							. '&task=' . $task
							. ($evid ? '&evid=' . $evid : '')
							. ($jevtype ? '&jevtype=' . $jevtype : '')
							. ($view->year ? '&year=' . $view->year : '')
							. ($view->month ? '&month=' . $view->month : '')
							. ($view->day ? '&day=' . $view->day : '')
							. $view->datamodel->getItemidLink()
							. $view->datamodel->getCatidsOutLink()
					;
					$link = JRoute::_($link);
					//if (strpos($link,"/")===0) $link = JString::substr($link,1);
					$uri =  JURI::getInstance(JURI::base());
					$root = $uri->toString(array('scheme', 'host', 'port'));

					$link = $root . $link;
					require_once(JPATH_SITE . '/' . 'components' . '/' . 'com_mailto' . '/' . 'helpers' . '/' . 'mailto.php');
					$url = JRoute::_('index.php?option=com_mailto&tmpl=component&link=' . MailToHelper::addLink($link));
					?>
					<li class="email-icon">
						<a href="javascript:void(0);" rel="nofollow" onclick="javascript:window.open('<?php echo $url; ?>', 'emailwin', 'width=400,height=350,menubar=yes,resizable=yes');
												return false;" title="<?php echo JText::_('EMAIL'); ?>">
							<span class="icon-envelope"> </span>
						</a>
					</li>
					<?php
				}
				echo $info;
				?>
			</ul>
		<?php
	}
	?>
	</div>
	<?php
	$view->loadModules("jevprejevents2");
	$view->loadModules("jevprejevents2_" . $task);
	?>
	<div class="contentpaneopen  jeventpage<?php
		echo $params->get('pageclass_sfx');
		echo $params->get("darktemplate", 0) ? " jeventsdark" : " ";
		echo $lang->isRTL() ? " jevrtl" : " ";
		?>  jevbootstrap" id="jevents_body">
	<?php
	if (($params->get('ruscalable', 0) == 1 || $params->get("ruthinwidth", 905) == "scalable") && (($task == "month.calendar" && !$params->get('rulistmonth', 0)) || ($task == "week.listevents" && $params->get('rutabularweek', 0)) ))
	{
		$baseurl = JURI::root();
		$version = JEventsVersion::getInstance();
		$release = $version->get("RELEASE", "1.0.0");
		?>
		<script type="text/javascript"><!--
			var myCSS = false;
			var processedClones = false;
			function setJEventsSize() {

				var jeventsBody = jevjq("#jevents_body");
				var jeventsBodyParent = jeventsBody.parent();
				var sizex = jeventsBodyParent.width();
				var narrow = false;
				var theme = "ruthin";

				if (!myCSS) {
					if (sizex < 485) {
						myCSS = jevjq("<link>");
						jevjq("head").append(myCSS); //IE hack: append before setting href

						myCSS.attr({
						  rel:  "stylesheet",
						  type: "text/css",
                                                  // including these stops the CSS from being parsed for some reason !!!
						 // id: 'myStyle',
						 // title: 'myStyle',
						  href: '<?php echo $baseurl; ?>components/com_jevents/views/'+theme+'/assets/css/narrowscalable.css?<?php echo $release;?>'
						});
						narrow = true;
					}
				}
				else {
					if (sizex < 485) {
						myCSS.attr('href', '<?php echo $baseurl; ?>components/com_jevents/views/'+theme+'/assets/css/narrowscalable.css?<?php echo $release;?>');
						narrow = true;
					}
					else {
						myCSS.attr('href','<?php echo $baseurl; ?>components/com_jevents/views/'+theme+'/assets/css/scalable.css?<?php echo $release;?>');
						narrow = false;
					}
				}
				if (narrow) {
					cloneEvents();
					var listrowblock = jevjq(".jev_listrowblock");
					if (listrowblock) {
						listrowblock.css('display', "block");
					}
				}
				else {
					var listrowblock = jevjq(".jev_listrowblock");
					if (listrowblock) {
						listrowblock.css('display', "none");
					}
					setTimeout(setOutOfMonthSize, 1000);
				}
			}
			function setOutOfMonthSize() {
				jevjq(".jev_dayoutofmonth").each(
					function(idx, el) {
						if (jevjq(el).parent().hasClass("slots1")) {
							jevjq(el).css('min-height', "81px");
						}
						else {
							var psizey = jevjq(el).parent().height();
							jevjq(el).css('min-height', psizey + "px");
						}
					}, this);
			}
			function cloneEvents() {
				if (!processedClones) {
					processedClones = true;

					var myEvents = jevjq(".eventfull");
					// sort these to be safe!!
					myEvents.sort(function(a, b) {
						if (!a.sortval) {
							var aparentclasses = jevjq(a).parent().attr('class').split(" ");
							for (var i = 0; i < aparentclasses.length; i++) {
								if (aparentclasses[i].indexOf("jevstart_") >= 0) {
									a.sortval = aparentclasses[i].replace("jevstart_", "");
								}
							}
						}
						if (!b.sortval) {
							var bparentclasses = jevjq(b).parent().attr('class').split(" ");
							for (var i = 0; i < bparentclasses.length; i++) {
								if (bparentclasses[i].indexOf("jevstart_") >= 0) {
									b.sortval = bparentclasses[i].replace("jevstart_", "");
								}
							}
						}
						if (a.sortval == b.sortval) {
							var asiblings = jevjq(a).parent().children();
							for (var i = 0; i < asiblings.length; i++) {
								if (jevjq(asiblings[i]).attr('class') && jevjq(asiblings[i]).attr('class').indexOf("hiddendayname") >= 0) {
									return -1;
								}
							}
							var bsiblings = jevjq(b).parent().children();
							for (var i = 0; i < bsiblings.length; i++) {
								if (jevjq(bsiblings[i]).attr('class') && jevjq(bsiblings[i]).attr('class').indexOf("hiddendayname") >= 0) {
									return 1;
								}
							}
						}
						return (a.sortval < b.sortval) ? -1 : (a.sortval > b.sortval) ? 1 : 0;
						//return a.sortval>b.sortval;
					});

					if (myEvents.length == 0) {
						return;
					}
					var listrowblock = jevjq('<div>', {'class': 'jev_listrowblock'});

					var event_legend_container = jevjq(".event_legend_container");
					if (event_legend_container.length && jevjq("#jevents_body .event_legend_container").length) {
						listrowblock.insertBefore(event_legend_container);
					}
					else {
						var toprow = jevjq("#jev_maincal .jev_toprow");
						listrowblock.insertAfter(toprow);
						var clearrow = jevjq('<div>', {'class': 'jev_clear'});
						clearrow.insertAfter(listrowblock);
					}

					var listrow = jevjq('<div>', {'class': 'jev_listrow'});

					var hasdaynames = false;
					myEvents.each(function(idx, el) {
						if (!hasdaynames) {
							var dayname = jevjq(el).parent().find(".hiddendayname");
							if (dayname) {
								hasdaynames = true;
							}
						}
					});

					myEvents.each(function(idx, el) {

						var dayname = jevjq(el).parent().find(".hiddendayname");
						if (dayname) {
							dayname.css('display', "block");
							dayname.appendTo(listrowblock);
						}
						if (dayname || !hasdaynames) {
							// really should be for each separate date!
							listrow = jevjq('<div>', {'class': 'jev_listrow'});
							listrow.css('margin-bottom',"10px");
							listrow.css('margin-top',"5px");
							listrow.appendTo(listrowblock);
						}

						var hiddentime = jevjq(el).parent().find(".hiddentime").first();
						hiddentime = hiddentime.find("a");
						hiddentime.removeClass("hiddentime");
						hiddentime.addClass("showntime");
						hiddentime.appendTo(listrow);

						var myClone = jevjq(el).parent().clone();
						/*
						var extraDays = 0;
						if (myClone.hasClass("jevblocks2")){
							extraDays = 1;
						}
						if (myClone.hasClass("jevblocks3")){
							extraDays = 2;
						}
						if (myClone.hasClass("jevblocks4")){
							extraDays = 3;
						}
						if (myClone.hasClass("jevblocks5")){
							extraDays = 4;
						}
						if (myClone.hasClass("jevblocks6")){
							extraDays = 5;
						}
						if (myClone.hasClass("jevblocks7")){
							extraDays = 6;
						}
						*/
						myClone.addClass("jev_daywithevents");
						myClone.removeClass("jev_dayoutofmonth");
						myClone.removeClass("jevblocks0");
						myClone.removeClass("jevblocks1");
						myClone.removeClass("jevblocks2");
						myClone.removeClass("jevblocks3");
						myClone.removeClass("jevblocks4");
						myClone.removeClass("jevblocks5");
						myClone.removeClass("jevblocks6");
						myClone.removeClass("jevblocks7");
						myClone.css('height' ,"inherit");
						myClone.appendTo(listrow);
						/*
						for (var ex=0;ex<extraDays;ex++) {
							jevjq('<div>', {'class': 'jev_clear'}).appendTo(listrow);
							hiddentime = jevjq(el).parent().find(".hiddentime"+(ex+1));
							hiddentime = hiddentime.find("a");
							hiddentime.removeClass("hiddentime");
							hiddentime.addClass("showntime");
							hiddentime.appendTo(listrow);
							myClone.clone().appendTo(listrow);
						}
						*/

						var clearrow = jevjq('<div>', {'class': 'jev_clear'});
						clearrow.appendTo(listrow);
					});
				}
			}
			jevjq(document).ready(setJEventsSize);
			// set load event too incase template sets its own domready trigger
			jevjq(document).load(setJEventsSize);
			jevjq(window).resize(setJEventsSize);
			//-->
		</script>

		<?php
	}

}
