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


jimport('joomla.application.component.modellist');
class DroppicsModelImagesinfo extends JModelList
{
    /**
     * Items total
     * @var integer
     */
    var $_total = null;

    /**
     * Pagination object
     * @var object
     */
    var $_pagination = null;

    public function __construct ($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'p.id',
                'id_gallery', 'id_gallery',
                'theme','g.theme'
        );
    }

        parent::__construct($config);
    }


    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search_drp', 'filter_search_drp');
        $this->setState('filter.search_drp', $search);

        $order_by = $this->getUserStateFromRequest($this->context . '.filter.order_by', 'filter_order_by');
        if (empty($order_by)){
            $order_by ='id.DESC';
        }
        $this->setState('filter.order_by', $order_by);

        $gallery_id = $this->getUserStateFromRequest($this->context . '.filter.gallery_id', 'filter_gallery_id');
        $this->setState('filter.gallery_id', $gallery_id);

        $image_mbulk_copy = $this->getUserStateFromRequest($this->context . '.filter.image_mbulk_copy', 'filter_image_mbulk_copy');
        $this->setState('filter.image_mbulk_copy', $image_mbulk_copy);

        // List state information.
        parent::populateState($ordering, $direction);
    }



    //get all image
    protected function getListQuery(){

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $params = JComponentHelper::getParams('com_droppics');
        $t = $params->get('lastmodified','');
        if($t) {
            $t = '?'.$t;
        }
        $query->select('g.theme,g.params,g.id AS id_gallery ');
        $query->from('#__droppics AS g ');
        $query->select('p.id, p.file, concat(p.file,"'.$t.'") as filever, p.position,'.
                'p.alt,p.title,p.params as picture_params,p.upload_date ')
            ->join('RIGHT', '#__droppics_pictures as p ON g.id=p.id_gallery ');


        // Filter by search in title.
        $search = $this->getState('filter.search_drp');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('g.id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(p.file LIKE ' . $search . ' OR p.title LIKE ' . $search . ')');
            }
        }
        $gallery_id = $this->getState('filter.gallery_id');
        if (!empty($gallery_id))
        {
            $query->where('g.id = (' . $gallery_id . ')');
        }


        // Add the list ordering clause.
        $order_by = $this->state->get('filter.order_by', 'id.DESC');
        $order_by = explode('.',$order_by);
        $orderCol = '';
        switch ($order_by[0]){
            case "id":
                $orderCol = 'p.id';
                break;
            case "name":
                $orderCol = 'p.file';
                break;
            case "alt":
                $orderCol = 'p.alt';
                break;
            case "title":
                $orderCol = 'p.title';
                break;
            default:
                $orderCol = 'id';
        }
        $query->order($db->escape($orderCol . ' ' . $order_by[1]));
        return $query;

    }


    public function getItems()
    {
        $items = parent::getItems();
        return $items;
    }
}