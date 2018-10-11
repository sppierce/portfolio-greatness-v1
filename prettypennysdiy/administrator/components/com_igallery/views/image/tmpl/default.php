<?php defined('_JEXEC') or die();
JHTML::_('behavior.framework', true);
if($this->isSite == true)
{
	echo JToolBar::getInstance('toolbar')->render('toolbar');
}
?>

<?php if(!IG_J30): ?>
<script type="text/javascript">
var iToolbarIds = ['toolbar-apply','toolbar-save','toolbar-forward','toolbar-cancel'];
iToolbarIds.each(function(id, index)
{
    if( !!(document.id(id) || document.id(id) === 0 ) )
	{
		document.id(id).getElement('a').set('href', 'javascript: void(0)');
	}	
});	
</script>
<?php endif; ?>

<?php
if(IG_J30)
{
    $wrapperStart = '<div class="control-group">';
    $wrapperEnd = '</div>';

    $labelStart = '<div class="control-label">';
    $labelEnd = '</div>';

    $valueStart = '<div class="controls">';
    $valueEnd = '</div>';
}
else
{
    $wrapperStart = '';
    $wrapperEnd = '';

    $labelStart = '<li>';
    $labelEnd = '';

    $valueStart = '';
    $valueEnd = '</li>';
}
?>

<div style="clear: both"></div>
<form action="<?php JRoute::_('index.php?option=com_igallery'); ?>" method="post" name="adminForm"  id="adminForm">
	<div class="width-60 fltlft form-horizontal" style="width: 60%;">
		<fieldset class="adminform">

            <?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>

            <?php echo $wrapperStart; ?>
			<?php echo $labelStart; ?><?php echo $this->form->getLabel('description'); ?><div style="clear: both"></div><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('description'); ?><?php echo $valueEnd; ?>
            <?php echo $wrapperEnd; ?>
			
            <?php if( $this->isSite == false || $this->params->get('allow_frontend_photo_tags', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('tags'); ?><?php echo $labelEnd; ?>
                <?php echo $valueStart; ?><?php echo $this->form->getInput('tags'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>

            <?php echo $wrapperStart; ?>
			<?php echo $labelStart; ?><img src="<?php echo IG_IMAGE_HTML_RESIZE; ?><?php echo $this->thumbFile['folderName']; ?>/<?php echo $this->thumbFile['fullFileName']; ?>"
			width="<?php echo $this->thumbFile['width']; ?>" height="<?php echo $this->thumbFile['height'] ?>" alt="<?php echo $this->item->alt_text; ?>"/><?php echo $labelEnd; ?><?php if(!IG_J30): ?></li><?php endif; ?>
            <?php echo $wrapperEnd; ?>
			
			<?php if( $this->isSite == false || $this->params->get('allow_frontend_photo_alt', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('alt_text'); ?><?php echo $labelEnd; ?>
				<?php echo $valueStart; ?><?php echo $this->form->getInput('alt_text'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>
			
			<?php if( $this->isSite == false || $this->params->get('allow_frontend_photo_link', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('link'); ?><?php echo $labelEnd; ?>
				<?php echo $valueStart; ?><?php echo $this->form->getInput('link'); ?><?php echo $valueEnd; ?>
				<?php echo $wrapperEnd; ?>
			<?php endif; ?>
			
			<?php if( $this->isSite == false || $this->params->get('allow_frontend_photo_window', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('target_blank'); ?><?php echo $labelEnd; ?>
				<?php echo $valueStart; ?><?php echo $this->form->getInput('target_blank'); ?><?php echo $valueEnd; ?>
				<?php echo $wrapperEnd; ?>
			<?php endif; ?>
			
			<?php if( $this->isSite == false || $this->params->get('allow_frontend_photo_access', 0) == 1): ?>
            <?php echo $wrapperStart; ?>
            <?php echo $labelStart; ?><?php echo $this->form->getLabel('access'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('access'); ?><?php echo $valueEnd; ?>
			<?php echo $wrapperEnd; ?>
			<?php endif; ?>
			
			<?php if($this->isSite == false): ?>
            <?php echo $wrapperStart; ?>
            <?php echo $labelStart; ?><?php echo $this->form->getLabel('user'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('user'); ?><?php echo $valueEnd; ?>
			<?php echo $wrapperEnd; ?>
			<?php endif; ?>

            <?php echo $wrapperStart; ?>
			<?php echo $labelStart; ?><?php echo $this->form->getLabel('hits'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('hits'); ?><?php echo $valueEnd; ?>
			<?php echo $wrapperEnd; ?>

            <?php echo $wrapperStart; ?>
            <?php echo $labelStart; ?><?php echo $this->form->getLabel('filename'); ?><?php echo $labelEnd; ?>
            <?php echo $valueStart; ?><?php echo $this->form->getInput('filename'); ?><?php echo $valueEnd; ?>
            <?php echo $wrapperEnd; ?>

            <?php echo $wrapperStart; ?>
			<?php echo $labelStart; ?><?php echo $this->form->getLabel('published'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('published'); ?><?php echo $valueEnd; ?>
			<?php echo $wrapperEnd; ?>

            <?php echo $wrapperStart; ?>
			<?php if($this->isSite == false): ?>
			<?php echo $labelStart; ?><?php echo $this->form->getLabel('publish_up'); ?><?php echo $labelEnd; ?>
		    <?php echo $valueStart; ?><?php echo $this->form->getInput('publish_up'); ?><?php echo $valueEnd; ?>
		    <?php echo $wrapperEnd; ?>

            <?php echo $wrapperStart; ?>
			<?php echo $labelStart; ?><?php echo $this->form->getLabel('publish_down'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('publish_down'); ?><?php echo $valueEnd; ?>
			<?php echo $wrapperEnd; ?>
			<?php endif; ?>
			
			<?php if(!IG_J30): ?></ul><?php endif; ?>
			<?php echo $this->form->getInput('id'); ?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<?php if($this->isSite == true): ?>
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid', 0); ?>" />
	<?php endif; ?>
	<input type="hidden" name="catid" value="<?php echo $this->category->id; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>