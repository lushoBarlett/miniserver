<?php

namespace Server;

use Server\File;

class Request {

	const GET = "GET";
	const POST = "POST";
	const PUT = "PUT";
	const PATCH = "PATCH";
	const DELETE = "DELETE";
	const HEAD = "HEAD";
	const OPTIONS = "OPTIONS";
	const TRACE = "TRACE";
	const CONNECT = "CONNECT";

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
		if ($debug)
			foreach ($debug as $k => $v)
				$this->{$k} = $v;
		else
			$this->get();
	}

	private function get() {
		$this->action = $this->splitURI($_SERVER["REQUEST_URI"] ?? null);
		$this->secure = isset($_SERVER["HTTPS"]);
		$this->method = $_SERVER["REQUEST_METHOD"] ?? null;
		$this->contentType = $_SERVER["CONTENT_TYPE"] ?? null;

		$this->raw = $this->getraw();
		$this->post = $_POST;
		$this->get = $_GET;
		$this->cookies = $_COOKIE;

		$this->json = $this->getjson();
		$this->files = $this->getfiles();
	}

	private function splitURI(?string $uri) : ?string {
		return $uri ? explode("?", $uri)[0] : null;
	}

	private function getraw() {
		if ($this->method == self::GET) {
			try {
				return urldecode($_SERVER["QUERY_STRING"]);
			} catch (\Exception $e) {
				return (string)$e;
			}
		}
	
		return file_get_contents("php://input");
	}

	private function getjson() {
		if ($this->contentType == "application/json") {
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
					$formatted[$identifier][$i] = new File(
						$atributes['name'][$i],
						$atributes['type'][$i],
						$atributes['tmp_name'][$i],
						$atributes['error'][$i],
						$atributes['size'][$i]
					);
				}
			}

			// else, normal file format
			else {
				$formatted[$identifier] = new File(
					$atributes['name'],
					$atributes['type'],
					$atributes['tmp_name'],
					$atributes['error'],
					$atributes['size']
				);
			}
		}

		return $formatted;
	}
}

?>
