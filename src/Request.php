<?php

namespace Server;

class Request {

	public $action;
	public $secure;
	public $method;
	public $get;
	public $post;
	public $raw;
	public $contentType;
	public $json;
	public $cookies;

	public function __construct(?array $debug = null) {
		$this->get();

		if ($debug)
			foreach ($debug as $k => $v)
				$this->{$k} = $v;
	}

	private function get() {
		$this->action = isset($_SERVER["REQUEST_URI"]) ?
			$_SERVER["REQUEST_URI"] : null;

		$this->secure = isset($_SERVER["HTTPS"]) ?
			true : false;
	
		$this->method = isset($_SERVER["REQUEST_METHOD"]) ?
			$_SERVER["REQUEST_METHOD"] : null;

		$this->cookies = $_COOKIE;
	
		$this->contentType = isset($_SERVER["HTTP_CONTENT_TYPE"]) ?
			$_SERVER["HTTP_CONTENT_TYPE"] : null;
	
		$this->raw = file_get_contents("php://input");
	
		$this->post = $_POST;
	
		$this->get = $_GET;

		try {
			$this->json = json_decode($this->raw);
		} catch(\Exception $e) {
			$this->json = null;
		}
	}
}

?>
