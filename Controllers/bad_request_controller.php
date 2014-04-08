<?php
require_once(dirname(__FILE__).'/../qhq.php');

class bad_request_controller extends controller{
	public function process_request($uri, $method, $data = null){
		$this->respond_to_client(404);
	}
}
?>