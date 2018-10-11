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

jimport('joomla.application.component.model');

class DroppicsModelFrontcategories extends JModelLegacy
{   
    
    public function getCategories($id_gallery) {
        $db = $this->getDbo();

        $params_com = JComponentHelper::getParams('com_droppics');
        $t = $params_com->get('lastmodified','');
        if($t) {
            $t = '?'.$t;
        }

        $query = $db->getQuery(true);
        $query->select('c.id as id_category');
        $query->select('c.title as category_title');
        $query->select('p.id as id_picture');
        $query->select('p.file as picture_file');
        $query->select('concat(p.file,"'.$t.'") as picture_ver');
        $query->select('p.title as picture_title');
        $query->select('p.alt as picture_alt');
        $query->from('#__categories as c');
        $query->leftJoin('#__droppics_pictures as p ON p.id_gallery = c.id');
        $query->where('c.parent_id='.(int)$id_gallery);
        $query->where('c.extension="com_droppics"');
        $query->where('p.position=0');
        $query->order('c.lft ASC');
        $query->group('p.id_gallery');

        $db->setQuery($query);
        if(!$db->query()){
            return false;
        }
        return $db->loadObjectList();    
    }
    
        public function getParent($id_gallery){
        $db = $this->getDbo();

            $params_com = JComponentHelper::getParams('com_droppics');
            $t = $params_com->get('lastmodified','');
            if($t) {
                $t = '?'.$t;
            }

        $query = $db->getQuery(true);
        $query->select('c.id as id_category');
        $query->select('c.title as category_title');
        $query->select('p.id as id_picture');
        $query->select('p.file as picture_file');
        $query->select('concat(p.file,"'.$t.'") as picture_ver');
        $query->select('p.title as picture_title');
        $query->select('p.alt as picture_alt');
        $query->from('#__categories as c');
        $query->leftJoin('#__droppics_pictures as p ON p.id_gallery = c.id');
        $query->where('c.id = (SELECT x.parent_id FROM #__categories as x WHERE x.id='.(int)$id_gallery.')');
        $query->where('c.extension="com_droppics"');
        $query->order('c.lft ASC');
        $query->group('p.id_gallery');
        $query->order('p.position ASC');

        $db->setQuery($query);
        if(!$db->query()){
            return false;
        }
        return $db->loadObject();    
    }
    // get all id child or parent
    function getAllChildOrParent($id,&$arr,$getchid) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        if ($getchid) {
            $query->select('id');
            $query->from('#__categories as c');
            $query->where('c.parent_id = ' . (int)$id);
            $db->setQuery($query);
            if ($db->query()) {
                foreach ($db->loadObjectList() as $idcat) {
                    $arr[] = (int)$idcat->id;
                    $this->getAllChildOrParent($idcat->id, $arr,$getchid);
                }
            } else {
                return false;
            }
        }else{
            $query->select('parent_id');
            $query->from('#__categories as c');
            $query->where('c.id = ' . (int)$id);
            $db->setQuery($query);
            if ($db->query()) {
                foreach ($db->loadObjectList() as $idcat) {
                    if((int)$idcat->parent_id > 1) {
                        $arr[] = (int)$idcat->parent_id;
                        $this->getAllChildOrParent($idcat->parent_id, $arr, $getchid);
                    }
                }
            } else {
                return false;
            }
        }
        return $arr;
    }
}