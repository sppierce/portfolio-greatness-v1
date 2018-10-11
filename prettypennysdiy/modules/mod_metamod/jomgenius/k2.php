<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JomGenius classes
 * 
 * @package		JomGenius
 * @version		1
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2010-2013 Brandon IT Consulting. All rights reserved.
 */

class JomGeniusClassK2 extends JomGeniusParent {
	
	
	var $Itemid;
	var $view;
	var $option;
	var $task;
	var $userid;
	var $groupid;
	var $catid;
	var $app;
	var $featured;
		
	function __construct() {
		$jinput				= JFactory::getApplication()->input; // j3.0
		
		$this->Itemid		= $jinput->getVar('Itemid');
		$this->view			= $jinput->getWord('view');
		$this->option		= $jinput->getVar('option');
		$this->task			= JString::strtolower($jinput->getVar('task'));
		$this->cid			= $jinput->getInt('cid', 0);
		$this->tag			= $jinput->getVar('tag');
		$this->year			= $jinput->getVar('year');
		$this->month		= $jinput->getVar('month');
		$this->day			= $jinput->getVar('day');
		$this->layout		= $jinput->getVar('layout');
		$this->featured		= $jinput->getVar('featured');
		$this->tag			= $jinput->getVar('tag');
		$this->id			= $jinput->getVar('id');
	}
	
	function shouldInstantiate() {
		/* for K2, we allow it to be instantiated even if we are not currently on the K2 component.
		 */
		return $this->componentExists( 'com_k2' );
	}
	
	/**
	 * A generic function that knows how to get lots of different info about the current page or product.
	 */
	function info( $origtype ) {
		$this->layout = JRequest::getVar("layout");
		$type = strtolower( str_replace( array(' ','_'), '', $origtype ) );
//		echo "<br>id: " . $this->id . "|<br>type: $type<br>";
//		echo "view: " . $this->view . "| task: " . $this->task . "| layout: " . $this->layout . "| featured: " . $this->featured . "<br>"; var_dump($this->featured);
		switch( $type ) {
				
			case 'pagetype':				return $this->pageType();
			case 'categoryid':
			case 'categoryids':				return $this->categoryIds();
			case 'categoryname':
			case 'categorynames':			return $this->categoryNames();
			case 'ancestorcategoryids':		return $this->ancestorCategoryIds();
			case 'ancestorcategorynames':	return $this->ancestorCategoryNames();
			case 'itemtags':				return $this->itemTags();
		}
		switch( $origtype ) {
			case 'item_id':
			case 'item_title':
			case 'item_alias':
			case 'item_published':
			case 'item_introtext':
			case 'item_fulltext':
			case 'item_text':
			case 'item_video':
			case 'item_gallery':
			case 'item_extra_fields':
			case 'item_extra_fields_search':
			case 'item_created':
			case 'item_created_age':
			case 'item_created_by':
			case 'item_created_by_alias':
			case 'item_checked_out':
			case 'item_checked_out_time':
			case 'item_checked_out_age':
			case 'item_modified':
			case 'item_modified_age':
			case 'item_modified_by':
			case 'item_publish_up':
			case 'item_publish_down':
			case 'item_trash':
			case 'item_access':
			case 'item_ordering':
			case 'item_featured':
			case 'item_featured_ordering':
			case 'item_image_caption':
			case 'item_image_credits':
			case 'item_video_caption':
			case 'item_video_credits':
			case 'item_hits':
			case 'item_metadescription':
			case 'item_metadata':
			case 'item_metakeywords':
			case 'item_plugins':
			case 'item_language':
			case 'item_rating_count':
			case 'item_rating_sum':
			case 'item_rating_average':
			case 'item_comments_count':
			case 'item_unpublished_comments_count':
			case 'item_attachments_count':
			case 'category_alias':
			case 'category_description':
			case 'category_published':
			case 'category_access':
			case 'category_ordering':
			case 'category_image':
			case 'category_language':	return $this->itemInfo($origtype);
			
			default:
		}
	}
	
