<?php

namespace Server\Modules;

use Server\State;

class StatusModule extends Module {

	private $status;
	private $proc;

	public function __construct(string $status, callable $proc) {
		$this->status = $status;
		$this->proc = $proc;
	}

	public function response(State $s) : State {
		if ($s->response->get_status() == $this->status)
			$s->response = ($this->proc)($s->response);

		return $s;
	}
}

?>