<?php
//Mandatory fields.
$app_type = 'CApp';
$routes = array('bad_request_controller>>bad_request_controller',
			 	'>>main_controller',
			 	'/admins>>admins_controller',
			 	'/projects>>projects_controller',
			 	'/admins/:admin_id>>admin_controller',
			 	'/projects/:reference_id>>project_controller');


//Possible fields
$database = array('hostname' => '127.0.0.1',
			 	  'username' => 'root',
			 	  'password' => '',
			 	  'db_name' => 'FOOYOSTUDIO');

$db_creation_language = array();
$new_table_query = "CREATE TABLE IF NOT EXISTS `admin` (
        `admin_id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(64) NOT NULL UNIQUE,
        `password` varchar(64) NOT NULL,
        `name` varchar(64) NOT NULL,
        `access_token` varchar(128) DEFAULT NULL,
        `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`admin_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1" ;
$db_creation_language[] = $new_table_query;

$new_table_query = "CREATE TABLE IF NOT EXISTS `project` (
        `reference_id` varchar(32) NOT NULL,
        `user_name` varchar(64) NOT NULL,
        `project_description` varchar(4096) NOT NULL,
        `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`reference_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
$db_creation_language[] = $new_table_query;

$new_table_query = "CREATE TABLE IF NOT EXISTS `reply` (
        `reply_id` int(11) NOT NULL AUTO_INCREMENT,
        `replyer` varchar(64) NOT NULL,
        `content` varchar(1024) NOT NULL,
        `project_reference` varchar(32) NOT NULL,
        `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`reply_id`),
        FOREIGN KEY (`project_reference`) REFERENCES project (`reference_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
$db_creation_language[] = $new_table_query;

return array('app_type' => $app_type,
			 'routes' => $routes,
			 'database' => isset($database) ? $database : null,
			 'db_creation_language' => isset($db_creation_language) ? $db_creation_language : null,
			 );

?>