	function pageType() {
		$user 	= JFactory::getUser();
		$jinput				= JFactory::getApplication()->input; // j3.0
		
		$featured = parent::menuItemParam('catFeaturedItems', '1');

		if ( $this->option != 'com_k2' ) return ''; // short circuit
		if ( $this->view == 'item' ) return 'item.view';
		if ( $this->view == 'itemlist'
			and $this->task == 'user'
			and ( $this->layout == 'user' or $this->layout == '')
		 ) {
			return 'user.itemlist';
			// if ( $featured == '0' ) return 'user.itemlist.nofeatured'; // FIXME:: user pages do not seem to rely on upper level "featured" inclusion.
			// if ( $featured == '1' ) return 'user.itemlist.withfeatured';
			// if ( $featured == '2' ) return 'user.itemlist.onlyfeatured';
			
		}
		if ( $this->view == 'itemlist'
			// and $this->task == 'category'
			and $this->layout == 'category' ) {
				
			if ( $featured == '0' ) return 'category.itemlist.nofeatured';
			if ( $featured == '1' ) return 'category.itemlist.withfeatured';
			if ( $featured == '2' ) return 'category.itemlist.onlyfeatured';
			return 'category.itemlist'; // single category shown: and you can't filter by featured/notfeatured.
		}
		
		// a different kind of category list, that happens when you click on a category that was a subcategory
		// of another category that was in a menu item. i.e. it's not directly in a menu item.
		
		if ( $this->view == 'itemlist'
			and $this->task == 'category'
			and $this->layout == '' ) {
				
			if ( $featured == '0' ) return 'category.itemlist.nofeatured';
			if ( $featured == '1' ) return 'category.itemlist.withfeatured';
			if ( $featured == '2' ) return 'category.itemlist.onlyfeatured';
			return 'category.itemlist'; // single category shown: and you can't filter by featured/notfeatured.
		}
		
		if ( $this->view == 'itemlist'
			and $this->tag != ''
			and $this->task == 'tag' ) return 'tag.itemlist';
		
		$source = parent::menuItemParam('source');
		if ( $this->view == 'latest'
			and $source == 1
			and $this->layout == 'latest' ) {
			return 'category.latest';
		}

		if ( $this->view == 'latest'
			and $source == 0
			and $this->layout == 'latest' ) {
			return 'user.latest';
		}
		
	}
	
	function itemInfo( $type ) {
		$pageType = $this->pageType();
		
		switch ($pageType) {
		 	case 'item.view':
				$row = $this->_infoForItemId((int)$this->id);
				return @$row->$type; // may be a null. We don't care.
				
			// We should really allow the category_* things to be queried on category
			// pages as well as item.view pages. For a later date...
			
			case 'category.itemlist.nofeatured':
			case 'category.itemlist.withfeatured':
			case 'category.itemlist.onlyfeatured':
			case 'category.itemlist':
			
			case 'user.itemlist.nofeatured':
			case 'user.itemlist.withfeatured':
			case 'user.itemlist.onlyfeatured':
			
			case 'tag.itemlist':
			case 'category.latest':
			case 'user.latest':
			default:
				break;
		}
	}
	
