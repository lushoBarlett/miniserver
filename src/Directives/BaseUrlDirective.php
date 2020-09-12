<?php

namespace Server\Directives;

use Server\Routing\Route;

class BaseUrlDirective extends Directive {

	private $base;

	public function __construct(string $base) {
		$this->base = Route::trim($base);
	}

	public function request_event(State $s) {
		$action = Route::trim($s->request->action);

		$s->request->action =
			substr($action, 0, strlen($this->base)) == $this->base ?
			substr($action, strlen($base)) : null;

		return $s;
	}
}

?>
