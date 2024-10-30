<?php
class ControllerPaymentAuthorizenetAim extends Controller {
	private $error = array();

	public function index() {
		return true;
	}

	protected function validate() {
		return true;
	}
}