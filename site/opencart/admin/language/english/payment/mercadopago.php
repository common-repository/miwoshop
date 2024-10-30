<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
* @author 		hcasatti
*/

// No Permission
defined('MIWI') or die('Restricted access');

// Heading
$_['heading_title']	= 'Mercado Pago Version v2.0';

// Text
$_['text_ipn'] = 'Configure your <b>Instant Payment Motification</b> to receive your automatic order status changes at: 
           <a href="https://www.mercadopago.com/mla/herramientas/notificaciones" target="_blank">Arg</a> or
           <a href="https://www.mercadopago.com/mlm/herramientas/notificaciones" target="_blank">Mex</a> or
           <a href="https://www.mercadopago.com/mlv/herramientas/notificaciones" target="_blank">Ven</a> or
           <a href="https://www.mercadopago.com/mlb/ferramentas/notificacoes" target="_blank">Bra</a><br />
           Set your url follwing this exemple: http//www.your_store_address_root.com/index.php?route=payment/mercadopago2/retorno/&';
$_['text_edit']							= 'Edit Mercado Pago';
$_['text_payment']						= 'Payment';
$_['text_success']						= 'Sucess, your modifications are done!';
$_['text_mercadopago']					= '<a onclick="window.open(\'https://www.mercadopago.com\');" target="_blank"><img src="view/image/payment/mercadopago.png" alt="Mercadopago" title="Mercadopago" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_argentina']					= 'Argentina';
$_['text_brasil']						= 'Brasil';
$_['text_colombia']						= 'Colombia';
$_['text_chile']						= 'Chile';

// Entry
$_['entry_client_id_text']				= 'Cliente ID';
$_['entry_client_id']					= 'Cliente ID :  <br />To get this fild, follow:<a href="https://www.mercadopago.com/mla/herramientas/aplicaciones" target="_blank">Arg</a> or <a href="https://www.mercadopago.com/mlm/herramientas/aplicaciones" target="_blank">Mex</a> or
              <a href="https://www.mercadopago.com/mlv/herramientas/aplicaciones" target="_blank">Ven</a> or <a href="https://www.mercadopago.com/mlb/ferramentas/aplicacoes" target="_blank">Bra</a>';
$_['entry_client_secret_text']			= 'Client Secret';
$_['entry_client_secret']				= 'Client Secret : <br />To get this fild, follow:<a href="https://www.mercadopago.com/mla/herramientas/aplicaciones" target="_blank">Arg</a> or <a href="https://www.mercadopago.com/mlm/herramientas/aplicaciones" target="_blank">Mex</a> or
             <a href="https://www.mercadopago.com/mlv/herramientas/aplicaciones" target="_blank">Ven</a> or <a href="https://www.mercadopago.com/mlb/ferramentas/aplicacoes" target="_blank">Bra</a>';
$_['entry_installments']				= 'Maximum accepted payments';
$_['entry_geo_zone']					= 'Geo Zona:';
$_['entry_status']						= 'Status:';
$_['entry_country']						= 'Sales Country:';
$_['entry_sort_order']					= 'Sort order:';

$_['entry_payments_not_accept']			= '<span data-toggle="tooltip" title="<b>Important</b> If you change the Sales Country, save the page and only after that select the methods that you dont´t want to accept." data-original-title="">Check the payments methods that you <b>don´t want</b> to accept:</span>';
$_['entry_url']                         = '<span data-toggle="tooltip" title="Insert your store root url installation<br /> (Always write the url with <b>http://</b> or <b>https://</b> )<br/><i>IE. http://www.mystore.com/store/</i><br />" data-original-title="">Store Url:</span>';
$_['entry_url_text']                    = 'Store Url';
$_['entry_debug']                       = '<span data-toggle="tooltip" title="Turn on to show the erro log on checkout" data-original-title="">Debug mode:</span>';
$_['entry_sandbox']                     = '<span data-toggle="tooltip" title="Sandbox is used for testing the Checkout and IPN. Without the need for a valid credit card to approve to purchase test." data-original-title="">Sandbox mode:</span>';
$_['entry_type_checkout']               = 'Type Checkout:';
$_['entry_category']                    = '<span data-toggle="tooltip" title="Select the category that best fits your shop" data-original-title="">Category:</span>';
$_['entry_order_status']				= '<span data-toggle="tooltip" title="Select the default order status of your orders" data-original-title="">Order status:</span>';
$_['entry_order_status_completed']		= '<span data-toggle="tooltip" title="Select the status order case your order is <b>Approved</b>" data-original-title="">Order Completed:</span>';
$_['entry_order_status_pending']		= '<span data-toggle="tooltip" title="Select the status order when the buyer did not finish the payment yet" data-original-title="">Order Pending:</span>';
$_['entry_order_status_canceled']		= '<span data-toggle="tooltip" title="Select the status order case the payment was <b>Cancelled</b>" data-original-title="">Order Canceled:</span>';
$_['entry_order_status_in_process']		= '<span data-toggle="tooltip" title="Select the status order case the payment is <b>been analysing</b>" data-original-title="">Order In Progress:</span>';
$_['entry_order_status_rejected']		= '<span data-toggle="tooltip" title="Select the status order case the payment was <b>reject</b>" data-original-title="">Order Reject:</span>';
$_['entry_order_status_refunded']		= '<span data-toggle="tooltip" title="Select the status order case the payment was <b>Refunded</b>" data-original-title="">Order Refunded:</span>';
$_['entry_order_status_in_mediation']	= '<span data-toggle="tooltip" title="Select the status order case the payment is in <b>Mediation</b>" data-original-title="">Order Mediation:</span>';

// Error
$_['error_permission']	    = 'Sorry, you no permissionto to modify Mercado Pago v2.0';
$_['error_client_id']	    = 'Sorry, your <b>Client Id</b> is mandatory.';
$_['error_client_secret']	= 'Sorry <b>Client Secret</b> is mandatory.';
$_['error_mercadopago_url']	= 'Sorry <b>Store Url</b> is mandatory.';

//Tabs
$_['tab_general']		= 'General';
$_['tab_order_status']	= 'Order Status';

// installments
$_['18'] = '18';
$_['15'] = '15';
$_['12'] = '12';
$_['9']  = '9';
$_['6']  = '6';
$_['3']  = '3';
$_['1']  = '1';
?>