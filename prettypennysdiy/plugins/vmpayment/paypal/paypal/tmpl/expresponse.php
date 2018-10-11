<?php
/**
 *
 * Paypal payment plugin
 *
 * @author Jeremy Magne
 * @version $Id: paypal.php 7217 2013-09-18 13:42:54Z alatak $
 * @package VirtueMart
 * @subpackage payment
 * Copyright (C) 2004-2014 Virtuemart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
defined('_JEXEC') or die();

$response = $viewData['response'];

?>
<br />
<style>
	.paypalordersummary td {padding:10px;}
</style>
<table cellpadding="2" class="paypal_ordersummary">
	<?php 
	echo $this->getHtmlRow('VMPAYMENT_PAYPAL_API_PAYMENT_NAME',  $viewData['payment_name']);
	if ( $viewData['success']) {
		echo $this->getHtmlRow('COM_VIRTUEMART_ORDER_NUMBER', $response['invoice']);
		if ($viewData['method']->payment_type == '_xclick-subscriptions' || $viewData['method']->payment_type == '_xclick-payment-plan') {
			echo $this->getHtmlRow('VMPAYMENT_PAYPAL_PROFILEID', $response['PROFILEID']);
			echo $this->getHtmlRow('VMPAYMENT_PAYPAL_PROFILESTATUS', $response['STATUS']);
		} else {
			echo $this->getHtmlRow('VMPAYMENT_PAYPAL_API_AMOUNT', $response['PAYMENTINFO_0_AMT'] . ' ' . $response['PAYMENTINFO_0_CURRENCYCODE']);
			echo $this->getHtmlRow('VMPAYMENT_PAYPAL_API_TRANSACTION_ID', $response['PAYMENTINFO_0_TRANSACTIONID']);
		}
		//echo $this->getHtmlRow('VMPAYMENT_PAYPAL_API_AUTHORIZATION_CODE', $response['CORRELATIONID']);
	} else {
		for ($i = 0; isset($response["L_ERRORCODE".$i]); $i++) {
			echo $this->getHtmlRow('VMPAYMENT_PAYPAL_API_ERROR_CODE', $response["L_ERRORCODE".$i]);
			$message = isset($response["L_LONGMESSAGE".$i]) ? $response["L_LONGMESSAGE".$i]: $response["L_SHORTMESSAGE".$i];
			echo $this->getHtmlRow('VMPAYMENT_PAYPAL_API_ERROR_DESC', $message);
		}

	}
	?>
</table>
<?php if ( $viewData['success']) { ?>
	<br />
	<a class="vm-button-correct" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=orders&layout=details&order_number='.$viewData["order"]['details']['BT']->order_number.'&order_pass='.$viewData["order"]['details']['BT']->order_pass, false)?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_VIEW_ORDER'); ?></a>
<?php } ?>
