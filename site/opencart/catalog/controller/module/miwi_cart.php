<?php 
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerModuleMiwiCart extends Controller {
	public function index() {
		$this->load->language('module/miwi_cart');
				
    	$data['heading_title'] = $this->language->get('heading_title');
		$data['text_subtotal'] = $this->language->get('text_subtotal');
		$data['text_empty'] = $this->language->get('text_empty');
		$data['text_remove'] = $this->language->get('text_remove');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_cart'] = $this->language->get('text_cart');
		$data['text_checkout'] = $this->language->get('text_checkout');
		$data['button_checkout'] = $this->language->get('button_checkout');
		$data['button_remove'] = $this->language->get('button_remove');
		
		$data['cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
		
		// Get Cart Products
		$data['products'] = array();
		
		foreach ($this->cart->getProducts() as $result) {

			$option_data = array();

			foreach ($result['option'] as $option) {
				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (strlen($option['option_value']) > 20 ? substr($option['option_value'], 0, 20) . '..' : $option['option_value'])
				);
			}
				
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}

			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$total = $this->currency->format($this->tax->calculate($result['total'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$total = false;
			}

            $this->load->model('tool/image');
            $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				
			$data['products'][] = array(
				'key'      => $result['key'],
				'thumb'    => $image,
				'name'     => $result['name'],
				'model'    => $result['model'],
				'option'   => $option_data,
				'recurring'=> ($result['recurring'] ? $result['recurring']['name'] : ''),
				'quantity' => $result['quantity'],
				'stock'    => $result['stock'],
				'price'    => $price,
				'total'    => $total,
				'href'     => $this->url->link('product/product', 'product_id=' . $result['product_id'])
			);
		}
		
		// Gift Voucher
		$data['vouchers'] = array();
		
		if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'])
				);
			}
		}
		
		if (!$this->config->get('config_customer_price')) {
			$data['display_price'] = TRUE;
		} elseif ($this->customer->isLogged()) {
			$data['display_price'] = TRUE;
		} else {
			$data['display_price'] = FALSE;
		}
		
    	// Calculate Totals
		$total_data = array();					
		$total = 0;
		$taxes = $this->cart->getTaxes();
		
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {						 
			$this->load->model('extension/extension');
			
			$sort_order = array(); 
			
			$results = $this->model_extension_extension->getExtensions('total');
			
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
			
			array_multisort($sort_order, SORT_ASC, $results);
			
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);
		
					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
			}
		}
		
		$sort_order = array(); 
	  
		foreach ($total_data as $key => $value) {
      		$sort_order[$key] = $value['sort_order'];
    	}

    	array_multisort($sort_order, SORT_ASC, $total_data);

		$data['totals'] = array();

		foreach ($total_data as $result) {
			$data['totals'][] = array(
				'title' => $result['title'],
				'text'  => $this->currency->format($result['value']),
			);
		}
		
		$data['ajax'] = $this->config->get('cart_ajax');
		
		$this->id = 'miwi_cart';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/miwi_cart.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/module/miwi_cart.tpl', $data);
		} else {
			return $this->load->view('default/template/module/miwi_cart.tpl', $data);
		}
	}
}
?>