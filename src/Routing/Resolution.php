<?php

namespace Server\Routing;

class Resolution {

	public $value;
	public $route;
	public $args;

	public function __construct($value, string $route, array $args = []) {
		$this->value = $value;
		$this->route = $route;
		$this->args = $args;
	}
}

?>