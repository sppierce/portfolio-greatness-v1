/**
 * @package     pwebbox
 * @version 	1.0.0
 *
 * @copyright   Copyright (C) 2015 Perfect Web. All rights reserved. http://www.perfect-web.co
 * @license     GNU General Public Licence http://www.gnu.org/licenses/gpl-3.0.html
 */

jQuery(document).ready(function ($) {
    $('.pwebbox_bottombar_toggler').on('click', function() {

        var id = $(this).attr('id').replace('pwebbox', '').replace('_toggler', '');
        var box = $('#pwebbox' + id);
        var plugin_class = $(this).parent().parent().find('.pwebbox-content').children().attr('class');
        
        box.hide();
        
        if(plugin_class.indexOf('cookiepolicy') > -1) {  
            $.cookie( $(this).attr('id').replace('#', '').replace('toggler', 'notification'), 'closed', {expires: 365, path: '/'});
        }
    });
});