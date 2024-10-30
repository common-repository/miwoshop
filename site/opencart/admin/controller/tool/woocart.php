<?php 
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, www.miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerToolWoocart extends Controller {

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
       		'text'      => 'WooCart',
			'href'      => $this->url->link('tool/woocart', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/woocart.tpl', $data));
  	} 
  	
  	public function migrateDatabase(){
		$this->load->model('tool/woocart');
        $post = $this->request->post['opencart'];
        $post['component'] = 'opencart';
		
		$this->model_tool_woocart->migrateDatabase($post);
	}
  	
  	public function migrateFiles() {
		$this->load->model('tool/woocart');
        $post = $this->request->post['woocart'];
        $post['component'] = 'woocart';
		
		$this->model_tool_woocart->migrateFiles($post);
	}
	
	public function fixMenus() {
		$this->load->model('tool/woocart');
        $post = $this->request->post['woocart'];
        $post['component'] = 'woocart';
		
		$this->model_tool_woocart->fixMenus($post);
	}
	
	public function fixModules() {
		$this->load->model('tool/woocart');
        $post = $this->request->post['woocart'];
        $post['component'] = 'woocart';
		
		$this->model_tool_woocart->fixModules($post);
	}
}