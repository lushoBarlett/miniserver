<?php

namespace Server\Controllers;

use Server\Environment;

class Node {

	public $cons;
	public $env;

	public function __construct(string $cons, $env = null) {
		$this->cons = $cons;
		$this->env = is_array($env) ? new Environment($env) : $env;
	}
}

?>