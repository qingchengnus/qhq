<?php
require_once(dirname(__FILE__).'/../qhq.php');
require_once(dirname(__FILE__).'/../Models/admin.php');
class admins_controller extends controller{
	public function process_request($uri, $method, $data = null){
		switch ($method) {
			case 'POST':
				if(strcmp($data['credential'], "1352463570") != 0){
					$this->respond_to_client(401);
				} else {
					$access_token = $this->generate_access_token($data['email'], $data['password']);
					$new_admin = new admin($data['email'], $data['password'], $data['name'], $access_token);
					if (!$new_admin->save()) {
						echo("Fail to save, error: ".mysql_error());
					} else {
						$message = array("access_token" => $access_token);
						$this->respond_to_client(201, $message);
					}
				}
				break;
			case 'DELETE':
				$constraint = "email = '".$data['email']."'";
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
			case 'PUT':
				$constraint = "email = '".$data['email']."'";
				$admins = admin::query($constraint);
				if (!$admins) {
					echo("Fail to query, error: ".mysql_error());
				}
				if (!isset($admins) || empty($admins)) {
					$this->respond_to_client(400);
				} else {
					$admin = $admins[0];
					//echo("access_token is ".$data['access_token']);
					if (strcmp($admin->password, $data['password']) == 0) {
						$message = array("access_token" => $admin->access_token,
										 "admin_id" => $admin->admin_id);
						$this->respond_to_client(200, $message);
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