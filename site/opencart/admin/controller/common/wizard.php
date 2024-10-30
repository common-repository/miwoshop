<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerCommonWizard extends Controller {
	public function index() {
        $this->language->load('common/wizard');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');

        $data['text_skip'] = $this->language->get('text_skip');
        $data['text_message'] = $this->language->get('text_message');
        $data['text_personal_id'] = $this->language->get('text_personal_id');
        $data['text_logo'] = $this->language->get('text_logo');
        $data['text_name'] = $this->language->get('text_name');
        $data['text_mail'] = $this->language->get('text_mail');
        $data['text_telephone'] = $this->language->get('text_telephone');
        $data['text_address'] = $this->language->get('text_address');
        $data['text_country'] = $this->language->get('text_country');
        $data['text_zone'] = $this->language->get('text_zone');
        $data['text_currency'] = $this->language->get('text_currency');
        $data['text_step_1'] = $this->language->get('text_step_1');
        $data['text_step_2'] = $this->language->get('text_step_2');
        $data['text_step_3'] = $this->language->get('text_step_3');
        $data['text_step_4'] = $this->language->get('text_step_4');
        $data['text_step_5'] = $this->language->get('text_step_5');
        $data['text_step_6'] = $this->language->get('text_step_6');
        $data['text_step_7'] = $this->language->get('text_step_7');
        $data['text_clear'] = $this->language->get('text_clear');
        $data['text_image_manager'] = $this->language->get('text_image_manager');
       	$data['text_browse'] = $this->language->get('text_browse');
       	$data['text_select'] = $this->language->get('text_select');
       	$data['text_none'] = $this->language->get('text_none');
       	$data['text_save_and_new_product'] = $this->language->get('text_save_and_new_product');
       	$data['text_save_and_migration'] = $this->language->get('text_save_and_migration');
       	$data['text_grid'] = $this->language->get('text_grid');
       	$data['text_list'] = $this->language->get('text_list');
       	$data['text_yes'] = $this->language->get('text_yes');
       	$data['text_no'] = $this->language->get('text_no');
        $data['text_product_display'] = $this->language->get('text_product_display');
        $data['text_add_home_menu'] = $this->language->get('text_add_home_menu');
        $data['text_migration'] = $this->language->get('text_migration');
        $data['text_language_info'] = $this->language->get('text_language_info');

        $this->load->model('tool/image');
        $data['logo'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['no_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder'] = $this->model_tool_image->resize('placeholder.png', 100, 100);
        $data['thumb'] = $this->model_tool_image->resize('placeholder.png', 100, 100);
        $data['image'] = "";

        $this->load->model('localisation/country');
        $data['countries'] = $this->model_localisation_country->getCountries();
        $data['country_id'] = $this->config->get('config_country_id');
        $data['zone_id'] = $this->config->get('config_zone_id');

        $this->load->model('localisation/currency');
      	$data['currencies'] = $this->model_localisation_currency->getCurrencies();
        $data['config_currency'] = $this->config->get('config_currency');

        $data['lang'] = $this->_checkLang();
        $data['mig_coms'] = $this->_checkMigrateComponents();

        # links
        $data['link_home'] =  $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL');
        $data['link_skip'] =  $this->url->link('common/wizard/skip', 'token=' . $this->session->data['token'], 'SSL');
        $data['link_new_product'] =  $this->url->link('catalog/product/add', 'token=' . $this->session->data['token'], 'SSL');
        $data['link_action'] =  $this->url->link('common/wizard/save', 'token=' . $this->session->data['token'], 'SSL');
        $data['link'] =  $this->url->link('catalog/review', 'token=' . $this->session->data['token'] . '&sort=r.status&order=ASC', 'SSL');

        $data['header'] = $this->load->controller('common/header');
      	$data['footer'] = $this->load->controller('common/footer');
      	$data['token'] = $this->session->data['token'];

        $this->response->setOutput($this->load->view('common/wizard.tpl', $data));
	}

    public function save(){

        if(!empty($this->request->post['pid'])) {
            $data['pid'] =  $this->request->post['pid'];
        }

        if(isset($this->request->post['miwoshop_display'])) {
            $data['miwoshop_display'] =  $this->request->post['miwoshop_display'];
        }

        if(isset($this->request->post['home_menu'])) {
            $data['home_menu'] =  $this->request->post['home_menu'];
        }

        if(!empty($this->request->post['logo'])) {
            $data['config']['config_logo'] =  $this->request->post['logo'];
        }

        if(!empty($this->request->post['name'])) {
            $data['config']['config_name'] =  $this->request->post['name'];
            $data['config']['config_owner'] =  $this->request->post['name'];
            $data['config']['config_title'] =  $this->request->post['name'];
        }

        if(!empty($this->request->post['mail'])) {
            $data['config']['config_email'] =  $this->request->post['mail'];
        }

        if(!empty($this->request->post['telephone'])) {
            $data['config']['config_telephone'] =  $this->request->post['telephone'];
        }

        if(!empty($this->request->post['address'])) {
            $data['config']['config_address'] =  $this->request->post['address'];
        }

        if(!empty($this->request->post['country_id'])) {
            $data['config']['config_country_id'] =  $this->request->post['country_id'];
        }

        if(!empty($this->request->post['zone_id'])) {
            $data['config']['config_zone_id'] =  $this->request->post['zone_id'];
        }

        if(!empty($this->request->post['currency'])) {
            $data['config']['config_currency'] =  $this->request->post['currency'];
        }

        if(!empty($this->request->post['currency'])) {
            $data['config']['config_currency'] =  $this->request->post['currency'];
        }

        $this->load->model('common/wizard');
        $result = $this->model_common_wizard->save($data);

        if(!empty($this->request->post['redirect'])) {
            $this->response->redirect($this->request->post['redirect']);
        }

        echo json_encode($result);
        exit;
    }

    public function skip() {
        MiwoShop::get('base')->setConfig('wizard', 1);
        $this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
    }

    private function  _checkMigrateComponents() {
        return array();
        $components = array ('com_virtuemart', 'com_hikashop', 'com_redshop', 'com_tienda', 'com_joomshopping', 'com_rokquickcart', 'com_eshop', 'com_aceshop', 'com_joocart', 'com_ayelshop');
        $installed  = array();

        $this->load->model('common/wizard');
        $_installed = $this->model_common_wizard->getInstalledComponents($components);

        if(empty($_installed)) {
            return $installed;
        }

        foreach($_installed as $tmp) {
            $_tmp = str_replace('com_', '', $tmp['element']);
            $com['name'] = ucfirst($_tmp);
            $com['link'] = $this->url->link('tool/'.$_tmp, 'token=' . $this->session->data['token'], 'SSL');;

            $installed[] = $com;
        }

        return $installed;
    }

    private function _checkLang(){
        return false;
        $this->load->model('common/wizard');
        $lang_count = $this->model_common_wizard->getLanguageCount();

        if($lang_count > 1) {
            return true;
        }

        $def_lang = MFactory::getLanguage()->getDefault();

        if($def_lang != 'en-GB') {
            return true;
        }

        return false;

    }

}
