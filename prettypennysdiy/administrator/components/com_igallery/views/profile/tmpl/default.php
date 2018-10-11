<?php defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.framework', true);
?>

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

<form action="index.php?option=com_igallery" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php if(!IG_J30): ?>
	<div class="width-100 fltlft">
	<?php endif; ?>

	<?php if(IG_J30): ?>
	<div class="row-fluid">
	<div class="span10 form-horizontal">
	<?php endif; ?>

		<?php if(!IG_J30): ?>
		<?php echo JHtml::_('tabs.start','igallery_profiles', array('useCookie'=>1)); ?>
		<?php endif; ?>

        <?php if(IG_J30): ?>
		<ul class="nav nav-tabs">
            <li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('GENERAL'); ?></a></li>
            <li><a href="#igmenu" data-toggle="tab"><?php echo JText::_('MENU'); ?></a></li>
            <li><a href="#main-image" data-toggle="tab"><?php echo JText::_('MAIN_IMAGE'); ?></a></li>
            <li><a href="#main-thumbs" data-toggle="tab"><?php echo JText::_('MAIN_THUMBNAILS'); ?></a></li>
            <li><a href="#main-other" data-toggle="tab"><?php echo JText::_('MAIN_OTHER'); ?></a></li>
            <li><a href="#lbox-image" data-toggle="tab"><?php echo JText::_('LIGHTBOX_IMAGE'); ?></a></li>
            <li><a href="#lbox-thumbs" data-toggle="tab"><?php echo JText::_('LIGHTBOX_THUMBNAILS'); ?></a></li>
            <li><a href="#lbox-other" data-toggle="tab"><?php echo JText::_('LIGHTBOX_OTHER'); ?></a></li>
            <li><a href="#acl" data-toggle="tab"><?php echo JText::_('JCONFIG_PERMISSIONS_LABEL'); ?></a></li>
        </ul>
        <?php endif; ?>

        <?php if(IG_J30): ?>
        <div class="tab-content">
        <?php endif; ?>

        <?php if(!IG_J30): ?>
        <?php echo JHtml::_('tabs.panel',JText::_('GENERAL'), 'general'); ?>
        <?php endif; ?>

        <?php if(IG_J30): ?>
        <div class="tab-pane active" id="general">
        <?php endif; ?>

        <fieldset class="adminform">
		<legend><?php echo JText::_('GENERAL'); ?></legend>

			<?php if(!IG_J30): ?>
			<ul class="adminformlist">
			<?php endif; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('name'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('name'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('published'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('published'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('access'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('access'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('img_quality'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('img_quality'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_search'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_search'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('search_results'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('search_results'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_cat_title'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_cat_title'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('style'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('style'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('refresh_mode'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('refresh_mode'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>

            <?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('image_ordering'); ?><?php echo $labelEnd; ?>
            <?php echo $valueStart; ?><?php echo $this->form->getInput('image_ordering'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		<?php if(!IG_J30): ?>
		</ul>
		<?php endif; ?>

		</fieldset>
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('CORNERS'); ?></legend>

		<?php if(!IG_J30): ?>
        <ul class="adminformlist">
        <?php endif; ?>
		
		<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('round_large'); ?><?php echo $labelEnd; ?>
	    <?php echo $valueStart; ?><?php echo $this->form->getInput('round_large'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
		<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('round_thumb'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('round_thumb'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		
		<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('round_menu'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('round_menu'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		
		<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('round_fill'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('round_fill'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		<?php if(!IG_J30): ?>
		</ul>
		<?php endif; ?>
		</fieldset>
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('WATERMARK'); ?></legend>

		<?php if(!IG_J30): ?>
        <ul class="adminformlist">
        <?php endif; ?>

			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('watermark'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('watermark'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('watermark_position'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('watermark_position'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('watermark_transparency'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('watermark_transparency'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('watermark_text'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('watermark_text'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('watermark_text_color'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('watermark_text_color'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('watermark_text_size'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('watermark_text_size'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('watermark_filename'); ?><?php echo $labelEnd; ?>
			
			<?php if( strlen($this->item->watermark_filename) > 1 ): ?>
		  		<img src="<?php echo IG_IMAGE_HTML_WATERMARK; ?><?php echo $this->item->watermark_filename; ?>" alt=""/>
			<?php endif; ?>	
			
		    <?php echo $valueStart; ?><?php echo $this->form->getInput('watermark_filename'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('remove_wm_image'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('remove_wm_image'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		<?php if(!IG_J30): ?>
		</ul>
		<?php endif; ?>
		</fieldset>

		<?php if(IG_J30): ?>
		</div>
		<?php endif; ?>
	
	<?php if(!IG_J30): ?>
	<?php echo JHtml::_('tabs.panel',JText::_('MENU'), 'igmenu'); ?>
	<?php endif; ?>

	<?php if(IG_J30): ?>
    <div class="tab-pane" id="igmenu">
    <?php endif; ?>

		<fieldset class="adminform">
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
		
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('menu_access'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('menu_access'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('menu_max_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('menu_max_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('menu_max_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('menu_max_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('crop_menu'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('crop_menu'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('menu_image_defaults'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('menu_image_defaults'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('columns'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('columns'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_category_hits'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_category_hits'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_image_count'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_image_count'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('menu_pagination'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('menu_pagination'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('menu_pagination_amount'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('menu_pagination_amount'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		<?php if(!IG_J30): ?></ul><?php endif; ?>
		
		</fieldset>

		<?php if(IG_J30): ?>
		</div>
		<?php endif; ?>

		<?php if(!IG_J30): ?>
        <?php echo JHtml::_('tabs.panel',JText::_('MAIN_IMAGE'), 'main-image'); ?>
        <?php endif; ?>

        <?php if(IG_J30): ?>
        <div class="tab-pane" id="main-image">
        <?php endif; ?>
		
        <fieldset class="adminform">
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_large_image'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_large_image'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('max_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('max_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('max_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('max_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('crop_main'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('crop_main'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('main_image_align_horiz'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('main_image_align_horiz'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('main_image_align_vert'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('main_image_align_vert'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('img_container_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('img_container_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('img_container_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('img_container_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('fade_duration'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('fade_duration'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('preload'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('preload'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('magnify'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('magnify'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		<?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>

		<?php if(IG_J30): ?>
		</div>
		<?php endif; ?>

		<?php if(!IG_J30): ?>
        <?php echo JHtml::_('tabs.panel',JText::_('MAIN_THUMBNAILS'), 'main-thumbs'); ?>
        <?php endif; ?>

        <?php if(IG_J30): ?>
        <div class="tab-pane" id="main-thumbs">
        <?php endif; ?>
		
		<fieldset class="adminform">
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_thumbs'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_thumbs'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('thumb_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('thumb_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('thumb_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('thumb_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('crop_thumbs'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('crop_thumbs'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('thumb_position'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('thumb_position'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('images_per_row'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('images_per_row'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('thumb_container_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('thumb_container_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('thumb_container_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('thumb_container_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('thumb_scrollbar'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('thumb_scrollbar'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('arrows_up_down'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('arrows_up_down'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('arrows_left_right'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('arrows_left_right'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_thumb_info'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_thumb_info'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('scroll_speed'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('scroll_speed'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('scroll_boundary'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('scroll_boundary'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('thumb_pagination'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('thumb_pagination'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('thumb_pagination_amount'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('thumb_pagination_amount'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
		<?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>

		<?php if(IG_J30): ?>
		</div>
		<?php endif; ?>

		<?php if(!IG_J30): ?>
        <?php echo JHtml::_('tabs.panel',JText::_('MAIN_OTHER'), 'main-other'); ?>
        <?php endif; ?>

        <?php if(IG_J30): ?>
        <div class="tab-pane" id="main-other">
        <?php endif; ?>
		
		<fieldset class="adminform">
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('allow_comments'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('allow_comments'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>

			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('allow_rating'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('allow_rating'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>

			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('gallery_des_position'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('gallery_des_position'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('align'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('align'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('download_image'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('download_image'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_tags'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_tags'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('report_image'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('report_image'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('share_facebook'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('share_facebook'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('plus_one'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('plus_one'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>

            <?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('twitter_button'); ?><?php echo $labelEnd; ?>
            <?php echo $valueStart; ?><?php echo $this->form->getInput('twitter_button'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_numbering'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_numbering'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
		<?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('SLIDESHOW'); ?></legend>
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_slideshow_controls'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_slideshow_controls'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('slideshow_position'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('slideshow_position'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('slideshow_autostart'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('slideshow_autostart'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('slideshow_pause'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('slideshow_pause'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		<?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('IMAGE_DESCRIPTIONS'); ?></legend>
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_descriptions'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_descriptions'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('photo_des_position'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('photo_des_position'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('photo_des_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('photo_des_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('photo_des_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('photo_des_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('show_filename'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('show_filename'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		<?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>

		<?php if(IG_J30): ?>
		</div>
		<?php endif; ?>

		<?php if(!IG_J30): ?>
        <?php echo JHtml::_('tabs.panel',JText::_('LIGHTBOX_IMAGE'), 'lbox-image'); ?>
        <?php endif; ?>

        <?php if(IG_J30): ?>
        <div class="tab-pane" id="lbox-image">
        <?php endif; ?>
		
		<fieldset class="adminform">
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lightbox'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lightbox'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_max_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_max_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_max_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_max_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('crop_lbox'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('crop_lbox'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_image_align_horiz'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_image_align_horiz'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_image_align_vert'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_image_align_vert'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_img_container_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_img_container_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_img_container_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_img_container_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_fade_duration'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_fade_duration'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_preload'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_preload'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
		<?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>

		<?php if(IG_J30): ?>
		</div>
		<?php endif; ?>

		<?php if(!IG_J30): ?>
        <?php echo JHtml::_('tabs.panel',JText::_('LIGHTBOX_THUMBNAILS'), 'lbox-thumbs'); ?>
        <?php endif; ?>

        <?php if(IG_J30): ?>
        <div class="tab-pane" id="lbox-thumbs">
        <?php endif; ?>
		
		<fieldset class="adminform">
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
		    <?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_show_thumbs'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_show_thumbs'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_thumb_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_thumb_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_thumb_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_thumb_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_crop_thumbs'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_crop_thumbs'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_thumb_position'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_thumb_position'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_thumb_container_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_thumb_container_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_thumb_container_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_thumb_container_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_images_per_row'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_images_per_row'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_thumb_scrollbar'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_thumb_scrollbar'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_arrows_up_down'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_arrows_up_down'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_arrows_left_right'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_arrows_left_right'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_show_thumb_info'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_show_thumb_info'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_scroll_speed'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_scroll_speed'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_scroll_boundary'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_scroll_boundary'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		<?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>

		<?php if(IG_J30): ?>
		</div>
		<?php endif; ?>

		<?php if(!IG_J30): ?>
        <?php echo JHtml::_('tabs.panel',JText::_('LIGHTBOX_OTHER'), 'lbox-other'); ?>
        <?php endif; ?>

        <?php if(IG_J30): ?>
        <div class="tab-pane" id="lbox-other">
        <?php endif; ?>
		

		<fieldset class="adminform">
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_allow_comments'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_allow_comments'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_allow_rating'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_allow_rating'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>

			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_close_position'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_close_position'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_download_image'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_download_image'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_show_tags'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_show_tags'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_report_image'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_report_image'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_share_facebook'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_share_facebook'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_plus_one'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_plus_one'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>

            <?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_twitter_button'); ?><?php echo $labelEnd; ?>
            <?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_twitter_button'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_show_numbering'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_show_numbering'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
		<?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('SLIDESHOW'); ?></legend>
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_show_slideshow_controls'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_show_slideshow_controls'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_slideshow_position'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_slideshow_position'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_slideshow_autostart'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_slideshow_autostart'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_slideshow_pause'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_slideshow_pause'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			<?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>
		
		<fieldset class="adminform">
		<legend><?php echo JText::_('IMAGE_DESCRIPTIONS'); ?></legend>
		<?php if(!IG_J30): ?><ul class="adminformlist"><?php endif; ?>
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_show_descriptions'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_show_descriptions'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_photo_des_position'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_photo_des_position'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_photo_des_width'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_photo_des_width'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_photo_des_height'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_photo_des_height'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			
			<?php echo $wrapperStart; ?><?php echo $labelStart; ?><?php echo $this->form->getLabel('lbox_show_filename'); ?><?php echo $labelEnd; ?>
			<?php echo $valueStart; ?><?php echo $this->form->getInput('lbox_show_filename'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
		<?php if(!IG_J30): ?></ul><?php endif; ?>
		</fieldset>

		<?php if(IG_J30): ?>
		</div>
		<?php endif; ?>

		<?php if(!IG_J30): ?>
        <?php echo JHtml::_('tabs.panel',JText::_('JCONFIG_PERMISSIONS_LABEL'), 'acl'); ?>
        <?php endif; ?>

        <?php if(IG_J30): ?>
        <div class="tab-pane" id="acl">
        <?php endif; ?>

		<?php if(!IG_J30): ?>
		<?php echo JHtml::_('sliders.start','permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
        <?php echo JHtml::_('sliders.panel',JText::_('PERMISSIONS'), 'access-rules'); ?>
		<?php endif; ?>

			<fieldset class="panelform">
				<?php echo $wrapperStart; ?><?php //echo $labelStart; ?><?php //echo $this->form->getLabel('rules'); ?><?php //echo $labelEnd; ?>
				<?php echo $valueStart; ?><?php echo $this->form->getInput('rules'); ?><?php echo $valueEnd; ?><?php echo $wrapperEnd; ?>
			</fieldset>

		<?php if(!IG_J30): ?>
		<?php echo JHtml::_('sliders.end'); ?>
		<?php endif; ?>
	
	<?php if(!IG_J30): ?>
	<?php echo JHtml::_('tabs.end'); ?>
	<?php endif; ?>

	<?php if(IG_J30): ?>
    </div>
    <?php endif; ?>
		
	</div>
	<div style="clear: both"></div>

	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

    <?php if(IG_J30): ?>
	</div>
	</div>
	<?php endif; ?>

</form>