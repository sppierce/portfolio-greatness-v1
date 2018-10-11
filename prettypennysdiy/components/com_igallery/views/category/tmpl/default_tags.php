<?php defined('_JEXEC') or die('Restricted access'); ?>
	
<div id="<?php echo $this->mode; ?>_tags_container<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode; ?>_tags_container">

<?php
$db	= JFactory::getDBO();
$query = 'SELECT id FROM #__igallery WHERE parent = 0 ORDER BY ordering LIMIT 1';
$db->setQuery($query);
$topCategory = $db->loadObject();

for($i=0; $i<count($this->photoList); $i++)
{
    $row = $this->photoList[$i];
    $style = $i==0 ? 'display: block' : 'display: none';
    ?>
    <div style="<?php echo $style; ?>" class="tags_div">
    <?php
    echo JText::_('TAGS').': ';

    if( strlen($row->tags) > 0 )
    {
        $tagsArray = explode(',', $row->tags);
        for($k=0; $k<count($tagsArray); $k++)
        {
            ?>
            <a href="<?php echo JRoute::_('index.php?option=com_igallery&view=category&igid='.$topCategory->id.'&igtype=category&ighidemenu=1&igchild=1&igtags='.$tagsArray[$k].'&Itemid='.$this->Itemid); ?>"><?php echo $tagsArray[$k]; ?></a>
            <?php
        }
    }
    ?>
    </div>
    <?php
}
?>
</div>

