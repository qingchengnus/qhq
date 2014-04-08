<?php
require_once(dirname(__FILE__).'/../qhq.php');
class project extends model{
	public $reference_id;
	public $user_name;
	public $project_description;
	public $time_created;

	public function __construct(){
		$args = func_get_args();
		$args_count = func_num_args();
		switch ($args_count) {
			case 3:
				$this->reference_id = $args[0];
				$this->user_name = $args[1];
				$this->project_description = $args[2];
				$this->time_created = null;
				break;
			default:
				# code...
				break;
		}
	}
}
?>