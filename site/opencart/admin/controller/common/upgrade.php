<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerCommonUpgrade extends Controller {

    public function index() {
        $this->language->load('common/upgrade');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_auto'] = $this->language->get('text_auto');
        $data['text_manual'] = $this->language->get('text_manual');
        $data['text_upload_upgrade'] = $this->language->get('text_upload_upgrade');
        $data['text_upload_pkg'] = $this->language->get('text_upload_pkg');
        $data['text_error'] = '';
        $data['text_success'] = '';

        if (!$this->validate('access')) {
            exit();
        }

        if (isset($this->session->data['msg']) and ($this->session->data['msg'] !== $this->language->get('text_success'))) {
            $data['text_error'] = $this->session->data['msg'];
            unset($this->session->data['msg']);
        } else if(isset($this->session->data['msg'])) {
            $data['text_success'] = $this->session->data['msg'];;
            unset($this->session->data['msg']);
        }

		$data['text_auto_btn'] = $this->language->get('text_auto_btn');
		$data['error_personal_id'] = $this->language->get('error_personal_id');

        $data['token'] = $this->session->data['token'];

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('common/upgrade', 'token=' . $this->session->data['token'] , 'SSL'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('common/upgrade/upgrade', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
  
        $this->response->setOutput($this->load->view('common/upgrade.tpl', $data));
    }
	
    public function upgrade() {
        $this->language->load('common/upgrade');

        if (!$this->validate('modify')) {
            exit();
        }

        $this->load->model('common/upgrade');

        // Upgrade
        if (!$this->model_common_upgrade->upgrade()) {
            $this->session->data['msg'] = $this->language->get('text_error');
        }
        else {
            $this->session->data['msg'] = $this->language->get('text_success');
        }

        // Return
        $this->response->redirect($this->url->link('common/upgrade', 'token=' . $this->session->data['token'] , 'SSL'));
    }

    protected function validate($type) {
        if (!$this->user->hasPermission($type, 'common/upgrade')) {
            $error['warning'] = $this->language->get('error_permission');
            echo json_encode($error);
        }

        if (empty($error['warning'])) {
            return true;
        } else {
            return false;
        }
    }
}
?>