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
<div class="well">
    <div class="btn-group" style="float: right;">
            <button id="btnsave" class="btn"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_IMAGE_SAVE'); ?> <i class="icon-ok"></i></button>
            <a id="backtogallery" class="btn" href="" ><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_IMAGE_BACKTO_GALLERY'); ?> <i class="icon-remove"></i></a>
    </div>
</div>
<div id="picture_params">
    <div class="well">
        <h2><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_REDIM'); ?></h2>
        <div>
            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_ORIGINAL'); ?> : <span id="originalSize"></span>
        </div>
        <div class="control-group form-inline">
            <input type="text" class="input-mini" id="resizeWidth"> x <input type="text" class="input-mini" id="resizeHeight">
            <button id="applyresize" class="btn"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_IMAGE_RESIZE'); ?></button>
        </div>
        <div>
            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_SELECTION'); ?> : <span id="currentSelection">0 x 0</span>
        </div>
    </div>
    <div id="picture_tools" class="well">
        <h2><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_TOOLS'); ?></h2>
        
        <button id="btnCrop" class="btn"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_CROP'); ?><i class="icon-crop"></i></button>
        <button id="btnMirror" class="btn"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_MIRROR'); ?><i class=""></i></button>
        <button id="btnVMirror" class="btn"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_VMIRROR'); ?><i class=""></i></button>
        <button id="btnRotate" class="btn"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_ROTATE'); ?><i class=""></i></button>


        <a id="imageLoad" class="btn" >
          <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_REVERT'); ?>
        </a>
    </div>
    
    <div class="well">
        <h2><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_FILTERS'); ?></h2>
        <div class="filter">
            Brightness : 
            <input type="range" min="-100" max="100" step="1" value="0" data-filter="brightness">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            Contrast : 
            <input type="range" min="-100" max="100" step="1" value="0" data-filter="contrast">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            Saturation :
            <input type="range" min="-100" max="100" step="1" value="0" data-filter="saturation">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            Vibrance :
            <input type="range" min="-100" max="100" step="1" value="0" data-filter="vibrance">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            Exposure :
            <input type="range" min="-100" max="100" step="1" value="0" data-filter="exposure">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            Hue :
            <input type="range" min="0" max="100" step="1" value="0" data-filter="hue">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            Sepia :
            <input type="range" min="0" max="100" step="1" value="0" data-filter="sepia">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            Gamma :
            <input type="range" min="0" max="10" step="0.1" value="0" data-filter="gamma">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            Noise :
            <input type="range" min="0" max="100" step="1" value="0" data-filter="noise">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            Clip :
            <input type="range" min="0" max="100" step="1" value="0" data-filter="clip">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            Sharpen :
            <input type="range" min="0" max="100" step="1" value="0" data-filter="sharpen">
            <span class="filterValue">0</span>
        </div>

        <div class="filter">
            StackBlur :
            <input type="range" min="0" max="20" step="1" value="0" data-filter="stackBlur">
            <span class="filterValue">0</span>
        </div>

        <select id="presetFilters">
            <option value="">---</option>
            <option value="vintage">Vintage</option>
            <option value="lomo">Lomo</option>
            <option value="clarity">Clarity</option>
            <option value="sinCity">Sin City</option>
            <option value="sunrise">Sunrise</option>
            <option value="crossProcess">Cross Process</option>
            <option value="orangePeel">Orange Peel</option>
            <option value="love">Love</option>
            <option value="grungy">Grungy</option>
            <option value="jarques">Jarques</option>
            <option value="pinhole">Pinhole</option>
            <option value="oldBoot">Old Boot</option>
            <option value="glowingSun">Glowing Sun</option>
            <option value="hazyDays">Hazy Days</option>
            <option value="herMajesty">Her Majesty</option>
            <option value="nostalgia">Nostalgia</option>
            <option value="hemingway">Hemingway</option>
            <option value="concentrate">Concentrate</option>  
        </select>
    </div>
</div>
<div id="picture_edit">
    <img id="camanimg" src="" />
</div>