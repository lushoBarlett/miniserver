<?php

namespace Mini;

use Mini\Tools\HTTP;
use Mini\Data\File;

class Request {

	public string $action = "";
	public bool $secure = false;
	public int $method = HTTP::GET;
	public array $get = [];
	public array $post = [];
	public string $raw = "";
	public string $contentType = "";
	/** @var mixed $json */
	public $json = null;
	public array $cookies = [];
	public array $files = [];
	public string $IP = "";

	public function __construct(?array $debug = null) {
		if ($debug !== null) {
			foreach ($debug as $k => $v)
				$this->{$k} = $v;
		} else {
			$this->get();
		}
	}

	// TODO: fail on missing $_SERVER value
	private function get() : void {
		$this->action = $this->splitURI($_SERVER["REQUEST_URI"]);
		$this->secure = isset($_SERVER["HTTPS"]);
		$this->method = HTTP::from_string($_SERVER["REQUEST_METHOD"]);
		$this->contentType = $_SERVER["CONTENT_TYPE"];

		$this->raw = $this->getraw();
		$this->post = $_POST;
		$this->get = $_GET;
		$this->cookies = $_COOKIE;

		$this->json = $this->getjson();
		$this->files = $this->getfiles();
		$this->IP = $this->getIP();
	}

	private function splitURI(string $uri) : string {
		return explode("?", $uri)[0];
	}

	private function getraw() : string {
		if ($this->method == HTTP::GET) {
			try {
				return urldecode($_SERVER["QUERY_STRING"] ?? "");
			} catch (\Exception $e) {
				return (string)$e;
			}
		}
	
		return file_get_contents("php://input");
	}

	/**
	 * @return mixed
	 */
	private function getjson() {
		if ($this->contentType == "application/json") {
			try {
				return json_decode($this->raw);
			} catch (\Exception $e) {
				return (string)$e;
			}
		}
	}

	/**
	 * @return array<array<File>>
	 */
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
				$formatted[$identifier][0] = new File(
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

	private function getIP() : string {
		return $_SERVER['HTTP_X_FORWARDED_FOR']
		    ?? $_SERVER["REMOTE_ADDR"]
		    ?? $_SERVER["HTTP_CLIENT_IP"]
		    ?? '';
	}
}

?>