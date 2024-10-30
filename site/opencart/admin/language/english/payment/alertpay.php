<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');

// Heading
$_['heading_title']      	= 'AlertPay';

$_['text_edit']            	= 'Edit AlertPay';
$_['text_alertpay']	= '<img src="view/image/payment/alertpay.png" alt="AlertPay" title="AlertPay" style="border: 1px solid #EEEEEE;" />';

// Text 
$_['text_payment']       	= 'Payment';
$_['text_success']       	= 'Success: You have modified AlertPay account details!';
      
// Entry
$_['entry_merchant']     	= 'Merchant ID:';
$_['entry_security']     	= 'Security Code:';
$_['entry_callback']     	= '<span data-toggle="tooltip" title="This has to be set in the AlertPay control panel. You will also need to check the <b>IPN Status</b> to enabled." data-original-title="">Alert URL:</span>';
$_['entry_total']        	= '<span data-toggle="tooltip" title="The checkout total the order must reach before this payment method becomes active." data-original-title="">Total:</span>';
$_['entry_order_status'] 	= 'Order Status:';
$_['entry_geo_zone']     	= 'Geo Zone:';
$_['entry_status']       	= 'Status:';
$_['entry_sort_order']   	= 'Sort Order:';

$_['entry_merchant_id']		= 'Merchant ID';
$_['entry_security_key']	= 'Security Code';
$_['entry_total_key']		= 'Total';

// Error
$_['error_permission']   	= 'Warning: You do not have permission to modify payment AlertPay!';
$_['error_merchant']     	= 'Merchant ID Required!';
$_['error_security']     	= 'Security Code Required!';
?>