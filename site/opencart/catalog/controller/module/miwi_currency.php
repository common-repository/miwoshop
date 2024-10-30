<?php 
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerModuleMiwiCurrency extends Controller {
	public function index() {
		$data['output'] = $this->load->controller('common/currency');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/miwi_currency.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/module/miwi_currency.tpl', $data);
		} else {
			return $this->load->view('default/template/module/miwi_currency.tpl', $data);
		}
	}
}