/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

var jevcftextarea = {
	setvalue: function(id){
		jQuery('#pdv'+id).val( jQuery('#dv'+id).val());
	},
	changeCols:function(id){
		jQuery('#pdv'+id).prop('cols', jQuery('#cols'+id).val());
		jQuery('#dv'+id).prop('cols', jQuery('#cols'+id).val());
		jQuery('#pdv'+id).css('width','auto');
		jQuery('#dv'+id).css('width','auto');
	},
	changeRows:function(id){
		jQuery('#pdv'+id).prop('rows', jQuery('#rows'+id).val());
		jQuery('#dv'+id).prop('rows', jQuery('#rows'+id).val());
		jQuery('#pdv'+id).css('height','auto');
		jQuery('#dv'+id).css('height','auto');
	}
}