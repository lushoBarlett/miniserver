<?php

namespace Server\Directives;

class StatusDirective extends Directive {

	private $status;
	private $proc;

	public function __construct(string $status, callable $proc) {
		$this->status = $status;
		$this->proc = $proc;
	}

	public function response_event(Response $r) : Response {
		if ($r->get_status() == $this->status)
			return ($this->proc)($r);

		return $r;
	}
}

?>

