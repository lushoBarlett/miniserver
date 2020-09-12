<?php

namespace Server;

class Resolution {

	public $node;
	public $route;
	public $args;
	public $failed;

	public function __construct($node, string $route, array $args = [], bool $failed = false) {
		$this->node = $node;
		$this->route = $route;
		$this->args = $args;
		$this->failed = $failed;
	}

	public static function failed() {
		return new self(null, "", [], true);
	}
}

?>
