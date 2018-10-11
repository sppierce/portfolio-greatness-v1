<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
if($this->mode == 'main')
{
    $show_thumb_scrollbar = $this->profile->thumb_scrollbar;
    $images_per_row = $this->profile->images_per_row;
    $show_left_right_arrows = $this->profile->arrows_left_right;
    $show_up_down_arrows = $this->profile->arrows_up_down;
    $thumb_array = $this->thumbFiles;
    $thumb_padding = $this->dimensions['thumbPadding'];
    $thumb_margin = $this->dimensions['thumbMargin'];
    $column_widths = $this->dimensions['thumbColumnWidths'];
    $thumb_table_width = $this->dimensions['thumbTableWidth'];

    if($this->profile->thumb_position == 'left' || $this->profile->thumb_position == 'right')
    {
        if($this->profile->show_large_image == 0)
        {
            JError::raise(2, 500, JText::_('Please show the main image or set the thumb position to below.') );
        }

        $thumb_table_width_percent = round( ($thumb_table_width/$this->dimensions['thumbContainerWidthNoScroll']) * 100, 2);
        $thumb_container_width_percent = round( ($this->dimensions['thumbContainerWidth']/$this->dimensions['galleryWidth']) * 100, 2);
        $thumb_container_height = $this->profile->thumb_container_height == 0 ? $this->dimensions['largestHeight'] : $this->profile->thumb_container_height;
    }
    else
    {
        $thumb_table_width_percent = round( ($this->dimensions['thumbTableWidth']/$this->dimensions['galleryWidth']) * 100, 2);
        $thumb_container_width = $this->dimensions['thumbContainerWidth'] == 0 ? $this->dimensions['galleryWidth'] : $this->dimensions['thumbContainerWidth'];
        $thumb_container_width_percent = round( ($thumb_container_width/$this->dimensions['galleryWidth']) * 100, 2);

        $thumb_container_height = $this->profile->thumb_container_height;
    }

}
else
{
    $show_thumb_scrollbar = $this->profile->lbox_thumb_scrollbar;
    $images_per_row = $this->profile->lbox_images_per_row;
    $show_left_right_arrows = $this->profile->lbox_arrows_left_right;
    $show_up_down_arrows = $this->profile->lbox_arrows_up_down;
    $thumb_array = $this->lboxThumbFiles;
    $thumb_padding = $this->dimensions['lboxThumbPadding'];
    $thumb_margin = $this->dimensions['lboxThumbMargin'];
    $column_widths = $this->dimensions['lboxThumbColumnWidths'];
    $thumb_table_width = $this->dimensions['lboxThumbTableWidth'];

    if($this->profile->lbox_thumb_position == 'left' || $this->profile->lbox_thumb_position == 'right')
    {

        $thumb_table_width_percent = round( ($thumb_table_width/$this->dimensions['lboxThumbContainerWidthNoScroll']) * 100, 2);
        $thumb_container_width_percent = round( ($this->dimensions['lboxThumbContainerWidth']/$this->dimensions['galleryLboxWidth']) * 100, 2);
        $thumb_container_height = $this->profile->lbox_thumb_container_height == 0 ? $this->dimensions['largestLboxHeight'] : $this->profile->lbox_thumb_container_height;
    }
    else
    {
        $thumb_table_width_percent = round( ($this->dimensions['lboxThumbTableWidth']/$this->dimensions['galleryLboxWidth']) * 100, 2);
        $thumb_container_width = $this->dimensions['lboxThumbContainerWidth'] == 0 ? $this->dimensions['galleryLboxWidth'] : $this->dimensions['lboxThumbContainerWidth'];
        $thumb_container_width_percent = round( ($thumb_container_width/$this->dimensions['galleryLboxWidth']) * 100, 2);

        $thumb_container_height = $this->profile->lbox_thumb_container_height;
    }
}
?>

<div id="<?php echo $this->mode; ?>_thumbs_arrow_wrapper<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode; ?>_thumbs_arrow_wrapper" style="width: <?php echo $thumb_container_width_percent; ?>%;">

<?php if($show_up_down_arrows == 1): ?>
    <div id="<?php echo $this->mode; ?>_up_arrow<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode; ?>_up_arrow" >
		<img src="<?php echo IG_IMAGE_ASSET_PATH; ?>up_arrow.png"  alt="" />
	</div>
<?php endif; ?>

<?php $overflow = $show_thumb_scrollbar == 1 ? ' overflow: auto;' : ' overflow: hidden;'; ?>
<?php $height = $thumb_container_height != 0 ? ' max-height: '.$thumb_container_height.'px;' : ''; ?>

