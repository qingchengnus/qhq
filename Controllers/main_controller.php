<?php
require_once(dirname(__FILE__).'/../qhq.php');
class main_controller extends controller{
	public function process_request($uri, $method, $data = null){
		$message = array('title' => 'Welcome to QHQ!', 
						 'content' => 'Welcome to QHQ!');
		$main_view = new view($message);
		echo($main_view->render());
	}
}
?>