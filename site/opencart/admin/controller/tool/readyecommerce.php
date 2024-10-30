<?php 
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, www.miwisoft.com
* @license		http://www.miwisoft.com/company/license
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerToolReadyecommerce extends Controller {

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
       		'text'      => 'Ready! Ecommerce',
			'href'      => $this->url->link('tool/readyecommerce', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/readyecommerce.tpl', $data));
  	} 
  	
  	public function importCategories(){
		if (!$this->validate()) {
			return $this->forward('error/permission');
		}

		$this->load->model('tool/readyecommerce');
		
		$this->model_tool_readyecommerce->importCategories($this->request->post['readyecommerce']);
	}
  	
  	public function importProducts() {
		if (!$this->validate()) {
			return $this->forward('error/permission');
		}

		$this->load->model('tool/readyecommerce');
		
		$this->model_tool_readyecommerce->importProducts($this->request->post['readyecommerce']);
	}

  	public function importManufacturers() {
		if (!$this->validate()) {
			return $this->forward('error/permission');
		}

		$this->load->model('tool/readyecommerce');

		$this->model_tool_readyecommerce->importManufacturers($this->request->post['readyecommerce']);
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'tool/readyecommerce')) {
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