<?php
/*
 * @package Latest News - JomSocial  Plugin for J!MailALerts Component
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */
defined('_JEXEC') or die('Restricted access');
?>

<?php $plugin_title = $plugin_params->get('plugintitle',""); ?>
<?php if (!empty($plugin_title)) : ?>
<h2 class="subTitle">
<?php 
echo $plugin_title;
?>
</h2>
<?php endif; ?>
<?php echo $vars; ?>