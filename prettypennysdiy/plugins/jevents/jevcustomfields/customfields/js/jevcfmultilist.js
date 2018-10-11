/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

var jevcfmultilist = {
    setvalue: function (id) {
        jQuery('#pdv' + id).val(jQuery('#dv' + id).val());
    },
    changeSize:function(id){
    
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
        $el = jQuery("#options" + id + " tr.blankrow");
        $clone = $el.clone();

        // reset values for template row for next time we use it!
        $el.removeClass("blankrow");
        var op = $el.attr('class').replace("blankrow", "");
        var parts = op.split("_");
        newop = parseInt(parts[1]) + 1;
        newgroup = parseInt(parts[0]);
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

        $clone.removeClass("blankrow" + op);
        $clone.removeClass("blankrow");
        $clone.css("display", "")
        $clone.insertBefore($el);

    },
    deleteOption: function (el) {
        $el = jQuery(el);
        $el.parent().parent().remove();
    },
    updatePreview: function (id) {
        jQuery('#pdv' + id).empty();
        // create the new options
        jQuery("#options" + id + " input.inputlabel").each(function (idx, el) {
            $el = jQuery(el);
            if ($el.val() != "" && $el.val() != "xxx") {
                var rid = el.id.replace("_t_", "_r_");
                rid = rid.replace("optionsfield", "archivefield");
                // do not preview archived elements
                if (jQuery("#" + rid).prop('checked')) {
                    return;
                }
                var opt = jQuery('<option>');
                opt.text(jevcflist.htmlspecialchars_decode($el.val()));
                var vid = "#"+el.id.replace("_t_", "_v_");
                opt.val(jQuery(vid).val());
                jQuery('#pdv' + id).append(opt);

            }
        });
        jQuery('#pdv' + id).trigger("chosen:updated");
        // old style version - still needed!
        jQuery('#pdv' + id).trigger("liszt:updated");

        // update preview selections
        //alert('input[name=default' + id+']' + " " +jQuery('input[name=default' + id+']' ).length);
        jQuery('input[name=default' + id+']' ).each(function (idx, opt) {
           // alert(jQuery(opt).prop('checked')+" " +jQuery(opt).prop('onclick'));
            jQuery(opt).triggerHandler('click');
        });

    },
    defaultOption: function (el, id, op) {
        var value = jQuery("#options" + id + "_v_" + op).val();
        var checked = jQuery("#options" + id + "_d_" + op).prop('checked');
        //alert("#options" + id + "_v_" + op +" "+value +" "+checked+" el="+el);

        jQuery('#pdv' + id + " option").each(function (idx, opt) {

            if (jQuery(opt).val() == value) {
                opt.selected = checked;
            }
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