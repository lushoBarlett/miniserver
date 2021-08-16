<?php

namespace Mini\Data;

class Header {

	public string $name;
	public string $value;

	public function __construct(string $name, string $value) {
		$this->name = $name;
		$this->value = $value;
	}

	public function set() : void {
		header("{$this->name}: {$this->value}");
	}
}

?>