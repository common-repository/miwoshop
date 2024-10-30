<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerPaymentEasysocialpoints extends Controller {

	public function index() {
		$this->load->language('payment/easysocialpoints');
				
		$order_info 		 = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$data['total'] = $order_info['total'];
        $user_id        	 = MiwoShop::get('user')->getJUserIdFromOCustomer($order_info['customer_id'], $order_info['email']);
		
		$data['text_instruction'] 								= $this->language->get('text_instruction');
    	$data['text_payable'] 									= $this->language->get('text_payable');
		$data['text_address'] 									= $this->language->get('text_address');
		$data['text_payment'] 									= $this->language->get('text_payment');
		$data['easysocialpoints'] 								= $this->language->get('easysocialpoints');
		$data['easysocialpoints_submit'] 						= $this->language->get('easysocialpoints_submit');
		$data['easysocialpoints_conversation_label'] 			= $this->language->get('easysocialpoints_conversation_label');
		$data['easysocialpoints_conversation_desc'] 			= $this->language->get('easysocialpoints_conversation_desc');
		$data['easysocialpoints_conversion_rate_message'] 		= $this->language->get('easysocialpoints_conversion_rate_message');
		$data['easysocialpoints_total_points_needed_message'] 	= $this->language->get('easysocialpoints_total_points_needed_message');
		$data['easysocialpoints_current_points_situation'] 		= $this->language->get('easysocialpoints_current_points_situation');
		$data['easysocialpoints_not_enough_points_message'] 	= $this->language->get('easysocialpoints_not_enough_points_message');
		$data['easysocialpoints_total_points_deducted_message'] = $this->language->get('easysocialpoints_total_points_deducted_message');
		$data['easysocialpoints_msg_not_enough_points'] 		= $this->language->get('easysocialpoints_msg_not_enough_points');
		
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['order_id'] 		= $this->session->data['order_id'];
		$data['user_id'] 		= $user_id;
		$data['conversion']   	= $this->config->get('easysocialpoints_points');
		
        $this->load->model('payment/easysocialpoints');
		$data['user_points']  	= $this->model_payment_easysocialpoints->getESPoints($user_id);
		$data['continue'] 	  	= $this->url->link('checkout/success');
				
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/easysocialpoints.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/easysocialpoints.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/easysocialpoints.tpl', $data);
		} 
	}
	
	public function confirm() {
		$this->load->language('payment/easysocialpoints');
		$this->load->model('checkout/order');
		$this->load->model('payment/easysocialpoints');

		// Do the payment
		$user_points 		= $this->model_payment_easysocialpoints->getESPoints($this->request->post['user_id']);
		$points_charge 		= $this->request->post['total'] * $this->config->get('easysocialpoints_points');

        $result = 0;

		if ($points_charge <= $user_points)	{
			$result = $this->model_payment_easysocialpoints->updateESPoints(-$points_charge, $this->request->post['user_id']);
		}

        if($result) {
            $order_status_id = $this->config->get('easysocialpoints_order_status_id');
            $comment = '';
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $order_status_id, $comment, true);
        }
        else {
			$this->response->redirect($this->url->link('common/home'));
		}

	}
}
?>