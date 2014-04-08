<?php
require_once('qhq.php');
$configuration_file = dirname(__FILE__).'/configure.php';
$requestURI = explode('/', $_SERVER['REQUEST_URI']);

// Change this value to 2 after deploy.
// Sample API call: qivi.com/api/user
$addr_offset = 2;
$commands = array_values($requestURI);

for ($i=0; $i < $addr_offset; $i++) { 
	unset($commands[$i]);
}
$commands = array_values($commands);
for ($i=0; $i < sizeof($commands); $i++) { 
	if ($commands[$i] == "") {
		unset($commands[$i]);
	}
	
}

$commands = array_values($commands);
//echo("length of the commands is ".sizeof($commands));

$app = qhq::create_application($configuration_file);
$method = $_SERVER['REQUEST_METHOD'];
$data = array();
parse_str(file_get_contents("php://input"),$data);

$app->process_request($commands, $method, $data);