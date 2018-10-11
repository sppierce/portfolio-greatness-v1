<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JomGenius classes
 * 
 * @package		JomGenius
 * @version		1.0 (HikaShop starter edition)
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2010-2013 Brandon IT Consulting. All rights reserved.
 *
 * @changelog	v1	basic functionality
 *					
 */

class JomGeniusClassHikashop extends JomGeniusParent {
	
	/* for Hikashop, we allow it to be instantiated even if we are not currently on the Hikashop component.
	 * This is because we might want to check the contents of the cart on any page.
	 */
	
	var $Itemid;
	var $view;
	var $option;
	var $product_id;
	var $page;
	var $category_id;
	var $layout;
	var $task;
	
	function __construct() {
		$jinput				= JFactory::getApplication()->input;
		
		$this->Itemid		= $jinput->get('Itemid');
		$this->view			= $jinput->get('view');
		$this->layout		= $jinput->get('layout');
		$this->cid			= $jinput->get('cid');					// product id or other type of id?
		$this->task			= $jinput->get('task');					// e.g. show
		$this->ctrl			= $jinput->get('ctrl');					// e.g. product
		$this->option		= $jinput->get('option');
		$this->step			= $jinput->get('step');
		$this->name			= $jinput->get('name');
		$this->layout		= $jinput->get('layout');				// e.g. listing
		$this->product_id	= $jinput->get('product_id');			// e.g. on direct product pages
		$this->menu_main_category		= $jinput->get('menu_main_category');				// e.g. 2
		
		if ( $this->option == 'com_hikashop' and $this->view == 'product' and $this->layout == 'show' and $this->cid == '' ) {
			// for some reason you can specify more than 1 item when setting up this type of menu item,
			// but the page only actually displays the 1st one.
			$tmp_cid_arr = JomGeniusParent::menuItemParam( 'product_id' );
			if ( is_array($tmp_cid_arr) and count( $tmp_cid_arr )) {
				$this->cid = $tmp_cid_arr[0];
			}
		}
		return;
		
		// use the following as a template for how to deal with different page types for Hikashop, when necessary...
		
		// What happens when the URL is "&option=com_hikashop&Itemid=XXX", so there's no "page" in the query string?
		// Borrowed from hikashop_parser.php to get the logic of how to interpret a request when there's a menu item
		// and itemid for the page, but the _GET has no "page" in it.
		
		if ( $this->option == 'com_hikashop' and !isset( $_REQUEST['page']) ) {
			
			/* need to revisit all these for VM1.98 / J1.7 FIXME */
			$tmp_product_id			= JomGeniusParent::menuItemParam( 'product_id' );
			$tmp_category_id		= JomGeniusParent::menuItemParam( 'category_id' );
			$tmp_flypage			= JomGeniusParent::menuItemParam( 'flypage' );
			$tmp_page				= JomGeniusParent::menuItemParam( 'page' );

			if( !empty( $tmp_product_id ) ) {
				$this->product_id	= $tmp_product_id;
				$this->view			= 'productdetails'; // new
			} elseif( !empty( $tmp_category_id ) ) {
				$this->category_id	= $tmp_category_id ;
				$this->view			= 'category'; // new
			}

			if( ( !empty( $tmp_product_id ) || !empty( $tmp_category_id ) ) && !empty( $tmp_flypage ) ) {
				$this->flypage		= $tmp_flypage; // not actually used here...
			}

			if( !empty( $tmp_page ) ) {
				$this->page			= $tmp_page;
			}
		}
	}
	
	function shouldInstantiate() {
		if ( $this->componentExists( 'com_hikashop' ) ) {
			return true;
		}
		return false;
	}
	

	/**
	 * A generic function that knows how to get lots of different info about the current page or product.
	 */
	function info( $type ) {
		$type = strtolower( str_replace( array(' ','_'), '', $type ) );
		
		
		switch( $type ) {
			case 'productid':
				return $this->productId();
				
			case 'pagetype':
				return $this->pageType();
				
			case 'categoryid':
				return $this->categoryId();

			case 'categoryids':
				return $this->categoryIds();

			case 'ancestorcategoryids':
				return $this->categoryIds( 'all' );

			default:
		
		}
	}
	
