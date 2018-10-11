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
?>

<div class="wrap seo_extended_table_page">
    <div id="icon-edit-pages" class="icon32 icon32-posts-page"></div>
    <form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_droppics&view=imagesinfo'); ?>" method="post" >
        <div class="tablenav top">
            <input type="hidden" name="page" value="metaseo_image_meta">
            <div class="alignleft actions bulkactions">
                <label for="filter-by-date" class="screen-reader-text">Filter by date</label>
                <select name = "filter[gallery_id]" id = "filter_gallery_id" class="metaseo-filter" >
                    <?php
                    $content = '';
                    if ($this->f_gallery_id =='0' || $this->f_gallery_id =='' ) {
                        $content .= '<option selected ="selected" value ="0">All category</option >';
                    }else{
                        $content .= '<option value ="0">All category</option >';
                    }
                    if(!empty($this->categories)) {
                        for ($index = 0; $index < count($this->categories); $index++) {
                            if($index+1!=count($this->categories)){
                                $nextlevel = $this->categories[$index+1]->level;
                            }else{
                                $nextlevel = 0;
                            }
                            $selected = '';
                            $space_str='';
                            if($nextlevel>$this->categories[$index]->level){
                                if (($this->categories[$index]->level-1) > 0){
                                    $space_str = str_repeat('&nbsp;',$this->categories[$index]->level-1);
                                }
                            }elseif($nextlevel==$this->categories[$index]->level){
                                $space_str = str_repeat('&nbsp;',$nextlevel-1);
                            }else{
                                if (($this->categories[$index]->level-1) > 0){
                                    $space_str = str_repeat('&nbsp;',$this->categories[$index]->level-1);
                                }
                            }


                            if ($this->categories[$index]->id == $this->f_gallery_id) {
                                $content .= '<option selected ="selected" value = ' .
                                    $this->categories[$index]->id . '>'.$space_str.''
                                    . $this->categories[$index]->title . '</option >';
                            } else {
                                $content .= '<option value = ' . $this->categories[$index]->id . '> '.$space_str.''
                                    . $this->categories[$index]->title . '</option >';
                            }

                        }
                    echo $content;
                }?>
                </select >

                <input type="submit" name="filter_date_action" id="image-submit" class="button" value="Filter">
            </div>
            <div class="alignleft">
                <select name="filter[image_mbulk_copy]" id="filter_image_mbulk_copy" class="mbulk_copy">
                    <option value="all">All Images</option>
                    <option value="bulk-copy-title-alt">Selected images</option>
                </select>
                <input type="button" name="image_do_copy_alt" class="button button-primary btn_do_copy image_do_copy_alt" value="Image name as alt text">
                <input type="button" name="image_do_copy_title" class="button button-primary btn_do_copy image_do_copy_title" value="Image name as image title">
                <span class="spinner"></span> </div>
            <input type="hidden" name="page" value="metaseo_image_meta">
            <div style="float:right;margin-left:8px;"></div>
            <p class="search-box">
                <input type="search" id="filter_search_drp" name="filter[search_drp]" value="<?php echo $this->escape($this->f_search_drp); ?>">
                <input type="hidden" id="filter_order_by" name="filter[order_by]" value="<?php echo $this->escape($this->f_order_by); ?>">
                <input type="submit" name="search" id="search-submit" class="button" value="Search">
            </p>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed striped metaseo_images">
            <thead>
            <tr>
                <th scope="col" id="cb" class="manage-column column-cb check-column" style="padding:8px 10px;"><label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox" style="margin:0;"></th>
                <th scope="col" id="col_id" class="manage-column column-col_id" style="" colspan="1"><a href="#" onclick="return false;" class="js-stools-column-order" data-name="id" >ID<span></span></a></th>
                <th scope="col" id="col_image" class="manage-column column-col_image" style="" colspan="3">Image</th>
                <th scope="col" id="col_image_name" class="manage-column column-col_image_name sortable asc" style="" colspan="4"><a href="#" onclick="return false;" class="js-stools-column-order" data-name="name"  >Name<span></span></a></th>
                <th scope="col" id="col_image_alternative" class="manage-column column-col_image_alternative" style="" colspan="3"><a href="#" onclick="return false;" class="js-stools-column-order" data-name="alt" >Alternative text<span></span></a></th>
                <th scope="col" id="col_image_title" class="manage-column column-col_image_title sortable asc" style="" colspan="3"><a href="#" onclick="return false;" class="js-stools-column-order" data-name="title" >Title<span></span></a></th>
                <th scope="col" id="col_image_desc" class="manage-column column-col_image_desc" style="" colspan="3">Caption</th>
            </tr>
            </thead>
            <tbody>
            <?php echo $this->loadTemplate('list'); ?>
            </tbody>
            <tfoot>
            <tr>
                <th scope="col" class="manage-column column-cb check-column" style="padding:8px 10px;"><label class="screen-reader-text" for="cb-select-all-2">Select All</label>
                    <input id="cb-select-all-2" type="checkbox" style="margin:0;"></th>
                <th scope="col" class="manage-column column-col_id" style="" colspan="1"><a href="#" onclick="return false;" class="js-stools-column-order" data-name="id" >ID<span></span></a></th>
                <th scope="col" class="manage-column column-col_image" style="" colspan="3">Image</th>
                <th scope="col" class="manage-column column-col_image_name sortable asc" style="" colspan="4"><a href="#" onclick="return false;" class="js-stools-column-order" data-name="name"  >Name<span></span></a></th>
                <th scope="col" class="manage-column column-col_image_alternative" style="" colspan="3"><a href="#" onclick="return false;" class="js-stools-column-order" data-name="alt" >Alternative text<span></span></a></th>
                <th scope="col" class="manage-column column-col_image_title sortable asc" style="" colspan="3"><a href="#" onclick="return false;" class="js-stools-column-order" data-name="title" >Title<span></span></a></th>
                <th scope="col" class="manage-column column-col_image_desc" style="" colspan="3">Caption</th>
            </tr>
            </tfoot>
        </table>
        <div class="tablenav bottom">
            <input type="hidden" name="page" value="metaseo_image_meta">
            <div class="alignleft actions bulkactions">
                <label for="filter-by-date" class="screen-reader-text">Filter by date</label>
                <select name = "filter[gallery_id]" id = "filter_gallery_id" class="metaseo-filter" >
                    <?php
                    $content = '';
                    if ($this->f_gallery_id =='0' || $this->f_gallery_id =='' ) {
                        $content .= '<option selected ="selected" value ="0">All category</option >';
                    }else{
                        $content .= '<option value ="0">All category</option >';
                    }
                    if(!empty($this->categories)) {
                        for ($index = 0; $index < count($this->categories); $index++) {
                            $selected = '';
                            //if($index ==0){$selected = 'selected';}
                            if ($this->categories[$index]->id == $this->f_gallery_id) {
                                $content .= '<option selected ="selected" value =' . $this->categories[$index]->id . '>'
                                    . $this->categories[$index]->title . '</option >';
                            } else {
                                $content .= '<option value =' . $this->categories[$index]->id . '>'
                                    . $this->categories[$index]->title . '</option >';
                            }

                        }
                        echo $content;
                    }?>
                </select >
                <input type="submit" name="filter_date_action" id="image-submit" class="button" value="Filter">
            </div>
            <div class="alignleft">
                <select name="filter[image_mbulk_copy]" id="filter_image_mbulk_copy" class="mbulk_copy">
                    <option value="all">All Images</option>
                    <option value="bulk-copy-title-alt">Selected images</option>
                </select>
                <input type="button" name="image_do_copy_alt" class="button button-primary btn_do_copy image_do_copy_alt" value="Image name as alt text">
                <input type="button" name="image_do_copy_title" class="button button-primary btn_do_copy image_do_copy_title" value="Image name as image title">
                <span class="spinner"></span> </div>
            <input type="hidden" name="page" value="metaseo_image_meta">
            <div style="float:right;margin-left:8px;">
                <input type="number" required="" min="1" value="<?php echo $this->f_limit_drp ?>" maxlength="3" name="list[limit]"
                id="list_limit" class="metaseo_imgs_per_page screen-per-page" max="999" step="1">
                <input type="submit" name="btn_perpage" class="button_perpage button" id="button_perpage" value="Apply">
            </div>
            <div class="tablenav-pages">
                <?php echo $this->pagination->getListFooter(); ?>
            </div>
            <br class="clear">
        </div>
    </form>
</div>