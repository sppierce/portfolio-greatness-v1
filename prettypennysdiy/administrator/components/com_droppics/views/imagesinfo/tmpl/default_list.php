<?php
/**
 * Droppics
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die;

function human_filesize($bytes, $decimals = 2) {
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor > 0) $sz = 'KMGT';
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
}
function root_folder(){ return  droppicsBase::getParam('change_root_folder_old', 'com_droppics');}

?>
<?php
$content = '';
if(!empty($this->pictures)) {
    foreach ($this->pictures as $picture){
        $pparams = json_decode($picture->picture_params);
        if(isset($pparams->content_custom_title)){
            $content_custom_title = $pparams->content_custom_title;
        }else{$content_custom_title ='';}

        $image_path = droppicsBase::getFullPicturePath($picture->id_gallery).$picture->file;
        $imageInfo = getimagesize($image_path);

        $content .= '<tr id="record_'.$picture->id.'" class="alternate">';
        $content .= '<td scope="row" class="check-column" style="padding:8px 10px;"><input id="cb-select-'
            .$picture->id.'" class="metaseo_post"type="checkbox" name="cb-selected" value="'.$picture->id.'"></td>';
        $content .= '<td class="col_id" colspan="1">'.$picture->id.'</td>';
        $content .= '<td class="col_image column-col_image" colspan="3"><img src="'.COM_MEDIA_BASEURL.'/'.root_folder().'/'
            .$picture->id_gallery.'/thumbnails/'.$picture->filever.'" width="100px" height="100px" class="metaseo-image" data-name="Chrysanthemum.jpg" data-img-post-id="'.$picture->id.'"></td>';
        $content .= '<td class="col_image_name column-col_image_name" colspan="4">';
        $content .= '<div class="img-name-wrapper">';
        $content .= '<textarea name="name_image['.$picture->id.']" class="metaseo-img-meta metaseo-img-name"  data-file-name="'.$picture->file.'"  data-id-gallery="'.$picture->id_gallery.'" data-meta-type="change_image_name" id="img-name-'.$picture->id.'" data-post-id="'.$picture->id.'" rows="2" data-extension=".'.pathinfo($picture->file, PATHINFO_EXTENSION).'" style="overflow: hidden; word-wrap: break-word; resize: none; height: 44px;">'.pathinfo($picture->file, PATHINFO_FILENAME).'</textarea>';
        $content .= '<span class="img_type">.'.pathinfo($picture->file, PATHINFO_EXTENSION).'</span>';
        $content .= '<p>'.human_filesize(filesize($image_path)).' - '.$imageInfo[0].'x'.$imageInfo[1].' - '.date("d F Y", strtotime($picture->upload_date)).'</p>';
        $content .= '<span class="saved-info" style="position:relative"> <span class="meta-update"style="position:absolute"></span> </span></div>';
        $content .= '</td>';
        $content .= '<td class="col_image_alternative column-col_image_alternative" colspan="3">';
        $content .= '<textarea name="img_alternative['.$picture->id.']" class="metaseo-img-meta drp-img-alt" data-meta-type="alt_text"   data-id-gallery="'.$picture->id_gallery.'" id="img-alt-'.$picture->id.'"  data-post-id="'.$picture->id.'" rows="2" style="overflow: hidden; word-wrap: break-word; resize: none; height: 44px;">' .$picture->alt .'</textarea>';
        $content .= '<span class="saved-info" style="position:relative"> <span class="meta-update" style="position:absolute"></span> </span>';
        $content .= '</td>';
        $content .= '<td class="col_image_title column-col_image_title" colspan="3">';
        $content .= '<textarea name="img_title['.$picture->id.']" class="metaseo-img-meta drp-img-title" data-meta-type="image_title"  data-id-gallery="'.$picture->id_gallery.'" id="img-title-'.$picture->id.'" data-post-id="'.$picture->id.'" rows="2" style="overflow: hidden; word-wrap: break-word; resize: none; height: 44px;">'.$picture->title.'</textarea>';
        $content .= '<span class="saved-info" style="position:relative"> <span class="meta-update" style="position:absolute"></span> </span>';
        $content .= '</td>';
        $content .= '<td class="col_image_desc column-col_image_desc" colspan="3">';
        $content .= '<textarea name="img_desc['.$picture->id.']" class="metaseo-img-meta drp-img-caption" data-meta-type="image_caption"   data-id-gallery="'.$picture->id_gallery.'" id="img-desc-'.$picture->id.'" data-post-id="'.$picture->id.'" rows="2" style="overflow: hidden; word-wrap: break-word; resize: none; height: 44px;">'.$content_custom_title.'</textarea>';
        $content .= '<span class="saved-info" style="position:relative"> <span class="meta-update" style="position:absolute"></span> </span>';
        $content .= '</td>';
        $content .= '</tr>';
    }
    echo $content;
}else
{
    $content .= '<tr id="record_237" class="alternate">';
    $content .= '<td colspan="8"> No Matching Results';
    $content .= '</td>';
    $content .= '</tr>';
    echo $content;
}



?>
