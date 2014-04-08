<?php
require_once(dirname(__FILE__).'/../qhq.php');
class admin extends model{
	public $admin_id;
	public $email;
	public $password;
	public $access_token;
	public $name;
	public $time_created;

	public function __construct(){
		$args = func_get_args();
		$args_count = func_num_args();
		switch ($args_count) {
			case 4:
				$this->email = $args[0];
				$this->password = $args[1];
				$this->name = $args[2];
				$this->access_token = $args[3];
				$this->admin_id = null;
				$this->time_created = null;
				break;
			default:
				# code...
				break;
		}
	}
}
?>