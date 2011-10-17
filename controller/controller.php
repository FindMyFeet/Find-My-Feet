<?php

function HTTPStatus($num) {
  
   static $http = array (
       100 => "HTTP/1.1 100 Continue",
       101 => "HTTP/1.1 101 Switching Protocols",
       200 => "HTTP/1.1 200 OK",
       201 => "HTTP/1.1 201 Created",
       202 => "HTTP/1.1 202 Accepted",
       203 => "HTTP/1.1 203 Non-Authoritative Information",
       204 => "HTTP/1.1 204 No Content",
       205 => "HTTP/1.1 205 Reset Content",
       206 => "HTTP/1.1 206 Partial Content",
       300 => "HTTP/1.1 300 Multiple Choices",
       301 => "HTTP/1.1 301 Moved Permanently",
       302 => "HTTP/1.1 302 Found",
       303 => "HTTP/1.1 303 See Other",
       304 => "HTTP/1.1 304 Not Modified",
       305 => "HTTP/1.1 305 Use Proxy",
       307 => "HTTP/1.1 307 Temporary Redirect",
       400 => "HTTP/1.1 400 Bad Request",
       401 => "HTTP/1.1 401 Unauthorized",
       402 => "HTTP/1.1 402 Payment Required",
       403 => "HTTP/1.1 403 Forbidden",
       404 => "HTTP/1.1 404 Not Found",
       405 => "HTTP/1.1 405 Method Not Allowed",
       406 => "HTTP/1.1 406 Not Acceptable",
       407 => "HTTP/1.1 407 Proxy Authentication Required",
       408 => "HTTP/1.1 408 Request Time-out",
       409 => "HTTP/1.1 409 Conflict",
       410 => "HTTP/1.1 410 Gone",
       411 => "HTTP/1.1 411 Length Required",
       412 => "HTTP/1.1 412 Precondition Failed",
       413 => "HTTP/1.1 413 Request Entity Too Large",
       414 => "HTTP/1.1 414 Request-URI Too Large",
       415 => "HTTP/1.1 415 Unsupported Media Type",
       416 => "HTTP/1.1 416 Requested range not satisfiable",
       417 => "HTTP/1.1 417 Expectation Failed",
       500 => "HTTP/1.1 500 Internal Server Error",
       501 => "HTTP/1.1 501 Not Implemented",
       502 => "HTTP/1.1 502 Bad Gateway",
       503 => "HTTP/1.1 503 Service Unavailable",
       504 => "HTTP/1.1 504 Gateway Time-out"       
   );
  
   header($http[$num], true);
}
abstract class Controller {

	protected $template;
	protected $data = array();
	protected $head = false;
	
	public $is_page = false;
	public $title = "";
	public $jscripts = array();
	
	protected $serve_json = false;
	
	public static function load($name, $ispage) {
		$name = trim(strtolower($name));
		if ($name === 'controller') return null;
		if (file_exists("controller/$name".".php")) {
			require_once("controller/$name".".php");
			$c = $name."Controller";
			$h = new $c();
			$h->is_page = $ispage;
			return $h;
		}
	}
	
	/* The main purpose of init() is to identify what exactly it is we're going to do things to.*/
	public function init($get) {
		return null;
	}
	
	public function get() {
		$this->render();
	}
		
	public function render() {
		if ($this->head) return;
		if (isset($_REQUEST['accept'])) 
			$accept = strtolower($_REQUEST['accept']);
		else if(isset($_SERVER['HTTP_ACCEPT']))
			$accept = strtolower($_SERVER['HTTP_ACCEPT']);
		//Json
		if (strpos($accept, "json") !== false) {
			$this->renderJSON();
		}
		//Valid jsonp
		elseif (isset($_GET['jsonp'])) {
			ob_start();
			$this->renderJSON();
			$c = ob_get_clean();
			header("Content-type: application/javascript; charset=UTF-8", true);
			echo $_GET['jsonp']."(".$c.");";
		}
		elseif (strpos($accept, "jsonp")) {
			HTTPStatus(400);
		}
		//HTML
		elseif (strpos($accept, "html") !== false) {
			$this->renderHTML();
		}
		else
			$this->renderHTML();
	}
	
	public function error($text, $code) {
		if (isset($_REQUEST['accept'])) 
			$accept = strtolower($_REQUEST['accept']);
		else
			$accept = strtolower($_SERVER['HTTP_ACCEPT']);
			
		$this->data = array("error" => $text);
		if (strpos($accept, "json") !== false) {
			HTTPStatus($code);
			$this->renderJSON();
		}
		elseif (isset($_GET['jsonp'])) {
			HTTPStatus($code);
			ob_start();
			$this->renderJSON();
			$c = ob_get_clean();
			header("Content-type: application/javascript; charset=UTF-8", true);
			echo $_GET['jsonp']."(".$c.");";
			die();
		}
		elseif (strpos($accept, "jsonp")) {
			HTTPStatus(400);
			die();
		}
		else {
			$t = rawurlencode($text);
			header("Location: index.php?page=home&error=$t", 303);
			die();
		}			
	}
	
	public function handleRequest($method, $get, $post) {
		$this->init($get);
		$this->error = (isset($get['error']) ? $get['error'] : "");
		if ($method == 'HEAD') {
			$method = "GET";
			$this->head = true;
		}
		$methods = array('GET', 'POST', 'PUT', 'DELETE');
		if (in_array(strtoupper($method), $methods) && method_exists($this, strtolower($method))) {
			$this->$method();
		}
		else {
			$r = array("HEAD");
			foreach ($methods as $m) {
				if (method_exists($this, strtolower($method))) {
					$r[] = $m;
				}
			}
			HTTPStatus(405);
			header("Allow: ".implode(", ", $r));
		}


	}
	
	public function renderJSON() {
		header("Content-type: application/json; charset=UTF-8", true);
		if ($this->serve_json) {
			echo json_encode($this->data);
		}
		else {
			HTTPStatus(406);
			echo json_encode(
				array("error"=>
					array(
						"code"=>"406",
						"message"=>"This page has no JSON representation. Try HTML instead."
				)));
		}
	}
	
	public function renderHTML() {
		header("Content-Type: text/html; charset=UTF-8", true);
		$data = $this->data;
		if ($this->is_page) {
			$this->body = "view/web/$this->template".".php";
			include "view/web/global.php";
		}
		else {
			include "view/web/$this->template".".php";		
		}
		
	}
}

