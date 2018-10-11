<?php
defined('_JEXEC') or die('Restricted access');

function IconicViewHelperFooter16($view)
{
	if (JRequest::getInt('pop', 0))
	{
		?>
		<div class="ev_noprint"><p align="center">
				<a href="#close" onclick="if (window.parent == window) {
									self.close();
								} else
									try {window.parent.jQuery('#myEditModal').modal('hide');}catch (e){}
									try {
										window.parent.SqueezeBox.close();
										return false;
									} catch (e) {
										self.close();
										return false;
									}" title="<?php echo JText::_('JEV_CLOSE'); ?>"><?php echo JText::_('JEV_CLOSE'); ?></a>
			</p></div>
		<?php
	}
	$view->loadHelper("JevViewCopyright");
	JevViewCopyright();
	?>
	</div>
	</div> <?php // close of #jevents opening div ?>
	<?php
	$dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger('onJEventsFooter');

	$task = JRequest::getString("jevtask");
	$view->loadModules("jevpostjevents");
	$view->loadModules("jevpostjevents_" . $task);

	// New experimental scalable layout
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	if (($params->get('icscalable', 0) == 1 || $params->get("iconicwidth", 905) == "scalable") && (($task == "month.calendar" && !$params->get('iclistmonth', 0)) || ($task == "week.listevents" && $params->get('ictabularweek', 0)) ))
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
				var theme = "iconic";

				if (!myCSS) {
					if (sizex < 485) {
						myCSS = jevjq("<link>");
						jevjq("head").append(myCSS); //IE hack: append before setting href

						myCSS.attr({
						  rel:  "stylesheet",
						  type: "text/css",
						  id: 'myStyle',
						  title: 'myStyle',
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
					if (event_legend_container.length) {
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


						var hiddenicon = jevjq(el).parent().find(".hiddenicon");
						hiddenicon = hiddenicon.find("a");
						hiddenicon.removeClass("hiddenicon");
						hiddenicon.appendTo(listrow);

						var myClone = jevjq(el).parent().clone();
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

	if ($params->get('iccolourscheme', 'red') == "gradient")
	{
		static $loadedScalableCssGradient = false;
		if (!$loadedScalableCssGradient)
		{
			$document = JFactory::getDocument();
			$loadedScalableCssGradient = true;
			$icgradient1 = $params->get('icgradient1', '#FECCB1,#F17432 50%,#EA5507 61%,#FB955E');
			$icgradienttext1 = $params->get('icgradienttext1', '#F17432');
			$icgradienttext2 = $params->get('icgradienttext2', '#FFF');
			$icbackground1 = $params->get('icbackground1', '#F17432');
			$icbackground2 = $params->get('icbackground2', '#EEE');
			$oldmsie = explode(",", $icgradient1);
			if (count($oldmsie) > 1)
			{
				$oldmsie1 = $oldmsie[0];
				$oldmsie2 = $oldmsie[count($oldmsie) - 1];
			}
			else
			{
				$oldmsie1 = $oldmsie2 = $oldmsie;
			}
			$imgpath = JUri::root()."components/com_jevents/views/iconic/assets/images";

			$css = <<<CSS
/** Gradient **/

#jevents_body .jev_daysnames_gradient{
	 background:transparent;
	 background-image: linear-gradient( $icgradient1);
	 background: -ms-linear-gradient(top, $icgradient1);
	 filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='$oldmsie1', endColorstr='$oldmsie2',GradientType=0 );
	 -ms-filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='$oldmsie1', endColorstr='$oldmsie2',GradientType=0 );
	 background-image: linear-gradient( $icgradient1);
}

#jevents_body .jev_gradient .jev_weeknum_gradient{
	background:transparent;
	background: -moz-linear-gradient(left, $icgradient1);
	background: -webkit-linear-gradient(left, $icgradient1);
	background: -o-linear-gradient(left, $icgradient1);
	background: -ms-linear-gradient(left, $icgradient1);
	 filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='$oldmsie1', endColorstr='$oldmsie2',GradientType=1 );
	 -ms-filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='$oldmsie1', endColorstr='$oldmsie2',GradientType=1 );
	background: linear-gradient(left, $icgradient1);
}

#jevents_body .jev_gradient .jev_topleft_gradient{
	background-color: $icgradienttext1!important;
}
#jevents_body .jev_gradient .jev_topleft_gradient span {
	display:block;
	height:23px;
	margin-top:23px;
	/* background-image: linear-gradient( $icgradient1);*/
}
#jevents_body .jev_daysnames_gradient > span {
	color:$icgradienttext2!important;
}
.jev_gradient .jev_toprow .previousmonth, .jev_gradient  .jev_toprow .nextmonth {
	background-color: $icgradienttext1!important;
}
.jev_gradient .jev_toprow .previousmonth a {
	padding-left:20px;
	background:url($imgpath/Left.png) no-repeat $icgradienttext1 top left !important;
}
.jev_gradient  .jev_toprow div.nextmonth a {
	padding-right:20px;
	background:url($imgpath/Right.png) no-repeat $icgradienttext1 center right !important;
}

.jev_gradient .jev_toprow .currentmonth {
	background-color:$icbackground2!important;
	color:$icgradienttext1!important;
}
.jev_gradient .jev_toprow a, .jev_gradient  .jev_toprow a {
	color:$icgradienttext2!important;
}
.jev_gradient .jevdateicon {
	color:$icgradienttext2!important;
	border:solid 1px $icgradienttext1!important;
}

.jev_gradient .jevdateicon1 {
	background-color: $icbackground2!important;
	color:$icgradienttext1!important;
}
.jev_gradient .jevdateicon2 {
	background-image: linear-gradient( $icgradient1);
}
.jev_gradient .jev_toprow .jev_header2 {
	background-image: linear-gradient( $icgradient1);
}
CSS;
			$document->addStyleDeclaration($css);
		}
	}
	
	JEVHelper::componentStylesheet($view, "extra.css");
	jimport('joomla.filesystem.file');

	// Lets check if we have editted before! if not... rename the custom file.
	if (JFile::exists(JPATH_SITE . "/components/com_jevents/assets/css/jevcustom.css"))
	{
		// It is definitely now created, lets load it!
		JEVHelper::stylesheet('jevcustom.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
	}

}
