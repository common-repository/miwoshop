<?php 
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerModuleMiwiMinicart extends Controller {
	public function index() {
		$data['output'] = $this->load->controller('common/cart');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/miwi_minicart.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/module/miwi_minicart.tpl', $data);
		} else {
			return $this->load->view('default/template/module/miwi_minicart.tpl', $data);
		}
	}
}
?>