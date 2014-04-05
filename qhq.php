<?php
/**
 * This is the all in one file for qhq framework, hopefully!
 *
 * @author Rick QING Cheng <qingchengnus@gmail.com>
 *
 * @version 0.00
 *
 */
/*class model{
	$keys = array();
	$key_types = array();

	public __construct(){
		$args = func_get_args();
		$args_count = func_num_args();
		if($args_count >= 2){
			for($i = 0; $i < $args_count; $i ++){

			}
		}
		
	}


}*/

class view{

}

abstract class controller{

	private $uri;

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

	abstract function process_request($uri, $method, $data = null);
}

abstract class qhq_app{

	private static $instance;
	protected $routes;


	abstract function process_request($uri, $method, $data=null);


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
			$routes = $configure['routes'];
			$type = $configure['app_type'];
			//echo("app type is ".$type);
			//require_once(dirname(__FILE__).'\Controllers\bad_request_controller.php');
			//$this->bad_request_controller = new bad_request_controller(); 
			if (isset($routes)){
				if(!empty($routes)){
					$routes_count = sizeof($routes);
					//echo("routes count is ".$routes_count);
					for ($i=0; $i < $routes_count; $i++) {
						//echo("routes ".$i." is ".$routes[$i]);
						$uri_controller = explode(">>", $routes[$i]);
						$this->add_route($uri_controller[0], $uri_controller[1]);
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
  		$this->routes = array_values($this->routes);
  		$this->routes[$uri] = $controller;
  		require_once(dirname(__FILE__).'\Controllers\\'.$controller.'.php');
  		$this->{$controller} = new $controller(); 
  	}


}

class console_app extends qhq_app{
	//private $routes;
	public function process_request($uri, $method, $data=null){
		//echo("requested uri is ".$uri[0]);
		//echo("length of requested uri is ".sizeof($uri));
		$uri = $this->reform_uri($uri);
		if (isset($routes[$uri])) {
			$controller = $this->routes[$uri];
			return $this->{$controller}.process_request($uri, $method, $data);
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
		$uri_pieces = explode('/', $uri);
		$size;
		$requested_uri_pieces = explode('/', $requested_uri);
		if($size = sizeof($uri_pieces) != sizeof($requested_uri_pieces)){
			return false;
		} else {
			for ($i=0; $i < $size; $i++) { 
				if(!(strpos($uri_pieces[$i],':') !== false) && strcmp($uri_pieces[$i], $requested_uri_pieces[$i]) != 0){
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
					if($this->is_same_uri($key, $uri)){
						return $value;
					}
				}
			}
			

			return "bad_request_controller";
		}
		
	}
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