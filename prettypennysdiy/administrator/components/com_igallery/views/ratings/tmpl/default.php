<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.framework', true);
$editStateOk = igGeneralHelper::authorise('core.edit.state');
?>

<form action="index.php?option=com_igallery" method="post" name="adminForm" id="adminForm">

<table class="adminlist table table-striped">
<thead>
	<tr>
		
		<th width="20">
			<input type="checkbox" name="toggle" value="" <?php if(!IG_J30): ?>onclick="checkAll(<?php echo count( $this->items ); ?>);"<?php endif; ?><?php if(IG_J30): ?>onclick="Joomla.checkAll(this)"<?php endif; ?> />
		</th>

        <th class="title" align="left">
            <?php echo JText::_('THUMBNAIL')?>
        </th>

        <th width="160" nowrap="nowrap">
            <?php echo JText::_('RATING'); ?>
        </th>
		
		<th class="title" align="left">
			<?php echo JText::_('JAUTHOR')?>
		</th>

        <th class="title" align="left">
            <?php echo JText::_('JCATEGORY')?>
        </th>

        <th class="title" align="left">
            <?php echo JText::_('IP_ADDRESS')?>
        </th>

        <th width="80" nowrap="nowrap">
            <?php echo JText::_('JDATE'); ?>
        </th>

		<th width="80" nowrap="nowrap">
			<?php echo JText::_('JPUBLISHED'); ?>	
		</th>

        <th width="5%" class="nowrap">
			<?php echo JText::_('JGRID_HEADING_ID'); ?>
		</th>
		
	</tr>
</thead>	
<tfoot>
	<tr>
		<td colspan="9">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>

<tbody>
<?php


	foreach($this->items as $i => $item):
	?>
	<tr class="row<?php echo $i % 2; ?>">
		
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>

		<td>
			<img src="<?php echo IG_IMAGE_HTML_RESIZE; ?><?php echo $this->thumbFiles[$i]['folderName']; ?>/<?php echo $this->thumbFiles[$i]['fullFileName']; ?>"
			width="<?php echo $this->thumbFiles[$i]['width']; ?>" height="<?php echo $this->thumbFiles[$i]['height'] ?>" alt="<?php echo $item->alt_text; ?>"/>
		</td>

        <td>
            <?php for($k=0; $k<$item->rating; $k++): ?>
            	<img src="<?php echo IG_IMAGE_ASSET_PATH ?>star-on.png" alt="star" />
            <?php endfor; ?>
        </td>

        <td>
            <?php if(!empty($item->author_name) ){ echo $item->author_name; }else{  echo JText::_('GUEST'); }  ?>
        </td>

        <td>
            <a target="_blank" href="<?php echo JRoute::_(IG_HOST.'/index.php?option=com_igallery&view=category&igid='.$item->category_id); ?>"><?php echo $item->category_name;?></a>
        </td>

        <td>
            <?php echo $item->ip; ?>
        </td>

        <td>
            <?php echo date('l jS \of F Y h:i:s A', $item->date); ?>
        </td>
		
		<td class="center">
				<?php echo JHtml::_('jgrid.published', $item->published, $i, 'ratings.',true);?>
		</td>
		
		<td class="center">
			<?php echo $item->id; ?>
		</td>
		
	</tr>
	<?php endforeach; ?>

</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>
</form>

