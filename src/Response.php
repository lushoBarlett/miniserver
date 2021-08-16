<?php

namespace Mini;

use Mini\Data\Cookie;
use Mini\Data\Header;
use Mini\Tools\Template;

class Response {

	public int $status = 200;
	public ?string $redirect = null;
	public string $payload = "";
	public array $cookies = [];
	public array $headers = [];

	public function __toString() : string {
		http_response_code($this->status);

		if ($this->redirect)
			$this->headers[] = new Header("Location", $this->redirect);

		foreach ($this->headers as $header)
			$header->set();

		foreach ($this->cookies as $cookie)
			$cookie->set();

		return $this->payload;
	}

	public function status(int $status) : self {
		$this->status = $status;
		return $this;
	}

	public function payload(string $content) : self {
		$this->payload = $content;
		return $this;
	}

	public function redirect(string $url) : self {
		$this->status(302);
		$this->redirect = $url;
		return $this;
	}

	public function cookie(Cookie $cookie) : self {
		$this->cookies[] = $cookie;
		return $this;
	}

	public function header(Header $header) : self {
		$this->headers[] = $header;
		return $this;
	}

	// STATIC CONSTRUCTORS //

	public static function OK() : self {
		return (new self)->status(200);
	}

	public static function redirectTo(string $url, bool $temporary = true) : self {
		return (new self)
			->redirect($url)
			->status($temporary ? 302 : 301);
	}
	
	public static function withView(string $view) : self {
		$view = file_get_contents($view);
		
		return (new self)->payload($view);
	}
	
	/**
	 * @param string|Template $template
	 */
	public static function withTemplate($template, array $vars = []) : self {
		// TODO: error handling
		if (is_string($template)) {
			$template = new Template($template);
			$template->declare_all($vars);
		}

		return (new self)->payload($template->render());
	}
	
	public static function withText(string $text) : self {
		return (new self)->payload($text);
	}

	public static function withStatus(int $status) : self {
		return (new self)->status($status);
	}

	/**
	 * @param array<Cookie> $args
	 */
	public static function withCookies(...$args) : self {
		$r = new self;
		foreach ($args as $cookie)
			$r->cookie($cookie);
		return $r;
	}

	/**
	 * @param array<Header> $args
	 */
	public static function withHeaders(...$args) : self {
		$r = new self;
		foreach ($args as $header)
			$r->header($header);
		return $r;
	}

	public static function notFound() : self {
		return self::withStatus(404);
	}

	public static function serverError() : self {
		return self::withStatus(500);
	}

	/**
	 * @param mixed $value
	 */
	public static function withJSON($value) : self {
		return self::withText(json_encode($value));
	}
}

?>