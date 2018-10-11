/**
* JEvents Component for Joomla 1.5.x
*
* @version     $Id: jicaleventrepeat.php 1631 2009-11-26 05:38:33Z geraint $
* @package     JEvents
* @copyright   Copyright (C) 2008-2017 GWE Systems Ltd, 2006-2008 JEvents Project Group
* @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
* @link        http://www.jevents.net
*/
var rsvpFieldTypes = new Object();


var rsvpforms = {
	Version: '1.0.0',
	forms: {},
	initialized: false,
	language: 'english',
	debugging: false,
	// how many balance Elements are defined
	settingUpBalanceElements:false,
	balanceElements:0,


	//   Routine to handle errors generated by rsvpforms.
	//
	//  A javascript error object contains two fields: type and message.
	//  Predefined Types:
	//  EvalError, RangeError, ReferenceError, SyntaxError, TypeError
	//  and URIError

	handleError: function(errorObj) {
		if (rsvpforms.debugging) alert('rsvpforms Error :: ' + errorObj.message);
		// trigger the error (as it might be handled by window.onerror)
		throw(errorObj);
	},
	handleWarning: function(warningObj) {
		try {
			if (rsvpforms.debugging) window.console.log('rsvpforms Warning :: ' + warningObj.message);
		} catch (errorObj) {}
	},

	setupOpenResponse: function(){
		jQuery('.rsvpfield').each(function(i, el){
			jQuery(el).on('click', function(event){
				if (jQuery(event.target).hasClass("sortable-handler")){
					return;
				}
				rsvpforms.highlightField(el.id);
			});
			var deleteButton = jQuery('#deleteFieldButton'+el.id);
			if (deleteButton.length) deleteButton.on('click',function(){rsvpforms.deleteField(el.id)});
		});
		rsvpforms.setupSortableFields();
		rsvpforms.fieldsHaveReordered();
	},

	createNewField: function(){

		var fieldtype = jQuery("#templatetype").val();
		// Add all 3 balance types in one go!
		if (fieldtype=="jevrbalance" && !rsvpforms.settingUpBalanceElements && jQuery('#rsvpfields').find("input[value='jevrbalance']").length==0){
			rsvpforms.settingUpBalanceElements = true;
			var balanceFieldId = rsvpforms.createNewField();
			var firstBalanceField = balanceFieldId;
			// Get the translated label ;)
			jQuery("#fl"+balanceFieldId).val(	jQuery("#balancetype"+balanceFieldId+ " option[value='total']").text() );
			jQuery("#balancetype"+balanceFieldId+ " option[value='total']").prop("selected", true);
			jQuery("#balancetype"+balanceFieldId+ " option[value='total']").attr("selected", true);
			// update the chosen replacement
			jQuery("#balancetype"+balanceFieldId).chosen();
			jQuery("#balancetype"+balanceFieldId).trigger("liszt:updated");
			rsvppro.updateLabel(balanceFieldId);

			jQuery("#templatetype").val("jevrbalance");
			balanceFieldId = rsvpforms.createNewField();
			jQuery("#fl"+balanceFieldId).val(	jQuery("#balancetype"+balanceFieldId+ " option[value='paid']").text() );
			jQuery("#balancetype"+balanceFieldId+ " option[value='paid']").prop("selected", true);
			jQuery("#balancetype"+balanceFieldId+ " option[value='paid']").attr("selected", true);
			// update the chosen replacement
			jQuery("#balancetype"+balanceFieldId).chosen();
			jQuery("#balancetype"+balanceFieldId).trigger("liszt:updated");
			rsvppro.updateLabel(balanceFieldId);

			jQuery("#templatetype").val("jevrbalance");
			balanceFieldId = rsvpforms.createNewField();
			jQuery("#fl"+balanceFieldId).val(	jQuery("#balancetype"+balanceFieldId+ " option[value='outstanding']").text() );
			jQuery("#balancetype"+balanceFieldId+ " option[value='outstanding']").prop("selected", true);
			jQuery("#balancetype"+balanceFieldId+ " option[value='outstanding']").attr("selected", true);
			// update the chosen replacement
			jQuery("#balancetype"+balanceFieldId).chosen();
			jQuery("#balancetype"+balanceFieldId).trigger("liszt:updated");
			rsvppro.updateLabel(balanceFieldId);
			jQuery("#templatetype").val("jevrbalance");

			// Highlight the first field
			//rsvpforms.highlightField(firstBalanceField);
			return;
		}
		var fieldhtml = rsvpFieldTypes[jQuery("#templatetype").val()];
		var id = rsvpforms.uniqId('field');
		var newDiv = jQuery("<div>").addClass('rsvpfield').prop( 'id',id);
		fieldhtml = fieldhtml.replace(/###/g,id);
		newDiv.html(fieldhtml) ;

		newDiv.on('click',function(event){
			if (jQuery(event.target).hasClass("sortable-handler")){
				return;
			}
			rsvpforms.highlightField(id);
		})
		// update the label preview
		newDiv.find('.rsvpfl').on('change',function(){
			jQuery('#pl'+id).html(this.value);
		});
		jQuery('#rsvpfields').append(newDiv);

		var deleteButton = jQuery('#deleteFieldButton').clone();
		deleteButton.prop(id , 'deleteFieldButton'+id);
		deleteButton.css('display','block');
		deleteButton.on('click',function(){
			rsvpforms.deleteField(newDiv.prop('id'))
		});
		newDiv.prepend(deleteButton);

		newDiv.prepend(jQuery('<span class="sortable-handler" style="cursor: move;float:left;margin:10px 10px 0px 0px;"><i class="icon-menu"></i></span>'));

		// make shure this field is highlighted and others not
		rsvpforms.highlightField(id);
		rsvpforms.setupSortableFields();
		rsvpforms.fieldsHaveReordered();

		// make sure fees are shown/hidden accordingly
		rsvppro.toggleFees();

		if (conditionalEditorPlugin) {
			rsvpforms.setupConditionalOptions();
			conditionalEditorPlugin.update(id, '');
		}

		jQuery('select').chosen({
			disable_search_threshold : 10,
			allow_single_deselect : true
		});

                newDiv[0].scrollIntoView();
                // Joomla backend template bit at the top that hides the element!
                window.scrollBy(0,-150);
                
		setupRSVPTemplateBootstrap();
		return id;
	},
	deleteField: function(id){
		jQuery("#"+id).off();
		jQuery("#"+id).remove();
	},
	highlightField: function(id){
		$el=  jQuery("#"+id);
		$el.css('border-width',"1px");
		$el.find(".rsvpfieldinput").css('display','block');
		$el.find('.deleteFieldButton').css('display','block');
		jQuery('#'+id+"preview").addClass('previewright');
		rsvpforms.hideInactiveFields(id);
	},
	hideInactiveFields: function(id){
		// must un-highlight all the other fields
		jQuery('.rsvpfield').each(function(i,el){
			if(el.id !=id) {
				$el = jQuery(el);
				$el.css('border-width',"0px");
				$el.find(".rsvpfieldinput").css('display', 'none');
				$el.find('.deleteFieldButton').css('display','none');
				jQuery('#'+el.id+"preview").removeClass('previewright');
			}
		});
	},
	setupConditionalOptions: function() {
		var rsvpbooleanfield = jQuery(".rsvpbooleanfield");
                var rsvpradiofield = jQuery(".rsvpradiofield");
		if(rsvpbooleanfield.length || rsvpradiofield.length) {
			jQuery(".rsvpconditional").css("display","block");
		}
		else {
			jQuery(".rsvpconditional").css("display","none");
		}
	},
	uniqId : function(prefix){
		var newDate = new Date();
		return prefix+newDate.getTime();
	},

	setupSortableFields : function(){
		// causes problems with sortable option interfering with entering data
		jQuery('#rsvpfields').sortable(
			{
				"update" : rsvpforms.fieldsHaveReordered,
				handle : ".sortable-handler",
				items : "> div.rsvpfield"
			}
		);
        
                // list options sortable tooS
        	jQuery('#rsvpfields .sortablelistoptions').sortable(
			{
				handle : ".sortable-handler",
				items : " tr:not(.sortablelabels)",
                                containment: "parent"
			}
		);

/*
		// to avoid this ensure all the inputs within the sortable will respond to their input events - must stop the propogation to the Sortable
		jQuery('#rsvpfields').find('input,textarea,select').each(function(i, el){
			jQuery(el).on('mousedown',function (eventObj) {
				eventObj.stopPropagation();
			});
		});
*/
	},

	fieldsHaveReordered : function(){
		var order = 0;
		jQuery('.rsvpfield').each(function(i, el){
			var ordering = jQuery('#ordering'+el.id);
			if (ordering) ordering.val(order);
			order ++;
		});
	},
        
        fieldOptionCapacityWarning : function(id) {
            var $peruser = jQuery("#peruser"+id);
            if($peruser.val()==0) {
                return;
            }
            // warn about field option capacities!
            var hasCapacities = false;
            jQuery("#options"+id+" input.jevoption_capacity").each(function(ixd, elem) {
              $elem = jQuery(elem);
              if ($elem.val()!=="" && parseInt($elem.val()) != 0){
                  hasCapacities = true;
              }
            });
            if (hasCapacities){
                $peruser.val(0);
                alert(Joomla.JText._("JEV_CANNOT_HAVE_FIELD_OPTION_CAPACITIES_WITH_GUESTS"));
               	
                $peruser.chosen();
		$peruser.trigger("liszt:updated");
            }
        }
}


var rsvppro = {
	updateLabel: function(id){
		jQuery('#pl'+id).html(jQuery('#fl'+id).val());
	},
	toggleFees:function(){
		var hasfees = document.adminForm.elements['withfees'];
		for(var i = 0; i < hasfees.length; i++) {
			if (hasfees[i].checked){
				if (hasfees[i].value==1){
					jQuery(".rsvp_nofees").each(function(idx, el){
						jQuery(el).removeClass("rsvp_nofees");
						jQuery(el).addClass("rsvp_hasfees");
					});
					jQuery(".jevconfig").each(function(idx , el){
						jQuery(el).removeClass("jevconfighidden");
					});
					jQuery('#jevtemplate_fields').html(fulllist);
					jQuery('#jevtemplate_fields select').chosen();//trigger("chosen:updated");
				}
				else {
					jQuery(".rsvp_hasfees").each(function(idx, el){
						jQuery(el).removeClass("rsvp_hasfees");
						jQuery(el).addClass("rsvp_nofees");
					});
					jQuery(".jevconfig").each(function(idx , el){
						jQuery(el).addClass("jevconfighidden");
					});
					jQuery('#jevtemplate_fields').html(freelist);
					jQuery('#jevtemplate_fields select').chosen();//trigger("chosen:updated");
				}
			}
		}
	},
	hasFees:function(el){
		rsvppro.toggleFees();
	},
	toggleTickets:function(){
		var hasticket = document.adminForm.elements['withticket'];
		for(var i = 0; i < hasticket.length; i++) {
			if (hasticket[i].checked){
				if (hasticket[i].value==1){
					jQuery(".jevticket").each(function(idx, el){
						jQuery(el).removeClass("jevtickethidden");
					});
				}
				else {
					jQuery(".jevticket").each(function(idx, el){
						jQuery(el).addClass("jevtickethidden");
					});
				}
			}
		}

		// setup the field inserts!
		ticketsEditorPlugin.update('ticketfields');
	},
	hasTicket:function(el){
		rsvppro.toggleTickets();
	}
}


ticketsEditorPlugin = {

	update: function (pluginName) {

		var optgroup = document.getElement("#"+pluginName + " optgroup.templatefields");
		// clean out the kids first
		while (optgroup.firstChild) {
			optgroup.removeChild(optgroup.firstChild);
		}
		// now add the new kids
		jQuery("#rsvpfields .rsvpfl").each(function(i, el){
			var val = el.value.replace(":","")+':field:#'+ el.id.substring(2)+"#";
			ticketsEditorPlugin.node(optgroup, el.value+" [VALUE]", val);
			var val = el.value.replace(":","")+':label:#'+ el.id.substring(2)+"#";
			ticketsEditorPlugin.node(optgroup, el.value+" [LABEL]", val);
		});

	},
	node: function(parent, label, val){
		optnode = jQuery("<option>").val(val).text(label);
		jQuery(parent).append(optnode);
	},

	insert: function ( fieldName, pluginNode) {
		var textToInsert = '{' + jQuery("#"+pluginNode).val()  + '}';

		// Bail if the selectedIndex is 0 (as this is the 'Select...' option)
		if (  jQuery("#"+pluginNode).prop('selectedIndex') == 0 ) return true;

		// insert the text using the library code
		$result = jInsertEditorText(textToInsert,fieldName);

		// reset the selected element back to 'Select...'
		jQuery("#"+pluginNode).prop('selectedIndex',  0);

		jQuery("#"+pluginNode).trigger("liszt:updated");

		return true;
	}
}

var activeMessageField = false;

messagesEditorPlugin = {

	update: function (pluginClass) {

		var optgroups = document.getElements ("select."+pluginClass+" optgroup.templatefields");
		optgroups.each(function(optgroup){
			// clean out the kids first
			while (optgroup.firstChild) {
				optgroup.removeChild(optgroup.firstChild);
			}

			// now add the new kids
			jQuery("#rsvpfields .rsvpfl").each(function(i, el){
				var val = el.value.replace(":","")+':field:#'+ el.id.substring(2)+"#";
				messagesEditorPlugin.node(optgroup, el.value+" [VALUE]", val);
				var val = el.value.replace(":","")+':label:#'+ el.id.substring(2)+"#";
				messagesEditorPlugin.node(optgroup, el.value+" [LABEL]", val);
			});
		})


	},
	node: function(parent, label, val){
		optnode = jQuery("<option>").val(val).text(label);
		jQuery(parent).append(optnode);
	},

	insert: function ( pluginNode) {

		var textToInsert = '{' + jQuery("#"+pluginNode).val()  + '}';

		// Bail if the selectedIndex is 0 (as this is the 'Select...' option)
		if (  jQuery("#"+pluginNode).prop('selectedIndex') == 0 ) return true;

		// insert the text into the field
		if ( jQuery("#"+pluginNode.replace("fields",""))){
			$result = jInsertEditorText(textToInsert,pluginNode.replace("fields",""));
		}
		else {
			var node = pluginNode.replace("fields","]").replace("params","params[");
			$result = jInsertEditorText(textToInsert,node);
		}
		//this.insertAtCursor($(pluginNode.replace("fields","")), textToInsert);

		// reset the selected element back to 'Select...'
		jQuery("#"+pluginNode).prop('selectedIndex',  0);

		jQuery("#"+pluginNode).trigger("liszt:updated");

		return true;
	}

}

conditionalEditorPlugin = {
        booleanoptions: false,
	update: function (fieldid, basefield) {
		var selects = jQuery(".cf");
		selects.each(function(index, sel){
			if (sel.id.substring(2) == fieldid){
				// now add the new kids
				jQuery("#rsvpfields .rsvpfl").each(function(i, el){
					// only support boolean and radio base fields for now

					if (el.getAttribute('rel')=="jevrboolean" || el.getAttribute('rel')=="jevrradio"){
						var val = el.id.substring(2);
						// don't do the field itself
						if (val==fieldid) return;
						conditionalEditorPlugin.node(sel, el.value, val, basefield);
                                                conditionalEditorPlugin.updateSelection(sel.id);
					}
				});
			}
		})

	},
	node: function(parent, label, val, basefield){
		optnode = jQuery("<option>").val(val).text(label);
		jQuery(parent).append(optnode);
		if (val==basefield){
			optnode.prop('selected', true);
			// Must also make the behaviour visible!
			jQuery(parent).closest('.rsvpconditional').find(".rsvpconditionalselector").css("display","block");
		}
	},
	updateSelection:function(id) {                  
                var selectedVal = jQuery('#'+id).find("option:selected").val();
		if(selectedVal != ""){                   
			// Must also make the behaviour visible!
                        var conditionalselector = jQuery('#'+id).closest('.rsvpconditional').find(".rsvpconditionalselector");
			conditionalselector.css("display","block");
                        
                        // Must set the values to match too!
                        // search for radio options to replace the default boolean values
                        var filtervalues = conditionalselector.find(".cfvfv");
                        if (!this.booleanoptions){
                            this.booleanoptions = filtervalues.find("option").clone();
                        }
                        filtervalues.find("option").remove().end();

                        var hiddencfvfv = jQuery("#hiddencfvfv"+id.substring(2)).val();

                        var optionvalues = jQuery("#options"+selectedVal).find('.jevoption_value');
                        if (optionvalues.length){
                            optionvalues.each (function (idx, ov) {                                
                                var id = ov.id.replace("_v_","_t_");
                                var label = jQuery("#"+id).val();
                                if (label!=""){
                                    optnode = jQuery("<option>").val(jQuery(ov).val()).text(label);
                                    if (hiddencfvfv == jQuery(ov).val()){
                                        optnode.attr("selected", true);
                                    }
                                    //if (jQuery(ov).val()==juery())
                                    filtervalues.append(optnode);                            
                                }
                            });
                        }
                        else {
                        // else a boolean field 
                        this.booleanoptions.each (function (idx, bopt) {
                            optnode = jQuery("<option>").val(jQuery(bopt).val()).text(jQuery(bopt).text());
                            if (hiddencfvfv == jQuery(bopt).val()){
                                optnode.attr("selected", true);
                            }
                            filtervalues.append(optnode);                                                        
                        });
                        }
                        
                  	filtervalues.chosen();
			filtervalues.trigger("liszt:updated");

	}
		else {
			// Must also make the behaviour hidden
			jQuery('#'+id).closest('.rsvpconditional').find(".rsvpconditionalselector").css("display","none");
		}
	}
}

jQuery(document).ready(function(){
	jQuery("fieldset.jevmessages textarea").each(function (index, el) {
		jQuery(el).on('mousedown',function() {
			activeMessageField = el;
		});
	})
});

function setupForms(){
	try {
		rsvpforms.hideInactiveFields(0);
		rsvpforms.setupOpenResponse();
		rsvpforms.setupConditionalOptions();
	}
	catch (e) {

	}

	if (!jQuery("#newFieldButton").length) return;
	jQuery("#newFieldButton").on("click", function(){
		rsvpforms.createNewField();
	});

	rsvppro.toggleFees();
	rsvppro.toggleTickets();

	messagesEditorPlugin.update('messagesEditorPlugin');

}

jQuery(document).ready(function(){
	setupForms();
});

function rsvpToggleDetails(el) {
	var node =jQuery(el).parent().find('.largeDetail');
	var nodeContent =jQuery(el).parent().find('.largeDetailContent');
	var nodeHeight = nodeContent.height()+6;
	if (!nodeHeight ){
		nodeHeight =100;
	}
	if (jQuery(node).prop('status')!='show'){
		closeOtherRsvpToggles();
		//el.getElement('div').innerHTML = hide;
		jQuery(el).find('span.spanopen').css('display','inline');
		jQuery(el).find('span.spanclosed').css('display','none');
		setRsvpToggleStatus(node);
		jQuery(node).css('height',"100%");
		jQuery(node).css('overflow',"visible");
                node[0].scrollIntoView();
                // Joomla backend template bit at the top that hides the element!
                window.scrollBy(0,-150);
	}
	else {
		jQuery(el).find('span.spanclosed').css('display','inline');
		jQuery(el).find('span.spanopen').css('display','none');
		setRsvpToggleStatus(node);
		jQuery(node).css('overflow',"hidden");
		jQuery(node).css('height',"0px");
	}
        return false;
}
function closeOtherRsvpToggles(){
	jQuery('.largeDetail').each(function(index, node){
		if (jQuery(node).prop('status')!="hide"){
			el = jQuery(node).parent().find('.toggleEnlarge');
			jQuery(el).find('span.spanclosed').css('display','inline');
			jQuery(el).find('span.spanopen').css('display','none');
			jQuery(node).css('overflow',"hidden");
			jQuery(node).css('height',"0px");
			jQuery(node).prop('status','hide' );
		}
	});
}
function setRsvpToggleStatus (node) {
	if (jQuery(node).prop('status')!='show'){
		jQuery(node).prop('status','show');
	}
	else {
		jQuery(node).prop('status','hide');
	}
}
function setupRsvpToggles() {
	jQuery('.largeDetail').each(function(index, node){
		jQuery(node).prop('status','hide');
	});
	jQuery('.toggleEnlarge').each(
		function (index, el) {
			jQuery(el).on('mousedown', function(evt){
				rsvpToggleDetails(this);
				return false;
			});
		}
	);
}

jQuery(document).ready(function(){

	setupRsvpToggles();
});