	function categoryNames() {
		if ( $this->option != 'com_k2' ) return null;
		
		$category_ids = null;
		
		// 1 - get the category id if we are on a category page (single category itemlist)

		if ( $this->view == "itemlist"
				and $this->task == "category"
				and ( $this->layout == "category" or $this->layout == '' )
				and $this->id != '' ) {
			/* category list pages with a single category */
			$category_ids = array( (int)$this->id );
		}
		
		// multiple categories:
		if ( $this->view == "itemlist" and $this->layout == "category" and $this->id == '') {
			/* category list pages with a single category */
			$category_ids = parent::menuItemParam('categories');
		}
				
		// 2 - get the category ids if we are on a latest items page with any number of categories selected

		if ( $this->view == "latest" and $this->layout == "latest" ) {
			// On this type of page, it's only showing items directly in the categories specified, NOT their ancestors.
			// However, we will use this function to return the list of all ancestors as well as 
			$category_ids = parent::menuItemParam('categoryIDs');
		}
		
		// 3 - get the category ids if we are on an item page.
		
		if ( $this->view == 'item' ) {
			$row = $this->_infoForItemId( (int)$this->id );
			return @$row->category_name;
			// this has all the ancestors in it. No need to re-query to get the list.
		}
		
		// if the layout is blank, that means we're on an an-hoc user page, and (int)$id refers to the
		// user. In that case I don't think there's a category filter active.
		if ( $this->view == 'itemlist' and $this->task == 'user' and $this->layout == 'user' ) {
			$category_ids = parent::menuItemParam('userCategoriesFilter');
		}
				
		// If we got to here, we have a list of category ids that the page was DIRECTLY
		// about. So we need to query this database for all ancestors of the categories
		// mentioned.
		if ( !is_null( $category_ids ) ) {
			if ( !is_array( $category_ids ) ) {
				$category_ids = array($category_ids);
			}
			$category_ids = array_unique( $category_ids );
			$rows = $this->_infoForCategoryIds( $category_ids );
			if ( $rows ) {
				$ret = array();
				foreach ( $rows as $row ) {
					$ret[] = $row->category_name;
				}
				return array_unique($ret);
			}
		}
		
	}
	
	
	/* returns the category id of the list, or the item being displayed.
	 * If the list: this is taken from the URL
	 * If the item: if a category id was in the URL then this is used. Otherwise,
	 *  the category of the item is used.
	 */
	function categoryIds() {
		if ( $this->option != 'com_k2' ) return null;
		
		// 1 - get the category id if we are on a category page (single category itemlist)

		if ( $this->view == "itemlist"
				and $this->task == "category"
				and ( $this->layout == "category" or $this->layout == '' )
				and $this->id != '' ) {
			/* category list pages with a single category */
			return (int)$this->id;
		}
		
		// multiple categories:
		if ( $this->view == "itemlist" and $this->layout == "category" and $this->id == '') {
			/* category list pages with a single category */
			return parent::menuItemParam('categories');
		}
		
		// 2 - get the category ids if we are on a latest items page with any number of categories selected

		if ( $this->view == "latest" and $this->layout == "latest" ) {
			// On this type of page, it's only showing items directly in the categories specified, NOT their ancestors.
			// However, we will use this function to return the list of all ancestors as well as 
			return parent::menuItemParam('categoryIDs');
		}
		
		// if the layout is blank, that means we're on an an-hoc user page, and (int)$id refers to the
		// user. In that case I don't think there's a category filter active.
		if ( $this->view == 'itemlist' and $this->task == 'user' and $this->layout == 'user' ) {
			return parent::menuItemParam('userCategoriesFilter');
		}
		
		// 3 - get the category ids if we are on an item page.
		
		if ( $this->view == 'item' ) {
			$row = $this->_infoForItemId( (int)$this->id );
			return (int)@$row->category_id;
			// this has all the ancestors in it. No need to re-query to get the list.
		}
		
	}

	function ancestorCategoryIds() {
		if ( $this->option != 'com_k2' ) return null;

		$category_ids = null;
		
		// 1 - get the category id if we are on a category page (single category itemlist)

		if ( $this->view == "itemlist"
				and $this->task == "category"
				and ( $this->layout == "category" or $this->layout == '' )
				and $this->id != '' ) {
			/* category list pages with a single category */
			$category_ids = array( (int)$this->id );
		}
		
		// multiple categories:
		if ( $this->view == "itemlist" and $this->layout == "category" and $this->id == '') {
			/* category list pages with a single category */
			$category_ids = parent::menuItemParam('categories');
		}
		
		// 2 - get the category ids if we are on a latest items page with any number of categories selected

		if ( $this->view == "latest" and $this->layout == "latest" ) {
			// On this type of page, it's only showing items directly in the categories specified, NOT their ancestors.
			// However, we will use this function to return the list of all ancestors as well as 
			$category_ids = parent::menuItemParam('categoryIDs');
		}
		
		// if the layout is blank, that means we're on an an-hoc user page, and (int)$id refers to the
		// user. In that case I don't think there's a category filter active.
		if ( $this->view == 'itemlist' and $this->task == 'user' and $this->layout == 'user' ) {
			$category_ids = parent::menuItemParam('userCategoriesFilter');
		}
		
		// 3 - get the category ids if we are on an item page.
		
		if ( $this->view == 'item' ) {
			$row = $this->_infoForItemId( (int)$this->id );
			// this has all the ancestors in it. No need to re-query to get the list.
			return @$row->ancestor_category_ids;
		}
		
		// if we got to here, we have a list of category ids that the page was DIRECTLY
		// about. So we need to query this database for all ancestors of the categories
		// mentioned.
		if ( !is_null( $category_ids ) ) {
			if ( !is_array( $category_ids ) ) {
				$category_ids = array($category_ids);
			}
			$rows = $this->_infoForCategoryIds( $category_ids );
			if ( $rows ) {
				$ret = array();
				foreach ( $rows as $row ) {
					$ret = array_unique( array_merge( $ret, $row->ancestor_category_ids ));
				}
				return $ret;
			}
		}
	}
	
