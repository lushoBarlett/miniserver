<?php

namespace Server\Directives;

class BaseUrlDirective extends Directive {

	private $base;

	public function __construct(string $base) {
		$this->base = route_trim($base);
	}

	public function request_event(Request $r) : Request {
		$action = route_trim($r->action);

		$r->action =
			substr($action, 0, strlen($this->base)) == $this->base ?
			substr($action, strlen($base)) : null;

		return $r;
	}
}

?>
