<?php
require_once(dirname(__FILE__).'/../qhq.php');
require_once(dirname(__FILE__).'/../Models/project.php');
require_once(dirname(__FILE__).'/../Models/reply.php');
class project_controller extends controller{
	public function process_request($uri, $method, $data = null){
		$extra_data = $this->extract_data_from_uri($uri);
		switch ($method) {
			case 'GET':
				$constraint = "reference_id = '".$extra_data['reference_id']."'";
				$projects = project::query($constraint);
				if (!$projects) {
					//echo("Fail to query, error: ".mysql_error());
				}
				if (!isset($projects) || empty($projects)) {
					$this->respond_to_client(400);
				} else {
					$project = $projects[0];
					//echo("access_token is ".$data['access_token']);
					
					$message = array("user_name" => $project->user_name,
									 "time_created" => $project->time_created,
									 "project_description" => $project->project_description);

					$constraint = "project_reference = '".$extra_data['reference_id']."'";
					$reply_array = array();
					$replies = reply::query($constraint);
					if(!empty($replies)){
						foreach ($replies as $reply) {
							$reply = array($reply->replyer, $reply->content, $reply->time_created);
							$reply_array[] = $reply;
						}
					}
					$message['replies'] = $reply_array;
					$this->respond_to_client(200, $message);
					
				}
				break;
			case 'DELETE':
				$constraint = "reference_id = '".$extra_data['reference_id']."'";
				$projects = project::query($constraint);
				if (!$projects) {
					echo("Fail to query, error: ".mysql_error());
				}
				if (!isset($projects) || empty($projects)) {
					$this->respond_to_client(400);
				} else {
					$project = $projects[0];
					project::delete($constraint);
					$this->respond_to_client(200);
				}
				
				break;
			case 'PUT':
				$new_reply = new reply($data['replyer'], $data['content'], $extra_data['reference_id']);
				if (!$new_reply->save()) {
					echo("Fail to save, error: ".mysql_error());
				} else {
					$this->respond_to_client(201);
				}
				break;
			default:
				$this->respond_to_client(404);
				break;
		}
	}
}
?>