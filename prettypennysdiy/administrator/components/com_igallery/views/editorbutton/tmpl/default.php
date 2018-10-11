<?php defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.framework', true);
?>

<form action="index.php?option=com_igallery" method="post" name="adminForm" id="adminForm">
	<div class="width-100 fltlft">
		<fieldset class="adminform">
			<ul class="adminformlist">
			
			<li><?php echo $this->form->getLabel('ig_type'); ?>
			<?php echo $this->form->getInput('ig_type'); ?></li>

			<li><?php echo $this->form->getLabel('ig_category'); ?>
			<?php echo $this->form->getInput('ig_category'); ?></li>
			
			<li><?php echo $this->form->getLabel('ig_include_child'); ?>
			<?php echo $this->form->getInput('ig_include_child'); ?></li>
			
			<li><?php echo $this->form->getLabel('ig_add_links'); ?>
			<?php echo $this->form->getInput('ig_add_links'); ?></li>
			
			
			<li><?php echo $this->form->getLabel('ig_profile'); ?>
			<?php echo $this->form->getInput('ig_profile'); ?></li>
			
			<li><?php echo $this->form->getLabel('ig_tags'); ?>
			<?php echo $this->form->getInput('ig_tags'); ?></li>
			
			<li><?php echo $this->form->getLabel('ig_max_images'); ?>
			<?php echo $this->form->getInput('ig_max_images'); ?></li>
			
			</ul>

		</fieldset>
	</div>
	<div class="clr"></div>
	<input type="button" onclick="insertToken();window.parent.SqueezeBox.close();" name="editor_submit" value="<?php echo JText::_( 'JSAVE' ); ?>" style="width:80px;" />
	<input type="button" onclick="window.parent.window.parent.SqueezeBox.close();" name="editor_cancel" value="<?php echo JText::_( 'JCANCEL' ); ?>" style="width:80px;" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>