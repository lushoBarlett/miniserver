<?php

namespace Server\Controllers;

class SimpleController implements IController {

	private $proc;

	public function __construct(Service $serv, array $metadata) {
		$this->proc = $metadata["@procedure"];
	}

	public function __service_init(Request $request) : Response {
		return ($this->proc)($request);
	}

	public static function Node(callable $procedure) {
		return (object)[
			"cons" => self::class,
			"meta" => ["@procedure" => $procedure]
		];
	}
}

?>
