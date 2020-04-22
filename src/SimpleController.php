<?php

namespace Server;

use Server\Request;
use Server\Response;
use Server\IController;

class SimpleController implements IController {

	private $processor;

	public function __construct(callable $processor) {
		$this->processor = $processor;
	}

	public function process(Request $request) : Response {
		return ($this->processor)(...func_get_args());
	}
}

?>