	function ancestorCategoryNames() {
		if ( $this->option != 'com_k2' ) return null;

		$category_ids = null;
		
		// 1 - get the category id if we are on a category page (single category itemlist)

		if ( $this->view == "itemlist"
				and $this->task == "category"
				and ( $this->layout == "category" or $this->layout == '' )
				and $this->id != '' ) {
			/* category list pages with a single category */
			$category_ids = array( (int)$this->id );
		}
		
		// multiple categories:
		if ( $this->view == "itemlist" and $this->layout == "category" and $this->id == '') {
			/* category list pages with a single category */
			$category_ids = parent::menuItemParam('categories');
		}
		
		// 2 - get the category ids if we are on a latest items page with any number of categories selected

		if ( $this->view == "latest" and $this->layout == "latest" ) {
			// On this type of page, it's only showing items directly in the categories specified, NOT their ancestors.
			// However, we will use this function to return the list of all ancestors as well as 
			$category_ids = parent::menuItemParam('categoryIDs');
		}
		
		// if the layout is blank, that means we're on an an-hoc user page, and (int)$id refers to the
		// user. In that case I don't think there's a category filter active.
		if ( $this->view == 'itemlist' and $this->task == 'user' and $this->layout == 'user' ) {
			$category_ids = parent::menuItemParam('userCategoriesFilter');
		}
		
		// 3 - get the category ids if we are on an item page.
		
		if ( $this->view == 'item' ) {
			$row = $this->_infoForItemId( (int)$this->id );
			// this has all the ancestors in it. No need to re-query to get the list.
			return @$row->ancestor_category_names;
		}
		
		// if we got to here, we have a list of category ids that the page was DIRECTLY
		// about. So we need to query this database for all ancestors of the categories
		// mentioned.
		if ( !is_null( $category_ids ) ) {
			if ( !is_array( $category_ids ) ) {
				$category_ids = array($category_ids);
			}
			$rows = $this->_infoForCategoryIds( $category_ids );
			if ( $rows ) {
				$ret = array();
				foreach ( $rows as $row ) {
					$ret = array_unique( array_merge( $ret, $row->ancestor_category_names ));
				}
				return $ret;
			}
		}
	}
	
	function _infoForCategoryIds( $ids ) {
		$rows = array();
		if ( is_null($ids) ) return null;
		if ( !is_array($ids) ) $ids = array($ids);
		if ( !count($ids) ) return null;
		$ids = implode( ',', $ids );


		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true); 

