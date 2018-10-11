/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

var jevcflist = {
    setvalue: function (id) {
        jQuery('#pdv' + id).val(jQuery('#dv' + id).val());
    },
    changeMultiple: function (id) {
        jQuery('#pdv' + id).prop('size', jQuery('#size' + id).val());
        jQuery('#dv' + id).prop('size', jQuery('#size' + id).val());
    },
    showNext: function (el, id, op) {
        if (jQuery("#options" + id + "_t_" + op).val() != "") {
            var sib = $(el).parentNode.parentNode.getNext();
            if (sib)
                sib.style.display = '';
        }
    },
    newOption: function (id) {
        // we prepare $el to replace the blank row and clone the existing blank row to use
        $el = jQuery("#options" + id + " tr.blankrow");
        $clone = $el.clone();

        // reset values for template row for next time we use it!
        $el.removeClass("blankrow");
        var op = $el.attr('class').replace("blankrow", "");
        //alert(op);
        var parts = op.split("_");
        if (parts.length == 2) {
            // opt group elements like lists                    
            var newop = parseInt(parts[1]) + 1;
            var newgroup = parseInt(parts[0]);

            var re = new RegExp("_" + op + '"', "g");
            $el.html($el.html().replace(re, '_' + newgroup + '_' + newop + '"'));
            re = new RegExp("\\[" + op + "\\]", "g");
            $el.html($el.html().replace(re, "[" + newgroup + '_' + newop + "]"));
            re = new RegExp("'" + op + "'", "g");
            $el.html($el.html().replace(re, "'" + newgroup + '_' + newop + "'"));
            re = new RegExp('"' + op + '"', "g");
            $el.html($el.html().replace(re, '"' + newgroup + '_' + newop + '"'));

            $el.removeClass("blankrow" + op);
            $el.addClass("blankrow" + newgroup + '_' + newop);
            $el.addClass("blankrow");

            var oldop = parseInt(parts[1]);
            // use old op since we start to count from zero
            $el.find("#options" + id + "_v_" + newgroup + '_' + newop).val(newop);
            $el.find("#options" + id + "_v_" + newgroup + '_' + newop).attr('value', newop);

        } else {
            // no opt groups like checkboxes and radio boxes
            var newop = parseInt(op) + 1;
            var re = new RegExp("_" + op + '"', "g");
            $el.html($el.html().replace(re, '_' + newop + '"'));
            re = new RegExp("\\[" + op + "\\]", "g");
            $el.html($el.html().replace(re, "[" + newop + "]"));
            re = new RegExp("'" + op + "'", "g");
            $el.html($el.html().replace(re, "'" + newop + "'"));
            re = new RegExp('"' + op + '"', "g");
            $el.html($el.html().replace(re, '"' + newop + '"'));
            $el.removeClass("blankrow" + op);
            $el.addClass("blankrow" + newop);
            $el.addClass("blankrow");
        }

        $clone.removeClass("blankrow" + op);
        $clone.removeClass("blankrow");
        $clone.css("display", "")
        $clone.insertBefore($el);

    },
    startOptgroup: function (id) {
        // we prepare $el to replace the blank row and clone the existing blank row to use
        $el = jQuery("#options" + id + " tr.blankoptgroupstart");

        // reset values for template row for next time we use it!
        $clone = $el.clone();
        $clone.css("display", "")
        $clone.removeClass("blankoptgroupstart", "");
        var optg = $clone.attr('class').replace("blankoptgroupstart", "");
        var newoptg = parseInt(optg) + 1;
        var re = new RegExp("_" + optg + '"', "g");
        $el.html($el.html().replace(re, '_' + newoptg + '"'));
        re = new RegExp("\\[" + optg + "\\]", "g");
        $el.html($el.html().replace(re, "[" + newoptg + "]"));
        re = new RegExp("'" + optg + "'", "g");
        $el.html($el.html().replace(re, "'" + newoptg + "'"));
        re = new RegExp('"' + optg + '"', "g");
        $el.html($el.html().replace(re, '"' + newoptg + '"'));
        $el.removeClass("blankoptgroupstart" + optg);
        $el.addClass("blankoptgroupstart" + newoptg);
        $el.addClass("blankoptgroupstart");
        $currentoptgroup = $el.find(".currentgroup");
        $currentoptgroup.val(newoptg);

        $clone.removeClass("blankoptgroupstart" + optg);
        
        // find the target
        $target = jQuery("#options" + id + " tr.blankrow")
        // insert infront of the target
        $clone.insertBefore($target);
        
        // We must also update the option blank row to take account of the group id
        $target.removeClass("blankrow");
        var op = $target.attr('class').replace("blankrow", "");
        var parts = op.split("_");
        $target.addClass("blankrow");
        $target.removeClass("blankrow"+op);
        $target.addClass("blankrow"+optg+"_"+parts[1]);
        re = new RegExp("_"+op+'"', "g");
        $target.html($target.html().replace(re,"_"+optg+"_"+parts[1]+'"' ));
        
    },
    endOptgroup: function (id) {
        $el = jQuery("#options" + id + " tr.blankoptgroupend");

        // reset values for template row for next time we use it!
        $clone = $el.clone();
        $clone.css("display", "")
        $clone.removeClass("blankoptgroupend", "");
        var optg = $clone.attr('class').replace("blankoptgroupend", "");
        newoptg = parseInt(optg) + 1;
        var re = new RegExp("_" + optg + '"', "g");
        $el.html($el.html().replace(re, '_' + newoptg + '"'));
        re = new RegExp("\\[" + optg + "\\]", "g");
        $el.html($el.html().replace(re, "[" + newoptg + "]"));
        re = new RegExp("'" + optg + "'", "g");
        $el.html($el.html().replace(re, "'" + newoptg + "'"));
        re = new RegExp('"' + optg + '"', "g");
        $el.html($el.html().replace(re, '"' + newoptg + '"'));
        re = new RegExp('"-' + optg + '"', "g");
        $el.html($el.html().replace(re, '"-' + newoptg + '"'));
        $el.removeClass("blankoptgroupend" + optg);
        $el.addClass("blankoptgroupend" + newoptg);
        $el.addClass("blankoptgroupend");

        $clone.removeClass("blankoptgroupend" + optg);
        $clone.addClass("optgroupend_" + optg);

        // find the target
        $target = jQuery("#options" + id + " tr.blankrow")
        // insert infront of the target
        $clone.insertBefore($target);
        
        // We must also update the option blank row to take account of the group id
        $target.removeClass("blankrow");
        var op = $target.attr('class').replace("blankrow", "");
        var parts = op.split("_");
        $target.addClass("blankrow");
        $target.removeClass("blankrow"+op);
        $target.addClass("blankrow"+optg+"_"+parts[1]);
        re = new RegExp("_"+op+'"', "g");
        $target.html($target.html().replace(re,"_"+optg+"_"+parts[1]+'"' ));        
        
    },
    deleteOption: function (el) {
        $el = jQuery(el);
        $el.parent().parent().remove();
    },
    updatePreview: function (id) {
        var countel = 0;
        jQuery('#pdv' + id).empty();
        var optgroup = false;
        // create the new options
        jQuery("#options" + id + " input.inputlabel").each(function (idx, el) {
            $el = jQuery(el);
            if ($el.val() != "" && $el.val() != "xxx") {
                var rid = el.id.replace("_t_", "_r_");
                rid = rid.replace("optionsfield", "archivefield");
                if (jQuery("#" + rid).length == 0) {
                    var ogid = el.id.replace("_t_", "_og_");
                    if (jQuery("#" + ogid).length) {
                        newgroup = jQuery('<optgroup>');
                        newgroup.prop('label', jevcflist.htmlspecialchars_decode($el.val()));
                        jQuery('#pdv' + id).append(newgroup);
                        optgroup = newgroup;
                    } else {
                        optgroup = false
                    }
                } else {
                    // do not preview archived elements
                    if (jQuery("#" + rid).prop('checked')) {
                        return;
                    }
                    var opt = jQuery('<option>');
                    opt.text(jevcflist.htmlspecialchars_decode($el.val()));
                    opt.val("cal" + countel);
                    if (optgroup) {
                        optgroup.append(opt);
                    } else {
                        jQuery('#pdv' + id).append(opt);
                    }
                    //jQuery('#pdv'+id).options[countel].text = el.val();
                    countel++;
                }
            }
        });
        jQuery('#pdv' + id).trigger("chosen:updated");
        // old style version - still needed!
        jQuery('#pdv' + id).trigger("liszt:updated");
    },
    updateCloseGroup: function (id, currentgroup) {
        $close = jQuery(".optgroupend_" + currentgroup);
        if ($close.length) {
            $open = jQuery(".currentgroup_" + currentgroup);
            jQuery(".optgroupend_" + currentgroup + " .optgrouplabel").html($open.parent().find('.inputlabel').val());
        }
    },
    defaultOption: function (el, id, op) {
        var value = jQuery("#options" + id + "_v_" + op).val();
        jQuery("#dv" + id).val(value);
        var text = jQuery("#options" + id + "_t_" + op).val();
        jQuery('#pdv' + id + " option").each(function (idx, opt) {
            opt.selected = false;
            if (opt.text == text) {
                opt.selected = true;
            }
            ;
        });
        jQuery('#pdv' + id).trigger("chosen:updated");
        // old style version - still needed!
        jQuery('#pdv' + id).trigger("liszt:updated");
    },
    htmlspecialchars_decode: function (text)
    {
        var stub_object = jQuery('<span>');
        stub_object.html(text);
        var ret_val = stub_object.text();
        stub_object.remove();
        return ret_val;
    }
}