	/* returns the category id of the list, or the item being displayed.
	 * * top / bottom (default bottom)
	 * If the list: this is taken from the URL
	 * If the item: if a category id was in the URL then this is used. Otherwise,
	 *  if the item is in only 1 category, then that is used. Otherwise, if it's
	 *  in more than one, then one will be selected in no particular order.
	 */
	function categoryId() {
		$ids = $this->categoryIds();
		if (is_array($ids) and count($ids) > 0) return $ids[0];
		return null;
	}
	
	/* returns an array of immediate category ids that the item is in; not their parents.
	 * If the category id is in the URL, then this one will be first on the list.
	 */
	function categoryIds( $type="bottom" ) {
		$jinput				= JFactory::getApplication()->input;
		
		switch ( $type ) {
			case 'top':
				return $this->_topLevelCategoryIds();

			case 'all':
				return $this->_allCategoryIds();
			
			case 'bottom':
			default:
				$ids = array();
				if ($this->option == 'com_hikashop' ) {
					if ($this->view == 'category' or $this->ctrl == 'category') {
						// menu link to main category, plus any categories navigated inside that
						if ($this->cid) {
							return array($this->cid);
						}
						if ($this->layout == 'listing') {
							$res = $this->hsConfig('menu_' . $this->Itemid, 'selectparentlisting');
							return array($res);
						}
					}
					else if ($this->view == 'product' and ($this->task == 'listing' or $this->layout == 'listing')) {
						// Clever things: for these ones, HS stores them in a config thing in its own
						// database. So we need to extract it keyed on menu id (Itemid) and decode the
						// setting.
						$res = $this->hsConfig('menu_' . $this->Itemid, 'selectparentlisting');
						return array($res);
					}
					else if ( $pid = $this->productId() ) {
						$allCatInfo = $this->_categoryInfoForProductId( $pid );
						if ( is_array( $allCatInfo ) ) {
							// step through array, and return array of items containing the c1 item in each
							foreach ( $allCatInfo as $cat ) {
								$ids[] = $cat['c1'];
							}
							return $ids;
						}
					}
				}
				//was not in Hikashop
				return array();
		}
	}
	
	function _allCategoryIds() {
		return $this->_allCategoryStuff( 'ids' );
	}

	function _allCategoryNames() {
		return $this->_allCategoryStuff( 'names' );
	}
		
	function _allCategoryStuff( $type ) {
		$ids = array();

		if ($this->option == 'com_hikashop' ) {
			if ( ($this->layout == 'show' or $this->task == 'show') and ($this->ctrl == 'product' or $this->view == 'product') ) {
				$allCatInfo = $this->_categoryInfoForProductId( $this->cid );
			} else if ( $this->view == 'category' or $this->ctrl == 'category' ) {
				if ($this->cid) {
					$allCatInfo = $this->_categoryInfoForCategoryId( $this->cid );
				} else if ($this->layout == 'listing') {
					$allCatInfo = $this->_categoryInfoForCategoryId( $this->hsConfig('menu_' . $this->Itemid, 'selectparentlisting') );
				}
			} else if ($this->view == 'product' and $this->layout == 'listing') { // product listing page
				if ($this->cid) {
					$allCatInfo = $this->_categoryInfoForCategoryId( $this->cid );
				} else if ($this->layout == 'listing') { // direct menu link
					$allCatInfo = $this->_categoryInfoForCategoryId( $this->hsConfig('menu_' . $this->Itemid, 'selectparentlisting') );
				}
			} else {
				return;
			}
			if ( is_array( $allCatInfo ) ) {
				// step through array, and return array of items containing the c1 item in each
				foreach ( $allCatInfo as $cat ) {
					for ( $i = 1; $i <= 8; $i++ ) {
						if ($type == 'names') {
							$thing = $cat['n' . $i];
						} else {
							$thing = $cat['c' . $i];
						}
						if ($thing != null and !in_array( $thing, $ids ) ) {
							$ids[] = $thing;
						}
					}
				}
				return $ids;
			}
		}
		//was not in hks
		return null;
		
	}
	
