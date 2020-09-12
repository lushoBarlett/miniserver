<?php

namespace Server\Routing;

class Resolution {

	public $node;
	public $route;
	public $args;

	public function __construct(Node $node, string $route, array $args = []) {
		$this->node = $node;
		$this->route = $route;
		$this->args = $args;
	}
}

?>
