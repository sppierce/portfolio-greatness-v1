<?php defined('_JEXEC') or die;
JHTML::_('behavior.framework', true);
if($this->isSite == true)
{
	echo JToolBar::getInstance('toolbar')->render('toolbar');
}

?>
<?php if(!IG_J30): ?>
<script type="text/javascript">

    var buttonsArray = document.id('toolbar').getElements('a[class=toolbar]');

    buttonsArray.each(function(el,index)
    {
        el.set('href', 'javascript: void(0)');
    });
</script>
<?php endif; ?>

<?php if(IG_J30): ?>
<style type="text/css">
td.order input{
width: 40px !important;
}
</style>
<?php endif; ?>

<div style="clear: both"></div>
<form action="index.php?option=com_igallery&view=categories<?php if($this->isSite == true){ echo '&Itemid='. JRequest::getInt('Itemid', 0); } ?>" method="post" name="adminForm" id="adminForm">

<fieldset id="filter-bar">
	<div class="filter-search fltlft btn-group pull-left">
		<label class="filter-search-lbl element-invisible" for="filter_search" style="float: left;"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" />
		<?php if(IG_J30): ?></div><div class="btn-group pull-left"><?php endif; ?>
		<button type="submit" class="btn hasTooltip"><?php if(IG_J30): ?><i class="icon-search"></i><?php endif; ?><?php if(!IG_J30): ?><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?><?php endif; ?></button>
		<button type="button" class="btn hasTooltip" onclick="document.id('filter_search').value='';this.form.submit();"><?php if(IG_J30): ?><i class="icon-remove"></i><?php endif; ?><?php if(!IG_J30): ?><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?><?php endif; ?></button>
    </div>

	<div class="filter-select fltrt pull-right">
		<select name="filter_published" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
			<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions',array('archived'=>0,'trash'=>0,'all'=>0)), 'value', 'text', $this->state->get('filter.published'), true);?>
		</select>
	</div>
	<?php if(IG_J30): ?>
	<div class="btn-group pull-right">
        <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
        <?php echo $this->pagination->getLimitBox(); ?>
    </div>
    <?php endif; ?>
</fieldset>
<div style="clear:both; height: 0px; overflow: hidden;"> </div>

<table class="adminlist table table-striped">
	<thead>
		<tr>
			
			<th width="20">
				<input type="checkbox" name="toggle" value="" <?php if(!IG_J30): ?>onclick="checkAll(<?php echo count( $this->items ); ?>);"<?php endif; ?><?php if(IG_J30): ?>onclick="Joomla.checkAll(this)"<?php endif; ?> />
			</th>
			
			<th>
				<?php echo JText::_('JGLOBAL_TITLE'); ?>
			</th>
			
			<th>
				<?php echo JText::_( 'MANAGE_IMAGES' ); ?>
			</th>
			
			<th>
				<?php echo JText::_('JAUTHOR' ); ?>
			</th>
			
			<th>
				<?php echo JText::_('PROFILE'); ?>
			</th>
			
			<th width="5%">
				<?php echo JText::_('JPUBLISHED'); ?>
			</th>
			
			<?php if( !empty($this->moderate) ): ?>
			<th width="5%">
				<?php echo JText::_( 'APPROVED' ); ?>
			</th>
			<?php endif; ?>
			
			<th style="width:120px;">
				<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
				<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'categories.saveorder'); ?>
			</th>
			
			<?php if($this->isSite == false): ?>
			<th width="1%" class="nowrap">
					<?php echo JText::_('JGRID_HEADING_ID'); ?>
			</th>
			<?php endif; ?>
		</tr>
	</thead>

<?php $footerColumns = empty($this->moderate) ? 8 : 9; ?>		
	<tfoot>
		<tr>
			<td colspan="<?php echo $footerColumns; ?>">
			<?php if($this->isSite == false): ?>
				<?php echo $this->pagination->getListFooter(); ?>
			<?php endif; ?>
			</td>
		</tr>
	</tfoot>
	
	<tbody>	
    <?php

	foreach($this->items as $i => $item):
		
		$editOk = igGeneralHelper::authorise('core.edit', $item->id, null, $item->profile, $item->user);
		$editStateOk = igGeneralHelper::authorise('core.edit.state', $item->id, null, $item->profile, $item->user);

		?>
		<tr class="row<?php echo $i % 2; ?>">
			
			<td class="center">
				<?php echo JHtml::_('grid.id', $i, $item->id); ?>
			</td>
			
			<td>
				<?php if($editOk): ?>
				<a href="<?php echo JRoute::_('index.php?option=com_igallery&view=icategory&id='.$item->id, false); ?>">
				<?php endif; ?>
				
                <?php $item->name = strlen($item->name) < 1 ? '____' : $item->name ?>
                <?php for($k=0;$k<$item->level;$k++){echo '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';}if($item->level>0){echo'<sup>|_</sup>&nbsp;';}echo $this->escape($item->name); ?>
				
				<?php if($editOk): ?>
				</a>
				<?php endif; ?>
			</td>
			
			<td class="center">
			<?php if($editOk): ?>
				<a href="<?php echo JRoute::_('index.php?option=com_igallery&view=images&catid='.$item->id, false); ?>">
			<?php endif; ?>
					<?php echo JText::_( 'MANAGE_IMAGES' ); ?> (<?php echo $item->numimages; ?>)
			<?php if($editOk): ?>
				</a>
			<?php endif; ?>
			</td>
			
			<td class="center">
				<?php echo empty($item->name_of_user) ? 'Error: No User With Id: '.$item->user.' Found' : $item->name_of_user; ?>
			</td>
			
			<td class="center">
			<?php if($this->canConfigure && $this->isSite == false): ?>
				<a href="index.php?option=com_igallery&view=profile&id=<?php echo $item->profile; ?>">
			<?php endif; ?>
			
			<?php echo $item->profile_name; ?>
			
			<?php if($this->canConfigure && $this->isSite == false): ?>
				</a>
			<?php endif; ?>
			
			</td>
			
			<td class="center">
				<?php echo JHtml::_('jgrid.published', $item->published, $i, 'categories.', $editStateOk, 'cb', $item->publish_up, $item->publish_down);?>
			</td>
			
			<?php if( !empty($this->moderate) ): ?>
			<td align="center">
				<?php echo igHtmlHelper::moderateImage($item, $i, 'categories', $this->isSite); ?>
			</td>	
			<?php endif; ?>
			
			<td class="order nowrap center" style="width:120px;">
			<?php
				$showOrderUp = false;
				$showOrderDown = false;
				foreach($this->items as $key =>$value)
				{
					if ($item->parent == $value->parent && $item->ordering > $value->ordering)
					{
						$showOrderUp = true;
					}
					
					if ($item->parent == $value->parent && $item->ordering < $value->ordering)
					{
						$showOrderDown = true;
					}
				}
    		?>
			
				<span><?php echo $this->pagination->orderUpIcon($i, $showOrderUp, 'categories.orderup', 'JLIB_HTML_MOVE_UP', $editStateOk);?></span>
				<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, $showOrderDown, 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $editStateOk ); ?></span>
				<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="text-area-order" />
			</td>
			
			<?php if($this->isSite == false): ?>
			<td class="center">
				<?php echo $item->id; ?>
			</td>
			<?php endif; ?>
			
		</tr>
		<?php endforeach; ?>
</tbody>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>
</form>

