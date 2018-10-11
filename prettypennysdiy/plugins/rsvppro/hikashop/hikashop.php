<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgRsvpproHikashop extends JPlugin
{

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->catid = $this->params->get("catid", "0");

		$lang = JFactory::getLanguage();
		$lang->load("plg_hikashop_rsvppro", JPATH_ADMINISTRATOR);

	}

	public
	function generatePaymentPage(&$html, $attendee, $rsvpdata, $event, $transaction)
	{

		$jinput = JFactory::getApplication()->input;

		// Make double sure!
		$lang = JFactory::getLanguage();
		$lang->load("plg_hikashop_rsvppro", JPATH_ADMINISTRATOR);

		// TODO transaction based invoiceid
		$invoice = $jinput->getString("invoiceid", "") . '_' . $transaction->transaction_id;

		$html = "Invoice id is $invoice<br/>";
		$amount = RsvpHelper::ceil_dec(JRequest::getFloat("amount",0), 2, ".");

		$mainframe = JFactory::getApplication();
		$Itemid = $jinput->getInt("Itemid");
		$detaillink = JRoute::_(JUri::root() . $event->viewDetailLink($event->yup(), $event->mup(), $event->dup(), false), false);

		// Do we redirect back to the event?
		if ($this->params->get("redirecttoevent",0)){
			JFactory::getApplication()->enqueueMessage(JText::_( 'JEV_RSVPPRO_HIKASHOP_PRODUCT_ADDED_TO_CART' ));
			JRequest::setVar("return_url",urlencode(base64_encode($event->viewDetailLink($event->yup(), $event->mup(), $event->dup(), true))));
		}

		$currency = $this->params->get("currency", "GBP");
		if (isset($rsvpdata->template) && is_numeric($rsvpdata->template))
		{
			$db = JFactory::getDbo();
			$db->setQuery("Select params from #__jev_rsvp_templates where id=" . intval($rsvpdata->template));
			$templateParams = $db->loadObject();

			if ($templateParams)
			{
				$templateParams = json_decode($templateParams->params);
				$templateParams = RsvpHelper::translateTemplateParams($rsvpdata->template, $templateParams);
			}
			else
			{
				$templateParams = $params;
			}
		}
		else
		{
			$templateParams = $params;
		}
		$currency = isset($templateParams->Currency) ? $templateParams->Currency : $currency;

		$html = $this->params->get("template", "Total Fees = {TOTALFEES}<br/>Fees Already Paid= {FEESPAID}<br/>Outstanding Balance = {BALANCE}<br/><br/>Please send your payment to ...");
		if (isset($attendee->outstandingBalances))
		{
			$html = str_replace("{TOTALFEES}", $currency . " " . $attendee->outstandingBalances['totalfee'], $html);
			$html = str_replace("{FEESPAID}", $currency . " " . $attendee->outstandingBalances['feepaid'], $html);
			$html = str_replace("{BALANCE}", $currency . " " . $attendee->outstandingBalances['feebalance'], $html);
			$html = str_replace("{RAWBALANCE}",  $attendee->outstandingBalances['feebalance'], $html);
		}

		// setup transaction data
		$transaction->amount = $amount;
		$transaction->currency = $currency;
		$transaction->attendee_id = $attendee->id;
		$transaction->gateway = "hikashop";
		$transaction->params = new stdClass();
		$transaction->params = json_encode($transaction->params);

		$transaction_store = $transaction->store();

		// This redirects the visitor to the Hikashop checkout.
		$rp_id = $event->rp_id();

		$eventid = $event->ev_id();

		// attach anonymous creator etc.
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onDisplayCustomFields', array(&$event));

		$product = $this->getProduct($rsvpdata, $rp_id, $eventid);

		// Make sure the product has been created yet
		if (!$product)
		{
			$this->setupProduct($event, $rsvpdata,$templateParams);

			$product = $this->getProduct($rsvpdata, $rp_id, $eventid);

		}
		else if ($product->product_name != $event->title() . " (".JEV_CommonFunctions::jev_strftime(JText::_("DATE_FORMAT_4"), JevDate::strtotime($event->startrepeat)).")" ) {
			// need to update product title
			$this->updateProductTitle($event, $product);
		}
		// Do we need to set and image in the product
		//if (isset($event->filedata["image1"]) && !isset($product)){

		$this->addToCart($amount, $transaction, $product);

	}

	public static function NotifyPayment($templateParams) {
		return $templateParams->get("hsnotifyppay", 1);
	}

	public static function PaymentMessageType() {
		return "hspay";
	}

	static public
	function transactionDetailLink($transaction, $rsvpdata, $attendee, $event)
	{

		$plugin = JPluginHelper::getPlugin("rsvppro", "hikashop");
		$params = new JRegistry($plugin->params);
		$hikacategory = $params->get('hikacategory',0);
		$rsvp_sku = $params->get('skuprefix', 'RSVP');

		$rp_id = $event->rp_id();
		$eventid = $event->ev_id();

		$db = JFactory::getDBO();
		if ($rsvpdata->allrepeats)
		{
			$sku = $db->quote($rsvp_sku . '_' . $eventid . '_0');
		}
		else
		{
			$sku = $db->quote($rsvp_sku . '_' . $eventid . '_' . $rp_id);
		}


		$Itemid = JRequest::getInt("Itemid");
		$url_itemid = '';
		if(!empty($Itemid)){
			$url_itemid='&Itemid='.$Itemid;
		}

		include_once(JPATH_ADMINISTRATOR . '/components/com_hikashop/helpers/helper.php');
		$orderUrl = hikashop_completeLink('order'.$url_itemid);
		$config =& hikashop_config();
		if($config->get('force_ssl',0) && strpos('https://',$orderUrl) === false) {
			$orderUrl = str_replace('http://','https://',HIKASHOP_LIVE) . 'index.php?option=com_hikashop&ctrl=order';
		}

		// Get the order info
		$sql = "SELECT ord.*, prod.* FROM #__hikashop_order as ord"
			. " \n LEFT JOIN #__hikashop_order_product as prod ON prod.order_id = ord.order_id"
			. "\n WHERE 1 "
			. " \n AND prod.rsvptransactionid=" .  $transaction->transaction_id
			. "\n ORDER BY order_modified desc"
			. "\n Limit 1";
		$db->setQuery($sql);
		// Make sure Joomfish doesn't translate this
		$order = $db->loadObject('stdClass', false);

		if (!$order)
			return "";

		ob_start();
		?>
        <a href="<?php echo hikashop_completeLink('order&task=show&cid='.$order->order_id.$url_itemid); ?>">
			<?php echo $order->order_number; ?>
        </a>
		<?php
		$orderUrl = ob_get_clean();
		return $orderUrl;

	}

	public
	function activeGatewayClass(&$activeGatewayClass, $action = "notify")
	{
		$gateway = JRequest::getString("gateway");

		if ($gateway == "hikashop" || $gateway == "2" || strpos($gateway, "hikashop") === 0)
		{
			$activeGatewayClass = __CLASS__;
		}

	}

	public
	function activeGateways(&$activeGatewayClasses)
	{
		$activeGatewayClasses[] = __CLASS__;

	}

	public
	function updateHikaPaymentStatus($rsvpdata, $attendee, $event, $transaction,$order)
	{
		if (!$event)
		{
			return;
		}

		// Important to avoid repeats AND RECURSION
		static $updated = array();
		if (isset($updated[$event->rp_id()]))
		{
			return;
		}
		$updated[$event->rp_id()] = 1;

		$namefields = array("ju.username", "ju.name", "ju.name, ju.username");
		$params = JComponentHelper::getParams("com_rsvppro");
		$namefield = $namefields [$params->get("userdatatype", 0)];

		$where = array();
		$join = array();

		$where[] = "ev.ev_id IS NOT NULL";
		$where[] = "atdees.id = $transaction->attendee_id";

		$query = "SELECT det.*, atd.* , atd.id as atd_id, atdc.atdcount, atdees.*,atdees.id as atdee_id, ju.username, ju.email, "
			. " CASE WHEN atdees.user_id=0 THEN atdees.email_address ELSE CONCAT_WS(' - ',$namefield,ju.email) END as attendee, "
			. " CASE WHEN atdees.user_id=0 THEN atdees.email_address ELSE ju.name END as name "
			. "\n FROM #__jevents_vevent as ev "
			. "\n LEFT JOIN #__jevents_vevdetail as det ON ev.detail_id=det.evdet_id"
			. "\n LEFT JOIN #__jev_attendance AS atd ON atd.ev_id = ev.ev_id"
			. "\n LEFT JOIN #__jev_attendeecount AS atdc ON atd.id = atdc.at_id"
			. "\n LEFT JOIN #__jev_attendees AS atdees ON atdees.at_id = atd.id"
			. "\n LEFT JOIN #__users AS ju ON ju.id = atdees.user_id"
			. ( count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '' )
			. ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' )
		;
		$db = JFactory::getDbo();
		$db->setQuery($query);

		$atdee = $db->loadObject();

		$newstate = 1;
		if ($order->order_status=="cancelled"  || $order->order_status=="refunded"   || $order->order_status=="created"  ) {
			$newstate = 0;
		}
		// else $order->order_status=="confirmed"

		$sql = "UPDATE #__jev_rsvp_transactions SET paymentstate=$newstate  WHERE transaction_id = " . $transaction->transaction_id ;
		$db->setQuery($sql);
		$db->execute();
		$transaction->paymentstate = $newstate;

		$comparams = JComponentHelper::getParams("com_rsvppro");
		include_once(JPATH_ADMINISTRATOR . "/components/com_rsvppro/libraries/attendeehelper.php");
		$attendeehelper = new RsvpAttendeeHelper($comparams);

		// update attendee count etc.
		$xmlfile = JevTemplateHelper::getTemplate($rsvpdata);

		if (is_int($xmlfile) || file_exists($xmlfile))
		{
			// update attendee state
			$rsvpparams = new JevRsvpParameter($attendee->params, $xmlfile, $rsvpdata, $event);
			$feesAndBalances = $rsvpparams->outstandingBalance($attendee);
		}

		$attendeehelper->countAttendees($rsvpdata->id, true);

		$this->notifyHikaShopPayment($transaction, $attendee, $rsvpdata);
	}

	public
	function getProduct($rsvpdata, $rpid, $eventid)
    {
	    $plugin = JPluginHelper::getPlugin("rsvppro", "hikashop");

		$params = new JRegistry($plugin->params);
		$rsvp_sku = $params->get('skuprefix', 'RSVP');

		$db = JFactory::getDbo();
		if ($rsvpdata->allrepeats)
		{
			$sku = $db->quote($rsvp_sku . '_' . $eventid . '_0');
		}
		else
		{
			$sku = $db->quote($rsvp_sku . '_' . $eventid . '_' . $rpid);
		}
		$this->sku = $sku;

		// Clean up
		if (false){
			$sql = "SELECT p.product_id, product_name FROM #__hikashop_product as p"
				. " \n WHERE p.product_code LIKE ('" . $rsvp_sku . "%')";
			$db->setQuery($sql);
			$products = $db->loadObjectList('product_id');
			var_dump($products);

			$product_ids = array_keys($products);
			$product_ids[] = 0;

			$sql = "SELECT p.* FROM #__hikashop_product_category as p"
				. " \n WHERE p.product_id IN (" . implode(",", $product_ids).")";
			$db->setQuery($sql);
			$product_categories = $db->loadObjectList('product_id');

			var_dump($product_categories);

			$sql = "DELETE FROM #__hikashop_product_category "
				. " \n WHERE product_id IN (" . implode(",", $product_ids).")";
			$db->setQuery($sql);
			$db->execute();

			$sql = "DELETE FROM #__hikashop_product "
				. " \n WHERE product_id IN (" . implode(",", $product_ids).")";
			$db->setQuery($sql);
			$db->execute();

			$sql = "SELECT p.product_id, product_name FROM #__hikashop_product as p"
				. " \n WHERE p.product_code LIKE ('" . $rsvp_sku . "%')";
			$db->setQuery($sql);
			$products = $db->loadObjectList('product_id');
			var_dump($products);

			$product_ids = array_keys($products);
			$product_ids[] = 0;

			$sql = "SELECT p.* FROM #__hikashop_product_category as p"
				. " \n WHERE p.product_id IN (" . implode(",", $product_ids).")";
			$db->setQuery($sql);
			$product_categories = $db->loadObjectList('product_id');

			var_dump($product_categories);

			exit();
		}

		$sql = "SELECT p.* FROM #__hikashop_product as p"
			. " \n WHERE p.product_code=" . $sku;
		$db->setQuery($sql);

		// Make sure Joomfish doesn't translate this
		$product = $db->loadObject('stdClass', false);

		// Check integrity of the category
		if ($product) {

			$sql = "SELECT p.* FROM #__hikashop_product_category as p"
				. " \n WHERE p.product_id=" . $product->product_id;
			$db->setQuery($sql);
			$productCategory = $db->loadObject('stdClass');
			if ($productCategory->category_id==0) {
				$cat =  $params->get('hikacategory', 0);
				$sql = "UPDATE #__hikashop_product_category set category_id=".$cat." WHERE category_id=0 and product_id=" . $product->product_id;
				$db->setQuery($sql);
				$db->execute();
			}
		}

		return $product;

	}

	private
	function setupProduct($row, $rsvpdata, $templateParams)
	{
		// Create the product
		$db = JFactory::getDbo();

		$amount = RsvpHelper::ceil_dec(JRequest::getFloat("amount",0), 2, ".");
		$currency = $this->params->get("currency", "GBP");
		$currency = isset($templateParams->Currency) ? $templateParams->Currency : $currency;
		$db->setQuery("SELECT * from #__hikashop_currency where currency_code = '$currency'");
		$currencyData = $db->loadObject();
		if ($currencyData) {
			$currency = $currencyData->currency_id;
		}
		else {
			$currency = 1;
		}

		$db->setQuery("SELECT config_value from #__hikashop_config where config_namekey= 'main_currency'");
		$mainCurrency = $db->loadResult();

		$title = $row->title();
		$rp_id = $row->rp_id();
		$eventid = $row->ev_id();
		$plugin = JPluginHelper::getPlugin("rsvppro", "hikashop");
		$params = new JRegistry($plugin->params);
		$rsvp_sku = $params->get('skuprefix', 'RSVP');

		$cat =  $params->get('hikacategory', 0);
		if ($cat==0){
			JFactory::getApplication()->enqueueMessage(JText::_( 'JEV_RSVPPRO_HIKASHOP_NO_VALID_CATEGORY_SET_FOR_PRODUCTS' ), 'error');
		}

		$sql = "SELECT p.* FROM #__hikashop_category as p"
			. " \n WHERE p.category_id=" . $cat;
		$db->setQuery($sql);
		$productCategory = $db->loadObject('stdClass');
		if (!$productCategory ){
			JFactory::getApplication()->enqueueMessage(JText::_( 'JEV_RSVPPRO_HIKASHOP_NO_VALID_CATEGORY_SET_FOR_PRODUCTS' ), 'error');
		}

		$sdescription = strip_tags($row->content());
		$description = $row->content() ; // . "<br/>{jevent=$rp_id|_self|1}";

		//$ptypeid = $this->ptypeid;

		if (isset($row->_imageurl1))
		{
			$image = str_replace(JURI::root(), "", $row->_imageurl1);
			$thumb = str_replace(JURI::root(), "", $row->_thumburl1);
		}
		else
		{
			$image = $db->quote("");
			$thumb = $db->quote("");
		}

		if ($rsvpdata->allrepeats)
		{
			$sku = $rsvp_sku . '_' . $eventid . '_0';
		}
		else
		{
			$sku = $rsvp_sku . '_' . $eventid . '_' . $rp_id;
		}

		// is the custom field created?
		$cat =  $params->get('hikacategory', 0);
		$sql = "SELECT f.* FROM #__hikashop_field as f"
			. " \n WHERE f.field_namekey='rsvptransactionid'" ;
		$db->setQuery($sql);

		$field = $db->loadObject('stdClass', false);

		$fieldavailable = true;
		if (!$field){
			$fieldavailable = false;
		}
		else {
			$fieldcats = explode(",",$field->field_categories);
			JArrayHelper::toInteger($fieldcats);
			if (!in_array($cat, $fieldcats)){
				$fieldavailable = false;
			}
		}
		if (!$fieldavailable) {
			include_once(JPATH_ADMINISTRATOR . '/components/com_hikashop/helpers/helper.php');
			$fieldClass = hikashop_get("class.field");
			$field = new stdClass();
			$field->field_id = 0;
			$field->field_realname = 'Transaction Id';
			$field->field_namekey = 'rsvptransactionid';
			$field->field_table = 'item' ;
			$field->field_type = 'text' ;
			$field->field_required = 1;
			$field->field_published = 1;
			$field->field_frontcomp = 1; // must enable in frontend to propogate its value in the order!
			$field->field_backend = 1;
			$field->field_backend_listing = 1; // show in backend list of products within orders
			$field->field_with_sub_categories = 0;
			$field->field_access = "all";
			$field->field_default = "";
			$field->field_value = "";
			$field->field_categories = ",$cat,";
			$field->field_options = 'a:11:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:9:"filtering";s:1:"1";s:9:"maxlength";s:2:"10";s:4:"rows";s:0:"";s:9:"zone_type";s:7:"country";s:12:"pleaseselect";s:1:"0";s:4:"size";s:2:"10";s:6:"format";s:0:"";s:5:"allow";s:0:"";s:8:"readonly";s:1:"0";}';

			$tables = array('cart_product','order_product');

			foreach($tables as $table_name){
				$sql = "SHOW COLUMNS FROM ".$fieldClass->fieldTable($table_name);
				$db->setQuery($sql);
				$cols = @$db->loadObjectList("Field");

				if (!array_key_exists($field->field_namekey, $cols)){
					$query = 'ALTER TABLE '.$fieldClass->fieldTable($table_name).' ADD `'.$field->field_namekey.'` TEXT NULL';
					$db->setQuery($query);
					$db->execute();
				}
			}

			$field_id = $fieldClass->save($field);

			if(empty($field->field_id)){
				$orderClass = hikashop_get('helper.order');
				$orderClass->pkey = 'field_id';
				$orderClass->table = 'field';
				$orderClass->groupMap = 'field_table';
				$orderClass->groupVal = $field->field_table;
				$orderClass->orderingMap = 'field_ordering';
				$orderClass->reOrder();
			}


		}

		$tax = $this->params->get("hikataxcategory", 0);
		$tax = isset($templateParams->hikataxcategory) ? $templateParams->hikataxcategory: $tax;

		$productData = new stdClass();
		$productData->product_name = $row->title(). " (".JEV_CommonFunctions::jev_strftime(JText::_("DATE_FORMAT_4"), JevDate::strtotime($row->startrepeat)).")";
		//$productData->product_name = $row->title(). " (".$row->yup()."-".$row->mup()."-".$row->dup().")";
		$productData->product_url = "";
		$productData->product_meta_description = "";
		$productData->product_keywords = "";
		$productData->product_page_title = "";
		$productData->product_alias = "";
		$productData->product_canonical = "";
		$productData->product_code = $sku;
		$productData->product_tax_id = $tax;  // This is the product tax category - we may need to set this in the plugin parameters
		$productData->product_manufacturer_id = "0";
		$productData->product_layout = "";
		$productData->product_quantity_layout = "show_select";
		$productData->product_quantity = "-1";
		$productData->product_min_per_order = "0";
		$productData->product_max_per_order = "1";
		$productData->product_sale_start = "";
		$productData->product_sale_end = "";
		$productData->product_msrp = "0.01";
		$productData->product_warehouse_id = "";
		$productData->product_weight = "0.000";  // No shipping required
		$productData->product_weight_unit = "kg";
		$productData->product_length = "0.000";
		$productData->product_width = "0.000";
		$productData->product_dimension_unit = "m";
		$productData->product_height = "0.000";
		$productData->product_published = "1";
		$productData->product_access = "all";
		$productData->product_type = "main";
		// new product
		$productData->product_id = "0";
		$productData->categories = array();

		$cat =  $params->get('hikacategory', 0);
		$productData->categories[$cat] = $cat;
		$productData->related = array();
		$productData->options = array();
		$productData->images = array();
		// These images are from the database to need to generate and ID
		//$productData->images[2] = "2";
		$productData->files = array();
		$productData->imagesorder = array();
		// See above
		//$productData->imagesorder[2] = "0";
		$productData->tags = array();
		$productData->prices = array();
		$productData->prices[0] = new stdClass();
		$productData->prices[0]->price_value = "0.01";
		$productData->prices[0]->price_currency_id = $currency; // Need the currency codes
		$productData->prices[0]->price_min_quantity = "0";
		$productData->prices[0]->price_access = "all";
		// Need to save a price in the database
		$productData->prices[0]->price_id = "0";
		if ($mainCurrency != $currency) {
			$productData->prices[1] = clone $productData->prices[0];
			$productData->prices[1]->price_currency_id = $mainCurrency; // Need the currency codes
		}
		$productData->oldCharacteristics = array();
		$productData->product_description = $row->content();
		$productData->product_modified = time();

		include_once(JPATH_ADMINISTRATOR . '/components/com_hikashop/helpers/helper.php');
		$productClass = hikashop_get('class.product');

		$status = $productClass->save($productData);

		if (!$status)
			return;

		$productClass->updateCategories($productData,$status);
		$productClass->updatePrices($productData,$status);
		$productClass->updateFiles($productData,$status,'files');
		$productClass->updateFiles($productData,$status,'images',$productData->imagesorder);
		$productClass->updateRelated($productData,$status,'related');
		$productClass->updateRelated($productData,$status,'options');
		$productClass->updateCharacteristics($productData,$status);

		return;

	}

	private
	function addToCart($amount, $transaction, $product = null)
	{

		$app    = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;

		$product_id = $product->product_id;

		// set the transaction id so HikaShop can pick it up
		if (!array_key_exists("data",$_REQUEST)){
			$_REQUEST["data"] = array();
		}
		if (!array_key_exists("item",$_REQUEST["data"])){
			$_REQUEST["data"]["item"] = array();
		}
		$_REQUEST["data"]["item"]["rsvptransactionid"] =  $transaction->transaction_id;

		//$product->rsvptransactionid = $transaction->transaction_id;

		// TODO see if we can interact with visible cart module in HikaShop
		$module_id = (int) $jinput->getCmd('module_id', 0);

		$cart_type = $jinput->getString('hikashop_cart_type_' . $product_id . '_' . $module_id, 'null');

		if ($cart_type == 'null')
			$cart_type = $jinput->getString('hikashop_cart_type_' . $module_id, 'null');

		if ($cart_type == 'null')
		{
			$cart_type = $jinput->getString('cart_type', 'cart');
		}

		$cart_type_id = $cart_type . '_id';

		include_once(JPATH_ADMINISTRATOR . '/components/com_hikashop/helpers/helper.php');

		$class = hikashop_get('class.cart');

		$hasCart = method_exists($class, 'hasCart');
		$cart_id = 0;

		if ($hasCart && $class->hasCart($jinput->getInt('cart_id', 0))) {

			$cart_id = $class->cart->cart_id;

		} elseif($this->hika_version() === 3) {
			$cart_id = $class->getCurrentCartId();
		}

		$addTo = $jinput->getString('add_to', '');

		if ($addTo !== '')
		{
			$from_id = $cart_id;
			if ($addTo === 'cart')
			{
				$jinput->set('from_id', $cart_id);
			}
			$cart_id = $app->getUserState(HIKASHOP_COMPONENT . '.' . $addTo . '_id', 0);
			$cart_type_id = $addTo . '_id';
			$jinput->set('cart_type', $addTo);
		}else
		{
			$jinput->set('cart_type', $cart_type);
		}
		$jinput->set($cart_type_id, $cart_id);

		$tmpl   = $jinput->getCmd('tmpl', 'index');
		$add = $jinput->getCmd('add', '');
		$add = empty($add) ? 0 : 1;

		$quantity = 1;

		// get the products in the cart
		$cartproducts = $cart_id>0 ? $class->get($cart_id) : array();

		$alreadyloaded = false;
		foreach ($cartproducts as $cartproduct){
			if ($cartproduct->product_id === $product_id){
				$alreadyloaded = true;
				break;
			}
		}

		if (!$alreadyloaded) {
			if (hikashop_loadUser() !== NULL || $cart_type !== 'wishlist')
			{
				if (!empty($product_id))
				{
					$type =$jinput->getWord('type', 'product');

					if ($type === 'product')
					{
						$product_id = (int) $product_id;
					}
					$status = $class->update($product_id, $quantity, $add, $type);

				}
				elseif (!empty($cart_product_id))
				{
					$status = $class->update($cart_product_id, $quantity, $add, 'item');
				}
				else
				{
					$formData = JRequest::getVar('item', array(), '', 'array');
					if (!empty($formData))
					{
						$class->update($formData, 0, $add, 'item');
					}
					else
					{
						$formData = JRequest::getVar('data', array(), '', 'array');
						if (!empty($formData))
						{

							$class->update($formData, 0, $add);
						}
					}
				}
				// We failed to update product, so we need to add product
				if ($this->hika_version()  === 3 && !$status) {
					$success = $class->addProduct($cart_id, $product);
				}
			}
			$app->setUserState(HIKASHOP_COMPONENT . '.' . $cart_type . '_new', '1');
		}

		if (@$class->errors && $tmpl !== 'component')
		{
			if (!empty($_SERVER['HTTP_REFERER']))
			{
				if (strpos($_SERVER['HTTP_REFERER'], HIKASHOP_LIVE) === false && preg_match('#^https?://.*#', $_SERVER['HTTP_REFERER']))
					return false;
				$app->redirect(str_replace('&popup=1', '', $_SERVER['HTTP_REFERER']));
			}else
			{
				echo '<html><head><script type="text/javascript">history.back();</script></head><body></body></html>';
				exit;
			}
		}
		$app->setUserState(HIKASHOP_COMPONENT . '.shipping_method', null);
		$app->setUserState(HIKASHOP_COMPONENT . '.shipping_id', null);
		$app->setUserState(HIKASHOP_COMPONENT . '.shipping_data', null);
		$app->setUserState(HIKASHOP_COMPONENT . '.payment_method', null);
		$app->setUserState(HIKASHOP_COMPONENT . '.payment_id', null);
		$app->setUserState(HIKASHOP_COMPONENT . '.payment_data', null);
		$config = & hikashop_config();
		$checkout = $jinput->getString('checkout', '');

		if (!empty($checkout))
		{
			global $Itemid;
			$url = 'checkout';
			if (!empty($Itemid))
			{
				$url.='&Itemid=' . $Itemid;
			}
			$url = hikashop_completeLink($url, false, true);
			$this->setRedirect($url);
		}
		else
		{
			$url = $jinput->get('return_url', '');
			if (empty($url))
			{
				$url = $jinput->get('url', '');
				$url = urldecode($url);
			}
			else
			{
				$url = base64_decode(urldecode($url));
			}
			$url = str_replace(array('&popup=1', '?popup=1'), '', $url);

			if (empty($url))
			{
				global $Itemid;
				$url = 'checkout';
				if (!empty($Itemid))
				{
					$url.='&Itemid=' . $Itemid;
				}
				$url = hikashop_completeLink($url, false, true);
			}
			//Module Params for both statements below.
			$module = JModuleHelper::getModule('hikashop_cart', false);
			$config = & hikashop_config();
			$params = new HikaParameter(@$module->params);

			if ($tmpl === 'component' && $config->get('redirect_url_after_add_cart', 'stay_if_cart') !== 'checkout')
			{
				$js = '';
				jimport('joomla.application.module.helper');
				global $Itemid;
				if (isset($Itemid) && empty($Itemid))
				{
					$Itemid = null;
					$jinput->set('Itemid', null);
				}

				if (!empty($module))
				{
					$module_options = $config->get('params_' . $module->id);
				}
				if (empty($module_options))
				{
					$module_options = $config->get('default_params');
				}
				foreach ($module_options as $key => $optionElement)
				{
					$params->set($key, $optionElement);
				}
				if (!empty($module))
				{
					foreach (get_object_vars($module) as $k => $v)
					{
						if (!is_object($v))
						{
							$params->set($k, $v);
						}
					}
					$params->set('from', 'module');
				}
				$params->set('return_url', $url);
				hikashop_getLayout('product', 'cart', $params, $js);
				return true;
			}
			else
			{
				$config = & hikashop_config();
				$url = str_replace(array('&popup=1', '?popup=1'), '', $url);
				if ($jinput->getInt('popup', 0) || (@$jinput->getInt('quantity', 0) && $config->get('redirect_url_after_add_cart', 'stay_if_cart') === 'ask_user'))
				{
					if (strpos($url, '?'))
					{
						$url.='&';
					}
					else
					{
						$url.='?';
					}
					$url.='popup=1';
					$app->setUserState(HIKASHOP_COMPONENT . '.popup', '1');
				}
				if (hikashop_disallowUrlRedirect($url))
				{
					return false;
				}
				if ($jinput->getInt('hikashop_ajax', 0) === 0)
				{
					$mainframe = JFactory::getApplication();
					$mainframe->redirect($url);
					return false;
				}
				else
				{
					ob_clean();
					if ($params->get('from', 'module') !== 'module' || $config->get('redirect_url_after_add_cart', 'stay_if_cart') === 'checkout')
					{
						echo 'URL|' . $url;
						exit;
					}
					else
					{
						$mainframe = JFactory::getApplication();
						$mainframe->redirect($url);

						return false;
					}
				}
			}
		}

		return '';

	}



	private function notifyHikaShopPayment($transaction, $attendee, $rsvpdata){
		$templateParams  = RsvpHelper::getTemplateParams($rsvpdata);

		$this->log("Notify payment of ".$transaction->transaction_id." for attendee ".$attendee->id);

		// immediate notification
		if ($templateParams->get("hsnotifyppay", 1)>0  && $transaction->gateway == "hikashop" && $transaction->paymentstate)
		{

			$this->log("Notification is enabled fpr gateway");

			$comparams = JComponentHelper::getParams("com_rsvppro");
			include_once(JPATH_ADMINISTRATOR . "/components/com_rsvppro/libraries/attendeehelper.php");
			$this->helper = new RsvpAttendeeHelper($comparams);

			$rpid = $attendee->rp_id;

			$this->dataModel = new JEventsDataModel();
			$this->queryModel = new JEventsDBModel($this->dataModel);

			// Find the first repeat
			$vevent = $this->dataModel->queryModel->getEventById($rsvpdata->ev_id, false, "icaldb");
			if ($rpid == 0)
			{
				$repeat = $vevent->getFirstRepeat();
			}
			else
			{
				list($year, $month, $day) = JEVHelper::getYMD();
				$repeatdata = $this->dataModel->getEventData(intval($rpid), "icaldb", $year, $month, $day);
				if ($repeatdata && isset($repeatdata["row"]))
					$repeat = $repeatdata["row"];
			}

			$user = JEVHelper::getUser($attendee->user_id);
			if ($user->id == 0 && $comparams->get("attendemails", 0))
			{
				$name = $attendee->email_address;
				$username = $attendee->email_address;
			}
			else
			{
				$name = $user->name;
				$username = $user->username;
			}

			$class = "plgRsvppro" . ucfirst($transaction->gateway);
			$pluginpath = version_compare(JVERSION, "1.6.0", 'ge')?JPATH_SITE."/plugins/rsvppro/$transaction->gateway/" : JPATH_SITE."/plugins/rsvppro/";
			JLoader::register($class, $pluginpath . $transaction->gateway . ".php");

			$this->helper->notifyUser($rsvpdata, $repeat, $user, $name, $username, $attendee, 'hspay', false, $transaction);

			$creator = JFactory::getUser(isset($repeat->created_by) ?$repeat->created_by : $repeat->created_by());
			if ($creator && $templateParams->get("hsnotifycreatorpay", 1)) {
				// send copy to creator too - switching bcc to that for creator messages before doing this
				$comparams = JComponentHelper::getParams("com_rsvppro");
				$notifybcc = $comparams->get("notifybcc", "");
				$bcccreator = $comparams->get("bcccreator", "");
				$comparams->set("notifybcc", $bcccreator);
				$this->helper->notifyUser($rsvpdata, $repeat, $creator, $name, $username, $attendee, 'hspay', false, $transaction);
				// re-enable bcc settings
				$comparams->set("notifybcc", $notifybcc);
			}

			return $repeat;
		}
	}

	protected function log( $text )
	{
		return;
		$params = JComponentHelper::getParams(RSVP_COM_COMPONENT);

		$logfile = JPATH_SITE. str_replace("//","/",$params->get('PayPalLogFileLocation','/administrator/components/com_rsvppro/logs/') . '/hika_log.txt');

		$fp = @fopen($logfile, 'ab' );
		if ($fp){
			@fwrite( $fp, $text . "\n\n" );
			@fclose( $fp );
		}
	}

	protected function updateProductTitle($event, & $product) {
		$product->product_name = $event->title() . " (".JEV_CommonFunctions::jev_strftime(JText::_("DATE_FORMAT_4"), JevDate::strtotime($event->startrepeat)).")";

		$db = JFactory::getDbo();
		$sql = "UPDATE #__hikashop_product SET product_name=".$db->quote($product->product_name)
			. " \n WHERE product_id=" . $product->product_id;
		$db->setQuery($sql);
		$db->execute();

		// is there an image to set too!

	}

        public function setupProductInAdvance($rsvpdata, $rpid, $eventid, $event)
        {
            	// Make sure the product hasn't been created yet
		$product = $this->getProduct($rsvpdata, $rpid, $eventid);

		if (!$product)
		{

                        if (isset($rsvpdata->template) && is_numeric($rsvpdata->template))
                        {
                                $db = JFactory::getDBO();
                                $db->setQuery("Select params from #__jev_rsvp_templates where id=" . intval($rsvpdata->template));
                                $templateParams = $db->loadObject();
                                if ($templateParams)
                                {
                                        $templateParams = json_decode($templateParams->params);
                                        $templateParams = RsvpHelper::translateTemplateParams($rsvpdata->template, $templateParams);
                                }
                                else
                                {
                                        $templateParams = $this->params;
                                }
                        }
                        else
                        {
                                $templateParams = $this->params;
                        }

			$this->setupProduct($event, $rsvpdata,$templateParams);
			$product = $this->getProduct($rsvpdata, $rpid, $eventid);
		}

        }

	public function hika_version(){
		//Check hikashop version, quick and dirty!
		if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_hikashop/classes/cart_legacy.php')) {
			return 3;
		} else {
			return 2;
		}
    }
}
