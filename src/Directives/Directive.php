<?php

namespace Server\Directives;

use Server\State;

class Directive {

	public function request_event(State $s)    : State { return $s; }
	public function response_event(State $s)   : State { return $s; }

	public function exception_event(State $s)  : State { return $s; }
	public function error_event(State $s)      : State { return $s; }

	public function resolution_event(State $s) : State { return $s; }
}

?>