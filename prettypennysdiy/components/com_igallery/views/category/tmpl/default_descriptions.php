<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
if($this->mode == 'main')
{
    if($this->profile->photo_des_position == 'left' || $this->profile->photo_des_position == 'right')
    {
        $des_container_width = $this->profile->photo_des_width == 0 ? 200 : $this->profile->photo_des_width;
        $des_container_height = $this->profile->photo_des_height == 0 ? $this->dimensions['largestHeight'] : $this->profile->photo_des_height;

        $des_container_width_percent = round( ($des_container_width/$this->dimensions['galleryWidth']) * 100, 2);
        $height = 'max-height: '.$des_container_height.'px;';
    }
    else
    {
        $des_container_width = $this->profile->photo_des_width == 0 ? $this->dimensions['galleryWidth'] : $this->profile->photo_des_width;
        $des_container_height = $this->profile->photo_des_height == 0 ? 60 : $this->profile->photo_des_height;

        $des_container_width_percent = 100;
        $height = 'height: '.$des_container_height.'px;';
    }
}
else
{
    if($this->profile->lbox_photo_des_position == 'left' || $this->profile->lbox_photo_des_position == 'right')
    {
        $des_container_width = $this->profile->lbox_photo_des_width == 0 ? 200 : $this->profile->lbox_photo_des_width;
        $des_container_height = $this->profile->lbox_photo_des_height == 0 ? $this->dimensions['largestLboxHeight'] : $this->profile->lbox_photo_des_height;

        $des_container_width_percent = round( ($des_container_width/$this->dimensions['galleryLboxWidth']) * 100, 2);
        $height = 'max-height: '.$des_container_height.'px;';
    }
    else
    {
        $des_container_width = $this->profile->lbox_photo_des_width == 0 ? $this->dimensions['galleryLboxWidth'] : $this->profile->lbox_photo_des_width;
        $des_container_height = $this->profile->lbox_photo_des_height == 0 ? 60 : $this->profile->lbox_photo_des_height;

        $des_container_width_percent = 100;
        $height = 'height: '.$des_container_height.'px;';
    }

}
?>

<div id="<?php echo $this->mode; ?>_des_container<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode; ?>_des_container" style="overflow: auto; width: <?php echo $des_container_width_percent; ?>%; <?php echo $height; ?>">

<?php
for($i=0; $i<count($this->photoList); $i++)
{
    $row = &$this->photoList[$i];
    $style = $i==0 ? '' : 'style="display: none"';

    $profileValue = $this->mode == 'main' ? 'show_filename' : 'lbox_show_filename';
    $imgFileName = '';

    if($this->profile->{$profileValue} == 'full')
    {
        $imgFileName = $row->filename;
    }
    if($this->profile->{$profileValue} == 'no-id')
    {
        preg_match_all('/-[0-9]+/', $row->filename, $matches);
        $imgFileName = str_replace($matches[0][0], '', $row->filename);
    }
    if($this->profile->{$profileValue} == 'no-ext')
    {
        $imgFileName = str_replace(array('.jpg','.jpeg','.gif','.png'), '', $row->filename);
    }
    if($this->profile->{$profileValue} == 'no-id-no-ext')
    {
        preg_match_all('/-[0-9]+/', $row->filename, $matches);
        $imgFileName = str_replace($matches[0][0], '', $row->filename);
        $imgFileName = str_replace(array('.jpg','.jpeg','.gif','.png'), '', $imgFileName);
    }

    $imgFileName = str_replace('_',' ',$imgFileName);
    ?>
    <div <?php echo $style; ?> class="des_div"><?php if( strlen($imgFileName) > 1){echo $imgFileName.' ';} ?><?php echo $row->description; ?></div>
    <?php
}
?>

</div>

