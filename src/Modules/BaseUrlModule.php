<?php

namespace Server\Modules;

use Server\Routing\Route;
use Server\State;

class BaseUrlModule extends Module {

	private $base;

	public function __construct(string $base) {
		$this->base = Route::trim($base);
	}

	public function request(State $s) : State {
		$action = Route::trim($s->request->action);

		$s->request->action = substr($action, 0, strlen($this->base)) == $this->base
				      ? substr($action, strlen($this->base))
				      : null;

		return $s;
	}
}

?>