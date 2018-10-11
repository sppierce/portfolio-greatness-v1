<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.framework', true);
JHtml::_('behavior.modal');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>

<?php if($this->isSite == true): ?>
	<div style="clear: both"></div>
	<h2 style="margin-top: 20px;"><?php echo $this->category->name; ?></h2>
	
	<?php echo JToolBar::getInstance('toolbar')->render('toolbar'); ?>
	<div style="clear: both"></div>
<?php endif; ?>

<?php if(IG_J30): ?>
<style type="text/css">
td.order input{
width: 40px !important;
}

.copy_move_wrapper #copy_move{
float: left;
width: 85px !important;
font-size: 10px !important;
line-height: 15px !important;
}

.copy_move_wrapper #cat_id_copy_move{
float: left;
width: 135px !important;
font-size: 10px !important;
line-height: 15px !important;
}


.copy_move_wrapper select option{
font-size: 10px !important;
line-height: 15px !important;
}
</style>
<?php endif; ?>

<?php if(!IG_J30): ?>
<script type="text/javascript">
var iToolbarIds = ['toolbar-publish','toolbar-unpublish','toolbar-delete'];
iToolbarIds.each(function(id, index)
{
    if( !!(document.id(id) || document.id(id) === 0 ) )
	{
		document.id(id).getElement('a').set('href', 'javascript: void(0)');
	}	
});	
</script>
<?php endif; ?>

<?php if( $this->showImportServer ): ?>

<script type="text/javascript">
window.addEvent('load', function() {

	document.id('server_import').addEvent('change', function(e)
	{
		var path = document.id('server_import').getProperty('value');
		document.id('server_import_link').setProperty('href','index.php?option=com_igallery&view=serverimport&catid=<?php echo $this->category->id; ?>&tmpl=component&path=' + path);
	});
});
</script>


<fieldset class="adminform">
<form action="index.php?option=com_igallery&view=serverimport&tmpl=component" method="post" name="serverImport">
<table>
	<tr>
		
		<td>
		<ul class="adminformlist">
		<li>
		<?php echo $this->imagesForm->getLabel('server_import');
		echo $this->imagesForm->getInput('server_import'); ?>
		</li>
		</ul>

		</td>
		
		<td>
			<a class="modal" id="server_import_link" href="#" rel="{handler: 'iframe', size: {x: 400, y: 300}, onClose: function() {}}">
				<input type="button" name="import_server_button" value="<?php echo JText::_('JSUBMIT'); ?>" />
			</a>
		</td>	
</tr>
</table>
</form>
</fieldset>
<div style="clear:both; height: 0px; overflow: hidden;"></div>

<?php endif;?>

<form action="index.php?option=com_igallery&view=images&catid=<?php echo $this->category->id; ?><?php if($this->isSite == true){ echo '&Itemid='. JRequest::getInt('Itemid', 0); } ?>" method="post" name="adminForm" id="adminForm">

<fieldset id="filter-bar">
		<div class="filter-search fltlft btn-group pull-left"">
			<label class="filter-search-lbl element-invisible" for="filter_search" style="float: left;"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" />
			<?php if(IG_J30): ?></div><div class="btn-group pull-left"><?php endif; ?>
			<button type="submit" class="btn hasTooltip"><?php if(IG_J30): ?><i class="icon-search"></i><?php endif; ?><?php if(!IG_J30): ?><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?><?php endif; ?></button>
			<button type="button" class="btn hasTooltip" onclick="document.id('filter_search').value='';this.form.submit();"><?php if(IG_J30): ?><i class="icon-remove"></i><?php endif; ?><?php if(!IG_J30): ?><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?><?php endif; ?></button>
		</div>
		<div class="filter-select fltrt" style="float: right;">
			
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions',array('archived'=>0,'trash'=>0,'all'=>0)), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			<?php if($this->isSite == false):
			 echo $this->catDropDown; 
			 endif; ?>

		</div>

		<?php if(IG_J30): ?>
        <div class="btn-group pull-right">
            <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
            <?php echo $this->pagination->getLimitBox(); ?>
        </div>
        <?php endif; ?>


	</fieldset>
	<div style="clear:both; height: 0px; overflow: hidden;"> </div>

