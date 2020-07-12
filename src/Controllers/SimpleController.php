<?php

namespace Server\Controllers;

class SimpleController implements IController {

	private $env;

	public function __construct(Environment $env) {
		$this->env = $env;
	}

	public function __service_init(Request $r) : Response {
		return ($this->env->provider("proc"))($r);
	}

	public static function Node(callable $proc) {
		return (object)[
			"cons" => self::class,
			"env" => ["#proc" => $proc]
		];
	}
}

?>
