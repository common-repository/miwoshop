<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
* @author 		hcasatti
*/

// No Permission
defined('MIWI') or die('Restricted access');

class ControllerPaymentmercadopago extends Controller {
	private $error = array();

  	public function index() {
		return true;
  	}

  	private function validate() {
		return true;
  	}
}