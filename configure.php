<?php
//Mandatory fields.
$app_type = 'CApp';
$routes = array('bad_request_controller>>bad_request_controller',
			 	'>>main_controller');


//Possible fields
$database = array('hostname' => '127.0.0.1',
			 	  'username' => 'root',
			 	  'password' => '',
			 	  'db_name' => '');

$db_creation_language = array();


return array('app_type' => $app_type,
			 'routes' => $routes,
			 'database' => isset($database) ? $database : null,
			 'db_creation_language' => isset($db_creation_language) ? $db_creation_language : null,
			 );

?>