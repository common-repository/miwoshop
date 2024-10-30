<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerPaymentJomsocialpoints extends Controller {

	public function index() {
		$this->load->language('payment/jomsocialpoints');
				
		$order_info 	= $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$data['total'] 	= $order_info['total'];
		$user_id        = MiwoShop::get('user')->getJUserIdFromOCustomer($order_info['customer_id'], $order_info['email']);
		
		#language
		$data['text_instruction'] 								= $this->language->get('text_instruction');
    	$data['text_payable'] 									= $this->language->get('text_payable');
		$data['text_address'] 									= $this->language->get('text_address');
		$data['text_payment'] 									= $this->language->get('text_payment');
		$data['jomsocialpoints'] 								= $this->language->get('jomsocialpoints');
		$data['jomsocialpoints_submit'] 						= $this->language->get('jomsocialpoints_submit');
		$data['jomsocialpoints_conversation_label'] 			= $this->language->get('jomsocialpoints_conversation_label');
		$data['jomsocialpoints_conversation_desc'] 				= $this->language->get('jomsocialpoints_conversation_desc');
		$data['jomsocialpoints_conversion_rate_message'] 		= $this->language->get('jomsocialpoints_conversion_rate_message');
		$data['jomsocialpoints_total_points_needed_message'] 	= $this->language->get('jomsocialpoints_total_points_needed_message');
		$data['jomsocialpoints_current_points_situation'] 		= $this->language->get('jomsocialpoints_current_points_situation');
		$data['jomsocialpoints_not_enough_points_message'] 		= $this->language->get('jomsocialpoints_not_enough_points_message');
		$data['jomsocialpoints_total_points_deducted_message'] 	= $this->language->get('jomsocialpoints_total_points_deducted_message');
		$data['jomsocialpoints_msg_not_enough_points'] 			= $this->language->get('jomsocialpoints_msg_not_enough_points');
		
		#variable
		$data['button_confirm'] = $this->language->get('button_confirm');	
		$data['order_id'] 		= $this->session->data['order_id'];
		$data['user_id'] 		= $user_id;
		$data['conversion'] 	= $this->config->get('jomsocialpoints_points');
		$this->load->model('payment/jomsocialpoints');
		$data['user_points'] 	= $this->model_payment_jomsocialpoints->getJSPoints($user_id);
		$data['continue'] 		= $this->url->link('checkout/success');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/jomsocialpoints.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/jomsocialpoints.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/jomsocialpoints.tpl', $data);
		} 
		
	}
	
	public function confirm() {
		$this->load->language('payment/jomsocialpoints');
		$this->load->model('checkout/order');		
		$this->load->model('payment/jomsocialpoints');

		$user_points 	= $this->model_payment_jomsocialpoints->getJSPoints($this->request->post['user_id']);
		$points_charge  = $this->request->post['total'] * $this->config->get('jomsocialpoints_points');

		$result = 0;
		
		if ($points_charge <= $user_points)	{
			$points = $user_points - $points_charge;
			$result = $this->model_payment_jomsocialpoints->updateJSPoints($points, $this->request->post['user_id']);			
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