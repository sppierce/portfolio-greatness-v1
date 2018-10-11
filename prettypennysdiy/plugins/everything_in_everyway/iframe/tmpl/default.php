<?php
/**
 * @package     pwebbox
 * @version 	2.0.0
 *
 * @copyright   Copyright (C) 2015 Perfect Web. All rights reserved. http://www.perfect-web.co
 * @license     GNU General Public Licence http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die;
?>
<!-- PWebBox IFrame plugin -->
<div class="pwebbox-iframe-container" style="width:<?php echo (int) $plugin_params->get('width'); ?>px; height: <?php echo (int) $plugin_params->get('height'); ?>px;">
    <script type="text/javascript">

        jQuery(document).ready(function($){

            $.ajax({

                url: location.href,
                success: function(data){

                    var iframe = $('<iframe/>', {
                        'id' : 'pwebbox_iframe_<?php echo $id; ?>',
                        'frameborder' : 0,
                        'width' : '<?php echo (int) $plugin_params->get('width'); ?>',
                        'height' : '<?php echo (int) $plugin_params->get('height'); ?>',
                        'src' : '<?php echo $url; ?>',
                        'sandbox' : 'allow-forms allow-pointer-lock allow-popups allow-same-origin allow-scripts'
                    });

                    $('#pwebbox_iframe_holder_<?php echo $id; ?>').replaceWith(iframe);
                }
            });
        });

    </script>    
    
    <div id="pwebbox_iframe_holder_<?php echo $id; ?>"></div>    
</div>
<!-- PWebBox IFrame plugin end -->