		$query->select("DISTINCT

			cat1.id as category_id,
			cat1.name as category_name,
			cat1.alias as category_alias,
			cat1.description as category_description,
			cat1.published as category_published,
			cat1.access as category_access,
			cat1.ordering as category_ordering,
			cat1.image as category_image,
			cat1.language as category_language,

			cat1.name as ct1,
			cat1.id as ci1,
			cat2.name as ct2,
			cat2.id as ci2,
			cat3.name as ct3,
			cat3.id as ci3,
			cat4.name as ct4,
			cat4.id as ci4,
			cat5.name as ct5,
			cat5.id as ci5,
			cat6.name as ct6,
			cat6.id as ci6,
			cat7.name as ct7,
			cat7.id as ci7,
			cat8.name as ct8,
			cat8.id as ci8
			");
		$query->from("`#__k2_categories` cat1");
		$query->leftJoin( "`#__k2_categories` cat2 ON cat1.parent = cat2.id ")
			->leftJoin( "`#__k2_categories` cat3 ON cat2.parent = cat3.id ")
			->leftJoin( "`#__k2_categories` cat4 ON cat3.parent = cat4.id ")
			->leftJoin( "`#__k2_categories` cat5 ON cat4.parent = cat5.id ")
			->leftJoin( "`#__k2_categories` cat6 ON cat5.parent = cat6.id ")
			->leftJoin( "`#__k2_categories` cat7 ON cat6.parent = cat7.id ")
			->leftJoin( "`#__k2_categories` cat8 ON cat7.parent = cat8.id ")
			;

		$query->where( "cat1.id in ( $ids ) " );

		$db->setQuery( $query );
		$rows		= $db->loadObjectList();
		foreach ($rows as $row) {
			$cat_names	= array();
			$cat_ids	= array();
			for($i = 1; $i <= 8; $i++ ) {
				$ct = "ct$i";
				$ci = "ci$i";
				if ( $row->$ct != '' ) $cat_names[] = $row->$ct;
				if ( $row->$ci != '' ) $cat_ids[] = $row->$ci;
			}
			$row->ancestor_category_names = $cat_names;
			$row->ancestor_category_ids = $cat_ids;
		}
		return $rows;
	}
	
	
	function _infoForItemId( $id ) {
		static $rows = array();

		if ( !array_key_exists( $id, $rows ) ) {

			$db			= JFactory::getDbo();
			$query		= $db->getQuery(true); 

			$nullDate	= $db->Quote( $db->getNullDate() );
			$jnow		= JFactory::getDate();
			if ( version_compare( JVERSION, '3.0', '>=' ) ) {
				$my_id		= $db->Quote( $db->escape( (int)$id ) );
				$now		= $db->Quote( $db->escape( $jnow->toSQL() ) );
			} else {
				$my_id		= $db->Quote( $db->getEscaped( (int)$id ) );
				$now		= $db->Quote( $db->getEscaped( $jnow->toMySQL() ) );
			}

			$query->select("DISTINCT

				a.id as item_id,
				a.title as item_title,
				a.alias as item_alias,
				a.published as item_published,
				a.introtext as item_introtext,
				a.fulltext as item_fulltext,
				concat( a.introtext, ' ', a.fulltext ) as item_text,
				a.video as item_video,
				a.gallery as item_gallery,
				a.extra_fields as item_extra_fields,
				a.extra_fields_search as item_extra_fields_search,
				a.created as item_created,
				floor(time_to_sec(timediff(now(),a.created))/60) as item_created_age,
				a.created_by as item_created_by,
				a.created_by_alias as item_created_by_alias,
				a.checked_out as item_checked_out,
				a.checked_out_time as item_checked_out_time,
				floor(time_to_sec(timediff(now(),a.checked_out_time))/60) as item_checked_out_age,
				a.modified as item_modified,
				floor(time_to_sec(timediff(now(),a.modified))/60) as item_modified_age,
				a.modified_by as item_modified_by,
				a.publish_up as item_publish_up,
				a.publish_down as item_publish_down,
				a.trash as item_trash,
				a.access as item_access,
				a.ordering as item_ordering,
				a.featured as item_featured,
				a.featured_ordering as item_featured_ordering,
				a.image_caption as item_image_caption,
				a.image_credits as item_image_credits,
				a.video_caption as item_video_caption,
				a.video_credits as item_video_credits,
				a.hits as item_hits,
				a.metadesc as item_metadescription,
				a.metadata as item_metadata,
				a.metakey as item_metakeywords,
				a.plugins as item_plugins,
				a.language as item_language,
				
				( select count(*) from #__k2_comments kc where kc.itemID = $my_id and kc.published = 1 ) as item_comments_count,
				( select count(*) from #__k2_comments kc2 where kc2.itemID = $my_id and kc2.published = 0 ) as item_unpublished_comments_count,
				( select count(*) from #__k2_attachments ka where ka.itemID = $my_id ) as item_attachments_count,
				kr.rating_count as item_rating_count,
				kr.rating_sum as item_rating_sum,

				a.catid as category_id,
				cat1.name as category_name,
				cat1.alias as category_alias,
				cat1.description as category_description,
				cat1.published as category_published,
				cat1.access as category_access,
				cat1.ordering as category_ordering,
				cat1.image as category_image,
				cat1.language as category_language,

				cat1.name as ct1,
				cat1.id as ci1,
				cat2.name as ct2,
				cat2.id as ci2,
				cat3.name as ct3,
				cat3.id as ci3,
				cat4.name as ct4,
				cat4.id as ci4,
				cat5.name as ct5,
				cat5.id as ci5,
				cat6.name as ct6,
				cat6.id as ci6,
				cat7.name as ct7,
				cat7.id as ci7,
				cat8.name as ct8,
				cat8.id as ci8
				");
			$query->from("`#__k2_items` a");
			$query->leftJoin( "`#__k2_rating` kr ON kr.itemID = $my_id ")
				->leftJoin( "`#__k2_categories` cat1 ON a.catid = cat1.id ")
				->leftJoin( "`#__k2_categories` cat2 ON cat1.parent = cat2.id ")
				->leftJoin( "`#__k2_categories` cat3 ON cat2.parent = cat3.id ")
				->leftJoin( "`#__k2_categories` cat4 ON cat3.parent = cat4.id ")
				->leftJoin( "`#__k2_categories` cat5 ON cat4.parent = cat5.id ")
				->leftJoin( "`#__k2_categories` cat6 ON cat5.parent = cat6.id ")
				->leftJoin( "`#__k2_categories` cat7 ON cat6.parent = cat7.id ")
				->leftJoin( "`#__k2_categories` cat8 ON cat7.parent = cat8.id ")
				;

			$query->where( "a.id = $my_id" )
				->where( "a.published = 1" )
			    ->where( "( a.publish_up = $nullDate OR a.publish_up <= $now )" )
		    	->where( "( a.publish_down = $nullDate OR a.publish_down >= $now )" );

			$db->setQuery( $query, 0, 1 );
			$row		= $db->loadObject();
			$cat_names	= array();
			$cat_ids	= array();
			$rating_average = 0;
			if ($row) {
				for($i = 1; $i <= 8; $i++ ) {
					$ct = "ct$i";
					$ci = "ci$i";
					if ( $row->$ct != '' ) $cat_names[] = $row->$ct;
					if ( $row->$ci != '' ) $cat_ids[] = $row->$ci;
				}
				if ((int)$row->item_rating_count > 0) {
					$rating_average = (int)$row->item_rating_sum / (int)$row->item_rating_count;
				}
			} else {
				$row = new stdclass();
			}
			$row->ancestor_category_names = $cat_names;
			$row->ancestor_category_ids = $cat_ids;
			$row->item_rating_average = $rating_average;
			$rows[$id]	= $row;
		}

		return @$rows[$id];
	}
	
	function itemTags() {
		// we will cache here by item id.
		if ( $this->option != 'com_k2' ) return null;
		$pageType = $this->pageType();
		
		if ($pageType == 'item.view') {
			static $rows = array();

			$id = (int)$this->id;
			if (!$id) return;
		
			if ( !array_key_exists( $id, $rows ) ) {

				$db			= JFactory::getDbo();
				$query		= $db->getQuery(true); 

				$query->select("DISTINCT t.name as tag");
				$query->from("`#__k2_tags_xref` tx, `#__k2_tags` t");

				$query->where( "tx.itemID = $id" );
				$query->where( "tx.tagID = t.id" );
				$query->where( "t.published = 1" );

				$db->setQuery( $query );
				$res		= $db->loadObjectList();
				$tags = array();
				foreach($res as $row) {
					$tags[] = $row->tag;
				}
				$rows[$id]	= $tags;
			}
			return @$rows[$id];
		}
		
		if ($pageType == 'tag.itemlist') {
			if ($this->tag != '') {
				return array($this->tag);
			}
			return array();
		}
		
	}
	
}
