<?php

namespace Mini\Routing;

class Node {

	public ?Route $route;
	public array $children;

	public function __construct(Route $route = null, array $children = []) {
		$this->route = $route;
		$this->children = $children;
	}
}

?>