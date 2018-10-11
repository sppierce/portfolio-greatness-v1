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

defined('_JEXEC') or die;

class DroppicsViewFrontgallery extends JViewLegacy
{
	function display($tpl = null)
	{
            JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_droppics/models/','droppicsModel');
            JModelLegacy::addIncludePath(JPATH_BASE.'/components/com_droppics/models/','droppicsModel');

            $model = JModelLegacy::getInstance('Gallery','droppicsModel');
            $models = JModelLegacy::getInstance('Frontcategories','droppicsModel');
            $modelc = JModelLegacy::getInstance('Frontcategory','droppicsModel');

            JPluginHelper::importPlugin('droppics');
            $dispatcher = JDispatcher::getInstance();

            $id_gallery = JFactory::getApplication()->input->getInt('id_gallery');
            $pictures = $model->getGallery($id_gallery);
            $categories = $models->getCategories($id_gallery);
            $parent = $models->getParent($id_gallery);
            $listchid = array();
            if(!empty($pictures) || !empty($categories)){
                $gallery = $dispatcher->trigger('onShowFrontGallery',array(array('from_plugin'=>false,
                    'id_gallery'=>$id_gallery,'theme'=>$modelc->getCategoryTheme($id_gallery),'pictures'=>$pictures,'categories'=>$categories,'parent'=>$parent,'params'=>$modelc->getCategoryParams($id_gallery),'listchid'=>$listchid)));
                echo $gallery[0];
            }
            JFactory::getApplication()->close();
        }
}
