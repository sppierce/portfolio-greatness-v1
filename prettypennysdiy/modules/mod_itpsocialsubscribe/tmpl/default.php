<?php
/**
 * @package      ITPSocialSubscribe
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="itp-socialsubscribe<?php echo $moduleclass_sfx; ?>">
<?php 
echo ItpSocialSubscribeHelper::getFacebookLike($params);
echo ItpSocialSubscribeHelper::getTwitter($params);
echo ItpSocialSubscribeHelper::getLinkedInAndPinterest($params);
echo ItpSocialSubscribeHelper::getPinterest($params);
echo ItpSocialSubscribeHelper::getYoutube($params);
echo ItpSocialSubscribeHelper::getGoogleBadge($params);
echo ItpSocialSubscribeHelper::getSubscriptionIcons($params);
echo ItpSocialSubscribeHelper::getExtraButtons($params);
?>
</div>
<div style="clear:both;"></div>