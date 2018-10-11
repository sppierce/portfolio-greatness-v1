<?php
defined('_JEXEC') or die('Restricted access');

$compcfg = JComponentHelper::getParams('com_jevents');
$jinput = JFactory::getApplication()->input;

$cfg = JEVConfig::getInstance();
$Itemid = JEVHelper::getItemid();

$compname = JEV_COM_COMPONENT;
$viewname = $this->getViewName();
$viewpath = JURI::root() . "components/$compname/views/" . $viewname . "/assets";
$viewimages = $viewpath . "/images";

$view = $this->getViewName();
$data = $this->data;
$total = $this->total;
$start = $data['limitstart'];
$cd = $this->current_date;
$pd = $this->prev_date;
$nd = $this->next_date;

// Get Categories
$categories = JEV_CommonFunctions::getCategoryData();
// Filter out only Accessible categories for this user.
$accessiblecats = explode(",", $this->datamodel->accessibleCategoryList());

// Posibility to set order per view
$order = "rpt.startrepeat asc, rpt.endrepeat ASC, det.summary ASC";

// prefetch the first batch of data
require_once(JPATH_SITE."/components/com_jevents/views/float/helpers/gwejson_getfloatdata.php");
// Posibility to set order per view
$order = "rpt.startrepeat asc, rpt.endrepeat ASC, det.summary ASC";

$requestObject = new stdClass();
$requestObject->task = 'RangeData';
$requestObject->params = array($this->startdate, $this->enddate, $data['limit'], $data['limitstart'], $order);
$requestObject->Itemid = $jinput->getInt("Itemid", 0);

$returnData = new stdClass();

if (true || $jinput->getMethod == "POST"){
    $prefetchedData = "false";
}
else {
    $prefetchedData = json_encode(ProcessJsonRequest($requestObject, $returnData));
}

// Set out own output as this is a specialist view for the grid to work.
$width = $cfg->get('float_columns', 4);

$task = $jinput->getString('jevtask');

$Itemid = JEVHelper::getItemid();
if (isset($Itemid)) $item= "&Itemid=$Itemid";
else $item="";
// URL suffix to preserver catids!
$cat = $this->getCatidsOutLink();
$prev_sdate = $pd['sdate'];
$prev_edate = $pd['edate'];
$next_sdate = $nd['sdate'];
$next_edate = $nd['edate'];

$prev_link = JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=$task$item&startdate=$prev_sdate&enddate=$prev_edate".$cat);
$next_link = JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=$task$item&startdate=$next_sdate&enddate=$next_edate".$cat);

