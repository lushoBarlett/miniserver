<?php

namespace Server\Routing;

class Node {

	public $value;
	public $children;
	public $route;

	public function __construct($value = null, array $children = [], ?string $route = null) {
		$this->value = $value;
		$this->children = $children;
		$this->route = $route;
	}
}

?>