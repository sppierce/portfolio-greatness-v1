<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');

class igalleryModelratings extends JModelList
{

	function getListQuery($resolveFKs = true)
	{
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);

        $query->select('r.*');
		$query->from('#__igallery_ratings AS r');

		$query->select('i.gallery_id, i.filename, i.alt_text, i.rotation');
		$query->join('INNER', '`#__igallery_img` AS i ON i.id = r.image_id');

		$query->select('c.name as category_name, c.id as category_id');
		$query->join('INNER', '`#__igallery` AS c ON c.id = i.gallery_id');

		$query->select('p.thumb_width, p.thumb_height, p.crop_thumbs, p.img_quality, p.round_thumb, p.round_fill');
		$query->join('INNER', '`#__igallery_profiles` AS p ON p.id = c.profile');

		$query->select('u.name as author_name');
		$query->join('LEFT', '`#__users` AS u ON u.id = r.user');

		$query->order($db->escape($this->getState('list.ordering', 'r.date')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
}