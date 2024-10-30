<?php 
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, www.miwisoft.com
* @license		http://www.miwisoft.com/company/license
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerToolWpecommerce extends Controller {

	private $error = array(); 
    
  	public function index() {
		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),     		
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => 'MiwoShop Migration Tools',
			'href'      => '',
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => 'WP e-Commerce',
			'href'      => $this->url->link('tool/wpecommerce', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/wpecommerce.tpl', $data));
  	} 
  	
  	public function importCategories(){
		if (!$this->validate()) {
			return $this->forward('error/permission');
		}

		$this->load->model('tool/wpecommerce');
		
		$this->model_tool_wpecommerce->importCategories($this->request->post['wpecommerce']);
	}
  	
  	public function importProducts() {
		if (!$this->validate()) {
			return $this->forward('error/permission');
		}

		$this->load->model('tool/wpecommerce');
		
		$this->model_tool_wpecommerce->importProducts($this->request->post['wpecommerce']);
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'tool/wpecommerce')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		}
		else {
			return false;
		}		
	}
}