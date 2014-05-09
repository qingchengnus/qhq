<?php
/**
 * This is the all in one file for qhq framework, hopefully!
 *
 * @author Rick QING Cheng <qingchengnus@gmail.com>
 *
 * @version 0.00
 *
 */
class model{
	public static function query($constraint){
		$class = get_called_class();
		$vars = get_object_vars(new $class());
		$var_names = array();
		$results = array();
		foreach ($vars as $var_name => $value) {
			$var_names[] = $var_name;
		}
		$query = "SELECT * FROM ". $class ." WHERE ". $constraint;
		if($result = mysql_query($query)){
			while ($row = mysql_fetch_array($result)){
				$current_result = new $class();
				foreach ($row as $key => $value) {
					if(in_array($key, $var_names)){
						$current_result->$key = $value;
					}
				}
				$results[] = $current_result;
			}
			return $results;
		} else {
			return false;
		}
	}
	public static function update($constraint, $update){
		$class = get_called_class();
		$query = "UPDATE " . $class ." SET ". $update ." WHERE ". $constraint;
		return mysql_query($query);
	}
	public static function delete($constraint){
		$class = get_called_class();
		$query = "DELETE FROM " . $class ." WHERE ". $constraint;
		return mysql_query($query);
	}
	public function save(){
		$class = get_called_class();
		$vars = get_object_vars($this);
		$size = sizeof($vars);
		$query = "INSERT INTO " . $class ."(";
		foreach ($vars as $var_name => $value) {
			if(isset($value)){
				$query .= $var_name;
				$query .= ",";
			}
			
		}
		$query = substr($query, 0, -1);
		$query .= ")VALUES(";
		foreach ($vars as $var_name => $value) {
			if (isset($value)) {
				$value = mysql_real_escape_string($value);
				$query .= ("'". $value . "',");
			}
		}
		$query = substr($query, 0, -1);
		$query .= ")";
		return mysql_query($query);
	}

}

class view{
	protected $messages;
	protected $models;
	protected $html;
	public function __construct(){
		$args = func_get_args();
		$args_count = func_num_args();
		switch ($args_count) {
			case 1:
				$this->messages = $args[0];
				break;
			case 2:
				$this->models = $args[1];
			default:
				# code...
				break;
		}
	}

	public function render(){
		$html = "";
		$html .=   "<!DOCTYPE html>
				    <html>
				  		<head>
    			  			<title>
    			  			".$this->messages['title']."
    			  			</title>
    			  		</head>
                  		<body>
                  			".$this->messages['content']."
                  		</body>
                  	</html>";
        return $html;
	}
}

abstract class controller{

	protected $uri;

	public function __construct(){
		$args = func_get_args();
		$args_count = func_num_args();
		switch ($args_count) {
			case 1:
				$this->uri = $args[0];
				break;
			default:
				# code...
				break;
		}
	}

	public function get_uri(){
		return $this->uri;
	}

	protected function respond_to_client($response_code, $response_array = null){
		http_response_code($response_code);
		if(isset($response_array)){
			$response_json = json_encode($response_array);
			echo $response_json;
		} else {
			echo "";
		}
	}

	protected function extract_data_from_uri($requested_uri){
		$uri = $this->uri;
		$uri_pieces = explode('/', $uri);
		$size = sizeof($uri_pieces);
		$requested_uri_pieces = explode('/', $requested_uri);
		$extra_data = array();
		for ($i=0; $i < $size; $i++) { 
			if(strpos($uri_pieces[$i],':') !== false){
				$extra_data[ltrim ($uri_pieces[$i],':')] = $requested_uri_pieces[$i];
			}
		}
		return $extra_data;
	}

	abstract function process_request($uri, $method, $data = null);
}

abstract class qhq_app{

	private static $instance;
	protected $routes;




	public function __construct() {
		$this->routes = array();
		$args = func_get_args();
        $args_num = func_num_args();
        if($args_num == 1){
        	$configure = $args[0];
        }
        $this->app_configuration($configure);
	}

