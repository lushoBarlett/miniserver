<?php

namespace Server\Controllers;

use Server\Environment;
use Server\Request;
use Server\Response;

class ConstController implements IController {

	private $env;

	public function __construct(Environment $env) {
		$this->env = $env;
	}

	public function __service_init(Request $request) : Response {
		return $this->env->constant("resp");
	}

	public static function Node(...$args) : Node {
		list($r) = $args;
		return new Node(self::class, ["resp" => $r]);
	}
}

?>