<fieldset class="adminform">
<table class="adminlist table table-striped">
<thead>
	<tr>

		<th width="20">
			<input type="checkbox" name="toggle" value="" <?php if(!IG_J30): ?>onclick="checkAll(<?php echo count( $this->items ); ?>);"<?php endif; ?><?php if(IG_J30): ?>onclick="Joomla.checkAll(this)"<?php endif; ?> />
		</th>
		
		<th width="20">
			<?php echo JText::_( 'MENU_IMAGE' )?>
		</th>

		<th class="title" >
			<?php echo JText::_( 'THUMBNAIL' )?>
		</th>
		
		<?php if($this->isSite == false): ?>
		<th class="title">
			<?php echo JHTML::_('grid.sort', JText::_( 'FILENAME' ),
			'filename', $listDirn, $listOrder); ?>
		</th>
		<?php endif; ?>
		
		<?php if($this->isSite == false): ?>
		<th class="title">
			<?php echo JText::_('MAIN_IMAGE_FILESIZE'); ?>
		</th>
		<?php endif; ?>

		<th class="title">
			<?php echo JHTML::_('grid.sort', JText::_( 'JGLOBAL_DESCRIPTION' ), 'description',
			$listDirn, $listOrder ); ?>
		</th>
		
		<th class="title">
			<?php echo JHTML::_('grid.sort', JText::_( 'TAGS' ), 'tags',
			$listDirn, $listOrder ); ?>
		</th>
		
		<?php if($this->isSite == false): ?>
		<th class="title">
			<?php echo JHTML::_('grid.sort', JText::_( 'IMAGE_LINK' ), 'link',
			$listDirn, $listOrder ); ?>
		</th>
		<?php endif; ?>
		
		<th class="title" width="60">
			<?php echo JText::_( 'ROTATE' )?>
		</th>

		<th width="5%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_( 'JPUBLISHED' ), 'published',
			$listDirn, $listOrder ); ?>
		</th>
		
		<th width="5%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_( 'JGLOBAL_HITS' ), 'hits',
			$listDirn, $listOrder ); ?>
		</th>
		
		<?php if( !empty($this->moderate) ): ?>
		<th width="5%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_( 'APPROVED' ), 'moderate',
			$listDirn, $listOrder ); ?>
		</th>
		<?php endif; ?>

		<?php if($this->isSite == false): ?>
		<th width="5%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_( 'JGRID_HEADING_ACCESS' ), 'access',
			$listDirn, $listOrder ); ?>
		</th>
		<?php endif; ?>
		
		<th nowrap="nowrap">
			<?php echo JHTML::_('grid.sort',  JText::_( 'JGRID_HEADING_ORDERING' ), 'ordering',
			$listDirn, $listOrder); ?>
	 	</th>

		<th width="1%">
			<?php echo JHTML::_('grid.order',  $this->items, 'filesave.png', 'images.saveorder' ); ?>
		</th>

        <th width="1%" nowrap="nowrap">
            <?php echo JHTML::_('grid.sort', JText::_( 'JGRID_HEADING_ID' ), 'id',
            $listDirn, $listOrder ); ?>
        </th>

	</tr>
</thead>