	private function app_configuration($configure = null){
		if(isset($configure)){
			$type = $configure['app_type'];
			//echo("app type is ".$type);
			//require_once(dirname(__FILE__).'\Controllers\bad_request_controller.php');
			//$this->bad_request_controller = new bad_request_controller(); 
			if (isset($configure['routes']) && !empty($configure['routes'])){
				$raw_routes = $configure['routes'];
				$routes_count = sizeof($raw_routes);
				//echo("routes count is ".$routes_count);
				for ($i=0; $i < $routes_count; $i++) {
					//echo("routes ".$i." is ".$routes[$i]);
					$uri_controller = explode(">>", $raw_routes[$i]);
					$this->add_route($uri_controller[0], $uri_controller[1]);
					
				}
				//echo("Uri is ".$this->routes['admin'] ." and controller is ". $this->routes['admin'] ." .");
			}

			if (isset($configure['database']) && !empty($configure['database'])){
				$database = $configure['database'];
				$db_hostname = $database['hostname'];

				$db_username = $database['username'];

				$db_password = $database['password'];

				$db_name = $database['db_name']; 

				if ($db_name != '' && $db_username != '') {
					$con = mysql_connect($db_hostname, $db_username, $db_password);

					if (!$con) {
							die('Failed to connect to host:' . $db_hostname . ' Error: ' . mysql_error());
					} else {
						$db_name = $database['db_name']; 
						$sql = mysql_select_db($db_name, $con);
						if (!$sql) {
							if(isset($configure['db_creation_language'])){
								$db_creation_language = $configure['db_creation_language'];
								$new_database_query = "CREATE DATABASE `".$db_name."`";
						    	mysql_query($new_database_query, $con);
						    	echo (mysql_error());
						    	//$con = mysql_connect($db_hostname, $db_username, $db_password);
						    	$sql = mysql_select_db($db_name, $con);
						    	if (!$sql) {
						    		echo("db name is ".$db_name);
						    	}
						    	if (is_array($db_creation_language)) {
						    		foreach ($db_creation_language as $new_table_query) {
						    			if(!mysql_query($new_table_query, $con)){
						    				echo('Failed to create table. ' . $db_hostname . ' Error: ' . mysql_error());
						    				$query = "DROP DATABASE `".$db_name."`";
						    				mysql_query($query, $con);
						    			}
						    		}
						    	} else {
						    		if(!mysql_query($db_creation_language, $con)){
						    			echo('Failed to create table. ' . $db_hostname . ' Error: ' . mysql_error());
						    			$query = "DROP DATABASE `".$db_name."`";
						    			mysql_query($query, $con);
						    		}
						    	}
							}
						}
					}
				}
				
			}
		}
	}

  	/*public static getInstance() {
		if (!isset(self::$instance)) {
	      $new_instance = __CLASS__;
	      $instance = new $new_instance;
	    }
	    return self::$instance;
  	}*/

  	private function add_route($uri, $controller){
  		//echo("Uri is ".$uri ." and controller is ". $controller ." .");
  		//$this->routes = array_values($this->routes);
  		$this->routes[$uri] = $controller;
  		require_once(dirname(__FILE__).'\Controllers\\'.$controller.'.php');
  		$this->{$controller} = new $controller($uri); 
  	}

  	public function process_request($uri, $method, $data=null){
		//echo("requested uri is ".$uri[0]);
		//echo("length of requested uri is ".sizeof($uri));
		$uri = $this->reform_uri($uri);
		//echo("The uri request is ".$uri);
		if (isset($this->routes[$uri])) {
			//echo("hahahahahahahahahahahaha");
			$controller = $this->routes[$uri];
			return $this->{$controller}->process_request($uri, $method, $data);
		} else {
			$controller = $this->find_controller($uri, $this->routes);
			$controller = $this->{$controller};
			$controller->process_request($uri, $method, $data);
		}
		
	}

	private function reform_uri($uri_array){
		$result = '';
		foreach ($uri_array as $piece) {
			$result = $result.'/'.$piece;
		}
		return $result;
	}


	private function is_same_uri($uri, $requested_uri){
		//echo("uri is ". $uri ." and requested_uri is ". $requested_uri ." .");
		$uri_pieces = explode('/', $uri);
		$size = sizeof($uri_pieces);
		$requested_uri_pieces = explode('/', $requested_uri);
		if(sizeof($uri_pieces) != sizeof($requested_uri_pieces)){
			return false;
		} else {
			for ($i=0; $i < $size; $i++) { 
				if((!(strpos($uri_pieces[$i],':') !== false)) && strcmp($uri_pieces[$i], $requested_uri_pieces[$i]) != 0){

					return false;
				}
			}
		}

		return true;
	}


	private function find_controller($uri, $routes){
		//echo("routes length is ".sizeof($routes));
		//echo("uri is ".$uri);

		if(empty($uri)){
			return 'main_controller';
		} else {
			if(isset($routes)){
				foreach ($routes as $key => $value){
					//echo("uri is ".$uri. " key is ".$key);
					if($this->is_same_uri($key, $uri)){
						return $value;
					}
				}
			}
			return "bad_request_controller";
		}
		
	}


}

class console_app extends qhq_app{
	//private $routes;
	
}

class qhq{


	/*public static function createWebApplication($config=null)
	{
		return self::createApplication('WApp',$config);
	}*/


	public static function create_console_application($config=null)
	{
		return self::create_application('CApp',$config);
	}

	public static function create_application($config = null){
		$configure = require_once($config);
		if ($configure['app_type'] == 'CApp') {
			return new console_app($configure);
		} else {
			#TBD
		}
		
	}
}
?>