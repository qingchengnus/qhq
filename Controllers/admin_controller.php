<?php
require_once(dirname(__FILE__).'/../qhq.php');
require_once(dirname(__FILE__).'/../Models/admin.php');
class admin_controller extends controller{
	public function process_request($uri, $method, $data = null){
		$extra_data = $this->extract_data_from_uri($uri);
		//echo("admin id is ".$extra_data['admin_id']);
		//echo("my uri is ".$this->uri);
		switch ($method) {
			case 'GET':
				$constraint = "admin_id = '".$extra_data['admin_id']."'";
				$admins = admin::query($constraint);
				if (!$admins) {
					//echo("Fail to query, error: ".mysql_error());
				}
				if (!isset($admins) || empty($admins)) {
					$this->respond_to_client(400);
				} else {
					$admin = $admins[0];
					//echo("access_token is ".$data['access_token']);
					
					$message = array("access_token" => $admin->access_token,
									 "admin_id" => $admin->admin_id,
									 "name" => $admin->name,
									 "time_created" => $admin->time_created,
									 "admin_id" => $admin->admin_id);
					$this->respond_to_client(200, $message);
					
				}
				break;
			case 'DELETE':
				$constraint = "admin_id = '".$extra_data['admin_id']."'";
				$admins = admin::query($constraint);
				if (!$admins) {
					echo("Fail to query, error: ".mysql_error());
				}
				if (!isset($admins) || empty($admins)) {
					$this->respond_to_client(400);
				} else {
					$admin = $admins[0];
					//echo("access_token is ".$data['access_token']);
					if (strcmp($admin->access_token, $data['access_token']) == 0) {
						if(!admin::delete($constraint)){
							echo("Fail to delete, error: ".mysql_error());
						} else {
							$this->respond_to_client(200);
						}
					} else {
						$this->respond_to_client(401);
					}
				}
				
				break;
			default:
				$this->respond_to_client(404);
				break;
		}
	}
	private function generate_access_token($email, $password){
		$currentDateTime = date('Y/m/d H:i:s');
		return sha1($email.$password.$currentDateTime);
	}
}
?>