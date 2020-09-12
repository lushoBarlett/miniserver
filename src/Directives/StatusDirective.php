<?php

namespace Server\Directives;

class StatusDirective extends Directive {

	private $status;
	private $proc;

	public function __construct(string $status, callable $proc) {
		$this->status = $status;
		$this->proc = $proc;
	}

	public function response_event(State $s) : State {
		if ($s->response->get_status() == $this->status)
			$s->response = ($this->proc)($s->response);

		return $r;
	}
}

?>

