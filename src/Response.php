<?php

namespace Server;

use Cologger\Logger;

class Response {

	private $status;
	private $redirect;
	private $payload = "";
	private $cookies = [];
	private $headers = [];

	public function __construct() {}

	public function __toString() : string {
		http_response_code($this->status);

		if ($this->redirect)
			header("Location: $this->redirect");

		foreach ($this->headers as $k => $v)
			header("$k: $v");

		foreach ($this->cookies as $k => $j)
			setcookie(
				$k, $j['value'], $j['expire'],
				$j['secure'], $j['httponly']
			);

		return $this->payload;
	}
	
	public function status(int $status) : Response {
		$this->status = $status;
		return $this;
	}

	public function payload(string $content) : Response {
		$this->payload = $content;
		return $this;
	}

	public function redirect(string $url) : Response {
		$this->status(302);
		$this->redirect = $url;
		return $this;
	}

	public function cookie(
		string $name, string $value, int $expireIn = 0,
		bool $secure = false, bool $httpOnly = false
	) : Response {
		$this->cookies[$name] = [
			"value" => $value,
			"expire" => $expireIn,
			"secure" => $secure,
			"httponly" => $httpOnly
		];
		return $this;
	}

	public function header(string $header, string $value) : Response {
		$this->headers[$header] = $value;
		return $this;
	}

	/* Predefined Static Response Constructions (PSRC) */
	public static function redirectTo(string $url, bool $temporary = true) : Response {
		return (new Response)
			->redirect($url)
			->status($temporary ? 302 : 301);
	}
	
	public static function withView(string $template) : Response {
		$template = file_get_contents($template);

		# TODO: template processing
		
		return (new Response)
			->payload($template);
	}
	
	public static function withText(string $text) : Response {
		return (new Response)
			->payload($text);
	}

	public static function withStatus(int $status) : Response {
		return (new Response)
			->status($status);
	}

	public static function notFound() {
		return self::withStatus(404);
	}

	public static function serverError() {
		return self::withStatus(500);
	}
}

?>
