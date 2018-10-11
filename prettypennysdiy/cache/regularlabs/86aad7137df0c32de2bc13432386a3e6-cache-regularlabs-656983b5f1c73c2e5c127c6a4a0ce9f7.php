<?php die("Access Denied"); ?>#x#O:8:"stdClass":7:{s:6:"script";O:8:"stdClass":1:{s:15:"text/javascript";s:10888:"checkJQ();		var loadingEvents = false;
                var prefetchedData = false;
		
		var limit = 4;
		var currentFloatArgs = {"task":"RangeData","params":["2018-09-28","2018-10-28","4",0,"rpt.startrepeat asc, rpt.endrepeat ASC, det.summary ASC"],"Itemid":313};
		function getMoreEvents(dataurl, filters) {
			total = 0;
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
                                            alert('We could not get the data.');
                                    }
                                    if (json.error){
                                            try {
                                                    eval(json.error);
                                            }
                                            catch (e){
                                                    alert('We could not process the error handler.');
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
                                getMoreEvents('/prettypennysdiy/pretty-penny-s-events/daterange/-.html?ttoption=com_jevents&typeaheadtask=gwejson&file=getfloatdata&path=site&folder=views/float/helpers&token=e379aa553bd7da416d633228f26018c5', filtermodule.formToJson());
                
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
					getMoreEvents('/prettypennysdiy/pretty-penny-s-events/daterange/-.html?ttoption=com_jevents&typeaheadtask=gwejson&file=getfloatdata&path=site&folder=views/float/helpers&token=e379aa553bd7da416d633228f26018c5', filtermodule.formToJson());
					return false;
				});                
                        }
                        else {
                                getMoreEvents('/prettypennysdiy/pretty-penny-s-events/daterange/-.html?ttoption=com_jevents&typeaheadtask=gwejson&file=getfloatdata&path=site&folder=views/float/helpers&token=e379aa553bd7da416d633228f26018c5', false);
                        }
		}

		// Lets do a function, we can expand it as needed then.
		function jeviso_list() {

			jQuery('div.jeviso_container .jeviso_item').removeClass('w4');
			jQuery('div.jeviso_container .jeviso_item').addClass('w1 listv');
			// Reset layout for the changes to take effect
			jQuery('.jeviso_container').isotope('layout');
		}

		// Lets do a function, we can expand it as needed then.
		function jeviso_grid() {
			jQuery('div.jeviso_container .jeviso_item').addClass('w4');
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
jQuery(document).ready(function(){
            jQuery('.jeviso_container').isotope({
                //options
                layoutMode: 'packery',
                percentPosition: true, packery: { gutter: '.jeviso_gutter' }, 
                itemSelector: '.jeviso_item',
            })
        });";}s:7:"scripts";O:8:"stdClass":9:{s:40:"/prettypennysdiy/media/system/js/core.js";O:8:"stdClass":3:{s:4:"mime";s:15:"text/javascript";s:5:"defer";b:0;s:5:"async";b:0;}s:43:"/prettypennysdiy/media/jui/js/jquery.min.js";O:8:"stdClass":3:{s:4:"mime";s:15:"text/javascript";s:5:"defer";b:0;s:5:"async";b:0;}s:50:"/prettypennysdiy/media/jui/js/jquery-noconflict.js";O:8:"stdClass":3:{s:4:"mime";s:15:"text/javascript";s:5:"defer";b:0;s:5:"async";b:0;}s:51:"/prettypennysdiy/media/jui/js/jquery-migrate.min.js";O:8:"stdClass":3:{s:4:"mime";s:15:"text/javascript";s:5:"defer";b:0;s:5:"async";b:0;}s:46:"/prettypennysdiy/media/jui/js/bootstrap.min.js";O:8:"stdClass":3:{s:4:"mime";s:15:"text/javascript";s:5:"defer";b:0;s:5:"async";b:0;}s:64:"/prettypennysdiy/components/com_jevents/assets/js/jQnc.js?3.4.36";O:8:"stdClass":3:{s:4:"mime";s:15:"text/javascript";s:5:"defer";b:0;s:5:"async";b:0;}s:71:"/prettypennysdiy/media/com_jevents/lib_jevisotope/js/jevisotope.pkgd.js";O:8:"stdClass":3:{s:4:"mime";s:15:"text/javascript";s:5:"defer";b:0;s:5:"async";b:0;}s:76:"/prettypennysdiy/media/com_jevents/lib_jevisotope/js/jevimagesloaded.pkgd.js";O:8:"stdClass":3:{s:4:"mime";s:15:"text/javascript";s:5:"defer";b:0;s:5:"async";b:0;}s:73:"/prettypennysdiy/media/com_jevents/lib_jevisotope/js/packery-mode.pkgd.js";O:8:"stdClass":3:{s:4:"mime";s:15:"text/javascript";s:5:"defer";b:0;s:5:"async";b:0;}}s:5:"style";a:0:{}s:6:"styles";O:8:"stdClass":6:{s:52:"/prettypennysdiy/media/com_jevents/css/bootstrap.css";O:8:"stdClass":3:{s:4:"mime";s:8:"text/css";s:5:"media";N;s:7:"attribs";a:0:{}}s:63:"/prettypennysdiy/media/com_jevents/css/bootstrap-responsive.css";O:8:"stdClass":3:{s:4:"mime";s:8:"text/css";s:5:"media";N;s:7:"attribs";a:0:{}}s:73:"/prettypennysdiy/components/com_jevents/assets/css/eventsadmin.css?3.4.36";O:8:"stdClass":3:{s:4:"mime";s:8:"text/css";s:5:"media";N;s:7:"attribs";a:0:{}}s:68:"/prettypennysdiy/media/com_jevents/lib_jevisotope/css/jevisotope.css";O:8:"stdClass":3:{s:4:"mime";s:8:"text/css";s:5:"media";N;s:7:"attribs";a:0:{}}s:54:"/prettypennysdiy/media/com_jevents/float/css/float.css";O:8:"stdClass":3:{s:4:"mime";s:8:"text/css";s:5:"media";N;s:7:"attribs";a:0:{}}s:71:"/prettypennysdiy/components/com_jevents/assets/css/jevcustom.css?3.4.36";O:8:"stdClass":3:{s:4:"mime";s:8:"text/css";s:5:"media";N;s:7:"attribs";a:0:{}}}s:6:"custom";a:0:{}s:4:"html";s:1130:"<div style="padding:14px;">
	<div id="system-message-container">
	</div>

		<!-- Event Calendar and Lists Powered by JEvents //-->
	    <div id="jevents" class="isotope">
    <div class="contentpaneopen jeventpage   jevbootstrap" id="jevents_header">
                    </div>
    <div class="contentpaneopen  jeventpage   jevbootstrap" id="jevents_body">

    <div id='jeviso_main' class='jeviso_day'>
		<div class="jeviso_display_header">
			<div class="jeviso_view_switch">
							</div>
				</div>
        <!-- We are events! we need markup! :-) -->
        <div class="jeviso_container" itemscope itemtype="http://schema.org/Event">
            <div class="jeviso_gutter"></div>
            <!-- We will load in data on document load -->
        </div>
            </div>
    <div class="no_more">
        <div>
            No More Events.        </div>
    </div>
    <div id="jev_loadmore" class="hide">
        <a class="jev_load_more" onclick="loadInEvents()"
           href="javascript:void(0);">Load More Events..</a>
    </div>
	<div class="jeviso_footer">
			</div>

</div>
</div> <!-- close #jevents //-->

	</div>";s:5:"token";s:32:"e379aa553bd7da416d633228f26018c5";}