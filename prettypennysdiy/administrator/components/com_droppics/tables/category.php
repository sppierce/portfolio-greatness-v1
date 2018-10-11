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

// No direct access
defined('_JEXEC') or die;

require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_categories'.DIRECTORY_SEPARATOR.'tables'.DIRECTORY_SEPARATOR.'category.php');

/**
 * Category Table class
 *
 * @package		Joomla.Administrator
 * @subpackage          com_droppics
 * @since		1.5
 */
class DroppicsTableCategory extends CategoriesTableCategory
{
    public function store($updateNulls = false) {
        $meta = json_decode($this->metadata);
        if(isset($meta->tags)){
            $meta->tags = array();
        }
        $this->metadata = json_encode($meta);
        return parent::store($updateNulls);
    }
    
    public function delete($pk = null, $children = false) {
        return parent::delete($pk, $children);
    }
}