?>

    <div id='jeviso_main' class='jeviso_day'>
		<div class="jeviso_display_header">
			<div class="jeviso_view_switch">
				<?php
				if ($compcfg->get('float_dyn_switch', 1) == 1):
				?>
				<a href="#" class="btn" onclick="jeviso_list();"> List View </a>
				<a href="#" class="btn" onclick="jeviso_grid();"> Grid View </a>
				<?php endif; ?>
			</div>
		<?php

		if ($compcfg->get('float_nav_range', 1) == 1 || $compcfg->get('float_nav_range', 1) == 3):
		?>

			<div class="date nv_links">
				<div class="jeviso_curr_range">
					<?php
					// We want to hide the year for the start if it's the same as the end, lets do some checking
					if ($cd['syear'] == $cd['eyear']) {
						$sd_format = 2;
						$ed_format = 1;
					} else {
						$sd_format = 1;
						$ed_format = 1;
					}
					echo JEventsHTML::getDateFormat($cd['syear'], $cd['smonth'], $cd['sday'], $sd_format); ?>

					<?php echo ' - ' . JEventsHTML::getDateFormat($cd['eyear'], $cd['emonth'], $cd['eday'], $ed_format); ?>
				</div>
				<div class="jeviso_prev">
					<a href="<?php echo $prev_link; ?>" class="btn"> < </a>
				</div>
				<div class="jeviso_next">
					<a href="<?php echo $next_link; ?>" class="btn"> > </a>
				</div>
			</div>
		<?php endif;?>
		</div>
        <!-- We are events! we need markup! :-) -->
        <div class="jeviso_container" itemscope itemtype="http://schema.org/Event">
            <div class="jeviso_gutter"></div>
            <!-- We will load in data on document load -->
        </div>
        <?php
        //$args = json_encode(array("task" => 'RangeData', "params" => array("startdate"=>$this->startdate, "enddate"=>$this->enddate, "listlimit"=>$compcfg->get('float_list_limit', 20), "liststart"=>$data['limitstart'], "order"=>$order), "Itemid" => JRequest::getInt("Itemid", 0)));
        $args = json_encode(array("task" => 'RangeData', "params" => array($this->startdate, $this->enddate, $compcfg->get('float_list_limit', 20),$data['limitstart'], $order), "Itemid" => $jinput->getInt("Itemid", 0)));

        $dataURL = JRoute::_("index.php?option=com_jevents&ttoption=com_jevents&typeaheadtask=gwejson&file=getfloatdata&path=site&folder=views%2Ffloat%2Fhelpers&token=" . JSession::getFormToken() , false);

        //Set Error messages for the JS
        $error_not_get_data = JText::_("JEV_COULD_NOT_GET_DATA");
        $error_not_process_handler = JText::_("JEV_COULD_NOT_PROCESS_EHANDLER");
        //Setup the JS/JSON
        $limitstart = $data['limitstart'];
        $limit = $compcfg->get('float_list_limit', 20);

        $script = <<<SCRIPT
		var loadingEvents = false;
                var prefetchedData = $prefetchedData;
		
		var limit = $limit;
		var currentFloatArgs = $args;
		function getMoreEvents(dataurl, filters) {
			total = $total;
			current = jQuery('.jeviso_item').length;
			currenttest = current;
			limitstart = currentFloatArgs.params[3];

			currentFloatArgs.filters = filters;
			if (total < current) {
				alert('too many events, something is not right');
			}

                        if (prefetchedData) {
                            current = jQuery('.jeviso_item').length + prefetchedData.returned_count;
                            limitstart +=  Math.max(currentFloatArgs.params[2], prefetchedData.returned_count);
                            currentFloatArgs.params[3] = limitstart;

                            var fe=prefetchedData.ev_pdata;
                            jQuery("#jev_loadmore").removeClass("hide");
                            newItems = jQuery(fe).appendTo('div.jeviso_container');
                            jQuery('div.jeviso_container').isotope('appended', newItems);

                            // layout Isotope after each image loads, otherwise we will get overlaps
                            jQuery('div.jeviso_container').imagesLoaded().progress( function() {
                                    jQuery('div.jeviso_container').isotope('layout');
                            });
                
                            // clear out the prefretched data source
                            prefetchedData = false;
                
                            if (total <= current && limitstart >= total || current < limit) {
                                    jQuery( '.no_more' ).show(function(){
                                            jQuery(this).fadeOut(4000);
                                    });
                                    jQuery("#jev_loadmore").addClass("hide");
                            }
                
                        }
                        else {
                            loadingEvents = true;
                            var jSonRequest = jevjq.ajax({
                                    type : 'POST',
                                    dataType : 'json',
                                    url : dataurl,
                                    data : {'json':JSON.stringify(currentFloatArgs)},
                                    contentType: "application/x-www-form-urlencoded; charset=utf-8",
                                    scriptCharset: "utf-8"
                            })
                            .done(function(json){
                                    if (!json){
                                            alert('$error_not_get_data');
                                    }
                                    if (json.error){
                                            try {
                                                    eval(json.error);
                                            }
                                            catch (e){
                                                    alert('$error_not_process_handler');
                                            }
                                    }
                                    else {
                                            current = jQuery('.jeviso_item').length + json.returned_count;
                                            limitstart +=  Math.max(currentFloatArgs.params[2], json.returned_count);
                                            currentFloatArgs.params[3] = limitstart;

                                            var fe=json.ev_pdata;
                                            jQuery("#jev_loadmore").removeClass("hide");
                                            newItems = jQuery(fe).appendTo('div.jeviso_container');
                                            jQuery('div.jeviso_container').isotope('appended', newItems);

                                            // layout Isotope after each image loads, otherwise we will get overlaps
                                            jQuery('div.jeviso_container').imagesLoaded().progress( function() {
                                                    jQuery('div.jeviso_container').isotope('layout');
                                            });

                                            if (total <= current && limitstart >= total || current < limit) {
                                                    jQuery( '.no_more' ).show(function(){
                                                            jQuery(this).fadeOut(4000);
                                                    });
                                                    jQuery("#jev_loadmore").addClass("hide");
                                            }
                
                                            // Do we have any popups to process - check if setup script exists
                                            if (typeof setupEventPopups !== "undefined"){
                                                setupEventPopups();
                                            }

                                    }
                                    loadingEvents = false;

                            })
                            .fail( function( jqxhr, textStatus, error){
                                   // loadingEvents = false;
                                    alert(textStatus + ", " + error);
                            });
                            // Is the link still visible
                            if (!loadingEvents && isScrolledIntoView("#jev_loadmore")) {
                               //     loadInEvents();
                            }
                
                    }                                
		}

		function loadInEvents(){
			// Event Filtering
                
                        // are there any existing filters in action
			// Find the JEvents filter module (if present)
			var filtermodule = jQuery('form.jevfiltermodule');
			if (filtermodule.length) {
                                getMoreEvents('$dataURL', filtermodule.formToJson());
                
				// set the onsubmit method
				filtermodule.off('submit');
				filtermodule.on('submit', function() {
					// For events, from a usability point of view dynamically filtering this page is confusing, so lets wipe it
					//jQuery('.jeviso_container').empty();
                                        jQuery('.jeviso_container').isotope('remove', jQuery('.jeviso_container .jeviso_item'));
					// Reset layout for new events
					jQuery('.jeviso_container').isotope('layout');
                                        // Clear the load more link
                                        jQuery( '.no_more' ).show(function(){
                                                jQuery(this).fadeOut(4000);
                                        });
                                        jQuery("#jev_loadmore").addClass("hide");                
                                        // reset the limit start to zero!
                                        currentFloatArgs.params[3] = 0;
					// Now go fetch new events:
					getMoreEvents('$dataURL', filtermodule.formToJson());
					return false;
				});                
                        }
                        else {
                                getMoreEvents('$dataURL', false);
                        }
		}

		// Lets do a function, we can expand it as needed then.
		function jeviso_list() {

			jQuery('div.jeviso_container .jeviso_item').removeClass('w$width');
			jQuery('div.jeviso_container .jeviso_item').addClass('w1 listv');
			// Reset layout for the changes to take effect
			jQuery('.jeviso_container').isotope('layout');
		}

		// Lets do a function, we can expand it as needed then.
		function jeviso_grid() {
			jQuery('div.jeviso_container .jeviso_item').addClass('w$width');
			jQuery('div.jeviso_container .jeviso_item').removeClass('w1');
			jQuery('div.jeviso_container .jeviso_item').removeClass('listv');
			// Reset layout for the changes to take effect
			jQuery('.jeviso_container').isotope('layout');
		}

		jQuery.fn.formToJson =  function(){
			var json = {};
			jevjq(this).find('input, textarea, select').each(function(index,el){
				var name = el.name;
				var value = el.value;
				if (value === false || !name || el.disabled) return;
				// multi selects
				if (name.indexOf('[]')>=0 && (el.tagName.toLowerCase() =='select' ) && el.multiple==true){
					name = name.substr(0,name.length-2);
					if (!json[name]) json[name] = [];
					jevjq(el).find('option').each(function(eldx, opt){
						if (opt.selected ==true) json[name].push(opt.value);
					});
				}
				else if (name.indexOf('[]')>=0 && (el.type=='radio' || el.type=='checkbox') ){
					if (!json[name]) json[name] = [];
					if (el.checked==true) json[name].push(value);
				}
				else if (el.type=='radio' || el.type=='checkbox'){
					//alert(el+" "+el.name+ " "+el.checked+ " "+value);
					if (el.checked==true) {
						json[name] = value;
					}
				}
				else json[name] = value;
			});
			return json;
		}

		jQuery(document).ready(function(){
			loadInEvents();
			jQuery(window).on('scroll',  function() {
				if (!loadingEvents && isScrolledIntoView("#jev_loadmore")) {
					loadInEvents();
				}
			})
		});

