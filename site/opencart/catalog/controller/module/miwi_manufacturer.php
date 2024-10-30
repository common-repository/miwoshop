<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerModuleMiwiManufacturer extends Controller {
    public function index() {
        $this->load->language('module/miwi_manufacturer');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_select'] = $this->language->get('text_select');

        if (isset($this->request->get['miwi_manufacturer_id'])) {
            $data['miwi_manufacturer_id'] = $this->request->get['miwi_manufacturer_id'];
        } else {
            $data['miwi_manufacturer_id'] = 0;
        }

        $this->load->model('catalog/manufacturer');
				
        $data['miwi_manufacturers'] = array();

        $results = $this->model_catalog_manufacturer->getManufacturers();

        foreach ($results as $result) {
						
			$data['manufacturers'][] = array(
                'manufacturer_id' => $result['manufacturer_id'],
                'name'       	  => $result['name'],
				'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
            );
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/miwi_manufacturer.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/module/miwi_manufacturer.tpl', $data);
        } else {
            return $this->load->view('default/template/module/miwi_manufacturer.tpl', $data);
        }
    }
}
?>