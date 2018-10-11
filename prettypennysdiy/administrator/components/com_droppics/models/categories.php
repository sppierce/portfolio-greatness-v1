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

//jimport('joomla.application.component.modeladmin');
//jimport('joomla.access.access');

require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_categories'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'categories.php');

class DroppicsModelCategories extends CategoriesModelCategories
{   
    
    protected  $canDo;


    public function __construct($config = array()) {
        parent::__construct($config);
        $app = JFactory::getApplication();
        $app->setUserState('com_categories.categories.filter.extension', 'com_droppics');
        $app->setUserState('list.limit',10000);
        
        $this->canDo = DroppicsHelper::getActions();
    }

    
    public function populateState($ordering = null, $direction = null) {
        parent::populateState($ordering, $direction);
        $this->setState('list.start', 0);
        $this->setState('filter.extension', 'com_droppics');
        $this->state->set('list.limit', 10000);
    }
    
    public function getListQuery() {
        $db = $this->getDbo();
        $query = parent::getListQuery();
        $catid = $this->getState('category.id',null);
        if($catid!==null){
            $subQuery = "SELECT rgt,lft FROM #__categories as a WHERE id=".(int)$catid." AND extension='com_droppics'";
            $db->setQuery($subQuery);
            if(!$db->query()){
                return false;
            }
            $parent = $db->loadObject();
            $recursive = $this->getState('category.recursive',null);
            if($recursive){
                $query->where('a.rgt<= '.(int)$parent->rgt);
                $query->where('a.lft> '.(int)$parent->lft);
            }else{
                $query->where('a.parent_id = '.(int)$catid);
            }
        }
		
        $query->select('COUNT(p.id) as pictures');
        $query->join('LEFT', '#__droppics_pictures AS p ON a.id = p.id_gallery');
        $query->where('a.id IS NOT null');
        $query->group('a.id');
        
        
        
        if($this->canDo->get('core.edit.own') && !$this->canDo->get('core.edit')){
            $query->where('created_user_id='.(int)JFactory::getUser()->id);
        }
        
        return $query;
        
    }
}