<tfoot>
	<?php 
	$columns = $this->isSite == false ? 15 : 11;
	$columns = empty($this->moderate) ? $columns : $columns + 1;
	$columnsUsed = 0;
	if($this->isSite == false): ?>
	<tr>
	
		<?php if( !empty($this->category->id) ):?>
		<td colspan="5" style="text-align:left" class="copy_move_wrapper">
				<?php echo $this->copyMove; ?> <span style="float: left; margin: 8px 4px 0px 4px;"><?php echo JText::_('SELECTED_ITEMS_TO'); ?> </span><?php echo $this->catCopyMove; ?>
				<a href="#" onclick="javascript: submitbutton('images.copy_move')"><input type="button" name="copy_move_button" value="<?php echo JText::_('JSUBMIT'); ?>" /></a>
		</td>
		<?php $columnsUsed = $columnsUsed + 5; ?>
		<?php endif; ?>
		
		<td colspan="4" style="text-align:left">
			<span style="float: left; margin-top: 8px; font-size: 12px;"><?php echo JText::_('ADD_TAGS_TO_SELECTED'); ?>:</span> <input type="text" name="add_tags" id="add_tags" style="width: 80px;" />
			<a href="#" onclick="javascript: submitbutton('images.add_tags')"><input type="button" name="add_tags_button" value="<?php echo JText::_('JSUBMIT'); ?>" /></a>
		</td>
		<?php $columnsUsed = $columnsUsed + 4; ?>
		
		<td colspan="<?php echo $columns - $columnsUsed; ?>" style="text-align:left">
			<span style="float: left; margin-top: 8px; font-size: 12px;"><?php echo JText::_('REMOVE_TAGS_FROM_SELECTED'); ?>:</span> <input type="text" name="remove_tags" id="remove_tags" style="width: 80px;"/>
			<a href="#" onclick="javascript: submitbutton('images.remove_tags')"><input type="button" name="remove_tags_button" value="<?php echo JText::_('JSUBMIT'); ?>" /></a>
		</td>
		
	</tr>
	<?php endif; ?>
	
	<tr>
		<td colspan="<?php echo $columns; ?>">
		<?php if($this->isSite == false): ?>
			<?php echo $this->pagination->getListFooter(); ?>
		<?php endif; ?>
			</td>
		</tr>
</tfoot>

