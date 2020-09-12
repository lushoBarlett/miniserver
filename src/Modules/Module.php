<?php

namespace Server\Modules;

use Server\State;

class Module {

	public function request(State $s)    : State { return $s; }
	public function response(State $s)   : State { return $s; }

	public function exception(State $s)  : State { return $s; }
	public function error(State $s)      : State { return $s; }

	public function resolution(State $s) : State { return $s; }
}

?>