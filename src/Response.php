<?php

namespace Server;

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
				$k, $j['value'],
				($j['expire'] !== 0 ? time() + $j['expire'] : 0),
				"/", "", $j['secure'], $j['httponly']
			);

		return $this->payload;
	}
	
	public function get_status()   { return $this->status;   }
	public function get_redirect() { return $this->redirect; }
	public function get_payload()  { return $this->payload;  }
	public function get_cookies()  { return $this->cookies;  }
	public function get_headers()  { return $this->headers;  }

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

	// TODO: improve
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

	// STATIC CONSTRUCTORS //

	public static function redirectTo(string $url, bool $temporary = true) : Response {
		return (new Response)
			->redirect($url)
			->status($temporary ? 302 : 301);
	}
	
	public static function withView(string $view) : Response {
		$view = file_get_contents($view);
		
		return (new Response)->payload($view);
	}
	
	public static function withTemplate($template, ?array $vars) : Response {
		if (is_string($template)) {
			$template = new Template($template);
			$template->add_vars($vars ?? []);
		}
		// TODO: error handling

		return (new Response)->payload($template->render());
	}
	
	public static function withText(string $text) : Response {
		return (new Response)->payload($text);
	}

	public static function withStatus(int $status) : Response {
		return (new Response)->status($status);
	}

	public static function notFound() {
		return self::withStatus(404);
	}

	public static function serverError() {
		return self::withStatus(500);
	}

	public static function withJSON($value) {
		return self::withText(json_encode($value));
	}

	// TODO: withCookie
}

?>