	function hsconfig( $item, $param=null ) {
		$db		= JFactory::getDBO();
		$query = 'SELECT config_value from #__hikashop_config where config_namekey = ' . $db->quote($item);
		$db->setQuery($query);
		$res = $db->loadResult();
		if ($res) {
			$res = unserialize(base64_decode($res));
			if ($param) {
				return @$res[$param];
			}
			return $res;
		}
	}

	/**
	 * product_id
	 */
	function productId() {
		if ( ! (
			$this->option == 'com_hikashop'
			and ($this->view == 'product' or $this->ctrl == 'product')
			and ($this->task == 'show' or $this->layout == 'show')
			) ) {
			return '';
		}
		$c = (int)$this->cid;			// after navigating to a product page
		if ($c) return $c;
		$p = (int)$this->product_id;	// direct link to product page
		if ($p) return $p;
	}
	
	function pageType() {
		
//		print_r($this);
		
		if ($this->view == 'profile' and $this->option == 'com_users' /* and $this->layout == 'edit' */) {
			return 'user.profile';
		}
		if ( $this->option != 'com_hikashop' ) return null;

		if ($this->view == 'category' or $this->ctrl == 'category') {
			return 'category';
		}

		if (($this->view == 'checkout' or $this->ctrl == 'checkout') /* and $this->task == 'step' */) {
			
			require_once realpath(JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php');
			$conf =& hikashop_config();
			$checkout_workflow = trim($conf->get('checkout','login_address_shipping_payment_coupon_cart_status_confirm,end'));
			$steps=explode(',',$checkout_workflow);
						
			// note: the actual steps are set up in the admin part of Hikashop, and JomGenius
			// doesn't know what is actually on each step. We just report the step ids in sequence.
			$step = JFactory::getApplication()->input->getInt('step',0);
			// skip the login step if it's the only step on the page, and if person is already logged in
			if ($steps[$step] == 'login') {
				$user		= JFactory::getUser();
				if (!$user->guest) {
					JRequest::setVar('previous',$step);
					JRequest::setVar('step',$step+1);
				}
			}
			return 'checkout#' . $step;
		}
		
		if ($this->view == 'product' or $this->ctrl == 'product') {
			if ($this->task == 'send_email') {
				return 'contact.sent';
			}
			if ($this->layout == 'contact') {
				return 'contact'; /* a strange one this.. */
			}
			if ($this->layout == 'listing') {
				return 'product.listing';
			}
			if ($this->layout == 'compare') {
				return 'product.compare';
			}
			if ($this->layout == 'show' or $this->task == 'show') {
				return 'product.show';
			}
		}
		
		
		if ($this->view == 'address' or $this->ctrl == 'address') {
			if ($this->task == 'add') {
				return 'user.address.add';
			}
			if ($this->task == 'edit') {
				return 'user.address.edit';
			}
			return 'user.address.listing';
		}
		
		if ($this->view == 'order' or $this->ctrl == 'order') {
			if ($this->layout == 'listing' or ($this->layout == '' and $this->view == '' and $this->task == '')) {
				return 'user.order.listing';
			}
			if ($this->layout == 'show' or $this->task == 'show') {
				return 'user.order.show';
			}
		}
		
		if ($this->view == 'user' or $this->ctrl == 'user') {
			switch(strtolower($this->layout)) {
			 	case 'downloads':			return 'user.downloads';
				case '':
			 	case 'cpanel':				return 'user.cpanel';
			}
			return 'user';
		}

		return '';
	}
		
	/* This method returns an array of information, cached for each product_id, about the categories
	 * that the product is in.
	 * If the current URL is in hikashop and contains the category id, then this category is always put
	 * to the top of the list.
	 * Otherwise, each row contains:
	 * c1 - c5: the immediate category id of the product (c1), and all the parents, up to 5 levels including the child
	 * n1 - n5: the names of each of the categories above.
	 * To get the top-level name or id, you need to go backward through c5 to c1 to find the first one that's not blank.
	 */
	function _categoryInfoForProductId( $id ) {
		$id = (int)$id;
		static $infoForCategory = array();
		if ( array_key_exists( $id, $infoForCategory ) ) {
			return $infoForCategory[ $id ];
		}
		
		$db		= JFactory::getDBO();
		
		$query = "SELECT distinct
			cx1.`category_id` as c1,
			cx1.`category_name` as n1,
			cx2.`category_id` as c2,
			cx2.`category_name` as n2,
			cx3.`category_id` as c3,
			cx3.`category_name` as n3,
			cx4.`category_id` as c4,
			cx4.`category_name` as n4,
			cx5.`category_id` as c5,
			cx5.`category_name` as n5,
			cx6.`category_id` as c6,
			cx6.`category_name` as n6,
			cx7.`category_id` as c7,
			cx7.`category_name` as n7,
			cx8.`category_id` as c8,
			cx8.`category_name` as n8
			
			from #__hikashop_product hp
			left outer join #__hikashop_product_category hpc on (hp.`product_parent_id` = hpc.`product_id` OR hp.`product_id` = hpc.`product_id` )

			left outer join #__hikashop_category cx1 on hpc.`category_id` = cx1.`category_id`
			left outer join #__hikashop_category cx2 on cx1.`category_parent_id` = cx2.`category_id`
			left outer join #__hikashop_category cx3 on cx2.`category_parent_id` = cx3.`category_id`
			left outer join #__hikashop_category cx4 on cx3.`category_parent_id` = cx4.`category_id`
			left outer join #__hikashop_category cx5 on cx4.`category_parent_id` = cx5.`category_id`
			left outer join #__hikashop_category cx6 on cx5.`category_parent_id` = cx6.`category_id`
			left outer join #__hikashop_category cx7 on cx6.`category_parent_id` = cx7.`category_id`
			left outer join #__hikashop_category cx8 on cx7.`category_parent_id` = cx8.`category_id`

			where hp.`product_id` = $id";

		$db->setQuery( $query );
		$res = $db->loadAssocList();

		// cache it
		$infoForCategory[$id] = $res;

		return $res;
	}
	
	function _categoryInfoForCategoryId( $id ) {
		$category_id = (int)$id;
		if ( $category_id == 0 ) {
			return;
		}
		static $infoForCategory = array();
		if ( array_key_exists( $category_id, $infoForCategory ) ) {
			return $infoForCategory[ $category_id ];
		}
		$db		= JFactory::getDBO();
		
		$query = "SELECT distinct
			cx1.`category_id` as c1,
			cx1.`category_name` as n1,
			cx2.`category_id` as c2,
			cx2.`category_name` as n2,
			cx3.`category_id` as c3,
			cx3.`category_name` as n3,
			cx4.`category_id` as c4,
			cx4.`category_name` as n4,
			cx5.`category_id` as c5,
			cx5.`category_name` as n5,
			cx6.`category_id` as c6,
			cx6.`category_name` as n6,
			cx7.`category_id` as c7,
			cx7.`category_name` as n7,
			cx8.`category_id` as c8,
			cx8.`category_name` as n8
			
			from #__hikashop_category cx1
			left outer join #__hikashop_category cx2 on cx1.`category_parent_id` = cx2.`category_id`
			left outer join #__hikashop_category cx3 on cx2.`category_parent_id` = cx3.`category_id`
			left outer join #__hikashop_category cx4 on cx3.`category_parent_id` = cx4.`category_id`
			left outer join #__hikashop_category cx5 on cx4.`category_parent_id` = cx5.`category_id`
			left outer join #__hikashop_category cx6 on cx5.`category_parent_id` = cx6.`category_id`
			left outer join #__hikashop_category cx7 on cx6.`category_parent_id` = cx7.`category_id`
			left outer join #__hikashop_category cx8 on cx7.`category_parent_id` = cx8.`category_id`

			where
			cx1.`category_type` = 'product'
			and cx1.`category_id` = $category_id";
		
		$db->setQuery( $query );
		$res = $db->loadAssocList();

		// cache it
		$infoForCategory[$category_id] = $res;

		return $res;
	}
	
	

}