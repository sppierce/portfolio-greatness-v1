<?php defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.framework', true);
if($this->isSite == true)
{
	echo JToolBar::getInstance('toolbar')->render('toolbar');
}
?>
<?php if(!IG_J30): ?>
<script type="text/javascript">
var iToolbarIds = ['toolbar-apply','toolbar-save','toolbar-cancel'];
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
<form action="index.php?option=com_igallery" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="width-60 fltlft form-horizontal" style="width: 60%;">
		<fieldset class="adminform">
			<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>

            <?php echo $wrapperStart; ?>
			<?php echo $labelStart; ?><?php echo $this->form->getLabel('name'); ?><?php echo $labelEnd; ?>
            <?php echo $valueStart; ?><?php echo $this->form->getInput('name'); ?><?php echo $valueEnd; ?>
            <?php echo $wrapperEnd; ?>

			<?php if( $this->isSite == false || $this->params->get('allow_frontend_cat_alias', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('alias'); ?><?php echo $labelEnd; ?>
                <?php echo $valueStart; ?><?php echo $this->form->getInput('alias'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>

			<?php if( $this->isSite == false || $this->params->get('allow_frontend_cat_profile', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('profile'); ?><?php echo $labelEnd; ?>
                <?php echo $valueStart; ?><?php echo $this->form->getInput('profile'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>

			<?php if( $this->isSite == false || $this->params->get('allow_frontend_cat_parent', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('parent'); ?><?php echo $labelEnd; ?>
                <?php echo $valueStart; ?><?php echo $this->form->getInput('parent'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>



			<?php if( $this->isSite == false || $this->params->get('allow_frontend_cat_image', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('upload_image'); ?><?php echo $labelEnd; ?>

                <?php echo $valueStart; ?>
				<?php if(!empty($this->item->menu_image_filename)): ?>
					    <img src="<?php echo IG_IMAGE_HTML_RESIZE; ?><?php echo $this->fileArray['folderName']; ?>/<?php echo $this->fileArray['fullFileName']; ?>" alt=""/>
				<?php endif; ?>
				
				<?php echo $this->form->getInput('upload_image'); ?>
				<?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>
				
			
			<?php if( $this->isSite == false || $this->params->get('allow_frontend_cat_remove', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('remove_menu_image'); ?><?php echo $labelEnd; ?>
                <?php echo $valueStart; ?><?php echo $this->form->getInput('remove_menu_image'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>
				
			<?php if($this->isSite == false): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('user'); ?><?php echo $labelEnd; ?>
                <?php echo $valueStart; ?><?php echo $this->form->getInput('user'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>

            <?php echo $wrapperStart; ?>
			<?php echo $labelStart; ?><?php echo $this->form->getLabel('published'); ?><?php echo $labelEnd; ?>
            <?php echo $valueStart; ?><?php echo $this->form->getInput('published'); ?><?php echo $valueEnd; ?>
            <?php echo $wrapperEnd; ?>
				
			<?php if($this->isSite == false): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('publish_up'); ?><?php echo $labelEnd; ?>
                <?php echo $valueStart; ?><?php echo $this->form->getInput('publish_up'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>

                <?php echo $wrapperStart; ?>
				<?php echo $labelStart; ?><?php echo $this->form->getLabel('publish_down'); ?><?php echo $labelEnd; ?>
                <?php echo $valueStart; ?><?php echo $this->form->getInput('publish_down'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>
				
			<?php if( $this->isSite == false || $this->params->get('allow_frontend_cat_menu_des', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('menu_description'); ?><?php echo $labelEnd; ?>
				<div class="clr"></div>
                <?php echo $valueStart; ?><?php echo $this->form->getInput('menu_description'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>
			
			<?php if( $this->isSite == false || $this->params->get('allow_frontend_cat_gallery_des', 0) == 1): ?>
                <?php echo $wrapperStart; ?>
                <?php echo $labelStart; ?><?php echo $this->form->getLabel('gallery_description'); ?><?php echo $labelEnd; ?>
				<div class="clr"></div>
                <?php echo $valueStart; ?><?php echo $this->form->getInput('gallery_description'); ?><?php echo $valueEnd; ?>
                <?php echo $wrapperEnd; ?>
			<?php endif; ?>

            <?php echo $wrapperStart; ?>
            <?php echo $valueStart; ?><?php echo $this->form->getInput('id'); ?><?php echo $valueEnd; ?>
            <?php echo $wrapperEnd; ?>

            <?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<?php if($this->isSite == true): ?>
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid', 0); ?>" />
	<?php endif; ?>
	<?php echo JHtml::_('form.token'); ?>
</form>