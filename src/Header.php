<?php

namespace Server;

class Header {

	public $name;
	public $value;

	public function __construct(string $name, string $value) {
		$this->name = $name;
		$this->value = $value;
	}

	public function set_header() {
		header("{$this->name}: {$this->value}");
	}
}

?>