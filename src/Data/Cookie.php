<?php

namespace Mini\Data;

class Cookie {

	const None = "None";
	const Lax = "Lax";
	const Strict = "Strict";

	public string $name;
	public string $value;
	public ?int $expires = null;
	public string $path = "";
	public string $domain = "";
	public bool $secure = false;
	public bool $http_only = false;
	public string $same_site = "";

	public function __construct(string $name, string $value = "") {	
		$this->name = $name;
		$this->value = $value;
	}

	public function expires(int $expires) : self {
		$this->expires = $expires;
		return $this;
	}

	public function path(string $path) : self {
		$this->path = $path;
		return $this;
	}

	public function domain(string $domain) : self {
		$this->domain = $domain;
		return $this;
	}

	public function secure() : self {
		$this->secure = true;
		return $this;
	}

	public function http_only() : self {
		$this->http_only = true;
		return $this;
	}

	public function same_site(string $same_site) : self {
		$this->same_site = $same_site;
		return $this;
	}

	public function set() : void {
		setcookie($this->name, $this->value, [
			"expires" => $this->expires === null ? 0 : time() + $this->expires,
			"path" => $this->path,
			"domain" => $this->domain,
			"secure" => $this->secure,
			"httponly" => $this->http_only,
			"samesite" => $this->same_site
		]);
	}
}

?>
