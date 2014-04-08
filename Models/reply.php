<?php
require_once(dirname(__FILE__).'/../qhq.php');
class reply extends model{
	public $reply_id;
	public $replyer;
	public $project_reference;
	public $content;
	public $time_created;

	public function __construct(){
		$args = func_get_args();
		$args_count = func_num_args();
		switch ($args_count) {
			case 3:
				$this->replyer = $args[0];
				$this->content = $args[1];
				$this->project_reference = $args[2];
				$this->time_created = null;
				$this->reply_id = null;
				break;
			default:
				# code...
				break;
		}
	}
}
?>