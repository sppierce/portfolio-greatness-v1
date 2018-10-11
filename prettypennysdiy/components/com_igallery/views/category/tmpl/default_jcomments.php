<?php defined('_JEXEC') or die('Restricted access'); ?>

<div id="<?php echo $this->mode ?>_jcomments_wrapper<?php echo $this->uniqueid ?>" class="<?php echo $this->mode ?>_jcomments_wrapper">

<?php
if($this->mode == 'main' || $this->profile->allow_comments != 2)
{
	$jcommentsPath = JPATH_SITE.'/components/com_jcomments/jcomments.php';
	
	if( file_exists($jcommentsPath) )
	{
	    include_once($jcommentsPath);
	    echo JComments::showComments($this->photoList[0]->id, 'com_igallery', $this->mode.'-'.$this->photoList[0]->filename);
	}
}
?>

</div>