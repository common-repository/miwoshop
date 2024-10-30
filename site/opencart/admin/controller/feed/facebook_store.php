<?php
class ControllerFeedFacebookStore extends Controller {
	private $error = array(); 
	
	public function index() {
		return true;
  	}

    public function getProduct() {
        $json = array();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

	protected function validate() {
		return true;
  	}

    protected function getModules() {
        $results = array();

        return $results;
    }

    protected function getFeeds($data) {
        $result = array();

        return $result;
    }
}