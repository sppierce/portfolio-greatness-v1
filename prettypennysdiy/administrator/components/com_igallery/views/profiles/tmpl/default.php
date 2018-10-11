<?php defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.framework', true);
$editStateOk = igGeneralHelper::authorise('core.edit.state');
?>

<?php if(IG_J30): ?>
<style type="text/css">
td.order input{
width: 40px !important;
}
</style>
<?php endif; ?>

<form action="index.php?option=com_igallery" method="post" name="adminForm" id="adminForm">

<table class="adminlist table table-striped" id="articleList">
<thead>
	<tr>
		
		<th width="20">
			<input type="checkbox" name="toggle" value="" <?php if(!IG_J30): ?>onclick="checkAll(<?php echo count( $this->items ); ?>);"<?php endif; ?><?php if(IG_J30): ?>onclick="Joomla.checkAll(this)"<?php endif; ?> />
		</th>
		
		<th class="title" align="left">
			<?php echo JText::_('JGLOBAL_TITLE')?>
		</th>
		
		<th width="80" nowrap="nowrap">
			<?php echo JText::_('JPUBLISHED'); ?>	
		</th>
		
		<th width="10%">
				<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
				<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'profiles.saveorder'); ?>
		</th>
		
		<th width="5%" class="nowrap center">
			<?php echo JText::_('JGRID_HEADING_ID'); ?>
		</th>
		
	</tr>
</thead>	
<tfoot>
	<tr>
		<td colspan="5">
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
		
		<?php $item->name = strlen($item->name) < 1 ? '____' : $item->name ?>
		<td>
			<a href="index.php?option=com_igallery&amp;view=profile&amp;id=<?php echo $item->id; ?>"><?php echo $this->escape($item->name);?></a>
		</td>
		
		<td class="center">
				<?php echo JHtml::_('jgrid.published', $item->published, $i, 'profiles.',$editStateOk);?>
		</td>
		
		<td class="order nowrap center" >
			<span><?php echo $this->pagination->orderUpIcon($i, isset($this->items[$i-1]), 'profiles.orderup', 'JLIB_HTML_MOVE_UP', $editStateOk ); ?></span>
			<span><?php echo $this->pagination->orderDownIcon($i, count($this->items), isset($this->items[$i+1]), 'profiles.orderdown', 'JLIB_HTML_MOVE_DOWN', $editStateOk ); ?></span>
			<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="text-area-order" />
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