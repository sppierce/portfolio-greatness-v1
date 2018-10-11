<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="igallery_clear"></div>
<form action="index.php?option=com_igallery&amp;view=category&amp;id=<?php echo $this->category->id; ?>&amp;Itemid=<?php echo $this->Itemid; ?>" method="post" name="ig_menu_pagination">

<?php $allCategories = igStaticHelper::getCategories(); ?>

<?php if(count($this->categoryChildren) != 0): ?>

	<div id="cat_child_wrapper<?php echo $this->uniqueid; ?>" class="cat_child_wrapper profile<?php echo $this->profile->id; ?>" style="max-width: <?php echo $this->dimensions['menuOverallWidth']; ?>px;">
	<?php
	$counter = 0;
	$columns = $this->profile->columns == 0 ? count($this->categoryChildren) : $this->profile->columns;
	
	while( $counter < count($this->categoryChildren) ):
        $columnCounter = 0;
        for($i=0; $i<$columns; $i++):

			if( isset($this->categoryChildren[$counter]) ):

				$row = $this->categoryChildren[$counter];
                $linkItemid = $this->source == 'component' ? $this->Itemid : igUtilityHelper::getItemid($row->id);
			    $link = JRoute::_('index.php?option=com_igallery&amp;view=category&amp;igid='.$row->id.'&amp;Itemid='.$linkItemid);
                $menuDivWidth = $this->dimensions['menuColumns'][$columnCounter] - ($this->dimensions['menuDivPadding'] * 2);
                ?>
				
				<div class="cat_child" style="width: <?php echo round( ($menuDivWidth / $this->dimensions['menuOverallWidth']) * 100, 2); ?>%; padding: <?php echo round( ( ($this->dimensions['menuDivPadding'] - 1) / $this->dimensions['menuOverallWidth']) * 100, 2); ?>%;" >

                    <h3 class="cat_child_h3">
						<a href="<?php echo $link; ?>" class="cat_child_a"><?php echo $row->name; ?> </a>
					</h3>

					<?php if( isset($row->fileArray)): ?>
						<a href="<?php echo $link; ?>">
						   <img src="<?php echo IG_IMAGE_HTML_RESIZE; ?><?php echo $row->fileArray['folderName']; ?>/<?php echo $row->fileArray['fullFileName']; ?>" alt="<?php echo $row->name; ?>" style="width: <?php echo round( ($row->fileArray['width'] / $menuDivWidth) * 100, 2); ?>%; padding: <?php echo round( ($this->dimensions['menuImgPadding'] / $menuDivWidth) * 100, 2); ?>%; margin: <?php echo round( ($this->dimensions['menuImgMargin'] / $menuDivWidth) * 100, 2); ?>%;"/>
						</a>
                    <?php endif; ?>

					<?php echo $row->menu_description; ?>

					<?php if($this->profile->show_image_count == 1 ): ?>
                        <?php if($row->numimages > 0): ?>
                            <br /><?php echo $row->numimages; ?> <?php echo JText::_('IMAGES'); ?>
                        <?php else: ?>
                            <?php $childCategories = igTreeHelper::getChildIds($allCategories, $row->id); ?>
                            <?php if( count($childCategories) > 0 ): ?>
                                <br /><?php echo count($childCategories); ?> <?php echo JText::_('COM_IGALLERY_CATEGORIES'); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

					<?php if($this->profile->show_category_hits == 1): ?>
					    <br /><?php echo $row->hits; ?> <?php echo JText::_('COM_IGALLERY_VIEWS'); ?>
                    <?php endif; ?>
				</div>
            <?php endif; ?>

			<?php $counter++; ?>
            <?php $columnCounter++; ?>
        <?php endfor ?>
        <div class="igallery_clear"></div>

    <?php endwhile; ?>

</div>
<div class="igallery_clear"></div>
<?php endif; ?>

<?php if($this->profile->menu_pagination == 1): ?>    

	<div class="pagination">
		<?php echo $this->menuPagination->getPagesLinks(); ?>
	</div>

	<div class="pagination">
		<?php echo $this->menuPagination->getPagesCounter(); ?>
	</div>

<?php endif; ?>

</form>
<div class="igallery_clear"></div>