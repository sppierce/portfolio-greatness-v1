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
<!-- PWebBox Custom HTML plugin -->
<div class="pwebbox-customhtml-container">
    <div id="pwebbox_customhtml_<?php echo $id; ?>">
        <?php echo $plugin_params->get('html_code'); ?>
    </div>
</div>
<!-- PWebBox Custom HTML plugin end -->
