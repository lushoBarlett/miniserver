<?php

namespace Server\Controllers;

class ConstController implements IController {

	private $env;

	public function __construct(Environment $env) {
		$this->env = $env;
	}

	public function __service_init(Request $request) : Response {
		return $this->env->constant("resp");
	}

	public static function Node(Response $r) : object {
		return (object)[
			"cons" => self::class,
			"meta" => ["resp" => $r]
		];
	}
}

?>

