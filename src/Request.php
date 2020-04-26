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
	public $files;

	public function __construct(?array $debug = null) {
		$this->get();

		if ($debug)
			foreach ($debug as $k => $v)
				$this->{$k} = $v;
	}

	private function get() {
		$this->action = isset($_SERVER["REQUEST_URI"]) ? $this->splitURI($_SERVER["REQUEST_URI"]) : null;

		$this->secure = isset($_SERVER["HTTPS"]) ? true : false;
	
		$this->method = isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : null;

		$this->cookies = $_COOKIE;
	
		$this->contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : null;
	
		$this->raw = $this->getraw();

		$this->post = $_POST;
	
		$this->get = $_GET;

		$this->json = $this->getjson();

		$this->files = $this->getfiles();
	}

	private function splitURI(string $uri) {
		return explode("?", $uri)[0];
	}

	private function getraw() {
		if ($this->method === "GET") {
			try {
				return urldecode($_SERVER["QUERY_STRING"]);
			} catch (\Exception $e) {}
		}
	
		return file_get_contents("php://input");
	}

	private function getjson() {
		if ($this->contentType === "application/json") {
			try {
				return json_decode($this->raw);
			} catch (\Exception $e) {
				return (string)$e;
			}
		}
	}

	private function getfiles() : array {
		$formatted = [];

		// identifier is the html form name
		// atributes is an array for file information
		foreach ($_FILES as $identifier => $atributes) {
			
			// if array, multiple file format is used
			if (is_array($atributes['name'])) {

				$formatted[$identifier] = [];

				for($i = 0; $i < count($atributes['name']); $i++) {
					$formatted[$identifier][$i] = [
						"name" => $atributes['name'][$i],
						"type" => $atributes['type'][$i],
						"tmp_name" => $atributes['tmp_name'][$i],
						"error" => $atributes['error'][$i],
						"size" => $atributes['size'][$i]
					];
				}
			}

			// else, normal file format
			else {
				$formatted[$identifier] = $atributes;
			}
		}

		return $formatted;
	}
}

?>
