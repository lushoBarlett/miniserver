<?php

namespace Server\Controllers;

class ConstantController implements IController {

	private $const;

	public function __construct(Service $serv, array $metadata) {
		$this->const = $metadata["@constant"];
	}

	public function __service_init(Request $request) : Response {
		return $this->const;
	}

	public static function Node(Response $constant) {
		return (object)[
			"cons" => self::class,
			"meta" => ["@constant" => $constant]
		];
	}
}

?>