<?php
foreach ($this->items as $i => $item) :
	
	$editOk = igGeneralHelper::authorise('core.edit', $item->gallery_id, null, $item->id_of_profile, $item->category_owner);
	$editStateOk = igGeneralHelper::authorise('core.edit.state', $item->gallery_id, null, $item->id_of_profile, $item->category_owner);
	$editOwn = igGeneralHelper::authorise('core.edit.own', $item->gallery_id, null, $item->id_of_profile, $item->category_owner) && $item->user == $this->user->id;
	
	$editLink 	= JRoute::_('index.php?option=com_igallery&view=image&id='.$item->id, false);
	?>
	<tr class="row<?php echo $i % 2; ?>">

		<td>
			<?php echo JHTML::_('grid.id', $i, $item->id ); ?>
		</td>
		
		<td>
		<?php if($item->menu_image == 1): ?>
			<a href="<?php echo JRoute::_('index.php?option=com_igallery&task=images.assignMenuImage&catid='.$item->gallery_id.'&id='.$item->id.'&cvalue=0', false) ?>">
				<img src="<?php echo IG_HOST; ?>media/com_igallery/images/menu/icon-16-default.png" />
			</a>
		<?php else: ?>
			<a href="<?php echo JRoute::_('index.php?option=com_igallery&task=images.assignMenuImage&catid='.$item->gallery_id.'&id='.$item->id.'&cvalue=1', false) ?>">
				<img src="<?php echo IG_HOST; ?>media/com_igallery/images/menu/icon-16-notdefault.png" />
			</a>
		<?php endif; ?>
		</td>

		<td>
			<img src="<?php echo IG_IMAGE_HTML_RESIZE; ?><?php echo $this->thumbFiles[$i]['folderName']; ?>/<?php echo $this->thumbFiles[$i]['fullFileName']; ?>"
			width="<?php echo $this->thumbFiles[$i]['width']; ?>" height="<?php echo $this->thumbFiles[$i]['height'] ?>" alt="<?php echo $item->alt_text; ?>"/>
		</td>
		
		<?php if($this->isSite == false): ?>
		<td align="center">
			<?php echo $item->filename;
			
			if( empty($this->category->id) )
			{
				?><br /><a href="<?php echo JRoute::_('index.php?option=com_igallery&view=images&catid='.$item->gallery_id, false); ?>"><?php echo $item->name;?></a><?php
			}
			?>
		
		</td>
		<?php endif; ?>
		
		<?php if($this->isSite == false): ?>
		<td align="center">
			<?php echo round($this->mainFiles[$i]['filesize']/1000); ?>Kb
		</td>
		<?php endif; ?>

		<td align="center">
			<?php echo $item->description;?>
			
			<?php if($editOk || $editOwn): ?>
				<a href="<?php echo $editLink;?>"><?php echo JText::_( 'JACTION_EDIT' ); ?></a>
			<?php endif; ?>
		</td>
		
		<td align="center">
			<?php echo $item->tags;?>
			<?php if($editOk || $editOwn): ?>
				<a href="<?php echo $editLink;?>"><?php echo JText::_( 'JACTION_EDIT' ); ?></a>
			<?php endif; ?>
		</td>
		
		<?php if($this->isSite == false): ?>
		<td align="center">
			<?php echo $item->link;?>
			<?php if($editOk || $editOwn): ?>
				<a href="<?php echo $editLink;?>"><?php echo JText::_( 'JACTION_EDIT' ); ?></a>
			<?php endif; ?>
		</td>
		<?php endif; ?>
		
		<td style="text-align: center;">
		<?php if($editStateOk): ?>
			<a href="<?php echo JRoute::_('index.php?option=com_igallery&task=images.rotate&catid='.$item->gallery_id.'&id='.$item->id.'&rvalue=0', false); ?>">
				<img src="<?php echo IG_HOST; ?>media/com_igallery/images/rotate-left.png" style="float: none;" />
			</a>
		
			<a href="<?php echo JRoute::_('index.php?option=com_igallery&task=images.rotate&catid='.$item->gallery_id.'&id='.$item->id.'&rvalue=1', false); ?>">
				<img src="<?php echo IG_HOST; ?>media/com_igallery/images/rotate-right.png" style="float: none;" />
			</a>
		<?php endif; ?>
		</td>

		<td align="center">
			<?php echo JHtml::_('jgrid.published', $item->published, $i, 'images.', $editStateOk, 'cb', $item->publish_up, $item->publish_down); ?>
		</td>
		
		<td align="center">
			<?php echo $item->hits; ?>
		</td>
		
		<?php if( !empty($this->moderate) ): ?>
		<td align="center">
			<?php echo igHtmlHelper::moderateImage($item, $i, 'images', $this->isSite); ?>
		</td>	
		<?php endif; ?>
		
		<?php if($this->isSite == false): ?>
		<td align="center">
		<?php if($editOk || $editOwn): ?>
			<a href="<?php echo $editLink;?>">
		<?php endif; ?>
			
		<?php echo $item->access_group_name;?>
		
		<?php if($editOk || $editOwn): ?>
		</a>
		<?php endif; ?>
		</td>
		<?php endif; ?>

		<td class="order nowrap center" colspan="2">
			<span><?php echo $this->pagination->orderUpIcon($i, isset($this->items[$i-1]), 'images.orderup', 'JLIB_HTML_MOVE_UP', $editStateOk ); ?></span>
            <span><?php echo $this->pagination->orderDownIcon($i, count($this->items), isset($this->items[$i+1]), 'images.orderdown', 'JLIB_HTML_MOVE_DOWN', $editStateOk ); ?></span>
            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="text_area" style="text-align: center" />
		</td>

        <td align="center">
            <?php echo $item->id; ?>
        </td>

	</tr>
	<?php endforeach; ?>

</table>


<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<input type="hidden" name="boxchecked" value="0" />

<?php echo JHtml::_('form.token'); ?>
</fieldset>
</form>