function isScrolledIntoView(elem)
		{
			var jqelem = jQuery(elem);
			if (!jqelem.length){
				return false;
			}
			if (jqelem.hasClass('hide')) {
				return false;
			}
			var jqwindow = jQuery(window);

			var docViewTop = jqwindow.scrollTop();
			var docViewBottom = docViewTop + jqwindow.height();

			var elemTop = jqelem.offset().top;
			var elemBottom = elemTop + jqelem.height();

			//alert(elemBottom +" : "+ docViewBottom + " : " +elemTop + " : " +docViewTop);
			return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
		}

SCRIPT;
        JFactory::getDocument()->addScriptDeclaration($script);

        $iso_params = array("layoutMode" => "packery", "layoutOpts" => "percentPosition: true, packery: { gutter: '.jeviso_gutter' }, ", "itemSelector" => ".jeviso_item", "", "jevlayout" => "$view");
        //Load the Isotope Library
        JevIsotope::isotopep($data, $iso_params, $filters = null);
        ?>
    </div>
    <div class="no_more">
        <div>
            <?php echo JText::_("JEV_NO_MORE_EVENTS_TO_SHOW"); ?>
        </div>
    </div>
    <div id="jev_loadmore" class="hide">
        <a class="jev_load_more" onclick="loadInEvents()"
           href="javascript:void(0);"><?php echo JText::_("JEV_LOAD_MORE_EVENTS"); ?></a>
    </div>
	<div class="jeviso_footer">
		<?php
			if ($compcfg->get('float_nav_range', 1) == 2 || $compcfg->get('float_nav_range', 1) == 3):
		?>

		<div class="date nv_links">
			<div class="jeviso_curr_range">
				<?php
				// We want to hide the year for the start if it's the same as the end, lets do some checking
				if ($cd['syear'] == $cd['eyear']) {
					$sd_format = 2;
					$ed_format = 1;
				} else {
					$sd_format = 1;
					$ed_format = 1;
				}
				echo JEventsHTML::getDateFormat($cd['syear'], $cd['smonth'], $cd['sday'], $sd_format); ?>

				<?php echo ' - ' . JEventsHTML::getDateFormat($cd['eyear'], $cd['emonth'], $cd['eday'], $ed_format); ?>
			</div>
			<div class="jeviso_prev">
				<a href="<?php echo $prev_link; ?>" class="btn"> < </a>
			</div>
			<div class="jeviso_next">
				<a href="<?php echo $next_link; ?>" class="btn"> > </a>
			</div>
		</div>
		<?php endif;?>
	</div>

<?php
// Create the pagination object

// No need for pagination as we fetch dynamically, but we will keep it here for debugging purposes.

//if ($data["total"]>$data["limit"]){
//	$this->paginationForm($data["total"], $data["limitstart"], $data["limit"]);
//}
