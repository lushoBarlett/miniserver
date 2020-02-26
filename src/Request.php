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

		$this->trycatch( function() {
			$this->action = $_SERVER["REQUEST_URI"];
		});

		$this->trycatch( function() {
			$this->secure = $_SERVER["HTTPS"] ? true : false;
		});
		
		$this->trycatch( function() {
			$this->method = $_SERVER["REQUEST_METHOD"];
		});

		$this->trycatch( function() {
			$this->cookies = $_COOKIE;
		});
		
		$this->trycatch( function() {
			$this->contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		});
		
		$this->trycatch( function() {
			$this->raw = file_get_contents("php://input");
		});
		
		$this->trycatch( function() {
			$this->post = $_POST;
		});
		
		$this->trycatch( function() {
			$this->get = $_GET;
		});
		
		$this->trycatch( function() {
			$this->json = json_decode($this->raw);
		});
	}

	private function trycatch(callable $f, array $args = []) {
		try {
			return $f(...$args);
		} catch (\Exception $e) {
			return $e;
		}
	}
}

?>
