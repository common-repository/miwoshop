<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerPaymentAlphauserpoints extends Controller {

	public function index() {
		$this->load->language('payment/alphauserpoints');
				
		$order_info 	= $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$data['total'] 	= $order_info['total'];
		$user_id        = MiwoShop::get('user')->getJUserIdFromOCustomer($order_info['customer_id'], $order_info['email']);
		
		#language
		$data['text_instruction'] 								= $this->language->get('text_instruction');
    	$data['text_payable'] 									= $this->language->get('text_payable');
		$data['text_address'] 									= $this->language->get('text_address');
		$data['text_payment'] 									= $this->language->get('text_payment');
		$data['alphauserpoints'] 								= $this->language->get('alphauserpoints');
		$data['alphauserpoints_submit'] 						= $this->language->get('alphauserpoints_submit');
		$data['alphauserpoints_conversation_label'] 			= $this->language->get('alphauserpoints_conversation_label');
		$data['alphauserpoints_conversation_desc'] 				= $this->language->get('alphauserpoints_conversation_desc');
		$data['alphauserpoints_conversion_rate_message'] 		= $this->language->get('alphauserpoints_conversion_rate_message');
		$data['alphauserpoints_total_points_needed_message'] 	= $this->language->get('alphauserpoints_total_points_needed_message');
		$data['alphauserpoints_current_points_situation'] 		= $this->language->get('alphauserpoints_current_points_situation');
		$data['alphauserpoints_not_enough_points_message'] 		= $this->language->get('alphauserpoints_not_enough_points_message');
		$data['alphauserpoints_total_points_deducted_message'] 	= $this->language->get('alphauserpoints_total_points_deducted_message');
		$data['alphauserpoints_msg_not_enough_points'] 			= $this->language->get('alphauserpoints_msg_not_enough_points');
		
		#variable
		$data['button_confirm'] = $this->language->get('button_confirm');	
		$data['order_id'] 		= $this->session->data['order_id'];
		$data['user_id'] 		= $user_id;
		$data['conversion'] 	= $this->config->get('alphauserpoints_points');
		$this->load->model('payment/alphauserpoints');
		$data['user_points'] 	= $this->model_payment_alphauserpoints->getAUPPoints($user_id);
		$data['continue'] 		= $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/alphauserpoints.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/alphauserpoints.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/alphauserpoints.tpl', $data);
		} 
	}
	
	public function confirm() {
		$this->load->language('payment/alphauserpoints');
	
		$this->load->model('checkout/order');	
		
		$this->load->model('payment/alphauserpoints');

		$priceInPoints  = $this->request->post['total'] * $this->config->get('alphauserpoints_points');
		
		$result = 0;

		if ($this->model_payment_alphauserpoints->getAUPPoints($this->request->post['user_id']) >= $priceInPoints)	{
			$description = 'MiwoShop Order #' . $this->session->data['order_id'];
			$this->model_payment_alphauserpoints->bookAUPPoints($this->request->post['user_id'], -$priceInPoints, $description);			
		}		
		
		if($result) {
            $order_status_id = $this->config->get('jomsocialpoints_order_status_id');
            $comment='';
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $order_status_id, $comment, true);
        } else {
            $this->response->redirect($this->url->link('common/home'));
		}

	}
}
?>