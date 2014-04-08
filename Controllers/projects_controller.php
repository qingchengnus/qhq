<?php
require_once(dirname(__FILE__).'/../qhq.php');
require_once(dirname(__FILE__).'/../Models/project.php');
class projects_controller extends controller{
	public function process_request($uri, $method, $data = null){
		switch ($method) {
			case 'POST':
				$new_project = new project($data['reference_id'], $data['user_name'], $data['project_description']);
				if (!$new_project->save()) {
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