<div id="<?php echo $this->mode; ?>_thumb_container<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode; ?>_thumb_container" style="<?php echo $overflow.$height; ?>">

    <table id="<?php echo $this->mode; ?>_thumb_table<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode; ?>_thumb_table" cellpadding="0" cellspacing="0" style="width: <?php echo $thumb_table_width_percent; ?>%;">

    <?php
    $thumbsInRow = $images_per_row == 0 ? count($this->photoList) : $images_per_row;
    $counter = 0;
    $profileValue = $this->mode == 'main' ? 'show_thumb_info' : 'lbox_show_thumb_info';

    if($this->profile->{$profileValue} == 'tags')
    {
        $db	= JFactory::getDBO();
        $query = 'SELECT id FROM #__igallery WHERE parent = 0 ORDER BY ordering LIMIT 1';
        $db->setQuery($query);
        $topCategory = $db->loadObject();
    }

    while( $counter < count($this->photoList) ): ?>
        <tr>
        <?php
        $columnCounter = 0;
        for($i=0; $i<$thumbsInRow; $i++):
        if( isset($this->photoList[$counter]) ):
            $row = $this->photoList[$counter];
            $thumbClass = ($i == 0) ? 'active_thumb' : 'inactive_thumb';

            if($this->source == 'component')
            {
                $fileHashNoExt = JFile::stripExt($this->photoList[$counter]->filename);
                $fileHashNoRef = substr($fileHashNoExt, 0, strrpos($fileHashNoExt, '-') );
                $link = str_replace('&', '&amp;', $this->currentUrl.'#!'.$fileHashNoRef);

            }
            else
            {
                $link = '#';
            }
            ?>
            <td align="center" id="<?php echo $this->mode; ?>-<?php echo $this->uniqueid; ?>-<?php echo $counter + 1; ?>" class="<?php echo $thumbClass; ?>" style="width: <?php echo round( ($column_widths[$columnCounter]/$thumb_table_width) * 100, 2);?>%">
                <a href="<?php echo $link; ?>" class="imglink">
                    <img class="ig_thumb" src="<?php echo IG_IMAGE_HTML_RESIZE ?><?php echo $thumb_array[$counter]['folderName']; ?>/<?php echo $thumb_array[$counter]['fullFileName']; ?>" title="<?php echo $row->alt_text; ?>" alt="<?php echo $row->alt_text; ?>" style="max-width: <?php echo $thumb_array[$counter]['width']; ?>px; width: <?php echo round( ( $thumb_array[$counter]['width']/$column_widths[$columnCounter] ) * 100, 2); ?>%; padding: <?php echo round( ( $thumb_padding/$column_widths[$columnCounter] ) * 100, 2); ?>%; margin: <?php echo round( ( $thumb_margin/$column_widths[$columnCounter] ) * 100, 2); ?>%;"  />
                </a>

                <?php
                $profileValue = $this->mode == 'main' ? 'show_thumb_info' : 'lbox_show_thumb_info';
                $thumbText = '';

                switch($this->profile->{$profileValue})
                {
                    case 'description':
                        $thumbText = $row->description;
                        break;
                    case 'alt':
                        $thumbText = $row->alt_text;
                        break;
                    case 'tags':
                    if( strlen($row->tags) > 0 )
                    {
                        $tagsArray = explode(',', $row->tags);
                        $thumbText = JText::_('TAGS').': ';
                        for($k=0; $k<count($tagsArray); $k++)
                        {
                            $thumbText .= '<a href="'.JRoute::_('index.php?option=com_igallery&view=category&igid='.$topCategory->id.'&igtype=tagged&igshowmenu=0&igchild=1&igtags='.$tagsArray[$k].'&Itemid='.$this->Itemid).'">'.$tagsArray[$k].'</a> ';
                        }
                    }
                    break;
                    case 'full':
                        $thumbText = str_replace('_',' ',$row->filename);
                        break;
                    case 'no-id':
                        preg_match_all('/-[0-9]+/', $row->filename, $matches);
                        $thumbText = str_replace($matches[0][0], '', $row->filename);
                        $thumbText = str_replace('_',' ',$thumbText);
                    break;
                    case 'no-ext':
                        $thumbText = str_replace(array('.jpg','.jpeg','.gif','.png'), '', $row->filename);
                        $thumbText = str_replace('_',' ',$thumbText);
                    break;
                    case 'no-id-no-ext':
                        preg_match_all('/-[0-9]+/', $row->filename, $matches);
                        $thumbText = str_replace($matches[0][0], '', $row->filename);
                        $thumbText = str_replace(array('.jpg','.jpeg','.gif','.png'), '', $thumbText);
                        $thumbText = str_replace('_',' ',$thumbText);
                    break;
                    default:
                        $thumbText = '';
                }
                ?>
                <span class="<?php echo $this->mode; ?>_thumb_text"><?php echo $thumbText; ?></span>
            </td>
            <?php else: ?>
                <td>
                    &nbsp;
                </td>
            <?php endif; ?>
            <?php $counter++; ?>
            <?php $columnCounter++; ?>
       <?php endfor; ?>
       </tr>

       <?php endwhile; ?>
    </table>
</div>

<?php if($show_left_right_arrows == 1): ?>
    <div class="igallery_clear"></div>
    <div id="<?php echo $this->mode; ?>_left_right_arrows_div<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode; ?>_left_right_arrows_div">

        <img src="<?php echo IG_IMAGE_ASSET_PATH; ?>left_arrow.png" id="<?php echo $this->mode; ?>_left_arrow<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode; ?>_left_arrow_img" alt="" />
        <img src="<?php echo IG_IMAGE_ASSET_PATH; ?>right_arrow.png" id="<?php echo $this->mode; ?>_right_arrow<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode; ?>_right_arrow_img" alt="" />

    </div>
<?php endif; ?>

<?php if($show_up_down_arrows == 1): ?>
    <div class="igallery_clear"></div>
    <div id="<?php echo $this->mode; ?>down_arrow<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode; ?>_down_arrow">
        <img src="<?php echo IG_IMAGE_ASSET_PATH; ?>down_arrow.png" id="<?php echo $this->mode; ?>_down_arrow<?php echo $this->uniqueid; ?>" alt="" />
    </div>
<?php endif; ?>

<?php if($this->profile->thumb_pagination == 1 && $this->mode == 'main'): ?>
    <div class="igallery_clear"></div>
    <form action="index.php?option=com_igallery&amp;view=igcategory&amp;id=<?php echo $this->category->id; ?>&amp;Itemid=<?php echo $this->Itemid; ?>" method="post" name="ig_thumb_pagination">
    <div class="pagination">
        <?php echo $this->thumbPagination->getPagesLinks(); ?>
    </div>
    </form>
<?php endif; ?>

</div>