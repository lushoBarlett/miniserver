<?php

namespace Server\Directives;

class BaseUrlDirective extends Directive {

	private $base;

	public function __construct(string $base) {
		$this->base = route_trim($base);
	}

	public function request_event(State $s) {
		$action = route_trim($s->request->action);

		$s->request->action =
			substr($action, 0, strlen($this->base)) == $this->base ?
			substr($action, strlen($base)) : null;

		return $s;
	}
}

?>
