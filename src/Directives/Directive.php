<?php

namespace Server\Directives;

use Server\Request;
use Server\Response;

class Directive {

	public function request_event(Request $r) : Request { return $r; }
	public function response_event(Response $r) : Response { return $r; }

	public function exception_event(\Exception $e) : void {}
	public function error_event(\Error $e) : void {}

	public function resolution_event(Resolution $r) : Resolution { return $r; }
}
